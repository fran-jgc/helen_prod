<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");



$cod_deuda = $_REQUEST['cod_deuda'];
$sql = sql_convenio::get_sql('AJAX_CHANGE_TIPO_DEUDA',$cod_deuda);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);
$count = count($result);

if($count==0){
	print urlencode(json_encode('NO_CUOTAS'));
}
else{
	print urlencode(json_encode($result));
}
?>