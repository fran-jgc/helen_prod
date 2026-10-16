<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");
$temp = new Template_appl('request_crear_desde.htm');	
$entrable = true;
if (K_TIPO_BD=="oci"){
	$sql = "select	'' SELECCION_BOLETA 
					,'' SELECCION_SIMULACION     
					,'' NRO_SELECCION
					,'' RUT
					,'' DIG_VERIF
					,'' NOMBRE
			from dual";
}else if (K_TIPO_BD=="mssql"){
	$sql = "select	'' SELECCION_BOLETA 
					,'' SELECCION_SIMULACION  
					,'' NRO_SELECCION
					,'' RUT
					,'' DIG_VERIF
					,'' NOMBRE";	
}

$dw_boleta = new datawindow($sql);

$dw_boleta->add_control($control = new edit_radio_button('SELECCION_BOLETA', 'BOLETA', 'BOLETA', 'Boleta','SELECCION_OPCION'));
$control->set_onChange("checked_radio_button(this);");
$dw_boleta->add_control($control = new edit_radio_button('SELECCION_SIMULACION', 'SIMULACION', 'SIMULACION', 'Simulación','SELECCION_OPCION'));
$control->set_onChange("checked_radio_button(this);");
$dw_boleta->add_control(new static_num('RUT'));
$dw_boleta->add_control(new static_text('DIG_VERIF'));
$dw_boleta->add_control(new static_text('NOMBRE'));
$dw_boleta->add_control($control = new edit_text('NRO_SELECCION',20,20));
$control->set_onChange("f_get_info_seleccion();");

$dw_boleta->insert_row();

$dw_boleta->habilitar($temp, $entrable);
print $temp->toString();
?>