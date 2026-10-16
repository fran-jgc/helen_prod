<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class wi_inf_arqueo_caja extends w_param_informe_helen {
    
	function __construct($cod_item_menu) {
	    
		$xml = session::get('K_ROOT_DIR').'appl/inf_arqueo_caja/inf_arqueo_caja.xml';
		$sql_informe = sql_inf_arqueo_caja::get_sql('INPUT');
		parent::__construct('inf_arqueo_caja', $cod_item_menu, 'Informe Arqueo Caja', $xml, $sql_informe, 'spi_arqueo_caja');
			
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
  		
		$cod_usuario = $this->cod_usuario;
		$sql = sql_inf_arqueo_caja::get_sql('SELECT', $cod_usuario);
						
		$this->dws['dw_param'] = new datawindow($sql);
		
		$this->dws['dw_param']->add_control(new edit_date('FECHA_INICIO'));
		$this->dws['dw_param']->add_control(new edit_date('FECHA_TERMINO'));
		$this->dws['dw_param']->add_control(new drop_down_list('ESTADO',array('TODAS','COBRADO','POR_COBRAR'),array('TODAS','COBRAD0','POR COBRAR'),100));
		$sql = sql_inf_arqueo_caja::get_sql('DD_USUARIO', $cod_usuario);
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_USUARIO', $sql, 170, '', false));
		
		$sql = sql_inf_arqueo_caja::get_sql('DD_FAMILIA_DEUDA');
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_FAMILIA_DEUDA', $sql, 170, '', false));
		
		$this->dws['dw_param']->add_control(new edit_radio_button('RESUMEN', 'RESUMEN', 'DETALLE','Resumen', 'FORMATO'));
		$this->dws['dw_param']->add_control(new edit_radio_button('DETALLE', 'DETALLE', 'RESUMEN','Detalle', 'FORMATO'));
	}
	function make_filtro() {
		
		$cod_usuario 	= $this->dws['dw_param']->get_item(0, 'COD_USUARIO');
		$fecha_inicio 	= $this->dws['dw_param']->get_item(0, 'FECHA_INICIO');
		$fecha_termino 	= $this->dws['dw_param']->get_item(0, 'FECHA_TERMINO');
		$resumen 		= $this->dws['dw_param']->get_item(0, 'RESUMEN');
		$detalle 		= $this->dws['dw_param']->get_item(0, 'DETALLE');
		$cod_familia_deuda	= $this->dws['dw_param']->get_item(0, 'COD_FAMILIA_DEUDA');

		if($resumen =='RESUMEN'){
			// Filtro
			$nom_usuario = $this->dws['dw_param']->controls['COD_USUARIO']->get_label_from_value($cod_usuario);
			$this->filtro = "Cajero = $nom_usuario\n";
			$this->filtro .= "Fecha Inicio = $fecha_inicio\n";
			$this->filtro .= "Fecha Término = $fecha_termino\n";
			$nom_familia_deuda = $this->dws['dw_param']->controls['COD_FAMILIA_DEUDA']->get_label_from_value($cod_familia_deuda);
			$this->filtro .= "Familia Deuda = $nom_familia_deuda\n";
					
			// Arma los parametros para el SP
			$fecha_inicio =$this->str2date($fecha_inicio);
			$fecha_termino =$this->str2date($fecha_termino);
			$this->param = "$cod_usuario, $fecha_inicio, $fecha_termino, '$resumen', $cod_familia_deuda";
			
			$this->sp = 'spi_arqueo_caja';
			$xml = session::get('K_ROOT_DIR').'appl/inf_arqueo_caja/'.K_CLIENTE.'/inf_arqueo_caja_resumen.xml';
			if (file_exists($xml))
				$this->xml = $xml;
			else 
				$this->xml = session::get('K_ROOT_DIR').'appl/inf_arqueo_caja/inf_arqueo_caja_resumen.xml';
		}
		else if($detalle =='DETALLE'){
			// Filtro
			$nom_usuario = $this->dws['dw_param']->controls['COD_USUARIO']->get_label_from_value($cod_usuario);
			$this->filtro = "Cajero = $nom_usuario\n";
			$this->filtro .= "Fecha Inicio = $fecha_inicio\n";
			$this->filtro .= "Fecha Término = $fecha_termino\n";
			$nom_familia_deuda = $this->dws['dw_param']->controls['COD_FAMILIA_DEUDA']->get_label_from_value($cod_familia_deuda);
			$this->filtro .= "Familia Deuda = $nom_familia_deuda\n";
			
					
			// Arma los parametros para el SP
			$fecha_inicio =$this->str2date($fecha_inicio);
			$fecha_termino =$this->str2date($fecha_termino);
			$this->param = "$cod_usuario, $fecha_inicio, $fecha_termino, '$detalle', $cod_familia_deuda";
			
			$this->sp = 'spi_arqueo_caja';
			$xml = session::get('K_ROOT_DIR').'appl/inf_arqueo_caja/'.K_CLIENTE.'/inf_arqueo_caja_detalle.xml';
			if (file_exists($xml))
				$this->xml = $xml;
			else 
				$this->xml = session::get('K_ROOT_DIR').'appl/inf_arqueo_caja/inf_arqueo_caja_detalle.xml';						
		}
	}
	
	function genera_pdf($labels = array(), $con_logo = true,$orientation='L',$unit='pt',$format='letter') {
			parent::genera_pdf($labels, $con_logo,$orientation, $unit,$format);
		}
}
?>