<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_carga_otra_ues extends sql_tabla {
	
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			
			//////////////////		INPUT		//////////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= 	"SELECT COD_CARGA_OTRA_UES
									,TO_CHAR(FECHA_CARGA_OTRA_UES,'dd/mm/yyyy') FECHA_CARGA_OTRA_UES
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
							FROM CARGA_OTRA_UES C, USUARIO U 
							WHERE COD_CARGA_OTRA_UES ={KEY1}
							AND C.COD_USUARIO = U.COD_USUARIO";
				}
			break;
			
			/////////////////////		OUTPUT		///////////////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT	COD_CARGA_OTRA_UES 
									,TO_CHAR(FECHA_CARGA_OTRA_UES,'dd/mm/yyyy') FECHA_CARGA_OTRA_UES
									,TO_CHAR(FECHA_REGISTRO,'dd/mm/yyyy') FECHA_REGISTRO
									,TOTAL_REG_CARGA
									,TOTAL_REG_EXITOSO
									,TOTAL_REG_PDTE
									,CB.COD_USUARIO
									,U.NOM_USUARIO
							FROM CARGA_OTRA_UES CB, USUARIO U	
							WHERE CB.COD_USUARIO=U.COD_USUARIO
							ORDER BY COD_CARGA_OTRA_UES desc";
				}
			break;
				//////////////////		INPUT		//////////////////////
			case 'ITEM_CARGA_OTRA_UES' :
				if (K_TIPO_BD=="oci"){
					$sql= 	"select CB.COD_CARGA_OTRA_UES
									,CB.TOTAL_REG_CARGA
									,CB.TOTAL_REG_EXITOSO
									,CB.TOTAL_REG_PDTE
									,TO_CHAR(CB.FECHA_CARGA_OTRA_UES,'dd/mm/yyyy') FECHA_CARGA_OTRA_UES
									,IT.COD_ESTADO_CARGA
									,EC.NOM_ESTADO_CARGA
									,0 NRO_ITEM_CARGA_ORSAN
									,IT.COD_ITEM_CARGA_OTRA_UES COD_ITEM_CARGA_OTRA_UES_H
									,IT.RUT 
									,IT.COD_DEUDA
									,IT.ANO_CUOTA
									,IT.NRO_CUOTA
									,TO_CHAR(IT.FECHA_DEPOSITO, 'dd/mm/yyyy') FECHA_DEPOSITO
									,TO_CHAR(IT.FECHA_PAGO, 'dd/mm/yyyy') FECHA_PAGO
									,IT.COD_ORIGEN_PAGO
									,IT.MONTO_UTM 
									,IT.MONTO_PESOS 
									,IT.COD_ITEM_CARGA_OTRA_UES
									,IT.MONTO_DESCTO_UTM
									,IT.MONTO_DESCTO_PESOS
									FROM ITEM_CARGA_OTRA_UES IT 
									 	left OUTER JOIN CARGA_OTRA_UES CB  ON  IT.COD_CARGA_OTRA_UES= CB.COD_CARGA_OTRA_UES
									 	left OUTER JOIN ESTADO_CARGA EC  ON  IT.COD_ESTADO_CARGA= EC.COD_ESTADO_CARGA
					                WHERE IT.COD_CARGA_OTRA_UES= {KEY1}
					                AND IT.COD_CARGA_OTRA_UES= CB.COD_CARGA_OTRA_UES
					                AND IT.COD_ESTADO_CARGA= EC.COD_ESTADO_CARGA
									ORDER BY fecha_pago, cod_item_carga_otra_ues";
				}
			break;
			
			/////////////////////////////		ERROR			/////////////////////////////////////
			default:
			base::error("sql_carga_otra_ues::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>