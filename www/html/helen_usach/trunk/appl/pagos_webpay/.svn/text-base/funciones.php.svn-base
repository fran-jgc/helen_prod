<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

function valida_inicio(){
	if (!session::is_set('COD_USUARIO')){
		header ('Location:'."error.php?error=1");/*header ('Location:'."error.php?error=1");*/
	}
}

function encriptar_url($txt_input, $key){	
	$result = '';
	for($i=0; $i<strlen($txt_input); $i++) {
		$char = substr($txt_input, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)+ord($keychar));
		$result.=$char;
	}
	$txt_ouput = base64_encode($result);

	return $txt_ouput;
}

function dencriptar_url($txt_input, $key){	
	$result = '';
	$string = base64_decode($txt_input);
	for($i=0; $i<strlen($string); $i++) {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)-ord($keychar));
		$result.=$char;
	}
	$txt_ouput = $result;
	
	return $txt_ouput;
}
function get_date(){
	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	if (K_TIPO_BD=='oci')
		$sql = "SELECT	To_Char(sysdate, 'dd/mm/yyyy') FECHA_ACTUAL FROM DUAL";
	else if (K_TIPO_BD=='mssql')
		$sql = "SELECT	convert(varchar, getdate(), 103) FECHA_ACTUAL";
		
	$result_fecha = $db-> build_results($sql);
	$fecha_actual = $result_fecha[0]['FECHA_ACTUAL'];
	
	return $fecha_actual;	
}
function get_valor_utm($cod_moneda){
	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	if (K_TIPO_BD=='oci')
		$sql = "SELECT f_mon_get_valor_moneda($cod_moneda, SYSDATE) VALOR_UTM_MENSUAL FROM DUAL";
	else if (K_TIPO_BD=='mssql')
		$sql = "SELECT dbo.f_mon_get_valor_moneda($cod_moneda, GETDATE()) VALOR_UTM_MENSUAL";
		
	$result_moneda = $db-> build_results($sql);
	$valor_utm = $result_moneda[0]['VALOR_UTM_MENSUAL'];
	
	return $valor_utm;
}
class edit_pass extends edit_password {
	var $tabindex;
	var $size;
	var $maxlen;
	
	function edit_pass($field, $size, $maxlen) {
		parent::edit_password($field, $size, $maxlen);
	}
	function draw_entrable($dato, $record) {
		$field = $this->field.'_'.$record;
		if ($this->readonly)
			$ctrl = '<input class="campoformulario" name="'.$field.'" id="'.$field.'" type="'.$this->type.'" readonly="true" value="'.$dato.'" size="'.$this->size.'" maxLength="'.$this->maxlen.'" ';
		else
			$ctrl = '<input class="campoformulario" name="'.$field.'" id="'.$field.'" type="'.$this->type.'" value="'.$dato.'" size="'.$this->size.'" maxLength="'.$this->maxlen.'" ';
		
		if ($this->type != 'hidden' || $this->forzar_js)
			$ctrl .= $this->make_java_script();

		if ($this->style!='')
			$ctrl .= ' style="'.$this->style.'"';			
		// Se asigna el tabindex
		$ctrl .= ' tabindex="'.($this->tabindex + $record).'"';
		
		$ctrl .= '/>';
			
		return $ctrl;
	}
}
function pass_aleatorio(){
	//GENERA EL ALFANUMERICO ALETORIO Y LLENA LA VARIABLE $RES = ALETORIO
	$length = 6;
	$source = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$source .= '1234567890';
	
	if($length>0){
        $RES = "";
        $source = str_split($source,1);
        for($i=1; $i<=$length; $i++){
            mt_srand((double)microtime() * 1000000);
            $num	= mt_rand(1,count($source));
            $RES	.= $source[$num-1];
        }	 
    }
    return $RES;
}
?>