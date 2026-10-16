<?php
///////////////////////
// php llamado con ajax en el javascript
////////////////////////
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
$cod_tipo_doc_pago	= $_REQUEST['cod_tipo_doc_pago'];
$cod_deuda			= $_REQUEST['cod_deuda'];


$deposito = 3;
$resultado = "";

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

if ($cod_tipo_doc_pago == $deposito){
	$sql =sql_cambio_documento::get_sql('DD_BANCO_DEPOSITO',$cod_deuda);
	$result = $db->build_results($sql);
	$row_count = $db->count_rows();
	for($i=0;$i<$row_count;$i++){
		$COD_BANCO = $result[$i]['COD_BANCO'];
		$NOM_BANCO = $result[$i]['NOM_BANCO'];
		$resultado .= $COD_BANCO."|".$NOM_BANCO."*";
	}
}else{
	$sql =sql_cambio_documento::get_sql('DD_BANCO');
	$result = $db->build_results($sql);
	$row_count = $db->count_rows();	
	for($i=0;$i<$row_count;$i++){
		$COD_BANCO = $result[$i]['COD_BANCO'];
		$NOM_BANCO = $result[$i]['NOM_BANCO'];
		$resultado .= $COD_BANCO."|".$NOM_BANCO."*";		
	}
}
print $resultado;
?>