<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$rut = urldecode($_REQUEST['rut']);
if($rut == NULL){
	print urlencode('VACIO');
}
else{
	$sql 		= sql_cambio_documento::get_sql('AJAX_EXISTE_RUT',$rut);
	$db 		= new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	$result 	= $db->build_results($sql);
	$row_count	= $db->count_rows();
	If ($row_count == 0) {
		$result  = 0;
		print urlencode('NO_RUT');
	}
	else{
		$sql = sql_cambio_documento::get_sql('AJAX_INFO_DEUDOR',$rut);
		$db 		= new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result 	= $db->build_results($sql);
		$row_count	= $db->count_rows();
	
		If ($row_count == 0) {
			$result  = 0;
		}
		else {
			$result 	= $db->build_results($sql);
			print urlencode(json_encode($result));
		}
	}
}
?>