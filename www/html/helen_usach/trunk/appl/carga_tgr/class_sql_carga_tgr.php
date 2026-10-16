<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_carga_tgr extends sql_tabla {
	
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			
			//////////////////		INPUT		//////////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= 	"SELECT COD_CARGA_TGR
									,TO_CHAR(FECHA_CARGA_TGR,'dd/mm/yyyy') FECHA_CARGA_TGR
									,TO_CHAR(FECHA_REGISTRO,'dd/mm/yyyy') FECHA_REGISTRO
									,TOTAL_REG_CARGA
									,TOTAL_REG_EXITOSO
									,TOTAL_REG_PDTE							
									,MONTO_REG_CARGA
									,MONTO_REG_EXITOSO	
									,MONTO_REG_PDTE	
									,'' OCULTA_PROCESAR
									,0 COD_ORIGEN_PAGO
									,0 COD_ORIGEN_PAGO_H 
									,U.NOM_USUARIO
							FROM CARGA_TGR C, USUARIO U 
							WHERE COD_CARGA_TGR ={KEY1}
							AND C.COD_USUARIO = U.COD_USUARIO";
				}
			break;
			
			/////////////////////		OUTPUT		///////////////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT	COD_CARGA_TGR 
									,TO_CHAR(FECHA_CARGA_TGR,'dd/mm/yyyy') FECHA_CARGA_TGR
									,TO_CHAR(FECHA_REGISTRO,'dd/mm/yyyy') FECHA_REGISTRO
									,TOTAL_REG_CARGA
									,TOTAL_REG_EXITOSO
									,TOTAL_REG_PDTE
									,CB.COD_USUARIO
									,U.NOM_USUARIO
							FROM CARGA_TGR CB, USUARIO U	
							WHERE CB.COD_USUARIO=U.COD_USUARIO
							ORDER BY COD_CARGA_TGR desc";
				}
			break;
				//////////////////		INPUT		//////////////////////
			case 'ITEM_CARGA_TGR' :
				if (K_TIPO_BD=="oci"){
					$sql= 	"select CB.COD_CARGA_TGR
									,CB.TOTAL_REG_CARGA
									,CB.TOTAL_REG_EXITOSO
									,CB.TOTAL_REG_PDTE
									,TO_CHAR(CB.FECHA_CARGA_TGR,'dd/mm/yyyy') FECHA_CARGA_TGR
									,IT.COD_ESTADO_CARGA
									,EC.NOM_ESTADO_CARGA
									,0 NRO_ITEM_CARGA_ORSAN
									,IT.COD_ITEM_CARGA_TGR COD_ITEM_CARGA_TGR_H
									,IT.RUT 
									,TO_CHAR(IT.FECHA_PAGO, 'dd/mm/yyyy') FECHA_PAGO
									,IT.MONTO_PESOS 
									,IT.FOLIO
									,IT.COD_ITEM_CARGA_TGR
									FROM ITEM_CARGA_TGR IT 
									 	left OUTER JOIN CARGA_TGR CB  ON  IT.COD_CARGA_TGR= CB.COD_CARGA_TGR
									 	left OUTER JOIN ESTADO_CARGA EC  ON  IT.COD_ESTADO_CARGA= EC.COD_ESTADO_CARGA
					                WHERE IT.COD_CARGA_TGR= {KEY1}
					                AND IT.COD_CARGA_TGR= CB.COD_CARGA_TGR
					                AND IT.COD_ESTADO_CARGA= EC.COD_ESTADO_CARGA
									ORDER BY fecha_pago, cod_item_carga_tgr";
				}
			break;
			
			/////////////////////////////		ERROR			/////////////////////////////////////
			default:
			base::error("sql_carga_tgr::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>