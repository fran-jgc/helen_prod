<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wp_carta_base extends w_proceso {
	const K_CARTA = 12;
	 
   	function __construct() {
 		if(K_TIPO_BD == "oci"){
	   		$sql = "select null COD_CARTA
	    			from DUAL";
 		}
   		else if(K_TIPO_BD == "mssql"){
	   		$sql = "select null COD_CARTA";
   		}
 		$this->dws['dw_carta'] = new datawindow($sql); 
    	$sql = "select	COD_CARTA
						,NOM_CARTA
				from	CARTA";
    	$this->dws['dw_carta']->add_control(new drop_down_dw('COD_CARTA', $sql));
    	parent::__construct('carta', $_REQUEST['cod_item_menu'], self::K_CARTA, 'php');
   	}
	function load_dws() {
		parent::load_dws();
		$this->dws['dw_carta']->retrieve($this->cod_proceso);
	}
	function redraw_dws(&$temp) {
		parent::redraw_dws($temp);
		$this->dws['dw_carta']->habilitar($temp, true);
	}
	function make_cmd_php($cod_ejecucion_proceso) {
		$this->dws['dw_carta']->get_values_from_POST();
		$cod_carta = $this->dws['dw_carta']->get_item(0, 'COD_CARTA');

		// Debe retornar el php y sus parametros para ser ejecutado
		return $this->root_url."appl/proc_carta/".K_CLIENTE."/carta.php?cod_ejecucion_proceso=$cod_ejecucion_proceso&cod_carta=$cod_carta&cod_usuario=".$this->cod_usuario;
	}
	
	
}

// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wp_carta.php";
if (file_exists($file_name)) 
	require_once($file_name);
else {
	class wp_carta extends wp_carta_base {
	    function __construct() {
	        parent::__construct(); 
		}
	}
}

?>