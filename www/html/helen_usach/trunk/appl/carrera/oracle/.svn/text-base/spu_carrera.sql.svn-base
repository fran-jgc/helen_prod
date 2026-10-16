CREATE OR REPLACE PROCEDURE spu_carrera (
   ve_operacion       IN       VARCHAR2
 , ve_cod_carrera     IN       number
 , ve_nom_carrera     IN       VARCHAR2  := NULL
 , ve_cod_facultad    IN       NUMBER    := NULL
)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO carrera
                  (cod_carrera
                  ,nom_carrera
                  ,cod_facultad
                  )
           VALUES (ve_cod_carrera
                  ,ve_nom_carrera
                  ,ve_cod_facultad
                  );
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE carrera
         SET nom_carrera  = ve_nom_carrera,
             cod_facultad = ve_cod_facultad
       WHERE cod_carrera = ve_cod_carrera;
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM carrera
            WHERE cod_carrera = ve_cod_carrera;
   END IF;
END;
/