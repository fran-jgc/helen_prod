<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_estado_deuda = $_REQUEST['cod_estado_deuda'];
$temp = new Template_appl('dlg_print.htm');	
$temp->setVar("COD_ESTADO_DEUDA", $cod_estado_deuda);
print $temp->toString();
?>