CREATE OR REPLACE FUNCTION f_get_tabs_por_modulo( ve_cod_item_menu  varchar2,
                                                  ve_cod_perfil     NUMERIC
                                                 )
RETURN VARCHAR2
AS
BEGIN
  DECLARE
    res           VARCHAR2(500);
		cod_item_menu VARCHAR2(10);
		nom_item_menu VARCHAR2(100);
		autoriza_menu VARCHAR2(1);

    CURSOR c_cursor  IS
      SELECT  i.cod_item_menu
              ,i.nom_item_menu
              ,a.autoriza_menu
      FROM    item_menu i
              ,autoriza_menu a
      WHERE   i.cod_item_menu like ve_cod_item_menu || '%'
              AND i.cod_item_menu <> ve_cod_item_menu
              AND Length(i.cod_item_menu) = Length(ve_cod_item_menu) + 2
              AND a.cod_item_menu = i.cod_item_menu
              AND a.cod_perfil = ve_cod_perfil
              AND i.TIPO_ITEM_MENU = 'T'
              AND i.visible <> 'N';
  BEGIN
    res := '';
    FOR a IN c_cursor LOOP
      res := res || a.cod_item_menu || '|' || a.nom_item_menu || '|' || a.autoriza_menu || '|';
    END LOOP;

    IF (Length(res) > 0)THEN
      res := SubStr(res, 1, Length(res)-1);
    END IF;

    RETURN res;
   END;
END;
/