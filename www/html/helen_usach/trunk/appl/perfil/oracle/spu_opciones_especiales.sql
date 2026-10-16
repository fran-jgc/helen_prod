CREATE OR REPLACE PROCEDURE spu_opciones_especiales (
   ve_cod_perfil              IN       NUMERIC
 , ve_cod_item_menu           IN       VARCHAR2
 , ve_autoriza                IN       VARCHAR2
)
AS
BEGIN
   DECLARE
      vl_count                                NUMERIC;
      vl_autoriza_menu                        VARCHAR (1);
   BEGIN
      IF (ve_autoriza = 'S')
      THEN
         vl_autoriza_menu                     := 'E';
      ELSE
         vl_autoriza_menu                     := 'N';
      END IF;

      SELECT COUNT (*)
        INTO vl_count
        FROM autoriza_menu
       WHERE cod_perfil = ve_cod_perfil
         AND cod_item_menu = ve_cod_item_menu;

      IF (vl_count = 0)
      THEN
         INSERT INTO autoriza_menu
                     (cod_autoriza_menu
                    , cod_perfil
                    , cod_item_menu
                    , autoriza_menu
                    , impresion
                     )
              VALUES (autoriza_menu_sq.NEXTVAL
                    , ve_cod_perfil
                    , ve_cod_item_menu
                    , vl_autoriza_menu
                    , 'N'
                     );
      ELSE
         UPDATE autoriza_menu
            SET autoriza_menu = vl_autoriza_menu
          WHERE cod_perfil = ve_cod_perfil
            AND cod_item_menu = ve_cod_item_menu;
      END IF;
   END;
END;
/