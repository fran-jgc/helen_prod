CREATE OR REPLACE FUNCTION f_deuda_last_fecha_abono (
   VE_COD_DEUDA   NUMBER
)
   RETURN DATE
AS
   VS_FECHA_ABONO                          DATE;
/******************************************************************************
   NAME:       f_deuda_last_fecha_abono

   Entrega la ultima fecha de registro de un abono para la deuda entrante
   REVISIONS:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        22/01/2010  Sergio Castrp     creacion
   NOTAS:

******************************************************************************/
BEGIN
   DECLARE
      KL_COD_ESTADO_PAGO_VIGENTE              NUMBER := 1;
   BEGIN
      SELECT MAX (FECHA_ABONO)
        INTO VS_FECHA_ABONO
        FROM ABONO
       WHERE COD_DEUDA = VE_COD_DEUDA
         AND COD_ESTADO_PAGO = KL_COD_ESTADO_PAGO_VIGENTE;

      RETURN VS_FECHA_ABONO;
   END;
END;
/