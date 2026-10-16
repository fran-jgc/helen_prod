-- Esta función retorna valor uno o cero, para determinar si el usuario puede ver todos los registros.
CREATE OR REPLACE FUNCTION f_get_tiene_acceso (ve_cod_usuario_actual number, ve_cod_usuario number)

RETURN NUMBER
AS

  	vl_cod_perfil     NUMBER;
	vl_autoriza_menu  varchar2(1);
	kl_ver_todo_ingreso varchar2(10);

BEGIN
	kl_ver_todo_ingreso := '990505';

	select cod_perfil
	INTO vl_cod_perfil
	from usuario where cod_usuario = ve_cod_usuario_actual;

	select autoriza_menu
	INTO vl_autoriza_menu
	from autoriza_menu
	where cod_perfil = vl_cod_perfil
		and cod_item_menu = kl_ver_todo_ingreso;

	IF (vl_autoriza_menu = 'E') THEN -- = 'Ver todos los ingresos'
		return 1;
	END IF;

	IF (ve_cod_usuario_actual = ve_cod_usuario) THEN
		return 1;
	END IF;

	return 0;
END; 
