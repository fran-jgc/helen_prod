<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");


class dw_item_cartola extends datawindow{
    function __construct() {

        $sql = sql_cartola_banco::get_sql('DW_ITEM_CARTOLA');

        parent::__construct($sql,'ITEM_CARTOLA', true, true);

        $this->add_control(new static_text('COD_ITEM_CARTOLA'));
        $this->add_control(new static_text('DETALLE_MOVIMIENTO'));
        $this->add_control(new static_num('CHEQUE_CARGO'));
        $this->add_control(new static_num('DEPOSITO_ABONO'));
        $this->add_control(new static_num('SALDO'));
		$this->add_control(new static_text('SUCURSAL'));
		$this->add_control(new edit_num('NRO_COMPROBANTE', 10,10));
    }
    
    function update($db){
        $sp = 'spu_item_cartola';
        for ($i = 0; $i < $this->row_count(); $i++) {
        	$statuts = $this->get_status_row($i);
            if ($statuts == K_ROW_NOT_MODIFIED){
                continue;
            }
            // siempre es update
            $nro_comprobante         = $this->get_item($i, 'NRO_COMPROBANTE');
            $cod_item_cartola		 = $this->get_item($i, 'COD_ITEM_CARTOLA');
            
            $nro_comprobante      = ($nro_comprobante =='') ? "null" : $nro_comprobante;
            
            $param = "$nro_comprobante,
            		  $cod_item_cartola";
           	
            if (!$db->EXECUTE_SP($sp, $param)){
                return false;
            }
        }
        return true;
    }
}


class wi_cartola_banco extends w_input {
    function __construct($cod_item_menu) {
        parent::__construct('cartola_banco', $cod_item_menu);
		

		$sql = sql_cartola_banco::get_sql('INPUT');
		$this->dws['dw_cartola_banco'] = new datawindow($sql);
		
		$this->dws['dw_item_cartola']     = new dw_item_cartola();
		
		$this->dws['dw_cartola_banco']->add_control(new static_num('COD_CARTOLA'));
		$this->dws['dw_cartola_banco']->add_control(new static_text('FECHA_REGISTRO'));
		$this->dws['dw_cartola_banco']->add_control(new static_text('NOM_USUARIO'));
		
		$sql = "SELECT COD_CUENTA_CORRIENTE
			   		  ,NOM_CUENTA_CORRIENTE
				FROM CUENTA_CORRIENTE
				ORDER BY COD_CUENTA_CORRIENTE";
		$this->dws['dw_cartola_banco']->add_control($control = new drop_down_dw('COD_CUENTA_CORRIENTE', $sql, 150));
		
		$this->dws['dw_cartola_banco']->add_control(new static_num('NRO_CARTOLA'));
		$this->dws['dw_cartola_banco']->add_control(new static_text('FECHA_CARTOLA'));
		$this->dws['dw_cartola_banco']->add_control(new edit_text_multiline('OBS', 80, 3));
		
	}

	function new_record() {
		$this->dws['dw_cartola_banco']->insert_row();
	}
	
	function load_record() {
		$cod_cartola_banco	= $this->get_item_wo($this->current_record, 'COD_CARTOLA');
		$this->dws['dw_cartola_banco']->retrieve($cod_cartola_banco);
		
		$this->dws['dw_item_cartola']->retrieve($cod_cartola_banco);
		
		$this->dws['dw_cartola_banco']->set_entrable('COD_CUENTA_CORRIENTE', false);
		$this->dws['dw_cartola_banco']->set_entrable('OBS', true);
	}
	function get_key() {
		return $this->dws['dw_cartola_banco']->get_item(0, 'COD_CARTOLA');
	}
	
	function save_record($db) {
        $cod_cartola = $this->dws['dw_cartola_banco']->get_item(0, 'COD_CARTOLA');
        $obs = $this->dws['dw_cartola_banco']->get_item(0, 'OBS');
        
        //$obs      = ($obs =='') ? "null" : $obs;
        
        $sp = 'spu_cartola';
		$param = "$cod_cartola
				 ,'$obs'";
           	
        if (!$db->EXECUTE_SP($sp, $param)){
			return false;
        }
        
        if (!$this->dws['dw_item_cartola']->update($db)){
        	   return false;
        }
        
        return true;
	}
}
?>