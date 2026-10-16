CREATE OR REPLACE PROCEDURE spu_estado_plan_pago (
   ve_operacion              	 IN       VARCHAR
 , ve_cod_estado_plan_pago       IN       NUMERIC
 , ve_nom_estado_plan_pago       IN       VARCHAR := NULL
)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO estado_plan_pago
                  (cod_estado_plan_pago
                 , nom_estado_plan_pago
                  )
           VALUES (estado_plan_pago_sq.NEXTVAL
                 , ve_nom_estado_plan_pago
                  );
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE estado_plan_pago
         SET nom_estado_plan_pago = ve_nom_estado_plan_pago
       WHERE cod_estado_plan_pago = ve_cod_estado_plan_pago;
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM estado_plan_pago
            WHERE cod_estado_plan_pago = ve_cod_estado_plan_pago;
   END IF;
END;
/