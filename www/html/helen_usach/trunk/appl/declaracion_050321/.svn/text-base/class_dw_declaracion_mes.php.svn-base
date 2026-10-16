<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class edit_monto_pesos extends edit_num {
	var $tabindex;
	
	function __construct($field, $tabindex) {
		$this->tabindex = $tabindex;
		parent::__construct($field);
	}
	function draw_entrable($dato, $record) {
		$field = $this->field.'_'.$record;
		if ($this->readonly)
			$ctrl = '<input class="'.$this->class.'" name="'.$field.'" id="'.$field.'" type="'.$this->type.'" readonly="true" value="'.$dato.'" size="'.$this->size.'" maxLength="'.$this->maxlen.'" ';
		else
			$ctrl = '<input class="'.$this->class.'" name="'.$field.'" id="'.$field.'" type="'.$this->type.'" value="'.$dato.'" size="'.$this->size.'" maxLength="'.$this->maxlen.'" ';
		
		if ($this->type != 'hidden' || $this->forzar_js)
			$ctrl .= $this->make_java_script();

		if ($this->style!='')
			$ctrl .= ' style="'.$this->style.'"';
			
		// Se asigna el tabindex
		$ctrl .= ' tabindex="'.($this->tabindex + $record).'"';
		
		$ctrl .= '/>';
			
		return $ctrl;
	}
}

class edit_num_dj extends edit_num {
	
    function __construct($field, $size = 16, $maxlen = 16, $num_dec=0, $solo_positivos = true, $readonly=false, $con_separador_miles=true) {
        parent::__construct($field, $size, $maxlen, $type='text', $readonly);
		$this->num_dec = $num_dec;
		$this->solo_positivos = $solo_positivos;
		$this->con_separador_miles = $con_separador_miles;
		$this->set_onKeyPress('return onlyNumbers_dj(this, event,'.$num_dec.', '.($solo_positivos ? 1 : 0).');');
		$this->class = 'input_num';
	}	
}

class dw_declaracion_mes extends datawindow {
	const K_MONEDA_UTM = 3;
	
	function __construct() {
		$sql =  sql_declaracion::get_sql('DW_DECLARACION_MES');
		parent::__construct($sql, 'DECLARACION_MES');
		$this->add_control(new edit_text('COD_DECLARACION_MES1', 10, 10, 'hidden'));
		$this->add_control(new edit_text('MES1', 10, 10, 'hidden'));
		$this->add_control(new edit_text('COD_DECLARACION_MES2', 10, 10, 'hidden'));
		$this->add_control(new edit_text('MES2', 10, 10, 'hidden'));
		$this->add_control(new edit_num_dj('MONTO_DEUDOR_PESOS1'));
		$this->add_control(new edit_num_dj('MONTO_DEUDOR_PESOS2'));
		$this->add_control(new edit_num_dj('MONTO_CONYUGE_PESOS1'));
		$this->add_control(new edit_num_dj('MONTO_CONYUGE_PESOS2'));

		$this->add_control($control = new edit_num('VALOR_UTM1', 10, 10, 0));
		$control -> type = 'hidden';

		$this->add_control($control1 = new edit_num('VALOR_UTM2', 10, 10, 0));
		$control1 -> type = 'hidden';

		$this->set_computed('MONTO_DEUDOR_MONEDA1', '[VALOR_UTM1]==0 ? 0 : [MONTO_DEUDOR_PESOS1] / [VALOR_UTM1]', 4);
		$this->set_computed('MONTO_DEUDOR_MONEDA2', '[VALOR_UTM2]==0 ? 0 : [MONTO_DEUDOR_PESOS2] / [VALOR_UTM2]', 4);
		$this->set_computed('MONTO_CONYUGE_MONEDA1', '[VALOR_UTM1]==0 ? 0 : [MONTO_CONYUGE_PESOS1] / [VALOR_UTM1]', 4);
		$this->set_computed('MONTO_CONYUGE_MONEDA2', '[VALOR_UTM2]==0 ? 0 : [MONTO_CONYUGE_PESOS2] / [VALOR_UTM2]', 4);

		// Totales por linea en pesos
		$this->set_computed('MONTO_DEUDOR_PESOS', '[MONTO_DEUDOR_PESOS1] + [MONTO_DEUDOR_PESOS2]');
		$this->controls['MONTO_DEUDOR_PESOS']->type = 'hidden';
		$this->set_computed('MONTO_CONYUGE_PESOS', '[MONTO_CONYUGE_PESOS1] + [MONTO_CONYUGE_PESOS2]');
		$this->controls['MONTO_CONYUGE_PESOS']->type = 'hidden';
			
		// Totales por linea en moneda
		$this->set_computed('MONTO_DEUDOR_MONEDA', '[MONTO_DEUDOR_MONEDA1] + [MONTO_DEUDOR_MONEDA2]', 4);
		$this->controls['MONTO_DEUDOR_MONEDA']->type = 'hidden';
		$this->set_computed('MONTO_CONYUGE_MONEDA', '[MONTO_CONYUGE_MONEDA1] + [MONTO_CONYUGE_MONEDA2]', 4);
		$this->controls['MONTO_CONYUGE_MONEDA']->type = 'hidden';

		// Totales por linea en totales
		$this->accumulate('MONTO_DEUDOR_PESOS');
		$this->accumulate('MONTO_CONYUGE_PESOS');
		$this->accumulate('MONTO_DEUDOR_MONEDA');
		$this->accumulate('MONTO_CONYUGE_MONEDA');
		
		$this->set_mandatory('MONTO_DEUDOR_PESOS1', 'Monto');
		$this->set_mandatory('MONTO_DEUDOR_PESOS2', 'Monto');
	}
	function new_declaracion() {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$ano = $db->current_year() - 1;
		
		for($i=0; $i < 6; $i++) {
			$row = $this->insert_row();
			$mes = $i + 1;
			$this->set_item($row, 'MES1', $mes);
			$this->set_item($row, 'NOM_MES1', $this->nom_mes($mes));
			$sql = sql_declaracion::get_sql('VALOR_MONEDA',$mes,$ano);
			$result = $db->build_results($sql);
			$this->set_item($row, 'VALOR_UTM1', $result[0]['VALOR_MONEDA']);

			$mes = $i + 7;
			$this->set_item($row, 'MES2', $mes);
			$this->set_item($row, 'NOM_MES2', $this->nom_mes($mes));
			$sql = sql_declaracion::get_sql('VALOR_MONEDA',$mes,$ano);
			$result = $db->build_results($sql);
			$this->set_item($row, 'VALOR_UTM2', $result[0]['VALOR_MONEDA']);
		}
	}
	function update($db,$cod_declaracion,$operacion) {
		$sp = 'sp_decl_declaracion_mes';

		for ($i = 0; $i < $this->row_count(); $i++){
			$cod_declaracion_mes1		= $this->get_item($i, 'COD_DECLARACION_MES1');
			$mes1						= $this->get_item($i, 'MES1');
			$monto_deudor_moneda1		= $this->get_item($i, 'MONTO_DEUDOR_MONEDA1');
			$monto_deudor_pesos1    	= $this->get_item($i, 'MONTO_DEUDOR_PESOS1');
			$monto_conyuge_moneda1		= $this->get_item($i, 'MONTO_CONYUGE_MONEDA1');
			$monto_conyuge_pesos1		= $this->get_item($i, 'MONTO_CONYUGE_PESOS1');
			$cod_declaracion_mes2		= $this->get_item($i, 'COD_DECLARACION_MES2');
			$mes2						= $this->get_item($i, 'MES2');
			$monto_deudor_moneda2		= $this->get_item($i, 'MONTO_DEUDOR_MONEDA2');
			$monto_deudor_pesos2		= $this->get_item($i, 'MONTO_DEUDOR_PESOS2');
			$monto_conyuge_moneda2		= $this->get_item($i, 'MONTO_CONYUGE_MONEDA2');
			$monto_conyuge_pesos2		= $this->get_item($i, 'MONTO_CONYUGE_PESOS2');

			$cod_declaracion_mes1	= ($cod_declaracion_mes1=='') ? "null" : $cod_declaracion_mes1;
			$monto_deudor_moneda1	= ($monto_deudor_moneda1=='') ? 0 : $monto_deudor_moneda1;
			$monto_deudor_pesos1    = ($monto_deudor_pesos1=='') ? 0 : $monto_deudor_pesos1;
			$monto_conyuge_moneda1	= ($monto_conyuge_moneda1=='') ? 0 : $monto_conyuge_moneda1;
			$monto_conyuge_pesos1	= ($monto_conyuge_pesos1=='') ? 0 : $monto_conyuge_pesos1;
			$cod_declaracion_mes2	= ($cod_declaracion_mes2=='') ? "null" : $cod_declaracion_mes2;
			$monto_deudor_moneda2	= ($monto_deudor_moneda2=='') ? 0 : $monto_deudor_moneda2;
			$monto_deudor_pesos2    = ($monto_deudor_pesos2=='') ? 0 : $monto_deudor_pesos2;
			$monto_conyuge_moneda2	= ($monto_conyuge_moneda2=='') ? 0 : $monto_conyuge_moneda2;
			$monto_conyuge_pesos2	= ($monto_conyuge_pesos2=='') ? 0 : $monto_conyuge_pesos2;


			$param = "	'$operacion'
					, $cod_declaracion
					, $cod_declaracion_mes1
					, $mes1
					, $monto_deudor_moneda1
					, $monto_deudor_pesos1
					, $monto_conyuge_moneda1
					, $monto_conyuge_pesos1
					, $cod_declaracion_mes2
					, $mes2
					, $monto_deudor_moneda2
					, $monto_deudor_pesos2
					, $monto_conyuge_moneda2
					, $monto_conyuge_pesos2";
			if (!$db->EXECUTE_SP($sp, $param))
			return false;
		}
		return true;
	}
}
?>