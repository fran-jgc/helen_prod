<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$mes 		= $_REQUEST['mes'];
$ano 		= $_REQUEST['ano'];
$cta_cte	= $_REQUEST['cta_cte'];

$sql = sql_conciliacion::get_sql('AJAX_EXISTE_CONCILIACION',$mes,$ano,$cta_cte);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);
$row_count = $db->count_rows();

If ($row_count > 0){
	$respuesta  = 'EXISTE_CONCILIACION';
}
else{
	$respuesta  = '';
}
print $respuesta;
?>