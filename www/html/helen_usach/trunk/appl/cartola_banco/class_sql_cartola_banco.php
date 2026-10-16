<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	


class sql_cartola_banco extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			
			//////////////////     INPUT       /////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= "";
				}			
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT COD_CARTOLA
	  							 ,CONVERT(varchar(20),FECHA_REGISTRO,103) FECHA_REGISTRO
	  							 ,NOM_USUARIO
	  							 ,CC.COD_CUENTA_CORRIENTE
							     ,NRO_CARTOLA
							     ,CONVERT(VARCHAR(20),FECHA_CARTOLA,103) FECHA_CARTOLA
							     ,OBS
						   FROM CARTOLA C
  							   ,CUENTA_CORRIENTE CC
							   ,USUARIO U
						   WHERE COD_CARTOLA = {KEY1}
    							AND C.COD_USUARIO = U.COD_USUARIO
							    AND CC.COD_CUENTA_CORRIENTE = C.COD_CUENTA_CORRIENTE";
				}
			break;
			
			////////////////////////  OUTPUT   ///////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= 	"";
				}				
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT  		COD_CARTOLA
										, CONVERT(VARCHAR(20), FECHA_REGISTRO, 103) FECHA_REGISTRO
										, U.NOM_USUARIO
										, CTA.NOM_CUENTA_CORRIENTE
										, NRO_CARTOLA
						           		, CONVERT(VARCHAR(20), FECHA_CARTOLA, 103) FECHA_CARTOLA
						    FROM  		CARTOLA C, USUARIO U, CUENTA_CORRIENTE CTA 
						    WHERE   	C.COD_USUARIO = U.COD_USUARIO
						    	AND		C.COD_CUENTA_CORRIENTE = CTA.COD_CUENTA_CORRIENTE
						            	ORDER BY COD_CARTOLA DESC";
				}
			break;
			
			
			////////////////////////  DW_ITEM_CARTOLA   ///////////
			case 'DW_ITEM_CARTOLA' :
				if (K_TIPO_BD=="oci"){
					$sql= 	"";
				}				
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT	COD_ITEM_CARTOLA
									,DETALLE_MOVIMIENTO
									,CHEQUE_CARGO
									,DEPOSITO_ABONO
									,SALDO
									,SUCURSAL
									,NRO_COMPROBANTE
							FROM ITEM_CARTOLA IC
								,CARTOLA C
							WHERE	C.COD_CARTOLA = IC.COD_CARTOLA
								AND IC.COD_CARTOLA = {KEY1}";
				}
			break;
				
			
				
				//////////////////   ERRROR ////////////////////
			default:
				base::error("sql_cartola_banco::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>