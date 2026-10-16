<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
$cod_deuda	= $_REQUEST['cod_deuda'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql= sql_boleta::get_sql('AJAX_DEUDA_MOROSA',$cod_deuda);
$result = $db->build_results($sql);
$count = count($result);
$cadena_morosa = '';

if($count > 0){
	for($x=0;$x<$count;$x=$x+1){
		$ano = $result[$x]['ANO_COBRO'];
		if($cadena_morosa == ''){
			$cadena_morosa = $ano;
		}
		else{
			$cadena_morosa = $cadena_morosa.', '.$ano;
		}
	}
}
print $cadena_morosa;	
?>