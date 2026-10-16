<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
	
	$temp = new Template_appl('dlg_find_suspension.htm');
	$rut = $_REQUEST['rut'];

 IF (K_CLIENTE == 'UTEM') {
	$sql_dw="select FD.COD_FAMILIA_DEUDA,NOM_FAMILIA_DEUDA,TP.COD_TIPO_DEUDA,D.COD_DEUDA, NULL RADIO 
			from FAMILIA_DEUDA FD
			,TIPO_DEUDA TP 
			,DEUDA D , DEUDOR DE
			WHERE FD.COD_FAMILIA_DEUDA = TP.COD_FAMILIA_DEUDA
			AND D.COD_TIPO_DEUDA = TP.COD_TIPO_DEUDA
			AND D.COD_DEUDOR = DE.COD_DEUDOR
			AND D.COD_TIPO_DEUDA IN ('CS','CSR','CSI','CI','CAME')
			AND DE.RUT = $rut";
}ELSE IF  (K_CLIENTE == 'UCT') {
	$sql_dw="select FD.COD_FAMILIA_DEUDA,NOM_FAMILIA_DEUDA,TP.COD_TIPO_DEUDA,D.COD_DEUDA, NULL RADIO 
			from FAMILIA_DEUDA FD
			,TIPO_DEUDA TP 
			,DEUDA D , DEUDOR DE
			WHERE FD.COD_FAMILIA_DEUDA = TP.COD_FAMILIA_DEUDA
			AND D.COD_TIPO_DEUDA = TP.COD_TIPO_DEUDA
			AND D.COD_DEUDOR = DE.COD_DEUDOR
			AND D.COD_ESTADO_DEUDA=2
			AND DE.RUT = $rut";
	
}ELSE IF  (K_CLIENTE == 'USACH') {
	$sql_dw="select FD.COD_FAMILIA_DEUDA,NOM_FAMILIA_DEUDA,TP.COD_TIPO_DEUDA,D.COD_DEUDA, NULL RADIO 
			from FAMILIA_DEUDA FD
			,TIPO_DEUDA TP 
			,DEUDA D , DEUDOR DE
			WHERE FD.COD_FAMILIA_DEUDA = TP.COD_FAMILIA_DEUDA
			AND D.COD_TIPO_DEUDA = TP.COD_TIPO_DEUDA
			AND D.COD_DEUDOR = DE.COD_DEUDOR
			AND D.COD_TIPO_DEUDA IN ('CS','CSR','CSN','CR12','CRI','CAME')
			AND D.COD_ESTADO_DEUDA = 2
			AND DE.RUT = $rut";
	
}ELSE{

	$sql_dw="select FD.COD_FAMILIA_DEUDA,NOM_FAMILIA_DEUDA,TP.COD_TIPO_DEUDA,D.COD_DEUDA, NULL RADIO 
			from FAMILIA_DEUDA FD
			,TIPO_DEUDA TP 
			,DEUDA D , DEUDOR DE
			WHERE FD.COD_FAMILIA_DEUDA = TP.COD_FAMILIA_DEUDA
			AND D.COD_TIPO_DEUDA = TP.COD_TIPO_DEUDA
			AND D.COD_DEUDOR = DE.COD_DEUDOR
			AND DE.RUT = $rut";
}
	
	$dw_llamado = new datawindow($sql_dw,'LLAMADO',true,true);
	//$dw_llamado->add_control(new static_text('COD_LLAMADO',1));
	$dw_llamado->add_control(new static_text('NOM_FAMILIA_DEUDA',1));
	$dw_llamado->add_control(new static_text('COD_TIPO_DEUDA',1));
	$dw_llamado->add_control(new static_text('COD_DEUDA',1));
	$dw_llamado->add_control(new edit_radio_button('RADIO', '', '', '', 'RADIO'));


$dw_llamado->retrieve();


$dw_llamado->habilitar($temp, true);
print $temp->toString();
?>