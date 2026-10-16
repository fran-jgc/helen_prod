<?php
////////////////////////////////////////////////////
////////////// USACH /////////////////
////////////////////////////////////////////////////

class wi_deuda extends wi_deuda_base {
function wi_deuda($cod_item_menu) {		
		parent::w_input('deuda', $cod_item_menu);
		$this->b_delete_visible  = false;

		$sql = "SELECT 	DA.COD_DEUDA
						  			, DO.RUT
						  			, DO.COD_DEUDOR
						  			, DO.DIG_VERIF
						  			, DO.NOMBRE
						  			, DO.APELLIDO_PATERNO
						  			, DO.APELLIDO_MATERNO
						  			, DA.COD_TIPO_DEUDA
						  			, TD.NOM_TIPO_DEUDA
						  			, DA.COD_ESTADO_DEUDA
						  			, ED.NOM_ESTADO_DEUDA
						  			, DA.COD_CARTERA
						  			, DA.COD_UNIVERSIDAD
						  			, TO_CHAR(DA.FECHA_INICIO_COBRO,'dd/mm/yyyy') FECHA_INICIO_COBRO  
						  			, DA.MONTO_INICIO_COBRO
						  			, M.SIMBOLO
						  			, DA.CANT_CUOTA
						  			, f_deuda_cat(DA.COD_DEUDA,To_Number(To_Char(SYSDATE,'yyyy'))) CAT
						  			, f_deuda_saldo_componente(DA.COD_DEUDA, 1) SALDO_NO_EXIGIBLE
						  			, f_deuda_saldo_componente(DA.COD_DEUDA, 1) * f_mon_get_valor_moneda(TD.COD_MONEDA) SALDO_NO_EXIGIBLE_PESOS				  
						  			, f_deuda_saldo_componente(DA.COD_DEUDA, 2) SALDO_VENCIDA
						  			, f_deuda_saldo_componente(DA.COD_DEUDA, 2) * f_mon_get_valor_moneda(TD.COD_MONEDA) SALDO_VENCIDA_PESOS				  
						  			, f_deuda_saldo_componente(DA.COD_DEUDA, 3) SALDO_VIGENTE
						  			, f_deuda_saldo_componente(DA.COD_DEUDA, 3) * f_mon_get_valor_moneda(TD.COD_MONEDA) SALDO_VIGENTE_PESOS				  
						  			, f_deuda_saldo_componente(DA.COD_DEUDA, 4) SALDO_FUTURO
						  			, f_deuda_saldo_componente(DA.COD_DEUDA, 4) * f_mon_get_valor_moneda(TD.COD_MONEDA) SALDO_FUTURO_PESOS
						  			, f_deuda_saldo_total(DA.COD_DEUDA) SALDO_TOTAL
						  			, f_deuda_saldo_total(DA.COD_DEUDA) * f_mon_get_valor_moneda(TD.COD_MONEDA) SALDO_TOTAL_PESOS
						  			, decode(DA.COD_ESTADO_DEUDA, 1, ''
						  										, 2, ''
						  										, 'none') DISPLAY_BOLETA
						  			, decode(DA.COD_ESTADO_DEUDA, 3, ''
						  										, 4, ''
						  										, 5, ''
						  										, 'none') DISPLAY_DEVOLUCION
						  			, 'EN COBRANZA EXTERNA' TITULO_DEUDA_MOROSA
						  			, 'none' VISIBLE_CAMPO
						  			, '' TITULO_DEUDOR_CRUZADO
									, 'none' VISIBLE_DEUDOR_CRUZADO
						  			, 'N' SELECCIONA_DICOM
									, '' COD_EMPRESA_COBRANZA
									, 0 SUMA_MONTO_ACTUALIZADO
									, 0 SUMA_MONTO_PORC
									, 0 VALOR_DICOM_H
									, 0 VALOR_EM_CO_H
									, 'DEUDOR CON DOCUMENTOS PROTESTADOS' TITULO_DOC_PROTES
									, 'none' VISIBLE_CAMPO_PROTES
									, 'DEUDOR CON PAGARES PROTESTADOS' TITULO_PAGARE_PROTES
									, 'none' VISIBLE_PAGARE_PROTES
					                , PORC_CF
					                , PORC_CU
					                , (PORC_CF + PORC_CU) SUM_PORC_CFCU						
						  	 FROM 	DEUDA DA LEFT OUTER JOIN CRUZADO_CFCU CC ON CC.COD_DEUDA = DA.COD_DEUDA
                      				, DEUDOR  DO, TIPO_DEUDA TD, ESTADO_DEUDA ED, MONEDA M
				             WHERE  DA.COD_DEUDOR=DO.COD_DEUDOR 
				     				AND DA.COD_DEUDA= {KEY1}
				     				AND TD.COD_TIPO_DEUDA = DA.COD_TIPO_DEUDA
				     				AND ED.COD_ESTADO_DEUDA = DA.COD_ESTADO_DEUDA
				     				AND M.COD_MONEDA = TD.COD_MONEDA";
		
		$this->dws['dw_deuda'] = new datawindow($sql);
		$this->dws['dw_deuda']->add_control(new static_num('RUT'));
		$this->dws['dw_deuda']->add_control(new edit_text_hidden('RUT_H',10,10));
		$this->dws['dw_deuda']->add_control(new edit_text_hidden('COD_DEUDOR'));
		$sql_1 = sql_deuda::get_sql('DD_COD_CARTERA');
		$this->dws['dw_deuda']->add_control(new drop_down_dw('COD_CARTERA', $sql_1, 150));
		$sql_1 = sql_deuda::get_sql('DD_COD_UNIVERSIDAD');
		$this->dws['dw_deuda']->add_control(new drop_down_dw('COD_UNIVERSIDAD', $sql_1, 150));
		
		$this->dws['dw_deuda']->add_control(new static_num('MONTO_INICIO_COBRO',2));
		$this->dws['dw_deuda']->add_control(new static_num('CAT',4));
		// saldos por componente
		$this->dws['dw_deuda']->add_control(new static_num('SALDO_NO_EXIGIBLE',4));
		$this->dws['dw_deuda']->add_control(new static_num('SALDO_NO_EXIGIBLE_PESOS',0));
		$this->dws['dw_deuda']->add_control(new static_num('SALDO_VENCIDA',4));
		$this->dws['dw_deuda']->add_control(new static_num('SALDO_VENCIDA_PESOS',0));
		$this->dws['dw_deuda']->add_control(new static_num('SALDO_VIGENTE',4));
		$this->dws['dw_deuda']->add_control(new static_num('SALDO_VIGENTE_PESOS',0));
		$this->dws['dw_deuda']->add_control(new static_num('SALDO_FUTURO',4));
		$this->dws['dw_deuda']->add_control(new static_num('SALDO_FUTURO_PESOS',0));
		$this->dws['dw_deuda']->add_control(new static_num('SALDO_TOTAL',4));
		$this->dws['dw_deuda']->add_control(new static_num('SALDO_TOTAL_PESOS',0));
		$this->dws['dw_deuda']->add_control(new edit_text_hidden('COD_ESTADO_DEUDA',10,10));
		$this->dws['dw_deuda']->add_control(new static_num('SUMA_MONTO_ACTUALIZADO',4));
		$this->dws['dw_deuda']->add_control(new static_num('SUMA_MONTO_PORC',2));
		
		$this->dws['dw_deuda']->add_control($control = new edit_check_box('SELECCIONA_DICOM', 'S', 'N'));
		$control->set_onChange("seleccion_dicom();");
		
		$sql_empresa_cobranza = sql_deuda::get_sql('DD_EMPRESA_COBRANZA');
		$this->dws['dw_deuda']->add_control($control = new drop_down_dw('COD_EMPRESA_COBRANZA', $sql_empresa_cobranza, 20, '', false));
		$control->set_onChange("seleccion_emp_cobranza();");
		
		$this->dws['dw_deuda']->add_control(new edit_text_upper('VALOR_DICOM_H', 10, 10, 'hidden'));
		$this->dws['dw_deuda']->add_control(new edit_text_upper('VALOR_EM_CO_H', 10, 10, 'hidden'));
		
		$this->dws['dw_deuda_plan_pago'] = new dw_deuda_plan_pago();
		$this->dws['dw_deuda_pagares'] = new dw_deuda_pagares();
		$this->dws['dw_deuda_cruzados'] = new dw_deuda_cruzados();
		$this->dws['dw_deuda_cruzados_interno'] = new dw_deuda_cruzados_interno();
		$this->dws['dw_deuda_abonos'] = new dw_deuda_abonos();
		$this->dws['dw_deuda_descuentos'] = new dw_deuda_descuentos();
		
		//CRUZADO_CFCU		
		$this->dws['dw_deuda']->add_control(new static_num('PORC_CF',2));
		$this->dws['dw_deuda']->add_control(new static_num('PORC_CU',2));
		$this->dws['dw_deuda']->add_control(new static_num('SUM_PORC_CFCU',2));
		
		// asigna los mandatorys		
		$this->dws['dw_deuda']->set_mandatory('COD_CARTERA', 'Cartera de la deuda');
		$this->dws['dw_deuda']->set_mandatory('COD_UNIVERSIDAD', 'Universidad que cobra la deuda');

		//audoria
		$this->add_auditoria('COD_CARTERA');
		$this->add_auditoria('COD_UNIVERSIDAD');
	}
}
?>