 <?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_perfil extends sql_tabla {
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			/////////////////   INPUT_DW_OPCION_ESPECIAL   ///////////////
			case 'INPUT_DW_OPCION_ESPECIAL' :
				/////////////////   ORACLE   ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT		I.COD_ITEM_MENU
										, I.NOM_ITEM_MENU
										, A.COD_PERFIL
										,	CASE A.AUTORIZA_MENU
											WHEN 	'E'then 'S'
											ELSE	'N'
											END		AUTORIZA
										, A.IMPRESION
										,	(SELECT COUNT(*) FROM ITEM_MENU II
											WHERE	II.COD_ITEM_MENU	LIKE	I.COD_ITEM_MENU || '%'
											AND	II.COD_ITEM_MENU <> I.COD_ITEM_MENU) HIJOS
							FROM		ITEM_MENU I, AUTORIZA_MENU A
							WHERE		I.COD_ITEM_MENU like '99%'
							AND			A.COD_PERFIL = {KEY1}
							AND			I.COD_ITEM_MENU = A.COD_ITEM_MENU
							ORDER BY	I.COD_ITEM_MENU"
							;
				}
				/////////////////   SQL-SERVER   ///////////////
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT		I.COD_ITEM_MENU,
										I.NOM_ITEM_MENU,
										A.COD_PERFIL, 
										CASE A.AUTORIZA_MENU 
											when 'E'then 'S' 
											else 'N'
										end AUTORIZA,
										A.IMPRESION,
										(select count(*) from	ITEM_MENU II 
										where	II.COD_ITEM_MENU like I.COD_ITEM_MENU + '%' 
										and    II.COD_ITEM_MENU <> I.COD_ITEM_MENU) HIJOS						
						FROM			ITEM_MENU I, AUTORIZA_MENU A
						WHERE			I.COD_ITEM_MENU like '99%'AND
										A.COD_PERFIL = {KEY1} AND
										I.COD_ITEM_MENU = A.COD_ITEM_MENU
						order by I.COD_ITEM_MENU";
				}
			break;
			
			/////////////////   INPUT_DW_AUTORIZA_MENU   ///////////////
			case 'INPUT_DW_AUTORIZA_MENU' :
				/////////////////   ORACLE   ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT		i.COD_ITEM_MENU
										, i.NOM_ITEM_MENU
										, a.COD_PERFIL
										, CASE a.AUTORIZA_MENU WHEN 'N' THEN 'N' ELSE 'S' END LECTURA
										, CASE a.AUTORIZA_MENU WHEN 'E' THEN 'S' ELSE 'N' END ESCRITURA
										, a.IMPRESION
										, I.TIENE_IMPRESION
										,	(SELECT COUNT(*) 
											FROM	ITEM_MENU ii
											WHERE	ii.COD_ITEM_MENU LIKE i.COD_ITEM_MENU || '%'
											AND		ii.COD_ITEM_MENU <> i.COD_ITEM_MENU
											AND		ii.TIPO_ITEM_MENU in ('M', 'TP')) HIJOS, f_get_tabs_por_modulo (i.COD_ITEM_MENU, a.COD_PERFIL) TAB
										, TIPO_ITEM_MENU
							FROM		ITEM_MENU i, AUTORIZA_MENU a
							WHERE		a.COD_PERFIL = {KEY1}
							AND			a.COD_ITEM_MENU not LIKE '99%'
							AND			i.COD_ITEM_MENU = a.COD_ITEM_MENU
							AND			i.TIPO_ITEM_MENU in ('M', 'P', 'TP')
							ORDER BY	a.COD_ITEM_MENU";												
				}
				/////////////////   SQL-SERVER   ///////////////
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT			i.COD_ITEM_MENU,
											i.NOM_ITEM_MENU, 
											a.COD_PERFIL,  
											case a.AUTORIZA_MENU when 'N'then 'N' else 'S'end LECTURA,				
											case a.AUTORIZA_MENU when 'E' then 'S' else 'N' end ESCRITURA,
											a.IMPRESION,
											I.TIENE_IMPRESION,
											(select count(*) from	ITEM_MENU ii 
												where	ii.COD_ITEM_MENU like i.COD_ITEM_MENU + '%' 
													and ii.COD_ITEM_MENU <> i.COD_ITEM_MENU
													and ii.TIPO_ITEM_MENU in ('M', 'TP')) HIJOS,
											dbo.f_get_tabs_por_modulo (i.COD_ITEM_MENU, a.COD_PERFIL) TAB,
											TIPO_ITEM_MENU
								FROM		ITEM_MENU i, AUTORIZA_MENU a  
								WHERE		a.COD_PERFIL = {KEY1} and
											a.COD_ITEM_MENU not like '99%' and
											i.COD_ITEM_MENU = a.COD_ITEM_MENU and
											i.TIPO_ITEM_MENU in ('M', 'P', 'TP')
								ORDER BY	a.COD_ITEM_MENU";
				}
			break;
			
			/////////////////   INPUT_WI_PERFIL   ///////////////
			case 'INPUT_WI_PERFIL' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_PERFIL
									, NOM_PERFIL
									, NOM_PERFIL NOM_PERFIL_H
							FROM	PERFIL
							WHERE	COD_PERFIL = {KEY1}";
				}
			break;
			
			/////////////////   OUTPUT   ///////////////
			case 'OUTPUT' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_PERFIL
									, NOM_PERFIL
							FROM	PERFIL
							ORDER BY	COD_PERFIL";
				}
			break;
			case 'AJAX_NOM_PERFIL' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	NOM_PERFIL
							FROM	PERFIL
							WHERE	NOM_PERFIL = '{PARAM1}'";
				}
			break;
			/////////////////  ERROR ///////////////
			default:
			base::error("sql_perfil::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>