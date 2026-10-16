<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

$cod_deuda  	= urldecode($_REQUEST['cod_deuda']);
$cod_abono		= urldecode($_REQUEST['cod_abono']);

$resp = '';
$cant_abono = 0;

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$sql = "SELECT COUNT(*) CANT
        FROM CRUZADO C, DEUDA DE  
        WHERE C.COD_DEUDA = DE.COD_DEUDA
        AND DE.COD_DEUDA =$cod_deuda";

$result = $db->build_results($sql);
$cant_cruzado = $result[0]['CANT'];

if($cant > 0){
    $sql_abono = "SELECT COUNT(*) CANT
        FROM RENDICION_ABONO RA, RENDICION R
        WHERE R.COD_RENDICION = RA.COD_RENDICION 
        AND R.COD_ESTADO_RENDICION = 2
        AND RA.COD_ABONO =$cod_abono";
    
    $result_abono = $db->build_results($sql_abono);
    $cant_abono = $result_abono[0]['CANT'];
    
    
}


//si respuesta es SI, quiere decir que se pouede rendir, debido a que primero se debe rendir por modulo rendicion otras ues
if(($cant_cruzado > 0) && ($cant_abono > 0)){
    $resp = 'SI';
}
else if(($cant_cruzado > 0) && ($cant_abono == 0)){
    $resp = 'NO';
}
else if($cant_cruzado == 0){
    $resp = 'SI';
}

print $resp;
?>