<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$rut_deudor	= $_REQUEST['rut_deudor'];
$cod_deuda = $_REQUEST['cod_deuda'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$datos_deudor = '';

	//valida cartera diferente a la universidad
	$K_PARAM_CARTERA = 2;
	$cartera_defecto = base::get_parametro($K_PARAM_CARTERA);
	
	$sql = 	"SELECT COUNT(*) CANT
							FROM DEUDOR DR, DEUDA DA 
							WHERE DR.COD_DEUDOR = DA.COD_DEUDOR 
							AND DA.COD_CARTERA  <> $cartera_defecto
							AND DR.RUT = $rut_deudor
							AND DA.COD_DEUDA=$cod_deuda";
												
	$result_cartera = $db->build_results($sql);
			
	if ($result_cartera[0]['CANT']>0)
		$datos_deudor = 'CARTERA_DIFERENTE';
	else	
		$datos_deudor = 'CARTERA_CORRECTA';	

print $datos_deudor;	
?>