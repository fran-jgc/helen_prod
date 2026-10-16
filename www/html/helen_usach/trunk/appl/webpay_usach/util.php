<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once ("../../appl.ini");


$rut = $_POST['rut'];
$idTransaction = $_POST['idTransaction'];
$cod_wpt = $_POST['cod_wpt'];


$db_sp = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql_sp = 'spw_wp_init_transaccion';
$param_wp = "$cod_wpt,'$idTransaction'";
if ($db_sp->EXECUTE_SP($sql_sp, $param_wp)){
	echo "correcto";
}else{
	echo "incorrecto";
}
?>