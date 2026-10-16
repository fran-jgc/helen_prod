<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
	
	$cod_deuda = $_REQUEST["cod_deuda"]; 
	$cod_tipo = $_REQUEST["cod_tipo"]; 
	$K_EMITIDA = 1;
	$K_APROBADA= 2;
	$K_TIPO_MAGISTER = 1; //maximo 3
	$K_TIPO_DOCTORADO = 2;//maximo 4
	
	//LA SUSPENSION POR CESANTIA NO TIENE LIMETES
	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	
	
		$sql_mag ="select count(*) CANT_MAG
			   		from suspension
			   		where cod_deuda = $cod_deuda
			   		and cod_tipo_suspension in ($K_TIPO_MAGISTER)
			   		and cod_estado_suspension in ($K_APROBADA)";
			   		
		$result_mag = $db->build_results($sql_mag);
		$NUM_SUSP_MAG = $result_mag[0]['CANT_MAG'];
		
		$sql_doc ="select count(*) CANT_DOC
			   		from suspension
			   		where cod_deuda = $cod_deuda
			   		and cod_tipo_suspension in ($K_TIPO_DOCTORADO)
			   		and cod_estado_suspension in ($K_APROBADA)";
			   		
		$result_doc = $db->build_results($sql_doc);
		$NUM_SUSP_DOC = $result_doc[0]['CANT_DOC'];
		
		
		if($cod_tipo == $K_TIPO_MAGISTER){
			if($NUM_SUSP_MAG >= 3){
				print 'MAGISTER_MAX_3';
			}
		}
		else if ($NUM_SUSP_MAG + $NUM_SUSP_DOC >= 4){
			print 'MAGISTER_DOC_MAX_4';	
		}
		else{
			print 'MAXIMO_NO_SUPERADO';
		}
?>