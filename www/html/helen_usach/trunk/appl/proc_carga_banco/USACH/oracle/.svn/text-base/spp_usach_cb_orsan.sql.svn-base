PROMPT CREATE OR REPLACE PROCEDURE spp_usach_cb_orsan
CREATE OR REPLACE PROCEDURE spp_usach_cb_orsan(ve_cod_ejecucion_proceso NUMBER, ve_cod_origen_carga_banco NUMBER)
AS
BEGIN
  DECLARE
    vl_current_date               DATE:= Trunc(SYSDATE);
  vl_i              NUMBER;
    vl_count_rut                  NUMBER;
    vl_mensaje            VARCHAR2(100);
    vl_exist_deuda                NUMBER;
    vl_exist_tipo_deuda           NUMBER;
    vl_exist_plan_pago            NUMBER;
    vl_exist_pp_ano_cobro         NUMBER;
    vl_exist_pp_nro_cuota         NUMBER;
    vl_cod_usuario          NUMBER;
    vl_count_registros        NUMBER;
  vl_cod_plan_pago        NUMBER;
  vl_count_error_proceso        NUMBER;
    vl_exist_deuda_cruzada        NUMBER;
    -- CONSTANTES OCUPADAS PARA SABER QUE ORIGEN_CARGA_BANCO ESTA PROCESANDO
    K_CB_ORSAN_CD                 NUMBER:= 7;
    K_CB_ORSAN_CDE                NUMBER:= 8;
    K_CB_ORSAN_CRUZ_19287         NUMBER:= 9;
    K_CB_ORSAN_CRUZ_19848         NUMBER:= 10;
    K_CB_ORSAN_DEU_PROP           NUMBER:= 11;
    K_CB_ORSAN_MOROSOS            NUMBER:= 12;
    K_CB_ORSAN_REZAGA             NUMBER:= 13;
    K_CB_ORSAN_TGR                NUMBER:= 14;
    vl_nom_origen_cb              VARCHAR(40);
    --CONSTANTES OCUPADAS EN INSERT DE COMPROBANTE
    K_OC_ORSAN_CD                 NUMBER:= 36;
    K_OC_ORSAN_CDE                NUMBER:= 37;
    K_OC_ORSAN_CRUZ_19287         NUMBER:= 38;
    K_OC_ORSAN_CRUZ_19848         NUMBER:= 39;
    K_OC_ORSAN_DEU_PROP           NUMBER:= 40;
    K_OC_ORSAN_MOROSO             NUMBER:= 41;
    K_OC_ORSAN_REZAGADO           NUMBER:= 42;
    K_OC_ORSAN_TGR                NUMBER:= 43;
    -- CONSTANTES OCUPADAS EN INSERT DE ABONOS
    K_OR_PAGO_ORSAN_CD            NUMBER:= 10;
    K_OR_PAGO_ORSAN_CDE           NUMBER:= 11;
    K_OR_PAGO_ORSAN_CRUZ_19287    NUMBER:= 12;
    K_OR_PAGO_ORSAN_CRUZ_19848    NUMBER:= 13;
    K_OR_PAGO_ORSAN_DEU_PROP      NUMBER:= 14;
    K_OR_PAGO_ORSAN_MOROSO        NUMBER:= 15;
    K_OR_PAGO_ORSAN_REZAGA        NUMBER:= 16;
    K_OR_PAGO_ORSAN_TGR           NUMBER:= 17;
    K_EST_EXCEDENTE_SOLICITADO    NUMBER:= 1;

    ------------------------------------------
    vl_origen_pago                NUMBER;
    vl_origen_comprobante         NUMBER;
    vl_nom_comprobante        VARCHAR2(100);
    vl_cod_moneda                 NUMBER;
    vl_valor_moneda           NUMBER;
  vl_cod_comp_solidario       NUMBER;
    vl_monto_capital_pesos        NUMBER;
  vl_monto_interes_pesos        NUMBER;
    vl_monto_multa_pesos          NUMBER;
  vl_factor_K             NUMBER;
  vl_factor_IN            NUMBER;
    vl_factor_MULTA               NUMBER;
    vl_monto_capital        NUMBER;
  vl_monto_interes          NUMBER;
    vl_cod_componente_deuda       NUMBER;
    vl_monto_multa_moneda         NUMBER;
    vl_monto_pesos                NUMBER;
    vl_monto_moneda               NUMBER;
    vl_monto_multa            NUMBER;
    vl_cod_abono          NUMBER;
    vl_cod_cuenta_bco       NUMBER;
    vl_monto_descto_moneda        NUMBER;
  vl_cod_descuento            NUMBER;
    vl_cod_cuenta_dscto           NUMBER;
    vl_porc_otras_ues             NUMBER;
    vl_calc_total_abono           NUMBER;
    vl_monto_k_otras_ues          NUMBER;
    vl_monto_k_otras_ues_utm      NUMBER;
    vl_calc_total_descto          NUMBER;
    vl_monto_dscto_otras_ues      NUMBER;
    vl_monto_dscto_otras_ues_utm  NUMBER;
    vl_monto_capital_fict         NUMBER;
    vl_monto_interes_fict         NUMBER;
    vl_monto_multa_fict           NUMBER;
    vl_monto_dscto_capital_fict   NUMBER;
    vl_monto_dscto_interes_fict   NUMBER;
    vl_monto_dscto_multa_fict     NUMBER;
    vl_monto_dscto_capital        NUMBER;
    vl_monto_dscto_interes        NUMBER;
    vl_monto_dscto_multa          NUMBER;
    vl_count_fecha_dep          NUMBER;
    vl_cant_comp                  NUMBER;
    vl_cod_deudor_excedente       NUMBER;

    KL_FAMILIA_SOLIDARIO      NUMBER:= 1;
    /*
	 * los comprobantes se creaban con estado aprobado el cual [fabiola.arriagada@usach.cl] enviado el 21/10/2014 
	 * en un correo dijo que siempre se deben crear con estado pendiente
	 * jv 22/10/2014
	 * */
    K_COMP_PENDIENTE      NUMBER:= 1;
    --K_COMP_CONTABILIZADO      NUMBER:= 2;
    
    K_COMP_INGRESO          NUMBER:= 1;
    K_ESTADO_PAGO_VIGENTE         NUMBER:= 1;
    K_CTA_DSCTO           NUMBER:= 9;
    vl_fecha_deposito       DATE;

    --//CURSOR A = validaciones
    CURSOR c_usach_cb_orsan_valida IS
    SELECT   RUT
            ,DIG_VERIF
            ,ANO_CUOTA
        ,NRO_CUOTA
        ,COD_DEUDA
        ,PORC_USACH
            ,COD_TIPO_DEUDA
    FROM    USACH_CB_ORSAN
    WHERE   PROCESADO = 'N'
    AND     CARGADO = 'N'
    AND     COD_ORIGEN_CARGA_BANCO = 14;

    --//CURSOR B
    CURSOR c_usach_cb_orsan IS
    SELECT  COD_USACH_CB_ORSAN
            ,RUT
            ,ANO_CUOTA
            ,NRO_COMPROBANTE -- FOLIO_CARGA = ABONO
            ,NRO_CUOTA
            ,FECHA_PAGO
            ,PORC_USACH
            ,MONTO_CAPITAL_PESOS -- MONTO_K + MONTO_IN
            ,MONTO_INTERES_PESOS -- INTERES_PENAL
            ,MONTO_TOTAL_PESOS
            ,MONTO_DESCTO_PESOS
            ,COD_DEUDA
            ,COD_TIPO_DEUDA
            ,FECHA_VENCTO
            ,FECHA_DEPOSITO
    FROM    USACH_CB_ORSAN
    WHERE   PROCESADO = 'N'
    AND     CARGADO = 'N'
    AND     COD_ORIGEN_CARGA_BANCO = ve_cod_origen_carga_banco;


    BEGIN
    SELECT   Count(*)
    INTO vl_count_rut
    FROM    USACH_CB_ORSAN
    WHERE   PROCESADO = 'N'
    AND     CARGADO = 'N'
    AND     COD_ORIGEN_CARGA_BANCO = ve_cod_origen_carga_banco;


      -- usuario que ejecuta el proceso
      SELECT  cod_usuario
      INTO    vl_cod_usuario
      FROM    ejecucion_proceso
      WHERE   cod_ejecucion_proceso = ve_cod_ejecucion_proceso;

      -- calcula el total de registros
    SELECT  Count(*)
    INTO    vl_count_registros
    FROM    usach_cb_orsan
    WHERE   procesado = 'N'
    AND     cargado = 'N'
    AND     cod_origen_carga_banco = ve_cod_origen_carga_banco;

      spu_ejecucion_proceso ('SET_TOTAL', ve_cod_ejecucion_proceso, vl_count_registros);
      vl_i := 0;

      FOR A IN c_usach_cb_orsan_valida LOOP

        SELECT  Count(*)
        INTO    vl_count_rut
        FROM    deudor
        WHERE   rut = a.rut;

        IF(vl_count_rut = 0)THEN
          vl_mensaje := 'El Rut: ' || To_char(a.rut)||'-'||a.dig_verif|| ' No EXISTE';
          spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);

          UPDATE USACH_CB_ORSAN SET CARGADO = 'S'
          WHERE RUT = a.rut
          AND COD_ORIGEN_CARGA_BANCO = ve_cod_origen_carga_banco;
        ELSE
          -- valida que el deudor tenga deuda
          SELECT  Count(*)
          INTO    vl_exist_deuda
          FROM    deudor dr, deuda da
          WHERE   rut  = a.rut
          AND     dr.cod_deudor = da.cod_deudor
          AND     cod_deuda = a.cod_deuda;

          IF(vl_exist_deuda = 0)THEN
            vl_mensaje := 'La deuda : ' || To_char(a.cod_deuda) || ' No EXISTE o No CORRESPONDE, Rut deudor: '|| To_char(a.rut)||'-'||a.dig_verif;
            spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
            UPDATE USACH_CB_ORSAN SET CARGADO = 'S'
            WHERE RUT = a.rut
            AND COD_ORIGEN_CARGA_BANCO = ve_cod_origen_carga_banco;
          ELSE
            -- valida que el deudor tenga deuda cruzada
            SELECT  Count(*)
            INTO    vl_exist_deuda_cruzada
            FROM    deudor dr, deuda da
            WHERE   rut  = a.rut
            AND     dr.cod_deudor = da.cod_deudor
            AND     da.cod_deuda IN(SELECT cod_deuda FROM cruzado)
            AND     cod_deuda = a.cod_deuda;

            IF(vl_exist_deuda_cruzada = 0 AND a.porc_usach <> 100 )THEN
              vl_mensaje := 'La deuda : ' || To_char(a.cod_deuda) || ' No esta Cruzada con otra entidad recaudadora, Rut deudor: '|| To_char(a.rut)||'-'||a.dig_verif;
              spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
              UPDATE USACH_CB_ORSAN SET CARGADO = 'S'
              WHERE RUT = a.rut
              AND COD_ORIGEN_CARGA_BANCO = ve_cod_origen_carga_banco;
            ELSE
              SELECT  Count(*)
              INTO    vl_exist_tipo_deuda
              FROM    deuda
              WHERE   cod_tipo_deuda = a.cod_tipo_deuda
              AND     cod_deuda = a.cod_deuda;

              IF(vl_exist_tipo_deuda = 0)THEN
                vl_mensaje := 'La deuda : ' || To_char(a.cod_deuda) || ' No posee tipo deuda: '|| a.cod_tipo_deuda||', Rut deudor: '||To_char(a.rut)||'-'||a.dig_verif;
                spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
                UPDATE USACH_CB_ORSAN SET CARGADO = 'S'
                WHERE RUT = a.rut
                AND COD_ORIGEN_CARGA_BANCO = ve_cod_origen_carga_banco;
              ELSE
                SELECT  Count(*)
                INTO    vl_exist_plan_pago
                FROM    plan_pago
                WHERE   cod_deuda = a.cod_deuda;

                IF(vl_exist_plan_pago = 0)THEN
                  vl_mensaje := 'La deuda : ' || To_char(a.cod_deuda) || ' No posee planes de pago, Rut deudor: '||To_char(a.rut)||'-'||a.dig_verif;
                  spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
                  UPDATE USACH_CB_ORSAN SET CARGADO = 'S'
                  WHERE RUT = a.rut
                  AND COD_ORIGEN_CARGA_BANCO = ve_cod_origen_carga_banco;
                ELSE

                  SELECT  Count(*)
                  INTO    vl_exist_pp_ano_cobro
                  FROM    plan_pago
                  WHERE   cod_deuda = a.cod_deuda
                  AND     ano_cobro = a.ano_cuota;

                  IF(vl_exist_pp_ano_cobro = 0)THEN
                    vl_mensaje := 'Para la deuda: ' || To_char(a.cod_deuda) || ' No EXISTE plan pago con año cobro: '|| To_char(a.ano_cuota)|| ', Rut deudor: '||To_char(a.rut)||'-'||a.dig_verif;
                    spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
                    UPDATE USACH_CB_ORSAN SET CARGADO = 'S'
                    WHERE RUT = a.rut
                    AND COD_ORIGEN_CARGA_BANCO = ve_cod_origen_carga_banco;
                  /*ELSE
                    SELECT  Count(*)
                    INTO    vl_exist_pp_nro_cuota
                    FROM    plan_pago
                    WHERE   cod_deuda = a.cod_deuda
                    AND     ano_cobro = a.ano_cuota
                    AND     nro_cuota = a.nro_cuota;

                    IF(vl_exist_pp_nro_cuota = 0)THEN
                      vl_mensaje := 'Deuda: ' || To_char(a.cod_deuda) || ' No CORRESPONDE plan pago AÑO COBRO: '||To_char(a.ano_cuota)||' con NRO.CUOTA: '|| To_char(a.nro_cuota)|| ', Rut: '||To_char(a.rut)||'-'||a.dig_verif;
                      spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
                    END IF;*/
                  END IF;
                END IF;
              END IF;
            END IF;
          END IF;
        END IF;
        spu_ejecucion_proceso ('SET_ACTUAL', ve_cod_ejecucion_proceso, vl_i);
        vl_i := vl_i + 1;
      END LOOP;

      /*SELECT  Count(*)
      INTO    vl_count_error_proceso
      FROM    error_proceso
      WHERE   cod_ejecucion_proceso = ve_cod_ejecucion_proceso;

      IF(vl_count_error_proceso > 0)THEN
      -- se borra el contenido de la tabla,
      --ya que el usuario debe revisar el archivo y nuevamente debe ser cargado
      DELETE  usach_cb_orsan
      WHERE   procesado = 'N'
      AND     cargado = 'N'
      AND     cod_origen_carga_banco = ve_cod_origen_carga_banco;

      vl_i := vl_i - 1;
      spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, vl_i);
      RETURN;
      ELSE*/
      spu_ejecucion_proceso ('SET_ACTUAL', ve_cod_ejecucion_proceso, 0);
      vl_i := 1;

        IF(ve_cod_origen_carga_banco = K_CB_ORSAN_CD)THEN
          vl_nom_origen_cb := 'ORSAN - CREDITO DIRECTO';
          vl_origen_comprobante:= K_OC_ORSAN_CD;
          vl_origen_pago := K_OR_PAGO_ORSAN_CD;
        ELSIF(ve_cod_origen_carga_banco = K_CB_ORSAN_CDE)THEN
          vl_nom_origen_cb := 'ORSAN - CREDITO DIRECTO ESPECIAL';
          vl_origen_comprobante:= K_OC_ORSAN_CDE;
          vl_origen_pago := K_OR_PAGO_ORSAN_CDE;
        ELSIF(ve_cod_origen_carga_banco = K_CB_ORSAN_CRUZ_19287)THEN
          vl_nom_origen_cb := 'ORSAN - CRUZADO 19287';
          vl_origen_comprobante:= K_OC_ORSAN_CRUZ_19287;
          vl_origen_pago := K_OR_PAGO_ORSAN_CRUZ_19287;
        ELSIF(ve_cod_origen_carga_banco = K_CB_ORSAN_CRUZ_19848)THEN
          vl_nom_origen_cb := 'ORSAN - CRUZADO 19848';
          vl_origen_comprobante:= K_OC_ORSAN_CRUZ_19848;
          vl_origen_pago := K_OR_PAGO_ORSAN_CRUZ_19848;
        ELSIF(ve_cod_origen_carga_banco = K_CB_ORSAN_DEU_PROP)THEN
          vl_nom_origen_cb := 'ORSAN - DEUDORES PROPIOS';
          vl_origen_comprobante:= K_OC_ORSAN_DEU_PROP;
          vl_origen_pago := K_OR_PAGO_ORSAN_DEU_PROP;
        ELSIF(ve_cod_origen_carga_banco = K_CB_ORSAN_MOROSOS)THEN
          vl_nom_origen_cb := 'ORSAN - MOROSOS';
          vl_origen_comprobante:= K_OC_ORSAN_MOROSO;
          vl_origen_pago := K_OR_PAGO_ORSAN_MOROSO;
        ELSIF(ve_cod_origen_carga_banco = K_CB_ORSAN_REZAGA)THEN
          vl_nom_origen_cb := 'ORSAN - REZAGADOS';
          vl_origen_comprobante:= K_OC_ORSAN_REZAGADO;
          vl_origen_pago := K_OR_PAGO_ORSAN_REZAGA;
        ELSIF(ve_cod_origen_carga_banco = K_CB_ORSAN_TGR)THEN
          vl_nom_origen_cb := 'ORSAN - TGR';
          vl_origen_comprobante:= K_OC_ORSAN_TGR;
          vl_origen_pago := K_OR_PAGO_ORSAN_TGR;
        END IF;

        SELECT  COUNT(FECHA_DEPOSITO)
      into  vl_count_fecha_dep
      FROM    usach_cb_orsan
      WHERE   PROCESADO = 'N'
      AND     CARGADO = 'N'
      AND ROWNUM = 1;

      if(vl_count_fecha_dep > 0) THEN

        SELECT  FECHA_DEPOSITO
        into  vl_fecha_deposito
        FROM    usach_cb_orsan
        WHERE   PROCESADO = 'N'
        AND     CARGADO = 'N'
        AND ROWNUM = 1;


		/*
		 * los comprobantes se creaban con estado aprobado el cual [fabiola.arriagada@usach.cl] enviado el 21/10/2014 
		 * en un correo dijo que siempre se deben crear con estado pendiente
		 * jv 22/10/2014
		 * */
          --crea comprobante para fondo solidario
          vl_nom_comprobante := 'Comprobante Carga de Ingreso, '||vl_nom_origen_cb||', '|| TO_CHAR(vl_fecha_deposito,'dd/mm/yyyy');
		  --spu_comprobante ('INSERT', NULL, vl_fecha_deposito, vl_cod_usuario, vl_nom_comprobante, KL_FAMILIA_SOLIDARIO, K_COMP_CONTABILIZADO, K_COMP_INGRESO, vl_origen_comprobante, vl_cod_usuario, vl_fecha_deposito);
          spu_comprobante ('INSERT', NULL, vl_fecha_deposito, vl_cod_usuario, vl_nom_comprobante, KL_FAMILIA_SOLIDARIO, K_COMP_PENDIENTE, K_COMP_INGRESO, vl_origen_comprobante, vl_cod_usuario, vl_fecha_deposito);

          SELECT  comprobante_sq.CURRVAL
          INTO    vl_cod_comp_solidario
          FROM    dual;
      end if;

      FOR B IN c_usach_cb_orsan LOOP
        -- cuando entra al loop actualiza la tabla, y se marca procesado con 'S' para efecto de historial
        UPDATE  usach_cb_orsan
        SET     procesado = 'S'
        WHERE   cod_usach_cb_orsan = b.cod_usach_cb_orsan;
        -- verifica si el proceso ha sido cancelado

        IF (f_ep_cancela_proceso (ve_cod_ejecucion_proceso) = 'S') THEN
          RETURN;
        END IF;

        SELECT cod_plan_pago
              ,cod_moneda
        INTO  vl_cod_plan_pago
              ,vl_cod_moneda
        FROM plan_pago pp, deuda da, tipo_deuda td
        WHERE pp.cod_deuda = da.cod_deuda
        AND   da.cod_tipo_deuda = td.cod_tipo_deuda
        AND   da.cod_deuda = b.cod_deuda
        AND   ano_cobro = b.ano_cuota;
        --AND   nro_cuota = b.nro_cuota;
        --AND   da.cod_deuda IN (SELECT cod_deuda FROM cruzado);

        IF(b.monto_capital_pesos + b.monto_interes_pesos > 0)THEN

          vl_valor_moneda         := f_mon_get_valor_moneda(vl_cod_moneda, b.fecha_pago);
          vl_monto_capital_pesos  := round(f_pp_saldo_capital_caja(vl_cod_plan_pago, b.fecha_pago) * vl_valor_moneda, 0);
          vl_monto_interes_pesos  := round(f_pp_saldo_interes_normal_caja(vl_cod_plan_pago, b.fecha_pago) * vl_valor_moneda, 0);
          vl_monto_multa_pesos    := round(f_pp_saldo_multa_caja(vl_cod_plan_pago, b.fecha_pago) * vl_valor_moneda, 0);
          --dbms_output.put_line('vl_monto_capital_pesos' || vl_monto_capital_pesos);
          --dbms_output.put_line('vl_monto_interes_pesos' || vl_monto_interes_pesos);

          IF ((vl_monto_capital_pesos + vl_monto_interes_pesos) <> 0) THEN
            -- porporcion K, IN
            vl_factor_K  := vl_monto_capital_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
            vl_factor_IN := vl_monto_interes_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
            vl_factor_MULTA := vl_monto_multa_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);

            vl_monto_capital  := Round((b.monto_total_pesos / vl_valor_moneda) * vl_factor_K, 4);
            vl_monto_interes  := Round((b.monto_total_pesos / vl_valor_moneda) * vl_factor_IN, 4);
            vl_monto_multa  := Round(((b.monto_total_pesos / vl_valor_moneda) - vl_monto_capital - vl_monto_interes), 4);

            IF (vl_monto_interes < 0)THEN
              vl_monto_capital := vl_monto_capital + (vl_monto_interes * -1);
              vl_monto_interes := 0;
            END IF;

            /*
            -- si no cuadra con el total_moneda que se esta pagando, se agrega la diferencia al abono K
            IF ((vl_monto_capital + vl_monto_interes) <> round(b.monto_capital_pesos/ vl_valor_moneda,4)) THEN
              vl_monto_capital := vl_monto_capital + (round(b.monto_capital_pesos / vl_valor_moneda,4) - (vl_monto_capital + vl_monto_interes));
            END IF;
            */
            -----------------------------------------
            --- INICIO ABONO CORRESPONDIENTE USACH --
            -----------------------------------------
            -- No se llama al spu_abono, ya que dentro del proceso puede ser que se creen excedentes
            -- y eso se quiere evitar, se hara para spu_abono y para spu_descuento, insert directo

            -- calcula el componente del pago
            vl_cod_componente_deuda := f_pp_componente_pago(vl_cod_plan_pago, b.fecha_pago);

            -- obtengo el monto_multa
            --vl_monto_multa_moneda:= Round(b.monto_interes_pesos / vl_valor_moneda, 4);
            -- suma de montos para insertar en abono campo "MONTO_PESOS"
            vl_monto_pesos:= b.monto_capital_pesos + b.monto_interes_pesos;
            vl_monto_moneda := vl_monto_capital + vl_monto_interes + vl_monto_multa;

            spu_abono_carga(b.fecha_pago    -- fecha_abono
              ,vl_cod_usuario   -- cod_usuario
              ,vl_monto_capital   -- monto_capital
              ,vl_monto_interes   -- monto_interes
              ,vl_monto_multa     -- monto_multa
              ,vl_monto_pesos
              ,0          -- diferencia_pesos
              ,vl_cod_plan_pago -- cod_plan_pago
              ,b.cod_deuda    -- cod_deuda
              ,null       -- cod_abono_desde
              ,vl_origen_pago     -- cod_origen_pago
              ,null       -- cod_sub_boleta
              ,null       -- cod_documento_pago
              ,vl_cod_comp_solidario -- cod_comprobante
              ,vl_cod_abono);

            UPDATE abono
            SET fecha_deposito = b.fecha_deposito
                ,folio_carga = b.nro_comprobante
            WHERE cod_abono = vl_cod_abono;

            -- Se Obtiene la cuenta contable del banco
            /*SELECT  cc.cod_cuenta_contable
            INTO    vl_cod_cuenta_bco
            FROM    origen_carga_banco ocb, cuenta_corriente cc
            WHERE   ocb.cod_origen_carga_banco = ve_cod_origen_carga_banco
            AND     cc.cod_cuenta_corriente = ocb.cod_cuenta_corriente;*/
            vl_cod_cuenta_bco := 11102; --BANCO DE CHILE

            spu_item_comprobante ('INSERT', NULL, vl_cod_comp_solidario, vl_cod_cuenta_bco,  b.monto_capital_pesos + b.monto_interes_pesos, 0);
            spu_analitico ('INSERT', NULL, vl_cod_comp_solidario, b.cod_deuda, vl_cod_cuenta_bco, b.monto_capital_pesos + b.monto_interes_pesos, 0);

            sp_comp_ing (vl_cod_comp_solidario, vl_cod_abono);
            --dbms_output.put_line('bbbbbbb');
            ---------------------------------------
            --  FIN ABONO CORRESPONDIENTE USACH  --
            ---------------------------------------

            -----------------------------------------------
            -- INICIO ABONO CORRESPONDIENTE A OTRAS UNIV --
            -----------------------------------------------
            vl_porc_otras_ues := 100 - b.porc_usach;

            IF(vl_porc_otras_ues > 0)THEN

              vl_calc_total_abono       := (vl_monto_pesos * 100) / b.porc_usach;
              vl_monto_k_otras_ues      := vl_calc_total_abono * (vl_porc_otras_ues / 100);
              vl_monto_k_otras_ues_utm  := Round(vl_monto_k_otras_ues / vl_valor_moneda, 4);

              vl_monto_capital_fict := Round(vl_monto_k_otras_ues_utm * vl_factor_K, 4);
              vl_monto_interes_fict := Round(vl_monto_k_otras_ues_utm * vl_factor_IN, 4);
              vl_monto_multa_fict := Round((vl_monto_k_otras_ues_utm - vl_monto_capital_fict - vl_monto_interes_fict), 4);

              IF (vl_monto_interes_fict < 0)THEN
                vl_monto_capital_fict := vl_monto_capital_fict + (vl_monto_interes_fict * -1);
                vl_monto_interes_fict := 0;
              END IF;

              /*
              ABONO
                23      100
              ------  = -----
                10000     X

              select round((10000* 100) / 23,0) from dual
              --43478

              select round(43478 * 0.77,0) from dual
              --33478

              DESCUENTO
                23      100
              ------  = -----
                1000     X

              select round((1000* 100) / 23,0) from dual
              --4348

              select round(4348 * 0.77,0) from dual
              --3348


              select 10000 + 33478 + 1000 + 3348 from dual
              -- 47826
              */

              INSERT INTO ABONO_FICTICIO
              VALUES(abono_ficticio_sq.NEXTVAL  -- cod_abono_ficticio
                   , SYSDATE                    -- fecha_registro
                   , b.fecha_pago               -- fecha_abono
                   , vl_cod_usuario             -- cod_usuario
                   , vl_monto_capital_fict      -- monto_capital
                   , vl_monto_interes_fict      -- monto_interes
                   , vl_monto_multa_fict        -- monto_multa
                   , vl_monto_k_otras_ues_utm   -- monto_moneda
                   , vl_monto_k_otras_ues       -- monto_pesos
                   , b.cod_deuda                -- cod_deuda
                   , vl_cod_plan_pago           -- cod_plan_pago
                   , vl_cod_abono               -- cod_abono
                   , b.fecha_deposito         -- fecha_deposito
                   , b.nro_comprobante        -- folio_carga
                   ,NULL
                   ,NULL
                   ,NULL
                   ,NULL
                   ,NULL
                   ,NULL
                   ,NULL
                   ,NULL
                   ,NULL);


              --sp_pp_actualiza_estado(vl_cod_plan_pago);
              --sp_deuda_actualiza_estado(b.cod_deuda);
              --dbms_output.put_line('xxxxx');
            END IF;
            --dbms_output.put_line('yyyyy');
            ---------------------------------------------
            --  FIN ABONO CORRESPONDIENTE OTRAS UNIV.  --
            ---------------------------------------------

            ----------------------------
            -- INICIO DESCUENTO USACH --
            ----------------------------
            -- si el monto descto_univ_pesos_usach > 0, crea el descuento
            IF( b.monto_descto_pesos > 0)THEN
              vl_monto_descto_moneda := Round(b.monto_descto_pesos / vl_valor_moneda, 4);

              vl_monto_dscto_capital  := Round(vl_monto_descto_moneda * vl_factor_K, 4);
              vl_monto_dscto_interes  := Round(vl_monto_descto_moneda * vl_factor_IN, 4);
              vl_monto_dscto_multa    := Round((vl_monto_descto_moneda - vl_monto_dscto_capital - vl_monto_dscto_interes), 4);

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
                    ,vl_monto_descto_moneda   -- monto_moneda
                    ,b.monto_descto_pesos     -- monto_pesos
                    ,vl_cod_plan_pago         -- cod_plan_pago
                    ,b.cod_deuda             -- cod_deuda
                    ,NULL                     -- cod_comprobante
                    ,K_ESTADO_PAGO_VIGENTE    -- cod_estado_pago
                    ,vl_origen_pago           -- cod_origen_pago
                    ,null
                    ,null           --obs_int
                    );

              SELECT descuento_sq.CURRVAL
              INTO vl_cod_descuento
              FROM dual;

              -- contabiliza el descuento
              vl_cod_cuenta_dscto := f_comp_get_cuenta_tipo_deuda(b.cod_tipo_deuda, K_CTA_DSCTO);
              spu_item_comprobante('INSERT', NULL, vl_cod_comp_solidario, vl_cod_cuenta_dscto, b.monto_descto_pesos, 0);
              spu_analitico ('INSERT', NULL, vl_cod_comp_solidario, b.cod_deuda, vl_cod_cuenta_dscto, b.monto_descto_pesos, 0);

              sp_comp_ing (vl_cod_comp_solidario, NULL, vl_cod_descuento);
              --dbms_output.put_line('11111');

            END IF;
            --dbms_output.put_line('22222');

            -------------------------
            -- FIN DESCUENTO USACH --
            -------------------------

            ---------------------------------
            -- INICIO DESCUENTO OTRAS UNIV --
            ---------------------------------
            IF(b.monto_descto_pesos > 0)THEN
              vl_calc_total_descto          := (b.monto_descto_pesos * 100) / b.porc_usach;
              vl_monto_dscto_otras_ues      := vl_calc_total_descto * (vl_porc_otras_ues / 100);
              vl_monto_dscto_otras_ues_utm  := Round(vl_monto_dscto_otras_ues / vl_valor_moneda, 4);

              vl_monto_dscto_capital_fict := Round(vl_monto_dscto_otras_ues_utm * vl_factor_K, 4);
              vl_monto_dscto_interes_fict := Round(vl_monto_dscto_otras_ues_utm * vl_factor_IN, 4);
              vl_monto_dscto_multa_fict := Round((vl_monto_dscto_otras_ues_utm - vl_monto_dscto_capital_fict - vl_monto_dscto_interes_fict), 4);

              IF (vl_monto_dscto_interes_fict < 0)THEN
                vl_monto_dscto_capital_fict := vl_monto_dscto_capital_fict + (vl_monto_dscto_interes_fict * -1);
                vl_monto_dscto_interes_fict := 0;
              END IF;


              INSERT INTO descuento_ficticio
              VALUES(descuento_ficticio_sq.NEXTVAL  -- cod_descuento_ficticio
                    ,vl_current_date                -- fecha_registro
                    ,b.fecha_pago                   -- fecha_descuento
                    ,vl_cod_usuario                 -- cod_usuario
                    ,vl_monto_dscto_capital_fict   -- monto_capital
                    ,vl_monto_dscto_interes_fict    -- monto_interes
                    ,vl_monto_dscto_multa_fict      -- monto_multa
                    ,vl_monto_dscto_otras_ues_utm   -- monto_moneda
                    ,vl_monto_dscto_otras_ues       -- monto_pesos
                    ,b.cod_deuda                    -- cod_deuda
                    ,vl_cod_plan_pago               -- cod_plan_pago
                    ,vl_cod_descuento               -- cod_descuento
                    );
              --dbms_output.put_line('33333');
            END IF;
            --dbms_output.put_line('44444');
            ------------------------------
            -- FIN DESCUENTO OTRAS UNIV --
            ------------------------------
            sp_pp_actualiza_estado(vl_cod_plan_pago);
            sp_deuda_actualiza_estado(b.cod_deuda);

            UPDATE  usach_cb_orsan
            SET     cargado = 'S'
            WHERE   cod_usach_cb_orsan = b.cod_usach_cb_orsan;
          ELSE
            --crea excedente

            SELECT cod_deudor
            INTO vl_cod_deudor_excedente
            FROM deudor
            WHERE RUT = b.rut;

            spu_excedente('INSERT'
                         ,NULL
                         ,NULL
                         ,vl_cod_deudor_excedente
                         ,b.monto_total_pesos
                         ,K_EST_EXCEDENTE_SOLICITADO
                         ,NULL
                         ,vl_cod_usuario
                         ,NULL
                         ,NULL
                         ,NULL
                         ,NULL);
          END IF;
        END IF;



        -- actualiza avance
        spu_ejecucion_proceso ('SET_ACTUAL', ve_cod_ejecucion_proceso, vl_i);
        vl_i := vl_i + 1;

      END LOOP;

      if(vl_count_fecha_dep > 0) THEN
        sp_comp_simplifica (vl_cod_comp_solidario);

        SELECT Count(*)
        INTO vl_cant_comp
        FROM comprobante
        WHERE cod_comprobante = vl_cod_comp_solidario;

        IF(vl_cant_comp <> 0)then
        spu_comprobante ('APROBAR', vl_cod_comp_solidario, vl_fecha_deposito);
        END IF;
      end if;

      vl_i := vl_i - 1;
      spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, vl_i);

     EXCEPTION
        WHEN OTHERS THEN
        -- Si ocurre un error de la BD al grabar un registro lo registra en el LOG. del sistema
        spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', SQLCODE, SUBSTR(SQLERRM,1,100), 'spp_usach_cb_orsan: plan_pago: ' || To_Char(vl_cod_plan_pago));
        spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, vl_i);
  END;
END spp_usach_cb_orsan;
/

