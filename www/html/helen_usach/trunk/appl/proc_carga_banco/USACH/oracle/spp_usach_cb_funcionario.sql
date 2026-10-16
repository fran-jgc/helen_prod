PROMPT CREATE OR REPLACE PROCEDURE spp_usach_cb_funcionario
CREATE OR REPLACE PROCEDURE spp_usach_cb_funcionario(ve_cod_ejecucion_proceso number)
AS
BEGIN

	declare vl_current_date				  DATE:=trunc(sysdate);
			    vl_nom_comprobante		  VARCHAR2(100);
			    KL_FAMILIA_SOLIDARIO		NUMBER:=1;
			    
			    /*
				 * los comprobantes se creaban con estado aprobado el cual [fabiola.arriagada@usach.cl] enviado el 21/10/2014 
				 * en un correo dijo que siempre se deben crear con estado pendiente
				 * jv 22/10/2014
				 * */
			    K_COMP_PENDIENTE		NUMBER:=1;
			    --K_COMP_CONTABILIZADO		NUMBER:=2;
			    
			    K_COMP_INGRESO			    NUMBER:=1;
			    K_COMP_CARGA_FUNCIONARIO			  NUMBER:=44;
			    vl_cod_comp_solidario		NUMBER;
			    vl_cod_cuenta_bco			  NUMBER;
			    vl_cod_abono				    NUMBER;
			    vl_cod_usuario			    NUMBER;
			    vl_count_registros		  NUMBER;
			    vl_i							      NUMBER;
			    vl_mensaje				      VARCHAR2(100);
			    vl_count_deuda			    NUMBER;
			    K_ESTADO_PP_MOROSO		  NUMBER:=4;
			    vl_count_morosa			    NUMBER;
			    vl_cod_plan_pago			  NUMBER;
			    vl_cod_deuda				    NUMBER;
			    vl_cod_moneda				    NUMBER;
			    vl_monto_pago			  NUMBER;
			    K_ORIGEN_FUNCIONARIO		  NUMBER:=16;
          		K_ORIGEN_PAGO_FUNCIONARIO		NUMBER:=25;
			    vl_total_abono			    NUMBER;
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
          vl_ano_cobro            NUMBER;
          K_ESTADO_PP_EMITIDO           NUMBER:=1;
          K_ESTADO_PP_ABONADO           NUMBER:=2;
          KL_ESTADO_DEUDA_NORMAL		NUMBER:=2;
          vl_fecha_deposito				DATE;


  CURSOR c_cursor IS
	select	cod_usach_cb_funcionario
          ,cod_deuda
			    ,monto_pago
			    ,fecha_deposito
          ,folio_carga
          ,fecha_real_pago
	from	  usach_cb_funcionario
  WHERE   cargado = 'N'
  AND     procesado = 'N';

  BEGIN

	-- usuario que ejecuta el proceso
	SELECT	cod_usuario
  INTO    vl_cod_usuario
	FROM	  ejecucion_proceso
	WHERE	  cod_ejecucion_proceso = ve_cod_ejecucion_proceso;

	-- calcula el total de registros
	SELECT  Count(*)
  INTO    vl_count_registros
	FROM	  usach_cb_funcionario
  WHERE   cargado = 'N'
  AND     procesado = 'N';

	spu_ejecucion_proceso ('SET_TOTAL', ve_cod_ejecucion_proceso, vl_count_registros);
  vl_i := 1;

  SELECT	FECHA_DEPOSITO
into	vl_fecha_deposito
FROM    usach_cb_funcionario
WHERE   PROCESADO = 'N'
AND     CARGADO = 'N'
AND ROWNUM = 1;

	/*
	 * los comprobantes se creaban con estado aprobado el cual [fabiola.arriagada@usach.cl] enviado el 21/10/2014 
	 * en un correo dijo que siempre se deben crear con estado pendiente
	 * jv 22/10/2014
	 * */
	--crea comprobante para fondo solidario
  vl_nom_comprobante := 'Comprobante Carga de Ingreso: DEUDORES FUNCIONARIOS, '|| TO_CHAR(vl_fecha_deposito, 'dd/mm/yyyy');
  --spu_comprobante ('INSERT', NULL, vl_fecha_deposito, vl_cod_usuario, vl_nom_comprobante, KL_FAMILIA_SOLIDARIO, K_COMP_CONTABILIZADO, K_COMP_INGRESO, K_COMP_CARGA_FUNCIONARIO, vl_cod_usuario, vl_fecha_deposito);
  spu_comprobante ('INSERT', NULL, vl_fecha_deposito, vl_cod_usuario, vl_nom_comprobante, KL_FAMILIA_SOLIDARIO, K_COMP_PENDIENTE, K_COMP_INGRESO, K_COMP_CARGA_FUNCIONARIO, vl_cod_usuario, vl_fecha_deposito);

  SELECT  comprobante_sq.CURRVAL
	INTO    vl_cod_comp_solidario
	FROM    dual;

  FOR a IN c_cursor LOOP

    -- cuando entra al loop actualiza la tabla, y se marca procesado con 'S' para efecto de historial
 	  UPDATE  usach_cb_funcionario
    SET     procesado = 'S'
    WHERE   cod_usach_cb_funcionario = a.cod_usach_cb_funcionario;

 	  -- verifica si el proceso ha sido cancelado
		IF (f_ep_cancela_proceso(ve_cod_ejecucion_proceso) = 'S')THEN
			RETURN;
    END IF;

		-- valida que exista la deuda
		SELECT	count(*)
    INTO    vl_count_deuda
		FROM	  deuda
		WHERE   cod_deuda = a.cod_deuda;

		IF(vl_count_deuda = 0)THEN
			vl_mensaje := 'La deuda: '||to_char(a.cod_deuda)||' No EXISTE';
			spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
		ELSE
			--valida que la deuda sea familia solidario
			SELECT	count(*)
      INTO    vl_count_deuda
			FROM	  deuda da, tipo_deuda td
			WHERE cod_deuda = a.cod_deuda  
      AND   da.cod_tipo_deuda = td.cod_tipo_deuda
			AND		td.cod_familia_deuda = KL_FAMILIA_SOLIDARIO
			AND		da.cod_estado_deuda = KL_ESTADO_DEUDA_NORMAL;

			IF(vl_count_deuda = 0)THEN
				vl_mensaje := 'La deuda: '||to_char(a.cod_deuda)||' No corresponde a la Familia Fondo Solidario';
				spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
			ELSE

				SELECT  min(ano_cobro)
        INTO    vl_ano_cobro
        FROM    plan_pago p, deuda da, tipo_deuda td
        WHERE   da.cod_deuda = a.cod_deuda
        AND     p.cod_deuda = da.cod_deuda 
        AND     cod_estado_plan_pago in(K_ESTADO_PP_EMITIDO, K_ESTADO_PP_ABONADO ,K_ESTADO_PP_MOROSO)
        AND     td.cod_tipo_deuda = da.cod_tipo_deuda
        AND     cod_familia_deuda = KL_FAMILIA_SOLIDARIO
        AND		  f_pp_saldo_plan_pago(cod_plan_pago, sysdate) > 0
        AND		da.cod_estado_deuda = KL_ESTADO_DEUDA_NORMAL;

        SELECT  cod_plan_pago
							  ,cod_moneda
        INTO    vl_cod_plan_pago
                ,vl_cod_moneda
        FROM    deuda da, plan_pago p, tipo_deuda td
        WHERE   da.cod_deuda = a.cod_deuda
        AND     p.cod_deuda = da.cod_deuda 
        AND     da.cod_tipo_deuda = td.cod_tipo_deuda
        AND     cod_estado_plan_pago in(K_ESTADO_PP_EMITIDO, K_ESTADO_PP_ABONADO ,K_ESTADO_PP_MOROSO)
        AND     ano_cobro = vl_ano_cobro
        AND     cod_familia_deuda = KL_FAMILIA_SOLIDARIO
        AND		da.cod_estado_deuda = KL_ESTADO_DEUDA_NORMAL;

        IF(a.monto_pago > 0)THEN

					vl_valor_moneda		      := f_mon_get_valor_moneda(vl_cod_moneda, a.fecha_real_pago);
					vl_monto_capital_pesos  := round(f_pp_saldo_capital_caja(vl_cod_plan_pago, a.fecha_real_pago) * vl_valor_moneda, 0);
					vl_monto_interes_pesos  := round(f_pp_saldo_interes_normal_caja(vl_cod_plan_pago, a.fecha_real_pago) * vl_valor_moneda, 0);
					vl_monto_multa_pesos	  := round(f_pp_saldo_multa_caja(vl_cod_plan_pago, a.fecha_real_pago) * vl_valor_moneda, 0);
					--vl_monto_moroso := 	vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos;

          IF ((vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos) <> 0) THEN

            -- porporcion K, IN, IP
					  vl_factor_K  := vl_monto_capital_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
					  vl_factor_IN := vl_monto_interes_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
					  vl_factor_IP := vl_monto_multa_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);

					  vl_monto_capital	:= Round((a.monto_pago / vl_valor_moneda) * vl_factor_K, 4);--Round((vl_total_abono / vl_valor_moneda) * vl_factor_K, 4);
					  vl_monto_interes	:= Round((a.monto_pago / vl_valor_moneda) * vl_factor_IN, 4);--Round((vl_total_abono / vl_valor_moneda) * vl_factor_IN, 4);
					  vl_monto_multa		:= Round((a.monto_pago / vl_valor_moneda) * vl_factor_IP, 4);--Round((vl_total_abono / vl_valor_moneda) * vl_factor_IP, 4);

            -- si no cuadra con el total_moneda que se esta pagando, se agrega la diferencia al abono K
				    IF ((vl_monto_capital + vl_monto_interes + vl_monto_multa) <> round(a.monto_pago / vl_valor_moneda,4)) THEN
				      vl_monto_capital := vl_monto_capital + (round(a.monto_pago / vl_valor_moneda,4) - (vl_monto_capital + vl_monto_interes + vl_monto_multa));
				    END IF;

					  spu_abono_carga(a.fecha_real_pago		  -- fecha_abono
 									  ,vl_cod_usuario		  -- cod_usuario
									  ,vl_monto_capital		-- monto_capital
									  ,vl_monto_interes		-- monto_interes
									  ,vl_monto_multa		  -- monto_multa
									  ,a.monto_pago  -- monto_pesos
									  ,0						      -- diferencia_pesos
									  ,vl_cod_plan_pago		-- cod_plan_pago
									  ,a.cod_deuda			  -- cod_deuda
									  ,null					      -- cod_abono_desde
									  ,K_ORIGEN_PAGO_FUNCIONARIO	-- cod_origen_pago
									  ,null					      -- cod_sub_boleta
									  ,null					      -- cod_documento_pago
									  ,vl_cod_comp_solidario -- cod_comprobante
									  ,vl_cod_abono );


    			  -- Se Obtiene la cuenta contable del banco
					 /* SELECT	cc.cod_cuenta_contable
            INTO    vl_cod_cuenta_bco
					  FROM	  origen_carga_banco ocb, cuenta_corriente cc
					  WHERE	  ocb.cod_origen_carga_banco = K_ORIGEN_FUNCIONARIO
						  AND	  cc.cod_cuenta_corriente = ocb.cod_cuenta_corriente;*/
					vl_cod_cuenta_bco := 11102; --BANCO DE CHILE

					  spu_item_comprobante('INSERT', NULL, vl_cod_comp_solidario, vl_cod_cuenta_bco,  a.monto_pago, 0);
					  spu_analitico('INSERT', NULL, vl_cod_comp_solidario, vl_cod_deuda, vl_cod_cuenta_bco, a.monto_pago, 0);

					  sp_comp_ing(vl_cod_comp_solidario, vl_cod_abono);

					  --se debe hacer update a todos los abonos que se crean
            UPDATE  abono
					  SET		  fecha_deposito = a.fecha_deposito
                    ,folio_carga = a.folio_carga
					  WHERE	  fecha_abono = a.fecha_real_pago
            AND     cod_origen_pago = K_ORIGEN_PAGO_FUNCIONARIO
            AND     cod_deuda = a.cod_deuda;
            /*se marca el campo cargado = 'S', esto quiere decir que se proceso y se cargo todo correctamente
            solicitado en usach para historial*/

            UPDATE  usach_cb_funcionario
            SET     cargado = 'S'
            WHERE   cod_usach_cb_funcionario = a.cod_usach_cb_funcionario;

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
			spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', SQLCODE, SUBSTR(SQLERRM,1,100), 'spp_usach_cb_funcionario: plan_pago: ' || To_Char(vl_cod_plan_pago));
			spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, vl_i);

  END;
END;
/