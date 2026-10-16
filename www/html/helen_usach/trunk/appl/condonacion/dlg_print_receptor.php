<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
$temp = new Template_appl('dlg_print_receptor.htm');	

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$nom_receptor = '';
$current_date = $db->current_date();
$cod_usuario = session::get("COD_USUARIO");
$tipo_doc  = '';
$cod_doc  = '';

$entrable = true;

if (K_TIPO_BD=="oci"){
	$sql = "SELECT	'' NOM_RECEPTOR
					,'' FECHA_REGISTRO_H
					,'' COD_USUARIO_H
					,'' TIPO_DOC_H
					,'' COD_DOC_H
			FROM	DUAL";
}else if (K_TIPO_BD=="mssql"){
	$sql = "SELECT	'' NOM_RECEPTOR
					,'' FECHA_REGISTRO_H
					,'' COD_USUARIO_H
					,'' TIPO_DOC_H
					,'' COD_DOC_H";	
}

$dw_datos = new datawindow($sql);

$dw_datos->add_control(new edit_text_upper('NOM_RECEPTOR',40,40));
$dw_datos->add_control(new edit_text_hidden('FECHA_REGISTRO_H'));
$dw_datos->add_control(new edit_text_hidden('COD_USUARIO_H'));
$dw_datos->add_control(new edit_text_hidden('TIPO_DOC_H'));
$dw_datos->add_control(new edit_text_hidden('COD_DOC_H'));

$dw_datos->insert_row();
$dw_datos->set_item(0, 'NOM_RECEPTOR',$nom_receptor);
$dw_datos->set_item(0, 'FECHA_REGISTRO_H',$current_date);
$dw_datos->set_item(0, 'COD_USUARIO_H',$cod_usuario);
$dw_datos->set_item(0, 'TIPO_DOC_H',$tipo_doc);
$dw_datos->set_item(0, 'COD_DOC_H',$cod_doc);

$dw_datos->habilitar($temp, $entrable);
$menu = unserialize(session::get('menu_appl'));
$menu->draw($temp);	
print $temp->toString();
?>