<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_renegociacion extends w_output {
    function __construct() {
		$this->b_add_visible  = true;
		
		///////////////// CLASS_SQL = OUTPUT///////////////
		$sql = sql_renegociacion::get_sql('OUTPUT');
	 
		parent::__construct('renegociacion', $sql, $_REQUEST['cod_item_menu']);
		
		// formato
		$this->dw->add_control(new static_num('RUT'));

		//HEADERS
		$COD_RENEGOCIACION = new header_num('COD_RENEGOCIACION', 'COD_RENEGOCIACION', 'N° Reneg.');
		$FECHA_RENEGOCIACION = new header_date('FECHA_RENEGOCIACION', 'FECHA_RENEGOCIACION', 'Fecha');
		$RUT = new header_rut('RUT', 'DR', 'Rut');
		$NOMBRE = new header_text('NOMBRE', 'NOMBRE', 'Nombre');
		$APELLIDO_PATERNO = new header_text('APELLIDO_PATERNO', 'APELLIDO_PATERNO', 'Paterno');
		$APELLIDO_MATERNO = new header_text('APELLIDO_MATERNO', 'APELLIDO_MATERNO', 'Materno');
		$COD_DEUDA = new header_num('COD_DEUDA', 'DA.COD_DEUDA', 'N° Deuda');
		$sql = "select TD.COD_TIPO_DEUDA, TD.NOM_TIPO_DEUDA, U.NOM_USUARIO from TIPO_DEUDA TD, USUARIO U";	
		$COD_TIPO_DEUDA = new header_drop_down('COD_TIPO_DEUDA', 'DA.COD_TIPO_DEUDA', 'Tipo Deuda', $sql);
		$sql_estado = sql_renegociacion::get_sql("OUTPUT_DD_COD_ESTADO");
		$NOM_ESTADO_RENEGOCIACION = new header_drop_down('NOM_ESTADO_RENEGOCIACION', 'ER.COD_ESTADO_RENEGOCIACION', 'Estado', $sql_estado);
		$NOM_USUARIO = new header_text('NOM_USUARIO', 'U.NOM_USUARIO','Usuario');
		$sql_autoriza = sql_renegociacion::get_sql('DD_AUTORIZADO');
		$AUTORIZADO = new header_drop_down_string('AUTORIZADO', 'R.AUTORIZADO', 'Autorizado', $sql_autoriza);
		
		$this->add_header($COD_RENEGOCIACION);
		$this->add_header($FECHA_RENEGOCIACION);
		$this->add_header($RUT);
		$this->add_header($NOMBRE);
		$this->add_header($APELLIDO_PATERNO);
		$this->add_header($APELLIDO_MATERNO);
		$this->add_header($COD_DEUDA);
        $this->add_header($COD_TIPO_DEUDA);
		$this->add_header($NOM_USUARIO);
		$this->add_header($NOM_ESTADO_RENEGOCIACION);
     	$this->add_header($AUTORIZADO);
	}
}
?>
