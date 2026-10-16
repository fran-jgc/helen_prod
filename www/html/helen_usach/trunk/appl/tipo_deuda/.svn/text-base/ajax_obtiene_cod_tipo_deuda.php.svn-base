<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
	
$cod_tipo_deuda = $_REQUEST['cod_tipo_deuda'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
///////////////// CLASS_SQL = AJAX_COD_TIPO_DEUDA ///////////////
$sql = sql_tipo_deuda::get_sql('AJAX_COD_TIPO_DEUDA', $cod_tipo_deuda);		  
$result = $db->build_results($sql);
$row_count = $db->count_rows();

if ($row_count > 0){
	print urlencode(1);
}
?>