PROMPT CREATE OR REPLACE FUNCTION f_pp_saldo_plan_pago_flujo
CREATE OR REPLACE FUNCTION f_pp_saldo_plan_pago_flujo (ve_cod_deuda                   NUMBER
                                                      ,ve_ano                         NUMBER
                                                      ,ve_fecha                       DATE
                                                      ,ve_cod_tipo_deuda              VARCHAR2
                                                      ,ve_ano_flujo                   number
)
   RETURN plan_pago.monto_capital%TYPE
AS
/*
para las deudas CR12 que no esten pagadas no debe considerar el dscto
*/
BEGIN
DECLARE	vl_saldo_pp                           NUMBER;
        vl_dscto_CR12                         NUMBER;
        vl_count                              NUMBER;
        vl_cod_plan_pago                      NUMBER;
        vl_ab_pp                              NUMBER;
        vl_dscto_pp                           NUMBER;
        vl_ab_ficticio                        NUMBER;
        vl_dscto_ficticio                     NUMBER;
        vl_cod_prorroga                       NUMBER;
        vl_cod_suspension                     NUMBER;

BEGIN
  SELECT Count(*)
  INTO vl_count
  FROM plan_pago
  WHERE cod_deuda = ve_cod_deuda
    AND ano_cobro = ve_ano;

  IF (vl_count = 0) THEN
    SELECT Count(*)
    INTO vl_count
    FROM prorroga p
    WHERE p.COD_ESTADO_PRORROGA = 2
      AND p.COD_DEUDA = ve_cod_deuda
      AND To_number(To_Char(p.FECHA_SOLICITUD_TRAMITE,'yyyy')) > ve_ano_flujo
      AND p.cod_prorroga IN (SELECT cod_origen FROM plan_pago_h WHERE origen = 'PRORROGA');

   IF (vl_count = 0) THEN
    RETURN 0;
   ELSE
    SELECT cod_prorroga
    INTO vl_cod_prorroga
    FROM prorroga p
    WHERE p.COD_ESTADO_PRORROGA = 2
      AND p.COD_DEUDA = ve_cod_deuda
      AND To_number(To_Char(p.FECHA_SOLICITUD_TRAMITE,'yyyy')) > ve_ano_flujo
      AND p.cod_prorroga IN (SELECT cod_origen FROM plan_pago_h WHERE origen = 'PRORROGA')
      AND ROWNUM = 1;

    SELECT Nvl(Sum(monto_capital + monto_interes), 0)
    INTO vl_saldo_pp
    FROM plan_pago_h
    WHERE cod_deuda = ve_cod_deuda
      AND ano_cobro = ve_ano
      AND cod_origen = vl_cod_prorroga
      AND origen = 'PRORROGA';

    SELECT Nvl(Sum(a.monto_capital + a.monto_interes), 0)
    INTO vl_ab_pp
    FROM plan_pago p, abono_h a
    WHERE p.cod_deuda = ve_cod_deuda
      AND p.ano_cobro = ve_ano
      AND a.cod_plan_pago = p.cod_plan_pago
      AND a.cod_estado_pago = 1
      AND a.fecha_abono <= ve_fecha
      AND cod_origen = vl_cod_prorroga
      AND origen = 'PRORROGA';

    SELECT Nvl(Sum(d.monto_capital + d.monto_interes), 0)
    INTO vl_dscto_pp
    FROM plan_pago p, descuento_h d
    WHERE p.cod_deuda = ve_cod_deuda
      AND p.ano_cobro = ve_ano
      AND d.cod_plan_pago = p.cod_plan_pago
      AND d.cod_estado_pago = 1
      AND d.fecha_descuento <= ve_fecha
      AND cod_origen = vl_cod_prorroga
      AND origen = 'PRORROGA';

      --para las prorrogas no queda registro de ficticio en _h
   END IF;

    RETURN vl_saldo_pp - vl_ab_pp - vl_dscto_pp;
  -------------------------
  ELSIF (vl_count = 1) THEN
    --si la cuota fue suspendida desdepues del año_flijo se debe considerar lo que esta en plan_pago_h
    SELECT Count(*)
    INTO vl_count
    FROM suspension s
    WHERE s.COD_ESTADO_SUSPENSION= 2
      AND s.COD_DEUDA = ve_cod_deuda
      AND s.ANO_SUSPENSION = ve_ano
      AND To_number(To_Char(s.FECHA_CONFIRMA,'yyyy')) > ve_ano_flujo
      AND s.cod_suspension IN (SELECT p.cod_origen FROM plan_pago_h p WHERE p.origen = 'SUSPENSION' AND p.ano_cobro = ve_ano AND p.cod_deuda = s.cod_deuda);

    IF (vl_count > 0) THEN
      SELECT cod_suspension
      INTO vl_cod_suspension
      FROM suspension s
      WHERE s.COD_ESTADO_SUSPENSION = 2
        AND s.COD_DEUDA = ve_cod_deuda
        AND s.ANO_SUSPENSION = ve_ano
        AND To_number(To_Char(s.FECHA_CONFIRMA,'yyyy')) > ve_ano_flujo
        AND s.cod_suspension IN (SELECT p.cod_origen FROM plan_pago_h p WHERE p.origen = 'SUSPENSION' AND p.ano_cobro = ve_ano AND p.cod_deuda = s.cod_deuda)
        AND ROWNUM = 1;

      SELECT Nvl(Sum(monto_capital + monto_interes), 0)
      INTO vl_saldo_pp
      FROM plan_pago_h
      WHERE cod_deuda = ve_cod_deuda
        AND ano_cobro = ve_ano
        AND cod_origen = vl_cod_suspension
        AND origen = 'SUSPENSION';

      SELECT Nvl(Sum(a.monto_capital + a.monto_interes), 0)
      INTO vl_ab_pp
      FROM plan_pago p, abono_h a
      WHERE p.cod_deuda = ve_cod_deuda
        AND p.ano_cobro = ve_ano
        AND a.cod_plan_pago = p.cod_plan_pago
        AND a.cod_estado_pago = 1
        AND a.fecha_abono <= ve_fecha
        AND cod_origen = vl_cod_suspension
        AND origen = 'SUSPENSION';

      SELECT Nvl(Sum(d.monto_capital + d.monto_interes), 0)
      INTO vl_dscto_pp
      FROM plan_pago p, descuento_h d
      WHERE p.cod_deuda = ve_cod_deuda
        AND p.ano_cobro = ve_ano
        AND d.cod_plan_pago = p.cod_plan_pago
        AND d.cod_estado_pago = 1
        AND d.fecha_descuento <= ve_fecha
        AND cod_origen = vl_cod_suspension
        AND origen = 'SUSPENSION';

        --para las prorrogas no queda registro de ficticio en _h
      RETURN vl_saldo_pp - vl_ab_pp - vl_dscto_pp;
    END IF;
    -----fin caso suspension

    SELECT cod_plan_pago
    INTO vl_cod_plan_pago
    FROM plan_pago
    WHERE cod_deuda = ve_cod_deuda
      AND ano_cobro = ve_ano;

    SELECT monto_capital + monto_interes
    INTO vl_saldo_pp
    FROM plan_pago
    WHERE cod_plan_pago = vl_cod_plan_pago;

    SELECT Nvl(Sum(monto_capital + monto_interes), 0)
    INTO vl_ab_pp
    FROM abono
    WHERE cod_plan_pago = vl_cod_plan_pago
      AND cod_estado_pago = 1
      AND fecha_abono <= ve_fecha;

    SELECT Nvl(Sum(monto_capital + monto_interes), 0)
    INTO vl_dscto_pp
    FROM descuento
    WHERE cod_plan_pago = vl_cod_plan_pago
      AND cod_estado_pago = 1
      AND fecha_descuento <= ve_fecha;

    select Nvl(Sum(MONTO_CAPITAL + MONTO_INTERES), 0)
    INTO vl_ab_ficticio
	  from abono_ficticio
	  where COD_PLAN_PAGO = vl_cod_plan_pago
      and FECHA_ABONO <= ve_fecha;

    select Nvl(Sum(MONTO_CAPITAL + MONTO_INTERES), 0)
    INTO vl_dscto_ficticio
	  from descuento_ficticio
	  where COD_PLAN_PAGO = vl_cod_plan_pago
      and FECHA_DESCUENTO <= ve_fecha;
  ELSE
    SELECT Nvl(Sum(monto_capital + monto_interes), 0)
    INTO vl_saldo_pp
    FROM plan_pago
    WHERE cod_deuda = ve_cod_deuda
      AND ano_cobro = ve_ano;

    SELECT Nvl(Sum(a.monto_capital + a.monto_interes), 0)
    INTO vl_ab_pp
    FROM plan_pago p, abono a
    WHERE p.cod_deuda = ve_cod_deuda
      AND p.ano_cobro = ve_ano
      AND a.cod_plan_pago = p.cod_plan_pago
      AND a.cod_estado_pago = 1
      AND a.fecha_abono <= ve_fecha;

    SELECT Nvl(Sum(d.monto_capital + d.monto_interes), 0)
    INTO vl_dscto_pp
    FROM plan_pago p, descuento d
    WHERE p.cod_deuda = ve_cod_deuda
      AND p.ano_cobro = ve_ano
      AND d.cod_plan_pago = p.cod_plan_pago
      AND d.cod_estado_pago = 1
      AND d.fecha_descuento <= ve_fecha;

    select Nvl(Sum(a.MONTO_CAPITAL + a.MONTO_INTERES), 0)
    INTO vl_ab_ficticio
    FROM plan_pago p, abono_ficticio a
    WHERE p.cod_deuda = ve_cod_deuda
      AND p.ano_cobro = ve_ano
	    AND a.COD_PLAN_PAGO = vl_cod_plan_pago
      and a.FECHA_ABONO <= ve_fecha;

    select Nvl(Sum(a.MONTO_CAPITAL + a.MONTO_INTERES), 0)
    INTO vl_dscto_ficticio
    FROM plan_pago p, descuento_ficticio a
    WHERE p.cod_deuda = ve_cod_deuda
      AND p.ano_cobro = ve_ano
	    AND a.COD_PLAN_PAGO = vl_cod_plan_pago
      and a.FECHA_DESCUENTO <= ve_fecha;
  END IF;

  vl_saldo_pp := vl_saldo_pp - vl_ab_pp - vl_dscto_pp - vl_ab_ficticio - vl_dscto_ficticio;

  IF (ve_cod_tipo_deuda = 'CR12' and vl_saldo_pp > 0) THEN
    vl_dscto_CR12 := 0;

    SELECT Nvl(Sum(d.monto_moneda), 0)
    INTO vl_dscto_CR12
    FROM plan_pago p, descuento d
    WHERE p.cod_deuda = ve_cod_deuda
      AND p.ano_cobro = ve_ano
      AND d.cod_plan_pago = p.cod_plan_pago
      AND d.cod_estado_pago = 1
      AND d.cod_origen_pago = 22;

    vl_saldo_pp := vl_saldo_pp + vl_dscto_CR12;
  END IF;

  RETURN vl_saldo_pp;
END;
END;
/

