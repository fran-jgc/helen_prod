<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$ano = $_REQUEST['ano'];
$rut = $_REQUEST['rut'];
$K_VIGENTE = 2;
$K_CS = 'CS';
$K_CSR = 'CSR';
$K_CR12 = 'CR12';
$K_CRI	= 'CRI';

$sql = sql_suspension::get_sql('AJAX_NO_TIENE_ABONO', $rut, $K_VIGENTE, $K_CS, $K_CSR,$K_CR12,$K_CRI);
$db 		= new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result 	= $db->build_results($sql);
$row_count	= $db->count_rows();

If ($row_count > 0) {
	$result  = 0;
	print urlencode('TIENE_ABONO');
}else {
	print urlencode('ANO_OK');
}
?>