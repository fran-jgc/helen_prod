<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

$rut = $_REQUEST['rut'];
$ano = $_REQUEST['ano'];
$respuesta = '';

$K_VIGENTE = 2;
$K_CS   = 'CS';
$K_CSR  = 'CSR';
$K_CSN  = 'CSN';
$K_CR12 = 'CR12';
$K_CRI  = 'CRI';
$K_CAME = 'CAME';

$K_SOLICITADA = 1;
$K_APROBADA   = 2;
$K_PARAM_CARTERA = 2;

$sql = sql_suspension::get_sql('AJAX_EXISTE_RUT', $rut);
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);
$row_count = $db->count_rows();

if ($row_count == 0) {
	print urlencode('NO_EXISTE_RUT');
}
else {
	$sql = sql_suspension::get_sql('AJAX_ESTADO_DEUDA', $rut, $K_VIGENTE);
	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	$result = $db->build_results($sql);
	$row_count = $db->count_rows();
	
	if ($row_count == 0) {
		print urlencode('DEUDA_NO_VIG');
	}
	else {
		$sql = sql_suspension::get_sql(
			'AJAX_DEUDA_CS_CSR',
			$rut,
			$K_VIGENTE,
			$K_CS,
			$K_CSR,
			$K_CSN,
			$K_CR12,
			$K_CRI,
			$K_CAME
		);

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results($sql);
		$row_count = $db->count_rows();

		if ($row_count == 0) {
			print urlencode('DEUDA_NO_CS_CSR');
		}
		else {
			$sql = sql_suspension::get_sql(
				'AJAX_SOLICITUD',
				$rut,
				$ano,
				$K_VIGENTE,
				$K_CS,
				$K_CSR,
				$K_CSN,
				$K_CR12,
				$K_CRI,
				$K_CAME,
				$K_SOLICITADA,
				$K_APROBADA
			);

			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$result = $db->build_results($sql);
			$row_count = $db->count_rows();
				
			if ($row_count == 0) {
				print urlencode('NO_EXISTE_CUOTA_A_SUSPENDER');
				return;
			}
			else if ($row_count == 1) {
				$cod_deuda = $result[0]['COD_DEUDA'];

				$sql = "SELECT COD_SUSPENSION, NOM_ESTADO_SUSPENSION
						FROM SUSPENSION SU, ESTADO_SUSPENSION ES
						WHERE SU.COD_DEUDA = $cod_deuda
						AND SU.COD_ESTADO_SUSPENSION = ES.COD_ESTADO_SUSPENSION
						AND SU.ANO_SUSPENSION = $ano";

				$result = $db->build_results($sql);
				$row_suspension_deuda = $db->count_rows();
				
				if ($row_suspension_deuda == 1) {
					$cod_suspension = $result[0]['COD_SUSPENSION'];
					$nom_estado_suspension = $result[0]['NOM_ESTADO_SUSPENSION'];
						
					$sql = sql_suspension::get_sql(
						'AJAX_INFO_DEUDOR',
						$rut,
						$cod_deuda,
						$K_VIGENTE,
						$K_CS,
						$K_CSR,
						$K_CSN,
						$K_CR12,
						$K_CRI,
						$K_CAME
					);

					$result = $db->build_results($sql);
						
					$resultado  = "SUSPENSION_DEUDA_RECHAZADA|";
					$resultado .= $result[0]['DIG_VERIF']."|";
					$resultado .= $result[0]['NOMBRE']."|";
					$resultado .= $result[0]['COD_TIPO_DEUDA']."|";
					$resultado .= $result[0]['COD_DEUDA']."|";
					$resultado .= $result[0]['COD_DEUDOR']."|";
					$resultado .= $cod_suspension."|";
					$resultado .= $nom_estado_suspension;

					print urlencode($resultado);
					return;
				}
				else {
					$sql = sql_suspension::get_sql(
						'AJAX_INFO_DEUDOR',
						$rut,
						$cod_deuda,
						$K_VIGENTE,
						$K_CS,
						$K_CSR,
						$K_CSN,
						$K_CR12,
						$K_CRI,
						$K_CAME
					);

					$result = $db->build_results($sql);
						
					$resultado  = "SUSPENSION_DEUDA_NUEVA|";
					$resultado .= $result[0]['DIG_VERIF']."|";
					$resultado .= $result[0]['NOMBRE']."|";
					$resultado .= $result[0]['COD_TIPO_DEUDA']."|";
					$resultado .= $result[0]['COD_DEUDA']."|";
					$resultado .= $result[0]['COD_DEUDOR'];

					print urlencode($resultado);
				}
			}
			else {
				$resultado  = "VARIAS_DEUDAS|";
				$resultado .= $result[0]['DIG_VERIF']."|";
				$resultado .= $result[0]['NOMBRE']."|";
				$resultado .= $result[0]['COD_DEUDOR'];

				for ($i = 0; $i < $row_count; $i++) {
					$resultado .= "|".$result[$i]['COD_TIPO_DEUDA']."^".$result[$i]['COD_DEUDA'];
				}

				print urlencode($resultado);
				return;
			}
		}
	}
}
?>
