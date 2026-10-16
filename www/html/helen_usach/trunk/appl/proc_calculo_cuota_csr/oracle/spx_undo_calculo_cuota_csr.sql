CREATE OR REPLACE PROCEDURE spx_undo_calculo_cuota_csr
AS
BEGIN
  DECLARE
    K_TIPO_DEUDA_CSR              VARCHAR2(3) := 'CSR';
    K_ESTADO_DEUDA_EXIGIBLE       NUMBER:= 2;
    K_ORIGEN_PAGO_CSR             NUMBER:= 9;
    vl_cod_descuento              NUMBER;
    vl_cod_plan_pago              NUMBER;
    vl_count                      NUMBER;

    CURSOR C_DEUDA_CSR IS
      SELECT  d.cod_deuda
      FROM    deuda d
      WHERE   d.cod_tipo_deuda = K_TIPO_DEUDA_CSR
              AND d.cod_estado_deuda = K_ESTADO_DEUDA_EXIGIBLE;
  BEGIN
    FOR A IN C_DEUDA_CSR LOOP
      SELECT  cod_plan_pago
        INTO  vl_cod_plan_pago
        FROM  plan_pago
        WHERE cod_deuda = a.cod_deuda
        AND    ano_cobro = To_Number(To_Char(SYSDATE,'yyyy')); 

      DELETE FROM descuento 
      WHERE cod_deuda = a.cod_deuda
        AND cod_origen_pago = K_ORIGEN_PAGO_CSR
        AND cod_plan_pago = vl_cod_plan_pago;
      
      sp_pp_actualiza_estado(vl_cod_plan_pago);
      DELETE CAMBIO_ESTADO_PLAN_PAGO
      WHERE cod_plan_pago = vl_cod_plan_pago;

      COMMIT;
    END LOOP;
  END;
END spx_undo_calculo_cuota_csr;
/
