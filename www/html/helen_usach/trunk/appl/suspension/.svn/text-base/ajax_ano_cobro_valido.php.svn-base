<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$ano = $_REQUEST['ano'];
$rut = $_REQUEST['rut'];
$cod_deuda = $_REQUEST['cod_deuda'];
$K_ANULADA = 3;


$db_td  = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql_tipo_deuda = "SELECT COD_TIPO_DEUDA FROM DEUDA
						WHERE cod_deuda = $cod_deuda";
$result_tipo_deuda 	= $db_td->build_results($sql_tipo_deuda);
$tipo_deuda = $result_tipo_deuda[0]['COD_TIPO_DEUDA'];


$db  = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = sql_suspension::get_sql('AJAX_ANO_COBRO_EXISTE', $cod_deuda, $ano);
$result 	= $db->build_results($sql);
$row_count	= $db->count_rows();

$db_susp  = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql_tiene_suspension = "SELECT * FROM suspension
						WHERE cod_deuda = $cod_deuda
						AND ano_suspension = $ano
						AND cod_estado_suspension <> $K_ANULADA";
$result_susp 	= $db_susp->build_results($sql_tiene_suspension);
$row_count_susp	= $db_susp->count_rows();



if ($tipo_deuda != 'CS'){
	If ($row_count_susp > 0) {
		$result_susp  = 0;
		print urlencode('TIENE_SUSPENSION');
	}else If ($row_count > 0) {
		$result  = 0;
		print urlencode('S');
	}else {
		print urlencode('N');
	}
}else if ($tipo_deuda == 'CS')
{
	If ($row_count_susp > 0) {
		$result_susp  = 0;
		print urlencode('TIENE_SUSPENSION');
	}else {
		print urlencode('S');
	}
}
?>