<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
	
$nom_familia_deuda = $_REQUEST['nom_familia_deuda'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
///////////////// CLASS_SQL = AJAX_NOM_FAMILIA_DEUDA ///////////////
$sql = sql_familia_deuda::get_sql('AJAX_NOM_FAMILIA_DEUDA', $nom_familia_deuda);		  
$result = $db->build_results($sql);
$row_count = $db->count_rows();

if ($row_count > 0){
	print urlencode(1);
}
?>