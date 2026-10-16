CREATE OR REPLACE PROCEDURE spi_deudores_cobranza(ve_cod_tipo_deuda   VARCHAR2
                                                ,ve_ano_inicio      NUMBER
                                                ,ve_ano_termino     NUMBER)
AS
BEGIN
  DECLARE
    K_DEUDA_EXIGIBLE      NUMBER:=2;

    vl_saldo_deuda        NUMBER;
    vl_cant_deudor        NUMBER;

    CURSOR c_temp IS
    SELECT DISTINCT cod_deuda
    FROM TEMP_DEUDORES_COBRANZA;

  BEGIN
    INSERT INTO TEMP_DEUDORES_COBRANZA
      (RUT
      ,ANO
      ,COD_DEUDA
      ,NOM_ESTADO_PLAN_PAGO
      ,MONTO_CAPITAL
      ,MONTO_INTERES
      ,MONTO_MULTA
      ,MONTO_ABONO_CAPITAL
      ,MONTO_ABONO_INTERES
      ,SALDO_PLAN_PAGO
      )
    SELECT REPLACE(To_Char(de.rut, '99,999,999'), ',', '.') || '-' || de.dig_verif rut
          ,p.ano_cobro
          ,d.cod_deuda
          ,e.nom_estado_plan_pago
          ,p.monto_capital
          ,p.monto_interes
          ,f_pp_saldo_multa(p.cod_plan_pago) monto_multa
          ,f_pp_monto_abono(p.cod_plan_pago, 'CAPITAL') monto_abono_capital
          ,f_pp_monto_abono(p.cod_plan_pago, 'INTERES') monto_abono_interes
          ,f_pp_saldo_plan_pago(p.cod_plan_pago) saldo_plan_pago
    FROM deuda d, deudor de, plan_pago p, estado_plan_pago e
    WHERE d.cod_tipo_deuda = ve_cod_tipo_deuda
      AND d.cod_estado_deuda = K_DEUDA_EXIGIBLE
      AND de.cod_deudor = d.cod_deudor
      AND p.cod_deuda = d.cod_deuda
      AND p.ano_cobro BETWEEN ve_ano_inicio AND ve_ano_termino
      AND e.cod_estado_plan_pago = p.cod_estado_plan_pago
    ORDER BY p.ano_cobro, de.rut;

    FOR a IN c_temp LOOP
      vl_saldo_deuda := f_deuda_saldo_total(a.cod_deuda);
      UPDATE TEMP_DEUDORES_COBRANZA
      SET SALDO_DEUDA = vl_saldo_deuda
      WHERE COD_DEUDA = a.cod_deuda;
    END LOOP;

    SELECT Count(DISTINCT RUT)
    INTO vl_cant_deudor
    FROM TEMP_DEUDORES_COBRANZA;

    UPDATE TEMP_DEUDORES_COBRANZA
    SET CANT_DEUDOR = vl_cant_deudor;
  END;
END spi_deudores_cobranza;
/