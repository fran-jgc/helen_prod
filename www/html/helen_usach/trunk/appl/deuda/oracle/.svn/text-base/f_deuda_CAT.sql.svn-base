create or replace FUNCTION       f_deuda_cat(ve_cod_deuda NUMBER, ve_ano NUMBER)

RETURN NUMBER

AS

vl_monto_inicio_cobro NUMBER;
vl_cant_cuota         NUMBER;
vl_fecha_inicio_cobro DATE;
vl_cod_tipo_deuda     VARCHAR(20);
vs_CAT                NUMBER;
vl_periodo            NUMBER;
vl_max_pp             NUMBER;
vl_count_99           NUMBER;
vl_monto_ano           NUMBER;
vl_count              NUMBER;
vl_saldo_99           NUMBER;

BEGIN

  SELECT  monto_inicio_cobro
          ,Nvl(cant_cuota, 0)
          ,fecha_inicio_cobro
          ,cod_tipo_deuda
  INTO    vl_monto_inicio_cobro
          ,vl_cant_cuota
          ,vl_fecha_inicio_cobro
          ,vl_cod_tipo_deuda
  FROM    deuda
  WHERE   cod_deuda = ve_cod_deuda;

  IF (vl_cod_tipo_deuda <> 'CS') THEN
    RETURN NULL;
  END IF;

  SELECT Nvl(Max(ano_cobro), 0)
  INTO vl_max_pp
  FROM plan_pago
  WHERE cod_deuda = ve_cod_deuda
    AND ano_cobro < 9999;

  SELECT Count(*)
  INTO vl_count
  FROM PLAN_PAGO
  WHERE COD_DEUDA = ve_cod_deuda
  AND   ANO_COBRO = 9999;

  IF (vl_count > 0) then
    SELECT f_pp_saldo_plan_pago(cod_plan_pago, To_Date('31/12/' || To_Char(ve_ano),'dd/mm/yyyy') + 1 - 1/86400)
    INTO vl_saldo_99
    FROM PLAN_PAGO
    WHERE COD_DEUDA = ve_cod_deuda
    AND   ANO_COBRO = 9999
    AND ROWNUM = 1;
  ELSE
    vl_saldo_99 := 1; --se fuerza para que no entre al sgte if, y asi evitar que retorne cero
  END IF;

  IF (vl_max_pp < ve_ano AND vl_saldo_99 <= 0) then
    RETURN 0;
  END IF;


  IF (vl_cant_cuota = 0) THEN
    IF (vl_monto_inicio_cobro < 200) THEN
      vl_cant_cuota := 12;
    ELSE
      vl_cant_cuota := 15;
    END IF;
  END IF;

  vs_CAT      := vl_monto_inicio_cobro / vl_cant_cuota;
  vl_periodo  := ve_ano - To_Number(To_Char(vl_fecha_inicio_cobro,'yyyy')) + 1;

  IF (vl_periodo < 0 ) THEN
    vl_periodo := 0;
  END IF;

  vs_CAT      := vs_CAT * 1.02**vl_periodo;

  -- inicio: calculo de cat cuando la ultima cuota distinta a la 99 es menor a la cat prefijada 22-04-2015 IS
/*
  SELECT Count(*)
  INTO vl_count_99
  FROM PLAN_PAGO
  WHERE COD_DEUDA = ve_cod_deuda
  AND   ANO_COBRO = 9999;

  IF (vl_count_99>0) AND (vl_max_pp = ve_ano) THEN
    SELECT monto_capital+monto_interes
    INTO vl_monto_ano
    FROM PLAN_PAGO
    WHERE COD_DEUDA = ve_cod_deuda
    AND   ANO_COBRO = ve_ano;

    IF (vl_monto_ano < vs_CAT) AND (vl_monto_ano <> 0) THEN
      vs_CAT:=vl_monto_ano/1.02;
      RETURN Round(vs_CAT,4);
    END IF;
  END IF;*/

  RETURN Round(vs_CAT,4);


END;