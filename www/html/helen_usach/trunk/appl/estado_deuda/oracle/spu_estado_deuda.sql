CREATE OR REPLACE PROCEDURE spu_estado_deuda (
   ve_operacion              IN       VARCHAR
 , ve_cod_estado_deuda       IN       NUMERIC := NULL
 , ve_nom_estado_deuda       IN       VARCHAR := NULL
)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO estado_deuda
                  (cod_estado_deuda
                 , nom_estado_deuda
                  )
           VALUES (ve_cod_estado_deuda
                 , ve_nom_estado_deuda
                  );
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE estado_deuda
         SET nom_estado_deuda = ve_nom_estado_deuda
       WHERE cod_estado_deuda = ve_cod_estado_deuda;
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM estado_deuda
            WHERE cod_estado_deuda = ve_cod_estado_deuda;
   END IF;
END;
/

