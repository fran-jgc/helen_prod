PROMPT CREATE OR REPLACE PROCEDURE spp_genera_plan
CREATE OR REPLACE PROCEDURE spp_genera_plan(ve_cod_ejecucion_proceso  NUMBER
                                            ,ve_fecha_inicio_cobro    DATE)
AS
/******************************************************************************
   NAME:       spp_genera_plan
   Pasa una deuda de no exigible a exigible.
   Las deudas procesadas son las no exigibles que inician cobranza este año (o en fechas anteriores)
   cuya cartera sea la universad por defecto.
   En la deuda se actualiza el estado deuda, y se deja la fecha_inicio_cobro en 01/01/año_actual
   El capital SVP es el saldo al 31/12/año_anterior
   para las deudas CS solo se crea la cuota 9999 con K igual a saldo_svp y el interes del año completo
   las otras deudas se crea todas las cuotas de acuerdo a la regla de interes compuesto (equivalente a funcion PAGO excel)

   En el comprobante solo se contabliza el traspaso de K desde SVP a CP y LP
   El interes normal NO se contabiliza, este debe contabilizarse en el proceso de CAPITALIZACION
******************************************************************************/
BEGIN
  DECLARE
    K_DEUDA_NO_EXIGIBLE                    NUMBER := 1;
    K_PARAM_CARTERA_DEFECTO                NUMBER := 2;
    K_COMP_PENDIENTE                       NUMBER:=1;
    K_COMP_TRASPASO                        NUMBER:=3;
    K_COMP_GENERA_PLAN                     NUMBER:=14;

    K_CTA_SVP                               NUMBER:=1;
    K_CTA_CP                                NUMBER:=2;
    K_CTA_LP                                NUMBER:=3;

    CURSOR c_deuda(ve_fecha_saldo DATE) IS
    SELECT d.cod_deuda
          ,d.cant_cuota
          ,f_deuda_saldo_total(d.cod_deuda, ve_fecha_saldo) AS saldo_deuda
          ,d.cod_tipo_deuda
          ,t.cod_familia_deuda
          ,t.cod_moneda
    FROM deuda d, tipo_deuda t
    WHERE d.cod_estado_deuda = K_DEUDA_NO_EXIGIBLE
      AND d.fecha_inicio_cobro <= ve_fecha_inicio_cobro
      AND d.cod_cartera = f_get_parametro (K_PARAM_CARTERA_DEFECTO)
      AND t.cod_tipo_deuda = d.cod_tipo_deuda
      ORDER BY d.cod_tipo_deuda ASC;

    vl_cod_cuenta_svp           NUMBER;
    vl_cod_cuenta_cp            NUMBER;
    vl_cod_cuenta_lp            NUMBER;

    vl_cod_usuario              NUMBER;
    vl_cod_tipo_deuda           tipo_deuda.cod_tipo_deuda%TYPE;
    vl_fecha_31_12_ant          DATE;
    vl_valor_moneda_31_12_ant   NUMBER;
    vl_cod_comprobante          NUMBER;
    vl_monto_svp                NUMBER;
    vl_monto_cp                 NUMBER;
    vl_monto_lp                 NUMBER;
    vl_cuota_vigente            NUMBER;
    vl_nro_cuota_vigente        NUMBER;
    vl_count                    NUMBER;
    i                           NUMBER;
    vl_nom_comprobante          comprobante.nom_comprobante%TYPE;
    vl_cod_deuda_err            number := 0;
    vl_count_cant_cuota_pp      NUMBER := 0;


  BEGIN
    vl_fecha_31_12_ant := To_Date('31/12/' || To_Char(To_Number(To_Char(ve_fecha_inicio_cobro, 'yyyy')) - 1), 'dd/mm/yyyy') + 1 - 1/86400 ;

    -- usuario que ejecuta el proceso
    SELECT cod_usuario
    INTO vl_cod_usuario
    FROM ejecucion_proceso
    WHERE cod_ejecucion_proceso = ve_cod_ejecucion_proceso;

    -- IMPORTANTE: El where de este select debe ser igual al del cursor !!
    -- calcula el total de registros
    SELECT Count(*)
    INTO vl_count
    FROM deuda d, tipo_deuda t
    WHERE d.cod_estado_deuda = K_DEUDA_NO_EXIGIBLE
      AND d.fecha_inicio_cobro <= ve_fecha_inicio_cobro
      AND d.cod_cartera = f_get_parametro (K_PARAM_CARTERA_DEFECTO)
      AND t.cod_tipo_deuda = d.cod_tipo_deuda;

    spu_ejecucion_proceso('SET_TOTAL', ve_cod_ejecucion_proceso, vl_count);

    i := 1;
    vl_cod_tipo_deuda := '__NO_EXISTE__';
    --vl_cod_tipo_deuda := '';
    FOR a IN c_deuda(vl_fecha_31_12_ant) LOOP
      vl_cod_deuda_err := a.cod_deuda;

      -- verifica si el proceso ha sido cancelado
      IF (f_ep_cancela_proceso(ve_cod_ejecucion_proceso) = 'S') THEN
        sp_comp_simplifica(vl_cod_comprobante);
        COMMIT;
        RETURN;
      END IF;

      ------------------------------------------------------------------------------------------
      -- MAIN
      ------------------------------------------------------------------------------------------
      -- crea un nuevo comprobante si corresponde
      IF (vl_cod_tipo_deuda <> a.cod_tipo_deuda) THEN
        sp_comp_simplifica(vl_cod_comprobante);   -- Borra cuentas duplicadas en comprobante anterior

        vl_nom_comprobante := 'Genera Plan Pago: ' || a.cod_tipo_deuda;
        spu_comprobante('INSERT', NULL, vl_fecha_31_12_ant, vl_cod_usuario, vl_nom_comprobante, a.cod_familia_deuda, K_COMP_PENDIENTE, K_COMP_TRASPASO, K_COMP_GENERA_PLAN, vl_cod_usuario, ve_fecha_inicio_cobro);
        SELECT comprobante_sq.currval INTO vl_cod_comprobante FROM dual;
        vl_cod_tipo_deuda := a.cod_tipo_deuda;

        vl_valor_moneda_31_12_ant := f_mon_get_valor_moneda(a.cod_moneda, vl_fecha_31_12_ant);
        vl_cod_cuenta_svp := f_comp_get_cuenta_tipo_deuda (a.cod_tipo_deuda, K_CTA_SVP);
        vl_cod_cuenta_cp := f_comp_get_cuenta_tipo_deuda (a.cod_tipo_deuda, K_CTA_CP);
        vl_cod_cuenta_lp := f_comp_get_cuenta_tipo_deuda (a.cod_tipo_deuda, K_CTA_LP);
      END IF;

      IF (a.saldo_deuda <= 0) THEN
        spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, 'Deuda: ' || To_Char(a.cod_deuda) || ' tiene saldo menor o igual a cero.');
        GOTO end_loop;
      END IF;

      SELECT  Count(*)
      INTO    vl_count_cant_cuota_pp
      FROM    plan_pago
      WHERE   cod_deuda = a.cod_deuda;

      IF (vl_count_cant_cuota_pp > 0) THEN
        spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, 'Deuda: ' || To_Char(a.cod_deuda) || ' posee estado No Exigible, no debe tener cuotas');
        GOTO end_loop;
      END IF;

      vl_monto_svp := Round(a.saldo_deuda * vl_valor_moneda_31_12_ant, 0);
      spu_item_comprobante('INSERT', NULL, vl_cod_comprobante, vl_cod_cuenta_svp, 0, vl_monto_svp);
      spu_analitico ('INSERT', NULL, vl_cod_comprobante, a.cod_deuda, vl_cod_cuenta_svp, 0, vl_monto_svp);

      -- Actualiza la fecha inicio cobro al inicio del año sysadte
      UPDATE deuda
      SET fecha_inicio_cobro = To_Date('01/01/' || To_Char(ve_fecha_inicio_cobro, 'yyyy'),'dd/mm/yyyy')
      WHERE cod_deuda = a.cod_deuda;

      sp_deuda_genera_plan(a.cod_deuda, vl_cod_usuario);

      -- obtiene saldo CP y LP
      vl_monto_cp := 0;
      vl_monto_lp := 0;
      vl_cuota_vigente := f_deuda_cuota_vigente(a.cod_deuda);
      IF (vl_cuota_vigente IS NULL) THEN
        SELECT Sum(monto_capital)
        INTO vl_monto_lp
        FROM plan_pago
        WHERE cod_deuda = a.cod_deuda;
      ELSE
        SELECT nro_cuota
              ,monto_capital
        INTO vl_nro_cuota_vigente
            ,vl_monto_cp
        FROM plan_pago
        WHERE cod_plan_pago = vl_cuota_vigente;

        SELECT Nvl(Sum(monto_capital), 0)
        INTO vl_monto_lp
        FROM plan_pago
        WHERE cod_deuda = a.cod_deuda
          AND nro_cuota > vl_nro_cuota_vigente;
      END IF;

      -- lleva los montos a $
      vl_monto_cp := Round(vl_monto_cp * vl_valor_moneda_31_12_ant, 0);
      vl_monto_lp := Round(vl_monto_lp * vl_valor_moneda_31_12_ant, 0);

      -- Por los redondeos puede quedar descuadrado el comprobante
      IF (vl_monto_svp <> vl_monto_cp + vl_monto_lp) THEN
        IF (vl_monto_cp <> 0) THEN
          vl_monto_cp := vl_monto_cp + (vl_monto_svp - (vl_monto_cp + vl_monto_lp));
        ELSE
          vl_monto_lp := vl_monto_lp + (vl_monto_svp - (vl_monto_cp + vl_monto_lp));
        END IF;
      END IF;

      spu_item_comprobante('INSERT', NULL, vl_cod_comprobante, vl_cod_cuenta_cp, vl_monto_cp, 0);
      spu_analitico ('INSERT', NULL, vl_cod_comprobante, a.cod_deuda, vl_cod_cuenta_cp, vl_monto_cp, 0);

      spu_item_comprobante('INSERT', NULL, vl_cod_comprobante, vl_cod_cuenta_lp, vl_monto_lp, 0);
      spu_analitico ('INSERT', NULL, vl_cod_comprobante, a.cod_deuda, vl_cod_cuenta_lp, vl_monto_lp, 0);
      COMMIT;

      <<end_loop>>  -- 7not allowed unless an executable statement follows
      NULL; -- add NULL statement to avoid error

      -- actualiza avance
      spu_ejecucion_proceso('SET_ACTUAL', ve_cod_ejecucion_proceso, i);
      i := i + 1;
      -- end MAIN
      ------------------------------------------------------------------------------------------
    END LOOP;
    sp_comp_simplifica(vl_cod_comprobante);
  	spu_ejecucion_proceso('END', ve_cod_ejecucion_proceso, i-1);
    COMMIT;

  EXCEPTION
    WHEN OTHERS THEN
    -- Si ocurre un error de la BD al grabar un registro lo registra en el LOG. del sistema
    spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', SQLCODE, SUBSTR(SQLERRM,1,100), 'Deuda: ' || To_Char(vl_cod_deuda_err));
    COMMIT;
  END;
END spp_genera_plan;
/

