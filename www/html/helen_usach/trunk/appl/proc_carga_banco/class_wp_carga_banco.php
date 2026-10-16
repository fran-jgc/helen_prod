<?php
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wp_carga_banco_base extends w_proceso{
    
	const K_CARGA_BANCO = 19;

	var $archivo_cargado = false;
	
   	function __construct(){
   		/*Se agrega la fecha_deposito aqui ya que en la clase que esta en la carpeta USACH no funciono*/
   		if(K_TIPO_BD == "oci"){
			$sql = "select	null COD_ORIGEN_CARGA_BANCO
							,to_char(sysdate,'dd/mm/yyyy') FECHA_DEPOSITO
                    from 	DUAL";
 		}
   			
 		$this->dws['dw_carga_banco'] = new datawindow($sql); 
 			$sql = "SELECT	COD_ORIGEN_CARGA_BANCO
						,NOM_ORIGEN_CARGA_BANCO
				FROM	ORIGEN_CARGA_BANCO
				ORDER BY NOM_ORIGEN_CARGA_BANCO ASC";
    	$this->dws['dw_carga_banco']->add_control($control = new drop_down_dw('COD_ORIGEN_CARGA_BANCO', $sql));
    	
    	$control->set_onChange("oculta_muestra_f_depos(this);");
    	
    	$this->dws['dw_carga_banco']->add_control(new edit_date('FECHA_DEPOSITO', 10,10));
    	
    	    	
    	parent::__construct('carga_banco', $_REQUEST['cod_item_menu'], self::K_CARGA_BANCO);
   	}
   	
   	function load_dws() {
		parent::load_dws();
		$this->dws['dw_carga_banco']->retrieve($this->cod_proceso);
	}
   	
	function redraw_dws(&$temp) {
		
		parent::redraw_dws($temp);
		$this->dws['dw_carga_banco']->habilitar($temp, true);
		if ($this->archivo_cargado)
			$temp->setVar("ARCHIVO_CARGADO", "                    Archivo cargado");
		else
			$temp->setVar("ARCHIVO_CARGADO", "");
	}

	
}

// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wp_carga_banco.php";
if (file_exists($file_name)) {
	require_once($file_name);
}
else {
	class wp_carga_banco extends wp_carga_banco_base {
	    function __construct() {
	        parent::__construct('carga_banco', $_REQUEST['cod_item_menu'], self::K_CARGA_BANCO); 
		}
	}
}
?>