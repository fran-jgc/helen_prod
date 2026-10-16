PROMPT CREATE OR REPLACE FUNCTION f_deuda_concepto_cs
CREATE OR REPLACE FUNCTION f_deuda_concepto_cs(ve_cod_deuda NUMBER, ve_ano NUMBER)
RETURN NUMBER
AS
BEGIN
  DECLARE
    K_ESTADO_DECL_APRO              NUMBER:=2;
    K_ESTADO_CIVIL_SOLTERO          NUMBER:=1;
    K_ESTADO_CIVIL_CASADO           NUMBER:=2;
    K_CONCEPTO_COBRO_5PORC          NUMBER:=1;
    K_CONCEPTO_COBRO_0              NUMBER:=2;
    K_CONCEPTO_COBRO_35             NUMBER:=3;
    K_CONCEPTO_COBRO_20             NUMBER:=4;
    K_CONCEPTO_COBRO_DPA            NUMBER:=5;

    vs_cod_concepto_cobro           NUMBER;
    vl_count_declaracion            NUMBER;
    vl_cod_estado_civil             number;
    vl_cod_deudor                   NUMBER;
    vl_cod_declaracion              NUMBER;
    vl_con_hijo                     VARCHAR(1);
    vl_conyuge_es_deudor            VARCHAR(1);
    vl_conyuge_declara              VARCHAR(1);
    vl_monto_anual_deudor_moneda    NUMBER;
    vl_monto_anual_conyuge_moneda   NUMBER;
    vl_promedio_anual_deudor        NUMBER;
    vl_promedio_anual_conyuge       NUMBER;
    vl_promedio_anual_conjunto      NUMBER;
    vl_cuota99                      NUMBER;
    vl_cod_plan_pago99              NUMBER;
    vl_20_porc                      NUMBER;
    vl_count                        NUMBER;
    vl_pago_anterior                NUMBER;

  BEGIN
    SELECT  cod_deudor
    INTO    vl_cod_deudor
    FROM    deuda
    WHERE   cod_deuda = ve_cod_deuda;

    SELECT  Count(*)
    INTO    vl_count_declaracion
    FROM    declaracion
    WHERE   cod_deudor = vl_cod_deudor
      AND   ANO= ve_ano - 1
      AND   cod_estado_declaracion = K_ESTADO_DECL_APRO;

    IF (vl_count_declaracion > 1) THEN
      vl_count_declaracion := 0;
    end IF;


    IF (vl_count_declaracion = 1) THEN
      SELECT  cod_declaracion
              ,Nvl (con_hijo, 'N')
              ,conyuge_es_deudor
              ,conyuge_declara
              ,cod_estado_civil
      INTO    vl_cod_declaracion
              ,vl_con_hijo
              ,vl_conyuge_es_deudor
              ,vl_conyuge_declara
              ,vl_cod_estado_civil
      FROM    declaracion
      WHERE   ano = ve_ano - 1
              AND cod_deudor = vl_cod_deudor
              AND cod_estado_declaracion = K_ESTADO_DECL_APRO;

      SELECT  Nvl(Sum(monto_deudor_moneda),0)
             ,Nvl(Sum(monto_conyuge_moneda),0)
      INTO    vl_monto_anual_deudor_moneda
             ,vl_monto_anual_conyuge_moneda
      FROM    declaracion_mes
      WHERE   cod_declaracion = vl_cod_declaracion;

      vl_promedio_anual_deudor    := vl_monto_anual_deudor_moneda / 12;
      vl_promedio_anual_conyuge   := vl_monto_anual_conyuge_moneda / 12;
      vl_promedio_anual_conjunto  := vl_promedio_anual_deudor + vl_promedio_anual_conyuge;

      IF (vl_cod_estado_civil = K_ESTADO_CIVIL_SOLTERO) THEN
        IF (vl_con_hijo = 'S') THEN
          IF (vl_promedio_anual_deudor >= 7) THEN
            --Concepto Cobro 5%
            vs_cod_concepto_cobro := K_CONCEPTO_COBRO_5PORC;
          ELSE
            --Exento de Pago
            vs_cod_concepto_cobro := K_CONCEPTO_COBRO_0;
          END IF;
        ELSE -- (vl_con_hijo = 'N')
          IF (vl_promedio_anual_deudor >= 6) THEN
            --Exento de Pago
            vs_cod_concepto_cobro := K_CONCEPTO_COBRO_5PORC;
          ELSE
            --Concepto Cobro 5%
            vs_cod_concepto_cobro := K_CONCEPTO_COBRO_0;
          END IF;
        END IF;
      ELSIF (vl_cod_estado_civil = K_ESTADO_CIVIL_CASADO) THEN
        IF (vl_conyuge_es_deudor = 'S') THEN
          IF (vl_conyuge_declara ='S') THEN
            IF (vl_promedio_anual_conjunto < 8) THEN
              --Exento de Pago
              vs_cod_concepto_cobro := K_CONCEPTO_COBRO_0;
            ELSE
              IF (vl_promedio_anual_deudor >= 6) THEN
                --Concepto Cobro 5%
                vs_cod_concepto_cobro := K_CONCEPTO_COBRO_5PORC;
              ELSE
                IF (vl_promedio_anual_conjunto > 16) THEN
                  --3,5 UTM
                  vs_cod_concepto_cobro := K_CONCEPTO_COBRO_35;
                ELSE
                  --Exento de Pago
                  vs_cod_concepto_cobro := K_CONCEPTO_COBRO_0;
                END IF;
              END IF;
            END IF;
          ELSE -- (vl_conyuge_declara ='N')
            IF (vl_promedio_anual_deudor >= 6) THEN
              --Concepto Cobro 5%
              vs_cod_concepto_cobro := K_CONCEPTO_COBRO_5PORC;
            ELSE
              --3,5 UTM
              vs_cod_concepto_cobro := K_CONCEPTO_COBRO_35;
            END IF;
          END IF;
        ELSE -- (vl_conyuge_es_deudor = 'N')
          IF (vl_conyuge_declara ='S') THEN
            IF (vl_promedio_anual_conjunto < 8) THEN
              --Exento de Pago
              vs_cod_concepto_cobro := K_CONCEPTO_COBRO_0;
            ELSE
              IF (vl_promedio_anual_deudor >= 6) THEN
                --Concepto Cobro 5%
                vs_cod_concepto_cobro := K_CONCEPTO_COBRO_5PORC;
              ELSE
                --3,5 UTM
                vs_cod_concepto_cobro := K_CONCEPTO_COBRO_35;
              END IF;
            END IF;
          ELSE -- (vl_conyuge_declara ='N')
            IF (vl_promedio_anual_deudor >= 6) THEN
              --Concepto Cobro 5%
              vs_cod_concepto_cobro := K_CONCEPTO_COBRO_5PORC;
            ELSE
              --3,5 UTM
              vs_cod_concepto_cobro := K_CONCEPTO_COBRO_35;
            END IF;
          END IF;
        END IF;
      END IF;
    ELSIF (vl_count_declaracion = 0) THEN--NO POSEE DECLARACIONES PARA EL AÑO ANTERIOR
      SELECT  count(*)
      INTO    vl_count
      FROM    plan_pago
      WHERE   cod_deuda = ve_cod_deuda
              AND ano_cobro = 9999;

      IF(vl_count = 0)THEN
        vs_cod_concepto_cobro := K_CONCEPTO_COBRO_20; /*PARA EL TIPO DE CASO CSR APARENTEMENTE NO EXISTE CUOTA 9999*/
      ELSE

        SELECT  cod_plan_pago
        INTO    vl_cod_plan_pago99
        FROM    plan_pago
        WHERE   cod_deuda = ve_cod_deuda
                AND ano_cobro = 9999;


        vl_cuota99 := f_pp_saldo_plan_pago (vl_cod_plan_pago99,sysdate);
        vl_20_porc := ROUND (vl_cuota99 * 0.2, 4);

        SELECT Count(*)
        INTO vl_count
        FROM plan_pago
        WHERE cod_deuda = ve_cod_deuda
          AND ano_cobro = ve_ano -1;

        IF (vl_count=0) THEN
          vl_pago_anterior := 0;
        ELSE
          SELECT  Nvl(f_pp_monto_abono(cod_plan_pago, 'MONEDA', fecha_vencto), 0)
          INTO    vl_pago_anterior
          FROM    plan_pago
          WHERE   cod_deuda = ve_cod_deuda
                  AND ano_cobro = ve_ano -1;
        END IF;

        IF (vl_20_porc >= (2 * vl_pago_anterior)) THEN
          vs_cod_concepto_cobro := K_CONCEPTO_COBRO_20;
        ELSE
          vs_cod_concepto_cobro := K_CONCEPTO_COBRO_DPA;
        END IF;
      END IF;

    END IF;

    RETURN vs_cod_concepto_cobro;
  END;
END;
/

