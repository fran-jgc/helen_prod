PROMPT CREATE OR REPLACE PROCEDURE spu_autoriza_menu
CREATE OR REPLACE PROCEDURE spu_autoriza_menu (  ve_cod_perfil    NUMBER
                                                ,ve_cod_item_menu VARCHAR2
                                                ,ve_lectura       VARCHAR2
                                                ,ve_escritura     VARCHAR2
                                                ,ve_impresion     VARCHAR2
                                                ,ve_tabs          VARCHAR2
                                              )
AS
BEGIN
  DECLARE
    vl_count          NUMBER;
    vl_count_tab      NUMBER;
    vl_autoriza_menu  VARCHAR2(1);
    vl_pos            NUMBER;
    vl_cod_item_menu  item_menu.cod_item_menu%TYPE;
    vl_tabs           VARCHAR2(32767);
  BEGIN
    IF(ve_escritura = 'S') THEN
      vl_autoriza_menu := 'E';
    ELSIF(ve_lectura = 'S') THEN
      vl_autoriza_menu := 'L';
    ELSE
      vl_autoriza_menu := 'N';
    END IF;

    SELECT  Count(*)
    INTO    vl_count
    FROM    autoriza_menu
    WHERE   cod_perfil = ve_cod_perfil
            AND cod_item_menu = ve_cod_item_menu;

    IF(vl_count = 0) THEN
      INSERT INTO autoriza_menu
                  (cod_autoriza_menu
                  ,cod_perfil
                  ,cod_item_menu
                  ,autoriza_menu
                  ,impresion
                  )
      VALUES      (autoriza_menu_sq.NEXTVAL
                  ,ve_cod_perfil
                  ,ve_cod_item_menu
                  ,vl_autoriza_menu
                  ,ve_impresion);
    ELSE
      UPDATE  autoriza_menu
      SET     autoriza_menu     = vl_autoriza_menu
              ,impresion        = ve_impresion
      WHERE   cod_perfil        = ve_cod_perfil
              AND cod_item_menu = ve_cod_item_menu;
    END IF;

    vl_tabs := ve_tabs;
    WHILE (Length(vl_tabs) <> 3) LOOP
      vl_pos            := InStrB(vl_tabs , '|' );
      vl_cod_item_menu  := SubStr(vl_tabs, 1, vl_pos - 1);
      vl_pos            := InStrB(vl_tabs,'|' );
      vl_autoriza_menu  := SubStr(vl_tabs, vl_pos + 1, 1);
      vl_tabs           := SubStr(vl_tabs, vl_pos + 3, Length(vl_tabs));

      SELECT  count(*)
      INTO    vl_count_tab
      FROM    autoriza_menu
      WHERE   cod_perfil = ve_cod_perfil
              AND cod_item_menu = vl_cod_item_menu;

      IF (vl_count_tab=0) THEN
        INSERT INTO autoriza_menu (cod_autoriza_menu
                                  ,cod_perfil
                                  ,cod_item_menu
                                  ,autoriza_menu
                                  ,impresion)
        VALUES                    (autoriza_menu_sq.NEXTVAL
                                  ,ve_cod_perfil
                                  ,vl_cod_item_menu
                                  ,vl_autoriza_menu
                                  ,ve_impresion);
      ELSE
        UPDATE    autoriza_menu
        SET       autoriza_menu     = vl_autoriza_menu
                  ,impresion        = ve_impresion
        WHERE     cod_perfil        = ve_cod_perfil
                  AND cod_item_menu = vl_cod_item_menu;
      END IF;
    END LOOP;
  END;
END;
/

