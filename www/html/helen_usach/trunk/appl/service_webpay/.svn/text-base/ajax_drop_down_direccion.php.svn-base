<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../web_service/class_ws_helen.php");

$cod_campo = $_REQUEST['cod_campo'];
$value = $_REQUEST['value'];
$ws_helen = new wakeUp_helen();

$result= '';

switch ($cod_campo) {
	case 'COD_REGION': 
		//$sql = sql_deudor::get_sql('DD_CIUDAD');
		$result = $ws_helen->datos_ciudad($value);
		
		$nom = 'NOM_CIUDAD';
		
		for($i=0; $i<count($result); $i++){
			$result[$i][$nom] = utf8_encode($result[$i][$nom]);
			$result[$i]['1'] = utf8_encode($result[$i]['1']);
		}
		
		break;
	case 'COD_CIUDAD': 
		//$sql = sql_deudor::get_sql('DD_COMUNA');
		$result = $ws_helen->datos_comuna($value);
		$nom = 'NOM_COMUNA';
		
		for($i=0; $i<count($result); $i++){
			$result[$i][$nom] = utf8_encode($result[$i][$nom]);
			$result[$i]['1'] = utf8_encode($result[$i]['1']);
		}
		
		break;
}

print json_encode($result);
?>