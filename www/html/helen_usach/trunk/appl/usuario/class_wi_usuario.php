<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
/*
 Clase : WI_USUARIO
 */
class wi_usuario extends w_input {
    function __construct($cod_item_menu) {
        parent::__construct('usuario', $cod_item_menu);
		$this->b_delete_visible  = false;
		
		$sql = sql_usuario::get_sql('INPUT');

		$sql_perfil = sql_usuario::get_sql('DD_COD_PERFIL');
		
		$this->dws['dw_usuario'] = new datawindow($sql);

		$this->dws['dw_usuario']->add_control($control = new edit_text_upper('NOM_USUARIO', 80, 100));
		$control->set_onChange("f_get_nom_usuario();");

		$this->dws['dw_usuario']->add_control($control = new edit_text_upper('LOGIN', 80, 100));
		$js = $control->get_onChange();
		$control->set_onChange("f_get_nom_login();");
		
		$this->dws['dw_usuario']->add_control($control = new edit_password('PASSWORD', 80, 100));
		$js = $control->get_onChange();
		$control->set_onChange($js."valida_password(); ");
		
		$this->dws['dw_usuario']->add_control(new drop_down_dw('COD_PERFIL',$sql_perfil,180));
		$this->dws['dw_usuario']->add_control(new edit_text('TELEFONO', 14, 100));
		$this->dws['dw_usuario']->add_control(new edit_text('CELULAR', 14, 100));
		$this->dws['dw_usuario']->add_control(new edit_mail('MAIL', 80, 100));
		$this->dws['dw_usuario']->add_control(new edit_check_box('AUTORIZA_INGRESO','S','N',''));
		$this->dws['dw_usuario']->add_control(new edit_check_box('ES_CAJERO','S','N',''));

		// asigna los mandatorys

		$this->dws['dw_usuario']->set_mandatory('NOM_USUARIO', 'Nombre de Usuario');
		$this->dws['dw_usuario']->set_mandatory('LOGIN', 'Login');
		$this->dws['dw_usuario']->set_mandatory('PASSWORD', 'Password');
		$this->dws['dw_usuario']->set_mandatory('COD_PERFIL', 'Perfil');
		$this->dws['dw_usuario']->set_mandatory('TELEFONO', 'Teléfono');
		$this->dws['dw_usuario']->set_mandatory('MAIL', 'Mail');
	}

	function new_record() {
		$row = $this->dws['dw_usuario']->insert_row();
		$this->dws['dw_usuario']->set_item($row, 'AUTORIZA_INGRESO', 'S');
	}
	function load_record() {
		$cod_usuario = $this->get_item_wo($this->current_record, 'COD_USUARIO');
		$this->dws['dw_usuario']->retrieve($cod_usuario);
	}
	function get_key() {
		return $this->dws['dw_usuario']->get_item(0, 'COD_USUARIO');
	}
	function save_record($db) {
		$cod_usuario 	= $this->get_key();
		$nom_usuario	= $this->dws['dw_usuario']->get_item(0, 'NOM_USUARIO');
		$login			= $this->dws['dw_usuario']->get_item(0, 'LOGIN');
		$password		= $this->f_encriptar($this->dws['dw_usuario']->get_item(0, 'PASSWORD'));
		$perfil 		= $this->dws['dw_usuario']->get_item(0, 'COD_PERFIL');
		$aut_ingreso	= $this->dws['dw_usuario']->get_item(0, 'AUTORIZA_INGRESO');
		$telefono		= $this->dws['dw_usuario']->get_item(0, 'TELEFONO');
		$celular		= $this->dws['dw_usuario']->get_item(0, 'CELULAR');
		$mail			= $this->dws['dw_usuario']->get_item(0, 'MAIL');
		$es_cajero		= $this->dws['dw_usuario']->get_item(0, 'ES_CAJERO');
			
		$mail 			= ($mail=='') ? "null" : $mail;
		$telefono 		= ($telefono=='') ? "null" : $telefono;
		$celular 		= ($celular=='') ? "null" : $celular;
		$cod_usuario 	= ($cod_usuario=='') ? "null" : $cod_usuario;

		$sp = 'spu_usuario';
		if ($this->is_new_record()){
			$operacion = 'INSERT';
		}
		else{
			$operacion = 'UPDATE';
		}
		$param = " '$operacion'
					,$cod_usuario
					,'$nom_usuario'
					,'$login'
					,'$password'
					,$perfil
					,'$aut_ingreso'
					,'$mail'
					,'$telefono'
					,$celular
					,'$es_cajero'";
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$cod_usuario = $db->GET_IDENTITY("usuario");
				$this->dws['dw_usuario']->set_item(0, 'COD_USUARIO', $cod_usuario);
			}
			return true;
		}
		return false;
	}
}
////////////////////////
?>