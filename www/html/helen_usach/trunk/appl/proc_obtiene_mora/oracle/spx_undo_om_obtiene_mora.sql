PROMPT CREATE OR REPLACE PROCEDURE spx_undo_om_obtiene_mora
CREATE OR REPLACE PROCEDURE spx_undo_om_obtiene_mora(ve_fecha_vencto          DATE)
AS
BEGIN
  DECLARE
    K_PLAN_PAGO_EMITIDA          NUMBER := 1;
    K_PLAN_PAGO_MOROSA          NUMBER := 4;
    K_ORIGEN_NO_COBRABLE        NUMBER := 8;
    vl_cod_plan_pago             plan_pago.cod_plan_pago%TYPE;
    vl_cod_deuda                 plan_pago.cod_deuda%TYPE;
    vl_count                     NUMBER;

  BEGIN
    DELETE descuento
    WHERE  COD_ORIGEN_PAGO  =   K_ORIGEN_NO_COBRABLE;

    UPDATE plan_pago
    SET cod_estado_plan_pago = K_PLAN_PAGO_EMITIDA
    WHERE   Trunc(fecha_vencto) = Trunc(ve_fecha_vencto)
         AND cod_estado_plan_pago = K_PLAN_PAGO_MOROSA;

    DELETE cambio_estado_plan_pago;

    COMMIT;

  END;
END spx_undo_om_obtiene_mora;
/

