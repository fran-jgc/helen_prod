<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_boleta = $_REQUEST['cod_boleta'];
$temp = new Template_appl('dlg_print_boleta.htm');	
$temp->setVar("COD_BOLETA", $cod_boleta);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = "SELECT  COD_BOLETA       
				FROM    BOLETA
				WHERE COD_BOLETA = ".$cod_boleta;
			
$result = $db->build_results($sql);		
print $temp->toString();
?>