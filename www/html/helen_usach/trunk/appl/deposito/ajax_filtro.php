<?php
/******************* FILTRO EN EL INPUT     ******/
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$field = $_REQUEST['field'];
$filtro = $_REQUEST['filtro'];

$wi = unserialize(session::get('wi_deposito'));
$wi->dws['dw_documento_pago']->headers[$field]->set_value_filtro($filtro);
$wi->save_SESSION();

$result_filtro = array();
$indices = array_keys($wi->dws['dw_documento_pago']->headers);
for ($i=0; $i < count($wi->dws['dw_documento_pago']->headers); $i++) {
	$field = $indices[$i];
	$result_filtro[$field] = $wi->dws['dw_documento_pago']->headers[$field]->make_filtro();
}

print urlencode(json_encode($result_filtro));
/*******************FIN FILTRO EN EL INPUT     ******/
?>