CREATE OR REPLACE PROCEDURE spu_tipo_suspension (
   ve_operacion       		    IN       VARCHAR
 , ve_cod_tipo_suspension     IN       NUMERIC
 , ve_nom_tipo_suspension     IN       VARCHAR := NULL
 , ve_periodo_maximo          IN       NUMERIC := NULL
)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO tipo_suspension
                  (cod_tipo_suspension
                  ,nom_tipo_suspension
                  ,periodo_maximo
                  )
           VALUES (tipo_suspension_sq.NEXTVAL
                  ,ve_nom_tipo_suspension
                  ,ve_periodo_maximo
                  );
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE tipo_suspension
         SET nom_tipo_suspension = ve_nom_tipo_suspension
            ,periodo_maximo = ve_periodo_maximo
       WHERE cod_tipo_suspension = ve_cod_tipo_suspension;
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM tipo_suspension
            WHERE cod_tipo_suspension = ve_cod_tipo_suspension;
   END IF;
END;
/