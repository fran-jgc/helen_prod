PROMPT CREATE OR REPLACE PROCEDURE spu_usuario
CREATE OR REPLACE PROCEDURE spu_usuario (
   ve_operacion               IN       VARCHAR
 , ve_cod_usuario             IN       NUMERIC
 , ve_nom_usuario             IN       VARCHAR
 , ve_login                   IN       VARCHAR
 , ve_password                IN       VARCHAR
 , ve_cod_perfil              IN       NUMERIC
 , ve_autoriza_ingreso        IN       VARCHAR
 , ve_mail                    IN       VARCHAR
 , ve_telefono                IN       VARCHAR
 , ve_celular                 IN       VARCHAR
 , ve_es_cajero				        IN       VARCHAR
)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO usuario
                  (cod_usuario
                 , nom_usuario
                 , login
                 , PASSWORD
                 , cod_perfil
                 , autoriza_ingreso
                 , mail
                 , telefono
                 , celular
                 , es_cajero
                  )
           VALUES (usuario_sq.NEXTVAL
                 , ve_nom_usuario
                 , ve_login
                 , ve_password
                 , ve_cod_perfil
                 , ve_autoriza_ingreso
                 , ve_mail
                 , ve_telefono
                 , ve_celular
                 , ve_es_cajero
                  );
   END IF;

   IF (ve_operacion = 'UPDATE')
   THEN
      -- '*--SinCambio--*' encriptado equivale a '4b197ae8c766311824f077e3885d3478'
      IF (ve_password = '4b197ae8c766311824f077e3885d3478')
      THEN
         UPDATE usuario
            SET nom_usuario = ve_nom_usuario
              , login = ve_login
              , cod_perfil = ve_cod_perfil
              , autoriza_ingreso = ve_autoriza_ingreso
              , mail = ve_mail
              , telefono = ve_telefono
              , celular = ve_celular
              , es_cajero = ve_es_cajero
          WHERE cod_usuario = ve_cod_usuario;
      ELSE
         UPDATE usuario
            SET nom_usuario = ve_nom_usuario
              , login = ve_login
              , PASSWORD = ve_password
              , cod_perfil = ve_cod_perfil
              , autoriza_ingreso = ve_autoriza_ingreso
              , mail = ve_mail
              , telefono = ve_telefono
              , celular = ve_celular
              , es_cajero = ve_es_cajero
          WHERE cod_usuario = ve_cod_usuario;
      END IF;
   ELSE
      IF (ve_operacion = 'DELETE')
      THEN
         DELETE      usuario
               WHERE cod_usuario = ve_cod_usuario;
      END IF;
   END IF;
END;
/

