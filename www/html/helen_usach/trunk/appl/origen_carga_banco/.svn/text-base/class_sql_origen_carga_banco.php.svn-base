<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_origen_carga_banco extends sql_tabla {
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			/////////////////  INPUT ///////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "select  OCB.COD_ORIGEN_CARGA_BANCO
			 						,OCB.NOM_ORIGEN_CARGA_BANCO
			 						,CC.COD_CUENTA_CORRIENTE
							 from 	ORIGEN_CARGA_BANCO OCB LEFT OUTER JOIN CUENTA_CORRIENTE CC ON OCB.COD_CUENTA_CORRIENTE = CC.COD_CUENTA_CORRIENTE
                            where 	OCB.COD_ORIGEN_CARGA_BANCO = {KEY1} ";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT	OCB.COD_ORIGEN_CARGA_BANCO
									,OCB.NOM_ORIGEN_CARGA_BANCO
									,CC.COD_CUENTA_CORRIENTE
							FROM 	ORIGEN_CARGA_BANCO OCB left outer join CUENTA_CORRIENTE CC ON OCB.COD_CUENTA_CORRIENTE = CC.COD_CUENTA_CORRIENTE
							WHERE 	OCB.COD_ORIGEN_CARGA_BANCO = {KEY1}";
				}
				break;
				/////////////////  OUTPUT ///////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 		COD_ORIGEN_CARGA_BANCO,
	               						NOM_ORIGEN_CARGA_BANCO,
                            			CC.NOM_CUENTA_CORRIENTE		   			   
	        				FROM 		ORIGEN_CARGA_BANCO OCB, CUENTA_CORRIENTE CC
                  			WHERE   	OCB.COD_CUENTA_CORRIENTE = CC.COD_CUENTA_CORRIENTE(+)
							ORDER BY 	 COD_ORIGEN_CARGA_BANCO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql=	"SELECT 	COD_ORIGEN_CARGA_BANCO,
										NOM_ORIGEN_CARGA_BANCO,
										CC.NOM_CUENTA_CORRIENTE		   			   
							FROM 		ORIGEN_CARGA_BANCO OCB left outer join CUENTA_CORRIENTE CC on OCB.COD_CUENTA_CORRIENTE = CC.COD_CUENTA_CORRIENTE
							ORDER BY	COD_ORIGEN_CARGA_BANCO";
				}
				break;
					
			case 'DD_CUENTA_CORRIENTE_OUTPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT   COD_CUENTA_CORRIENTE
									,NOM_CUENTA_CORRIENTE 
						FROM 		CUENTA_CORRIENTE
						ORDER BY 	COD_CUENTA_CORRIENTE";
				}
				break;
					
			case 'DD_CUENTA_CORRIENTE' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT   COD_CUENTA_CORRIENTE
									, NOM_CUENTA_CORRIENTE 
						FROM 		CUENTA_CORRIENTE 
						ORDER BY 	COD_CUENTA_CORRIENTE";
				}
				break;
					
					
			case 'NOMBRE_ORIGEN_CARGA_BANCO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT		COD_ORIGEN_CARGA_BANCO
										,NOM_ORIGEN_CARGA_BANCO 
							FROM 		ORIGEN_CARGA_BANCO
							ORDER BY 	COD_ORIGEN_CARGA_BANCO";
				}
				break;
				///////////////		AJAX_NOM_ORIGEN_CARGA_BANCO	///////////////////
			case 'AJAX_NOM_ORIGEN_CARGA_BANCO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	NOM_ORIGEN_CARGA_BANCO
							FROM	ORIGEN_CARGA_BANCO
							WHERE	NOM_ORIGEN_CARGA_BANCO = '{PARAM1}'
									AND	COD_CUENTA_CORRIENTE = {PARAM2}";
				}
				break;
					
				/////////////////  ERROR ///////////////
			default:
				base::error("sql_origen_carga_banco::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>