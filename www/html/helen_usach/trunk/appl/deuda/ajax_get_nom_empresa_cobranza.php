<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
//require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_empresa_cobranza = $_REQUEST['cod_empresa_cobranza'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = sql_deuda::get_sql('AJAX_EMPRESA_COBRANZA', $cod_empresa_cobranza);
$result = $db->build_results($sql);
$resultado = '';
$resultado= $resultado.$result[0]['NOM_EMPRESA_COBRANZA'];
print $resultado;
?>