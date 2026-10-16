<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
	
$nom_comuna = $_REQUEST['nom_comuna'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
///////////////// CLASS_SQL = AJAX_NOM_COMUNA ///////////////
$sql = sql_comuna::get_sql('AJAX_NOM_COMUNA', $nom_comuna);		  
$result = $db->build_results($sql);
$row_count = $db->count_rows();

if ($row_count > 0){
	print urlencode(1);
}
?>