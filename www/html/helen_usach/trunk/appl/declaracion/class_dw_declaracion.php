<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class dw_declaracion_base extends datawindow {
	const K_VIA_DECLARACION_DIGITADA	= 1;
	const K_EST_DECL_INGRESADA			= 1;
	
	function __construct() {
		$sql = sql_declaracion::get_sql('INPUT');
		parent::__construct($sql);
		
		$this->add_control($control = new edit_rut('RUT'));
		$control->set_onChange("existe_rut();aviso_deuda_cruzada();");
		
		$sql_est_d= sql_declaracion::get_sql ('DD_COD_ESTADO_DECLARACION');		
		$this->add_control(new drop_down_dw('COD_ESTADO_DECLARACION', $sql_est_d, 120));
		$this->add_control(new drop_down_dw('COD_ESTADO_DECLARACION_NO_ING', $sql_est_d, 120));		
		$this->set_entrable('COD_ESTADO_DECLARACION_NO_ING', false);

		$this->add_control(new static_text('COD_DECLARACION_HH'));
		$this->add_control(new edit_text('COD_DEUDOR',10,10, 'hidden'));
		$this->add_control(new edit_text('K_CHILE_H',10,10,'hidden'));
		$this->add_control(new static_num('RUT_NO_ING'));
		$this->add_control(new static_text('DIG_VERIF'));
		$this->add_control(new static_text('DIG_VERIF_NO_ING'));
		$this->add_control(new static_text('NOMBRE'));
		$this->add_control(new static_text('NOMBRE_NO_ING'));
			
		$this->add_control(new static_text('ANO_COBRO'));
		$this->add_control(new edit_text_upper('ANO_COBRO_H', 10, 10, 'hidden'));

		$sql = sql_declaracion::get_sql ('DD_COD_VIA_DECLARACION');
		$this->add_control(new drop_down_dw('COD_VIA_DECLARACION', $sql));
		$this->set_entrable('COD_VIA_DECLARACION', false);
		$this->add_control(new static_text('FECHA_CONFIRMA_NO_ING'));
		$this->add_control(new edit_text('COD_USUARIO_CONFIRMA',10,10, 'hidden'));
		$this->add_control(new static_text('NOM_USUARIO_CONFIRMA'));
		$this->add_control(new static_num('VALOR_CUOTA',4));

		$this->add_control(new static_text('NOM_USUARIO'));
		$this->add_control(new static_text('NOM_USUARIO_NO_ING'));
		
				///PENDIENTE///
		$fecha = getdate();
		$ano   = $fecha["year"];
		
		$this->add_control($control = new edit_ano('ANO_DECLARACION'));
		$control->set_onChange("valida_ano(this,$ano); calc_ano_cobro()");
		
		$this->add_control(new static_text('FECHA_DECLARACION'));
		$this->add_control(new edit_check_box('ENTREGA_F22', 'S', 'N'));
		$this->add_control(new edit_check_box('CON_HIJO', 'S', 'N'));

		$sql_afp= sql_declaracion::get_sql ('DD_COD_AFP');
		$this->add_control(new drop_down_dw('COD_AFP', $sql_afp, 180));

		$sql_estado_civil = sql_declaracion::get_sql ('DD_COD_ESTADO_CIVIL');
		$this->add_control($control = new drop_down_dw('COD_ESTADO_CIVIL', $sql_estado_civil, 150));
		$control->set_onChange('mostrarOcultar_estado(this);');
		$this->add_control(new edit_rut('RUT_CONYUGE',10,8,'DIG_VERIF_CONYUGE'));
		$this->add_control(new edit_dig_verif('DIG_VERIF_CONYUGE','RUT_CONYUGE'));
		$this->add_control(new edit_text('NOM_CONYUGE',90,90));
		$this->add_control(new edit_check_box('CONYUGE_ES_DEUDOR', 'S', 'N'));
		$this->add_control($control = new edit_check_box('CONYUGE_DECLARA', 'S', 'N'));
		$control->set_onChange('mostrarOcultar();');

		$this->add_control(new edit_check_box('CERT_SUELDO', 'S', 'N'));
		$this->add_control(new edit_check_box('CERT_HONORARIO', 'S', 'N'));
		$this->add_control(new edit_check_box('CERT_AFP', 'S', 'N'));
		$this->add_control(new edit_check_box('DECLARA_RENTA', 'S', 'N'));
		$this->add_control(new edit_check_box('CERT_NOTARIAL_ING', 'S', 'N'));
		$this->add_control(new edit_check_box('ANTEC_CONYUGE', 'S', 'N'));
		$this->add_control(new edit_check_box('CERT_ALUM_REGULAR', 'S', 'N'));
		

		//MANDATORYS
		$this->set_mandatory('COD_ESTADO_DECLARACION', 'Estado declaración');
		$this->set_mandatory('RUT', 'RUT Deudor');
		$this->set_mandatory('ANO_DECLARACION', 'año declaración');
		$this->set_mandatory('COD_AFP', 'AFP');
		$this->set_mandatory('COD_ESTADO_CIVIL', 'Estado Civil');
	}
	function new_declaracion() {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$ano = $db->current_year();
		
		$this->insert_row();		
		$this->set_item(0, 'ANO_DECLARACION', $ano - 1);
		$this->set_item(0, 'COD_ESTADO_DECLARACION', self::K_EST_DECL_INGRESADA);
		$this->set_item(0, 'COD_ESTADO_DECLARACION_NO_ING', self::K_EST_DECL_INGRESADA);

		$cod_pais_chile = $this->get_constante('K_CHILE');
		$this->set_item(0, 'K_CHILE_H', $cod_pais_chile, 'primary', false);	// por defecto CHILE pero no cambia el status
		
		$this->set_item(0, 'COD_VIA_DECLARACION', self::K_VIA_DECLARACION_DIGITADA);
		$this->set_item(0, 'FECHA_DECLARACION', $this->current_date());
		//$this->set_item(0, 'CONYUGE_DECLARA', 'S');
		$this->set_item(0, 'CONYUGE_DECLARA', 'N');
		
		$this->set_item(0, 'VISIBLE_TABLE', 'none');
		$this->set_item(0, 'VISIBLE_TABLE_CARTERA', 'none');
		$this->set_item(0, 'VISIBLE_BOTON', 'none');
		$this->set_item(0, 'VISIBLE_TD', '');
		$this->set_item(0, 'TD_VALOR_CUOTA', 'none');
		$this->set_item(0, 'LABEL_CUOTA', 'SIN CUOTA');
		
		$this->set_item(0, 'ANO_COBRO', $ano);
		$this->set_item(0, 'ANO_COBRO_H', $ano);
	}	
}
/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_dw_declaracion.php";
if (file_exists($file_name)) 
	require_once($file_name);
else {
	class dw_declaracion extends dw_declaracion_base {
	    function __construct() {
	        parent::__construct(); 
		}
	}
}
?>