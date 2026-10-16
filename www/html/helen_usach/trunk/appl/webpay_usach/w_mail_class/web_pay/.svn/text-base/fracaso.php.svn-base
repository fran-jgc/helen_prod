<?php 
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
require_once ("../../../appl.ini");
require_once ("../funciones.php");

$TBK_ORDEN_COMPRA		= $_POST['TBK_ORDEN_COMPRA'];
$data ="RECHAZADO|$TBK_ORDEN_COMPRA";

$url  = '../webpay_cupon.php?data='.$data;
header ('Location: '.$url);

?>