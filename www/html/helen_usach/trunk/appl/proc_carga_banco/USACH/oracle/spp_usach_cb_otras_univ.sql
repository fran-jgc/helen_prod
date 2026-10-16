PROMPT CREATE OR REPLACE PROCEDURE spp_usach_cb_otras_univ
CREATE OR REPLACE PROCEDURE spp_usach_cb_otras_univ(ve_cod_ejecucion_proceso	NUMBER)
AS
BEGIN
  DECLARE
    vl_current_date               DATE:= Trunc(SYSDATE);
		vl_i						              NUMBER;
    vl_count_rut                  NUMBER;
    vl_mensaje					          VARCHAR2(100);
    vl_cod_deudor                 NUMBER;
    vl_count_ano_cobro            NUMBER;
    vl_count_nro_cuota            NUMBER;
    vl_cuota_anual                NUMBER;
    vl_count_error_proceso        NUMBER;
    vl_nom_comprobante			      VARCHAR2(100);
		KL_FAMILIA_SOLIDARIO		      NUMBER:= 1;
		/*
		 * los comprobantes se creaban con estado aprobado el cual [fabiola.arriagada@usach.cl] enviado el 21/10/2014 
		 * en un correo dijo que siempre se deben crear con estado pendiente
		 * jv 22/10/2014
		 * */		
		K_COMP_PENDIENTE		      NUMBER:= 1;
		--K_COMP_CONTABILIZADO		      NUMBER:= 2;

		K_COMP_INGRESO				        NUMBER:= 1;
		K_COMP_CARGA_BCO_OTRAS_UNIV	  NUMBER:= 31;
		K_ORIGEN_PAGO_OTRAS_UNIV			NUMBER:= 5;
		K_ORIGEN_CARGA_OTRAS_UNIV     NUMBER:= 4;
    K_ORIGEN_OTRA_UNIV_FICTICIO   NUMBER:= 23;
    K_ESTADO_PAGO_VIGENTE         NUMBER:= 1;
    K_ESTADO_DSCTO_VIGENTE        NUMBER:= 1;
    K_CTA_DSCTO					          NUMBER:= 9;
    vl_cod_comp_solidario		      NUMBER;
		vl_cod_deuda				          NUMBER;
		vl_cod_plan_pago			        NUMBER;
		vl_cod_cuenta_bco			        NUMBER;
		vl_cod_abono				          NUMBER;
    vl_cod_usuario				        NUMBER;
    vl_count_registros			      NUMBER;
		vl_cod_moneda                 NUMBER;
    vl_valor_moneda			          NUMBER;
		vl_monto_capital_pesos	      NUMBER;
		vl_monto_interes_pesos	      NUMBER;
		vl_factor_K				            NUMBER;
		vl_factor_IN				          NUMBER;
    vl_monto_capital			        NUMBER;
		vl_monto_interes			        NUMBER;
    vl_monto_multa			          NUMBER;
    vl_cod_componente_deuda       NUMBER;
    vl_monto_pesos                NUMBER;
    vl_monto_capital_otras_ues	  NUMBER;
    vl_monto_multa_otras_ues      NUMBER;
    vl_monto_moneda_otras_ues     NUMBER;
    vl_monto_pesos_otras_ues      NUMBER;
    vl_monto_descto             NUMBER;
    vl_cod_tipo_deuda             VARCHAR2(10);
    vl_cod_descuento              NUMBER;
    vl_cod_cuenta_dscto           NUMBER;
    vl_monto_descto_otras_ues     NUMBER;
    vl_monto_moneda_usach         NUMBER;
    vl_count_deuda                NUMBER;
    vl_cod_deuda_cruz             NUMBER;
    vl_dif_pesos                  NUMBER;
    KL_DIFERENCIA_MAX             NUMBER:= 40; --diferencia que puede existir entre los montos capital y utm
    vl_monto_interes_otras_ues    NUMBER;
    vl_monto_dscto_capital        NUMBER;
    vl_monto_dscto_interes        NUMBER;
    vl_monto_dscto_multa          NUMBER;
    vl_monto_dscto_capital_fict	  NUMBER;
    vl_monto_dscto_interes_fict	  NUMBER;
    vl_monto_dscto_multa_fict	    NUMBER;
    vl_fecha_deposito				DATE;


		--//CURSOR A = validaciones
		CURSOR c_usach_cb_otras_univ_valida IS
    SELECT  COD_USACH_CB_OTRAS_UNIV
            ,RUT
            ,ANO_COBRO
		        ,NRO_CUOTA
		        ,MONTO_CUOTA
            ,FECHA_PAGO
            ,MONTO_K_UTM_USACH
            ,MONTO_I_UTM_USACH
            ,MONTO_K_PESOS_USACH
            ,MONTO_I_PESOS_USACH
            ,DESCTO_UNIV_PESOS_USACH
		FROM    USACH_CB_OTRAS_UNIV
    WHERE   PROCESADO = 'N'
    AND     CARGADO = 'N';


    --//CURSOR A = validaciones
		CURSOR c_usach_cb_otras_univ IS
    SELECT   COD_USACH_CB_OTRAS_UNIV
            ,RUT
            ,ANO_COBRO
            ,NRO_CUOTA
            ,MONTO_CUOTA
            ,FECHA_PAGO
            ,MONTO_K_UTM_USACH
            ,MONTO_K_PESOS_USACH
            ,MONTO_I_UTM_USACH
            ,MONTO_I_PESOS_USACH
            ,DESCTO_UNIV_UTM_USACH
            ,DESCTO_UNIV_PESOS_USACH
            ,MONTO_K_UTM_OTRAS_UES
            ,MONTO_K_PESOS_OTRAS_UES
            ,MONTO_I_UTM_OTRAS_UES
            ,MONTO_I_PESOS_OTRAS_UES
            ,DESCTO_UTM_OTRAS_UES
            ,DESCTO_PESOS_OTRAS_UES
            ,FECHA_DEPOSITO
    FROM    USACH_CB_OTRAS_UNIV
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
		  INTO	  vl_count_registros
		  FROM	  usach_cb_otras_univ
      WHERE   procesado = 'N'
      AND     cargado = 'N';

	    spu_ejecucion_proceso ('SET_TOTAL', ve_cod_ejecucion_proceso, vl_count_registros);
	    vl_i := 1;

      FOR A IN c_usach_cb_otras_univ_valida LOOP

        SELECT Abs(Round(a.MONTO_K_UTM_USACH * f_mon_get_valor_moneda(3, a.FECHA_PAGO),0) - a.MONTO_K_PESOS_USACH)
        INTO vl_dif_pesos
        FROM usach_cb_otras_univ
        WHERE cod_usach_cb_otras_univ = a.cod_usach_cb_otras_univ;

        IF (vl_dif_pesos > KL_DIFERENCIA_MAX)THEN
            vl_mensaje := 'Los montos capital en UTM y PESOS no coinciden para el rut: ' || To_char(a.rut);
				    spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
        ELSE
          SELECT  Count(*)
          INTO    vl_count_rut
          FROM    deudor
          WHERE   rut = a.rut;

          IF(vl_count_rut = 0)THEN
            vl_mensaje := 'El rut: ' || To_char(a.rut) || ' No EXISTE';
				    spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
          ELSE
            SELECT  Count(*)
            INTO    vl_count_deuda
            FROM    deudor dr, deuda da
            WHERE   rut  = a.rut
            AND     dr.cod_deudor = da.cod_deudor;
            IF(vl_count_deuda = 0)THEN
              vl_mensaje := 'El rut: ' || To_char(a.rut) || ' No Posee deudas';
				      spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);

            ELSE
              SELECT  Count(*)
              INTO    vl_cod_deuda_cruz
              FROM    deudor dr, deuda da
              WHERE   rut  = a.rut
              AND     dr.cod_deudor = da.cod_deudor
              AND     da.cod_deuda IN(SELECT cod_deuda FROM cruzado);

              IF(vl_cod_deuda_cruz = 0)THEN
                vl_mensaje := 'El rut: ' || To_char(a.rut) || ' No Posee deuda cruzada con otra entidad recaudadora.';
				        spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
              ELSE
                SELECT  cod_deuda
                INTO    vl_cod_deuda
                FROM    deudor dr, deuda da
                WHERE   rut  = a.rut
                AND     dr.cod_deudor = da.cod_deudor
                AND     da.cod_deuda IN(SELECT cod_deuda FROM cruzado);

                SELECT  Count(*)
                INTO    vl_count_ano_cobro
                FROM    plan_pago
                WHERE   cod_deuda = vl_cod_deuda
                AND     ano_cobro = a.ano_cobro;

                IF(vl_count_ano_cobro = 0)THEN
                  vl_mensaje := 'El año cobro : ' || To_char(a.ano_cobro) || ' No EXISTE, para el deudor rut: '|| To_char(a.rut);
				          spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
                ELSE
                  SELECT  cod_plan_pago
                  INTO    vl_cod_plan_pago
                  FROM    plan_pago
                  WHERE   cod_deuda = vl_cod_deuda
                  AND     ano_cobro = a.ano_cobro
                  AND     cod_deuda IN(SELECT cod_deuda FROM cruzado);

                  SELECT  Count(*)
                  INTO    vl_count_nro_cuota
                  FROM    plan_pago
                  WHERE   cod_plan_pago = vl_cod_plan_pago
                  AND     nro_cuota = a.nro_cuota
                  AND     ano_cobro = a.ano_cobro;

                  IF(vl_count_nro_cuota = 0)THEN
                    vl_mensaje := 'plan pago: '|| To_char(vl_cod_plan_pago)|| ' no CORRESPONDE Nro de cuota: ' || To_char(a.nro_cuota) || ' del año cobro:  '||To_Char(a.ano_cobro)||', Rut deudor: '||To_char(a.rut);
				            spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
                  ELSE
                    SELECT  monto_capital + monto_interes
                    INTO    vl_cuota_anual
                    FROM    plan_pago
                    WHERE   cod_plan_pago = vl_cod_plan_pago;

                    IF(vl_cuota_anual <> a.monto_cuota)THEN
                      vl_mensaje := 'La cuota anual: ' || To_char(a.monto_cuota) || ' No CORRESPONDE, plan pago: '|| To_char(vl_cod_plan_pago)|| ' Rut deudor: '||To_char(a.rut);
				              spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
                    ELSE
                      IF (a.descto_univ_pesos_usach > 0 AND a.nro_cuota <> 99) THEN
                        vl_mensaje := 'La cuota del año: ' || To_char(a.ano_cobro) || ' asociado al Rut: '||To_char(a.rut)||' no debiese tener descuento';
				                spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
                      END IF;
                    END IF;

                  END IF;


                END IF;
              END IF;
            END IF;
          END IF;
        END IF;
        spu_ejecucion_proceso ('SET_ACTUAL', ve_cod_ejecucion_proceso, vl_i);
		    vl_i := vl_i + 1;
      END LOOP;

      SELECT  Count(*)
      INTO    vl_count_error_proceso
      FROM    error_proceso
      WHERE   cod_ejecucion_proceso = ve_cod_ejecucion_proceso;

      IF(vl_count_error_proceso > 0)THEN

        -- se borra el contenido de la tabla,
        --ya que el usuario debe revisar el archivo y nuevamente debe ser cargado
        DELETE  usach_cb_otras_univ
        WHERE   procesado = 'N'
        AND     cargado = 'N';

        vl_i := vl_i - 1;
	      spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, vl_i);

        RETURN;
      ELSE

        spu_ejecucion_proceso ('SET_ACTUAL', ve_cod_ejecucion_proceso, 0);
	      vl_i := 1;

	        SELECT	FECHA_DEPOSITO
			into	vl_fecha_deposito
			FROM    usach_cb_otras_univ
			WHERE   PROCESADO = 'N'
			AND     CARGADO = 'N'
			AND ROWNUM = 1;

		/*
		 * los comprobantes se creaban con estado aprobado el cual [fabiola.arriagada@usach.cl] enviado el 21/10/2014 
		 * en un correo dijo que siempre se deben crear con estado pendiente
		 * jv 22/10/2014
		 * */
        --crea comprobante para fondo solidario
	      vl_nom_comprobante := 'Comprobante Carga de Ingreso, OTRAS UNIVERSIDADES, ' || TO_CHAR(vl_fecha_deposito,'dd/mm/yyyy');
	      --spu_comprobante ('INSERT', NULL, vl_fecha_deposito, vl_cod_usuario, vl_nom_comprobante, KL_FAMILIA_SOLIDARIO, K_COMP_CONTABILIZADO, K_COMP_INGRESO, K_COMP_CARGA_BCO_OTRAS_UNIV, vl_cod_usuario, vl_fecha_deposito);
	      spu_comprobante ('INSERT', NULL, vl_fecha_deposito, vl_cod_usuario, vl_nom_comprobante, KL_FAMILIA_SOLIDARIO, K_COMP_PENDIENTE, K_COMP_INGRESO, K_COMP_CARGA_BCO_OTRAS_UNIV, vl_cod_usuario, vl_fecha_deposito);

        SELECT  comprobante_sq.CURRVAL
		    INTO    vl_cod_comp_solidario
		    FROM    dual;

        FOR B IN c_usach_cb_otras_univ LOOP
          -- cuando entra al loop actualiza la tabla, y se marca procesado con 'S' para efecto de historial
 	        UPDATE  usach_cb_otras_univ
          SET     procesado = 'S'
          WHERE   cod_usach_cb_otras_univ = b.cod_usach_cb_otras_univ;

          -- verifica si el proceso ha sido cancelado
			    IF (f_ep_cancela_proceso (ve_cod_ejecucion_proceso) = 'S') THEN
				    RETURN;
			    END IF;

          SELECT  cod_deudor
          INTO    vl_cod_deudor
          FROM    deudor
          WHERE   rut  = b.rut;

          SELECT  da.cod_deuda
                  ,cod_plan_pago
                  ,cod_moneda
                  ,da.cod_tipo_deuda
          INTO    vl_cod_deuda
                  ,vl_cod_plan_pago
                  ,vl_cod_moneda
                  ,vl_cod_tipo_deuda
          FROM    deuda da, plan_pago p, tipo_deuda td
          WHERE   cod_deudor = vl_cod_deudor
          AND     da.cod_deuda = p.cod_deuda
          AND     ano_cobro = b.ano_cobro
          AND     da.cod_tipo_deuda = td.cod_tipo_deuda
          AND     da.cod_deuda IN (SELECT cod_deuda FROM cruzado);

          IF(b.monto_k_pesos_usach + b.monto_i_pesos_usach > 0)THEN
            vl_valor_moneda		    := f_mon_get_valor_moneda(vl_cod_moneda, b.fecha_pago);
            vl_monto_capital_pesos  := round(f_pp_saldo_capital_caja(vl_cod_plan_pago, b.fecha_pago) * vl_valor_moneda, 0);
		    vl_monto_interes_pesos  := round(f_pp_saldo_interes_normal_caja(vl_cod_plan_pago, b.fecha_pago) * vl_valor_moneda, 0);

            IF ((vl_monto_capital_pesos + vl_monto_interes_pesos) <> 0) THEN
              -- porporcion K, IN
					    vl_factor_K  := vl_monto_capital_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos);
					    vl_factor_IN := vl_monto_interes_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos);

					    vl_monto_capital	:= Round((b.monto_k_pesos_usach / vl_valor_moneda) * vl_factor_K, 4);
					    vl_monto_interes	:= Round((b.monto_k_pesos_usach / vl_valor_moneda) * vl_factor_IN, 4);

              IF (vl_monto_interes < 0)THEN
                vl_monto_capital := vl_monto_capital + (vl_monto_interes * -1);
                vl_monto_interes := 0;
              END IF;

              -- si no cuadra con el total_moneda que se esta pagando, se agrega la diferencia al abono K
				      IF ((vl_monto_capital + vl_monto_interes) <> round(b.monto_k_pesos_usach / vl_valor_moneda,4)) THEN
				        vl_monto_capital := vl_monto_capital + (round(b.monto_k_pesos_usach / vl_valor_moneda,4) - (vl_monto_capital + vl_monto_interes));
				      END IF;

              -- obtengo el monto_multa
              vl_monto_multa:= Round(b.monto_i_pesos_usach / vl_valor_moneda, 4);
              vl_monto_pesos := b.monto_k_pesos_usach + b.monto_i_pesos_usach;

              -----------------------------------------
              --- INICIO ABONO CORRESPONDIENTE USACH --
              -----------------------------------------
              -- No se llama al spu_abono, ya que dentro del proceso puede ser que se creen excedentes
              -- y eso se quiere evitar, se hara para spu_abono y para spu_descuento, insert directo

              -- calcula el componente del pago
		          vl_cod_componente_deuda := f_pp_componente_pago(vl_cod_plan_pago, b.fecha_pago);
              vl_monto_moneda_usach := vl_monto_capital + vl_monto_interes + vl_monto_multa;

              --(3) si el pago es mayor que el saldo considerando cruzado debe crear pago a la cuota futura o exeedente

					  spu_abono_carga(b.fecha_pago		  -- fecha_abono
 									  ,vl_cod_usuario		  -- cod_usuario
									  ,vl_monto_capital   -- monto_capital
					                  ,vl_monto_interes   -- monto_interes
					                  ,vl_monto_multa     -- monto_multa
					                  ,vl_monto_pesos
									  ,0						      -- diferencia_pesos
									  ,vl_cod_plan_pago		-- cod_plan_pago
									  ,vl_cod_deuda			  -- cod_deuda
									  ,null					      -- cod_abono_desde
									  ,K_ORIGEN_PAGO_OTRAS_UNIV	-- cod_origen_pago
									  ,null					      -- cod_sub_boleta
									  ,null					      -- cod_documento_pago
									  ,vl_cod_comp_solidario -- cod_comprobante
									  ,vl_cod_abono);

              UPDATE abono
              SET fecha_deposito = b.fecha_deposito
              WHERE cod_abono = vl_cod_abono;

              -- Se Obtiene la cuenta contable del banco
					    /*SELECT	cc.cod_cuenta_contable
					    INTO	  vl_cod_cuenta_bco
					    FROM	  origen_carga_banco ocb, cuenta_corriente cc
					    WHERE	  ocb.cod_origen_carga_banco = K_ORIGEN_CARGA_OTRAS_UNIV
					    AND		  cc.cod_cuenta_corriente = ocb.cod_cuenta_corriente;*/
			vl_cod_cuenta_bco := 11102; --BANCO DE CHILE
              
              spu_item_comprobante ('INSERT', NULL, vl_cod_comp_solidario, vl_cod_cuenta_bco,  b.monto_k_pesos_usach + b.monto_i_pesos_usach, 0);
					    spu_analitico ('INSERT', NULL, vl_cod_comp_solidario, vl_cod_deuda, vl_cod_cuenta_bco, b.monto_k_pesos_usach + b.monto_i_pesos_usach, 0);

					    sp_comp_ing (vl_cod_comp_solidario, vl_cod_abono);  --(4)

					    ---------------------------------------
              --  FIN ABONO CORRESPONDIENTE USACH  --
              ---------------------------------------

              -----------------------------------------------
              -- INICIO ABONO CORRESPONDIENTE A OTRAS UNIV --
              -----------------------------------------------

              IF(b.monto_k_pesos_otras_ues + b.monto_i_pesos_otras_ues > 0)THEN

                vl_monto_capital_otras_ues := Round((b.monto_k_pesos_otras_ues / vl_valor_moneda) * vl_factor_K, 4);
                vl_monto_interes_otras_ues := Round((b.monto_k_pesos_otras_ues / vl_valor_moneda) * vl_factor_IN, 4);

                IF (vl_monto_interes_otras_ues < 0)THEN
                  vl_monto_capital_otras_ues := vl_monto_capital_otras_ues + (vl_monto_interes_otras_ues * -1);
                  vl_monto_interes_otras_ues := 0;
                END IF;

                -- si no cuadra con el total_moneda que se esta pagando, se agrega la diferencia al abono K
				        IF ((vl_monto_capital_otras_ues + vl_monto_interes_otras_ues) <> round(b.monto_k_pesos_otras_ues / vl_valor_moneda,4)) THEN
				          vl_monto_capital_otras_ues := vl_monto_capital_otras_ues + (round(b.monto_k_pesos_otras_ues / vl_valor_moneda,4) - (vl_monto_capital_otras_ues + vl_monto_interes_otras_ues));
				        END IF;

                vl_monto_multa_otras_ues   := Round(b.monto_i_pesos_otras_ues / vl_valor_moneda, 4);
                vl_monto_moneda_otras_ues  := vl_monto_capital_otras_ues + vl_monto_interes_otras_ues + vl_monto_multa_otras_ues;
                vl_monto_pesos_otras_ues   := b.monto_k_pesos_otras_ues + b.monto_i_pesos_otras_ues;

                INSERT INTO ABONO_FICTICIO
                VALUES(abono_ficticio_sq.NEXTVAL    -- cod_abono_ficticio
                     , SYSDATE                      -- fecha_registro
                     , b.fecha_pago                 -- fecha_abono
                     , vl_cod_usuario               -- cod_usuario
                     , vl_monto_capital_otras_ues   -- monto_capital
                     , vl_monto_interes_otras_ues   -- monto_interes
                     , vl_monto_multa_otras_ues     -- monto_multa
                     , vl_monto_moneda_otras_ues    -- monto_moneda
                     , vl_monto_pesos_otras_ues     -- monto_pesos
                     , vl_cod_deuda                 -- cod_deuda
                     , vl_cod_plan_pago             -- cod_plan_pago
                     , vl_cod_abono                 -- cod_abono_desde
                     , b.fecha_deposito             -- fecha_deposito
                     , NULL                         -- folio_carga
                     );

              END IF;
              ---------------------------------------------
              --  FIN ABONO CORRESPONDIENTE OTRAS UNIV.  --
              ---------------------------------------------

              ----------------------------
              -- INICIO DESCUENTO USACH --
              ----------------------------
              -- si el monto descto_univ_pesos_usach > 0, crea el descuento
              IF( b.descto_univ_pesos_usach > 0)THEN
                vl_monto_descto := Round(b.descto_univ_pesos_usach / vl_valor_moneda, 4);

                vl_monto_dscto_capital	:= Round(vl_monto_descto * vl_factor_K, 4);
                vl_monto_dscto_interes	:= Round(vl_monto_descto * vl_factor_IN, 4);
                vl_monto_dscto_multa	  := Round((vl_monto_descto - vl_monto_dscto_capital - vl_monto_dscto_interes), 4);

                IF (vl_monto_dscto_interes < 0)THEN
                  vl_monto_dscto_capital := vl_monto_dscto_capital + (vl_monto_dscto_interes * -1);
                  vl_monto_dscto_interes := 0;
                END IF;

                INSERT INTO descuento
                VALUES(descuento_sq.NEXTVAL     -- cod_descuento
                      ,vl_current_date          -- fecha_registro
                      ,b.fecha_pago             -- fecha_descuento
                      ,vl_cod_usuario           -- cod_usuario
                      ,vl_cod_componente_deuda  -- cod_componente_deuda
                      ,vl_monto_dscto_capital   -- monto_capital
                      ,vl_monto_dscto_interes   -- monto_interes
                      ,vl_monto_dscto_multa     -- monto_multa
                      ,vl_monto_descto        -- monto_moneda
                      ,b.descto_univ_pesos_usach -- monto_pesos
                      ,vl_cod_plan_pago         -- cod_plan_pago
                      ,vl_cod_deuda             -- cod_deuda
                      ,NULL                     -- cod_comprobante
                      ,K_ESTADO_PAGO_VIGENTE    -- cod_estado_pago
                      ,K_ORIGEN_PAGO_OTRAS_UNIV -- cod_origen_pago
                      ,null
                      ,null
                      );

                SELECT descuento_sq.CURRVAL
		            INTO vl_cod_descuento
		            FROM dual;

                -- contabiliza el descuento
				        vl_cod_cuenta_dscto := f_comp_get_cuenta_tipo_deuda(vl_cod_tipo_deuda, K_CTA_DSCTO);
				        spu_item_comprobante('INSERT', NULL, vl_cod_comp_solidario, vl_cod_cuenta_dscto, b.descto_univ_pesos_usach, 0);
				        spu_analitico	('INSERT', NULL, vl_cod_comp_solidario, vl_cod_deuda, vl_cod_cuenta_dscto, b.descto_univ_pesos_usach, 0);

				        sp_comp_ing	(vl_cod_comp_solidario, NULL, vl_cod_descuento);

              END IF;
              -------------------------
              -- FIN DESCUENTO USACH --
              -------------------------

              ---------------------------------
              -- INICIO DESCUENTO OTRAS UNIV --
              ---------------------------------
              IF(b.descto_pesos_otras_ues > 0)THEN
                vl_monto_descto_otras_ues := Round(b.descto_pesos_otras_ues / vl_valor_moneda, 4);

                vl_monto_dscto_capital_fict	:= Round(vl_monto_descto_otras_ues * vl_factor_K, 4);
                vl_monto_dscto_interes_fict	:= Round(vl_monto_descto_otras_ues * vl_factor_IN, 4);
                vl_monto_dscto_multa_fict	  := Round((vl_monto_descto_otras_ues - vl_monto_dscto_capital_fict - vl_monto_dscto_interes_fict), 4);

                IF (vl_monto_dscto_interes_fict < 0)THEN
                  vl_monto_dscto_capital_fict := vl_monto_dscto_capital_fict + (vl_monto_dscto_interes_fict * -1);
                  vl_monto_dscto_interes_fict := 0;
                END IF;

                INSERT INTO descuento_ficticio
                VALUES(descuento_ficticio_sq.NEXTVAL  -- cod_descuento_ficticio
                      ,vl_current_date                -- fecha_registro
                      ,b.fecha_pago                   -- fecha_descuento
                      ,vl_cod_usuario                 -- cod_usuario
                      ,vl_monto_dscto_capital_fict    -- monto_capital
                      ,vl_monto_dscto_interes_fict    -- monto_interes
                      ,vl_monto_dscto_multa_fict      -- monto_multa
                      ,vl_monto_descto_otras_ues      -- monto_moneda
                      ,b.descto_pesos_otras_ues       -- monto_pesos
                      ,vl_cod_deuda                   -- cod_deuda
                      ,vl_cod_plan_pago               -- cod_plan_pago
                      ,vl_cod_descuento               -- cod_descuento
                      );

              END IF;
              ------------------------------
              -- FIN DESCUENTO OTRAS UNIV --
              ------------------------------
              sp_pp_actualiza_estado(vl_cod_plan_pago);
	            sp_deuda_actualiza_estado(vl_cod_deuda);

              UPDATE  usach_cb_otras_univ
              SET     cargado = 'S'
              WHERE   cod_usach_cb_otras_univ = b.cod_usach_cb_otras_univ;

            END IF;
          END IF;
          -- actualiza avance
			    spu_ejecucion_proceso ('SET_ACTUAL', ve_cod_ejecucion_proceso, vl_i);
			    vl_i := vl_i + 1;
        END LOOP;
      END IF;

      sp_comp_simplifica (vl_cod_comp_solidario);
		  spu_comprobante ('APROBAR', vl_cod_comp_solidario, vl_fecha_deposito);

      vl_i := vl_i - 1;
	    spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, vl_i);

      EXCEPTION
        WHEN OTHERS THEN
	      -- Si ocurre un error de la BD al grabar un registro lo registra en el LOG. del sistema
			  spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', SQLCODE, SUBSTR(SQLERRM,1,100), 'spp_usach_cb_otras_univ: plan_pago: ' || To_Char(vl_cod_plan_pago));
			  spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, vl_i);
  END;
END spp_usach_cb_otras_univ;
/

