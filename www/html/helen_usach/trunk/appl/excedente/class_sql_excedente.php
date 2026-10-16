<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	


class sql_excedente_base extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			/////////////////		INPUT		///////////////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT  		E.COD_EXCEDENTE
										,TO_CHAR(E.FECHA_EXCEDENTE,'dd/mm/yyyy') FECHA_EXCEDENTE
										,E.COD_DEUDOR
										,D.RUT
										,D.DIG_VERIF
										,D.NOMBRE||' '||D.APELLIDO_PATERNO||' '||D.APELLIDO_MATERNO NOMBRE
										,E.COD_ESTADO_EXCEDENTE
										,TO_CHAR(E.FECHA_EGRESO,'dd/mm/yyyy') FECHA_EGRESO
										,E.COD_USUARIO
										,U.NOM_USUARIO
										,E.COD_COMPROBANTE
										,E.COD_ABONO
										,E.MONTO_EXCEDENTE
										,E.COD_CUENTA_CORRIENTE
										,E.NRO_CHEQUE
							FROM    	EXCEDENTE E, USUARIO U, DEUDOR D
							WHERE   	E.COD_EXCEDENTE = {KEY1} AND
										E.COD_USUARIO = U.COD_USUARIO (+) AND
										E.COD_DEUDOR = D.COD_DEUDOR";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT  		E.COD_EXCEDENTE
										,convert(varchar(20),E.FECHA_EXCEDENTE, 103) FECHA_EXCEDENTE
										,E.COD_DEUDOR
										,D.RUT
										,D.DIG_VERIF
										,D.NOMBRE + ' ' + D.APELLIDO_PATERNO + ' ' + D.APELLIDO_MATERNO NOMBRE
										,E.COD_ESTADO_EXCEDENTE
										,convert(varchar(20),E.FECHA_EGRESO,103) FECHA_EGRESO
										,E.COD_USUARIO
										,U.NOM_USUARIO
										,E.COD_COMPROBANTE
										,E.COD_ABONO
										,E.MONTO_EXCEDENTE
										,E.COD_CUENTA_CORRIENTE
										,E.NRO_CHEQUE
							FROM    	EXCEDENTE E left outer join USUARIO U on E.COD_USUARIO = U.COD_USUARIO, DEUDOR D
							WHERE   	E.COD_EXCEDENTE = {KEY1} AND
										E.COD_DEUDOR = D.COD_DEUDOR";
				}
			break;
				
			////////////////////		OUTPUT		//////////////////////	
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT 		E.COD_EXCEDENTE
										/*INI_CAMPOS*/
										, D.RUT
										, D.DIG_VERIF
										, D.NOMBRE
										, D.APELLIDO_PATERNO
										, D.APELLIDO_MATERNO
										, E.MONTO_EXCEDENTE
										, EE.NOM_ESTADO_EXCEDENTE
                    					, To_Char(FECHA_EGRESO,'dd/mm/yyyy') FECHA_EGRESO
                    					, AI.COD_INGRESO
                    					, E.COD_ESTADO_EXCEDENTE
                    					, E.COD_ABONO
                    					, ( SELECT ORIGEN_PAGO.NOM_ORIGEN_PAGO FROM ABONO, ORIGEN_PAGO WHERE ABONO.COD_ABONO = E.COD_ABONO AND ORIGEN_PAGO.COD_ORIGEN_PAGO = ABONO.COD_ORIGEN_PAGO ) ORIGEN_PAGO
										, ( SELECT ABONO.COD_DEUDA FROM ABONO WHERE ABONO.COD_ABONO = E.COD_ABONO  ) COD_DEUDA
                    					/*FIN_CAMPOS*/
							FROM    	EXCEDENTE E left outer join ABONO_INGRESO AI on E.COD_ABONO=AI.COD_ABONO
										, DEUDOR D, ESTADO_EXCEDENTE EE
							WHERE		E.COD_DEUDOR = D.COD_DEUDOR AND
										E.COD_ESTADO_EXCEDENTE = EE.COD_ESTADO_EXCEDENTE AND
										E.MONTO_EXCEDENTE > 0
										/*FIN_CONDICION*/
							ORDER BY  	E.COD_EXCEDENTE DESC";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT 		E.COD_EXCEDENTE
										/*INI_CAMPOS*/
										, D.RUT
										, D.DIG_VERIF
										, D.NOMBRE
										, D.APELLIDO_PATERNO
										, D.APELLIDO_MATERNO
										, E.MONTO_EXCEDENTE
										, EE.NOM_ESTADO_EXCEDENTE
                    					, convert(varchar(20), FECHA_EGRESO, 103) FECHA_EGRESO
                    					, AI.COD_INGRESO
                    					, E.COD_ESTADO_EXCEDENTE
                    					, ( SELECT ORIGEN_PAGO.NOM_ORIGEN_PAGO FROM ABONO, ORIGEN_PAGO WHERE ABONO.COD_ABONO = E.COD_ABONO AND ORIGEN_PAGO.COD_ORIGEN_PAGO = ABONO.COD_ORIGEN_PAGO ) ORIGEN_PAGO
										, ( SELECT ABONO.COD_DEUDA FROM ABONO WHERE ABONO.COD_ABONO = E.COD_ABONO  ) COD_DEUDA
                    					/*FIN_CAMPOS*/
							FROM    	EXCEDENTE E left outer join ABONO_INGRESO AI on E.COD_ABONO=AI.COD_ABONO
										, DEUDOR D, ESTADO_EXCEDENTE EE
							WHERE		E.COD_DEUDOR = D.COD_DEUDOR AND
										E.COD_ESTADO_EXCEDENTE = EE.COD_ESTADO_EXCEDENTE
										/*FIN_CONDICION*/
							ORDER BY  	E.COD_EXCEDENTE DESC";
				}	
			break;
				
			
			
			
			
			///////////////// PRINT EXCEDENTE /////////////////	
			case 'PRINT_RECORD' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT  	E.COD_EXCEDENTE
										,TO_CHAR(E.FECHA_EXCEDENTE,'dd/mm/yyyy') FECHA_EXCEDENTE
										,E.COD_DEUDOR
										,D.RUT
										,D.DIG_VERIF 
										,D.NOMBRE||' '||D.APELLIDO_PATERNO||' '||D.APELLIDO_MATERNO NOMBRE
										,EE.NOM_ESTADO_EXCEDENTE
										,TO_CHAR(E.FECHA_EGRESO,'dd/mm/yyyy') FECHA_EGRESO
										,E.COD_USUARIO
										,U.NOM_USUARIO
										,E.COD_COMPROBANTE
										,E.COD_ABONO
										,E.MONTO_EXCEDENTE
										,CC.NOM_CUENTA_CORRIENTE
										,E.NRO_CHEQUE
							FROM    	EXCEDENTE E LEFT OUTER JOIN CUENTA_CORRIENTE CC ON CC.COD_CUENTA_CORRIENTE= E.COD_CUENTA_CORRIENTE
													LEFT OUTER JOIN USUARIO U ON E.COD_USUARIO = U.COD_USUARIO
										, DEUDOR D, ESTADO_EXCEDENTE EE
							WHERE   	E.COD_EXCEDENTE = {PARAM1} AND
										E.COD_DEUDOR = D.COD_DEUDOR AND
										EE.COD_ESTADO_EXCEDENTE= E.COD_ESTADO_EXCEDENTE";
				}
			
			else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT  		E.COD_EXCEDENTE COD_EXCEDENTE
										,convert(varchar(20),E.FECHA_EXCEDENTE, 103) FECHA_EXCEDENTE
										,D.RUT 
										,D.DIG_VERIF  
										,D.NOMBRE + ' ' + D.APELLIDO_PATERNO + ' ' + D.APELLIDO_MATERNO NOMBRE
										,E.COD_ABONO
										,E.MONTO_EXCEDENTE
										,EE.NOM_ESTADO_EXCEDENTE
										,convert(varchar(20),E.FECHA_EGRESO,103) FECHA_EGRESO
										,E.NRO_CHEQUE
										,U.NOM_USUARIO
										,E.COD_COMPROBANTE
										,C.NOM_CUENTA_CORRIENTE
							FROM    	EXCEDENTE E left outer join USUARIO U on E.COD_USUARIO = U.COD_USUARIO
													left outer join CUENTA_CORRIENTE C on E.COD_CUENTA_CORRIENTE = C.COD_CUENTA_CORRIENTE
													, DEUDOR D,ESTADO_EXCEDENTE EE 	
							WHERE   	E.COD_EXCEDENTE ={PARAM1} AND
										E.COD_DEUDOR = D.COD_DEUDOR
										AND E.COD_ESTADO_EXCEDENTE = EE.COD_ESTADO_EXCEDENTE";
				}
			break;
			
			//////////////// DD_COD_ESTADO_EXCEDENTE /////////////////	
			case 'DD_COD_ESTADO_EXCEDENTE' :
					if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT 	COD_ESTADO_EXCEDENTE
										,NOM_ESTADO_EXCEDENTE 
							FROM 		ESTADO_EXCEDENTE 
							WHERE COD_ESTADO_EXCEDENTE IN (1,2) /* EMITIDO Y CON EGRESO*/
							ORDER BY 	COD_ESTADO_EXCEDENTE";
							
							
					
								
											
							
				}
			break;
			///////////////// DD_COD_CUENTA_CORRIENTE /////////////////	
			case 'DD_COD_CUENTA_CORRIENTE' :
					if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT 	COD_CUENTA_CORRIENTE
										,NOM_CUENTA_CORRIENTE
							FROM 		CUENTA_CORRIENTE
							ORDER BY 	NOM_CUENTA_CORRIENTE";
				}
			break;
			/////////////////  OBTIENE_USUARIO  ///////////////
				case 'OBTIENE_USUARIO' :
				if (K_TIPO_BD=="oci" ){
					$sql = "SELECT 	NOM_USUARIO, To_Char(SYSDATE, 'DD/MM/YYYY')FECHA_EGRESO 
							FROM 	usuario 
							WHERE 	COD_USUARIO={PARAM1}";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 	NOM_USUARIO, convert(varchar(20), getdate(), 103)FECHA_EGRESO 
							FROM 	usuario 
							WHERE 	COD_USUARIO={PARAM1}";
				}
				break;	

			///////////////		ERROR		/////////////////////
			default:
			base::error("sql_excedente::get_sql, selector: '$selector_sql' no definido");
				
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
		
			
	}
}
/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_sql_excedente.php";
if (file_exists($file_name))
    require_once($file_name);
else {
    class sql_excedente extends sql_excedente_base {
    }
}
?>