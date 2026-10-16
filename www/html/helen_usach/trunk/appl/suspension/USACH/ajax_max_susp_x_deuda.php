<?php
	///////////////////////
	// php llamado con ajax en el javascript 
	////////////////////////
	require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
	
	$cod_deuda = $_REQUEST["cod_deuda"]; 
	$cod_deudor = $_REQUEST["cod_deudor"]; 

	///////////////// CLASS_SQL = AJAX_MAX_SUSP ///////////////
	$sql = sql_suspension::get_sql('AJAX_MAX_SUSP', $cod_deudor, $cod_deuda);
	
	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	$result = $db->build_results($sql);

	$sql_tipo_deuda = "SELECT COD_TIPO_DEUDA
					   FROM DEUDA
					   WHERE COD_DEUDA = $cod_deuda";
	$result_tipo_deuda = $db->build_results($sql_tipo_deuda);
	
	if(K_CLIENTE == 'USACH' && $result_tipo_deuda[0]['COD_TIPO_DEUDA']=='CSR')
		$NUM_SUSP_X_DEUDA = -1;
	else
		$NUM_SUSP_X_DEUDA = $result[0]['NUM_SUSP_X_DEUDA'];
	
	print $NUM_SUSP_X_DEUDA;
?>