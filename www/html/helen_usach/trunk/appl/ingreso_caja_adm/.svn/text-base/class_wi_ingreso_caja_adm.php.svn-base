<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class dw_sub_boleta_adm extends datawindow{
	function __construct() {
		$sql = "SELECT 	SB.MONTO_CAPITAL
				     	,SB.MONTO_INTERES
     					,SB.MONTO_MULTA
     					,SB.MONTO_CAPITAL + SB.MONTO_INTERES + SB.MONTO_MULTA TOTAL_MONTO
     					,case B.CANT
							when 1 then B.MONTO_PESOS
							else round((SB.MONTO_CAPITAL + SB.MONTO_INTERES + SB.MONTO_MULTA) * dbo.f_mon_get_valor_moneda(T.COD_MONEDA, getdate()), 0) 
						 end TOTAL_MONTO_PESO
     					,case B.CANT
							when 1 then B.MONTO_PESOS
							else round((SB.MONTO_CAPITAL + SB.MONTO_INTERES + SB.MONTO_MULTA) * dbo.f_mon_get_valor_moneda(T.COD_MONEDA, getdate()), 0) 
						 end TOTAL_MONTO_PESO_H
						 
     					,B.DSCTO_CAPITAL
     					,B.DSCTO_INTERES
     					,B.DSCTO_MULTA
     					,B.DSCTO_CAPITAL + B.DSCTO_INTERES + B.DSCTO_MULTA TOTAL_DSCTO
				     	,B.DSCTO_PESOS TOTAL_DSCTO_PESO
				     	,(SB.MONTO_CAPITAL + SB.MONTO_INTERES + SB.MONTO_MULTA) + (B.DSCTO_CAPITAL + B.DSCTO_INTERES + B.DSCTO_MULTA) TOTAL
				     	,round((SB.MONTO_CAPITAL + SB.MONTO_INTERES + SB.MONTO_MULTA) * dbo.f_mon_get_valor_moneda(T.COD_MONEDA, getdate()),0) + round((B.DSCTO_CAPITAL + B.DSCTO_INTERES + B.DSCTO_MULTA) * dbo.f_mon_get_valor_moneda(T.COD_MONEDA, getdate()), 0) TOTAL_PESO
				     	,round((SB.MONTO_CAPITAL + SB.MONTO_INTERES + SB.MONTO_MULTA) * dbo.f_mon_get_valor_moneda(T.COD_MONEDA, getdate()),0) + round((B.DSCTO_CAPITAL + B.DSCTO_INTERES + B.DSCTO_MULTA) * dbo.f_mon_get_valor_moneda(T.COD_MONEDA, getdate()), 0) TOTAL_PESO_H
				  FROM 	SUB_BOLETA SB
				     	,BOLETA B
				     	,DEUDA D
				     	,TIPO_DEUDA T
				 WHERE 	SB.COD_BOLETA = B.COD_BOLETA
				   		AND COD_SUB_BOLETA = {KEY1}
				   		AND D.COD_DEUDA = B.COD_DEUDA
				   		AND T.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA";
		
		parent::__construct($sql, 'SUB_BOLETA', true, true);

		$this->add_control(new static_num('MONTO_CAPITAL',4));
		$this->add_control(new static_num('MONTO_INTERES',4));
		$this->add_control(new static_num('MONTO_MULTA',4));
		$this->add_control(new static_num('TOTAL_MONTO',4));
		$this->add_control(new static_num('TOTAL_MONTO_PESO'));
		$this->add_control(new edit_text('TOTAL_MONTO_PESO_H', 10,10, 'hidden'));
		
		$this->add_control(new static_num('DSCTO_CAPITAL',4));
		$this->add_control(new static_num('DSCTO_INTERES',4));
		$this->add_control(new static_num('DSCTO_MULTA',4));
		$this->add_control(new static_num('TOTAL_DSCTO',4));
		$this->add_control(new static_num('TOTAL_DSCTO_PESO'));

		$this->add_control(new static_num('TOTAL',4));
		$this->add_control(new static_num('TOTAL_PESO'));
		$this->add_control(new edit_text('TOTAL_PESO_H', 10,10, 'hidden'));
	}
}

class wi_ingreso_caja_adm extends w_input {
    function __construct($cod_item_menu) {
        parent::__construct('ingreso_caja_adm', $cod_item_menu);
		$this->b_delete_visible  = false;

		$sql = sql_ingreso_caja_adm::get_sql('INPUT');

		$this->dws['dw_ingreso_caja_adm'] = new datawindow($sql);
		$this->dws['dw_sub_boleta'] = new dw_sub_boleta_adm();

		//CONTROLES
		$this->dws['dw_ingreso_caja_adm']->add_control(new static_text('FECHA_INGRESO_CAJA_ADM'));
		$this->dws['dw_ingreso_caja_adm']->add_control(new static_num('RUT'));
		$this->dws['dw_ingreso_caja_adm']->add_control(new static_text('DIG_VERIF'));
		$this->dws['dw_ingreso_caja_adm']->add_control(new static_text('NOMBRE'));
		$this->dws['dw_ingreso_caja_adm']->add_control(new static_text('COD_DEUDA'));
		$this->dws['dw_ingreso_caja_adm']->add_control(new edit_text('COD_DEUDA_H', 10, 10, 'hidden'));
		$this->dws['dw_ingreso_caja_adm']->add_control(new static_text('COD_TIPO_DEUDA'));
		$this->dws['dw_ingreso_caja_adm']->add_control(new static_text('ANO_COBRO'));
		$this->dws['dw_ingreso_caja_adm']->add_control(new edit_text('COD_USUARIO',10, 10, 'hidden'));
		$this->dws['dw_ingreso_caja_adm']->add_control(new static_text('NOM_USUARIO'));
		$this->dws['dw_ingreso_caja_adm']->add_control(new static_text('SIMBOLO'));
		$this->dws['dw_ingreso_caja_adm']->add_control(new edit_num('MONTO'));
		
		$this->dws['dw_ingreso_caja_adm']->add_control($control = new edit_text('COD_BOLETA', 10, 10));
		$control->set_drop_down_dependiente('ingreso_caja_adm', 'COD_SUB_BOLETA');
		$js = $control->get_onChange();
		$control->set_onChange($js." f_get_info_boleta();");
		
		$sql = sql_ingreso_caja_adm::get_sql('DD_SUB_BOLETA');
		$this->dws['dw_ingreso_caja_adm']->add_control($control = new drop_down_dw('COD_SUB_BOLETA', $sql, 90));

		$js = $control->get_onChange();
		$control->set_onChange($js." f_get_info_sub_boleta();");
		
		//MANDATORY
		$this->dws['dw_ingreso_caja_adm']->set_mandatory('COD_BOLETA', 'Código Boleta');
		$this->dws['dw_ingreso_caja_adm']->set_mandatory('COD_SUB_BOLETA', 'Código Sub Boleta');
		$this->dws['dw_ingreso_caja_adm']->set_mandatory('MONTO', 'Pago Transbank');
		
		$this->set_first_focus('COD_BOLETA');
	}
	function new_record() {
		$this->dws['dw_ingreso_caja_adm']->insert_row();
		$this->dws['dw_sub_boleta']->insert_row();

		$this->dws['dw_ingreso_caja_adm']->set_item(0, 'FECHA_INGRESO_CAJA_ADM', $this->current_date());
		$this->dws['dw_ingreso_caja_adm']->set_item(0, 'COD_USUARIO', $this->cod_usuario);
		$this->dws['dw_ingreso_caja_adm']->set_item(0, 'NOM_USUARIO', $this->nom_usuario);
	}
	function redraw() {
		parent::redraw();
		if (session::is_set("wi_boleta.create_ingreso")) {
			$this->dws['dw_ingreso_caja_adm']->controls['COD_BOLETA']->readonly = true;
			$cod_boleta = session::get("wi_boleta.create_ingreso");
			session::un_set("wi_boleta.create_ingreso");
			print '<script type="text/javascript">
					// cod_boleta
					document.getElementById(\'COD_BOLETA_0\').value = '.$cod_boleta.';
					load_drop_down_from_text(\'ingreso_caja_adm\', \'COD_BOLETA_0\', \'COD_SUB_BOLETA\');  
					f_get_info_boleta();
					
					// nro subboleta
					var cod_sub_boleta = document.getElementById(\'COD_SUB_BOLETA_0\');
					cod_sub_boleta.selectedIndex = 1;
					f_get_info_sub_boleta();
					cod_sub_boleta.focus();
					</script>';
		}
	}
	function load_record() {
		$this->b_no_save_visible = false;
		$this->b_save_visible 	 = false;
		$this->b_modify_visible	 = false;
		
		$cod_ingreso_caja_adm = $this->get_item_wo($this->current_record, 'COD_INGRESO_CAJA_ADM');
		$this->dws['dw_ingreso_caja_adm']->retrieve($cod_ingreso_caja_adm);
				
		$cod_sub_boleta = $this->dws['dw_ingreso_caja_adm']->get_item(0, 'COD_SUB_BOLETA');
		$this->dws['dw_sub_boleta']->retrieve($cod_sub_boleta);

		$cod_boleta = $this->dws['dw_ingreso_caja_adm']->get_item(0, 'COD_BOLETA');
		
		$sql = sql_ingreso_caja_adm::get_sql('DD_SUB_BOLETA_CON_FECHA_PAGO');
		$this->dws['dw_ingreso_caja_adm']->controls['COD_SUB_BOLETA']->set_sql($sql);
		$this->dws['dw_ingreso_caja_adm']->controls['COD_SUB_BOLETA']->retrieve($cod_boleta);

	}
	function get_key() {
		return $this->dws['dw_ingreso_caja_adm']->get_item(0, 'COD_INGRESO_CAJA_ADM');
	}
	function save_record($db) {
		$cod_ingreso_caja_adm		= $this->dws['dw_ingreso_caja_adm']->get_item(0, 'COD_INGRESO_CAJA_ADM');
		$fecha_ingreso_caja_adm		= $this->dws['dw_ingreso_caja_adm']->get_item(0, 'FECHA_INGRESO_CAJA_ADM');
		$cod_usuario				= $this->dws['dw_ingreso_caja_adm']->get_item(0, 'COD_USUARIO');
		$cod_sub_boleta     		= $this->dws['dw_ingreso_caja_adm']->get_item(0, 'COD_SUB_BOLETA');
		$tipo_pago					= "'TRANSBANK'";	// FIJO
		$monto						= $this->dws['dw_ingreso_caja_adm']->get_item(0, 'MONTO');
		
		$cod_ingreso_caja_adm		= ($cod_ingreso_caja_adm=='') ? "null" : $cod_ingreso_caja_adm;
		$fecha_ingreso_caja_adm = $this->str2date($fecha_ingreso_caja_adm);
		
		$sp = 'spu_ingreso_caja_adm';
		if ($this->is_new_record())
			$operacion = 'INSERT';
		else
			$operacion = 'UPDATE';

		$param = " '$operacion'
					,$cod_ingreso_caja_adm
					,$fecha_ingreso_caja_adm
					,$cod_usuario
					,$cod_sub_boleta
					,$tipo_pago
					,$monto";

		if ($this->dws['dw_ingreso_caja_adm']->is_modified) {
			if ($db->EXECUTE_SP($sp, $param)){
				if ($this->is_new_record()) {
					$cod_ingreso_caja_adm = $db->GET_IDENTITY('ingreso_caja_adm');
					$this->dws['dw_ingreso_caja_adm']->set_item(0, 'COD_INGRESO_CAJA_ADM', $cod_ingreso_caja_adm);

					if (!$db->EXECUTE_SP($sp, "'CREA_ABONO_DESCUENTO' ,$cod_ingreso_caja_adm"))
						return false;
				}
			}
			else{
				return false;
			}
		}
								
		return true;
	}
}
?>