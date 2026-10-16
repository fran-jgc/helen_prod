<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_auditoria_proceso extends w_output {
	function __construct() {
		$this->b_add_visible  = true;
		
		///////////////// CLASS_SQL = OUTPUT///////////////
		$sql = sql_auditoria_proceso::get_sql('OUTPUT');
	 
		parent::__construct('auditoria_proceso', $sql, $_REQUEST['cod_item_menu']);
		//formato
		$this->dw->add_control(new static_num('ACTUAL_REGISTRO'));
		$this->dw->add_control(new static_num("CANT_ERROR"));
		$this->dw->add_control(new static_num("CANT_WARNING"));
		
		// headers
		$FECHA_EJECUCION_PROCESO = new header_date('FECHA_EJECUCION_PROCESO', 'FECHA_EJECUCION_PROCESO', 'Fecha');
		$sql_proceso = sql_auditoria_proceso::get_sql('DD_NOM_PROCESO');
		$NOM_PROCESO = new header_drop_down_string('NOM_PROCESO', 'P.COD_PROCESO', 'Proceso', $sql_proceso);
		$NOM_USUARIO = new header_text('NOM_USUARIO', 'NOM_USUARIO', 'Usuario');
		$ACTUAL_REGISTRO = new header_num('ACTUAL_REGISTRO', 'ACTUAL_REGISTRO', 'Cantidad Registro');
		$CANT_ERROR = new header_num('CANT_ERROR', "F_EP_CANT_ERROR(COD_EJECUCION_PROCESO, 'E')", 'Cantidad Errores');
		$CANT_WARNING = new header_num('CANT_WARNING', "F_EP_CANT_ERROR(COD_EJECUCION_PROCESO,'W')", 'Cantidad Warning');
		
		$this->add_header($FECHA_EJECUCION_PROCESO);
		$this->add_header($NOM_PROCESO);
		$this->add_header($NOM_USUARIO);
		$this->add_header($ACTUAL_REGISTRO);
		$this->add_header($CANT_ERROR);
		$CANT_ERROR->field_bd_order = "CANT_ERROR";
		$this->add_header($CANT_WARNING);
		$CANT_WARNING->field_bd_order = "CANT_WARNING";
		
		///////////////// CLASS_SQL = OUTPUT_DD_COD_ESTADO ///////////////
		
	}
}
?>