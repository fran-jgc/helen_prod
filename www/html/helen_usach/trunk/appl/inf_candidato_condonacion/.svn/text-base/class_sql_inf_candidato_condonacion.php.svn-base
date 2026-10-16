<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_inf_candidato_condonacion extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT x.RUT
							      ,x.DIG_VERIF
							      ,x.COD_TIPO_DEUDA
							      ,x.ANO_INICIO
							      ,x.MONTO_INICIO_COBRO
							      ,x.NOM_ESTADO_DEUDA
							      ,x.NOM_CARTERA
							      ,x.NOM_UNIVERSIDAD
							      ,x.SALDO_99 SALDO_FUT
							      ,x.EMAIL
							      ,x.ANO_ULTIMO_PP
							FROM TEMP_CANDIDATO_CONDONACION x 
							WHERE ES_CAND_CONDONA = 'S'
							ORDER BY RUT";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "";
				}
			break;
			
			case 'PARAMETRO_CONDONACION':
					$sql = "select  '01/01/{PARAM1}' FECHA_INICIO
									,'31/12/{PARAM1}' FECHA_TERMINO
									,'' R_CONDONACION
									,'' TODAS
									,'' TIPO_CONDONACION
				  		from  		DUAL";
			break;
			
			///////////////////		ERROR		///////////////////////////////
			default:
			base::error("sql_inf_candidato_condonacion::get_sql, selector: '$selector_sql' no definido");						
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>