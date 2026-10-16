<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$w_proceso = $_REQUEST['w_proceso'];
$w = unserialize(session::get($w_proceso));
$w->dws['dw_ejecucion_actual']->retrieve($w->cod_proceso);
$data = $w->dws['dw_ejecucion_actual']->data;
if ($data[0]['ACTUAL_FECHA_TERMINO'] == '__NO_RUN__') {	// ya termino el proceso
	$w->dws['dw_ejecucion_anterior']->retrieve($w->cod_proceso);
	$data2 = $w->dws['dw_ejecucion_anterior']->data;

	if ($data2[0]['LAST_CANCELA_PROCESO']=='S')
		$data[0]['ACTUAL_AVANCE'] = $data2[0]['ANTERIOR_AVANCE'];
	else
		$data[0]['ACTUAL_AVANCE'] = 100;
	$data[0]['ACTUAL_DURACION'] = $data2[0]['LAST_DURACION'];
	$data[0]['ACTUAL_CANT_ERROR'] = $data2[0]['LAST_CANT_ERROR'];
	$data[0]['ACTUAL_CANT_WARNING'] = $data2[0]['LAST_CANT_WARNING'];
	$data[0]['ARCHIVO_RESULTADO'] = $data2[0]['ARCHIVO_RESULTADO'];
	$data[0]['ACTUAL_ACTUAL_REGISTRO'] = $data2[0]['LAST_ACTUAL_REGISTRO'];
}
print urlencode(json_encode($data[0]));
?>