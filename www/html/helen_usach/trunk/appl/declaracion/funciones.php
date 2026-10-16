<?php
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

function base64url_decode($data) {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
} 
?>