<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_carga_orsan_usach extends sql_tabla {
	
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			
			//////////////////		INPUT		//////////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= 	"SELECT COD_CARGA_ORSAN_USACH 
									,COD_CARGA_ORSAN_USACH COD_CARGA_ORSAN
									,TO_CHAR(FECHA_CARGA_ORSAN_USACH,'dd/mm/yyyy') FECHA_CARGA_ORSAN
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
							FROM CARGA_ORSAN_USACH C, USUARIO U 
							WHERE COD_CARGA_ORSAN_USACH ={KEY1}
							AND C.COD_USUARIO = U.COD_USUARIO";
				}
			break;
			
			/////////////////////		OUTPUT		///////////////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT	COD_CARGA_ORSAN_USACH 
									,TO_CHAR(FECHA_CARGA_ORSAN_USACH,'dd/mm/yyyy') FECHA_CARGA_ORSAN
									,TO_CHAR(FECHA_REGISTRO,'dd/mm/yyyy') FECHA_REGISTRO
									,TOTAL_REG_CARGA
									,TOTAL_REG_EXITOSO
									,TOTAL_REG_PDTE
									,CB.COD_USUARIO
									,U.NOM_USUARIO
							FROM CARGA_ORSAN_USACH CB, USUARIO U	
							WHERE CB.COD_USUARIO=U.COD_USUARIO
							ORDER BY COD_CARGA_ORSAN_USACH desc";
				}
			break;
				//////////////////		INPUT		//////////////////////
			case 'ITEM_CARGA_ORSAN' :
				if (K_TIPO_BD=="oci"){
					$sql= 	"select CB.COD_CARGA_ORSAN_USACH
									,CB.TOTAL_REG_CARGA
									,CB.TOTAL_REG_EXITOSO
									,CB.TOTAL_REG_PDTE
									,TO_CHAR(CB.FECHA_CARGA_ORSAN_USACH,'dd/mm/yyyy') FECHA_CARGA_ORSAN
									,IT.COD_ESTADO_CARGA
									,EC.NOM_ESTADO_CARGA
									,0 NRO_ITEM_CARGA_ORSAN
									,IT.COD_ITEM_CARGA_ORSAN_USACH COD_ITEM_CARGA_ORSAN_H
									,IT.RUT 
									,IT.ANO_CUOTA
									,IT.COD_DEUDA
									,IT.NRO_CUOTA
									,TO_CHAR(IT.FECHA_DEPOSITO, 'dd/mm/yyyy') FECHA_DEPOSITO
									,TO_CHAR(IT.FECHA_PAGO, 'dd/mm/yyyy') FECHA_PAGO
									,IT.COD_ORIGEN_PAGO
                                    ,IT.MONTO_TOTAL_PESOS
                                    ,IT.MONTO_TOTAL_UTM
									,IT.MONTO_DESCTO_UTM
                                    ,IT.MONTO_DESCTO_PESOS
									,IT.COD_ITEM_CARGA_ORSAN_USACH 
									FROM ITEM_CARGA_ORSAN_USACH IT 
									 	left OUTER JOIN CARGA_ORSAN_USACH CB  ON  IT.COD_CARGA_ORSAN_USACH= CB.COD_CARGA_ORSAN_USACH
									 	left OUTER JOIN ESTADO_CARGA EC  ON  IT.COD_ESTADO_CARGA= EC.COD_ESTADO_CARGA
					                WHERE IT.COD_CARGA_ORSAN_USACH= {KEY1}
					                AND IT.COD_CARGA_ORSAN_USACH= CB.COD_CARGA_ORSAN_USACH
					                AND IT.COD_ESTADO_CARGA= EC.COD_ESTADO_CARGA
									ORDER BY fecha_pago, cod_deuda, ano_cuota, cod_item_carga_orsan_usach";
				}
			break;
			
			/////////////////////////////		ERROR			/////////////////////////////////////
			default:
			base::error("sql_carga_orsan_usach::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>