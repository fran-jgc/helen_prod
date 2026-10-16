<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_usuario extends sql_tabla {
	static function get_sql($selector_sql) {
		switch ($selector_sql) {
			/////////////////  INPUT ///////////////		
			case 'INPUT' :		
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql =  "select  U.COD_USUARIO,
									U.NOM_USUARIO,
									U.LOGIN,
									'*--SinCambio--*' PASSWORD,	
									U.COD_PERFIL,
									P.NOM_PERFIL,																		
									U.AUTORIZA_INGRESO,
									U.TELEFONO,
									U.CELULAR,
									U.MAIL,
									U.ES_CAJERO
							from 	USUARIO U, PERFIL P
							where 	COD_USUARIO = {KEY1} AND
									P.COD_PERFIL = U.COD_PERFIL";
				}

			break;
			///////////////// OUTPUT ///////////////
			case 'OUTPUT' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql =  "select 	U.COD_USUARIO,
					               		U.NOM_USUARIO,			  	   
					               		U.LOGIN,			  	   
					               		P.NOM_PERFIL			   			   
					        from 		USUARIO U, PERFIL P
							where 		P.COD_PERFIL = U.COD_PERFIL
							order by	U.COD_USUARIO";
				}
			break;
				
			/////////////////  DD_COD_PERFIL ///////////////
			case 'DD_COD_PERFIL' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql =  "select		COD_PERFIL,
									  	NOM_PERFIL
							from	  	PERFIL
							order by  	COD_PERFIL";
				}
			break;
				
			/////////////////  ERROR ///////////////
			default: 
				base::error("sql_usuario::get_sql, selector: '$selector_sql' no definido");			
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
				
	}
}
?>