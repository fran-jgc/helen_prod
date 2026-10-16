<?php
	///////////////////////
	// php llamado con ajax en el javascript 
	////////////////////////

	require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
	
	$rut = $_REQUEST['rut'];
	$tipo_deuda=$_REQUEST['tipo_deuda'];
	
	$sql = "SELECT DA.COD_DEUDA
			FROM DEUDA DA, DEUDOR DO
			WHERE DO.COD_DEUDOR = DA.COD_DEUDOR
			AND DO.RUT = $rut
			AND DA.COD_TIPO_DEUDA = '$tipo_deuda'
			AND DA.COD_ESTADO_DEUDA = 2
			ORDER BY DA.COD_DEUDA";
	
	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	$result = $db->build_results($sql);
	$row_count = $db->count_rows();
	If ($row_count == 0) 
	{
		$result  = 0;
	}
	else
	{
		$result = $db->build_results($sql);
		$COD_DEUDA = $result[0]['COD_DEUDA'];
		print $COD_DEUDA;
	}
	


?>
