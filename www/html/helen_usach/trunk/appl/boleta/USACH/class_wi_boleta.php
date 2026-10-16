<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
require_once("class_print_boleta.php");

class dw_detalle_plan_pago extends dw_detalle_plan_pago_base{
    
    function __construct() {
        
        parent::__construct();
		$sql = "SELECT 	PP.COD_PLAN_PAGO
									,PP.COD_PLAN_PAGO COD_PLAN_PAGO_H
									,D.COD_DEUDA
									,nvl(PP.ANO_COBRO,9999) ANO_COBRO
									,nvl(PP.ANO_COBRO,9999) ANO_COBRO_H
									,nvl(PP.NRO_CUOTA,9999) NRO_CUOTA
									,TD.COD_TIPO_DEUDA
									,Nvl(TO_CHAR(PP.FECHA_VENCTO,'dd/mm/yyyy'),'31/12/9999') FECHA_VENCTO
									,TO_CHAR(PP.FECHA_VENCTO,'dd/mm/yyyy') FECHA_VENCTO_H
									,F_PP_SALDO_CAPITAL(PP.COD_PLAN_PAGO, B.FECHA_BOLETA) SALDO_CAPITAL
									,F_PP_SALDO_INTERES_NORMAL(PP.COD_PLAN_PAGO, B.FECHA_BOLETA) SALDO_INTERES
				        			,0 SALDO_MULTA
				        			,F_PP_SALDO_CAPITAL(PP.COD_PLAN_PAGO, B.FECHA_BOLETA) + F_PP_SALDO_INTERES_NORMAL(PP.COD_PLAN_PAGO, B.FECHA_BOLETA) SALDO_MONEDA        
				        			,F_MON_GET_VALOR_MONEDA(M.COD_MONEDA, B.FECHA_BOLETA) * (F_PP_SALDO_CAPITAL(PP.COD_PLAN_PAGO, B.FECHA_BOLETA) + F_PP_SALDO_INTERES_NORMAL(PP.COD_PLAN_PAGO, B.FECHA_BOLETA)) SALDO_PESOS	
									,PP.COD_ESTADO_PLAN_PAGO COD_ESTADO_PLAN_PAGO_H
									,NOM_ESTADO_PLAN_PAGO					
						FROM 		BOLETA B
									,DEUDA D
									,PLAN_PAGO PP
									,MONEDA M
									,TIPO_DEUDA TD
									,ESTADO_PLAN_PAGO EPP
						WHERE		B.COD_DEUDA = D.COD_DEUDA 
									AND B.COD_PLAN_PAGO = PP.COD_PLAN_PAGO(+)
									AND B.COD_BOLETA= {KEY1}
									AND TD.COD_MONEDA = M.COD_MONEDA
									AND TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
									AND PP.COD_ESTADO_PLAN_PAGO = EPP.COD_ESTADO_PLAN_PAGO(+)
						ORDER BY	PP.NRO_CUOTA";
		$this->set_sql($sql); 
	}
}

class wi_boleta extends wi_boleta_base {
    
    function __construct($cod_item_menu) {
        parent::__construct($cod_item_menu);
		$this->b_print_visible		= true;
	}
	
	function load_record() {
		
		$this->b_save_visible  		= false;
		$this->b_no_save_visible	= false;
		$this->b_modify_visible  	= false;
		$this->b_print_visible		= true;
		
		$cod_boleta		= $this->get_item_wo($this->current_record, 'COD_BOLETA');
		$this->dws['dw_boleta']->retrieve($cod_boleta);
		$this->dws['dw_boleta']->remove_control('COD_TIPO_DEUDA');
		$this->dws['dw_detalle_plan_pago']->retrieve($cod_boleta);
		$ano_cobro 		= $this->dws['dw_detalle_plan_pago']->get_item(0, 'ANO_COBRO_H');
		$nro_cuota		= $this->dws['dw_detalle_plan_pago']->get_item(0, 'NRO_CUOTA');
		$fecha_vencto	= $this->dws['dw_detalle_plan_pago']->get_item(0, 'FECHA_VENCTO');

		$this->dws['dw_boleta']->set_item(0, 'ANO_COBRO_PP', $ano_cobro);
		$this->dws['dw_boleta']->set_item(0, 'NRO_CUOTA_PP', $nro_cuota);
		$this->dws['dw_boleta']->set_item(0, 'FECHA_VENCTO_PP', $fecha_vencto);
		
		$this->dws['dw_subcuota']->retrieve($cod_boleta);
		$this->dws['dw_tab_subcuota']->retrieve($cod_boleta);
		$this->dws['dw_tab_subcuota_detalle']->retrieve($cod_boleta);
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	 	$sql= sql_boleta::get_sql('DEUDA_MOROSA_USACH',$cod_boleta);
	 	$result = $db->build_results($sql);
	 	$count = count($result);

	 	$cadena_morosa = '';
	 	if($count > 0){
	 		$this->dws['dw_boleta']->set_item(0, 'MOROSA_DISPLAY', '');
	 		for($x=0;$x<$count;$x=$x+1){
				$ano = $result[$x]['ANO_COBRO'];
				if($cadena_morosa == ''){
					$cadena_morosa = $ano;
				}
				else{
					$cadena_morosa = $cadena_morosa.', '.$ano;
				}
	 		}
	 		$this->dws['dw_boleta']->set_item(0, 'CUOTA_MOROSA', $cadena_morosa);
	 	}
	}
	
	function print_record() {
		$cod_boleta = $this->get_key();
		$this->b_save_visible  		= false;
		$this->b_no_save_visible	= false;
		$this->b_modify_visible  	= false;
		$this->b_print_visible		= true;
		
	 	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

	 	$sql = "select $cod_boleta COD_BOLETA from DUAL";
		
	 	// reporte
		$labels = array();
		$file_name = $this->find_file('boleta', 'boleta.xml');
		
		$rpt = new print_boleta($sql, $file_name, $labels, "Boleta".$cod_boleta.'.pdf', 0);									
		$this->_load_record();	
	}
	
	function new_record(){
		$sql_ultimo_dia = sql_boleta::get_sql('ULTIMO_DIA');
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results($sql_ultimo_dia);
		
		////////////////////////////dw_boleta////////////////////////////
		$this->dws['dw_boleta']->insert_row();
		$this->dws['dw_boleta']->set_item(0, 'FECHA_BOLETA', $this->current_date());
		$this->dws['dw_boleta']->set_item(0, 'FECHA_HOY_H', $this->current_date());
		$this->dws['dw_boleta']->set_item(0, 'NOM_USUARIO', $this->nom_usuario);

		////////////////////////////dw_tab_subcuota////////////////////////////
		$this->dws['dw_tab_subcuota']->insert_row();
		$this->dws['dw_tab_subcuota']->set_item(0, 'FECHA_PRIMERA_CUOTA', $result[0]['ULTIMO_DIA_DEL_MES']);
		$this->dws['dw_tab_subcuota']->set_item(0, 'FECHA_PRIMERA_CUOTA_H', $result[0]['ULTIMO_DIA_DEL_MES']);
		$this->dws['dw_tab_subcuota']->set_item(0, 'CANT', 1);

		////////////////////////////dw_subcuota////////////////////////////
		$this->dws['dw_subcuota']->insert_row();

		////////////////////////////dw_tab_subcuota_detalle////////////////////////////
		$this->dws['dw_tab_subcuota_detalle']->insert_row();

		$this->dws['dw_tab_subcuota_detalle']->set_item(0, 'SC_FECHA_VENCTO', $result[0]['ULTIMO_DIA_DEL_MES']);
		$this->dws['dw_tab_subcuota_detalle']->set_item(0, 'SC_NRO_SUB_BOLETA', 1);
		
		$this->dws['dw_boleta']->set_item(0, 'CARTERA_CONOCIDA', 'N');
		$this->dws['dw_boleta']->set_item(0, 'MOROSA_DISPLAY', 'none');
	}
}
?>
