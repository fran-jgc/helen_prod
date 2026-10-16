CREATE OR REPLACE FUNCTION f_deuda_cuota_vigente (
   ve_cod_deuda   NUMBER
 , ve_fecha       DATE := SYSDATE
)
   RETURN NUMBER
AS
/******************************************************************************
   NAME:       f_deuda_cuota_vigente

   REVISIONS:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        24/10/2009   Victor Melo

   NOTES:
******************************************************************************/
BEGIN
   DECLARE
      vl_cod_plan_pago_vigente                NUMBER;
      vl_nro_cuota                            NUMBER;
   BEGIN
      -- busca las cuotas por vencer (distintas a 9999) y obtien la menor
      SELECT MIN (nro_cuota)
        INTO vl_nro_cuota
        FROM plan_pago
       WHERE cod_deuda = ve_cod_deuda
         AND ano_cobro <> 9999                                                                                                                       -- para el caso CS la cuota futura nunca es vigente
         AND fecha_vencto > ve_fecha;

      IF vl_nro_cuota IS NULL
      THEN
         RETURN NULL;
      END IF;

      SELECT cod_plan_pago
        INTO vl_cod_plan_pago_vigente
        FROM plan_pago
       WHERE cod_deuda = ve_cod_deuda
         AND nro_cuota = vl_nro_cuota;

      RETURN vl_cod_plan_pago_vigente;
   END;
END;
/