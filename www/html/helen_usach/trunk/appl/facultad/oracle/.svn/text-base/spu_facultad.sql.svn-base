CREATE OR REPLACE PROCEDURE spu_facultad (
   ve_operacion     IN       VARCHAR2
 , ve_cod_facultad  IN       NUMBER   := NULL
 , ve_nom_facultad  IN       VARCHAR2 := NULL
)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO facultad
                  (cod_facultad
                  ,nom_facultad
                  )
           VALUES (facultad_sq.NEXTVAL
                  ,ve_nom_facultad
                  );
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE facultad
         SET nom_facultad = ve_nom_facultad
       WHERE cod_facultad = ve_cod_facultad;
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM facultad
            WHERE cod_facultad = ve_cod_facultad;
   END IF;
END;
/