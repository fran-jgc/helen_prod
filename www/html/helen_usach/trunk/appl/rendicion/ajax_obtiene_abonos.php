<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

$fecha_inicio  	= urldecode($_REQUEST['fecha_inicio']);
$fecha_termino 	= urldecode($_REQUEST['fecha_termino']);
$tipo_deuda		= urldecode($_REQUEST['tipo_deuda']);

$fecha_inicio = "to_date('$fecha_inicio 00:00:00', 'dd/mm/yyyy hh24:mi:ss')";
$fecha_termino = "to_date('$fecha_termino 23:59:59', 'dd/mm/yyyy hh24:mi:ss')";

if($tipo_deuda == 'TODAS'){
    $tipo_deuda = "'CFR','CR12','CRI','CRPI','CS','CSR','CSN'";
}
else{
    $tipo_deuda = "'$tipo_deuda'";
}

$sql = sql_rendicion::get_sql('AJAX_DETALLE_ABONOS', $fecha_inicio, $fecha_termino, "$tipo_deuda");
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);

for($i=0;$i<count($result);$i++){
	$result[$i]['NOM_DEUDOR'] = urlencode($result[$i]['NOM_DEUDOR']);  	
}
print urlencode(json_encode($result));
?>