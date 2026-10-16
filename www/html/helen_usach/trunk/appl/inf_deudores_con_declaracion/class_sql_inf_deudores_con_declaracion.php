<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_inf_deudores_con_declaracion extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT RUT					  
							   ,NOMBRE                 
							   ,APELLIDO_PATERNO       
							   ,APELLIDO_MATERNO
							   ,DIRECCION
							   ,NOM_COMUNA
							  -- ,NOM_CIUDAD       
							   ,TELEFONO               
							   ,EMAIL                  
							   ,To_Char(FECHA_INGRESO, 'dd/mm/yyyy') FECHA_INGRESO
						FROM  	TEMP_DEUDORES
					   ORDER BY RUT";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "";
				}
			break;
			case 'FECHA' :
				if (K_TIPO_BD=="oci"){
					$sql = "select  {PARAM1} ANO
								,100 COD_ESTADO_DECLARACION
								,100 COD_VIA_DECLARACION
						from 	DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select  {PARAM1} ANO
								,100 COD_ESTADO_DECLARACION
								,100 COD_VIA_DECLARACION";
				}
			break;
			case 'DD_COD_ESTADO_DECLARACION' :
				if (K_TIPO_BD=="oci"){
					$sql = "select COD_ESTADO_DECLARACION
								, NOM_ESTADO_DECLARACION 
							from 		ESTADO_DECLARACION
							union
							select 100 COD_ESTADO_DECLARACION
      								,'TODAS' NOM_ESTADO_DECLARACION
                      		FROM dual
      						order by 	COD_ESTADO_DECLARACION desc";
				}
				else if(K_TIPO_BD=="mssql"){
					$sql = "select COD_ESTADO_DECLARACION
								, NOM_ESTADO_DECLARACION 
							from 		ESTADO_DECLARACION
							union
							select 100 COD_ESTADO_DECLARACION
      								,'TODAS' NOM_ESTADO_DECLARACION
                      		order by 	COD_ESTADO_DECLARACION desc";
				}
			break;
			case 'DD_COD_VIA_DECLARACION' :
				if (K_TIPO_BD=="oci"){
					$sql = "select COD_VIA_DECLARACION
								, NOM_VIA_DECLARACION 
							from 		VIA_DECLARACION
							union
							select 100 COD_VIA_DECLARACION
      								,'TODAS' NOM_VIA_DECLARACION
      						from dual
      						order by 	COD_VIA_DECLARACION desc";
				}
				else if(K_TIPO_BD=="mssql"){
					$sql = "select COD_VIA_DECLARACION
								, NOM_VIA_DECLARACION 
							from 		VIA_DECLARACION
							union
							select 100 COD_VIA_DECLARACION
      								,'TODAS' NOM_VIA_DECLARACION
      						order by 	COD_VIA_DECLARACION desc";
				}
			break;
			////////////////////			ERROR		//////////////////////////////////
			default:
			base::error("sql_inf_deudores_con_declaracion::get_sql, selector: '$selector_sql' no definido");
								
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>