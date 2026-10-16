<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

$fecha_inicio  	= urldecode($_REQUEST['fecha_inicio']);
$fecha_termino 	= urldecode($_REQUEST['fecha_termino']);
$tipo_deuda		= urldecode($_REQUEST['tipo_deuda']);
$opcion_abonos	= urldecode($_REQUEST['opcion_abonos']);
$opcion_desctos	= urldecode($_REQUEST['opcion_desctos']);

$K_EST_ABONO_VIGENTE 		= 1;
$K_EST_RENDICION_ANULADA 	= 3;
$K_UNIVERSIDAD_DEFECTO 		= 3;

$b = new base();
$fecha_inicio = $b->str2date($fecha_inicio);
$fecha_termino = $b->str2date($fecha_termino, '23:59:59');

if($opcion_abonos == 'S')
	$sql = sql_rendicion_ley::get_sql('AJAX_DETALLE_ABONOS', $fecha_inicio, $fecha_termino, $K_EST_ABONO_VIGENTE, $K_EST_RENDICION_ANULADA, $K_UNIVERSIDAD_DEFECTO, $tipo_deuda);
else if($opcion_desctos == 'S')
	$sql = sql_rendicion_ley::get_sql('AJAX_DETALLE_DESCTOS', $fecha_inicio, $fecha_termino, $K_EST_ABONO_VIGENTE, $K_EST_RENDICION_ANULADA, $K_UNIVERSIDAD_DEFECTO, $tipo_deuda);
	
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);
for($i=0;$i<count($result);$i++){
	$result[$i]['NOM_DEUDOR'] = urlencode($result[$i]['NOM_DEUDOR']);  	
}
print urlencode(json_encode($result));
?>