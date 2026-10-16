<?php
require_once(
	dirname(__FILE__)
	."/../../../../commonlib_seven/trunk/php/auto_load.php"
);

class radio_anula extends edit_radio_button {
	function __construct($field) {
		parent::__construct($field, 'S', 'N', '', $field);
	}

	function draw_entrable($dato, $record) {
		$value_true = $this->value_true;
		$this->value_true .= '_'.$record;
		$dato .= '_'.$record;

		$draw = parent::draw_entrable($dato, $record);
		$this->value_true = $value_true;

		return $draw;
	}

	function get_values_from_POST($record) {
		$value_true = $this->value_true.'_'.$record;
		$field_post = $this->group;

		if (
			isset($_POST[$field_post])
			&& $_POST[$field_post] == $value_true
		) {
			return $this->value_true;
		}

		return $this->value_false;
	}
}

class dw_abono extends datawindow {
	function __construct() {
		$sql = sql_anula_abono::get_sql('DW_ABONO');

		parent::__construct($sql, 'ABONO', true, true);

		$this->add_control(
			new static_text('NOM_ORIGEN_PAGO', 5, 5)
		);
		$this->add_control(
			new static_text('FECHA_ABONO', 5, 5)
		);
		$this->add_control(new static_text('COD_ABONO'));
		$this->add_control(
			new static_num('MONTO_CAPITAL', 4)
		);
		$this->add_control(
			new static_num('MONTO_INTERES', 4)
		);
		$this->add_control(
			new static_num('MONTO_MULTA', 4)
		);
		$this->add_control(
			new static_num('MONTO_MONEDA', 4)
		);
		$this->add_control(
			new static_num('MONTO_PESOS', 0)
		);

		$this->add_control(
			new edit_check_box(
				'ANULA_ABONO',
				'S',
				'N',
				'',
				'ANULA_ABONO'
			)
		);

		$this->add_control(
			new edit_check_box(
				'ENVIAR_A_EXCEDENTE',
				'S',
				'N'
			)
		);
	}
}

class wi_anula_abono extends w_input {
	function __construct($cod_item_menu) {
		parent::__construct(
			'anula_abono',
			$cod_item_menu
		);

		$this->b_delete_visible = false;

		$sql = sql_anula_abono::get_sql('INPUT');

		$this->dws['dw_anula_abono'] =
			new datawindow($sql);

		$this->dws['dw_abono'] = new dw_abono();

		$this->dws['dw_anula_abono']->add_control(
			new edit_text('RUT_H', 10, 10, 'hidden')
		);

		$this->dws['dw_anula_abono']->add_control(
			new edit_text(
				'COD_DEUDOR_H',
				10,
				10,
				'hidden'
			)
		);

		$this->dws['dw_anula_abono']->add_control(
			$control = new edit_num('RUT', 10, 10)
		);

		$control->set_drop_down_dependiente(
			'anula_abono',
			'COD_DEUDA_DD'
		);

		$js = $control->get_onChange();
		$control->set_onChange(
			$js." f_get_name_user();"
		);

		$this->dws['dw_anula_abono']->add_control(
			new static_num('DIG_VERIF')
		);

		$this->dws['dw_anula_abono']->add_control(
			new static_text('NOMBRE')
		);

		$this->dws['dw_anula_abono']->add_control(
			new edit_text('EXISTE', 2, 2, 'hidden')
		);

		$sql = sql_anula_abono::get_sql(
			'DD_COD_ABONO'
		);

		$this->dws['dw_anula_abono']->add_control(
			$control = new drop_down_dw(
				'COD_DEUDA_DD',
				$sql,
				90
			)
		);

		$control->set_drop_down_dependiente(
			'anula_abono',
			'COD_PLAN_PAGO_PP'
		);

		$js = $control->get_onChange();
		$control->set_onChange(
			$js."get_tipo_deuda(this);"
		);

		$this->dws['dw_anula_abono']->add_control(
			new static_text('COD_DEUDA')
		);

		$this->dws['dw_anula_abono']->add_control(
			new static_text('SIMBOLO')
		);

		$sql = sql_anula_abono::get_sql(
			'DD_PLAN_PAGO'
		);

		$this->dws['dw_anula_abono']->add_control(
			$control = new drop_down_dw(
				'COD_PLAN_PAGO_PP',
				$sql,
				90
			)
		);

		$js = $control->get_onChange();
		$control->set_onChange(
			$js." get_ano_cobro(this);"
		);

		$this->dws['dw_anula_abono']->add_control(
			new static_text('NRO_CUOTA')
		);

		$this->dws['dw_anula_abono']->add_control(
			new static_text('SALDO_CUOTA')
		);

		$this->dws['dw_anula_abono']->add_control(
			new static_num('COD_ANULA_ABONO')
		);

		$this->dws['dw_anula_abono']->add_control(
			new edit_text_multiline('OBS', 54, 5)
		);

		$this->dws['dw_anula_abono']->add_control(
			new static_num('COD_EXCEDENTE')
		);

		$this->set_first_focus('RUT');

		$this->dws['dw_anula_abono']->set_mandatory(
			'RUT',
			'Rut'
		);

		$this->dws['dw_anula_abono']->set_mandatory(
			'COD_DEUDA_DD',
			'Tipo Deuda'
		);
	}

	function new_record() {
		/* Evita que insert_row() construya una consulta sin WHERE. */
		$this->dws['dw_anula_abono']->retrieve('NULL');
		$this->dws['dw_anula_abono']->insert_row();

		$this->dws['dw_anula_abono']->set_item(
			0,
			'FECHA_ANULA_ABONO',
			$this->current_date()
		);

		$this->dws['dw_anula_abono']->set_item(
			0,
			'NOM_USUARIO',
			$this->nom_usuario
		);
	}

	function load_record() {
		$cod_anula_abono = $this->get_item_wo(
			$this->current_record,
			'COD_ANULA_ABONO'
		);

		$this->load_record_by_key($cod_anula_abono);
	}

	function load_record_by_key($cod_anula_abono) {
		$this->b_no_save_visible = false;
		$this->b_save_visible = false;
		$this->b_modify_visible = false;

		$this->dws['dw_anula_abono']->retrieve(
			$cod_anula_abono
		);

		$this->dws['dw_abono']->retrieve(
			$cod_anula_abono
		);

		$rut = $this->dws['dw_anula_abono']->get_item(
			0,
			'RUT'
		);

		$this->dws['dw_anula_abono']
			->controls['COD_DEUDA_DD']
			->retrieve($rut);

		$cod_deuda =
			$this->dws['dw_anula_abono']->get_item(
				0,
				'COD_DEUDA'
			);

		$this->dws['dw_anula_abono']
			->controls['COD_PLAN_PAGO_PP']
			->retrieve($cod_deuda);
	}

	function get_key() {
		return $this->dws['dw_anula_abono']->get_item(
			0,
			'COD_ANULA_ABONO'
		);
	}

	function get_selected_abonos() {
		$abonos = array();

		for (
			$i = 0;
			$i < $this->dws['dw_abono']->row_count();
			$i++
		) {
			$anula_abono =
				$this->dws['dw_abono']->get_item(
					$i,
					'ANULA_ABONO'
				);

			if ($anula_abono != 'S') {
				continue;
			}

			$cod_abono =
				$this->dws['dw_abono']->get_item(
					$i,
					'COD_ABONO'
				);

			$enviar_a_excedente =
				$this->dws['dw_abono']->get_item(
					$i,
					'ENVIAR_A_EXCEDENTE'
				);

			$abonos[] = array(
				'COD_ABONO' => $cod_abono,
				'ENVIAR_A_EXCEDENTE' =>
					($enviar_a_excedente == 'S')
						? 'S'
						: 'N'
			);
		}

		return $abonos;
	}

	function get_msg_valida_excedente(
		$db,
		$cod_abono,
		$result_comp
	) {
		$K_CONCEPTO_CUENTA_EXCEDENTE = 19;

		if (
			count($result_comp) == 0
			|| $result_comp[0]['COD_COMPROBANTE'] == ''
		) {
			return '';
		}

		$sql = "
			SELECT
				f_comp_get_cuenta_tipo_deuda(
					d.cod_tipo_deuda,
					$K_CONCEPTO_CUENTA_EXCEDENTE
				) COD_CUENTA_CONTABLE
			FROM ABONO A, DEUDA D
			WHERE A.COD_ABONO = $cod_abono
			  AND D.COD_DEUDA = A.COD_DEUDA
		";

		$result_cta = $db->build_results($sql);

		if (
			count($result_cta) == 0
			|| $result_cta[0]['COD_CUENTA_CONTABLE'] == ''
		) {
			return
				'No existe una cuenta contable configurada '.
				'para excedente en el tipo de deuda del abono.';
		}

		return '';
	}

	function validate_abono(
		$db,
		$cod_abono,
		$enviar_a_excedente
	) {
		$estados_permitidos = array(1, 2, 3);

		$sql = sql_anula_abono::get_sql(
			'COMP_APROBADO',
			$cod_abono
		);

		$result = $db->build_results($sql);

		if (
			count($result) > 0
			&& $result[0]['COD_COMPROBANTE'] != ''
			&&
			!in_array(
				(int)$result[0]['COD_ESTADO_COMPROBANTE'],
				$estados_permitidos,
				true
			)
		) {
			return
				'El comprobante original del abono '.
				$cod_abono.
				' tiene un estado no permitido.';
		}

		if ($enviar_a_excedente == 'S') {
			$mensaje = $this->get_msg_valida_excedente(
				$db,
				$cod_abono,
				$result
			);

			if ($mensaje != '') {
				return
					'Abono '.$cod_abono.': '.$mensaje;
			}
		}

		return '';
	}

	function validate_record() {
		$abonos = $this->get_selected_abonos();

		if (count($abonos) == 0) {
			return 'Debe seleccionar al menos un abono';
		}

		$obs = trim(
			(string)
			$this->dws['dw_anula_abono']->get_item(
				0,
				'OBS'
			)
		);

		if ($obs == '') {
			$this->set_first_focus('OBS');

			return
				'Debe ingresar "Observaciones" antes de grabar';
		}

		$db = new database(
			K_TIPO_BD,
			K_SERVER,
			K_BD,
			K_USER,
			K_PASS
		);

		foreach ($abonos as $abono) {
			$mensaje = $this->validate_abono(
				$db,
				$abono['COD_ABONO'],
				$abono['ENVIAR_A_EXCEDENTE']
			);

			if ($mensaje != '') {
				return $mensaje;
			}
		}

		return '';
	}

	function make_msg_error_oci(
		$error_oci,
		$sql = ''
	) {
		$error_number = '';
		$error_line = '';
		$error_message = '';
		$error_sqltext = '';

		if (is_array($error_oci)) {
			$error_number =
				isset($error_oci['code'])
					? $error_oci['code']
					: '';

			$error_line =
				isset($error_oci['offset'])
					? $error_oci['offset']
					: '';

			$error_message =
				isset($error_oci['message'])
					? trim(
						preg_replace(
							'/\s+/',
							' ',
							$error_oci['message']
						)
					)
					: '';

			$error_sqltext =
				isset($error_oci['sqltext'])
					? trim(
						preg_replace(
							'/\s+/',
							' ',
							$error_oci['sqltext']
						)
					)
					: '';
		}
		elseif (
			$error_oci !== false
			&& $error_oci !== null
			&& $error_oci !== ''
		) {
			$error_message = trim((string)$error_oci);
		}

		if ($error_message == '') {
			$error_message =
				'Oracle no devolvio detalle adicional.';
		}

		if ($error_sqltext == '' && $sql != '') {
			$error_sqltext = trim(
				preg_replace('/\s+/', ' ', $sql)
			);
		}

		$error = "ERROR_NUMBER: $error_number\n";
		$error .= "ERROR_SEVERITY: ORACLE\n";
		$error .= "ERROR_STATE:\n";
		$error .=
			"ERROR_PROCEDURE: SPU_ANULA_ABONO\n";
		$error .= "ERROR_LINE: $error_line\n";
		$error .= "ERROR_MESSAGE: $error_message\n";

		if ($error_sqltext != '') {
			$error .=
				"ERROR_SQLTEXT: $error_sqltext\n";
		}

		return str_replace("\n", "\\n", $error);
	}

	function save_record_oci(
		$db,
		$cod_usuario,
		$obs,
		$cod_abono,
		$enviar_a_excedente,
		$ejecutar_regula_fco
	) {
		$sql = "
			BEGIN
				spu_anula_abono(
					:ve_operacion,
					NULL,
					:ve_cod_usuario,
					:ve_obs,
					:ve_cod_abono,
					:ve_enviar_a_excedente,
					:ve_ejecutar_regula_fco
				);
			END;
		";

		$ve_operacion = 'INSERT';
		$ve_cod_usuario = (int)$cod_usuario;
		$ve_obs = $obs;
		$ve_cod_abono = (int)$cod_abono;

		$ve_enviar_a_excedente =
			($enviar_a_excedente == 'S') ? 'S' : 'N';
		$ve_ejecutar_regula_fco =
			($ejecutar_regula_fco == 'S') ? 'S' : 'N';

		$statement = oci_parse($db->con, $sql);

		if (!$statement) {
			$error_oci = oci_error($db->con);

			$this->alert(
				'No se pudo anular el abono '.
				$cod_abono.".\n\n".
				$this->make_msg_error_oci(
					$error_oci,
					$sql
				)
			);

			return false;
		}

		oci_bind_by_name(
			$statement,
			':ve_operacion',
			$ve_operacion,
			10
		);

		oci_bind_by_name(
			$statement,
			':ve_cod_usuario',
			$ve_cod_usuario
		);

		oci_bind_by_name(
			$statement,
			':ve_obs',
			$ve_obs,
			4000
		);

		oci_bind_by_name(
			$statement,
			':ve_cod_abono',
			$ve_cod_abono
		);

		oci_bind_by_name(
			$statement,
			':ve_enviar_a_excedente',
			$ve_enviar_a_excedente,
			1
		);

		oci_bind_by_name(
			$statement,
			':ve_ejecutar_regula_fco',
			$ve_ejecutar_regula_fco,
			1
		);

		if (!oci_execute($statement, OCI_DEFAULT)) {
			$error_oci = oci_error($statement);
			oci_free_statement($statement);

			$this->alert(
				'No se pudo anular el abono '.
				$cod_abono.".\n\n".
				$this->make_msg_error_oci(
					$error_oci,
					$sql
				)
			);

			return false;
		}

		oci_free_statement($statement);

		$cod_anula_abono =
			$db->GET_IDENTITY('anula_abono');

		$this->dws['dw_anula_abono']->set_item(
			0,
			'COD_ANULA_ABONO',
			$cod_anula_abono
		);

		return true;
	}



	function save_record($db) {
		$abonos = $this->get_selected_abonos();

		if (count($abonos) == 0) {
			$this->alert('Debe seleccionar al menos un abono');
			return false;
		}

		$cod_usuario = $this->cod_usuario;
		$obs = trim(
			(string)$this->dws['dw_anula_abono']->get_item(0, 'OBS')
		);

		if ($obs == '') {
			$this->alert('Debe ingresar "Observaciones" antes de grabar');
			return false;
		}

		$total_abonos = count($abonos);

		foreach ($abonos as $indice => $abono) {
			$cod_abono = (int)$abono['COD_ABONO'];
			$enviar_a_excedente =
				($abono['ENVIAR_A_EXCEDENTE'] == 'S') ? 'S' : 'N';
			$ejecutar_regula_fco =
				($indice === $total_abonos - 1) ? 'S' : 'N';

			if (K_TIPO_BD == 'oci') {
				if (!$this->save_record_oci(
					$db,
					$cod_usuario,
					$obs,
					$cod_abono,
					$enviar_a_excedente,
					$ejecutar_regula_fco
				)) {
					return false;
				}

				continue;
			}

			$obs_sql = str_replace("'", "''", $obs);
			$param =
				"'INSERT'".
				", NULL".
				", ".$cod_usuario.
				", '".$obs_sql."'".
				", ".$cod_abono.
				", '".$enviar_a_excedente."'".
				", '".$ejecutar_regula_fco."'";

			if (!$db->EXECUTE_SP('spu_anula_abono', $param)) {
				return false;
			}

			$cod_anula_abono = $db->GET_IDENTITY('anula_abono');
			$this->dws['dw_anula_abono']->set_item(
				0,
				'COD_ANULA_ABONO',
				$cod_anula_abono
			);
		}

		return true;
	}

	function procesa_event() {
		if (
			K_TIPO_BD == 'oci'
			&& isset($_POST['b_save_x'])
		) {
			$this->dws['dw_anula_abono']
				->get_values_from_POST();

			$this->dws['dw_abono']
				->get_values_from_POST();

			$mensaje = $this->validate_record();

			if ($mensaje != '') {
				$this->alert($mensaje);
				return;
			}

			$db = new database(
				K_TIPO_BD,
				K_SERVER,
				K_BD,
				K_USER,
				K_PASS
			);

			$db->BEGIN_TRANSACTION();

			if ($this->save_record($db)) {
				$db->COMMIT_TRANSACTION();

				$this->load_record_by_key(
					$this->get_key()
				);

				$this->redraw();
			}
			else {
				$db->ROLLBACK_TRANSACTION();
			}

			return;
		}

		parent::procesa_event();
	}
}
?>
