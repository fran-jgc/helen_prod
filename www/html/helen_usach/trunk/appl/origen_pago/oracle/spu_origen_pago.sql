CREATE OR REPLACE PROCEDURE spu_origen_pago (
   ve_operacion     IN       VARCHAR2
 , ve_cod_origen_pago       IN       NUMBER   := NULL
 , ve_nom_origen_pago       IN       VARCHAR2 := NULL
)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO origen_pago
                  (cod_origen_pago
                  ,nom_origen_pago
                  )
           VALUES (origen_pago_sq.NEXTVAL
                  ,ve_nom_origen_pago
                  );
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE origen_pago
         SET nom_origen_pago = ve_nom_origen_pago
       WHERE cod_origen_pago = ve_cod_origen_pago;
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM origen_pago
            WHERE cod_origen_pago = ve_cod_origen_pago;
   END IF;
END;
/