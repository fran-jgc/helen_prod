<?php
class dw_cruzado_mineduc extends datawindow{
	function __construct() {
		
		$sql = "SELECT CT_ANO
				    ,CT_TOT_SOLICITA
				    ,CT_UNI_SOLICITA
				    ,CT_PORCEN_UNI
				    ,(SELECT NOM_UNIVERSIDAD FROM UNIVERSIDAD WHERE RUT_UNIVERSIDAD = CT_RUT_UNI_RET) CT_RUT_UNI_RET
    				,(SELECT NOM_UNIVERSIDAD FROM UNIVERSIDAD WHERE RUT_UNIVERSIDAD = CT_RUT_UNI_SOL) CT_RUT_UNI_SOL
				FROM USACH_CB_MINEDUC UM, DEUDOR D
				WHERE D.COD_DEUDOR = {KEY1}
				    AND CT_RUT = D.RUT
				ORDER BY CT_ANO, CT_PORCEN_UNI DESC";

		parent::__construct($sql, 'CRUZADO_MINEDUC', false, false);
		
		$this->add_control(new static_num('CT_TOT_SOLICITA',4));
		$this->add_control(new static_num('CT_UNI_SOLICITA',4));
		$this->add_control(new static_num('CT_PORCEN_UNI',2));
	}
}

class dw_bitacora_deudor extends dw_bitacora_deudor_base {
    function __construct() {
        parent::__construct(); 
	}
}

class wi_deudor extends wi_deudor_base {
    function __construct($cod_item_menu) {
		
        parent::__construct($cod_item_menu); 
		$this->dws['dw_cruzado_mineduc'] = new dw_cruzado_mineduc();
		
		$sql = "select COD_SITUACION_ACADEMICA, NOM_SITUACION_ACADEMICA from  SITUACION_ACADEMICA ORDER BY COD_SITUACION_ACADEMICA";
		$this->dws['dw_deudor']->add_control(new drop_down_dw('COD_SITUACION_ACADEMICA', $sql, 150));
		$this->dws['dw_deudor']->add_control($control = new edit_radio_button('CANDIDATO_17_BIS_S', 'S', 'N', 'Si','CANDIDATO_17_BIS'));
		$this->dws['dw_deudor']->add_control($control = new edit_radio_button('CANDIDATO_17_BIS_N', 'N', 'S', 'No','CANDIDATO_17_BIS'));
		$this->add_auditoria('CANDIDATO_17_BIS');
		
	}
	function load_record() {
		parent::load_record();
		$cod_deudor	= $this->get_item_wo($this->current_record, 'COD_DEUDOR');
		$this->dws['dw_cruzado_mineduc']->retrieve($cod_deudor);
	}

	function save_record($db) {
		$cod_deudor			= $this->dws['dw_deudor']->get_item(0, 'COD_DEUDOR');
		$rut				= $this->dws['dw_deudor']->get_item(0, 'RUT');
		$dig_verif			= $this->dws['dw_deudor']->get_item(0, 'DIG_VERIF');
		$sexo				= $this->dws['dw_deudor']->get_item(0, 'SEXO');
		$cod_estado_civil	= $this->dws['dw_deudor']->get_item(0, 'COD_ESTADO_CIVIL');
		$apellido_paterno	= $this->dws['dw_deudor']->get_item(0, 'APELLIDO_PATERNO');
		$apellido_materno	= $this->dws['dw_deudor']->get_item(0, 'APELLIDO_MATERNO');
		$nombre				= $this->dws['dw_deudor']->get_item(0, 'NOMBRE');
		$fecha_nacim		= $this->dws['dw_deudor']->get_item(0, 'FECHA_NACIM');
		$fecha_defuncion	= $this->dws['dw_deudor']->get_item(0, 'FECHA_DEFUNCION');
		$cod_situacion_academica	= $this->dws['dw_deudor']->get_item(0, 'COD_SITUACION_ACADEMICA');
		$candidato_17_bis	= $this->dws['dw_deudor']->get_item(0, 'CANDIDATO_17_BIS_S');
		$cod_deudor 		= ($cod_deudor == '') ? "null" : $cod_deudor;
		$cod_situacion_academica = ($cod_situacion_academica == '') ? "null" : $cod_situacion_academica;
		$fecha_nacim = $this->str2date($fecha_nacim);
		$fecha_defuncion = $this->str2date($fecha_defuncion);
		
		$sp = 'spu_deudor';
		if ($this->is_new_record()){
			$operacion = 'INSERT';
		}
		else{
			$operacion = 'UPDATE';
		}
		$param	= "'$operacion'
					,$cod_deudor
					,$rut
					,'$dig_verif'
					,'$nombre'
					,'$apellido_paterno'
					,'$apellido_materno' 
					,'$sexo' 
					,$cod_estado_civil
					,$fecha_nacim
					,$fecha_defuncion
					,NULL
					,$cod_situacion_academica
					,'$candidato_17_bis'";
			
	    if (!$db->EXECUTE_SP($sp, $param))
	    	return false;		
	    	
	    if (!parent::save_record($db))
			return false;
		
		return true;
	}
}
?>