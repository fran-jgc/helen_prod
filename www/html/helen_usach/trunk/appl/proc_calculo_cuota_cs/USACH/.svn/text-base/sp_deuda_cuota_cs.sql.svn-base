PROMPT CREATE OR REPLACE PROCEDURE sp_deuda_cuota_cs
CREATE OR REPLACE PROCEDURE sp_deuda_cuota_cs (
            ve_cod_deuda                  deuda.cod_deuda%TYPE
           ,ve_ano                        plan_pago.ano_cobro%TYPE
           ,ve_es_recalculo               VARCHAR2 := 'N'
           ,ve_cod_ejecucion_proceso     NUMBER := NULL
           ,vs_cod_comprobante1_new        IN OUT NUMBER
           ,vs_cod_comprobante2_new        IN OUT NUMBER
           ,ve_cod_usuario                NUMBER)
AS
BEGIN
  DECLARE    K_ESTADO_DECL_APRO              NUMBER:=2;
              K_CONCEPTO_COBRO_5PORC          NUMBER:=1;
              K_CONCEPTO_COBRO_0              NUMBER:=2;
              K_CONCEPTO_COBRO_35             NUMBER:=3;
              K_CONCEPTO_COBRO_20             NUMBER:=4;
              K_CONCEPTO_COBRO_DPA            NUMBER:=5;
              K_ESTADO_PLAN_P_EMITIDA         NUMBER:=1;
              K_ESTADO_PLAN_P_EXENTA          NUMBER:=8;
              K_COND_CS_EMITIDA               NUMBER:=1;
              K_COND_CS_APROBADA              NUMBER:=2;
              K_COMP_VENCIDA                  NUMBER:=2;
              K_COMP_FUTURA                   NUMBER:=4;
              K_FONDO_SOLIDARIO                NUMBER:=1;
              K_ESTADO_COMP_PENDIENTE            NUMBER:=1;
              K_TIPO_COMP_TRASPASO            NUMBER:=3;
              K_ORIGEN_CALCULO_CUOTA_CS        NUMBER:=19;
              K_CTA_CP                        NUMBER:=2;
              K_CTA_LP                        NUMBER:=3;
            K_CTA_CASTIGO                    number:=4;

            vl_valor_cuota                  plan_pago.monto_capital%TYPE;
            vl_cod_concepto_cobro           concepto_cobro.cod_concepto_cobro%TYPE;
            vl_coun_plan_pago               NUMBER;
            vl_max_cuota                    plan_pago.nro_cuota%TYPE;
            vl_nro_cuota                    plan_pago.nro_cuota%TYPE;
            vl_cuota99                      plan_pago.monto_capital%TYPE;
            vl_monto_capital_99             plan_pago.monto_capital%TYPE;
            vl_monto_interes_99             plan_pago.monto_capital%TYPE;
            vl_fecha_vencto                 DATE;
            vl_fecha_declaracion            DATE;
            vl_20_porc                      NUMBER;
            vl_count                        NUMBER;
            vl_count_fe_decl                NUMBER;
            vl_pago_anterior                NUMBER;
            vl_monto_capital                NUMBER;
            vl_monto_interes                NUMBER;
            vl_cod_estado_pp                plan_pago.cod_estado_plan_pago%TYPE;
            vl_cod_plan_pago99              plan_pago.cod_plan_pago%TYPE;
            vl_cod_plan_pago                plan_pago.cod_plan_pago%TYPE;
            vl_operacion                    VARCHAR2(10);
            vl_count_cruzado                NUMBER;
            vl_cant_pp                      NUMBER;
            vl_cant_cuota                   NUMBER;
            vl_saldo_vencida                NUMBER;
            vl_saldo_futura                 NUMBER;
            vl_cod_estado_condonacion_cs    NUMBER;
            vl_monto_anual_deudor_moneda    NUMBER;
            vl_nom_comprobante                VARCHAR2(100);
            vl_fecha_actual                    DATE;
            vl_CTY                            NUMBER;
            vl_CAT                            NUMBER;
            vl_dif                            NUMBER;
            vl_cta_cp                        NUMBER;
            vl_cta_lp                        NUMBER;
            vl_cta_condonaciones            NUMBER;
            vl_monto                        NUMBER;
            vl_cod_moneda                    NUMBER;
            vl_cod_comprobante_anula        NUMBER;

      --variables para calculo de cuota sin DJ
      vl_new_ano_cobro    NUMBER;
      vl_new_nro_cuota    NUMBER;
      vl_cuota            NUMBER;
      vl_saldo            NUMBER;
      i                   NUMBER;
      vl_capital          NUMBER;
      vl_interes            NUMBER;
      KL_CONCEPTO_SIN_DJ  NUMBER:=11;


    BEGIN

        --SETAMOS LA VARIABLE FECHA
        SELECT SYSDATE
        INTO  vl_fecha_actual
        FROM DUAL;

        ------------------------------------------------------------------------------------------
        -- MAIN
        ------------------------------------------------------------------------------------------
        -- obtiene la moneda
        SELECT  cod_moneda
        INTO     vl_cod_moneda
        FROM    tipo_deuda td
                ,deuda d
        WHERE   td.cod_tipo_deuda =  d.cod_tipo_deuda
        AND        cod_deuda = ve_cod_deuda;


        -- obtiene las ctas
        vl_cta_cp    := f_comp_get_cuenta_tipo_deuda ('CS', K_CTA_CP);
        vl_cta_lp    := f_comp_get_cuenta_tipo_deuda ('CS', K_CTA_LP);
        vl_CAT         := f_deuda_CAT(ve_cod_deuda, ve_ano);

        IF (vs_cod_comprobante1_new IS NULL) THEN
            -- crear comprobante
            IF (ve_es_recalculo = 'S') THEN
                vl_nom_comprobante := 'Re-Calculo Cuota CS (CTY > CAT), deuda:  ' || TO_CHAR(ve_cod_deuda);
            ELSE
                vl_nom_comprobante := 'Calculo Cuota CS (CTY > CAT):  ' || TO_CHAR(SYSDATE, 'dd/mm/yyyy');
            END IF;

            spu_comprobante ('INSERT', NULL, vl_fecha_actual, ve_cod_usuario, vl_nom_comprobante, K_FONDO_SOLIDARIO, K_ESTADO_COMP_PENDIENTE, K_TIPO_COMP_TRASPASO, K_ORIGEN_CALCULO_CUOTA_CS, ve_cod_usuario, vl_fecha_actual);

            SELECT  comprobante_sq.CURRVAL
            INTO    vs_cod_comprobante1_new
            FROM    dual;

            IF (ve_es_recalculo = 'S') THEN
                vl_nom_comprobante := 'Re-Calculo Cuota CS (CAT > CTY), deuda:  ' || TO_CHAR(ve_cod_deuda);
            ELSE
                vl_nom_comprobante := 'Calculo Cuota CS (CAT > CTY):  ' ||  TO_CHAR(SYSDATE, 'dd/mm/yyyy');
            END IF;

            spu_comprobante ('INSERT', NULL, vl_fecha_actual, ve_cod_usuario, vl_nom_comprobante, K_FONDO_SOLIDARIO, K_ESTADO_COMP_PENDIENTE, K_TIPO_COMP_TRASPASO, K_ORIGEN_CALCULO_CUOTA_CS, ve_cod_usuario, vl_fecha_actual);

            SELECT  comprobante_sq.CURRVAL
            INTO    vs_cod_comprobante2_new
            FROM    dual;
        END IF;


        IF (ve_es_recalculo = 'S') THEN
            -- crea el comprobante que anula el calculo anterior
            SELECT    monto_capital + monto_interes
                    ,fecha_vencto
            INTO    vl_CTY
                    ,vl_fecha_vencto
            FROM    plan_pago
            WHERE    cod_deuda = ve_cod_deuda
              AND    ano_cobro = ve_ano;

            /*-- si han pasado mas de 3 meses del vencto => se debe usar la cta de castigo
            -- sino se usa la cta CP
            select    FECHA_DECLARACION
              into    vl_fecha_declaracion
              from    DECLARACION DE, DEUDA DA
             where    DA.COD_DEUDA = ve_cod_deuda
               and    DA.COD_DEUDOR = DE.COD_DEUDOR
               and    DE.ANO = ve_ano - 1
               and    DE.COD_ESTADO_DECLARACION = K_ESTADO_DECL_APRO;

            IF (vl_fecha_declaracion IS NOT NULL) THEN
                IF (MONTHS_BETWEEN(vl_fecha_declaracion, vl_fecha_vencto) > 3) THEN
                    vl_cta_cp := f_comp_get_cuenta_tipo_deuda ('CS', K_CTA_CASTIGO);
                END IF;
            END IF;*/

            IF (vl_fecha_vencto < sysdate) THEN
                vl_cta_cp := f_comp_get_cuenta_tipo_deuda ('CS', K_CTA_CASTIGO);
            END IF;

            vl_dif        := vl_CTY - vl_CAT;
            vl_monto    :=  round(vl_dif * f_mon_get_valor_moneda(vl_cod_moneda, SYSDATE), 0);

            IF (vl_CTY > vl_CAT) THEN
                vl_nom_comprobante    := 'Anula Calculo Cuota CS (CTY > CAT), deuda:  ' || TO_CHAR(ve_cod_deuda);
                spu_comprobante ('INSERT', NULL, vl_fecha_actual, ve_cod_usuario, vl_nom_comprobante, K_FONDO_SOLIDARIO, K_ESTADO_COMP_PENDIENTE, K_TIPO_COMP_TRASPASO, K_ORIGEN_CALCULO_CUOTA_CS, ve_cod_usuario, vl_fecha_actual);

                SELECT  comprobante_sq.CURRVAL
                INTO    vl_cod_comprobante_anula
                FROM    dual;

                spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante_anula, vl_cta_cp, 0, vl_monto);
                spu_analitico ('INSERT', NULL, vl_cod_comprobante_anula, ve_cod_deuda, vl_cta_cp, 0, vl_monto);

                spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante_anula, vl_cta_lp, vl_monto, 0);
                spu_analitico ('INSERT', NULL, vl_cod_comprobante_anula, ve_cod_deuda, vl_cta_lp, vl_monto, 0);
            ELSE
                vl_nom_comprobante := 'Anula Calculo Cuota CS (CAT > CTY), deuda:  ' || To_Char(ve_cod_deuda);
                spu_comprobante ('INSERT', NULL, vl_fecha_actual, ve_cod_usuario, vl_nom_comprobante, K_FONDO_SOLIDARIO, K_ESTADO_COMP_PENDIENTE, K_TIPO_COMP_TRASPASO, K_ORIGEN_CALCULO_CUOTA_CS, ve_cod_usuario, vl_fecha_actual);
                SELECT  comprobante_sq.CURRVAL
                INTO    vl_cod_comprobante_anula
                FROM    dual;

                vl_monto := - vl_monto;
                spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante_anula, vl_cta_cp, vl_monto, 0);
                spu_analitico ('INSERT', NULL, vl_cod_comprobante_anula, ve_cod_deuda, vl_cta_cp, vl_monto, 0);

                spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante_anula, vl_cta_lp, 0, vl_monto);
                spu_analitico ('INSERT', NULL, vl_cod_comprobante_anula, ve_cod_deuda, vl_cta_lp, 0, vl_monto);
            END IF;
        END IF;


        -- Si el deudor cumplio las 12 o 15 cuotas se debe revisar sis es candidato a condonacion CS
        -- cantidad de cuotas creadas
        SELECT    Count(*)
        INTO    vl_cant_pp
        FROM    plan_pago
        WHERE    cod_deuda  = ve_cod_deuda
        AND        ano_cobro <> 9999;

        SELECT    cant_cuota
        INTO    vl_cant_cuota
        FROM    deuda
        WHERE    cod_deuda  = ve_cod_deuda;

        -- cumplieron la cantidad de cuotas 12 o 15
        IF (vl_cant_pp = vl_cant_cuota AND ve_es_recalculo = 'N') THEN
            SELECT    Count(*)
            INTO    vl_count
            FROM    condonacion_cs
            WHERE    cod_deuda = ve_cod_deuda;

            IF (vl_count = 0) THEN
                vl_saldo_vencida := f_deuda_saldo_componente(ve_cod_deuda, K_COMP_VENCIDA, SYSDATE);
                -- no tienen saldo moroso
                IF (vl_saldo_vencida = 0) THEN
                    vl_saldo_futura := f_deuda_saldo_componente(ve_cod_deuda, K_COMP_FUTURA, SYSDATE);
                    -- tiene saldo futuro que condonar
                    IF (vl_saldo_futura > 0) THEN
                        INSERT INTO condonacion_cs
                                    (cod_condonacion_cs
                                    ,fecha_condonacion_cs
                                    ,cod_estado_condonacion_cs
                                    ,cod_deuda
                                    ,obs)
                        VALUES
                                    (condonacion_cs_sq.NEXTVAL
                                    ,SYSDATE
                                    ,K_COND_CS_EMITIDA
                                    ,ve_cod_deuda
                                    ,null);

                        RETURN;
                    END IF;
                END IF;
            ELSE
                SELECT    cod_estado_condonacion_cs
                INTO    vl_cod_estado_condonacion_cs
                FROM    condonacion_cs
                WHERE    cod_deuda = ve_cod_deuda;

                IF (vl_cod_estado_condonacion_cs = K_COND_CS_EMITIDA) THEN
                    RETURN;
                ELSIF (vl_cod_estado_condonacion_cs = K_COND_CS_APROBADA) THEN
                    RETURN;
          END IF;
            END IF;
        END IF;

        -- cruzado con una universidad que recauda cruzados
        SELECT  Count(*)
        INTO    vl_count_cruzado
        FROM    cruzado c
                ,universidad u
        WHERE   c.cod_deuda = ve_cod_deuda
        AND        c.cod_universidad = u.cod_universidad
        AND        u.recauda_cobranza = 'S';

        SELECT  Count(*)
        INTO    vl_count
        FROM    plan_pago
        WHERE   cod_deuda = ve_cod_deuda
        AND        ano_cobro = 9999;

        IF (vl_count > 0) THEN
            SELECT  cod_plan_pago
            INTO    vl_cod_plan_pago99
            FROM    plan_pago
            WHERE   cod_deuda = ve_cod_deuda
            AND        ano_cobro = 9999;

            -- suma a la cuota 99 la cuota ya calculada
            IF(ve_es_recalculo = 'S') THEN
                -- cuota actual
                select     monto_capital
                        ,monto_interes
                INTO     vl_monto_capital
                        ,vl_monto_interes
                from plan_pago
                WHERE   cod_deuda = ve_cod_deuda
                AND        ano_cobro = ve_ano;

                UPDATE  plan_pago
                SET     monto_capital = monto_capital + vl_monto_capital
                        ,monto_interes = monto_interes  + vl_monto_interes
                WHERE   cod_plan_pago = vl_cod_plan_pago99;
            END IF;

            vl_cuota99  := f_pp_saldo_plan_pago (vl_cod_plan_pago99, SYSDATE);

            if(vl_cuota99 >= 0) THEN -- si el saldo de la cuota 99 es >= 0 entra al if, sino lo arrojara como error

                vl_cod_concepto_cobro := f_deuda_concepto_cs(ve_cod_deuda, ve_ano);
                IF(vl_cod_concepto_cobro = K_CONCEPTO_COBRO_0) THEN
                    vl_valor_cuota := 0;

                ELSIF(vl_cod_concepto_cobro = K_CONCEPTO_COBRO_35) THEN
                    vl_valor_cuota := 3.5;

                ELSIF(vl_cod_concepto_cobro = K_CONCEPTO_COBRO_5PORC) THEN
                    SELECT  NVL(Sum(monto_deudor_moneda),0)
                    INTO    vl_monto_anual_deudor_moneda
                    FROM    deuda da, declaracion d, declaracion_mes dm
                    WHERE   da.cod_deuda = ve_cod_deuda
                    AND        d.cod_deudor = da.cod_deudor
                    AND        d.ano = ve_ano - 1
                    AND        d.cod_estado_declaracion = K_ESTADO_DECL_APRO
                    AND        dm.cod_declaracion = d.cod_declaracion;

                    vl_valor_cuota := ROUND(vl_monto_anual_deudor_moneda * 0.0500,4);

        ELSIF (vl_cod_concepto_cobro = KL_CONCEPTO_SIN_DJ) THEN

          vl_cuota99 := vl_cuota99/1.02;

                  SELECT Nvl(Max(ANO_COBRO),To_Number(To_Char(SYSDATE, 'yyyy'))-1) + 1
          INTO vl_new_ano_cobro
                  FROM    plan_pago
                  WHERE   cod_deuda = ve_cod_deuda
                        AND ANO_COBRO <> 9999;

          SELECT Nvl(Max(NRO_CUOTA),0) + 1
          INTO vl_new_nro_cuota
                  FROM    plan_pago
                  WHERE   cod_deuda = ve_cod_deuda
                        AND ANO_COBRO <> 9999;

                  --ineteres hasta mayo del a?o actual, se lleva a su valor a principio de a?o
                  IF (vl_cuota99 >= 0.0000 AND vl_cuota99 <= 50.9999) THEN
                      vl_cant_cuota := 6;
                  ELSIF (vl_cuota99 >= 51.0000 AND vl_cuota99 <= 100.9999) THEN
                      vl_cant_cuota := 9;
                  ELSIF (vl_cuota99 >= 101.0000 AND vl_cuota99 <= 200.9999) THEN
                      vl_cant_cuota := 12;
                  ELSE
                      vl_cant_cuota := 15;
          END IF;


                --copia pp en PLAN_PAGO_H
                sp_deuda_plan_pago_h (ve_cod_deuda, 'sp_deuda_cuota_cs: CONCEPTO_SIN_DJ', 0);

        vl_cuota := Round(vl_cuota99 * ((power((1.02),vl_cant_cuota)) * 0.02)/((power((1.02),vl_cant_cuota))-1), 4);
                vl_saldo := vl_cuota99;

                i := 1;
                while (i<=vl_cant_cuota) LOOP
                    IF (i = vl_cant_cuota) THEN
                        vl_capital := vl_saldo;

                        IF (vl_capital = 0) THEN
                            vl_interes := 0;
                        ELSE
                            vl_interes := Round(vl_cuota - vl_capital, 4);
                            IF (vl_interes < 0) THEN
                                vl_interes := 0;
              END IF;
                        END IF;
          ELSE
                      vl_interes := Round(vl_saldo * 0.02, 4);
                        vl_capital := Round(vl_cuota - vl_interes, 4);
                        IF (vl_capital  >  vl_saldo) THEN
                            vl_capital := vl_saldo;
            END IF;
                    END IF; --(i = vl_cant_cuota)


                    vl_saldo := vl_saldo - vl_capital;
                    vl_fecha_vencto := to_Date('31/12/' || To_Char(vl_new_ano_cobro), 'dd/mm/yyyy');

          INSERT INTO PLAN_PAGO
                           (COD_PLAN_PAGO
               ,COD_DEUDA
                           ,NRO_CUOTA
                           ,ANO_COBRO
                           ,MONTO_CAPITAL
                           ,MONTO_INTERES
                           ,FECHA_VENCTO
                           ,COD_CONCEPTO_COBRO
                           ,COD_ESTADO_PLAN_PAGO
                           ,COD_EMPRESA_COBRANZA
                           ,PUBLICA_DICOM
                           ,COD_ANALITICO_COMPROBANTE)
                    VALUES
                           (plan_pago_sq.NEXTVAL
               ,ve_cod_deuda
                           ,vl_new_nro_cuota
                           ,vl_new_ano_cobro
                           ,vl_capital
                           ,vl_interes
                           ,vl_fecha_vencto
                           ,KL_CONCEPTO_SIN_DJ
                           ,K_ESTADO_PLAN_P_EMITIDA
                           ,null
                           ,'N'
                           ,null);


                    vl_new_nro_cuota := vl_new_nro_cuota + 1;
                    vl_new_ano_cobro := vl_new_ano_cobro + 1;
                    i := i + 1;
                END LOOP;

                --borra pp 99
                update ABONO
                set COD_COMPONENTE_DEUDA = 1 -- NO EXIGIBLE
                    ,COD_PLAN_PAGO = null
                    ,COD_SUB_BOLETA = null
                    ,OBS_INT = 'PAGO EN CUOTA 99: COD' || To_Char(vl_cod_plan_pago99)
                where cod_plan_pago = vl_cod_plan_pago99;

                update DESCUENTO
                set COD_COMPONENTE_DEUDA = 1 -- NO EXIGIBLE
                    ,COD_PLAN_PAGO = null
                    ,COD_SUB_BOLETA = null
                    ,OBS_INT = 'DESCUENTO EN CUOTA 99: COD' || To_Char(vl_cod_plan_pago99)
                where cod_plan_pago = vl_cod_plan_pago99;
        
        DELETE WP_SUB_BOLETA
		WHERE cod_sub_boleta IN (SELECT cod_sub_boleta FROM sub_boleta sb,boleta b, plan_pago pp 
                             WHERE b.cod_plan_pago = pp.cod_plan_pago 
                             AND b.cod_boleta = sb.cod_boleta
                             AND pp.cod_plan_pago = vl_cod_plan_pago99); 
      
        DELETE sub_boleta
        WHERE cod_boleta IN (SELECT cod_boleta FROM boleta b, plan_pago pp WHERE b.cod_plan_pago = pp.cod_plan_pago AND pp.cod_plan_pago = vl_cod_plan_pago99);

        DELETE boleta
        WHERE cod_plan_pago = vl_cod_plan_pago99;

        DELETE cambio_estado_plan_pago
        where cod_plan_pago = vl_cod_plan_pago99;
        
		update ITEM_INGRESO
		set cod_plan_pago = null
		where cod_plan_pago = vl_cod_plan_pago99;
		
		delete descuento_ficticio
        where cod_plan_pago = vl_cod_plan_pago99;
		
        delete plan_pago
        where cod_plan_pago = vl_cod_plan_pago99;
        GOTO END_CALC_CUOTA;

            END IF;

            IF (vl_valor_cuota > vl_cuota99) THEN
                vl_monto_capital := Round(vl_cuota99 / 1.0200, 4);
                vl_monto_interes := Round(vl_cuota99 - vl_monto_capital, 4);
            ELSE
                vl_monto_capital := Round(vl_valor_cuota / 1.0200, 4);
                vl_monto_interes := Round(vl_valor_cuota - vl_monto_capital, 4);
            END IF;

                SELECT  Count(*)
                INTO    vl_coun_plan_pago
                FROM    plan_pago
                WHERE   cod_deuda = ve_cod_deuda;

                IF (vl_coun_plan_pago > 0 ) THEN
                    SELECT  Max(nro_cuota)
                    INTO    vl_max_cuota
                    FROM    plan_pago
                    WHERE   cod_deuda = ve_cod_deuda
                    AND        nro_cuota <> 99;

                    vl_nro_cuota := vl_max_cuota + 1;
                ELSE
                    vl_nro_cuota := 1;
                END IF;

				vl_fecha_vencto    := to_date('31/05/' || to_char(ve_ano + 1) || ' 23:59:59', 'dd/mm/yyyy HH24:MI:SS');
                IF ((vl_valor_cuota + vl_monto_interes) = 0) THEN
                    vl_cod_estado_pp  := K_ESTADO_PLAN_P_EXENTA;
                ELSE
                    vl_cod_estado_pp  := K_ESTADO_PLAN_P_EMITIDA;
                END IF;

                IF (vl_nro_cuota IS NULL) THEN
                    vl_nro_cuota := 1;
                END IF;

                IF(ve_es_recalculo = 'S') THEN
                    vl_operacion := 'UPDATE';
                    SELECT   cod_plan_pago
                            ,nro_cuota
                    INTO     vl_cod_plan_pago
                            ,vl_nro_cuota
                    FROM    plan_pago
                    WHERE   cod_deuda = ve_cod_deuda
                    AND        ano_cobro = ve_ano;
                ELSE
                    vl_operacion    := 'INSERT';
                    vl_cod_plan_pago := NULL;
                END IF;

                spu_plan_pago  (vl_operacion
                                ,vl_cod_plan_pago
                                ,ve_cod_deuda
                                ,vl_nro_cuota
                                ,ve_ano
                                ,vl_monto_capital
                                ,vl_monto_interes
                                ,vl_fecha_vencto
                                ,vl_cod_concepto_cobro
                                ,vl_cod_estado_pp
                                ,NULL    -- cod_empresa_cobranza
                                ,'N'    -- publica_dicom
                                );

                SELECT   monto_capital
                        ,monto_interes
                INTO     vl_monto_capital_99
                        ,vl_monto_interes_99
                FROM    plan_pago
                WHERE   cod_plan_pago = vl_cod_plan_pago99;

                vl_monto_capital_99 := vl_monto_capital_99 - vl_monto_capital;
                vl_monto_interes_99 := vl_monto_interes_99 - vl_monto_interes;

                IF (vl_monto_capital_99 < 0) THEN
                    vl_monto_interes_99 := vl_monto_capital_99 + vl_monto_interes_99;
                    vl_monto_capital_99 := 0;
                END IF;
                IF (vl_monto_interes_99 < 0) THEN
                    vl_monto_capital_99 := vl_monto_capital_99 + vl_monto_interes_99;
                    vl_monto_interes_99 := 0;
                END IF;

                UPDATE  plan_pago
                SET     monto_capital = vl_monto_capital_99
                        ,monto_interes = vl_monto_interes_99
                WHERE   cod_plan_pago = vl_cod_plan_pago99;

                sp_pp_actualiza_estado(vl_cod_plan_pago99);

                ------------
                /*-- si han pasado mas de 3 meses del vencto => se debe usar la cta de castigo
                -- sino se usa la cta CP
                select    Count(*)
                into      vl_count_fe_decl
                from      DECLARACION DE, DEUDA DA
                where      DA.COD_DEUDA = ve_cod_deuda
                and        DA.COD_DEUDOR = DE.COD_DEUDOR
                and        DE.ANO = ve_ano - 1
                and        DE.COD_ESTADO_DECLARACION = K_ESTADO_DECL_APRO;

                IF(vl_count_fe_decl = 0)THEN
                    vl_fecha_declaracion:= NULL;
                ELSE
                select    FECHA_DECLARACION
                  into    vl_fecha_declaracion
                  from    DECLARACION DE, DEUDA DA
                 where    DA.COD_DEUDA = ve_cod_deuda
                   and    DA.COD_DEUDOR = DE.COD_DEUDOR
                   and    DE.ANO = ve_ano - 1
                   and    DE.COD_ESTADO_DECLARACION = K_ESTADO_DECL_APRO
                AND  ROWNUM = 1;
                END IF;

                IF (vl_fecha_declaracion IS NOT NULL) THEN
                    IF (MONTHS_BETWEEN(vl_fecha_declaracion, vl_fecha_vencto) > 3) THEN
                        vl_cta_cp := f_comp_get_cuenta_tipo_deuda ('CS', K_CTA_CASTIGO);
                    END IF;
                END IF;*/

                vl_CTY := vl_monto_capital + vl_monto_interes;
                vl_dif := vl_CTY - vl_CAT;

                vl_monto :=  round(vl_dif * f_mon_get_valor_moneda(vl_cod_moneda, SYSDATE), 0);

                IF (vl_CTY > vl_CAT) THEN
                    spu_item_comprobante ('INSERT', NULL, vs_cod_comprobante1_new, vl_cta_cp, vl_monto, 0);
                    spu_analitico ('INSERT', NULL, vs_cod_comprobante1_new, ve_cod_deuda, vl_cta_cp, vl_monto, 0);

                    spu_item_comprobante ('INSERT', NULL, vs_cod_comprobante1_new, vl_cta_lp, 0, vl_monto);
                    spu_analitico ('INSERT', NULL, vs_cod_comprobante1_new, ve_cod_deuda, vl_cta_lp, 0, vl_monto);
                ELSE
                    vl_monto := - vl_monto;  --COMPROBAR QUE REALIZE BIEN EL CALCULO;
                    spu_item_comprobante ('INSERT', NULL, vs_cod_comprobante2_new, vl_cta_cp, 0, vl_monto);
                    spu_analitico ('INSERT', NULL, vs_cod_comprobante2_new, ve_cod_deuda, vl_cta_cp, 0, vl_monto);

                    spu_item_comprobante ('INSERT', NULL, vs_cod_comprobante2_new, vl_cta_lp, vl_monto, 0);
                    spu_analitico ('INSERT', NULL, vs_cod_comprobante2_new, ve_cod_deuda, vl_cta_lp, vl_monto, 0);
                END IF;
            else
                IF (ve_cod_ejecucion_proceso IS NOT NULL)THEN
                    spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, 'sp_deuda_cuota_cs Deuda: ' || TO_CHAR(ve_cod_deuda) || ' Cuota 99 tiene saldo negativo');
                END IF;
            END IF;
        ELSE
            IF (ve_cod_ejecucion_proceso IS NOT NULL)THEN
                spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, 'sp_deuda_cuota_cs Deuda: ' || TO_CHAR(ve_cod_deuda) || ' No Posee cuota 99');
            END IF;
        END IF;

    <<END_CALC_CUOTA>>  -- 7not allowed unless an executable statement follows
    NULL; -- add NULL statement to avoid error

    ------------------------------------------------------------------------------------------
    -- END MAIN
    ------------------------------------------------------------------------------------------
   END;
END sp_deuda_cuota_cs;
/

