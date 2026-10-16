<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");

class wi_inf_deudores_condonados extends w_param_informe_helen{
    function __construct($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_deudores_condonados/inf_deudores_condonados.xml';
		$sql_informe = sql_inf_deudores_condonados::get_sql('INPUT');
		parent::__construct('inf_deudores_condonados', $cod_item_menu, 'Deudores Condonados', $xml, $sql_informe, 'spi_deudores_condonados');
		

		$fecha = getdate();
  		$ano   = $fecha["year"];
  		
		$sql = sql_inf_deudores_condonados::get_sql('PARAMETRO_CONDONACION', $ano);
		$this->dws['dw_param'] = new datawindow($sql);
		
		$this->dws['dw_param']->add_control(new edit_date('FECHA_INICIO'));
		$this->dws['dw_param']->add_control(new edit_date('FECHA_TERMINO'));
		
		$sql_tipo_condonacion = sql_inf_deudores_condonados::get_sql('DD_TIPO_CONDONACION');
		$this->dws['dw_param']->add_control(new drop_down_dw('TIPO_CONDONACION', $sql_tipo_condonacion, 150));
		
		// mandatorys		
		$this->dws['dw_param']->set_mandatory('FECHA_INICIO', 'Fecha Inicio');	
		$this->dws['dw_param']->set_mandatory('FECHA_TERMINO', 'Fecha Termino');
		
	}
	function make_filtro(){
		$fecha_inicio 	  = $this->dws['dw_param']->get_item(0, 'FECHA_INICIO'); 
		$fecha_termino 	  = $this->dws['dw_param']->get_item(0, 'FECHA_TERMINO');
		$tipo_condonacion = $this->dws['dw_param']->get_item(0, 'TIPO_CONDONACION');
		
		if($tipo_condonacion == 1000){
			$filtro_condonacion= 'CONDONACIÓN LEY 19848';			
		}else if($tipo_condonacion == 0){
			$filtro_condonacion= 'Todas';			
		}else{
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$sql_condonacion = "SELECT NOM_TIPO_CONDONACION 
								FROM TIPO_CONDONACION
								WHERE COD_TIPO_CONDONACION = $tipo_condonacion";		
			$result_condonacion = $db->build_results($sql_condonacion);
			$nom_tipo_condonacion = $result_condonacion[0]['NOM_TIPO_CONDONACION'];
			$filtro_condonacion = $nom_tipo_condonacion;
		}
		
		if ($tipo_condonacion == 0){
			$this->xml = session::get('K_ROOT_DIR').'appl/inf_deudores_condonados/inf_deudores_condonados.xml';
		}
		else{
			$this->xml = session::get('K_ROOT_DIR').'appl/inf_deudores_condonados/inf_deudores_tipo_condonados.xml';
		}

		// Filtro
		$this->filtro = "Fecha Inicio = $fecha_inicio\n Fecha Término = $fecha_termino\n Tipo Condonación = $filtro_condonacion";
		$fecha_inicio = $this->str2date($fecha_inicio);	
		$fecha_termino = $this->str2date($fecha_termino);

		 
		// Arma los paramaetros para el SP		
		$this->param = "$fecha_inicio, $fecha_termino, $tipo_condonacion";
	}
}
?>