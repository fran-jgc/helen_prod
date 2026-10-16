<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
	
$nom_perfil = $_REQUEST['nom_perfil'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
///////////////// CLASS_SQL = AJAX_NOM_PERFIL ///////////////
$sql = sql_perfil::get_sql('AJAX_NOM_PERFIL', $nom_perfil);		  
$result = $db->build_results($sql);
$row_count = $db->count_rows();

if ($row_count > 0){
	print urlencode(1);
}
?>