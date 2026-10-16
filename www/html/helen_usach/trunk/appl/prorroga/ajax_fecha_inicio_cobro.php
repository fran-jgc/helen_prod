<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$ano = $_REQUEST['ano'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

///////////////// CLASS_SQL = AJAX_DEUDOR ///////////////
$sql = "SELECT convert(VARCHAR,dbo.f_makedate(01,01,$ano+3),103) FECHA";	  
$result = $db->build_results($sql);
print $result[0]['FECHA'];
?>