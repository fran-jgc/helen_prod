<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class edit_porcentaje extends edit_num {
    function __construct($field, $size = 6, $maxlen = 6, $num_dec=2) {
        parent::__construct($field, $size, $maxlen, $num_dec, true);
	}
}
class drop_down_mes extends drop_down_dw {	
    function __construct($field) {
		$sql_mes = "SELECT COD_MES, UPPER(NOM_MES) FROM MES";
		parent::__construct($field, $sql_mes, 180);
	}
	function draw_no_entrable($dato, $record) {
		return $this->draw_entrable($dato, $record);
	}
}
class edit_ano_entrable extends edit_ano {
    function __construct($field) {
        parent::__construct($field);
	}	
	function draw_no_entrable($dato, $record) {
		return $this->draw_entrable($dato, $record);
	}
}

class dw_valor_moneda extends datawindow {
    function __construct() {
		$sql = sql_moneda::get_sql('INPUT');
		parent::__construct($sql);		
		
		$sql_mes = sql_moneda::get_sql('INPUT_DD_MES');
		
		$this->add_control($control = new drop_down_mes('MES'));			
		$control->set_onChange("cambia_mes_y_ano();");
		$this->add_control($control = new edit_ano_entrable('ANO'));
		$control->set_onChange("cambia_mes_y_ano();");
		
		//mandatorys
		$this->set_mandatory('MES', 'Mes');
		$this->set_mandatory('ANO', 'Ano');
		
		for ($i=1; $i <= 31; $i++) {
			$this->add_control(new edit_num('VALOR'.$i, 16,16,2));			
			$this->add_control(new static_text('DIA'.$i));			
		}
		
	}
	
	function retrieve($cod_moneda, $mes, $ano) {
		parent::retrieve($cod_moneda, $mes, $ano);
		for ($i=29; $i <= 31; $i++) {
			$dia = $this->get_item(0, 'DIA'.$i);
			if ($dia == ' ')
				$this->controls['VALOR'.$i]->type = 'hidden';
			else
				$this->controls['VALOR'.$i]->type = 'text';
		}
	}
	function update($db, $cod_moneda, $valor_mensual, $monto_valor_menual) {		
		$mes = $this->get_item(0, 'MES');
		$ano = $this->get_item(0, 'ANO');		 
		for ($i=1; $i <= 31; $i++) {
			if ($valor_mensual=='S'){
				$valor = $monto_valor_menual;				
			} 
			else{
				$valor = $this->get_item(0, 'VALOR'.$i);
			}		
			$sp = 'spu_valor_moneda';			
			$param = $cod_moneda.",".$i.",".$mes.",".$ano.",".$valor;
			if (!$db->EXECUTE_SP($sp,$param))
				return false;
		}
		return true;
	}
}
class edit_monto_valor_mensual extends edit_num {
    function __construct() {
        parent::__construct('MONTO_VALOR_MENSUAL', 10, 10, 2);
	}
	function draw_no_entrable($dato, $record) {
		$htm = parent::draw_no_entrable($dato, $record);
		$field = $this->field.'_'.$record;
		$ctrl = '<label id="'.$field.'" >'.$htm.'</label>';		
		return $ctrl;
	}
}
class wi_moneda extends w_input {
	var $mes = 0;
	var $ano = 0;

	function __construct($cod_item_menu) {
	    parent::__construct('moneda', $cod_item_menu);

		$this->b_delete_visible = false;
		$sql = sql_moneda::get_sql('INPUT_DD_MONEDA');
		$this->dws['dw_moneda'] = new datawindow($sql);

		// asigna los formatos		
		$this->dws['dw_moneda']->add_control(new edit_text_upper('NOM_MONEDA', 75, 100));		
		$this->dws['dw_moneda']->add_control(new edit_text('SIMBOLO', 10, 10));
		$this->dws['dw_moneda']->add_control(new edit_num('PORC_PROYECTADA',10,10,4));
		$this->dws['dw_moneda']->add_control($control = new edit_check_box('VALOR_MENSUAL','S', 'N'));
		$control->set_onChange("muestra_campo();");
		
		$this->dws['dw_moneda']->add_control(new edit_monto_valor_mensual());
		$this->dws['dw_moneda']->add_control(new static_text('MES_H', 10, 10));
		$this->dws['dw_moneda']->add_control(new static_text('ANO_H', 10, 10));
		
		// asigna los mandatorys		
		$this->dws['dw_moneda']->set_mandatory('NOM_MONEDA', 'Nombre');
		$this->dws['dw_moneda']->set_mandatory('SIMBOLO', 'Símbolo');
				
		$this->dws['dw_valor_moneda'] = new dw_valor_moneda();
	}
	function new_record() {
		$this->dws['dw_moneda']->insert_row();
		$this->mes = date("m");
		$this->ano = date("Y");
		
		$this->dws['dw_valor_moneda']->retrieve(0, $this->mes, $this->ano);
	}
	function load_record() {
		$cod_moneda = $this->get_item_wo($this->current_record, 'COD_MONEDA');		

		if ($this->mes==0) {
			$this->mes = date("m");		 
			$this->ano = date("Y");		
		}
		$this->dws['dw_moneda']->retrieve($cod_moneda, $this->mes, $this->ano);
		
		if($cod_moneda == 1){
			$this->dws['dw_moneda']->set_entrable('MONTO_VALOR_MENSUAL', false);
			$this->dws['dw_moneda']->set_entrable('VALOR_MENSUAL', false);			
		}
		else{		
			$this->dws['dw_moneda']->set_entrable('MONTO_VALOR_MENSUAL', true);
			$this->dws['dw_moneda']->set_entrable('VALOR_MENSUAL', true);
		}
		
		///// MUESTRA LOS VALORES POR DIA O EL CAMPO VALOR MENSUAL ///////
		$valor_mensual = $this->dws['dw_moneda']->get_item(0, 'VALOR_MENSUAL');
		$monto_valor_mensual = $this->dws['dw_moneda']->get_item(0, 'DIV_MONTO_VALOR_MENSUAL');
		$tabla_valores = $this->dws['dw_moneda']->get_item(0, 'TABLA_VALORES');
		
		if($valor_mensual == 'S'){
			$this->dws['dw_moneda']->set_item(0, 'DIV_MONTO_VALOR_MENSUAL','');
			$this->dws['dw_moneda']->set_item(0, 'TABLA_VALORES','none');			
		}
		else{
			$this->dws['dw_moneda']->set_item(0, 'DIV_MONTO_VALOR_MENSUAL','none');
			$this->dws['dw_moneda']->set_item(0, 'TABLA_VALORES','');
		}
		
		$this->dws['dw_valor_moneda']->retrieve($cod_moneda, $this->mes, $this->ano);		
	}
	function get_key() {
		return $this->dws['dw_moneda']->get_item(0, 'COD_MONEDA');
	}
	function modify_record() {
		$this->mes = $_POST['MES_0'];
		$this->ano = $_POST['ANO_0'];
		return parent::modify_record();
	}
	function save_record($db) {
		$COD_MONEDA 		= $this->get_key();
		$NOM_MONEDA 		= $this->dws['dw_moneda']->get_item(0, 'NOM_MONEDA');
		$SIMBOLO 			= $this->dws['dw_moneda']->get_item(0, 'SIMBOLO');
		$VALOR_MENSUAL 		= $this->dws['dw_moneda']->get_item(0, 'VALOR_MENSUAL');
		$COD_MONEDA 		= ($COD_MONEDA=='') ? "null" : $COD_MONEDA;
		$PORC_PROYECTADA	= $this->dws['dw_moneda']->get_item(0, 'PORC_PROYECTADA');		
    
		$this->mes = $this->dws['dw_valor_moneda']->get_item(0, 'MES');
		$this->ano = $this->dws['dw_valor_moneda']->get_item(0, 'ANO');
		
		$sp = 'spu_moneda';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    
	    $param	= "'$operacion', $COD_MONEDA, '$NOM_MONEDA','$SIMBOLO', '$VALOR_MENSUAL',$PORC_PROYECTADA";	    
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$cod_moneda = $db->GET_IDENTITY();
				$this->dws['dw_moneda']->set_item(0, 'COD_MONEDA', $cod_moneda);
			}
			$valor_mensual = $this->dws['dw_moneda']->get_item(0, 'VALOR_MENSUAL');
			$monto_valor_mensual = $this->dws['dw_moneda']->get_item(0, 'MONTO_VALOR_MENSUAL');			 
			$cod_moneda = $this->dws['dw_moneda']->get_item(0, 'COD_MONEDA');
			return $this->dws['dw_valor_moneda']->update($db, $cod_moneda, $valor_mensual, $monto_valor_mensual);
		}
		return false;		
	}
}
////////////////////////
?>