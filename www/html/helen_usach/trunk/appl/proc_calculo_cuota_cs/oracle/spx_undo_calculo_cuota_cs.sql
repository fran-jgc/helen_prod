CREATE OR REPLACE PROCEDURE spx_undo_calculo_cuota_cs
AS
BEGIN
  DECLARE
    K_TIPO_DEUDA_CS           VARCHAR2(2) := 'CS';
    K_ESTADO_DEUDA_EXIGIBLE   NUMBER:= 2;
    vl_monto_capital          plan_pago.monto_capital%TYPE;
    vl_monto_interes          plan_pago.monto_interes%TYPE;
    vl_monto_capital9999      plan_pago.monto_capital%TYPE;
    vl_monto_interes9999      plan_pago.monto_interes%TYPE;
    vl_count_vl_ano_calculo             NUMBER;
    vl_count_9999             NUMBER;
    vl_ano_calculo            NUMBER := 2010;

    CURSOR C_DEUDA_CS IS
      SELECT  p.cod_deuda
              ,p.monto_capital
              ,p.monto_interes
              ,p.cod_plan_pago
      FROM    plan_pago p, deuda d
      WHERE   d.cod_tipo_deuda = K_TIPO_DEUDA_CS
              AND d.cod_estado_deuda = K_ESTADO_DEUDA_EXIGIBLE
              AND p.cod_deuda = d.cod_deuda
              AND p.ano_cobro = vl_ano_calculo;

  BEGIN
    FOR a IN C_DEUDA_CS LOOP
      UPDATE plan_pago
      SET monto_capital = monto_capital + a.monto_capital
        , monto_interes = monto_interes + a.monto_interes
      WHERE cod_deuda = a.cod_deuda
        AND ano_cobro = 9999;
        
		delete abono
		where cod_plan_pago = a.cod_plan_pago;
		
		delete descuento
		where cod_plan_pago = a.cod_plan_pago;
		
		delete cambio_estado_plan_pago
		where cod_plan_pago = a.cod_plan_pago;
		
		delete sub_boleta
		where cod_boleta in (select cod_boleta from boleta where cod_plan_pago = a.cod_plan_pago);

		delete boleta
		where cod_plan_pago = a.cod_plan_pago;

      DELETE plan_pago
      WHERE cod_plan_pago = a.cod_plan_pago;   
    END LOOP;

    DELETE condonacion_cs;

    COMMIT;
  END;
END spx_undo_calculo_cuota_cs;
/
