CREATE OR REPLACE PROCEDURE spu_cartera (
   ve_operacion       IN       VARCHAR2
 , ve_cod_cartera     IN       NUMBER
 , ve_nom_cartera     IN       VARCHAR2 := NULL
 , ve_encargado       IN       VARCHAR2 := NULL
)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO cartera
                  (cod_cartera
                  ,nom_cartera
                  ,encargado
                  )
           VALUES (cartera_sq.NEXTVAL
                  ,ve_nom_cartera
                  ,ve_encargado
                  );
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE cartera
         SET nom_cartera = ve_nom_cartera,
             encargado   = ve_encargado
       WHERE cod_cartera = ve_cod_cartera;
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM cartera
            WHERE cod_cartera = ve_cod_cartera;
   END IF;
END;
/