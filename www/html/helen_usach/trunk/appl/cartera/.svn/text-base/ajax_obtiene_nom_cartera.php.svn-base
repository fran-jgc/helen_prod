<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
	
$nom_cartera = $_REQUEST['nom_cartera'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
///////////////// CLASS_SQL = AJAX_NOM_CARTERA ///////////////
$sql = sql_cartera::get_sql('AJAX_NOM_CARTERA', $nom_cartera);		  
$result = $db->build_results($sql);
$row_count = $db->count_rows();

if ($row_count > 0){
	print urlencode(1);
}
?>