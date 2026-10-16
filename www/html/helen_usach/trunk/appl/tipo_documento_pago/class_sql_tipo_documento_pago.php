<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_tipo_documento_pago extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			
			/////////////   INPUT   //////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT 	COD_TIPO_DOCUMENTO_PAGO 
										,NOM_TIPO_DOCUMENTO_PAGO
							FROM 		TIPO_DOCUMENTO_PAGO
							WHERE 		COD_TIPO_DOCUMENTO_PAGO = {KEY1}
							ORDER BY 	COD_TIPO_DOCUMENTO_PAGO";
				}
			break;
			
			////////////   OUTPUT /////////////////////
			case 'OUTPUT' :
					if (K_TIPO_BD=="oci"  || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT 	COD_TIPO_DOCUMENTO_PAGO
              							,NOM_TIPO_DOCUMENTO_PAGO
        					FROM 		TIPO_DOCUMENTO_PAGO
							ORDER BY 	COD_TIPO_DOCUMENTO_PAGO";
				}
			break;
			
			///////////////		AJAX_COD_TIPO_DOCUMENTO_PAGO	///////////////////
			case 'AJAX_COD_TIPO_DOCUMENTO_PAGO' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_TIPO_DOCUMENTO_PAGO
							FROM	TIPO_DOCUMENTO_PAGO
							WHERE	COD_TIPO_DOCUMENTO_PAGO = '{PARAM1}'";
				}
				break;

			///////////////		DW_CUENTA_TIPO_FAMILIA_DEUDA	///////////////////
			case 'DW_CUENTA_TIPO_FAMILIA_DEUDA' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT  COD_CUENTA_TIPO_DOCUMENTO_PAGO
							        ,FD.COD_FAMILIA_DEUDA 
							        ,COD_CUENTA_CONTABLE
							        ,USAR_CUENTA_CTACTE
							FROM    FAMILIA_DEUDA FD, CUENTA_TIPO_DOCUMENTO_PAGO C
							WHERE 	FD.COD_FAMILIA_DEUDA = C.COD_FAMILIA_DEUDA
							AND 	COD_TIPO_DOCUMENTO_PAGO = {KEY1}";
				}
				break;
				
			///////////////		DD_FAMILIA_DEUDA	///////////////////
			case 'DD_FAMILIA_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select COD_FAMILIA_DEUDA,
									NOM_FAMILIA_DEUDA
							from FAMILIA_DEUDA
							order by COD_FAMILIA_DEUDA";
				}
				break;

			/////////////////////	DD_CUENTA_CONTABLE  ///////////////////////// 
			case 'DD_CUENTA_CONTABLE' :
				/////////////////  ORACLE ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT		COD_CUENTA_CONTABLE
										, To_Char(COD_CUENTA_CONTABLE) || ' ' || NOM_CUENTA_CONTABLE
							FROM		CUENTA_CONTABLE
							ORDER BY	COD_CUENTA_CONTABLE";
				}
				/////////////////  SQL-SERVER ///////////////
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT		COD_CUENTA_CONTABLE
										, CONVERT (VARCHAR(20),COD_CUENTA_CONTABLE) + ' ' + NOM_CUENTA_CONTABLE
							FROM		CUENTA_CONTABLE
							ORDER BY	COD_CUENTA_CONTABLE";
				}
				break;
			
			///////////////		NEW_RECORD_TIPO_DOCUMENTO_PAGO	///////////////////
			case 'NEW_RECORD_FAMILIA_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select 	COD_FAMILIA_DEUDA
							FROM 	FAMILIA_DEUDA
							order by COD_FAMILIA_DEUDA";
				}
				break;	
			/////////////////    ERROR    ///////////
			default:
				base::error("sql_tipo_documento_pago::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;	
	}
}
?>