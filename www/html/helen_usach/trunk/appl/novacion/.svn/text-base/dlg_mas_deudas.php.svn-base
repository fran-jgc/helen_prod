<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
$temp = new Template_appl('dlg_mas_deudas.htm');	


$rut = $_REQUEST["rut_deudor"];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$count_aprobada = "SELECT Count(*) CANTIDAD
					FROM DEUDOR DO, DEUDA DE, NOVACION N
					WHERE DE.COD_DEUDOR = DO.COD_DEUDOR        
						AND DE.COD_TIPO_DEUDA = 'CS'
						AND DO.RUT = $rut
						AND N.COD_DEUDA_ANTIGUA = DE.COD_DEUDA
						AND N.COD_ESTADO_NOVACION = 2";
						
$result_count = $db->build_results($count_aprobada);
$cant_aprobada = $result_count[0]['CANTIDAD'];

if($cant_aprobada>0){
	//echo '1';
	$sql = "SELECT  $cant_aprobada REGISTROS
					,'' OPCION
					,DO.RUT RUT_H
					,DE.COD_DEUDA COD_DEUDA_H
					,DE.COD_DEUDA        
					,ED.NOM_ESTADO_DEUDA
			FROM DEUDOR DO, DEUDA DE , ESTADO_DEUDA ED 
			WHERE DE.COD_DEUDOR = DO.COD_DEUDOR
			AND DE.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA        
			AND DE.COD_TIPO_DEUDA = 'CS'
			AND DO.RUT = $rut
			AND DE.COD_DEUDA NOT IN (SELECT COD_DEUDA_ANTIGUA FROM NOVACION N
									 WHERE DE.COD_DEUDA = N.COD_DEUDA_ANTIGUA
									 AND N.COD_ESTADO_NOVACION = 2)";
			
	$result = $db->build_results($sql);
	$msg = $result[0]['REGISTROS'];
			
}else{
	//echo '2';
	
	$count_no_aprobada = "SELECT Count(*) CANTIDAD
						FROM DEUDOR DO, DEUDA DE
						WHERE DE.COD_DEUDOR = DO.COD_DEUDOR        
							AND DE.COD_TIPO_DEUDA = 'CS'
							AND DO.RUT = $rut";
	
	$result_count = $db->build_results($count_no_aprobada);
	$cant_no_aprobada = $result_count[0]['CANTIDAD'];
	
	$sql = "SELECT $cant_no_aprobada REGISTROS
				,'' OPCION
    			,DO.RUT RUT_H
				,DE.COD_DEUDA COD_DEUDA_H
				,DE.COD_DEUDA        
        		,ED.NOM_ESTADO_DEUDA
		FROM DEUDOR DO, DEUDA DE, ESTADO_DEUDA ED
		WHERE DE.COD_DEUDOR = DO.COD_DEUDOR
			AND DE.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA        
			AND DE.COD_TIPO_DEUDA = 'CS'
			AND DO.RUT = $rut";
	$result = $db->build_results($sql);

}

$result = $db->build_results($sql);
$dw_datos = new datawindow($sql, 'DEUDAS');

$dw_datos->add_control(new edit_text_hidden('REGISTROS'));
$dw_datos->add_control(new edit_radio_button('OPCION', 'S', 'N', '','OPCION_NOVACION'));
$dw_datos->add_control(new edit_text_hidden('RUT_H'));
$dw_datos->add_control(new edit_text_hidden('COD_DEUDA_H'));
$dw_datos->add_control(new static_text('COD_DEUDA'));
$dw_datos->add_control(new static_text('NOM_ESTADO_DEUDA'));
$entrable = true;

$dw_datos->retrieve();
$dw_datos->habilitar($temp, $entrable);
print $temp->toString();
?>