<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_inf_abonos_base extends sql_tabla {
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT COD_INF_ABONO 
                                , SESSION_ID
                                , FECHA_REGISTRO
                                , RUT
                                , DV
                                , TIPO_DEUDA
                                , COD_DEUDA

                                , ESTADO_DEUDA
                                , FOLIO
                                , FOLIO_ORSAN
                                , ANO_PAGA
                                , COMPONENTE_DEUDA
                                , FECHA_PAGO
                                , COMPROBANTE
                                , ENTIDAD
                                , TIPO_DOCUMENTO_PAGO
                                , TIPO_ABONO
                                , ORDEN_COMPRA
                                , ESTADO_EXCEDENTE
                                , FECHA_DEPOSITO
                                , PORCENTAJE
                                , P_MONTO_CUOTA
                                , CAP_UTM
                                , INT_MULTA_UTM
                                , TOTAL_UTM
                                , CAP_PESOS
                                , INT_MULTA_PESOS
                                , TOTAL_PESOS
                                , FECHA_INGRESO
                                , DESCUENTO_UTM
                                , DESCUENTO_PESOS
                                , INICIO_COBRO
                                , PORC_USACH
                                , PORC_FONDO
                                , PORC_CF
                                , PORC_CU
                                , VENCIMIENTO
                                , COD_ESTADO_PAGO
                            FROM INF_ABONO
                            where SESSION_ID = '{PARAM1}'";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "spi_pronto_pago";
				}
				break;
			
			case 'INPUT_FECHA' :
				if (K_TIPO_BD=="oci"){
				    $sql = "select  '' COD_TIPO_DEUDA
                                    , '' COD_FAMILIA_DEUDA
                                      ,'01/01/{PARAM1}' FECHA_INICIAL
									,to_char(sysdate, 'dd/mm/yyyy') FECHA_FINAL
					  		from 	DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select '' COD_TIPO_DEUDA
                                    , '' COD_FAMILIA_DEUDA
									,'01/01/{PARAM1}' FECHA_INICIAL
									,'31/12/{PARAM1}' FECHA_FINAL";
				}
				break;		

			case 'INPUT_DD_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select COD_TIPO_DEUDA
									, COD_TIPO_DEUDA COD_TIPO_DEUDA_COD
							from 	 TIPO_DEUDA 
							order by COD_TIPO_DEUDA";
				}
				break;		
				////////////////////			ERROR		//////////////////////////////////
				default:
				base::error("sql_inf_abonos::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_sql_inf_abonos.php";
if (file_exists($file_name))
    require_once($file_name);
else {
    class sql_inf_abonos extends sql_inf_abonos_base {
    }
}
?>