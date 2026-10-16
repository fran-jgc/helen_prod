<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_deuda = $_REQUEST['cod_deuda'];

$sql = "SELECT COUNT(*) CANT FROM CRUZADO WHERE COD_DEUDA = $cod_deuda";

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);

if($result[0]['CANT']>0){
    print 'SI';
}else{
    print 'NO';
}

?>