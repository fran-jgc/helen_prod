CREATE OR REPLACE PROCEDURE spu_plan_pago_renegociacion (
   ve_operacion                   IN       VARCHAR2
 , ve_cod_plan_pago_renegociacion IN       NUMBER
 , ve_cod_renegociacion           IN       NUMBER   := NULL
 , ve_cod_plan_pago               IN       NUMBER   := NULL
 , ve_cod_boleta                  IN       NUMBER   := NULL
 , ve_cant_cuota                  IN       NUMBER   := NULL
 , ve_fecha_primer_pago           IN       DATE     := NULL
)
AS
BEGIN
  DECLARE

  BEGIN
   IF (ve_operacion = 'INSERT') THEN

      INSERT INTO plan_pago_renegociacion
            (cod_plan_pago_renegociacion
            , cod_renegociacion
            , cod_plan_pago
            , cod_boleta
            , cant_cuota
            , fecha_primer_pago
            )
      VALUES (plan_pago_renegociacion_sq.NEXTVAL
            , ve_cod_renegociacion
            , ve_cod_plan_pago
            , ve_cod_boleta
            , ve_cant_cuota
            , ve_fecha_primer_pago
            );
   ELSIF (ve_operacion = 'UPDATE') THEN
      UPDATE plan_pago_renegociacion
         SET cod_plan_pago      = ve_cod_plan_pago
           , cod_boleta         = ve_cod_boleta
           , cant_cuota         = ve_cant_cuota
           , fecha_primer_pago  = ve_fecha_primer_pago
       WHERE cod_plan_pago_renegociacion = ve_cod_plan_pago_renegociacion;
   ELSIF (ve_operacion = 'DELETE_ALL') THEN
      DELETE  plan_pago_renegociacion
      WHERE cod_renegociacion = ve_cod_renegociacion;
   END IF;
   END;
END;