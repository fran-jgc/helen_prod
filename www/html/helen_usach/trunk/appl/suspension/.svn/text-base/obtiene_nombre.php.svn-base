<?php
	///////////////////////
	// php llamado con ajax en el javascript 
	////////////////////////
	

	require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
	
	$rut = $_REQUEST['rut'];
	
	$sql =sql_suspension::get_sql('OBTIENE_NOMBRE',$rut);
	
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
		$NOMBRE = $result[0]['NOMBRE'];
		$DIG_VERIF = $result[0]['DIG_VERIF'];
		$COD_DEUDOR = $result[0]['COD_DEUDOR'];
		
		print $NOMBRE."|".$DIG_VERIF."|".$COD_DEUDOR;
		
		
	}



?>