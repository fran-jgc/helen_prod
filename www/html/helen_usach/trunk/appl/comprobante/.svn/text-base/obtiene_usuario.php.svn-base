<?php
	///////////////////////
	// php llamado con ajax en el javascript 
	////////////////////////
	

	require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
	
	$cod_usuario = $_REQUEST['cod_usuario'];

	
	$sql =sql_comprobante::get_sql('OBTIENE_USUARIO',$cod_usuario);
			
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
		$NOM_USUARIO = $result[0]['NOM_USUARIO'];
		$FECHA_DIA= $result[0]['FECHA_DIA'];
		print $NOM_USUARIO."|".$FECHA_DIA;
		
		
	}



?>