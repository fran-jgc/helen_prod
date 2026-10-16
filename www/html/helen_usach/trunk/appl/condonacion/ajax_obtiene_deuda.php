<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$rut = $_REQUEST['rut'];
$wi = unserialize(session::get('wi_condonacion'));
$sql_original = $wi->dws['dw_detalle_deuda']->get_sql();

$cod_moneda_utm 	= 3;
$ed_no_exigible 	= 1;
$ed_cobranza_normal	= 2;
$ed_condonada 		= 3;
$ed_cancelada 		= 4;
	
$sql = sql_condonacion::get_sql('AJAX_OBTIENE_DEUDA',$cod_moneda_utm,$cod_moneda_utm,$rut,$ed_no_exigible,$ed_cobranza_normal);

$wi->dws['dw_detalle_deuda']->set_sql($sql);
$wi->dws['dw_detalle_deuda']->make_tabla_htm($wi->nom_template);
$wi->dws['dw_detalle_deuda']->set_sql($sql_original);
$wi->save_SESSION();
?>