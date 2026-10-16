<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../web_service/class_ws_helen.php");
include("funciones.php");
class w_consulta_deuda{
	function w_consulta_deuda() {
		valida_inicio();
		
		$t = new Template_appl("consulta_deuda.html");

		$cod_deudor = session::get('cod_deudor');

		$ws_helen = new wakeUp_helen();
		
		$result = $ws_helen->get_datos_deuda($cod_deudor);

		$t->setVar("RUT", $result[0]['RUT']);
		$t->setVar("DIG_VERIF", $result[0]['DIG_VERIF']);
		$t->setVar("NOMBRE", utf8_encode($result[0]['NOMBRE']));
		$t->setVar("DIRECCION", utf8_encode($result[0]['DIRECCION']));
		$t->setVar("FONO_PARTICULAR", utf8_encode($result[0]['FONO_PARTICULAR']));
		$t->setVar("EMAIL_PARTICULAR", utf8_encode($result[0]['EMAIL_PARTICULAR']));
		$t->setVar("DIRECCION_COMERCIAL", utf8_encode($result[0]['DIRECCION_COMERCIAL']));
		$t->setVar("FONO_COMERCIAL", utf8_encode($result[0]['FONO_COMERCIAL']));
		$t->setVar("EMAIL_COMERCIAL", utf8_encode($result[0]['EMAIL_COMERCIAL']));
		$t->setVar("URL", $result[0]['URL']);
		$t->setVar("DIV_REPRO", $result[0]['DIV_REPRO']);
		
		$result = $ws_helen->listado_deudas($cod_deudor);
		
		$START_BLOCK = '';
		
		for($i = 0; $i < count($result); $i++){
		
			$key = 'helen';
			$cod_deuda_decode = urlencode(encriptar_url($result[$i]['COD_DEUDA'], $key));
		
			$START_BLOCK.='
					<tr>
						<th scope="row">
							<div>
								<a href="detalle_deuda.php?id='.$cod_deuda_decode.'" align="center">'.$result[$i]['COD_DEUDA'].'</a>
							</div>
						</th>

						<th scope="row">
							<div class="deuda_resultado" align="center">'.$result[$i]['NOM_ESTADO_DEUDA'].'</div>
						</th>

						<th scope="row">
							<div class="deuda_resultado" align="center">'.$result[$i]['COD_TIPO_DEUDA'].'</div>
						</th>

						<th scope="row">
							<div class="deuda_resultado" align="center">'.$result[$i]['SIMBOLO'].'</div>
						</th>

						<th scope="row">
							<div class="deuda_resultado" align="center">'.$result[$i]['FECHA_INICIO_COBRO'].'</div>
						</th>

						<th scope="row">
							<div class="deuda_resultado" align="center">'.$result[$i]['SALDO'].'</div>
						</th>

						<th scope="row">
							<div class="deuda_resultado" align="center">'.number_format($result[$i]['SALDO_PESOS'], 0, ',', '.').'</div>
						</th>
					</tr>
				';
		
		}
		
		$t->setVar("START_BLOCK_DEUDA",$START_BLOCK);
				
		print $t->toString();
	}
}

$w = new w_consulta_deuda();
?>