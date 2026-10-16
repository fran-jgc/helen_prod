PROMPT CREATE OR REPLACE PROCEDURE spp_usach_cb_huara
CREATE OR REPLACE PROCEDURE spp_usach_cb_huara(ve_cod_usuario NUMBER)
AS
BEGIN
	DECLARE
		vl_i						NUMBER;
		vl_cod_plan_pago			NUMBER;
		vl_count_registros			NUMBER;
		vl_mensaje					VARCHAR2(100);
		K_CARGA_BANCO				NUMBER;
		vl_cod_ejecucion_proceso	NUMBER;
		vl_count_rut				NUMBER;
		vl_exist_deuda				NUMBER;
		vl_exist_pp_ano_cobro		NUMBER;
		vl_cod_origen_pago			NUMBER;

		--//CURSOR
		CURSOR c_usach_cb_huara IS
			SELECT	 COD_USACH_CB_HUARA
					,RUT
					,COD_DEUDA
					,FECHA_ABONO
					,FECHA_DEPOSITO
					,FOLIO
					,MONTO_CAPITAL_UTM
					,MONTO_INTERES_UTM
					,MONTO_INTERES_NORMAL_UTM
					,MONTO_CAPITAL_PESOS
					,MONTO_INTERES_NORMAL_PESOS
					,MONTO_INTERES_MORATORIO_PESOS
					,MONTO_DESCUENTO_UTM
					,MONTO_DESCUENTO_PESOS
					,COD_COMPONENTE_DEUDA
					,COD_ENTIDAD_ORIGEN_PAGO
					,FECHA_INGRESO_ABONO
					,ANO_PAGO
					,PROCESADO
					,CARGADO
			FROM	usach_cb_huara
			WHERE   PROCESADO = 'N'
			AND     CARGADO = 'N'
			ORDER BY COD_USACH_CB_HUARA ASC;

	BEGIN
		K_CARGA_BANCO := 19;
		spu_ejecucion_proceso ('NEW', NULL, K_CARGA_BANCO, ve_cod_usuario);
		COMMIT;
		
		SELECT	ejecucion_proceso_sq.CURRVAL
		INTO	vl_cod_ejecucion_proceso
		FROM	dual;

		-- calcula el total de registros
		SELECT	Count(*)
		INTO	vl_count_registros
		FROM	usach_cb_huara
		WHERE	procesado = 'N'
		  AND	cargado = 'N';

  		spu_ejecucion_proceso ('SET_TOTAL', vl_cod_ejecucion_proceso, vl_count_registros);
	    vl_i := 1;
		COMMIT;
		
		FOR a IN c_usach_cb_huara LOOP
			-- cuando entra al loop actualiza la tabla, y se marca procesado con 'S' para efecto de historial
			SELECT  Count(*)
			INTO    vl_count_rut
			FROM    deudor
			WHERE   rut = a.rut;

			IF(vl_count_rut = 0)THEN
				vl_mensaje := 'El Rut: ' || To_char(a.rut) || ' No EXISTE';
				spu_error_proceso ('INSERT', NULL, vl_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
			ELSE

				-- valida que el deudor tenga deuda
				SELECT  Count(*)
				INTO    vl_exist_deuda
				FROM    deuda
				WHERE   cod_deuda = a.COD_DEUDA;

				IF(vl_exist_deuda = 0)THEN
					vl_mensaje := 'La deuda : ' || To_char(a.cod_deuda) || ' No EXISTE o No CORRESPONDE, Rut deudor: '|| To_char(a.rut);
					spu_error_proceso ('INSERT', NULL, vl_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
				ELSE
					SELECT  Count(*)
					INTO    vl_exist_pp_ano_cobro
					FROM    plan_pago
					WHERE   cod_deuda = a.cod_deuda
					AND     ano_cobro = a.ANO_PAGO;

					IF(vl_exist_pp_ano_cobro = 0)THEN
						vl_mensaje := 'Para la deuda: ' || To_char(a.cod_deuda) || ' No EXISTE plan pago con año cobro: '|| To_char(a.ano_pago)|| ', Rut deudor: '||To_char(a.rut);
						spu_error_proceso ('INSERT', NULL, vl_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
					ELSE
						/*cod_plan_pago*/
						SELECT  cod_plan_pago
						INTO    vl_cod_plan_pago
						FROM    plan_pago
						WHERE   cod_deuda = a.cod_deuda
						AND     ano_cobro = a.ano_pago;

						/*
						75	TESORERIA
						43	TRANSBANK WEBPAY
						80	TRANSBANK
						48	BANCO DEL DESARROLLO 25% DESCUENTO
						49	BANCO DEL DESARROLLO 20% DESCUENTO
						95	SERVIPAG
						*/

						/**origen pago**/
						/*
						IF(a.cod_entidad_origen_pago = 75)THEN
							vl_cod_origen_pago := 2; --'TESORERIA'
						ELSIF(a.cod_entidad_origen_pago = 43 OR a.cod_entidad_origen_pago = 80)THEN
							vl_cod_origen_pago := 21; --'TRANSBANK WEBPAY';
				        ELSIF(a.cod_entidad_origen_pago = 48 OR a.cod_entidad_origen_pago = 49)THEN
							vl_cod_origen_pago := 18; --'BANCO DEL DESARROLLO';
						ELSIF(a.cod_entidad_origen_pago = 95)THEN
							vl_cod_origen_pago := 22; --'SERVIPAG';
						END IF;
						*/
							vl_cod_origen_pago := a.cod_entidad_origen_pago + 100;

					IF(a.monto_capital_utm + a.monto_interes_utm + a.monto_interes_normal_utm > 0)THEN
						INSERT INTO abono
									(cod_abono
									,fecha_registro
									,fecha_abono
									,cod_usuario
									,cod_componente_deuda
									,monto_capital
									,monto_interes
									,monto_multa
									,monto_moneda
									,monto_pesos
									,diferencia_pesos
									,cod_plan_pago
									,cod_deuda
									,cod_abono_desde
									,cod_estado_pago
									,cod_origen_pago
									,cod_sub_boleta
									,cod_documento_pago
									,cod_comprobante
									,folio_carga)
							VALUES (abono_sq.NEXTVAL
									,a.fecha_ingreso_abono
									,a.fecha_abono
									,ve_cod_usuario
									,a.cod_componente_deuda
									,a.monto_capital_utm
									,a.monto_interes_utm
									,a.monto_interes_normal_utm
									,a.monto_capital_utm + a.monto_interes_utm + a.monto_interes_normal_utm
									,a.monto_capital_pesos	+ a.monto_interes_normal_pesos + a.monto_interes_moratorio_pesos
									,0
									,vl_cod_plan_pago
									,a.cod_deuda
									,NULL	--ve_cod_abono_desde
									,1		--K_ESTADO_PAGO_VIGENTE
									,vl_cod_origen_pago   --COD_ENTIDAD_ORIGEN_PAGO
									,NULL
									,NULL
									,NULL
									,a.folio);
					END IF;
							
					IF(a.monto_descuento_utm > 0)THEN
						INSERT INTO descuento
								(COD_DESCUENTO
								,FECHA_REGISTRO
								,FECHA_DESCUENTO
								,COD_USUARIO
								,COD_COMPONENTE_DEUDA
								,MONTO_CAPITAL
								,MONTO_INTERES
								,MONTO_MULTA
								,MONTO_MONEDA
								,MONTO_PESOS
								,COD_PLAN_PAGO
								,COD_DEUDA
								,COD_COMPROBANTE
								,COD_ESTADO_PAGO
								,COD_ORIGEN_PAGO
								,COD_SUB_BOLETA)
						VALUES	(descuento_sq.NEXTVAL
								,a.fecha_ingreso_abono
								,a.fecha_ingreso_abono
								,ve_cod_usuario
								,a.cod_componente_deuda
								,a.monto_descuento_utm
								,0
								,0
								,a.monto_descuento_utm
								,a.monto_descuento_pesos
								,vl_cod_plan_pago
								,a.cod_deuda
								,NULL
								,1		--K_ESTADO_PAGO_VIGENTE ve_cod_estado_pago
								,vl_cod_origen_pago --COD_ENTIDAD_ORIGEN_PAGO
								,NULL);
					END IF;
					--  actualizaq el estado PP
					sp_pp_actualiza_estado(vl_cod_plan_pago);
					sp_deuda_actualiza_estado(a.cod_deuda);

					UPDATE  usach_cb_huara
					SET     cargado = 'S'
					WHERE   cod_usach_cb_huara = a.cod_usach_cb_huara;

					END IF; --vl_exist_pp_ano_cobro
				END IF; --vl_exist_deuda
			END IF; --vl_count_rut
			UPDATE  usach_cb_huara
			SET     procesado = 'S'
			WHERE   cod_usach_cb_huara = a.cod_usach_cb_huara;
			vl_i := vl_i + 1;
		END LOOP;

		--delete usach_cb_huara;

		vl_i := vl_i - 1;
	    spu_ejecucion_proceso ('END', vl_cod_ejecucion_proceso, vl_i);

	EXCEPTION
		WHEN OTHERS THEN
			-- Si ocurre un error de la BD al grabar un registro lo registra en el LOG. del sistema
			spu_error_proceso('INSERT', NULL, vl_cod_ejecucion_proceso, 'E', SQLCODE, SUBSTR(SQLERRM,1,100), 'Carga de usach_cb_huara.');
			spu_ejecucion_proceso ('END', vl_cod_ejecucion_proceso, vl_i);

	END;
END spp_usach_cb_huara;
/