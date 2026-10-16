<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

//$tenor = $_REQUEST['ve_tenor'];
$monto_incial = $_REQUEST['ve_monto_incial'];
$tasa_interes_periodo = $_REQUEST['ve_tasa_interes_periodo'];

$monto_incial = str_replace(",", ".", $monto_incial);

$intereses = $monto_incial * $tasa_interes_periodo/100;

$monto_total = $monto_incial + $intereses;

print $intereses.'|'.$monto_total;
?>