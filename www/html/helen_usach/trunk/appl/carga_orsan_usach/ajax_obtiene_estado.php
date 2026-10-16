<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$rut 	= $_REQUEST['rut'];
$ano 	= $_REQUEST['ano'];
$cod_deuda 	= $_REQUEST['cod_deuda'];
$nro_cuota 	= $_REQUEST['nro_cuota'];

$rut		= ($rut=='') ? "null" : $rut;
$ano		= ($ano=='') ? "null" : $ano;
$cod_deuda	= ($cod_deuda=='') ? "null" : $cod_deuda;
$nro_cuota	= ($nro_cuota=='') ? "null" : $nro_cuota;



$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$sql = "SELECT NOM_ESTADO_CARGA
			  ,COD_ESTADO_CARGA 
		FROM ESTADO_CARGA
		WHERE COD_ESTADO_CARGA IN (dbo.f_get_estado_cb_orsan ($rut,$ano,$cod_deuda,$nro_cuota))";

$result = $db->build_results($sql);
$count=$db->count_rows();

if($count<>0)
$result = $db->build_results($sql);
else 
$result=0;

print urlencode(json_encode($result));
	
?>