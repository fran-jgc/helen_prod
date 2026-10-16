<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$rut = $_REQUEST['rut'];
if ($rut=='')
	print '';

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = "SELECT D.COD_DEUDOR
			,D.NOMBRE + ' ' + D.APELLIDO_PATERNO + ' ' + D.APELLIDO_MATERNO  NOM_DEUDOR
			,D.DIG_VERIF
			,DA.COD_DEUDA
			,DA.COD_TIPO_DEUDA
		FROM 	DEUDOR D, DEUDA DA
		WHERE 	D.RUT = $rut
		  and DA.COD_DEUDOR = D.COD_DEUDOR
		  and DA.COD_ESTADO_DEUDA = 2";	// exigible
$result = $db->build_results($sql);
for($i=0; $i<count($result);$i++)
	$result[$i]['NOM_DEUDOR'] = urlencode($result[$i]['NOM_DEUDOR']); 
	
print urlencode(json_encode($result));
?>