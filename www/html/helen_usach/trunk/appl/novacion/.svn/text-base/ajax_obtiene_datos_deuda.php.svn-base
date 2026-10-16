<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
$K_ESTADO_DEUDA_NO_EXIGIBLE = 1;
$K_ESTADO_DEUDA_COBRANZA_NORMAL	= 2;
$K_ESTADO_DEUDA_NOVADA 		= 7;
$K_COMPONENTE_NO_EXIGIBLE 	= 1;
$K_COMPONENTE_VENCIDA 		= 2;
$K_COMPONENTE_VIGENTE 		= 3;
$K_COMPONENTE_FUTURA 		= 4;
$K_DEUDA_CS					= "CS";
$K_NOVACION_SOLICITADA 		= 1;	      
$K_NOVACION_APROBADA 		= 2;
$K_PARAM_CARTERA			= 2;

$rut = $_REQUEST["rut_deudor"];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = sql_novacion::get_sql('AJAX_EXISTE_DEUDOR', $rut);		  
$result = $db->build_results($sql);
if ($result[0]['CUANTO']== 0){
	print urlencode('NO_EXISTE_DEUDOR');
	return;
}else{
	$sql = sql_novacion::get_sql('AJAX_TIPO_DEUDA', $rut, $K_DEUDA_CS);		  
	$result = $db->build_results($sql);
	if ($result[0]['COUNT_TIPO_DEUDA'] == 0){
		print urlencode('SIN_CS');
		return;
	}else{
		$sql = sql_novacion::get_sql('AJAX_ESTADO_DEUDA', $rut, $K_ESTADO_DEUDA_NO_EXIGIBLE, $K_ESTADO_DEUDA_COBRANZA_NORMAL);		  
		$result = $db->build_results($sql);
		if ($result[0]['CANT']== 0){
			print urlencode('SIN_NO_EXIGIBLE');
			return;
		}else{
		
			$cartera_defecto = base::get_parametro($K_PARAM_CARTERA);
			$sql = sql_novacion::get_sql('DEUDA_CARTERA_DIFERENTE', $cartera_defecto,$rut);
			$result = $db->build_results($sql);
			
			if($result[0]['CANT'] > 0) {
				print urlencode('CARTERA_DIFERENTE');
				return;
			}
			else{
				//VARIABLES DE NOVACION
				$NOVACION_SOLICITADA = 1;
				$NOVACION_APROBADA	= 2;
				$sql = sql_novacion::get_sql('AJAX_NOVAR_DEUDA', $rut, $K_DEUDA_CS, $NOVACION_SOLICITADA,$NOVACION_APROBADA);		  
				$result = $db->build_results($sql);
				$row_deuda = $db->count_rows();			
				
				if($row_deuda == 0){
					print urlencode('SIN_DEUDAS_POR_NOVAR');
					return;
				}else if($row_deuda == 1){
					$cod_deuda = $result[0]['COD_DEUDA'];
					$sql = "SELECT COD_NOVACION, NOM_ESTADO_NOVACION
							FROM NOVACION N, ESTADO_NOVACION EN
							WHERE N.COD_DEUDA_ANTIGUA = $cod_deuda
							AND N.COD_ESTADO_NOVACION = EN.COD_ESTADO_NOVACION";
					$result = $db->build_results($sql);
					$row_novada_deuda = $db->count_rows();
					
					if($row_novada_deuda == 1){
						$cod_novacion = $result[0]['COD_NOVACION'];
						$nom_estado_novacion = $result[0]['NOM_ESTADO_NOVACION'];
						
						$sql = sql_novacion::get_sql('AJAX_UNA_DEUDA_NOVADA', $rut, $cod_deuda, $K_DEUDA_CS, $K_COMPONENTE_NO_EXIGIBLE, $K_COMPONENTE_VENCIDA, $K_COMPONENTE_VIGENTE, $K_COMPONENTE_FUTURA);
						$result = $db->build_results($sql);
						
						$resultado  = "NOVACION_DEUDA_RECHAZADA"."|";
						$resultado .= $result[0]['DIG_VERIF']."|";
						$resultado .= $result[0]['NOMBRE']."|";
						$resultado .= $result[0]['COD_DEUDA']."|";
						$resultado .= $result[0]['COD_TIPO_DEUDA']."|";
						$resultado .= $result[0]['SIMBOLO']."|";
						$resultado .= $result[0]['NOM_CARTERA']."|";
						$resultado .= $result[0]['NOM_ESTADO_DEUDA']."|";
						$resultado .= $result[0]['CANT_CUOTA']."|";
						$resultado .= $result[0]['FECHA_INICIO_COBRO']."|";
						$resultado .= $result[0]['MONTO_INICIO_COBRO']."|";
						$resultado .= $result[0]['CAT']."|";
						$resultado .= number_format($result[0]['MONTO_SVP'], 0, '', '')."|";
						$resultado .= number_format($result[0]['MONTO_MORA'], 0, '', '')."|";
						$resultado .= number_format($result[0]['MONTO_CP'], 0, '', '')."|";
						$resultado .= number_format($result[0]['MONTO_LP'], 0, '', '')."|";
						$resultado .= number_format($result[0]['TOTAL_PESOS'], 0, '', '')."|";
						$resultado .= $cod_novacion."|";
						$resultado .= $nom_estado_novacion;				
						print urlencode($resultado);
						return;
					}else{
						$sql = sql_novacion::get_sql('AJAX_UNA_DEUDA_NOVADA', $rut, $cod_deuda, $K_DEUDA_CS, $K_COMPONENTE_NO_EXIGIBLE, $K_COMPONENTE_VENCIDA, $K_COMPONENTE_VIGENTE, $K_COMPONENTE_FUTURA);
						$result = $db->build_results($sql);
						
						$resultado  = "NOVAR_DEUDA"."|";
						$resultado .= $result[0]['DIG_VERIF']."|";
						$resultado .= $result[0]['NOMBRE']."|";
						$resultado .= $result[0]['COD_DEUDA']."|";
						$resultado .= $result[0]['COD_TIPO_DEUDA']."|";
						$resultado .= $result[0]['SIMBOLO']."|";
						$resultado .= $result[0]['NOM_CARTERA']."|";
						$resultado .= $result[0]['NOM_ESTADO_DEUDA']."|";
						$resultado .= $result[0]['CANT_CUOTA']."|";
						$resultado .= $result[0]['FECHA_INICIO_COBRO']."|";
						$resultado .= number_format($result[0]['MONTO_INICIO_COBRO'], 0, '', '')."|";
						$resultado .= number_format($result[0]['CAT'], 0, '', '')."|";
						$resultado .= number_format($result[0]['MONTO_SVP'], 0, '', '')."|";
						$resultado .= number_format($result[0]['MONTO_MORA'], 0, '', '')."|";
						$resultado .= number_format($result[0]['MONTO_CP'], 0, '', '')."|";
						$resultado .= number_format($result[0]['MONTO_LP'], 0, '', '')."|";
						$resultado .= number_format($result[0]['TOTAL_PESOS'], 0, '', '');				
						print urlencode($resultado);
						return;
					}
				}else{
					print urlencode('VARIAS_DEUDAS');
					return;
				}
			}
		}
	}
}
?>