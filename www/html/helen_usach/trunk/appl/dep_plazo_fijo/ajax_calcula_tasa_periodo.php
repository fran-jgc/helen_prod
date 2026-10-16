<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

//$tenor = $_REQUEST['ve_tenor'];
$tasa_interes_base = $_REQUEST['ve_tasa_interes_base'];
$dias = $_REQUEST['ve_dias'];

$tasa_interes_base = str_replace(",", ".", $tasa_interes_base);

$tasa_interes_periodo = ($tasa_interes_base / 30) * $dias;

print $tasa_interes_periodo;
?>