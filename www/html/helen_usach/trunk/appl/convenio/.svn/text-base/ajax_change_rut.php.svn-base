<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$rut = $_REQUEST['rut'];
if ($rut=='')
	print '';

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = sql_convenio::get_sql('AJAX_CHANGE_RUT',$rut);
$result = $db->build_results($sql);
for($i=0; $i<count($result);$i++)
	$result[$i]['NOM_DEUDOR'] = urlencode($result[$i]['NOM_DEUDOR']); 
	
print urlencode(json_encode($result));
?>