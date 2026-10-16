CREATE OR REPLACE PROCEDURE spi_morosidad_ano(ve_cod_tipo_deuda   VARCHAR2
                                              ,ve_ano_inicio      NUMBER
                                              ,ve_ano_termino     NUMBER)
AS
BEGIN
  DECLARE
    K_PLAN_PAGO_MOROSO      NUMBER:=4;

  BEGIN
    INSERT INTO TEMP_MOROSIDAD_ANO
      (ANO
      ,CANT_DEUDOR
      ,MONTO_MORA
      ,MONTO_MORA_PESOS
      ,MONTO_MULTA
      ,MONTO_MULTA_PESOS)
    SELECT p.ano_cobro
          ,Count(DISTINCT cod_deudor) cant_deudor
          ,Sum(f_pp_saldo_mora(p.cod_plan_pago)) monto_mora
          ,Sum(f_pp_saldo_mora(p.cod_plan_pago) * f_mon_get_valor_moneda(3, sysdate)) monto_mora_pesos 
          ,Sum(f_pp_saldo_multa(p.cod_plan_pago)) monto_multa
          ,Sum(f_pp_saldo_multa(p.cod_plan_pago) * f_mon_get_valor_moneda(3, sysdate)) monto_multa_pesos
    FROM plan_pago p, deuda d
    WHERE cod_estado_plan_pago = K_PLAN_PAGO_MOROSO
      AND ano_cobro BETWEEN ve_ano_inicio AND ve_ano_termino
      AND d.cod_deuda = p.cod_deuda
      AND d.cod_tipo_deuda = ve_cod_tipo_deuda
    GROUP BY ano_cobro
    ORDER BY ano_cobro;

    UPDATE TEMP_MOROSIDAD_ANO
    SET MONTO_TOTAL = MONTO_MORA + MONTO_MULTA
    	,MONTO_TOTAL_PESOS = MONTO_MORA_PESOS + MONTO_MULTA_PESOS;
  END;
END spi_morosidad_ano;
/