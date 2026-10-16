<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
$codigo_simulacion = $_REQUEST['codigo_simulacion'];
$K_ESTADO_SIMULACION_INGRESO = 1000;
$K_PARAM_MAX_VALIDEZ_SIMULACION = 24;

$sql = sql_ingreso::get_sql('AJAX_EXISTE_SIMULACION', $codigo_simulacion, $K_ESTADO_SIMULACION_INGRESO);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);
$row_count = $db->count_rows();
$respuesta = '';
	
if ($row_count == 0){
	$respuesta = "NO_EXISTE|";
}
else
{
	$sql_usach = sql_ingreso::get_sql('AJAX_VALIDEZ_SIMULACION_USACH',$K_PARAM_MAX_VALIDEZ_SIMULACION, $codigo_simulacion);
	$result_usach = $db->build_results($sql_usach);
	
	if($result_usach[0]['UNIVERSIDAD'] == 99999){
		
		$sql = sql_ingreso::get_sql('AJAX_SIMULACION_TIENE_INGRESO', $codigo_simulacion);
		$result = $db->build_results($sql);
		
		if ($result[0]['COD_INGRESO_SIMULACION'] != 0)
			$respuesta = "TIENE_INGRESO|".$result[0]['COD_INGRESO_SIMULACION'];
		else{
			$fecha_simulacion = $result_usach[0]['FECHA_INGRESO'];
			$separado = explode('/', $fecha_simulacion);
			//se da vuelta la fecha para comparar
			$fecha_valida = $result_usach[0]['DIA_HABIL'].'/'.$separado[1].'/'.$separado[2];
			$ultimo_dia_habil = $separado[2].$separado[1].$result_usach[0]['DIA_HABIL'];
		
			$fecha_actual = explode('/',$result_usach[0]['FECHA_ACTUAL'] );
			$fecha_actual = $fecha_actual[2].$fecha_actual[1].$fecha_actual[0];
		
			if($fecha_actual > $ultimo_dia_habil){
				$respuesta = "SIMULACION_NO_VALIDA|".$result_usach[0]['FECHA_INGRESO'].'|'.$fecha_valida;
			}
			else{
				$sql = sql_ingreso::get_sql('AJAX_INFO_SIMULACION', $codigo_simulacion);
				$result = $db->build_results($sql);
			
				$respuesta =  $result[0]['RUT']."|";
				$respuesta =  $respuesta.$result[0]['DIG_VERIF']."|";
				$respuesta =  $respuesta.$result[0]['NOMBRE'];
			}
		}
	}
	else{
		
		$sql = sql_ingreso::get_sql('AJAX_SIMULACION_TIENE_INGRESO', $codigo_simulacion);
		$result = $db->build_results($sql);
		if ($result[0]['COD_INGRESO_SIMULACION'] != 0)
			$respuesta = "TIENE_INGRESO|".$result[0]['COD_INGRESO_SIMULACION'];	
		else{
			$sql = sql_ingreso::get_sql('AJAX_VALIDEZ_SIMULACION', $codigo_simulacion);
			$result = $db->build_results($sql);
			if ($result[0]['TIENE_VALIDEZ'] == 'N')
				$respuesta = "NO_TIENE_VALIDEZ|".$result[0]['FECHA_INGRESO'];	
			else{
				$sql = sql_ingreso::get_sql('AJAX_INFO_SIMULACION', $codigo_simulacion);
				$result = $db->build_results($sql);
			
				$respuesta =  $result[0]['RUT']."|";
				$respuesta =  $respuesta.$result[0]['DIG_VERIF']."|";
				$respuesta =  $respuesta.$result[0]['NOMBRE'];
			}
		}
	}
}
print($respuesta);
?>