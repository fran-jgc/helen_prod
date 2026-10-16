<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
	
$cod_carrera = $_REQUEST['cod_carrera'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
///////////////// CLASS_SQL = AJAX_COD_CARRERA ///////////////
$sql = sql_carrera::get_sql('AJAX_COD_CARRERA', $cod_carrera);		  
$result = $db->build_results($sql);
$row_count = $db->count_rows();

if ($row_count > 0){
	print urlencode(1);
}
?>