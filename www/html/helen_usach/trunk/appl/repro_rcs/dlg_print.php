<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
$temp = new Template_appl('dlg_print.htm');	
$entrable = true;

	$sql = "select	'S' PAGARE
					,'N'  SOLICITUD
			from dual";


$dw_reprogramacion = new datawindow($sql);

$dw_reprogramacion->add_control($control = new edit_radio_button('PAGARE', 'S', 'N', 'Pagaré','print'));
$dw_reprogramacion->add_control($control = new edit_radio_button('SOLICITUD', 'S', 'N', 'Solicitud','print'));

$dw_reprogramacion->insert_row();

$dw_reprogramacion->habilitar($temp, $entrable);
print $temp->toString();
?>