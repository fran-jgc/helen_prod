<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_deudor = $_REQUEST['cod_deudor'];
$tipo_doc 	= $_REQUEST['tipo_doc'];

if($tipo_doc == 'original'){
	
	$db 		= new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	
	$sql 		= sql_cambio_documento::get_sql('AJAX_OBTIENE_DOCS', $cod_deudor);
	$db 		= new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	$result 	= $db->build_results($sql);
	$row_count	= $db->count_rows();
		
	If ($row_count == 0) {
		$result  = 0;
		print urlencode('NO_EXISTEN_DOCS');
	}
	else {
		print urlencode(json_encode($result));
	}
}	
else if ($tipo_doc == 'nuevo'){
	$sql 		= sql_cambio_documento::get_sql('AJAX_OBTIENE_DOCS_NUEVOS', $cod_deudor);
	$db 		= new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	$result 	= $db->build_results($sql);
	print urlencode(json_encode($result));
}	
?>