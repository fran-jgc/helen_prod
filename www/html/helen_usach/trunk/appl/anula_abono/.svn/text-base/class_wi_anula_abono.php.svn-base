<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
/*
 Clase : WI_ABONO
 */
class radio_anula extends edit_radio_button {	
	function __construct($field) {
	    parent::__construct($field, 'S', 'N', '', $field);
	}
	function draw_entrable($dato, $record) {
		$value_true = $this->value_true;
		$this->value_true = $this->value_true.'_'.$record;
		$dato = $dato.'_'.$record;
		$draw = parent::draw_entrable($dato, $record);
		$this->value_true = $value_true;
		return $draw;
	}		
	function get_values_from_POST($record) {
		$value_true = $this->value_true.'_'.$record;
		$field_post = $this->group;
		if (isset($_POST[$field_post]) && $_POST[$field_post]==$value_true)
			return $this->value_true;
		else 
			return $this->value_false;
	}
}

class dw_abono extends datawindow {
    function __construct() {

		$sql = sql_anula_abono::get_sql('DW_ABONO');
			
		parent::__construct($sql, 'ABONO', true, true);

		// asigna los formatos
		$this->add_control(new static_text('NOM_ORIGEN_PAGO',5,5));
		$this->add_control(new static_text('FECHA_ABONO',5,5));

		$this->add_control(new static_text('COD_ABONO'));
		$this->add_control(new static_num('MONTO_CAPITAL', 4));
		$this->add_control(new static_num('MONTO_INTERES', 4));
		$this->add_control(new static_num('MONTO_MULTA', 4));
		$this->add_control(new static_num('MONTO_MONEDA', 4));
		$this->add_control(new static_num('MONTO_PESOS', 0));

		$this->add_control(new edit_check_box('ANULA_ABONO', 'S', 'N', '', 'ANULA_ABONO'));
		$this->add_control(new edit_check_box('ENVIAR_A_EXCEDENTE', 'S', 'N'));
	}
}

/*
 Clase : WI_ANULA_ABONO
 */
class wi_anula_abono extends w_input {
    function __construct($cod_item_menu) {
        parent::__construct('anula_abono', $cod_item_menu);

		$this->b_delete_visible = false;

		$sql = sql_anula_abono::get_sql('INPUT');

		$this->dws['dw_anula_abono'] = new datawindow($sql);
		$this->dws['dw_abono'] = new dw_abono();

		// asigna los formatos
		$this->dws['dw_anula_abono']->add_control(new edit_text('RUT_H', 10, 10, 'hidden'));
		$this->dws['dw_anula_abono']->add_control(new edit_text('COD_DEUDOR_H', 10, 10, 'hidden'));

		$this->dws['dw_anula_abono']->add_control($control = new edit_num('RUT', 10, 10));

		$control->set_drop_down_dependiente('anula_abono', 'COD_DEUDA_DD');
		$js = $control->get_onChange();
		$control->set_onChange($js." f_get_name_user();");
		$this->dws['dw_anula_abono']->add_control(new static_num('DIG_VERIF'));
		$this->dws['dw_anula_abono']->add_control(new static_text('NOMBRE'));
		$this->dws['dw_anula_abono']->add_control(new edit_text('EXISTE', 2,2, 'hidden'));

		$sql = sql_anula_abono::get_sql('DD_COD_ABONO');		
		$this->dws['dw_anula_abono']->add_control($control = new drop_down_dw('COD_DEUDA_DD', $sql, 90));
		$control->set_drop_down_dependiente('anula_abono', 'COD_PLAN_PAGO_PP');
		$js = $control->get_onChange();
		$control->set_onChange($js."get_tipo_deuda(this);");

		$this->dws['dw_anula_abono']->add_control($control = new static_text('COD_DEUDA'));
		$this->dws['dw_anula_abono']->add_control($control = new static_text('SIMBOLO'));

		$sql = sql_anula_abono::get_sql('DD_PLAN_PAGO');
		$this->dws['dw_anula_abono']->add_control($control = new drop_down_dw('COD_PLAN_PAGO_PP', $sql, 90));
		$js = $control->get_onChange();
		$control->set_onChange($js." get_ano_cobro(this);");

		$this->dws['dw_anula_abono']->add_control($control = new static_text('NRO_CUOTA'));
		$this->dws['dw_anula_abono']->add_control($control = new static_text('SALDO_CUOTA'));
		$this->dws['dw_anula_abono']->add_control(new static_num('COD_ANULA_ABONO'));
		$this->dws['dw_anula_abono']->add_control(new edit_text_multiline('OBS',54,5));
		$this->dws['dw_anula_abono']->add_control(new static_num('COD_EXCEDENTE'));
		
		$this->set_first_focus('RUT');

		//mandatory
		$this->dws['dw_anula_abono']->set_mandatory('RUT', 'Rut');
		$this->dws['dw_anula_abono']->set_mandatory('COD_DEUDA_DD', 'Tipo Deuda');
	}
	function new_record() {
		$this->dws['dw_anula_abono']->insert_row();
		$this->dws['dw_anula_abono']->set_item(0, 'FECHA_ANULA_ABONO', $this->current_date());
		$this->dws['dw_anula_abono']->set_item(0, 'NOM_USUARIO', $this->nom_usuario);
	}
	function load_record() {
		$this->b_no_save_visible = false;
		$this->b_save_visible 	 = false;
		$this->b_modify_visible	 = false;
		
		$COD_ANULA_ABONO = $this->get_item_wo($this->current_record, 'COD_ANULA_ABONO');
		$this->dws['dw_anula_abono']->retrieve($COD_ANULA_ABONO);
		$COD_ABONO = $this->dws['dw_anula_abono']->get_item(0, 'COD_ABONO');
		$this->dws['dw_abono']->retrieve($COD_ANULA_ABONO);
		
		$rut = $this->dws['dw_anula_abono']->get_item(0, 'RUT');
		$this->dws['dw_anula_abono']->controls['COD_DEUDA_DD']->retrieve($rut);
		$cod_deuda = $this->dws['dw_anula_abono']->get_item(0, 'COD_DEUDA');
		$this->dws['dw_anula_abono']->controls['COD_PLAN_PAGO_PP']->retrieve($cod_deuda);
	}
	function get_key() {
		return $this->dws['dw_anula_abono']->get_item(0, 'COD_ANULA_ABONO');
	}
	function save_record($db) {
		$cod_usuario = $this->cod_usuario;
		$obs = $this->dws['dw_anula_abono']->get_item(0, 'OBS');
		
		for ($i = 0; $i < $this->dws['dw_abono']->row_count(); $i++){
			$ANULA_ABONO = $this->dws['dw_abono']->get_item($i, 'ANULA_ABONO');
			if ($ANULA_ABONO == 'S' ){
			 	$cod_abono          = $this->dws['dw_abono']->get_item($i, 'COD_ABONO');
			 	$enviar_a_excedente = $this->dws['dw_abono']->get_item($i, 'ENVIAR_A_EXCEDENTE');
				
				$sp = 'spu_anula_abono';
			  
				$param	= "'INSERT'
						, null
						, $cod_usuario
						,'$obs'
						, $cod_abono
						,'$enviar_a_excedente'";

				if (!$db->EXECUTE_SP($sp, $param))
					return false;
				else{
						$COD_ANULA_ABONO = $db->GET_IDENTITY("anula_abono");
						$this->dws['dw_anula_abono']->set_item(0, 'COD_ANULA_ABONO', $COD_ANULA_ABONO);
				}
			}
		}
		return true;
	}
}
?>