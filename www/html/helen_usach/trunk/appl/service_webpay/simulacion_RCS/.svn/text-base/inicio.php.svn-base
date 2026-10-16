<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../web_service/class_ws_helen.php");
require_once ("../../../appl.ini");

class w_inicio extends w_base {
	
	function w_inicio() {
		$cod_deudor = $_REQUEST['cod_deudor'];
		$t = new Template_appl("inicio.htm");
		
		$ws_helen = new wakeUp_helen();
		
		$result = $ws_helen->inicio_repro($cod_deudor);
		
		$t->setVar("STATIC_RUT_0", '<input class="input_text" name="STATIC_RUT_0" id="STATIC_RUT_0" type="text" value="'.$result[0]['STATIC_RUT'].'" size="10" maxlength="10">');
		$t->setVar("COD_UNIVERSIDAD_DEUDOR_0", '<input class="input_text" name="COD_UNIVERSIDAD_DEUDOR_0" id="COD_UNIVERSIDAD_DEUDOR_0" type="text" value="'.$result[0]['COD_UNIVERSIDAD_DEUDOR'].'" size="10" maxlength="10">');
		$t->setVar("RUT_0", '<input class="input_text" name="RUT_0" id="RUT_0" type="text" value="'.$result[0]['RUT'].'" size="10" maxlength="10">');
		$t->setVar("DIG_VERIF_0", '<input class="input_text" name="DIG_VERIF_0" id="DIG_VERIF_0" type="text" value="'.$result[0]['DIG_VERIF'].'" size="10" maxlength="10">');
		$t->setVar("NOMBRE_0", '<input class="input_text" name="NOMBRE_0" id="NOMBRE_0" type="text" value="'.utf8_encode($result[0]['NOMBRE']).'" size="10" maxlength="10">');
		$t->setVar("APELLIDO_PATERNO_0", '<input class="input_text" name="APELLIDO_PATERNO_0" id="APELLIDO_PATERNO_0" type="text" value="'.utf8_encode($result[0]['APELLIDO_PATERNO']).'" size="10" maxlength="10">');
		$t->setVar("APELLIDO_MATERNO_0", '<input class="input_text" name="APELLIDO_MATERNO_0" id="APELLIDO_MATERNO_0" type="text" value="'.utf8_encode($result[0]['APELLIDO_MATERNO']).'" size="10" maxlength="10">');
		$t->setVar("DIRECCION_0", '<input class="input_text" name="DIRECCION_0" id="DIRECCION_0" type="text" value="'.utf8_encode($result[0]['DIRECCION']).'" size="10" maxlength="10">');
		$t->setVar("NOM_COMUNA_0", '<input class="input_text" name="NOM_COMUNA_0" id="NOM_COMUNA_0" type="text" value="'.utf8_encode($result[0]['NOM_COMUNA']).'" size="10" maxlength="10">');
		$t->setVar("NOM_CIUDAD_0", '<input class="input_text" name="NOM_CIUDAD_0" id="NOM_CIUDAD_0" type="text" value="'.utf8_encode($result[0]['NOM_CIUDAD']).'" size="10" maxlength="10">');
		$t->setVar("EMAIL_0", '<input class="input_text" name="EMAIL_0" id="EMAIL_0" type="text" value="'.utf8_encode($result[0]['EMAIL']).'" size="10" maxlength="10">');
		$t->setVar("TELEFONO_0", '<input class="input_text" name="TELEFONO_0" id="TELEFONO_0" type="text" value="'.utf8_encode($result[0]['TELEFONO']).'" size="10" maxlength="10">');
		$t->setVar("CELULAR_0", '<input class="input_text" name="CELULAR_0" id="CELULAR_0" type="text" value="'.utf8_encode($result[0]['CELULAR']).'" size="10" maxlength="10">');
		
		$result_es_cruzado = $ws_helen->inicio_repro_2($cod_deudor);
		
		if($result_es_cruzado[0]['CANTIDAD'] > 1){
			$result_deuda = $ws_helen->inicio_repro_3($cod_deudor);
		}
		else{
			$result_deuda = $ws_helen->inicio_repro_4($cod_deudor);
		}
		
		$START_BLOCK = '';
		for($i = 0; $i < count($result_deuda); $i++){
			$START_BLOCK .= '<input class="input_text" name="COD_DEUDA_'.$i.'" id="COD_DEUDA_'.$i.'" type="text" value="'.$result_deuda[0]['COD_DEUDA'].'" size="10" maxlength="10">';
			$START_BLOCK .= '<input class="input_text" name="COD_TIPO_DEUDA_'.$i.'" id="COD_TIPO_DEUDA_'.$i.'" type="text" value="'.$result_deuda[0]['COD_TIPO_DEUDA'].'" size="10" maxlength="10">';
			$START_BLOCK .= '<input class="input_text" name="COD_UNIVERSIDAD_'.$i.'" id="COD_UNIVERSIDAD_'.$i.'" type="text" value="'.$result_deuda[0]['COD_UNIVERSIDAD'].'" size="10" maxlength="10">';
			$START_BLOCK .= '<input class="input_text" name="NOM_UNIVERSIDAD_'.$i.'" id="NOM_UNIVERSIDAD_'.$i.'" type="text" value="'.utf8_encode($result_deuda[0]['NOM_UNIVERSIDAD']).'" size="10" maxlength="10">';
			$START_BLOCK .= '<input class="input_text" name="SALDO_CAPITAL_'.$i.'" id="SALDO_CAPITAL_'.$i.'" type="text" value="'.$result_deuda[0]['SALDO_CAPITAL'].'" size="10" maxlength="10">';
			$START_BLOCK .= '<input class="input_text" name="SALDO_MULTA_'.$i.'" id="SALDO_MULTA_'.$i.'" type="text" value="'.$result_deuda[0]['SALDO_MULTA'].'" size="10" maxlength="10">';
		}
		
		$t->setVar("STARBLOCK", $START_BLOCK);
		
		print $t->toString();
		
	}

//session::set('simulacion_cri', 'simulacion');
}
$w = new w_inicio();

?>