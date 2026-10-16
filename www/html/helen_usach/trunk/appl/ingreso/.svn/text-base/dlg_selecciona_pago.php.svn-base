<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
$temp = new Template_appl('dlg_selecciona_pago.htm');	

$rut = $_REQUEST["rut"];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

if (K_TIPO_BD=="oci"){

	$sp = 'spdw_ingreso_selecciona_pago';
	$db->EXECUTE_SP($sp, $rut);
	
	$sql = "SELECT SELECCION
				,TIPO
				,CODIGO
				,FECHA_CUOTA
				,COD_CUOTA_CONVENIO
				,NRO_CUOTA
				,MONTO_MONEDA
				,MONTO_PESOS
			FROM TEMP_SELEC_PAGO";
		
	$db_2 = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	$result = $db_2->build_results($sql);
	$db->ROLLBACK_TRANSACTION();
	
}
else if (K_TIPO_BD=="mssql"){
	$sql = "exec spdw_ingreso_selecciona_pago ".$rut;
	$result = $db->build_results($sql);
}

$dw_datos = new datawindow($sql, 'TIPO_PAGO');

$dw_datos->add_control(new edit_check_box('SELECCION', 'S', 'N'));        
$dw_datos->add_control(new edit_text_hidden('RUT'));
$dw_datos->add_control(new static_text('TIPO'));
$dw_datos->add_control(new static_text('CODIGO'));
$dw_datos->add_control(new static_text('FECHA_CUOTA'));
$dw_datos->add_control(new edit_text_hidden('COD_CUOTA_CONVENIO'));
$dw_datos->add_control(new static_num('NRO_CUOTA'));
$dw_datos->add_control(new static_num('MONTO_MONEDA', 4));
$dw_datos->add_control(new static_num('MONTO_PESOS'));
$entrable = true;

$dw_datos->retrieve();

for($i = 0; $i < $dw_datos->row_count(); $i++){
	$tipo = $dw_datos->get_item($i, 'TIPO');
	
	if ($tipo == 'PAGO_NORMAL')
		$dw_datos->set_item($i, 'TIPO', 'PAGO NORMAL');
	else if($tipo == 'CONVENIO_PAGO_INICIAL')
		$dw_datos->set_item($i, 'TIPO', 'CONVENIO INICIAL');
	else if($tipo == 'CONVENIO_PAGO_CUOTA')
		$dw_datos->set_item($i, 'TIPO', 'PAGO CUOTA');
	else if($tipo == 'RENEG_PAGO_INICIAL')
		$dw_datos->set_item($i, 'TIPO', 'RENEG. INICIAL');

}

$dw_datos->habilitar($temp, $entrable);
print $temp->toString();
?>