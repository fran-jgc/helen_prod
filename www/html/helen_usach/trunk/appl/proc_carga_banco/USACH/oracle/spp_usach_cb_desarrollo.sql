PROMPT CREATE OR REPLACE PROCEDURE spp_usach_cb_desarrollo
CREATE OR REPLACE PROCEDURE spp_usach_cb_desarrollo(ve_cod_ejecucion_proceso	NUMBER)
AS
BEGIN
	DECLARE
		vl_current_date				        DATE := SYSDATE;
		vl_nom_comprobante			      VARCHAR2(100);
		KL_FAMILIA_SOLIDARIO		      NUMBER := 1;
		/*
		 * los comprobantes se creaban con estado aprobado el cual [fabiola.arriagada@usach.cl] enviado el 21/10/2014 
		 * en un correo dijo que siempre se deben crear con estado pendiente
		 * jv 22/10/2014
		 * */
		K_COMP_PENDIENTE		      NUMBER := 1;
		--K_COMP_CONTABILIZADO		      NUMBER := 2;
		
		K_COMP_INGRESO				        NUMBER := 1;
		K_COMP_CARGA_BCO_DESARROLLO	  NUMBER := 30;
		K_ORIGEN_PAGO_BANCO			      NUMBER := 18;
		K_ORIGEN_CARGA_DESARROLLO	    NUMBER := 1;
		vl_i						              NUMBER;
		vl_cod_comp_solidario		      NUMBER;
		vl_cod_comp_institucional	    NUMBER;
		vl_cod_comprobante			      NUMBER;
		vl_fecha_pago				          DATE;
		vl_cod_boleta				          NUMBER;
		vl_cod_deuda				          NUMBER;
		vl_cod_plan_pago			        NUMBER;
		vl_cod_cuenta_bco			        NUMBER;
		vl_cod_abono				          NUMBER;
		vl_cod_sub_boleta			        NUMBER(10);
		vl_cod_usuario				        NUMBER;
		vl_count_registros			      NUMBER;
		vl_mensaje					          VARCHAR2(100);
		vl_cod_familia_deuda		      NUMBER;
		vl_rut                        NUMBER;
		vl_fecha_pago_sub_boleta      DATE;
    	vl_cod_moneda                 NUMBER;
		vl_valor_moneda			          NUMBER;
		vl_monto_capital_pesos	      NUMBER;
		vl_monto_interes_pesos	      NUMBER;
		vl_monto_multa_pesos		      NUMBER;
		vl_factor_K				            NUMBER;
		vl_factor_IN				          NUMBER;
		vl_factor_IP				          NUMBER;
		vl_monto_capital			        NUMBER;
		vl_monto_interes			        NUMBER;
		vl_monto_multa			          NUMBER;
		vl_fecha_deposito				DATE;
		vl_count_comprobante	      NUMBER;
          

		--//CURSOR
		CURSOR c_usach_cb_desarrollo IS 
	    SELECT	 COD_USACH_CB_DESARROLLO
				,IDENTIFICADOR_DEUDOR
				,FECHA_REAL_PAGO
				,VALOR_CUOTA
				,NRO_CUOTA
	            ,case nvl(VALOR_RECAUDADO_EFECTIVO,0)
	            	WHEN 0 THEN VALOR_RECAUDADO_DOC
                	ELSE VALOR_RECAUDADO_EFECTIVO
	            end VALOR_RECAUDADO_EFECTIVO
	            ,FECHA_DEPOSITO
		FROM    usach_cb_desarrollo
		WHERE   PROCESADO = 'N'
		AND     CARGADO = 'N';

		BEGIN
			-- usuario que ejecuta el proceso
			SELECT	cod_usuario
			INTO	vl_cod_usuario
			FROM	ejecucion_proceso
			WHERE	cod_ejecucion_proceso = ve_cod_ejecucion_proceso;

			-- calcula el total de registros
			SELECT	Count(*)
			INTO	vl_count_registros
			FROM	usach_cb_desarrollo
			WHERE	procesado = 'N'
			AND		cargado = 'N';

			spu_ejecucion_proceso ('SET_TOTAL', ve_cod_ejecucion_proceso, vl_count_registros);
			vl_i := 1;

			SELECT	FECHA_DEPOSITO
			INTO	vl_fecha_deposito
			FROM    USACH_CB_DESARROLLO
			WHERE   PROCESADO = 'N'
			AND     CARGADO = 'N'
			AND		ROWNUM = 1;
	    
			/*
			 * los comprobantes se creaban con estado aprobado el cual [fabiola.arriagada@usach.cl] enviado el 21/10/2014 
			 * en un correo dijo que siempre se deben crear con estado pendiente
			 * jv 22/10/2014
			 * */
			--crea comprobante para fondo solidario
			vl_nom_comprobante := 'Comprobante Carga de Ingreso, BANCO DEL DESARROLLO, ' || TO_CHAR(vl_fecha_deposito,'dd/mm/yyyy');
			--spu_comprobante ('INSERT', NULL, vl_fecha_deposito, vl_cod_usuario, vl_nom_comprobante, KL_FAMILIA_SOLIDARIO, K_COMP_CONTABILIZADO, K_COMP_INGRESO, K_COMP_CARGA_BCO_DESARROLLO, vl_cod_usuario, vl_fecha_deposito);
			spu_comprobante ('INSERT', NULL, vl_fecha_deposito, vl_cod_usuario, vl_nom_comprobante, KL_FAMILIA_SOLIDARIO, K_COMP_PENDIENTE, K_COMP_INGRESO, K_COMP_CARGA_BCO_DESARROLLO, vl_cod_usuario, vl_fecha_deposito);

			SELECT  comprobante_sq.CURRVAL
			INTO    vl_cod_comp_solidario
			FROM    dual;

			FOR A IN c_usach_cb_desarrollo LOOP
				-- cuando entra al loop actualiza la tabla, y se marca procesado con 'S' para efecto de historial
				UPDATE  usach_cb_desarrollo
				SET     procesado = 'S'
				WHERE   cod_usach_cb_desarrollo = a.cod_usach_cb_desarrollo;
				
				-- verifica si el proceso ha sido cancelado
				IF (f_ep_cancela_proceso (ve_cod_ejecucion_proceso) = 'S') THEN
					RETURN;
				END IF;

				--vl_rut := To_Number(REPLACE(SubStr(A.IDENTIFICADOR_DEUDOR, 1, 8),' ',''));
				vl_cod_sub_boleta := To_Number(REPLACE(A.IDENTIFICADOR_DEUDOR,' ',''));

				/*obtengo el cod_boleta de la sub_boleta*/
				SELECT cod_boleta
				INTO vl_cod_boleta
				FROM sub_boleta
				WHERE cod_sub_boleta = vl_cod_sub_boleta;

				SELECT  count(*)
				INTO	  vl_count_registros
				FROM    boleta
				WHERE   COD_BOLETA = vl_cod_boleta;

				IF (vl_count_registros = 0) THEN  --vl_count_registros
					vl_mensaje := 'Boleta: ' || To_char(vl_cod_boleta) || ' No EXISTE';
					spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
				ELSE
					/*--VALIDA QUE LA SUB_BOLETA NO ESTE PAGADA
					SELECT  fecha_pago
					INTO    vl_fecha_pago_sub_boleta
					FROM    sub_boleta
					WHERE   cod_sub_boleta = vl_cod_sub_boleta;

					IF(vl_fecha_pago IS NOT NULL)THEN 
						vl_mensaje := 'La sub boleta código: '||to_char(vl_cod_sub_boleta)||' ya ha sido cancelada';
						spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
					ELSE */
					SELECT	 d.cod_deuda
							,cod_plan_pago
							,cod_familia_deuda
							,cod_moneda
					INTO	vl_cod_deuda
							,vl_cod_plan_pago
							,vl_cod_familia_deuda
							,vl_cod_moneda
					FROM    deuda d, boleta b, tipo_deuda td  
					WHERE   b.cod_boleta = vl_cod_boleta
					AND		d.cod_deuda = b.cod_deuda
					AND     td.cod_tipo_deuda = d.cod_tipo_deuda;

					SELECT	cod_sub_boleta
							,fecha_pago
					INTO	vl_cod_sub_boleta
							,vl_fecha_pago
					FROM	sub_boleta
					WHERE	cod_boleta = vl_cod_boleta
					AND		nro_sub_boleta = A.NRO_CUOTA;

						--valida que la sub_boleta no este pagada
						IF (vl_fecha_pago IS NOT NULL)THEN  --vl_fecha_pago
							vl_mensaje := 'La sub-boleta nº: ' || To_char(A.NRO_CUOTA) || ', de la boleta cod: ' || To_char(vl_cod_boleta) || ' se encuentra pagada. Fecha Pago: ' || To_char(vl_fecha_pago, 'dd/mm/yyyy');
							spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
						ELSE
							--dbms_output.put_line('A.VALOR_RECAUDADO_EFECTIVO.' || A.VALOR_RECAUDADO_EFECTIVO);
							IF(A.VALOR_RECAUDADO_EFECTIVO > 0)THEN
								vl_valor_moneda		      := f_mon_get_valor_moneda(vl_cod_moneda, A.FECHA_REAL_PAGO);
								vl_monto_capital_pesos  := round(f_pp_saldo_capital_caja(vl_cod_plan_pago, A.FECHA_REAL_PAGO) * vl_valor_moneda, 0);
								vl_monto_interes_pesos  := round(f_pp_saldo_interes_normal_caja(vl_cod_plan_pago, A.FECHA_REAL_PAGO) * vl_valor_moneda, 0);
								vl_monto_multa_pesos	  := round(f_pp_saldo_multa_caja(vl_cod_plan_pago, A.FECHA_REAL_PAGO) * vl_valor_moneda, 0);

								IF ((vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos) <> 0) THEN --sum_monto
									-- porporcion K, IN, IP
									vl_factor_K  := vl_monto_capital_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
									vl_factor_IN := vl_monto_interes_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
									vl_factor_IP := vl_monto_multa_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);

									vl_monto_capital	:= Round((A.VALOR_RECAUDADO_EFECTIVO / vl_valor_moneda) * vl_factor_K, 4);
									vl_monto_interes	:= Round((A.VALOR_RECAUDADO_EFECTIVO / vl_valor_moneda) * vl_factor_IN, 4);
									vl_monto_multa		:= Round((A.VALOR_RECAUDADO_EFECTIVO / vl_valor_moneda) * vl_factor_IP, 4);

									-- si no cuadra con el total_moneda que se esta pagando, se agrega la diferencia al abono K
									IF ((vl_monto_capital + vl_monto_interes + vl_monto_multa) <> round(A.VALOR_RECAUDADO_EFECTIVO / vl_valor_moneda,4)) THEN
										vl_monto_capital := vl_monto_capital + (round(A.VALOR_RECAUDADO_EFECTIVO / vl_valor_moneda,4) - (vl_monto_capital + vl_monto_interes + vl_monto_multa));
									END IF;
                
									IF (vl_cod_familia_deuda = KL_FAMILIA_SOLIDARIO) THEN
										vl_cod_comprobante := vl_cod_comp_solidario;
									ELSE
										vl_cod_comprobante := NULL;
									END IF;

									spu_abono_carga (A.FECHA_REAL_PAGO        --fecha_abono
												,vl_cod_usuario           --cod_usuario
												,vl_monto_capital         --monto_capital
												,vl_monto_interes         --monto_interes
												,vl_monto_multa           --monto_multa
												,A.VALOR_RECAUDADO_EFECTIVO --monto_pesos
												,0                        --diferencia_pesos
												,vl_cod_plan_pago         --cod_plan_pago
												,vl_cod_deuda             --cod_deuda
												,NULL                     --cod_abono_desde
												,K_ORIGEN_PAGO_BANCO      --cod_origen_pago
												,vl_cod_sub_boleta        --cod_sub_boleta
												,NULL                     --cod_documento_pago
												,vl_cod_comprobante       --cod_comprobante
												,vl_cod_abono);
                            
									IF(vl_cod_familia_deuda = KL_FAMILIA_SOLIDARIO)THEN
										-- Se Obtiene la cuenta contable del banco
										/*SELECT	cc.cod_cuenta_contable
										INTO	vl_cod_cuenta_bco
										FROM	origen_carga_banco ocb, cuenta_corriente cc
										WHERE	ocb.cod_origen_carga_banco = K_ORIGEN_CARGA_DESARROLLO
										AND		cc.cod_cuenta_corriente = ocb.cod_cuenta_corriente;*/
										vl_cod_cuenta_bco := 11103; --BANCO SCOTIABANK
										
										spu_item_comprobante ('INSERT', NULL, vl_cod_comp_solidario, vl_cod_cuenta_bco,  A.VALOR_RECAUDADO_EFECTIVO, 0);
										spu_analitico ('INSERT', NULL, vl_cod_comp_solidario, vl_cod_deuda, vl_cod_cuenta_bco, A.VALOR_RECAUDADO_EFECTIVO, 0);

										sp_comp_ing (vl_cod_comp_solidario, vl_cod_abono);

										-- update al abono con el nro comprobante
										UPDATE  abono
										SET     cod_comprobante = vl_cod_comp_solidario
												,fecha_deposito = A.FECHA_DEPOSITO
										WHERE   cod_abono = vl_cod_abono;
									ELSE
										-- update al abono con el nro comprobante
										UPDATE	abono
										SET		fecha_deposito = A.FECHA_DEPOSITO
										WHERE	cod_abono = vl_cod_abono;
									END IF;

									--marca la sub_boleta pagada
									UPDATE  sub_boleta
									SET     fecha_pago = A.FECHA_REAL_PAGO
									WHERE   cod_sub_boleta = vl_cod_sub_boleta;

									/*se marca el campo cargado = 'S', esto quiere decir que se proceso y se cargo todo correctamente
									solicitado en usach para historial*/
									UPDATE  usach_cb_desarrollo
									SET     cargado = 'S'
									WHERE   cod_usach_cb_desarrollo = a.cod_usach_cb_desarrollo;
				                END IF; --sum_monto
							ELSE
				               	--dbms_output.put_line('VALOR_RECAUDADO_EFECTIVO =====' || A.VALOR_RECAUDADO_EFECTIVO);
								vl_mensaje := 'La sub-boleta nº: ' || To_char(A.NRO_CUOTA) || ', de la boleta cod: ' || To_char(vl_cod_boleta) || ' No tiene Valor Recaudado en Efectivo.';
								spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
							END IF; --A.VALOR_RECAUDADO_EFECTIVO
						END IF;--vl_fecha_pago
					END IF; ----vl_count_registros
				-- actualiza avance
				spu_ejecucion_proceso ('SET_ACTUAL', ve_cod_ejecucion_proceso, vl_i);
				vl_i := vl_i + 1;
			END LOOP;

			sp_comp_simplifica (vl_cod_comp_solidario);
			SELECT	count(*)
			INTO	vl_count_comprobante
			FROM	item_comprobante
			WHERE	cod_comprobante = vl_cod_comp_solidario;
			
			IF (vl_count_comprobante > 0) THEN
				spu_comprobante ('APROBAR', vl_cod_comp_solidario, vl_fecha_deposito);
			END IF;

			--delete usach_cb_desarrollo;

			vl_i := vl_i - 1;
			spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, vl_i);

		EXCEPTION
			WHEN OTHERS THEN
				-- Si ocurre un error de la BD al grabar un registro lo registra en el LOG. del sistema
				spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', SQLCODE, SUBSTR(SQLERRM,1,100), 'spp_usach_cb_desarrollo: Boleta: ' || To_Char(vl_cod_boleta));
				spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, vl_i);

		END;
END spp_usach_cb_desarrollo;
/