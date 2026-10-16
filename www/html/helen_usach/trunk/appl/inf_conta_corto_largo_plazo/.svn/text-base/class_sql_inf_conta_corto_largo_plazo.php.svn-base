<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_inf_conta_corto_largo_plazo extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "RUT			
						,DIG_VERIF		
						,INICIO_COBRO	
						,EMITIDA_2008	
						,ABONO_2008			
						,DESCTO_2008	
						,CONVENIO_2008	
						,SALDO_2008		
						,CUOTA_2009		
						,EMITIDA_2009	
						,ABONO_2009		
						,DESCTO_2009	
						,CONVENIO_2009	
						,SALDO_2009	    
						,CUOTAS_LP			
						,EMITIDAS_LP		
						,ABONO_LP			
						,DESCTO_LP		
						,CONVENIO_LP	
						,SALGO_LP
						FROM TEMPO
						ORDER BY 	RUT";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "";
				}
			break;
			case 'FECHA' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	To_Char(SYSDATE,'YYYY') ANO
									,'' COD_TIPO_DEUDA
									,null COD_CARTERA
							FROM 	DUAL";						
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	datepart(YYYY, getdate()) ANO
									,'' COD_TIPO_DEUDA
									,null COD_CARTERA";
				}
			break;
			case 'DD_COD_TIPO_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT		COD_TIPO_DEUDA 
										, COD_TIPO_DEUDA TIPO_DEUDA
							FROM 		TIPO_DEUDA
							ORDER BY	NOM_TIPO_DEUDA";
				}
			break;				
			//////////////////////		DD_COD_CARTERA	///////////////////
			case 'DD_COD_CARTERA' :
				if (K_TIPO_BD=="oci")
					$sql = "SELECT	0 			COD_CARTERA
									,'Todas' 	NOM_CARTERA
							from DUAL
							union
							SELECT		COD_CARTERA
										, NOM_CARTERA
							FROM 		CARTERA
							ORDER BY	COD_CARTERA";
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	0 			COD_CARTERA
									,'Todas' 	NOM_CARTERA
							union
							SELECT		COD_CARTERA
										, NOM_CARTERA
							FROM 		CARTERA
							ORDER BY	COD_CARTERA";
				}
			break;
			////////////////////			ERROR		//////////////////////////////////
			default:
			base::error("sql_conta_corto_largo_plazo::get_sql, selector: '$selector_sql' no definido");		
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>