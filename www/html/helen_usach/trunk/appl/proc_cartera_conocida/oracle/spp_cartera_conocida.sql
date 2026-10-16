PROMPT CREATE OR REPLACE PROCEDURE spp_cartera_conocida
CREATE OR REPLACE PROCEDURE spp_cartera_conocida(ve_cod_ejecucion_proceso	number
												,ve_cod_usuario				number
												,ve_fecha_boleta			date)
AS
BEGIN
		DECLARE K_TIPO_DEUDA_COBRANZA_NORMAL	NUMBER:= 2;
		k_TIPO_MONEDA_UTM						NUMBER:= 3;
		K_COMPONENTE_DEUDA_VIGENTE				NUMBER:= 3;
		vl_contador								NUMBER;
		vl_cod_boleta							NUMBER;
		vl_cant_sub_boleta						NUMBER;
		vl_monto_capital						NUMBER;
		vl_monto_interes						NUMBER;
		vl_cod_plan_pago_err					NUMBER;
		i										NUMBER;
		vl_count								NUMBER;
		vc_cod_plan_pago						NUMBER;
		vc_monto_moneda							NUMBER;
		vc_cod_deuda							NUMBER;
		vc_fecha_vencto							DATE;
		vl_monto_pesos							NUMBER;
		vl_K_bol								NUMBER;
		vl_IN_bol								NUMBER;
		vl_K_sub								NUMBER;
		vl_IN_sub								NUMBER;
		vl_ultima_sub_boleta					NUMBER;
		vl_dif_K								NUMBER;
		vl_dif_IN								NUMBER;
		vl_total_monto_capital					NUMBER;
		vl_total_monto_interes					NUMBER;

	CURSOR c_plan_pago IS
			SELECT   PP.COD_PLAN_PAGO
					,PP.MONTO_CAPITAL + PP.MONTO_INTERES  MONTO_MONEDA
					,DE.COD_DEUDA
					,PP.FECHA_VENCTO
					,PP.MONTO_CAPITAL
			FROM	PLAN_PAGO PP, DEUDA DE
			WHERE	PP.COD_DEUDA = DE.COD_DEUDA
			  AND	PP.ANO_COBRO = To_Number(To_Char(ve_fecha_boleta, 'yyyy'))
			  AND	DE.COD_ESTADO_DEUDA = K_TIPO_DEUDA_COBRANZA_NORMAL
			  AND	PP.MONTO_CAPITAL > 0
			  AND	f_pp_saldo_plan_pago(PP.COD_PLAN_PAGO, ve_fecha_boleta) > 0
			  AND	PP.COD_PLAN_PAGO NOT IN	(SELECT nvl(COD_PLAN_PAGO,0) 
											 FROM BOLETA 
											 WHERE CARTERA_CONOCIDA = 'S');

  BEGIN
	------------------------------------------------------------------------------------------
	-- MAIN
	------------------------------------------------------------------------------------------

		SELECT	COUNT(*)
		INTO	vl_count
		FROM	PLAN_PAGO PP, DEUDA DE
		WHERE	PP.COD_DEUDA = DE.COD_DEUDA
		  AND	PP.ANO_COBRO = To_Number(To_Char(ve_fecha_boleta, 'yyyy'))
		  AND	DE.COD_ESTADO_DEUDA = K_TIPO_DEUDA_COBRANZA_NORMAL
		  AND	PP.MONTO_CAPITAL > 0
		  AND	f_pp_saldo_plan_pago(PP.COD_PLAN_PAGO, ve_fecha_boleta) > 0
		  AND	PP.COD_PLAN_PAGO NOT IN (SELECT nvl(COD_PLAN_PAGO,0)
										 FROM BOLETA
										 WHERE CARTERA_CONOCIDA = 'S');

	spu_ejecucion_proceso ('SET_TOTAL', ve_cod_ejecucion_proceso, vl_count);
	i := 1;

	FOR A IN c_plan_pago LOOP
		-- verifica si el proceso ha sido cancelado
        IF (f_ep_cancela_proceso(ve_cod_ejecucion_proceso) = 'S') THEN
          COMMIT;
          RETURN;
        END IF;
        
		IF(A.MONTO_CAPITAL <= 0)THEN
			spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, 'Plan Pago: ' || To_Char(A.COD_PLAN_PAGO) || ' tiene monto capital igual o menor a cero.');
		ELSE
			vl_cod_plan_pago_err := a.cod_plan_pago;
			vl_monto_pesos := round(a.monto_moneda * f_mon_get_valor_moneda(k_TIPO_MONEDA_UTM, ve_fecha_boleta),0);
		
			--SE CREA LA BOLETA
			INSERT INTO boleta
						(cod_boleta
						, fecha_boleta
						, cod_usuario
						, cod_deuda
						, cod_plan_pago
						, monto_capital
						, monto_interes
						, monto_multa
						, monto_moneda
						, monto_pesos
						, dscto_capital
						, dscto_interes
						, dscto_multa
						, dscto_pesos
						, fecha_primera_cuota
						, cant
						, cartera_conocida
						, cod_ejecucion_proceso)
				VALUES (boleta_sq.nextval
						, ve_fecha_boleta
						, ve_cod_usuario
						, a.cod_deuda
						, a.cod_plan_pago
						, f_pp_saldo_capital(a.cod_plan_pago, ve_fecha_boleta)
						, f_pp_saldo_interes_normal(a.cod_plan_pago, ve_fecha_boleta)
						, 0
						, a.monto_moneda
						, vl_monto_pesos
						, 0
						, 0
						, 0
						, 0
						, a.fecha_vencto
						, f_carta_get_cant_sub_boleta(a.cod_plan_pago)
						, 'S'
						, ve_cod_ejecucion_proceso);

			SELECT  boleta_sq.CURRVAL
			INTO    vl_cod_boleta
			FROM    DUAL;

			vl_cant_sub_boleta  := f_carta_get_cant_sub_boleta(a.cod_plan_pago);
			vl_contador         := 1;

			SELECT	 MONTO_CAPITAL / vl_cant_sub_boleta
					,MONTO_INTERES / vl_cant_sub_boleta
					,MONTO_CAPITAL
					,MONTO_INTERES
			INTO	vl_monto_capital
					,vl_monto_interes
					,vl_total_monto_capital
					,vl_total_monto_interes
			FROM	BOLETA
			WHERE	COD_BOLETA = vl_cod_boleta;

			WHILE (vl_contador <= vl_cant_sub_boleta) LOOP
				IF (vl_contador = vl_cant_sub_boleta) THEN
					vl_monto_capital := vl_total_monto_capital - (vl_monto_capital * (vl_contador-1));
					vl_monto_interes := vl_total_monto_interes - (vl_monto_interes * (vl_contador-1));
				END IF;
				
				--SE CREA SUB-BOLETA
				INSERT INTO sub_boleta
							(cod_sub_boleta
							, cod_boleta
							, nro_sub_boleta
							, fecha_vencto
							, monto_capital
							, monto_interes
							, monto_multa
							, fecha_pago
							, cod_componente_deuda)
					VALUES (sub_boleta_sq.nextval
							, vl_cod_boleta
							, vl_contador
							, a.fecha_vencto
							, vl_monto_capital
							, vl_monto_interes
							, 0
							, NULL
							, K_COMPONENTE_DEUDA_VIGENTE);

				vl_contador := vl_contador + 1;
			END LOOP;
			
			/*obtengo el MONTO_K y el MONTO_IN de BOLETA*/
	        SELECT monto_capital
	              ,monto_interes
	        INTO  vl_K_bol
	              ,vl_IN_bol
	        FROM boleta
	        WHERE cod_boleta = vl_cod_boleta;
	
	        /*obtengo el sum total de MONTO_K y MONTO_IN de SUB_BOLETA*/
	        SELECT Sum(monto_capital)
	                ,Sum(monto_interes)
	        INTO  vl_K_sub
	              ,vl_IN_sub
	        FROM sub_boleta
	        WHERE cod_boleta = vl_cod_boleta;
	
	        /*obtengo la ultima sub_boleta creada de la boleta*/
	        SELECT Max(cod_sub_boleta)
	        INTO    vl_ultima_sub_boleta
	        FROM sub_boleta
	        WHERE cod_boleta = vl_cod_boleta;
	
	        /*obtengo la diferencia del capital y el interes*/
	        vl_dif_K := vl_K_bol -  vl_K_sub;
	        vl_dif_IN:= vl_IN_bol - vl_IN_sub;
	
	        /* se hace un update a la ultima sub_boleta creada para que los montos cuadren con 
	        el valor que esta en boleta*/
	        UPDATE sub_boleta
	        SET monto_capital = monto_capital + vl_dif_K
	            ,monto_interes = monto_interes + vl_dif_IN
	        WHERE cod_sub_boleta = vl_ultima_sub_boleta;      

		END IF;

		spu_ejecucion_proceso ('SET_ACTUAL', ve_cod_ejecucion_proceso, i);
		i := i + 1;
	END LOOP;
	
	i := i - 1;

	spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, i);

	EXCEPTION
	WHEN OTHERS THEN
		-- Si ocurre un error de la BD al grabar un registro lo registra en el LOG. del sistema
		spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', SQLCODE, SubStr(SQLERRM,1,100), 'plan pago:...');
		COMMIT;
	END;
END spp_cartera_conocida;
/