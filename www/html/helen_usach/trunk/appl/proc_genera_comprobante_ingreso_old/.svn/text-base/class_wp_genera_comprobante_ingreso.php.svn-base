<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

ini_set('memory_limit', '750M');
ini_set('max_execution_time', 1200); //1200 seconds = 40 minutes 
ini_set('mssql.connect_timeout','400');
ini_set('mssql.timeout','400');

class wp_genera_comprobante_ingreso extends w_proceso {
	const K_GENERA_COMPROBANTE_INGRESO = 25;
	 
   	function __construct() {
   		
   		$sql = "SELECT	to_char(sysdate, 'dd/mm/yyyy') FECHA_INICIO
   		               ,to_char(sysdate, 'dd/mm/yyyy') FECHA_TERMINO
   		               ,to_char(sysdate, 'dd/mm/yyyy') FECHA_COMPROBANTE
   		               ,6 COD_ORIGEN_PAGO
   		               ,'none' DISPLAY_RESULTADO
   		        from dual";
		
		$this->dws['dw_genera_comprobante_ingreso'] = new datawindow($sql);
		
		$sql = "SELECT	COD_ORIGEN_PAGO
						,NOM_ORIGEN_PAGO
				FROM	ORIGEN_PAGO
				where COD_ORIGEN_PAGO in (1)
				ORDER BY NOM_ORIGEN_PAGO ASC";
    	$this->dws['dw_genera_comprobante_ingreso']->add_control($control = new drop_down_dw('COD_ORIGEN_PAGO', $sql,120));
    	$this->dws['dw_genera_comprobante_ingreso']->add_control(new edit_date('FECHA_INICIO', 12, 10));
    	$this->dws['dw_genera_comprobante_ingreso']->add_control(new edit_date('FECHA_TERMINO', 12, 10));
    	$this->dws['dw_genera_comprobante_ingreso']->add_control(new edit_date('FECHA_COMPROBANTE', 12, 10));
    	
    	parent::__construct('genera_comprobante_ingreso', $_REQUEST['cod_item_menu'], self::K_GENERA_COMPROBANTE_INGRESO);
   	}
	function load_dws() {
		parent::load_dws();
		$this->dws['dw_genera_comprobante_ingreso']->retrieve($this->cod_proceso);
		
	}
	function redraw_dws(&$temp) {
		parent::redraw_dws($temp);
		$this->dws['dw_genera_comprobante_ingreso']->habilitar($temp, true);
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		
		if(isset($_POST['b_init'])){
			$this->dws['dw_genera_comprobante_ingreso']->get_values_from_POST();
			$fecha_inicio= $this->dws['dw_genera_comprobante_ingreso']->get_item(0, 'FECHA_INICIO');
			$fecha_termino= $this->dws['dw_genera_comprobante_ingreso']->get_item(0, 'FECHA_TERMINO');
			$fecha_comprobante= $this->dws['dw_genera_comprobante_ingreso']->get_item(0, 'FECHA_COMPROBANTE');
			$origen_pago= $this->dws['dw_genera_comprobante_ingreso']->get_item(0, 'COD_ORIGEN_PAGO');
			$fecha_inicio = $this->str2date($fecha_inicio);
			$fecha_termino = $this->str2date($fecha_termino, "23:59:59");
			$fecha_comprobante = $this->str2date($fecha_comprobante);
			$cod_usuario= $this->cod_usuario;
			
			$sp = "spp_comprobante_ingreso";
			$param = "$origen_pago,$fecha_inicio,$fecha_termino,$fecha_comprobante,$cod_usuario";
			
			if (!$db->EXECUTE_SP($sp, $param)){
				$this->alert('Error en proceso');
			}else{
				$this->alert('Proceso Terminado');
			}
		}
	}
}
?>