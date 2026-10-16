CREATE OR REPLACE PROCEDURE spu_tipo_condonacion (
   ve_operacion       		   IN       VARCHAR
 , ve_cod_tipo_condonacion     IN       NUMERIC
 , ve_nom_tipo_condonacion     IN       VARCHAR := NULL
)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO tipo_condonacion
                  (cod_tipo_condonacion
                  ,nom_tipo_condonacion
                  )
           VALUES (tipo_condonacion_sq.NEXTVAL
                  ,ve_nom_tipo_condonacion
                  );
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE tipo_condonacion
         SET nom_tipo_condonacion = ve_nom_tipo_condonacion
       WHERE cod_tipo_condonacion = ve_cod_tipo_condonacion;
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM tipo_condonacion
            WHERE cod_tipo_condonacion = ve_cod_tipo_condonacion;
   END IF;
END;
/