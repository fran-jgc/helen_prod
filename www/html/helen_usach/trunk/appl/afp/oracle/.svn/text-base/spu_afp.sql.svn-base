CREATE OR REPLACE PROCEDURE spu_afp (
   ve_operacion     IN       VARCHAR2
 , ve_cod_afp       IN       NUMBER   := NULL
 , ve_nom_afp       IN       VARCHAR2 := NULL
)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO afp
                  (cod_afp
                  ,nom_afp
                  )
           VALUES (afp_sq.NEXTVAL
                  ,ve_nom_afp
                  );
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE afp
         SET nom_afp = ve_nom_afp
       WHERE cod_afp = ve_cod_afp;
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM afp
            WHERE cod_afp = ve_cod_afp;
   END IF;
END;
/