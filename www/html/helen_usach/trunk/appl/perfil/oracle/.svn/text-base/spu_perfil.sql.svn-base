CREATE OR REPLACE PROCEDURE spu_perfil (
     ve_operacion  IN VARCHAR2
    ,ve_cod_perfil IN NUMBER    := NULL
    ,ve_nom_perfil IN VARCHAR2  := NULL
)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO perfil
                  (cod_perfil
                  ,nom_perfil )
           VALUES (perfil_sq.NEXTVAL
                  ,ve_nom_perfil);
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE perfil
        SET nom_perfil = ve_nom_perfil
            where    cod_perfil    = ve_cod_perfil;
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE autoriza_menu
      WHERE cod_perfil = ve_cod_perfil;

      DELETE perfil
      WHERE cod_perfil = ve_cod_perfil;
   END IF;
END;
/