<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_campo = $_REQUEST['cod_campo'];
$value = $_REQUEST['value'];

$K_SOLIDARIO = 1;
$K_INSTITUCIONAL = 2;

	if($value == $K_SOLIDARIO){
		$sql = "SELECT COD_TIPO_DEUDA COD_TIPO_DEUDA
					  ,NOM_TIPO_DEUDA NOM_TIPO_DEUDA
				FROM TIPO_DEUDA 
				WHERE COD_FAMILIA_DEUDA =$value
				UNION
				SELECT	'CS_5-CS_35' COD_TIPO_DEUDA
						,'CS 5% / CS 3,5 UTM'	NOM_TIPO_DEUDA
				UNION
				SELECT	'CS_20'	COD_TIPO_DEUDA
						,'CREDITO SOLIDARIO DPA o 20%' NOM_TIPO_DEUDA
				UNION
				SELECT	'CSR'	COD_TIPO_DEUDA
						,'CREDITO SOLIDARIO REPROGRAMADO' NOM_TIPO_DEUDA
				UNION
				SELECT	'CSR_5-CSR_35'	COD_TIPO_DEUDA
						,'CSR 5% / CSR 3,5 UTM'	NOM_TIPO_DEUDA";
	}
	else if($value == $K_INSTITUCIONAL){
		$sql = "SELECT COD_TIPO_DEUDA COD_TIPO_DEUDA
					  ,NOM_TIPO_DEUDA NOM_TIPO_DEUDA
				FROM TIPO_DEUDA 
				WHERE COD_FAMILIA_DEUDA =$value";
	}
	//$nom = 'NOM_TIPO_DEUDA';

//$sql = base::replace_KEYS($sql, array($value));
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);

$result_final = array();
for($i=0; $i<count($result); $i++) {
	$result_final[$i] = $result[$i]; 
}
print urlencode(json_encode($result_final));
?>