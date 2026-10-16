<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$mes_actual = $_REQUEST['mes_actual'];
$ano_actual = $_REQUEST['ano_actual'];
$cod_moneda = $_REQUEST['cod_moneda'];
		
$wi_moneda = unserialize(session::get("wi_moneda"));		
$sql = $wi_moneda->dws['dw_valor_moneda']->get_sql();
$sql = str_replace("{KEY1}", $cod_moneda, $sql);
$sql = str_replace("{KEY2}", $mes_actual, $sql);
$sql = str_replace("{KEY3}", $ano_actual, $sql);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);			
$respuesta='';
for($i=1; $i<=31; $i++){
	$respuesta = $respuesta.$result[0]["DIA$i"]."|".$result[0]["VALOR$i"]."|";
}
print $respuesta;
?>