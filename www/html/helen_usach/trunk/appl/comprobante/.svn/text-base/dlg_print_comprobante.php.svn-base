<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_comprobante = $_REQUEST['cod_comprobante'];
$temp = new Template_appl('dlg_print_comprobante.htm');	
$temp->setVar("COD_COMPROBANTE", $cod_comprobante);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = sql_comprobante::get_sql('PRINT_COMPROBANTE',$cod_comprobante);
			
$result = $db->build_results($sql);		
print $temp->toString();
?>