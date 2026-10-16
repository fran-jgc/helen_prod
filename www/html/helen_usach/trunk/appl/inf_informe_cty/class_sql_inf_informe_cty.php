<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_inf_informe_cty extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT   RUT
									,ANO     				  
									,FECHA_VENCTO			  
									,MONTO_INGRESO_DEUDOR           
									,MONTO_INGRESO_CONYUGE
									,NOM_CONCEPTO_COBRO             
									,MONTO_CTY
									,MONTO_CAT
									,MONTO_PEC              
							FROM 	TEMP_INFORME_CTY
							ORDER BY RUT";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "spi_informe_cty";
				}
			break;
			case 'CARTERA' :
				if (K_TIPO_BD=="oci"){
					$sql = "select  '0' COD_CARTERA
						,'CS' COD_TIPO_DEUDA 
						,To_Char(SYSDATE,'yyyy') ANO
						from DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = " select  '0' COD_CARTERA
									,'CS' COD_TIPO_DEUDA 
									,year(getdate()) ANO";
				}
			break;
			case 'DD_COD_CARTERA' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 0 COD_CARTERA
								 ,'TODAS' NOM_CARTERA 
						FROM 	 DUAL UNION 
								 SELECT COD_CARTERA
								 , NOM_CARTERA 
						FROM 	 CARTERA 
						ORDER BY COD_CARTERA";						
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 0 COD_CARTERA
      								,'TODAS' NOM_CARTERA 
							UNION 
							SELECT COD_CARTERA
  									, NOM_CARTERA 
							FROM 	 CARTERA 
							ORDER BY COD_CARTERA";
				}
			break;
			case 'DD_COD_TIPO_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select COD_TIPO_DEUDA
								 , COD_TIPO_DEUDA NOM_TIPO_DEUDA 
						from 	 TIPO_DEUDA 
						where 	COD_TIPO_DEUDA in ('CS', 'CSR') 
						order by COD_TIPO_DEUDA";
				}
			break;		
			////////////////////			ERROR		//////////////////////////////////
			default:
			base::error("sql_inf_informe_cty::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>