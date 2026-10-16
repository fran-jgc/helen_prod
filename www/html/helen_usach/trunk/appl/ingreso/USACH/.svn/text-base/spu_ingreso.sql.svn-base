PROMPT CREATE OR REPLACE PROCEDURE spu_ingreso
CREATE OR REPLACE PROCEDURE spu_ingreso(
   ve_operacion                 IN   VARCHAR2
 , ve_cod_ingreso               IN   NUMBER
 , ve_fecha_ingreso             IN   DATE        DEFAULT NULL
 , ve_cod_usuario               IN   NUMBER        DEFAULT NULL
 , ve_cod_deudor                IN   NUMBER        DEFAULT NULL
 , ve_cod_estado_ingreso        IN   NUMBER        DEFAULT NULL
 , ve_tasa_interes              IN   NUMBER        DEFAULT NULL
 , ve_monto_cheque_fecha        IN   NUMBER        DEFAULT NULL
 , ve_cant_cheque_fecha         IN   NUMBER        DEFAULT NULL
 , ve_fecha_primer_cheque_fecha IN   DATE        DEFAULT NULL
 , ve_referencia                IN   VARCHAR2    DEFAULT NULL
 , ve_cod_familia_deuda         IN   NUMBER        DEFAULT NULL
 , ve_cod_ingreso_desde         IN   NUMBER        DEFAULT NULL
 , ve_monto_otros_gastos        IN   NUMBER        DEFAULT NULL
 , ve_glosa_otros_gastos        IN   VARCHAR2    DEFAULT NULL
 , ve_monto_excedente            IN   NUMBER        DEFAULT NULL
 , ve_cr_tipo                    IN   VARCHAR2    DEFAULT NULL
 , ve_cr_codigo                    IN   VARCHAR2    DEFAULT NULL
 , ve_cr_cuotas                    IN   VARCHAR2    DEFAULT NULL
 , ve_k_cliente                      IN     VARCHAR     := NULL
)


AS
BEGIN


    DECLARE
        K_VIGENTE                    NUMBER:= 1;
      K_ANULADO                    NUMBER:= 2;
      K_ORIGEN_PAGO_CAJA            NUMBER:= 1;
      K_COMP_PENDIENTE            NUMBER:= 1;
      K_TIPO_COMP_INGRESO            NUMBER:= 1;
      K_ORIGEN_INGRESO_PAGO        NUMBER:= 11;
      K_ESTADO_PAGO_VIGENTE        NUMBER:= 1;
      K_CTA_DSCTO                    NUMBER:= 9;
      K_TIPO_DOC_DEPOSITO            NUMBER:= 3;
      K_PAGO_ANULADO                NUMBER:= 2;
      K_EST_DOC_SIMULACION        NUMBER:= 7;
      K_EST_DOC_ANULADO            NUMBER:= 8;
      K_EST_EXCEDENTE_SOLICITADO    NUMBER:= 1;
      vl_cod_componente_deuda        NUMBER;
      vl_cod_estado_ingreso        NUMBER;
      vl_monto_asignado_doc        NUMBER;
      vl_monto_asignado_cuota        NUMBER;
      vl_por_asignar                NUMBER;
      vl_monto                    NUMBER;
      vl_cod_usuario                NUMBER;
      vl_monto_capital_pesos        NUMBER;
      vl_monto_interes_pesos        NUMBER;
      vl_monto_multa_pesos        NUMBER;
      vl_factor_K                    NUMBER;
      vl_factor_IN                NUMBER;
      vl_factor_IP                NUMBER;
      vl_monto_abono                NUMBER;
      vl_monto_ch_fecha            NUMBER;
      vl_monto_ch                    NUMBER;
      vl_count                    NUMBER;
      vl_count_doc_pago            NUMBER;
      vl_fecha_docto_pago         DATE;

    vl_cod_ingreso                NUMBER;
        vl_current_date                DATE:=SYSDATE;
        vl_monto_capital            NUMBER;
        vl_monto_interes            NUMBER;
        vl_monto_multa                NUMBER;
        vl_valor_moneda                NUMBER;
        vl_glosa_compro                VARCHAR2(100);
        vl_cod_familia_deuda        NUMBER;
        vl_cod_comprobante            NUMBER;
        vl_cod_cuenta_ingreso        NUMBER;
        vl_cod_abono                NUMBER;
        vl_cod_analitico            NUMBER;
        vl_fecha_abono                DATE;
        vl_fecha_descuento            DATE;
        vl_fecha_ingreso            DATE;
        vl_validez_simulacion        NUMBER;
        vl_int_ch_fecha                NUMBER;
        vl_suma_ch_fecha            NUMBER;
        vl_cod_deuda                NUMBER;
        vl_cod_cuenta_int            NUMBER;
        vl_monto_pesos                NUMBER;
        vl_cod_cuenta_dscto            NUMBER;
        vl_cod_descuento            NUMBER;
        K_PARAM_MAX_VALIDEZ_SIMULACION    NUMBER:=24;
      K_COD_ESTADO_SIMULACION        NUMBER:=1000;
        vl_monto_otro_gasto            NUMBER;
        vl_cuenta                    NUMBER;
        vl_cod_deudor                NUMBER;
        vl_monto_excedente            NUMBER;
        vl_cod_deudor_excedente     NUMBER;
        vl_cont_cod_1_ano            NUMBER;


    vl_ultimo_dia_habil NUMBER;
    vl_dia_actual       NUMBER;
    K_PARAM_TIPO_UNIVERSIDAD    NUMBER:=27;
    vl_tipo_universidad VARCHAR2(100);
    vl_cod_cuenta_resp_ch_fecha NUMBER;


    K_EST_DEUDA_CANCELADA NUMBER:=4;
    K_EST_PP_PAGADA       NUMBER:=3;
    vl_cod_estado_deuda         NUMBER;
    vl_cod_estado_plan_pago     NUMBER;
    vl_cod_cuota_convenio        NUMBER;
    vl_cta_excedente          NUMBER;
    K_CONCEPTO_CUENTA_EXCEDENTE NUMBER:=19;




    CURSOR C_ABONO_INGR IS
            SELECT    ai.cod_abono
                    ,a.cod_plan_pago
                    ,a.cod_deuda
            FROM    abono_ingreso ai, abono a
            WHERE    ai.cod_ingreso = ve_cod_ingreso
              AND    a.cod_abono = ai.cod_abono
            ORDER BY ai.cod_abono;

    -- anula los descuentos asociados al ingreso
    CURSOR C_DESCUENTO_INGR IS
            SELECT    di.cod_descuento
                    ,d.cod_plan_pago
                    ,d.cod_deuda
            FROM    descuento_ingreso di, descuento d
            WHERE    di.cod_ingreso = ve_cod_ingreso
              and    d.cod_descuento = di.cod_descuento;

    -- finalmente si viene desde boleta se desmarca la subboleta como pagada feha_pago = null
    CURSOR C_SUB_BOLETA IS
            SELECT    cod_sub_boleta
            FROM    item_ingreso
            WHERE    cod_ingreso = ve_cod_ingreso
              and    cod_sub_boleta is not NULL;

    CURSOR c_doc IS
            SELECT  dp.cod_documento_pago
                    ,dp.monto
                    ,dp.cod_tipo_documento_pago
                    ,dp.cod_banco
                    ,dp.fecha_documento_pago
            FROM    documento_ingreso di, documento_pago dp
            WHERE    di.cod_ingreso = ve_cod_ingreso
              AND    di.cod_documento_pago = dp.cod_documento_pago
            ORDER BY dp.cod_documento_pago;

    CURSOR c_doc_fecha IS
            SELECT  dp.cod_documento_pago
                    ,dp.monto
            FROM    documento_ingreso_ch_fecha di, documento_pago dp
            WHERE    di.cod_ingreso = ve_cod_ingreso
              AND    di.cod_documento_pago = dp.cod_documento_pago
            ORDER BY dp.cod_documento_pago;

    CURSOR c_item_ingreso(vc_cod_ingreso number) IS
            SELECT  i.cod_item_ingreso
                    ,i.monto_pago_pesos
                    ,i.cod_plan_pago
                    ,i.cod_deuda
                    ,d.cod_deudor
                    ,pp.fecha_vencto
                    ,i.saldo_total_pesos
                    ,i.monto_pago
                    ,td.cod_moneda
                    ,i.cod_sub_boleta
            FROM    item_ingreso i left outer join plan_pago pp on i.cod_plan_pago = pp.cod_plan_pago, deuda d, tipo_deuda td
            WHERE   i.cod_ingreso = vc_cod_ingreso
              AND   d.cod_deuda = i.cod_deuda
              AND   td.cod_tipo_deuda = d.cod_tipo_deuda
            ORDER BY   d.cod_tipo_deuda, d.cod_deuda, pp.ano_cobro;


    CURSOR C_DSCTO IS
            SELECT    i.monto_descuento monto_moneda
                    ,i.cod_plan_pago
                    ,pp.fecha_vencto
                    ,i.cod_deuda
                    ,i.cod_sub_boleta
                    ,d.cod_tipo_deuda
                    ,td.cod_moneda
            FROM    item_ingreso i left outer join plan_pago pp on i.cod_plan_pago = pp.cod_plan_pago, deuda d, tipo_deuda td
            WHERE    i.cod_ingreso = ve_cod_ingreso
              and    d.cod_deuda = i.cod_deuda
              and   td.cod_tipo_deuda = d.cod_tipo_deuda
              and    i.monto_descuento > 0;

          CURSOR C_CUOTAS IS
      select column_value item
          from TABLE (split(ve_cr_cuotas, '-'));


    BEGIN    ---BEGIN INI

        IF (ve_operacion = 'INSERT') THEN ----INI INSERT
            SELECT    Count(*)
            INTO    vl_count
            FROM    ingreso
            WHERE    TO_NUMBER(TO_CHAR(fecha_ingreso, 'YYYY')) = TO_NUMBER(TO_CHAR(SYSDATE, 'YYYY'))
              AND    cod_estado_ingreso <> K_COD_ESTADO_SIMULACION;

            select COUNT (*) into vl_cont_cod_1_ano
            from INGRESO
            where COD_INGRESO = To_Number(TO_CHAR(SYSDATE, 'YY') || '00001');

        IF ((vl_count = 0)and (vl_cont_cod_1_ano =0))THEN--se agrega validacion que no exista el primer cod_ingreso del a?o con fecha_ingreso de a?o anterior EP + IS 05-01-2015

                SELECT To_Number(TO_CHAR(SYSDATE, 'YY') || '00001')
                INTO vl_cod_ingreso
                FROM dual;
            ELSE
                SELECT NVL(max(cod_ingreso),0)+1
                INTO vl_cod_ingreso
                FROM ingreso
                WHERE cod_estado_ingreso <> K_COD_ESTADO_SIMULACION;
            END IF;

            INSERT INTO ingreso
                     (cod_ingreso
                    , fecha_ingreso
                    , cod_usuario
                    , cod_deudor
                    , cod_estado_ingreso
                    , tasa_interes
                    , monto_cheque_fecha
                    , cant_cheque_fecha
                    , fecha_primer_cheque_fecha
                    , referencia
                    , cod_familia_deuda
                    , cod_ingreso_desde
                    , monto_otros_gastos
                    , glosa_otros_gastos
                    , monto_excedente
                    , k_cliente)
              VALUES (vl_cod_ingreso
                    , ve_fecha_ingreso
                    , ve_cod_usuario
                    , ve_cod_deudor
                    , K_VIGENTE
                    , ve_tasa_interes
                    , ve_monto_cheque_fecha
                    , ve_cant_cheque_fecha
                    , ve_fecha_primer_cheque_fecha
                    , ve_referencia
                    , ve_cod_familia_deuda
                    , ve_cod_ingreso_desde
                    , ve_monto_otros_gastos
                    , ve_glosa_otros_gastos
                    , ve_monto_excedente
                    , ve_k_cliente);

             IF (ve_cr_tipo = 'Convenio') THEN
                FOR a IN C_CUOTAS LOOP
                    IF (a.item= '0') THEN
                        insert into PAGO_INICIAL_CONVENIO (COD_CONVENIO, COD_INGRESO)
                        values (ve_cr_codigo, vl_cod_ingreso);
                    ELSE
                        select COD_CUOTA_CONVENIO
                        INTO vl_cod_cuota_convenio
                        from CUOTA_CONVENIO
                        where NRO_CUOTA = To_Number(a.item)
                            and COD_CONVENIO = To_Number(ve_cr_codigo);

                        insert into PAGO_CUOTA_CONVENIO (COD_CUOTA_CONVENIO, COD_INGRESO)
                        values (vl_cod_cuota_convenio, vl_cod_ingreso);

                    END IF;
                END LOOP;
            ELSIF (ve_cr_tipo = 'Renegociacion') THEN
                insert into PAGO_INICIAL_RENEGOCIACION (COD_RENEGOCIACION, COD_INGRESO)
                values(ve_cr_codigo, vl_cod_ingreso);

            END IF;

        ---END INSERT
        ELSIF (ve_operacion = 'INSERT_SIMULACION') THEN        ----INI INSERT_SIMULACION
            SELECT    Count(*)
            INTO    vl_count
            FROM    ingreso
            WHERE    cod_estado_ingreso = K_COD_ESTADO_SIMULACION;

            IF (vl_count = 0) THEN
                vl_cod_ingreso := 1;
            ELSE
                SELECT    max(cod_ingreso) + 1
                INTO    vl_cod_ingreso
                FROM    ingreso
                WHERE    cod_estado_ingreso = K_COD_ESTADO_SIMULACION;
            END IF;

            vl_validez_simulacion := f_get_parametro(K_PARAM_MAX_VALIDEZ_SIMULACION);

      --PARAMETRO VALIDEZ_SIMULACION FUE CAMBIADO AL VALOR 'N' SOLO PARA LA BASE DATO USACH
      IF(vl_validez_simulacion = 99999) THEN
        vl_dia_actual := To_Number(To_Char(SYSDATE,'dd'));
        vl_ultimo_dia_habil := To_Number(To_Char(To_Date(f_get_ultimo_dia_habil(sysdate)),'dd'));

        vl_validez_simulacion := vl_ultimo_dia_habil -  vl_dia_actual;
      END IF;

            INSERT INTO ingreso
                     (cod_ingreso
                    , fecha_ingreso
                    , cod_usuario
                    , cod_deudor
                    , cod_estado_ingreso
                    , tasa_interes
                    , monto_cheque_fecha
                    , cant_cheque_fecha
                    , fecha_primer_cheque_fecha
                    , referencia
                    , cod_familia_deuda
                    , validez_simulacion
                    , monto_otros_gastos
                    , glosa_otros_gastos
                    , k_cliente)
              VALUES (vl_cod_ingreso
                    , ve_fecha_ingreso
                    , ve_cod_usuario
                    , ve_cod_deudor
                    , K_COD_ESTADO_SIMULACION
                    , ve_tasa_interes
                    , ve_monto_cheque_fecha
                    , ve_cant_cheque_fecha
                    , ve_fecha_primer_cheque_fecha
                    , ve_referencia
                    , ve_cod_familia_deuda
                    , vl_validez_simulacion
                    , ve_monto_otros_gastos
                    , ve_glosa_otros_gastos
                    , ve_k_cliente);

        ----FIN INSERT_SIMULACION
        ELSIF (ve_operacion = 'UPDATE') THEN    ---INI UPDATE
            SELECT cod_estado_ingreso
            INTO vl_cod_estado_ingreso
            FROM ingreso
            WHERE cod_ingreso = ve_cod_ingreso;

            IF(vl_cod_estado_ingreso = K_VIGENTE
                AND ve_cod_estado_ingreso = K_ANULADO) THEN    ----INI ESTADO_ANULACION

        -- anula el ingreso
              UPDATE ingreso
              SET cod_estado_ingreso = K_ANULADO
              WHERE cod_ingreso = ve_cod_ingreso;

                FOR a IN C_ABONO_INGR LOOP
                    update abono
                    set cod_estado_pago = K_PAGO_ANULADO
                    where cod_abono = a.cod_abono;

                    -- actualiza el estado de plan_pago y deuda si corresponde
                    sp_pp_actualiza_estado (a.cod_plan_pago);
                    sp_deuda_actualiza_estado (a.cod_deuda);
                END LOOP;

                -- anula los descuentos asociados al ingreso
                FOR d IN C_DESCUENTO_INGR LOOP
                    update descuento
                    set cod_estado_pago = K_PAGO_ANULADO
                    where cod_descuento = d.cod_descuento;

                    -- actualiza el estado de plan_pago y deuda si corresponde
                    sp_pp_actualiza_estado (d.cod_plan_pago);
                    sp_deuda_actualiza_estado (d.cod_deuda);
                END LOOP;

                -- finalmente si viene desde boleta se desmarca la subboleta como pagada feha_pago = null
                FOR c IN C_SUB_BOLETA LOOP
                    update sub_boleta
                    set fecha_pago = null
                    where cod_sub_boleta = c.cod_sub_boleta;
                END LOOP;

                -- marca los documento_pago como ANULADO
                update documento_pago
                set cod_estado_documento_pago = K_EST_DOC_ANULADO
                where cod_documento_pago in (select cod_documento_pago
                                             from documento_ingreso
                                             where cod_ingreso = ve_cod_ingreso);

                update documento_pago
                set cod_estado_documento_pago = K_EST_DOC_ANULADO
                where cod_documento_pago in (select cod_documento_pago
                                             from documento_ingreso_ch_fecha
                                             where cod_ingreso = ve_cod_ingreso);



            END IF;    --- FIN ESTADO_ANULACION
        ----FIN UPDATE
        ELSIF (ve_operacion = 'ASIGNA') THEN    -----INI ASIGNA

             -- usuario, familia
            SELECT   cod_usuario
                    ,cod_familia_deuda
                    ,fecha_ingreso
                    ,monto_excedente
                    ,cod_deudor
            INTO     vl_cod_usuario
                    ,vl_cod_familia_deuda
                    ,vl_fecha_ingreso
                    ,vl_monto_excedente
                    ,vl_cod_deudor_excedente
            FROM ingreso
            WHERE cod_ingreso = ve_cod_ingreso;

            vl_glosa_compro := 'Abonos Ingreso:' ||    to_char(ve_cod_ingreso);
            spu_comprobante ('INSERT'
                        ,NULL
                        ,vl_current_date
                        ,vl_cod_usuario
                        ,vl_glosa_compro
                        ,vl_cod_familia_deuda
                        ,K_COMP_PENDIENTE
                        ,K_TIPO_COMP_INGRESO
                        ,K_ORIGEN_INGRESO_PAGO
                        ,NULL
                        ,vl_current_date);

            SELECT  comprobante_sq.CURRVAL
            INTO vl_cod_comprobante
            FROM dual;

            IF(vl_monto_excedente > 0)then
                spu_excedente('INSERT',NULL,NULL,vl_cod_deudor_excedente,vl_monto_excedente,K_EST_EXCEDENTE_SOLICITADO,NULL,vl_cod_usuario,NULL,NULL,NULL,NULL);

                -- se contabiliza el excedente
                ------------------------------

                -- se obtiene la cuenta del tipo_deuda del primer item_ingreso
                SELECT  f_comp_get_cuenta_tipo_deuda(d.cod_tipo_deuda, K_CONCEPTO_CUENTA_EXCEDENTE)
                        ,d.cod_deuda
                        ,d.cod_deudor
                INTO vl_cta_excedente
                        ,vl_cod_deuda
                        ,vl_cod_deudor
                      FROM    item_ingreso i, deuda d
                      WHERE   i.cod_ingreso = ve_cod_ingreso
                        AND   d.cod_deuda = i.cod_deuda
                  AND ROWNUM = 1;


                spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cta_excedente, 0, vl_monto_excedente);
                --se graba el nro de la ultima deuda
                spu_analitico ('INSERT', NULL, vl_cod_comprobante, vl_cod_deuda, vl_cta_excedente, 0, vl_monto_excedente);

                        SELECT  analitico_comprobante_sq.CURRVAL
                        INTO vl_cod_analitico
                        FROM dual;

                update analitico_comprobante
                        SET cod_deudor = vl_cod_deudor
                        where cod_analitico_comprobante = vl_cod_analitico;
            END IF;

            -- distribuye y deja grabdo en MONTO_DOC_ASIGNADO, que documento (o parte de el) se asigna al pago de una cuota
            -- tambien crea la cuota
            FOR a IN c_doc LOOP        -----INI CURSOR c_doc = a

                -- obtiene la cuenta de ingreso
                vl_cod_cuenta_ingreso := f_ingreso_get_cuenta(a.cod_documento_pago);

                spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cod_cuenta_ingreso,  a.monto, 0);
                spu_analitico ('INSERT', NULL, vl_cod_comprobante, null, vl_cod_cuenta_ingreso, a.monto, 0);

                SELECT  analitico_comprobante_sq.CURRVAL
                INTO vl_cod_analitico
                FROM dual;

                UPDATE ANALITICO_COMPROBANTE
                SET COD_INGRESO = ve_cod_ingreso
                WHERE COD_ANALITICO_COMPROBANTE = vl_cod_analitico;

                FOR b IN c_item_ingreso(ve_cod_ingreso) LOOP    -----INI CURSOR c_item_ingreso = b
                    SELECT Nvl(Sum(monto_asignado), 0)
                    INTO vl_monto_asignado_doc
                    FROM monto_doc_asignado
                    WHERE cod_documento_pago = a.cod_documento_pago;

                    SELECT Nvl(Sum(monto_asignado), 0)
                    INTO vl_monto_asignado_cuota
                    FROM monto_doc_asignado
                    WHERE cod_item_ingreso = b.cod_item_ingreso;

              vl_por_asignar := b.monto_pago_pesos - vl_monto_asignado_cuota;
                    vl_monto := a.monto - vl_monto_asignado_doc;

                    IF (vl_por_asignar < vl_monto) THEN
                        vl_monto := vl_por_asignar;
                    END IF;
                    IF (vl_monto > 0) THEN    ---INI IF_MONTO
                        -- VMC, 28-02-2011 el analitico de un ch a fecha queda asociado siempre a la ultima deuda que se abono y esto podria ser un error
                        update analitico_comprobante
                        set cod_deuda = b.cod_deuda
                            ,cod_deudor = b.cod_deudor
                        where cod_analitico_comprobante = vl_cod_analitico;

                        INSERT INTO monto_doc_asignado
                                    (cod_monto_doc_asignado
                                    ,cod_item_ingreso
                                    ,cod_documento_pago
                                    ,monto_asignado)
                            VALUES    (monto_doc_asignado_sq.NEXTVAL
                                    ,b.cod_item_ingreso
                                    ,a.cod_documento_pago
                                    ,vl_monto);
                        ------------------------------
                        -- crea el abono
                ------------------------------
                IF (a.cod_tipo_documento_pago = K_TIPO_DOC_DEPOSITO) THEN
                            vl_fecha_abono := a.fecha_documento_pago;
                            vl_fecha_descuento := a.fecha_documento_pago;
                        ELSE
                            vl_fecha_abono := vl_fecha_ingreso;
                            vl_fecha_descuento := vl_fecha_ingreso;
                        END IF;

                        vl_valor_moneda := f_mon_get_valor_moneda(b.cod_moneda, vl_fecha_abono);
                        vl_monto_abono := round(vl_monto / vl_valor_moneda, 4);

                        -- componente_deuda y porporcion K, IN, IP
                IF (b.cod_plan_pago IS NULL) THEN
                            vl_monto_capital_pesos := b.saldo_total_pesos;
                            vl_monto_interes_pesos := 0;
                            vl_monto_multa_pesos := 0;
                ELSE
                            vl_monto_capital_pesos := ROUND(f_pp_saldo_capital_caja(b.cod_plan_pago)* vl_valor_moneda,0);
                            vl_monto_interes_pesos := ROUND(f_pp_saldo_interes_normal_caja(b.cod_plan_pago) * vl_valor_moneda,0);
                            vl_monto_multa_pesos := ROUND(f_pp_saldo_multa_caja(b.cod_plan_pago) * vl_valor_moneda,0);


                        END IF;

                        IF ((vl_monto_capital_pesos
                            + vl_monto_interes_pesos
                            + vl_monto_multa_pesos) <> 0) THEN    ---INI SUMAS_TO
                            -- porporcion K, IN, IP
                            vl_factor_K  := vl_monto_capital_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
                            vl_factor_IN := vl_monto_interes_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
                            vl_factor_IP := vl_monto_multa_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
               ELSE
                vl_factor_K  := 1;
                vl_factor_IN := 0;
                vl_factor_IP := 0;
               END IF;


                            vl_monto_capital := Round(vl_monto_abono * vl_factor_K, 4);

                            vl_monto_interes := Round(vl_monto_abono * vl_factor_IN, 4);
                            vl_monto_multa := Round(vl_monto_abono * vl_factor_IP, 4);

                            -- si no cuadra con el total_moneda que se esta pagando, se agrega la diferencia al abono K
                            IF ((vl_monto_capital + vl_monto_interes + vl_monto_multa) <> vl_monto_abono) THEN
                                vl_monto_capital := vl_monto_capital + (vl_monto_abono - (vl_monto_capital + vl_monto_interes + vl_monto_multa));
                            END IF;

                            spu_abono    (vl_fecha_abono                  --ve_fecha_abono
                                ,vl_cod_usuario             --ve_cod_usuario
                                ,vl_monto_capital
                                ,vl_monto_interes
                                ,vl_monto_multa
                                ,vl_monto
                                ,0
                                ,b.cod_plan_pago
                                ,b.cod_deuda
                                ,NULL
                                ,K_ORIGEN_PAGO_CAJA
                                ,NULL
                                ,a.cod_documento_pago
                                ,vl_cod_comprobante
                                ,vl_cod_abono);

              --IF(vl_cod_abono IS NOT NULL)THEN
                sp_comp_ing (vl_cod_comprobante, vl_cod_abono);
              --END IF;

                            -- graba los abonos relacionados al ingreso
                            insert into ABONO_INGRESO
                                        (COD_ABONO_INGRESO
                                        ,COD_INGRESO
                                        ,COD_ABONO )
                            VALUES
                     (abono_ingreso_sq.NEXTVAL
                                      ,ve_cod_ingreso
                                      ,vl_cod_abono);

                    END IF;        ----FIN IF_MONTO
                END LOOP;    -----FIN CURSOR c_item_ingreso = b
            END LOOP;    -----FIN CURSOR c_doc = a

            -------------------------------
        SELECT f_get_parametro (K_PARAM_TIPO_UNIVERSIDAD)
        INTO vl_tipo_universidad
        FROM dual;


        -- crea abonos de ch a fecha
        SELECT monto_cheque_fecha
        INTO vl_monto_ch_fecha
        FROM ingreso
        WHERE cod_ingreso = ve_cod_ingreso;

      FOR a IN c_doc_fecha LOOP    -----INI CURSOR c_doc_fecha = a
                vl_monto_ch := a.monto;
                IF (vl_monto_ch > vl_monto_ch_fecha) THEN
                    vl_monto_ch := vl_monto_ch_fecha;
                END IF;

                vl_monto_ch_fecha := vl_monto_ch_fecha - vl_monto_ch;

                -- obtiene la cuenta de ingreso
                vl_cod_cuenta_ingreso := f_ingreso_get_cuenta(a.cod_documento_pago);

                spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cod_cuenta_ingreso,  a.monto, 0);
                spu_analitico ('INSERT', NULL, vl_cod_comprobante, null, vl_cod_cuenta_ingreso, a.monto, 0);

                SELECT  analitico_comprobante_sq.CURRVAL
                INTO vl_cod_analitico
                FROM dual;

                UPDATE ANALITICO_COMPROBANTE
                SET COD_INGRESO = ve_cod_ingreso
                WHERE COD_ANALITICO_COMPROBANTE = vl_cod_analitico;

                FOR b IN c_item_ingreso(ve_cod_ingreso) LOOP
                    SELECT Nvl(Sum(monto_asignado), 0)
                    INTO vl_monto_asignado_doc
                    FROM monto_doc_asignado
                    WHERE cod_documento_pago = a.cod_documento_pago;
                    --905090
                    SELECT Nvl(Sum(monto_asignado), 0)
                    INTO vl_monto_asignado_cuota
                    FROM monto_doc_asignado
                    WHERE cod_item_ingreso = b.cod_item_ingreso;
                    /*494435
                    select 905090 - 494435
                    410655*/
             /* vl_por_asignar := b.monto_pago_pesos - vl_monto_asignado_cuota;
                    vl_monto := vl_monto_ch - vl_monto_asignado_doc;*/

                vl_monto := a.monto;


                /*IF (vl_por_asignar < vl_monto) THEN
                      vl_monto := vl_por_asignar;
                  END IF;*/
                --dbms_output.put_line(vl_monto);

              IF (vl_monto > 0) THEN    ---INI IF_MONTO
            -- VMC, 28-02-2011 el analitico de un ch a fecha queda asociado siempre a la ultima deuda que se abono y esto podria ser un error
                        update analitico_comprobante
                        set cod_deuda = b.cod_deuda
                            ,cod_deudor = b.cod_deudor
                        where cod_analitico_comprobante = vl_cod_analitico;

                    INSERT INTO monto_doc_asignado
                            (cod_monto_doc_asignado
                            ,cod_item_ingreso
                            ,cod_documento_pago
                            ,monto_asignado)
                            VALUES (monto_doc_asignado_sq.NEXTVAL
                                    ,b.cod_item_ingreso
                                    ,a.cod_documento_pago
                                    ,vl_monto);


                IF (vl_tipo_universidad <> 'ESTATAL') THEN
                        ------------------------------
                        -- crea el abono
                        ------------------------------
                        vl_fecha_abono := vl_fecha_ingreso;
                        vl_valor_moneda := f_mon_get_valor_moneda(b.cod_moneda, vl_fecha_abono);
                        vl_monto_abono := round(vl_monto /  vl_valor_moneda, 4);

                        -- componente_deuda y porporcion K, IN, IP
                        IF (b.cod_plan_pago IS NULL) THEN
                            vl_monto_capital_pesos := b.saldo_total_pesos;
                            vl_monto_interes_pesos := 0;
                            vl_monto_multa_pesos := 0;
                        ELSE
                            vl_monto_capital_pesos := ROUND(f_pp_saldo_capital_caja(b.cod_plan_pago) * vl_valor_moneda, 0);
                            vl_monto_interes_pesos := ROUND(f_pp_saldo_interes_normal_caja(b.cod_plan_pago) * vl_valor_moneda,0);
                            vl_monto_multa_pesos := ROUND(f_pp_saldo_multa_caja(b.cod_plan_pago) * vl_valor_moneda, 0);
                        END IF;

                        if ((vl_monto_capital_pesos
                            + vl_monto_interes_pesos
                            + vl_monto_multa_pesos) <> 0) THEN    ---INI SUMAS_TO = 2
                            -- porporcion K, IN, IP
                            vl_factor_K  := vl_monto_capital_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
                            vl_factor_IN := vl_monto_interes_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
                            vl_factor_IP := vl_monto_multa_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);

                            vl_monto_capital := Round(vl_monto_abono * vl_factor_K, 4);
                            vl_monto_interes := Round(vl_monto_abono * vl_factor_IN, 4);
                            vl_monto_multa := Round(vl_monto_abono * vl_factor_IP, 4);

                            -- si no cuadra con el total_moneda que se esta pagando, se agrega la diferencia al abono K
                            IF ((vl_monto_capital + vl_monto_interes + vl_monto_multa) <> vl_monto_abono) THEN
                                vl_monto_capital := vl_monto_capital + (vl_monto_abono - (vl_monto_capital + vl_monto_interes + vl_monto_multa));
                            END IF;

                            spu_abono    (vl_fecha_abono                  --ve_fecha_abono
                                        ,vl_cod_usuario             --ve_cod_usuario
                                        ,vl_monto_capital
                                        ,vl_monto_interes
                                        ,vl_monto_multa
                                        ,vl_monto
                                        ,0
                                        ,b.cod_plan_pago
                                        ,b.cod_deuda
                                        ,NULL
                                        ,K_ORIGEN_PAGO_CAJA
                                        ,NULL
                                        ,a.cod_documento_pago
                                        ,vl_cod_comprobante
                                        ,vl_cod_abono);

              --IF(vl_cod_abono IS NOT NULL)THEN
                sp_comp_ing (vl_cod_comprobante, vl_cod_abono);
              --END IF;

                            -- graba los abonos relacionados al ingreso
                            INSERT INTO ABONO_INGRESO
                                        (COD_ABONO_INGRESO
                                        ,COD_INGRESO
                                        ,COD_ABONO )
                            VALUES
                     (abono_ingreso_sq.NEXTVAL
                                      ,ve_cod_ingreso
                                      ,vl_cod_abono);

                        END IF;    ---FIN SUMAS_TO = 2
END IF;-- (vl_tipo_universidad <> 'ESTATAL')

                    END IF;    ---FIN IF_MONTO = 2
                END LOOP;    -----FIN CURSOR c_item_ingreso = b (2)

            IF (vl_tipo_universidad = 'ESTATAL') THEN
              select    cod_cuenta_resp_ch_fecha
                    INTO    vl_cod_cuenta_resp_ch_fecha
                        from    familia_deuda
                        where    cod_familia_deuda = vl_cod_familia_deuda;

              spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cod_cuenta_resp_ch_fecha, 0, a.monto);
              spu_analitico ('INSERT', NULL, vl_cod_comprobante, null, vl_cod_cuenta_resp_ch_fecha, 0, a.monto);

              SELECT  analitico_comprobante_sq.CURRVAL
                INTO vl_cod_analitico
                FROM dual;

              update analitico_comprobante
              set cod_deudor = vl_cod_deudor
                    ,COD_INGRESO = ve_cod_ingreso
              where cod_analitico_comprobante = vl_cod_analitico;


            END IF;
            END LOOP;    -----FIN CURSOR c_doc_fecha = a (2)

IF (vl_tipo_universidad <> 'ESTATAL') THEN

            SELECT    Nvl(monto_cheque_fecha, 0)
            INTO    vl_monto_ch_fecha
            FROM    ingreso
            WHERE    cod_ingreso = ve_cod_ingreso;

            IF (vl_monto_ch_fecha > 0) THEN    ---INI MONTO_FECHA
                select    sum(dp.monto)
                INTO    vl_suma_ch_fecha
                from    documento_ingreso_ch_fecha di, documento_pago dp
                where    di.cod_ingreso = ve_cod_ingreso
                  and    dp.cod_documento_pago = di.cod_documento_pago;

                vl_int_ch_fecha := vl_suma_ch_fecha - vl_monto_ch_fecha;
                -- VMC, cuando se usa tasa interes cero pr problemas de redondeo puede ser que la suma de los ch a fecha
                -- sea menor "en pocos pesos" que el monto_ch_fecha y en este caso el interes queda negativo
                -- para estos casos no debe contabilizarse el interes ch a fecha
                IF (vl_int_ch_fecha > 0) then
                    select    cod_deuda
                    INTO    vl_cod_deuda
                    from    item_ingreso
                    where    rownum = 1
                      AND    cod_ingreso = ve_cod_ingreso;

                    -- verifica si tiene mas deudas en el ingreso, para saber si deja analitico asociado a deuda
                    select    count(*)
                    INTO    vl_count
                    from    item_ingreso
                    where    cod_ingreso = ve_cod_ingreso
                      and    cod_deuda <> vl_cod_deuda;

                    IF (vl_count > 0) THEN
                        -- el analitico no podra dejarse asociado a una deuda,
                        --porque se estan pagando varias deudas
                        vl_cod_deuda := null;
                    END IF;

                    select    cod_cuenta_ch_fecha
                    INTO    vl_cod_cuenta_int
                    from    familia_deuda
                    where    cod_familia_deuda = vl_cod_familia_deuda;

                    spu_item_comprobante('INSERT'
                                         ,NULL
                                         ,vl_cod_comprobante
                                         ,vl_cod_cuenta_int
                                         ,0
                                         ,vl_int_ch_fecha);

                    spu_analitico    ('INSERT'
                                    ,NULL
                                    ,vl_cod_comprobante
                                    ,vl_cod_deuda
                                    ,vl_cod_cuenta_int
                                    ,0
                                    ,vl_int_ch_fecha);

                    SELECT  analitico_comprobante_sq.CURRVAL
                    INTO vl_cod_analitico
                    FROM dual;

                    UPDATE ANALITICO_COMPROBANTE
                    SET COD_INGRESO = ve_cod_ingreso
                    WHERE COD_ANALITICO_COMPROBANTE = vl_cod_analitico;
                end if;
            END IF;    ---FIN MONTO_FECHA

  END IF; --(vl_tipo_universidad = 'ESTATAL')


            -- finalmente si viene desde boleta se desmarca la subboleta como pagada feha_pago = null
            FOR c IN C_SUB_BOLETA LOOP
                update sub_boleta
                set fecha_pago = SYSDATE
                where cod_sub_boleta = c.cod_sub_boleta;
            END LOOP;


            FOR c IN C_DSCTO LOOP    ---FIN CURSOR C_DSCTO = a (2)

                vl_valor_moneda := f_mon_get_valor_moneda(c.cod_moneda, vl_fecha_descuento);
                vl_monto_pesos := round(c.monto_moneda * vl_valor_moneda, 0);

                vl_cod_componente_deuda := f_pp_componente_pago(c.cod_plan_pago, vl_fecha_descuento);
                -- VMC, 18-01-2011.  Se asume que el descuento siempre va a Capital
                vl_monto_capital_pesos := vl_monto_pesos;
                vl_monto_interes_pesos := 0;
                vl_monto_multa_pesos := 0;

                vl_monto_capital := c.monto_moneda;
                vl_monto_interes := 0;
                vl_monto_multa := 0;


                SELECT count(*)
                INTO vl_count_doc_pago
                FROM documento_ingreso di, documento_pago dp
                WHERE dp.cod_documento_pago = di.cod_documento_pago
                AND   di.COD_INGRESO = ve_cod_ingreso
                AND   fecha_registro is not null;

                IF(vl_count_doc_pago > 0)THEN

                    SELECT fecha_documento_pago
                        INTO vl_fecha_docto_pago
                    FROM documento_ingreso di, documento_pago dp
                    WHERE dp.cod_documento_pago = di.cod_documento_pago
                    AND   di.COD_INGRESO = ve_cod_ingreso
                    AND ROWNUM = 1;


                    IF(vl_fecha_docto_pago>vl_fecha_ingreso)THEN
                        vl_fecha_ingreso := vl_fecha_docto_pago;
                    END IF;

                END IF;



                spu_descuento('INSERT'                    --@ve_operacion
                             ,null                        --@ve_cod_descuento
                             ,vl_fecha_ingreso            --@ve_fecha_descuento
                             ,vl_cod_usuario            --@ve_cod_usuario
                             ,vl_cod_componente_deuda    --@ve_cod_componente_deuda
                             ,vl_monto_capital            --@ve_monto_capital
                             ,vl_monto_interes            --@ve_monto_interes
                             ,vl_monto_multa            --@ve_monto_multa
                             ,c.monto_moneda            --@ve_monto_moneda
                             ,vl_monto_pesos            --@ve_monto_pesos
                             ,c.cod_plan_pago            --@ve_cod_plan_pago
                             ,c.cod_deuda                --@ve_cod_deuda
                             ,vl_cod_comprobante        --@ve_cod_comprobante
                             ,K_ESTADO_PAGO_VIGENTE        --@ve_cod_estado_pago
                             ,K_ORIGEN_PAGO_CAJA        --@ve_cod_origen_pago
                             ,c.cod_sub_boleta);        --@ve_cod_sub_boleta


                -- no se usa @@identity, porque spu_descuento llama a sp_pp_actualiza_estado y puede crear registro en otra tabla

        select descuento_sq.currval
                INTO vl_cod_descuento
                from dual;


                -- graba los decuento relacionados al ingreso
                insert into DESCUENTO_INGRESO
                            (COD_DESCUENTO_INGRESO
                            ,COD_INGRESO
                            ,COD_DESCUENTO )
                    values    (descuento_abono_sq.NEXTVAL
                            ,ve_cod_ingreso
                            ,vl_cod_descuento);

                -- contabiliza el descuento
                vl_cod_cuenta_dscto := f_comp_get_cuenta_tipo_deuda(c.cod_tipo_deuda, K_CTA_DSCTO);
                spu_item_comprobante('INSERT'
                                    ,NULL
                                    ,vl_cod_comprobante
                                    ,vl_cod_cuenta_dscto
                                    ,vl_monto_pesos
                                    ,0);

                spu_analitico    ('INSERT'
                                ,NULL
                                ,vl_cod_comprobante
                                ,vl_cod_deuda
                                ,vl_cod_cuenta_dscto
                                ,vl_monto_pesos
                                ,0);

                SELECT  analitico_comprobante_sq.CURRVAL
                INTO vl_cod_analitico
                FROM dual;

                UPDATE ANALITICO_COMPROBANTE
                SET COD_INGRESO = ve_cod_ingreso
                WHERE COD_ANALITICO_COMPROBANTE = vl_cod_analitico;

                sp_comp_ing    (vl_cod_comprobante, NULL, vl_cod_descuento);
            END LOOP;    ---INI CURSOR C_DSCTO = a (2)

            /* ERROR EN REDONDEO DEJA NO PAGADA LA CUOTA
       si en el ingreso pago el total de la cuota y la cuota no quedo en estado PAGADA, se fuerza ewl cambio
       La razon por la cual queda a veces con un peque?o saldo es por redonde en los decimales
       ya que por cada ch se crea un abono y es posible que al final la suma de los abonos se levemente menor al monto inicial
       Ej UCM
       UTM= 38518
       cod_ingreso =1100530

       2,6619    102.532    deberia ser 2,661924295
       2,6619    102.532    deberia ser 2,661924295
       2,6619    102.532    deberia ser 2,661924295
       2,6619    102.532    deberia ser 2,661924295
       2,5871     99.650    deberia ser 2,587102134
       suma = 13,2347
       saldo ini = 13,2348

       Con esta solucion los abonos qeudan cuadrados y la cuota pagada.
       En la ventana de detalle de un plan_pago se puede ver RARO un caso en que los abonos no sumen igual a la cuota pero esta este pagada
       Esto no se puede resolver, salvo que se adulteren los monto utm de los abonos lo cual es un error mayor.
       En resumen se opta por el "error" de menor impacto!!

       Para las ues ESTATAL si tiene ch a fecha NO realiza el ajuste!!
      */
      SELECT    Nvl(monto_cheque_fecha, 0)
            INTO    vl_monto_ch_fecha
            FROM    ingreso
            WHERE    cod_ingreso = ve_cod_ingreso;

      IF (vl_tipo_universidad <> 'ESTATAL' OR vl_monto_ch_fecha=0) THEN

        FOR d IN c_item_ingreso(ve_cod_ingreso) LOOP
          -- SOLO cuando el pago no es de subboleta, porque al ser de subboleta puede ser una parcialidad
          IF (d.monto_pago_pesos = d.saldo_total_pesos and d.cod_sub_boleta is null) THEN -- pago total

            IF (d.cod_plan_pago IS NULL) THEN -- deuda No exigible
              SELECT cod_estado_deuda
              INTO vl_cod_estado_deuda
              FROM deuda
              WHERE cod_deuda = d.cod_deuda;

              IF (vl_cod_estado_deuda <> K_EST_DEUDA_CANCELADA) THEN
                sp_deuda_update_estado(d.cod_deuda, K_EST_DEUDA_CANCELADA);
              END IF;
            ELSE -- deuda Exigible
              SELECT cod_estado_plan_pago
              INTO vl_cod_estado_plan_pago
              FROM plan_pago
              WHERE cod_plan_pago = d.cod_plan_pago;

              IF (vl_cod_estado_plan_pago <> K_EST_PP_PAGADA) THEN
                sp_pp_update_estado(d.cod_plan_pago, K_EST_PP_PAGADA);
                   sp_deuda_actualiza_estado(d.cod_deuda);

              END IF;
            END IF;
          END IF;
        END LOOP;
      END IF;

            ---------------
            -- contabiliza los otros gastos si Existen
            select NVL(monto_otros_gastos, 0)
            into vl_monto_otro_gasto
            from ingreso
            where cod_ingreso = ve_cod_ingreso;

            select f.cod_cuenta_ch_fecha
                ,i.cod_deudor
            into vl_cuenta
                ,vl_cod_deudor
            from ingreso i, familia_deuda f
            where i.cod_ingreso = ve_cod_ingreso
              and f.cod_familia_deuda = i.cod_familia_deuda;

            if(vl_monto_otro_gasto > 0)then
                spu_item_comprobante('INSERT', NULL, vl_cod_comprobante, vl_cuenta,  0, vl_monto_otro_gasto);
                spu_analitico('INSERT', NULL, vl_cod_comprobante, NULL, vl_cuenta, 0, vl_monto_otro_gasto);
                SELECT  analitico_comprobante_sq.CURRVAL
                INTO vl_cod_analitico
                FROM dual;
                update analitico_comprobante
                set cod_deudor = vl_cod_deudor
                    ,cod_ingreso = ve_cod_ingreso
                where cod_analitico_comprobante = vl_cod_analitico;
            end if;
            ---------------
            -- si todo el comprobante es a una misma deuda.
            -- se llenan con el cod_deuda los analiticos que no tengan deuda
            select count(distinct cod_deuda)
              INTO vl_count
            from analitico_comprobante
            where cod_comprobante = vl_cod_comprobante
              and cod_deuda is not null;

            if (vl_count = 1) then
                select cod_deuda
          ,cod_deudor
                into vl_cod_deuda
          ,vl_cod_deudor
                from analitico_comprobante
                where cod_comprobante = vl_cod_comprobante
                  and rownum = 1;

                update analitico_comprobante
                set cod_deuda = vl_cod_deuda
          ,cod_deudor = vl_cod_deudor
                where cod_comprobante = vl_cod_comprobante
                  and cod_deuda is null;
            end if;
            ---------------

            -- mueve COMPROBANTE => PRE_COMPROBANTE
            sp_ingreso_pre_comprobante (ve_cod_ingreso, vl_cod_comprobante);

        ----FIN ASIGNA
        ELSIF (ve_operacion = 'END_SIMULACION') THEN    ---INI END_SIMULACION
            -- marca los documento_pago como simulacion
            update documento_pago
            set cod_estado_documento_pago = K_EST_DOC_SIMULACION
            where cod_documento_pago in (select cod_documento_pago
                                         from documento_ingreso
                                         where cod_ingreso = ve_cod_ingreso);

            update documento_pago
            set cod_estado_documento_pago = K_EST_DOC_SIMULACION
            where cod_documento_pago in (select cod_documento_pago
                                         from documento_ingreso_ch_fecha
                                         where cod_ingreso = ve_cod_ingreso);
        END IF;    ---END_SIMULACION CIERRA TODOS LOS IF
   END;
END spu_ingreso;
/