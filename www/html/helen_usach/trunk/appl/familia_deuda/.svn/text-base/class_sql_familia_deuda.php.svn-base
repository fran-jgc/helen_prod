<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_familia_deuda extends sql_tabla {
	static function get_sql($selector_sql) {
		switch ($selector_sql) {
			////////////////		INPUT		///////////////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_FAMILIA_DEUDA
										,NOM_FAMILIA_DEUDA
										,COD_CUENTA_CH_FECHA
										,COD_CUENTA_PROTESTO
										,COD_CUENTA_COBRANZA
							FROM 		FAMILIA_DEUDA
							WHERE 		COD_FAMILIA_DEUDA = {KEY1}";
				}
				break;
				///////////////////		OUTPUT		/////////////////////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT		FD.COD_FAMILIA_DEUDA
									  	,FD.NOM_FAMILIA_DEUDA
					          			,CC.NOM_CUENTA_CORRIENTE
						   	FROM 		FAMILIA_DEUDA FD
					          			,CUENTA_CORRIENTE CC
							WHERE     	CC.COD_FAMILIA_DEUDA (+)= FD.COD_FAMILIA_DEUDA
							ORDER BY  	COD_FAMILIA_DEUDA DESC";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql=	"SELECT		FD.COD_FAMILIA_DEUDA
									  	,FD.NOM_FAMILIA_DEUDA
							  			,CC.NOM_CUENTA_CORRIENTE
							FROM 		FAMILIA_DEUDA FD left outer join  CUENTA_CORRIENTE CC on CC.COD_FAMILIA_DEUDA = FD.COD_FAMILIA_DEUDA
							ORDER BY  	FD.COD_FAMILIA_DEUDA DESC";
				}
				break;
				///////////////		AJAX_NOM_FAMILIA_DEUDA	///////////////////
			case 'AJAX_NOM_FAMILIA_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	FD.NOM_FAMILIA_DEUDA
							FROM	FAMILIA_DEUDA FD
							WHERE	FD.NOM_FAMILIA_DEUDA = '{PARAM1}'";
				}
				break;
				///////////////		DW_CUENTA_TIPO_DOCUMENTO_PAGO	///////////////////
			case 'DW_CUENTA_TIPO_DOCUMENTO_PAGO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT COD_CUENTA_TIPO_DOCUMENTO_PAGO
							      ,COD_FAMILIA_DEUDA
							      ,COD_CUENTA_CONTABLE
							      ,COD_TIPO_DOCUMENTO_PAGO
							      ,USAR_CUENTA_CTACTE
							FROM CUENTA_TIPO_DOCUMENTO_PAGO
							WHERE COD_FAMILIA_DEUDA = {KEY1}";
				}
				break;
				
				///////////////		DD_TIPO_DOCUMENTO_PAGO	///////////////////
			case 'DD_TIPO_DOCUMENTO_PAGO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select COD_TIPO_DOCUMENTO_PAGO,
									NOM_TIPO_DOCUMENTO_PAGO
							from TIPO_DOCUMENTO_PAGO
							order by NOM_TIPO_DOCUMENTO_PAGO";
				}
				break;
				
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
			case 'NEW_RECORD_TIPO_DOCUMENTO_PAGO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select COD_TIPO_DOCUMENTO_PAGO
							FROM TIPO_DOCUMENTO_PAGO
							order by COD_TIPO_DOCUMENTO_PAGO";
				}
				break;
			
				//////////////////		ERROR		//////////////////////////////
			default:
				base::error("sql_familia_deuda::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>