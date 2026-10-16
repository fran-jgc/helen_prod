<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

$temp = new Template_appl('request_cierre_caja.htm');	

$sql = "SELECT to_char( nvl(min(fecha_ingreso), sysdate),'dd/mm/yyyy') FECHA_DESDE
				,to_char( sysdate,'dd/mm/yyyy') FECHA_HASTA	
	  	FROM    ingreso
		where	cod_comprobante is null
		and	  cod_estado_ingreso = 1";	//emitido
$dw = new datawindow($sql);
$dw->add_control(new edit_date('FECHA_DESDE'));
$dw->add_control(new edit_date('FECHA_HASTA'));
$dw->retrieve();
$dw->habilitar($temp, true);
print $temp->toString();
?>