<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
$rut = $_REQUEST["rut_deudor"];
$cod_deuda = $_REQUEST["cod_deuda"];
$K_DEUDA_CS					= "CS";
$K_COMPONENTE_NO_EXIGIBLE 	= 1;
$K_COMPONENTE_VENCIDA 		= 2;
$K_COMPONENTE_VIGENTE 		= 3;
$K_COMPONENTE_FUTURA 		= 4;

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
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
?>