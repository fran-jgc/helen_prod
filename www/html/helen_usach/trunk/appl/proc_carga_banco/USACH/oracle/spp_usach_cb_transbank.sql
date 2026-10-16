PROMPT CREATE OR REPLACE PROCEDURE spp_usach_cb_transbank
CREATE OR REPLACE PROCEDURE spp_usach_cb_transbank(ve_cod_ejecucion_proceso number)
AS
BEGIN

	declare vl_current_date				  DATE:=Trunc(SYSDATE);
			    vl_cod_usuario			    NUMBER;
			    vl_count_registros		  NUMBER;
			    vl_i							      NUMBER;
			    vl_nom_comprobante		  VARCHAR2(100);
			    K_FAMILIA_SOLIDARIO		  NUMBER:=1;
			    /*
				 * los comprobantes se creaban con estado aprobado el cual [fabiola.arriagada@usach.cl] enviado el 21/10/2014 
				 * en un correo dijo que siempre se deben crear con estado pendiente
				 * jv 22/10/2014
				 * */
			    K_COMP_PENDIENTE		NUMBER:=1;
			    --K_COMP_CONTABILIZADO		NUMBER:=2;

			    K_COMP_INGRESO			    NUMBER:=1;
			    K_ORIGEN_PAGO_TRANSBANK	NUMBER:=21;
			    vl_cod_comp_solidario		NUMBER;
          vl_count_sub_boleta     NUMBER;
          vl_mensaje				      VARCHAR2(100);
          vl_count_sb_f_solidario NUMBER;
          vl_cod_plan_pago        NUMBER;
          vl_cod_deuda            NUMBER;
          vl_cod_moneda           NUMBER;
          vl_valor_moneda			    NUMBER;
			    vl_monto_capital_pesos	NUMBER;
			    vl_monto_interes_pesos	NUMBER;
			    vl_monto_multa_pesos		NUMBER;
			    vl_factor_K				      NUMBER;
			    vl_factor_IN				    NUMBER;
			    vl_factor_IP				    NUMBER;
			    vl_monto_capital			  NUMBER;
			    vl_monto_interes			  NUMBER;
			    vl_monto_multa			    NUMBER;
          K_COMP_CARGA_TRANSBANK  NUMBER:=32;
          vl_cod_cuenta_bco			  NUMBER;
			    vl_cod_abono				    NUMBER;
          K_ORIGEN_TRANSBANK      NUMBER:=5;
          vl_cod_comprobante      NUMBER;
          vl_cod_familia_deuda    NUMBER;
          vl_fecha_pago           DATE;
          vl_fecha_deposito		date;

  CURSOR c_cursor IS
	SELECT	 cod_usach_cb_transbank
          ,fecha_deposito -- este es fecha_abono
          ,cod_sub_boleta
			    ,monto
			    ,fecha_abono -- este campo es fecha_deposito
          ,procesado
          ,cargado
	FROM	  usach_cb_transbank
  WHERE   procesado = 'N'
  AND     cargado = 'N';

  BEGIN

	-- usuario que ejecuta el proceso
	SELECT	cod_usuario
  INTO    vl_cod_usuario
	FROM	  ejecucion_proceso
	WHERE	  cod_ejecucion_proceso = ve_cod_ejecucion_proceso;

	-- calcula el total de registros
	SELECT  Count(*)
  INTO    vl_count_registros
	FROM	  usach_cb_servipag
  WHERE   procesado = 'N'
  AND     cargado = 'N'
  AND ROWNUM = 1;

	spu_ejecucion_proceso ('SET_TOTAL', ve_cod_ejecucion_proceso, vl_count_registros);
  vl_i := 1;

    SELECT	FECHA_DEPOSITO
into	vl_fecha_deposito
FROM    usach_cb_servipag
WHERE   PROCESADO = 'N'
AND     CARGADO = 'N';

/*
 * los comprobantes se creaban con estado aprobado el cual [fabiola.arriagada@usach.cl] enviado el 21/10/2014 
 * en un correo dijo que siempre se deben crear con estado pendiente
 * jv 22/10/2014
 * */
	--crea comprobante para fondo solidario
  vl_nom_comprobante := 'Comprobante Carga de Ingreso: TRANSBANK, '|| TO_CHAR(vl_fecha_deposito, 'dd/mm/yyyy');
  --spu_comprobante ('INSERT', NULL, vl_fecha_deposito, vl_cod_usuario, vl_nom_comprobante, K_FAMILIA_SOLIDARIO, K_COMP_CONTABILIZADO, K_COMP_INGRESO, K_COMP_CARGA_TRANSBANK, vl_cod_usuario, vl_fecha_deposito);
  spu_comprobante ('INSERT', NULL, vl_fecha_deposito, vl_cod_usuario, vl_nom_comprobante, K_FAMILIA_SOLIDARIO, K_COMP_PENDIENTE, K_COMP_INGRESO, K_COMP_CARGA_TRANSBANK, vl_cod_usuario, vl_fecha_deposito);

  SELECT  comprobante_sq.CURRVAL
	INTO    vl_cod_comp_solidario
	FROM    dual;

  FOR a IN c_cursor LOOP

    -- cuando entra al loop actualiza la tabla, y se marca procesado con 'S' para efecto de historial
 	  UPDATE  usach_cb_transbank
    SET     procesado = 'S'
    WHERE   cod_usach_cb_transbank = a.cod_usach_cb_transbank;

    -- verifica si el proceso ha sido cancelado
    IF (f_ep_cancela_proceso(ve_cod_ejecucion_proceso) = 'S')THEN
			RETURN;
    END IF;

		-- valida que exista sub_boleta
		SELECT	count(*)
    INTO    vl_count_sub_boleta
		FROM	  sub_boleta
		WHERE   cod_sub_boleta = a.cod_sub_boleta;

		IF(vl_count_sub_boleta = 0)THEN
    	vl_mensaje := 'La sub boleta código: '||to_char(a.cod_sub_boleta)||' No EXISTE';
			spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
		ELSE
      --VALIDA QUE LA SUB_BOLETA NO ESTE PAGADA
      SELECT  fecha_pago
      INTO    vl_fecha_pago
      FROM    sub_boleta
      WHERE   cod_sub_boleta = a.cod_sub_boleta;

      IF(vl_fecha_pago IS NOT NULL)THEN 
        vl_mensaje := 'La sub boleta código: '||to_char(a.cod_sub_boleta)||' ya ha sido cancelada';
			  spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
      ELSE
        SELECT  b.cod_plan_pago
                ,b.cod_deuda
                ,cod_moneda
                ,cod_familia_deuda
        INTO    vl_cod_plan_pago
                ,vl_cod_deuda
                ,vl_cod_moneda
                ,vl_cod_familia_deuda
        FROM    sub_boleta sb, boleta b, deuda d, tipo_deuda td
        WHERE   cod_sub_boleta = a.cod_sub_boleta
        AND     sb.cod_boleta = b.cod_boleta
        AND     d.cod_deuda = b.cod_deuda
        AND     td.cod_tipo_deuda = d.cod_tipo_deuda;

        IF(a.monto > 0)THEN
          vl_valor_moneda		  := f_mon_get_valor_moneda(vl_cod_moneda, a.fecha_abono);
          vl_monto_capital_pesos  := round(f_pp_saldo_capital_caja(vl_cod_plan_pago, a.fecha_abono) * vl_valor_moneda, 0);
		  vl_monto_interes_pesos  := round(f_pp_saldo_interes_normal_caja(vl_cod_plan_pago, a.fecha_abono) * vl_valor_moneda, 0);
		  vl_monto_multa_pesos	  := round(f_pp_saldo_multa_caja(vl_cod_plan_pago, a.fecha_abono) * vl_valor_moneda, 0);

          IF ((vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos) <> 0) THEN
            -- porporcion K, IN, IP
					  vl_factor_K  := vl_monto_capital_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
					  vl_factor_IN := vl_monto_interes_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
					  vl_factor_IP := vl_monto_multa_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);

					  vl_monto_capital	:= Round((a.monto / vl_valor_moneda) * vl_factor_K, 4);--Round((vl_total_abono / vl_valor_moneda) * vl_factor_K, 4);
					  vl_monto_interes	:= Round((a.monto / vl_valor_moneda) * vl_factor_IN, 4);--Round((vl_total_abono / vl_valor_moneda) * vl_factor_IN, 4);
					  vl_monto_multa		:= Round((a.monto / vl_valor_moneda) * vl_factor_IP, 4);--Round((vl_total_abono / vl_valor_moneda) * vl_factor_IP, 4);

              -- si no cuadra con el total_moneda que se esta pagando, se agrega la diferencia al abono K
				    IF ((vl_monto_capital + vl_monto_interes + vl_monto_multa) <> round(a.monto / vl_valor_moneda,4)) THEN
				      vl_monto_capital := vl_monto_capital + (round(a.monto / vl_valor_moneda,4) - (vl_monto_capital + vl_monto_interes + vl_monto_multa));
				    END IF;

            IF (vl_cod_familia_deuda = K_FAMILIA_SOLIDARIO) THEN
              vl_cod_comprobante := vl_cod_comp_solidario;
            ELSE
              vl_cod_comprobante := NULL;
            END IF;

				    spu_abono_carga(a.fecha_deposito    -- fecha_abono
 									    ,vl_cod_usuario		  -- cod_usuario
									    ,vl_monto_capital		-- monto_capital
									    ,vl_monto_interes		-- monto_interes
									    ,vl_monto_multa		  -- monto_multa
									    ,a.monto            -- monto_pesos
									    ,0						      -- diferencia_pesos
									    ,vl_cod_plan_pago		-- cod_plan_pago
									    ,vl_cod_deuda			  -- cod_deuda
									    ,null					      -- cod_abono_desde
									    ,K_ORIGEN_PAGO_TRANSBANK	-- cod_origen_pago
									    ,a.cod_sub_boleta		-- cod_sub_boleta
									    ,null					      -- cod_documento_pago
									    ,vl_cod_comprobante -- cod_comprobante
									    ,vl_cod_abono );

            IF(vl_cod_familia_deuda = K_FAMILIA_SOLIDARIO)THEN
    			    -- Se Obtiene la cuenta contable del banco
					    /*SELECT	cc.cod_cuenta_contable
              INTO    vl_cod_cuenta_bco
					    FROM	  origen_carga_banco ocb, cuenta_corriente cc
					    WHERE	  ocb.cod_origen_carga_banco = K_ORIGEN_TRANSBANK
						    AND	  cc.cod_cuenta_corriente = ocb.cod_cuenta_corriente;*/
						vl_cod_cuenta_bco := 11102; --BANCO DE CHILE
						
					    spu_item_comprobante('INSERT', NULL, vl_cod_comp_solidario, vl_cod_cuenta_bco,  a.monto, 0);
					    spu_analitico('INSERT', NULL, vl_cod_comp_solidario, vl_cod_deuda, vl_cod_cuenta_bco, a.monto, 0);

					    sp_comp_ing(vl_cod_comp_solidario, vl_cod_abono);

					    --se debe hacer update a todos los abonos que se crean
              UPDATE  abono
					    SET		  fecha_deposito = a.fecha_abono
					    WHERE	  fecha_abono = a.fecha_deposito
              AND     cod_origen_pago = K_ORIGEN_PAGO_TRANSBANK;

              UPDATE sub_boleta
              SET fecha_pago = a.fecha_deposito
              WHERE cod_sub_boleta = a.cod_sub_boleta;
            ELSE
              --se debe hacer update a todos los abonos que se crean
              UPDATE  abono
					    SET		  fecha_deposito = a.fecha_abono
					    WHERE	  fecha_abono = a.fecha_deposito
              AND     cod_origen_pago = K_ORIGEN_PAGO_TRANSBANK;

              UPDATE sub_boleta
              SET fecha_pago = a.fecha_deposito
              WHERE cod_sub_boleta = a.cod_sub_boleta;

            END IF;
            /*se marca el campo cargado = 'S', esto quiere decir que se proceso y se cargo todo correctamente
            solicitado en usach para historial*/
            UPDATE  usach_cb_transbank
            SET     cargado = 'S'
            WHERE   cod_usach_cb_transbank = a.cod_usach_cb_transbank
            AND   cod_sub_boleta = a.cod_sub_boleta;
          END IF;
        END IF;
      END IF;
    END IF;
  	-- actualiza avance
		spu_ejecucion_proceso ('SET_ACTUAL', ve_cod_ejecucion_proceso, vl_i);
		vl_i := vl_i + 1;
 	END LOOP;

  sp_comp_simplifica (vl_cod_comp_solidario);
	spu_comprobante ('APROBAR', vl_cod_comp_solidario, vl_fecha_deposito);

  vl_i := vl_i -1;
  spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, vl_i);

  EXCEPTION
      WHEN OTHERS THEN
	    -- Si ocurre un error de la BD al grabar un registro lo registra en el LOG. del sistema
			spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', SQLCODE, SUBSTR(SQLERRM,1,100), 'spp_usach_cb_transbank: deuda: ' || To_Char(vl_cod_deuda));
			spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, vl_i);

  END;
END;
/