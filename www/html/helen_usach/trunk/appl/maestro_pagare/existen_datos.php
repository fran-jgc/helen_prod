<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

	$ano = $_REQUEST['ano'];

	///////////////// CLASS_SQL = EXISTE ///////////////
	$sql = sql_maestro_pagare::get_sql('EXISTE', $ano);

	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	$result = $db->build_results($sql);
	$respuesta = '';
	
	if(count($result) == 0){
		$respuesta = 'N';
	}
	else{
		$respuesta = 'S';
	}
	print $respuesta;

?>