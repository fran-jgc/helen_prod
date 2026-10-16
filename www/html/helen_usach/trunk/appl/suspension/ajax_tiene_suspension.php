<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
	
	$cod_deuda = $_REQUEST["cod_deuda"]; 
	$ano_suspension = $_REQUEST["ano"]; 

	///////////////// CLASS_SQL = CANT_SUSP ///////////////
	$sql = "select count(*) CANT_SUSP
			from suspension
			where cod_deuda = $cod_deuda
			and ano_suspension= $ano_suspension";
	
	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	$result = $db->build_results($sql);

	$NUM_SUSP = $result[0]['CANT_SUSP'];
	print $NUM_SUSP;
?>