PROMPT CREATE OR REPLACE FUNCTION f_deuda_saldo_componente
CREATE OR REPLACE FUNCTION f_deuda_saldo_componente(
  ve_cod_deuda        NUMERIC
  ,ve_cod_componente  NUMERIC
  ,ve_fecha           DATE := SYSDATE
)
  RETURN NUMBER
AS
/******************************************************************************
   NAME:       f_deuda_saldo_componente

   REVISIONS:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        24/10/2009   Victor Melo

   NOTES:
******************************************************************************/
BEGIN
  DECLARE
    K_NO_EXIGIBLE             NUMBER := 1;
    K_COMP_NO_EXIGIBLE        NUMBER := 1;
    K_COMP_VENCIDA            NUMBER := 2;
    K_COMP_VIGENTE            NUMBER := 3;
    K_COMP_FUTURA             NUMBER := 4;
    K_VIGENTE                 NUMBER := 1;
    K_DEUDA_NO_EXIGIBLE       NUMBER := 1;
    K_DEUDA_EXIGIBLE          NUMBER := 2;
    K_UNIVERSIDAD_DEFECTO     NUMBER := 3;
    vl_cod_estado_deuda       NUMBER;
    vl_tasa                   NUMBER(14,4);
    vl_monto_pagare           NUMBER(14,4);
    vl_monto_abono            NUMBER(14,4);
    vl_monto_dscto            NUMBER(14,4);
    vl_saldo                  NUMBER(14,4);
    vl_monto_otras_ues        NUMBER(14,4);
    vl_fecha_act              DATE;
    vl_cod_plan_pago_vigente  NUMBER;
    vl_nro_cuota              NUMBER;
  BEGIN
    vl_cod_estado_deuda := f_deuda_get_estado (ve_cod_deuda, ve_fecha);

    IF (vl_cod_estado_deuda <> K_DEUDA_NO_EXIGIBLE
          AND vl_cod_estado_deuda <> K_DEUDA_EXIGIBLE) THEN
      RETURN 0;
    END IF;

    IF (ve_cod_componente = K_NO_EXIGIBLE) THEN
      IF (vl_cod_estado_deuda = K_NO_EXIGIBLE) THEN
        -- obtiene la tasa de interes
        SELECT  1 + (tasa_interes_credito / 100)
        INTO    vl_tasa
        FROM    tipo_deuda td
                ,deuda d
        WHERE   d.cod_deuda = ve_cod_deuda
                AND td.cod_tipo_deuda = d.cod_tipo_deuda;

        -- Se calcula la actualizacion hasta fines año, es decir se tratan de cobrar los intereses de todo el año
        vl_fecha_act := TO_DATE ('31/12/' || To_Char(ve_fecha, 'yyyy'), 'dd/mm/yyyy');

        SELECT  Nvl(Sum((monto_moneda - Nvl(monto_moneda_rebaja,0)) * Power(vl_tasa, (vl_fecha_act - fecha_pagare) / 365)), 0)
        INTO    vl_monto_pagare
        FROM    pagare
        WHERE   cod_deuda = ve_cod_deuda;

        SELECT  Nvl(Sum(monto_moneda * Power(vl_tasa, (vl_fecha_act - fecha_abono) / 365)), 0)
        INTO    vl_monto_abono
        FROM    abono
        WHERE   cod_deuda = ve_cod_deuda
                AND cod_componente_deuda = K_NO_EXIGIBLE
				and fecha_abono < vl_fecha_act
                AND cod_estado_pago = K_VIGENTE;

        SELECT  Nvl(Sum(monto_moneda * Power(vl_tasa, (vl_fecha_act - fecha_descuento) / 365)), 0)
        INTO    vl_monto_dscto
        FROM    descuento
        WHERE   cod_deuda = ve_cod_deuda
                AND cod_componente_deuda = K_NO_EXIGIBLE
				and fecha_descuento < vl_fecha_act
                AND cod_estado_pago = K_VIGENTE;

        SELECT  Nvl(Sum(monto_actualizado * Power(vl_tasa, (vl_fecha_act - fecha_consolidacion) / 365)), 0)
        INTO    vl_monto_otras_ues
        FROM    cruzado
        WHERE   cod_deuda = ve_cod_deuda
                AND cod_universidad <> f_get_parametro(K_UNIVERSIDAD_DEFECTO);

        RETURN Round(vl_monto_pagare + vl_monto_otras_ues - vl_monto_abono - vl_monto_dscto, 4);
      ELSE
        RETURN 0;
      END IF;
    ELSIF (ve_cod_componente = K_COMP_VENCIDA) THEN
      SELECT  Nvl(Sum(f_pp_saldo_mora (cod_plan_pago, ve_fecha) + f_pp_saldo_multa (cod_plan_pago, ve_fecha)), 0)
      INTO    vl_saldo
      FROM    plan_pago
      WHERE   cod_deuda = ve_cod_deuda;

      RETURN vl_saldo;
    ELSIF (ve_cod_componente = K_COMP_VIGENTE) THEN
      vl_cod_plan_pago_vigente := f_deuda_cuota_vigente (ve_cod_deuda);

      IF (vl_cod_plan_pago_vigente IS NULL) THEN
        RETURN 0;
      END IF;

      SELECT  Nvl(f_pp_saldo_capital (cod_plan_pago, ve_fecha) + f_pp_saldo_interes_normal (cod_plan_pago, ve_fecha), 0)
      INTO    vl_saldo
      FROM    plan_pago
      WHERE   cod_plan_pago = vl_cod_plan_pago_vigente;

      RETURN vl_saldo;
    ELSIF (ve_cod_componente = K_COMP_FUTURA) THEN
      vl_cod_plan_pago_vigente             := f_deuda_cuota_vigente (ve_cod_deuda);

      IF (vl_cod_plan_pago_vigente IS NULL) THEN
        -- para el CS se puede dar que tenga cuota futura pero que no tenga vigente
        SELECT  Nvl(Sum(f_pp_saldo_capital (cod_plan_pago, ve_fecha) + f_pp_saldo_interes_normal (cod_plan_pago, ve_fecha)), 0)
        INTO    vl_saldo
        FROM    plan_pago
        WHERE   cod_deuda = ve_cod_deuda
                AND fecha_vencto > ve_fecha;
      ELSE
        SELECT  nro_cuota
        INTO    vl_nro_cuota
        FROM    plan_pago
        WHERE   cod_plan_pago = vl_cod_plan_pago_vigente;

        SELECT  Nvl(Sum(f_pp_saldo_capital (cod_plan_pago, ve_fecha) + f_pp_saldo_interes_normal (cod_plan_pago, ve_fecha)), 0)
        INTO    vl_saldo
        FROM    plan_pago
        WHERE   cod_deuda = ve_cod_deuda
                AND nro_cuota > vl_nro_cuota;
      END IF;

      RETURN vl_saldo;
    END IF;
  END;
END f_deuda_saldo_componente;
/

