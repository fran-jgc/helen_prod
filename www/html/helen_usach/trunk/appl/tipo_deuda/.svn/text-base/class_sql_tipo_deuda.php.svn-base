<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_tipo_deuda extends sql_tabla {
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			/////////////////  INPUT  ///////////////
			case 'INPUT' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT  CTD.COD_CUENTA_TIPO_DEUDA,
									 CNCTA.COD_CONCEPTO_CUENTA,
						 			 CNCTA.NOM_CONCEPTO_CUENTA,
						 			 CC.COD_CUENTA_CONTABLE,
						 			 CC.NOM_CUENTA_CONTABLE,
						 			 TD.COD_TIPO_DEUDA COD_TIPO_DEUDA_H,
						 			 0 NUEVO
				 			FROM 	 CONCEPTO_CUENTA CNCTA,CUENTA_CONTABLE CC, TIPO_DEUDA TD, CUENTA_TIPO_DEUDA  CTD
				 			WHERE 	 CTD.COD_TIPO_DEUDA=TD.COD_TIPO_DEUDA 
									 AND CTD.COD_CONCEPTO_CUENTA= CNCTA.COD_CONCEPTO_CUENTA
						 	   		 AND CTD.COD_CUENTA_CONTABLE=CC.COD_CUENTA_CONTABLE(+) 
						 			 AND CTD.COD_TIPO_DEUDA='{KEY1}'
							ORDER BY CNCTA.COD_CONCEPTO_CUENTA";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql="SELECT		CTD.COD_CUENTA_TIPO_DEUDA,
										CNCTA.COD_CONCEPTO_CUENTA,
										CNCTA.NOM_CONCEPTO_CUENTA,
										CC.COD_CUENTA_CONTABLE,
										CC.NOM_CUENTA_CONTABLE,
										TD.COD_TIPO_DEUDA COD_TIPO_DEUDA_H,
										0 NUEVO
							FROM 		CUENTA_TIPO_DEUDA  CTD left outer join CUENTA_CONTABLE CC on CTD.COD_CUENTA_CONTABLE=CC.COD_CUENTA_CONTABLE
										,CONCEPTO_CUENTA CNCTA
										,TIPO_DEUDA TD
							WHERE 		CTD.COD_TIPO_DEUDA=TD.COD_TIPO_DEUDA 
										AND CTD.COD_CONCEPTO_CUENTA= CNCTA.COD_CONCEPTO_CUENTA
										AND CTD.COD_TIPO_DEUDA='{KEY1}'
							ORDER BY	CNCTA.COD_CONCEPTO_CUENTA";
				}
				break;
					
				/////////////////  INPUT_DD_CUENTA ///////////////
			case 'INPUT_DD_CUENTA' :
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

				/////////////////  INPUT_TIPO_DEUDA   ///////////////
			case 'INPUT_TIPO_DEUDA' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT		TD.COD_TIPO_DEUDA
										, TD.NOM_TIPO_DEUDA									
										, TD.COD_MONEDA
										, TD.COD_FAMILIA_DEUDA
										, TD.CANT_CUOTA
										, TD.MONTO_NO_COBRABLE
										, TD.TASA_INTERES_CREDITO
										, TD.TASA_INTERES_PENAL
										, TD.PERIODO_GRACIA
										, f_get_td_convenio (TD.COD_TIPO_DEUDA, 9999) CONVENIO_FUTURA
				                    	, f_get_td_convenio (TD.COD_TIPO_DEUDA, 9998) CONVENIO_NO_EXIGIBLE
				                    	, f_get_td_convenio (TD.COD_TIPO_DEUDA, 9997) CONVENIO_PRONTO_PAGO
				                    	, M.SIMBOLO
							FROM		TIPO_DEUDA TD, MONEDA M
							WHERE		TD.COD_TIPO_DEUDA = '{KEY1}'
									AND M.COD_MONEDA = TD.COD_MONEDA
							ORDER BY	COD_TIPO_DEUDA desc";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT		TD.COD_TIPO_DEUDA
										, TD.NOM_TIPO_DEUDA									
										, TD.COD_MONEDA
										, TD.COD_FAMILIA_DEUDA
										, TD.CANT_CUOTA
										, TD.MONTO_NO_COBRABLE
										, TD.TASA_INTERES_CREDITO
										, TD.TASA_INTERES_PENAL
										, TD.PERIODO_GRACIA
										, dbo.f_get_td_convenio (TD.COD_TIPO_DEUDA, 9999) CONVENIO_FUTURA
				                    	, dbo.f_get_td_convenio (TD.COD_TIPO_DEUDA, 9998) CONVENIO_NO_EXIGIBLE
				                    	, dbo.f_get_td_convenio (TD.COD_TIPO_DEUDA, 9997) CONVENIO_PRONTO_PAGO
				                    	, M.SIMBOLO
							FROM		TIPO_DEUDA TD, MONEDA M
							WHERE		TD.COD_TIPO_DEUDA = '{KEY1}'
									AND M.COD_MONEDA = TD.COD_MONEDA
							ORDER BY	COD_TIPO_DEUDA desc";
				}
				break;
					
				/////////////////  INPUT_DD_MONEDA  ///////////////
			case 'INPUT_DD_MONEDA' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT		COD_MONEDA
										, NOM_MONEDA
							FROM		MONEDA
							ORDER BY	NOM_MONEDA";
				}
				break;

				/////////////////  INPUT_DD_FAMILIA  ///////////////
			case 'INPUT_DD_FAMILIA' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_FAMILIA_DEUDA
									,NOM_FAMILIA_DEUDA
							FROM	FAMILIA_DEUDA";
				}
				break;

				/////////////////  INPUT_RESULT  ///////////////
			case 'INPUT_RESULT' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT		'' COD_CUENTA_TIPO_DEUDA
										, CNCTA.COD_CONCEPTO_CUENTA
										, CNCTA.NOM_CONCEPTO_CUENTA
										, '' COD_CUENTA_CONTABLE
										, '' NOM_CUENTA_CONTABLE
										, '' COD_TIPO_DEUDA_H
										, 1 NUEVO
							FROM		CONCEPTO_CUENTA CNCTA
							WHERE		NOM_CONCEPTO_CUENTA <> '--DISPONIBLE--'							
							ORDER BY	CNCTA.COD_CONCEPTO_CUENTA";
				}
				break;

				/////////////////  OUTPUT  ///////////////
			case 'OUTPUT' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_TIPO_DEUDA,
                  					NOM_TIPO_DEUDA,  
                  					NOM_FAMILIA_DEUDA,
                  					NOM_MONEDA
            				FROM 	TIPO_DEUDA TD, FAMILIA_DEUDA FD, MONEDA M
             				WHERE 	TD.COD_FAMILIA_DEUDA = FD.COD_FAMILIA_DEUDA AND
                    				TD.COD_MONEDA = M.COD_MONEDA
   			       			ORDER BY  COD_TIPO_DEUDA DESC";
				}
				break;
			
				/////////////////  OUTPUT_UCM  ///////////////
			case 'OUTPUT_UCM' :
				/////////////////  ORACLE  -  (familia 1 y 2)  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_TIPO_DEUDA,
                  					NOM_TIPO_DEUDA,  
                  					NOM_FAMILIA_DEUDA,
                  					NOM_MONEDA
            				FROM 	TIPO_DEUDA TD, FAMILIA_DEUDA FD, MONEDA M
             				WHERE 	TD.COD_FAMILIA_DEUDA IN (1, 2)
             				AND		TD.COD_FAMILIA_DEUDA = FD.COD_FAMILIA_DEUDA 
             				AND		TD.COD_MONEDA = M.COD_MONEDA
   			       			ORDER BY  COD_TIPO_DEUDA DESC";
				}
				break;
				
				/////////////////  OUTPUT_UCM2  ///////////////
			case 'OUTPUT_UCM2' :
				/////////////////  ORACLE  -  (familia 3)  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_TIPO_DEUDA,
                  					NOM_TIPO_DEUDA,  
                  					NOM_FAMILIA_DEUDA,
                  					NOM_MONEDA
            				FROM 	TIPO_DEUDA TD, FAMILIA_DEUDA FD, MONEDA M
             				WHERE 	TD.COD_FAMILIA_DEUDA = 3
             				AND		TD.COD_FAMILIA_DEUDA = FD.COD_FAMILIA_DEUDA
             				AND		TD.COD_MONEDA = M.COD_MONEDA
   			       			ORDER BY  COD_TIPO_DEUDA DESC";
				}
				break;
				
				////////////////////////	DD_MONEDA		///////////////////
			case 'DD_MONEDA' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_MONEDA
									,NOM_MONEDA
							FROM 	MONEDA 
							ORDER BY COD_MONEDA";
				}
				break;
				////////////////////////	DD_FAMILIA_DEUDA		///////////////////
			case 'DD_FAMILIA_DEUDA' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 		COD_FAMILIA_DEUDA
										,NOM_FAMILIA_DEUDA 
										FROM FAMILIA_DEUDA 
							ORDER BY	COD_FAMILIA_DEUDA";
				}
				break;
			case 'DW_CONVENIO_BDD' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT  COD_CONVENIO_BDD		COD_CONVENIO_BDD_H
								        ,COD_TIPO_DEUDA 	COD_TIPO_DEUDA_CON_H
										,CONVENIO
										,CONVENIO			CONVENIO_A
										,ANO										
										,CON_DECLARACION
							FROM 		CONVENIO_BDD
							WHERE   	COD_TIPO_DEUDA ='{KEY1}'
							AND			ANO <>9999
							AND			ANO <>9998
							AND			ANO <>9997";
				}
				break;
				///////////////		AJAX_COD_TIPO_DEUDA	///////////////////
			case 'AJAX_COD_TIPO_DEUDA' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_TIPO_DEUDA
							FROM	TIPO_DEUDA
							WHERE	COD_TIPO_DEUDA = '{PARAM1}'";
				}
				break;
				///////////////		AJAX_NOM_TIPO_DEUDA	///////////////////
			case 'AJAX_NOM_TIPO_DEUDA' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	NOM_TIPO_DEUDA
							FROM	TIPO_DEUDA
							WHERE	NOM_TIPO_DEUDA = '{PARAM1}'";
				}
				break;
					
				/////////////////  ERROR ///////////////
			default:
				base::error("sql_tipo_deuda::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>