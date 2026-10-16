<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../web_service/class_ws_helen.php");
require_once ("../../appl.ini");
require_once ("funciones.php");

class w_webpay_cupon {
	function w_webpay_cupon() {
		$data	= $_REQUEST['data'];
		$dato = explode('|', $data);
		$cod_estado_transaccion = $dato[0];
		$cod_transaccion = $dato[1];
		$cod_deudor = session::get('COD_DEUDOR');
		
		$t = new Template_appl("webpay_cupon.html");
		
		if ($cod_estado_transaccion == 'RECHAZADO') {
			$MENSAJE_TRANSACCION = "Su transacci&oacute;n no se ha podido efectuar con &eacute;xito, si el problema<br>persiste, comun&iacute;quese con su banco emisor";

			$t->setVar("DISPLAY_FRACASO", '');
			$t->setVar("DISPLAY_APROBADO", 'none');
			$t->setVar("TITULO_TRANSACCION", 'TRANSACCION RECHAZADA');
			$t->setVar("MENSAJE_TRANSACCION", 'Su transaccion no se ha podido efectuar ');
			$t->setVar("NRO_COMPRA", $cod_transaccion);
			$t->setVar("NOM_COMPRADOR", '');
			$t->setVar("NRO_TARJETA", '');
			$t->setVar("MONTO_PAGO", '');
			$t->setVar("FECHA_TRANSACCION",'');
			$t->setVar("HORA_TRANSACCION", '');
			$t->setVar("CODIGO_TRANSACCION", '');
			$t->setVar("NRO_TRANSACCION", '');
			$t->setVar("TIPO_TRANSACCION", '');
			$t->setVar("NRO_CUOTAS", '');
			$t->setVar("TIPO_CUOTAS", '');
		
		}else if ($cod_estado_transaccion == 'APROBADO') {
			$cod_autorizacion = $dato[1];
			$fecha_transaccion = $dato[2];
			$tarjeta_final = $dato[3];
			$nro_cuota = $dato[4];
			$tipo_cuota = $dato[5];
			$orden_compra = $dato[6];
			$MENSAJE_TRANSACCION = "";
			
			$dato_compra = explode('_', $orden_compra);
			$tbk_rut = $dato_compra[0];
			$tbk_monto_pago = $dato_compra[1];
			$tbk_cod_transacion = $dato_compra[2];
			
			$ws_helen = new wakeUp_helen();
			$result = $ws_helen->datos_webpay_cupon($tbk_rut);
			
			$monto_pesos = number_format($tbk_monto_pago, 0, ',', '.');
			//existe la posibilidad que el deudor page la deuda con datos de un tercero validar esa opcion
			$nom_deudor = $result[0]['NOMBRE'];

			$t->setVar("DISPLAY_FRACASO", 'none');
			$t->setVar("DISPLAY_APROBADO", '');
			$t->setVar("TITULO_TRANSACCION", 'TRANSACCION EXITOSA');
			$t->setVar("MENSAJE_TRANSACCION", '');
			$t->setVar("NRO_COMPRA", $tbk_cod_transacion);
			$t->setVar("NOM_COMPRADOR", $nom_deudor);
			$t->setVar("NRO_TARJETA", "************$tarjeta_final");
			$t->setVar("MONTO_PAGO", $monto_pesos);
			$t->setVar("FECHA_TRANSACCION",substr($fecha_transaccion,1,11));
			$t->setVar("HORA_TRANSACCION", substr($fecha_transaccion,11,19));
			$t->setVar("CODIGO_TRANSACCION", $cod_autorizacion);
			$t->setVar("NRO_TRANSACCION", '');
			$t->setVar("TIPO_TRANSACCION", 'TRANSBANK');
			$t->setVar("NRO_CUOTAS", $nro_cuota);
			$t->setVar("TIPO_CUOTAS", $tipo_cuota);
		}

		
		
		$t->setVar("MENSAJE_TRANSACCION", $MENSAJE_TRANSACCION);
		
		print $t->toString();
	}
}		
$w = new w_webpay_cupon();
?>