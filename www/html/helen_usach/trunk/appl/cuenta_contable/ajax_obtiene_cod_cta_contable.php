<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
	
$cod_cta_contable = $_REQUEST['cod_cta_contable'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
///////////////// CLASS_SQL = AJAX_COD_CTA_CONTABLE ///////////////
$sql = sql_cuenta_contable::get_sql('AJAX_COD_CTA_CONTABLE', $cod_cta_contable);		  
$result = $db->build_results($sql);
$row_count = $db->count_rows();

if ($row_count > 0){
	print urlencode(1);
}
?>