<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wp_carta_aviso extends w_proceso {
	const K_CARTA_AVISO = 15;

	function __construct() {
		$sql = "select null COD_CARTA
    			from DUAL";
		$this->dws['dw_carta_aviso'] = new datawindow($sql);
		$sql = "SELECT 1 COD_CARTA, 'PRIMERA CARTA' NOM_CARTA FROM dual
				UNION
				SELECT 2 COD_CARTA, 'SEGUNDA CARTA' NOM_CARTA FROM dual
				UNION
				SELECT 3 COD_CARTA, 'TERCERA CARTA' NOM_CARTA FROM dual";
		$this->dws['dw_carta_aviso']->add_control(new drop_down_dw('COD_CARTA', $sql));
		parent::__construct('carta_aviso', $_REQUEST['cod_item_menu'], self::K_CARTA_AVISO, 'php');
	}
	function load_dws() {
		parent::load_dws();
		$this->dws['dw_carta_aviso']->retrieve($this->cod_proceso);
	}
	function redraw_dws(&$temp) {
		parent::redraw_dws($temp);
		$this->dws['dw_carta_aviso']->habilitar($temp, true);
	}
	function make_cmd_php($cod_ejecucion_proceso) {
		$this->dws['dw_carta_aviso']->get_values_from_POST();
		$cod_carta = $this->dws['dw_carta_aviso']->get_item(0, 'COD_CARTA');
		// Debe retornar el php y sus parametros para ser ejecutado
		$sp = 'spu_fecha_carta_aviso';
		$param = "$cod_carta, $this->cod_usuario";
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		if (!$db->EXECUTE_SP($sp, $param)){
			return false;
		}
		return $this->root_url."appl/proc_carta_aviso/carta_aviso.php?cod_ejecucion_proceso=$cod_ejecucion_proceso&cod_carta=$cod_carta";
	}
}
?>