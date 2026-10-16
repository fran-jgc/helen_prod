<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
	
$cod_tipo_descuento = $_REQUEST['cod_tipo_descuento'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
///////////////// CLASS_SQL = AJAX_COD_TIPO_DESCUENTO ///////////////
$sql = sql_tipo_descuento::get_sql('AJAX_COD_TIPO_DESCUENTO', $cod_tipo_descuento);		  
$result = $db->build_results($sql);
$row_count = $db->count_rows();

if ($row_count > 0){
	print urlencode(1);
}
?>