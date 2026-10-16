<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_pagare extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			/////////////////  INPUT   ///////////////
			case 'INPUT' :
				/////////////////  ORACLE ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	P.COD_PAGARE
									, P.FOLIO_PAGARE
									, TO_CHAR(P.FECHA_PAGARE,'dd/mm/yyyy') FECHA_PAGARE
									, DD.RUT
									, DD.DIG_VERIF
									, DD.NOMBRE||' '||DD.APELLIDO_PATERNO||' '||DD.APELLIDO_MATERNO NOMBRE
									, M.NOM_MONEDA
									, P.COD_DEUDA
									, TD.COD_TIPO_DEUDA COD_TIPO_DEUDA
									, TD.NOM_TIPO_DEUDA TIPO_DEUDA
									, P.MONTO_MONEDA
									, P.MONTO_PESOS
									, EP.COD_ESTADO_PAGARE
									, D.COD_ESTADO_DEUDA ESTADO_DEUDA_H
									, TO_CHAR(P.FECHA_PROTESTO,'dd/mm/yyyy') FECHA_PROTESTO
				                  	, TO_CHAR(P.FECHA_DEVOLUCION,'dd/mm/yyyy') FECHA_DEVOLUCION
				                  	, DEVOLUCION_A
				                  	, NOM_USUARIO NOM_USUARIO_DEVOLUCION
							FROM	PAGARE P left outer join USUARIO UD ON UD.COD_USUARIO = P.COD_USUARIO_DEVOLUCION 
									, DEUDA D
									, DEUDOR DD
									, TIPO_DEUDA TD
									, ESTADO_PAGARE EP
									, MONEDA M
							WHERE	P.COD_DEUDA = D.COD_DEUDA
							AND		D.COD_DEUDOR = DD.COD_DEUDOR
							AND		D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
							AND		P.COD_ESTADO_PAGARE = EP.COD_ESTADO_PAGARE
							AND		TD.COD_MONEDA = M.COD_MONEDA
							AND		COD_PAGARE = {KEY1}";
				}
				///////////////// SQL-SERVER  ///////////////
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	P.COD_PAGARE
									, P.FOLIO_PAGARE
									, convert(varchar(20), P.FECHA_PAGARE,103) FECHA_PAGARE
									, DD.RUT
									, DD.DIG_VERIF
									, DD.NOMBRE + ' ' + DD.APELLIDO_PATERNO + ' ' + DD.APELLIDO_MATERNO NOMBRE
									, M.NOM_MONEDA
									, P.COD_DEUDA
									, TD.COD_TIPO_DEUDA COD_TIPO_DEUDA
									, TD.NOM_TIPO_DEUDA TIPO_DEUDA
									, P.MONTO_MONEDA
									, P.MONTO_PESOS
									, EP.COD_ESTADO_PAGARE
									, D.COD_ESTADO_DEUDA ESTADO_DEUDA_H
									, convert(varchar(20), P.FECHA_PROTESTO,103) FECHA_PROTESTO
									, convert(varchar(20), P.FECHA_DEVOLUCION, 103) FECHA_DEVOLUCION
				                  	, DEVOLUCION_A
				                  	, NOM_USUARIO NOM_USUARIO_DEVOLUCION
							FROM	PAGARE P left outer join USUARIO UD ON UD.COD_USUARIO = P.COD_USUARIO_DEVOLUCION
									, DEUDA D
									, DEUDOR DD
									, TIPO_DEUDA TD
									, ESTADO_PAGARE EP
									, MONEDA M
							WHERE	P.COD_DEUDA = D.COD_DEUDA
							AND		D.COD_DEUDOR = DD.COD_DEUDOR
							AND		D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
							AND		P.COD_ESTADO_PAGARE = EP.COD_ESTADO_PAGARE
							AND		TD.COD_MONEDA = M.COD_MONEDA
							AND		COD_PAGARE = {KEY1}";
				}
			break;
			
			/////////////////  OUTPUT  ///////////////
			case 'OUTPUT' :
				/////////////////  ORACLE  ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	P.COD_PAGARE
									, DD.RUT
									/*INI_CAMPOS*/
									, P.FOLIO_PAGARE
									, TO_CHAR(P.FECHA_PAGARE,'dd/mm/yyyy') FECHA_PAGARE
									, DD.DIG_VERIF
									, P.COD_DEUDA
									, TD.NOM_TIPO_DEUDA
									, TD.COD_TIPO_DEUDA
									, P.MONTO_MONEDA
									, P.MONTO_PESOS
									, EP.NOM_ESTADO_PAGARE
									, TO_CHAR(P.FECHA_PROTESTO,'dd/mm/yyyy') FECHA_PROTESTO
									/*FIN_CAMPOS*/
							FROM	PAGARE P left outer join ESTADO_PAGARE EP on P.COD_ESTADO_PAGARE = EP.COD_ESTADO_PAGARE
									, DEUDA D
									, DEUDOR DD
									, TIPO_DEUDA TD
							WHERE	P.COD_DEUDA = D.COD_DEUDA
							AND		D.COD_DEUDOR = DD.COD_DEUDOR
							AND		D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
							/*FIN_CONDICION*/
							ORDER BY DD.RUT desc";
				}
				/////////////////  SQL-SERVER  ///////////////
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	P.COD_PAGARE
									, DD.RUT
									/*INI_CAMPOS*/
									, P.FOLIO_PAGARE
									, CONVERT(VARCHAR(20),P.FECHA_PAGARE,103) FECHA_PAGARE
									, DD.DIG_VERIF
									, P.COD_DEUDA
									, TD.NOM_TIPO_DEUDA
									, TD.COD_TIPO_DEUDA
									, P.MONTO_MONEDA
									, P.MONTO_PESOS
									, EP.NOM_ESTADO_PAGARE
									, CONVERT(VARCHAR(20),P.FECHA_PROTESTO,103) FECHA_PROTESTO
									/*FIN_CAMPOS*/
							FROM	PAGARE P left outer join ESTADO_PAGARE EP on P.COD_ESTADO_PAGARE = EP.COD_ESTADO_PAGARE
									, DEUDA D
									, DEUDOR DD
									, TIPO_DEUDA TD
							WHERE	P.COD_DEUDA = D.COD_DEUDA
							AND		D.COD_DEUDOR = DD.COD_DEUDOR
							AND		D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
							/*FIN_CONDICION*/
							ORDER BY DD.RUT desc";
				}
			break;
			//sentencia para drop_down_dw en wi_pagare
			
			/////////////////  INPUT_DD_ESTADO  ///////////////
			case 'INPUT_DD_ESTADO' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_ESTADO_PAGARE
									, NOM_ESTADO_PAGARE
							FROM	ESTADO_PAGARE";
				}
			break;
			
			/////////////////  OUTPUT_ESTADO_PAGARE  ///////////////
			case 'OUTPUT_ESTADO_PAGARE' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT COD_ESTADO_PAGARE 
									,NOM_ESTADO_PAGARE 
							FROM 	ESTADO_PAGARE 
							ORDER BY	COD_ESTADO_PAGARE";
				}
			break;
			/////////////////////	DD_TIPO_DEUDA	///////////////	
			case 'DD_TIPO_DEUDA' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 		COD_TIPO_DEUDA
										,NOM_TIPO_DEUDA 
							FROM 		TIPO_DEUDA 
							ORDER BY	COD_TIPO_DEUDA";
				}
			break;
			
			case 'PRINT_RECORD' :
				if (K_TIPO_BD=="oci"){
					if(K_CLIENTE == 'USACH'){
					$sql=	"SELECT DD.COD_DEUDOR
								   ,DD.RUT
							       ,DD.DIG_VERIF
							       ,DD.NOMBRE 
							       ,DD.APELLIDO_PATERNO  
							       ,DD.APELLIDO_MATERNO 
							       ,PA.FOLIO_PAGARE
							       ,PA.FECHA_PAGARE
							       ,PA.COD_DEUDA
							       ,PA.COD_UNIVERSIDAD
							       ,(select nom_universidad from universidad
							         where cod_universidad = PA.COD_UNIVERSIDAD) NOM_UNIVERSIDAD
							       ,(select direccion from universidad
							         where cod_universidad = PA.COD_UNIVERSIDAD) DIRECCION_UNIVERSIDAD
							       ,PA.MONTO_MONEDA
							       ,PA.MONTO_PESOS
							       ,PA.FOLIO_PAGARE_SUSPENSION
							       ,PA.FECHA_PAGARE_SUSPENSION
							       ,PA.ANO_SUSPENSION
							 FROM PAGARE PA, DEUDA DE, DEUDOR DD
							 WHERE PA.COD_DEUDA = DE.COD_DEUDA
							 AND DE.COD_DEUDOR = DD.COD_DEUDOR
							 AND PA.COD_PAGARE= {PARAM1}";
					}
				}
			break;
			
			//////////////// PRINT_PARAM ////////////////
			case 'PRINT_PARAM' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	f_get_parametro(5) NOM_UNIVERSIDAD
					                ,f_get_parametro(13) MAIL_UNIVERSIDAD
					                ,f_get_parametro(9) DIR_UNIVERSIDAD
					                ,f_get_parametro(10) FONO_UNIVERSIDAD
					                ,f_get_parametro(15) FAX_UNIVERSIDAD
							FROM 	DUAL";
				}else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	dbo.f_get_parametro(5) NOM_UNIVERSIDAD
					                ,dbo.f_get_parametro(13) MAIL_UNIVERSIDAD
					                ,dbo.f_get_parametro(9) DIR_UNIVERSIDAD
					                ,dbo.f_get_parametro(10) FONO_UNIVERSIDAD
					                ,dbo.f_get_parametro(15) FAX_UNIVERSIDAD";
				}
			break;
			
			
			/////////////////  ERROR ///////////////
			default: 
			base::error("sql_pagare::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>