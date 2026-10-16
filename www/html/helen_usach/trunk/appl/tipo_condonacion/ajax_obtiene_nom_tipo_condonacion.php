<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
	
$nom_tipo_condonacion = $_REQUEST['nom_tipo_condonacion'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
///////////////// CLASS_SQL = AJAX_NOM_TIPO_CONDONACION ///////////////
$sql = sql_tipo_condonacion::get_sql('AJAX_NOM_TIPO_CONDONACION', $nom_tipo_condonacion);		  
$result = $db->build_results($sql);
$row_count = $db->count_rows();

if ($row_count > 0){
	print urlencode(1);
}
?>