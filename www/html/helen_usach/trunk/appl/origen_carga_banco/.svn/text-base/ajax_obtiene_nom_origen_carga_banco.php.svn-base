<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
	
$nom_origen_carga_banco = $_REQUEST['nom_origen_carga_banco'];
$cod_cuenta_corriente	= $_REQUEST['cod_cuenta_corriente'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
///////////////// CLASS_SQL = AJAX_NOM_ORIGEN_CARGA_BANCO ///////////////
$sql = sql_origen_carga_banco::get_sql('AJAX_NOM_ORIGEN_CARGA_BANCO', $nom_origen_carga_banco, $cod_cuenta_corriente);		  
$result = $db->build_results($sql);
$row_count = $db->count_rows();

if ($row_count > 0){
	print urlencode(1);
}
?>