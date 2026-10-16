<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

$cod_boleta = $_REQUEST['nro_boleta'];
$K_DEUDA_EXIGIBLE = 2;
$K_PARAM_CARTERA  = 2;

$sql = sql_ingreso::get_sql('AJAX_EXISTE_BOLETA', $cod_boleta);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);
$row_count = $db->count_rows();
$respuesta = '';
	
if ($row_count == 0){
	$respuesta = "NO_EXISTE|";
}
else
{	
	$sql = sql_ingreso::get_sql('AJAX_EXISTE_SUB_BOLETA', $cod_boleta);
	
	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	$result = $db->build_results($sql);
	$row_count = $db->count_rows();
	$respuesta = '';
		
	if ($row_count == 0){
		$respuesta = "NO_EXISTE_SB|";
	}
	else{
		$sql = sql_ingreso::get_sql('AJAX_SUB_BOLETA_PAGADA', $cod_boleta);
	
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results($sql);
		$row_count = $db->count_rows();
		$respuesta = '';
			
		if ($row_count == 0){
			$respuesta = "SUB_BOLETA_PAGADA|";
		}
		else{
			
			$sql = sql_ingreso::get_sql('AJAX_INFO_BOLETA', $cod_boleta);
			
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$result = $db->build_results($sql);
			$row_count = $db->count_rows();
			$respuesta = '';
			
			if ($row_count == 0){
				$respuesta = "0|";
			}
			else{
				$cartera_defecto = base::get_parametro($K_PARAM_CARTERA);
				if(K_CLIENTE == 'UCM2'){
					$sql = sql_ingreso::get_sql('VALIDA_BOLETA_CARTERA_UCM2', $cod_boleta,$cartera_defecto);	
				}
				else{
					$sql = sql_ingreso::get_sql('VALIDA_BOLETA_CARTERA', $cod_boleta,$cartera_defecto);
				}
				
				$result = $db->build_results($sql);
				
				if($result[0]['CANT'] == 0) {
					$respuesta = 'BOLETA_CON_CARTERA|';
				}
				else{
					$sql = sql_ingreso::get_sql('AJAX_VALIDEZ_BOLETA', $cod_boleta);
					$result = $db->build_results($sql);	
					print_r($result);
					$respuesta = '';		
					if ($result[0]['CANT'] == 0)
						$respuesta = "NO_TIENE_VALIDEZ|";	
					else{
					
						$sql = sql_ingreso::get_sql('AJAX_INFO_BOLETA', $cod_boleta);
						$result = $db->build_results($sql);
						$row_count = $db->count_rows();
						$respuesta = '';
						
						$respuesta =  $result[0]['RUT']."|";
						$respuesta =  $respuesta.$result[0]['DIG_VERIF']."|";
						$respuesta =  $respuesta.$result[0]['NOMBRE']."|";
						$respuesta =  $respuesta.$result[0]['COD_DEUDA']."|";
						$respuesta =  $respuesta.$result[0]['COD_TIPO_DEUDA']."|";
						$respuesta =  $respuesta.$result[0]['ANO_COBRO'];
					}
				}
			}
		}
	}
}
print urlencode($respuesta);
?>