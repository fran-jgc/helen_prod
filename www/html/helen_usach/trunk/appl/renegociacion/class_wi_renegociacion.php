<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class dw_plan_pago_renegociacion extends datawindow {
	function __construct() {
		$sql = "select 'S' SELECCION
						,PP.COD_PLAN_PAGO
						,PP.ANO_COBRO
						,convert(varchar, PP.FECHA_VENCTO, 103) FECHA_VENCTO
						,PPR.SALDO_CUOTA
						,PPR.SALDO_MULTA
						,PPR.TOTAL_SALDO_MONEDA
						,PPR.TOTAL_SALDO_PESOS
						,PP.COD_PLAN_PAGO COD_PLAN_PAGO_H
						,PPR.SALDO_CUOTA SALDO_CUOTA_H
						,PPR.SALDO_MULTA SALDO_MULTA_H
						,PPR.TOTAL_SALDO_MONEDA TOTAL_SALDO_MONEDA_H
						,PPR.TOTAL_SALDO_PESOS TOTAL_SALDO_PESOS_H
				from PLAN_PAGO_RENEGOCIACION PPR, PLAN_PAGO PP
				where PPR.COD_RENEGOCIACION = {KEY1}
				  and PP.COD_PLAN_PAGO = PPR.COD_PLAN_PAGO";
		
		parent::__construct($sql, 'PP_RENEGOCIACION');
		$this->add_control($control = new edit_check_box('SELECCION', 'S', 'N'));
		$control->set_onChange("change_seleccion(this);");
		$this->add_control(new static_text('ANO_COBRO'));
		$this->add_control(new static_text('FECHA_VENCTO'));
		$this->add_control(new static_num('SALDO_CUOTA', 4));
		$this->add_control(new static_num('SALDO_MULTA', 4));
		$this->add_control(new static_num('TOTAL_SALDO_MONEDA', 4));
		$this->add_control(new static_num('TOTAL_SALDO_PESOS', 0));

		$this->add_control(new edit_text('COD_PLAN_PAGO_H', 10, 10, 'hidden'));
		$this->add_control(new edit_text('SALDO_CUOTA_H', 10, 10, 'hidden'));
		$this->add_control(new edit_text('SALDO_MULTA_H', 10, 10, 'hidden'));
		$this->add_control(new edit_text('TOTAL_SALDO_MONEDA_H', 10, 10, 'hidden'));
		$this->add_control(new edit_text('TOTAL_SALDO_PESOS_H', 10, 10, 'hidden'));
	}
	function update($db, $cod_renegociacion){
		$sp = 'spu_plan_pago_renegociacion';
		for ($i = 0; $i < $this->row_count(); $i++){
			$statuts = $this->get_status_row($i);
			if ($statuts == K_ROW_NOT_MODIFIED || $statuts == K_ROW_NEW)
				continue;
			$seleccion = $this->get_item($i, 'SELECCION');
			if ($seleccion!='S')
				continue;
				
			$cod_plan_pago = $this->get_item($i, 'COD_PLAN_PAGO_H');
			$saldo_cuota = $this->get_item($i, 'SALDO_CUOTA_H');
			$saldo_multa = $this->get_item($i, 'SALDO_MULTA_H');
			$total_saldo_moneda = $this->get_item($i, 'TOTAL_SALDO_MONEDA_H');
			$total_saldo_pesos = $this->get_item($i, 'TOTAL_SALDO_PESOS_H');

			$param = "'INSERT',$cod_renegociacion, $cod_plan_pago, $saldo_cuota, $saldo_multa, $total_saldo_moneda, $total_saldo_pesos";
			if (!$db->EXECUTE_SP($sp, $param)){
				return false;
			}
		}
		return true;
	}
}
class dw_suma_plan_pago_rin extends datawindow {
    function __construct() {
		$sql = "select sum(PPR.SALDO_CUOTA) SUMA_SALDO_CUOTA
						,sum(PPR.SALDO_MULTA) SUMA_SALDO_MULTA
						,sum(PPR.TOTAL_SALDO_MONEDA) SUMA_TOTAL_SALDO_MONEDA
						,sum(PPR.TOTAL_SALDO_PESOS) SUMA_TOTAL_SALDO_PESOS
				from PLAN_PAGO_RENEGOCIACION PPR
				where PPR.COD_RENEGOCIACION = {KEY1}";
		parent::__construct($sql);
		$this->add_control(new static_num('SUMA_SALDO_CUOTA', 4));
		$this->add_control(new static_num('SUMA_SALDO_MULTA', 4));
		$this->add_control(new static_num('SUMA_TOTAL_SALDO_MONEDA', 4));
		$this->add_control(new static_num('SUMA_TOTAL_SALDO_PESOS', 0));
	}
}
class wi_renegociacion extends w_input {
	const K_ESTADO_RENEGOCIACION_EMITIDA	= 1;
	const K_ESTADO_RENEGOCIACION_CONFIRMADA	= 2;
	const K_ESTADO_RENEGOCIACION_ANULADA	= 3;
	const K_PARAM_PORC_PAGO_RENEG = 28;
	const K_OPCION_ESPECIAL = '992005';  //numero perteneciente a item_menu de rengociacion, opcion especial.

	function __construct($cod_item_menu)	{
	    parent::__construct('renegociacion', $cod_item_menu);
		$this->b_delete_visible  = false;

		//////////////// CLASS_SQL = INPUT ///////////////
		$sql =	"SELECT COD_RENEGOCIACION
						,convert(varchar, R.FECHA_RENEGOCIACION, 103) FECHA_RENEGOCIACION
						, U.NOM_USUARIO
						, R.COD_ESTADO_RENEGOCIACION
						, dbo.f_last_mod('NOM_USUARIO', 'RENEGOCIACION', 'COD_ESTADO_RENEGOCIACION', R.COD_RENEGOCIACION) NOM_USUARIO_CAMBIO
				        , dbo.f_last_mod('FECHA_CAMBIO', 'RENEGOCIACION', 'COD_ESTADO_RENEGOCIACION', R.COD_RENEGOCIACION) FECHA_CAMBIO
				        ,D.COD_DEUDOR
				        ,D.RUT
				        ,D.DIG_VERIF
						,D.NOMBRE + ' ' + D.APELLIDO_PATERNO + ' ' + D.APELLIDO_MATERNO  NOM_DEUDOR
						,DA.COD_TIPO_DEUDA
						,DA.COD_DEUDA
						,DA.COD_DEUDA COD_DEUDA_H
						,C.NOM_CARTERA
						,E.NOM_ESTADO_DEUDA
						,DA.CANT_CUOTA CANT_CUOTA_DEUDA
						,M.SIMBOLO
						,dbo.f_get_parametro(".self::K_PARAM_PORC_PAGO_RENEG.") PORC_PAGO_SUGERIDO
						,dbo.f_get_parametro(".self::K_PARAM_PORC_PAGO_RENEG.") PORC_PAGO_SUGERIDO_H
						,R.PAGO_INICIAL_SUGERIDO
						,R.PAGO_INICIAL_SUGERIDO PAGO_INICIAL_SUGERIDO_H
						,R.PAGO_INICIAL
						,R.MONTO_RENEGOCIAR
						,R.MONTO_RENEGOCIAR MONTO_RENEGOCIAR_H
						,R.MONTO_RENEGOCIAR_MONEDA
						,R.MONTO_RENEGOCIAR_MONEDA MONTO_RENEGOCIAR_MONEDA_H
						,year(R.FECHA_INICIO_COBRO) ANO_INICIO_COBRO
						,R.MONTO_RENEGOCIAR_ACT
						,R.MONTO_RENEGOCIAR_ACT MONTO_RENEGOCIAR_ACT_H
						,dbo.f_mon_get_valor_moneda(T.COD_MONEDA, FECHA_RENEGOCIACION) VALOR_MONEDA
						,R.CANT_CUOTA
						,round(R.CUOTA_ANUAL_MONEDA * dbo.f_mon_get_valor_moneda(T.COD_MONEDA, FECHA_RENEGOCIACION), 0) CUOTA_ANUAL
						,R.CUOTA_ANUAL_MONEDA
						,R.CUOTA_ANUAL_MONEDA CUOTA_ANUAL_MONEDA_H
						,R.OBS
						,R.COD_DEUDA_NUEVA
						,R.COD_COMPROBANTE
						,R.COD_ESTADO_RENEGOCIACION COD_ESTADO_ACTUAL
						,'' COD_USUARIO_H
						,dbo.f_reneg_saldo_pago_inicial(R.COD_RENEGOCIACION) SALDO_RENEGOCIACION_H
						,R.AUTORIZADO
						,case R.AUTORIZADO when 'N' then 
							''
						else
							dbo.f_last_mod('NOM_USUARIO', 'RENEGOCIACION', 'AUTORIZADO', R.COD_RENEGOCIACION)
						end USUARIO_AUTORIZA							
						,case autorizado when 'N' then 
							''
						else
							dbo.f_last_mod('FECHA_CAMBIO', 'RENEGOCIACION', 'AUTORIZADO', R.COD_RENEGOCIACION) 
						end FECHA_AUTORIZA		
				FROM 	RENEGOCIACION R, USUARIO U, DEUDA DA, TIPO_DEUDA T, MONEDA M, DEUDOR D, CARTERA C, ESTADO_DEUDA E 
				WHERE 	R.COD_RENEGOCIACION = {KEY1}
				AND		U.COD_USUARIO = R.COD_USUARIO
				AND		DA.COD_DEUDA = R.COD_DEUDA
				AND		D.COD_DEUDOR = DA.COD_DEUDOR
 				and 	C.COD_CARTERA = DA.COD_CARTERA
		  		and 	E.COD_ESTADO_DEUDA = DA.COD_ESTADO_DEUDA
		  		and		T.COD_TIPO_DEUDA = DA.COD_TIPO_DEUDA
				and		M.COD_MONEDA = T.COD_MONEDA";

		$this->dws['dw_renegociacion'] = new datawindow($sql);
		$this->dws['dw_renegociacion']->add_control(new static_text('FECHA_RENEGOCIACION'));
		$this->dws['dw_renegociacion']->add_control($control = new edit_num('RUT', 10, 10));
		$this->dws['dw_renegociacion']->add_control(new static_text('DIG_VERIF'));
		$this->dws['dw_renegociacion']->add_control(new static_text('NOM_DEUDOR'));
		$control->set_onChange("change_rut(this);");
		$sql = "SELECT		COD_ESTADO_RENEGOCIACION
							,NOM_ESTADO_RENEGOCIACION
				FROM		ESTADO_RENEGOCIACION
				ORDER BY	COD_ESTADO_RENEGOCIACION";
		$this->dws['dw_renegociacion']->add_control($control = new drop_down_dw('COD_ESTADO_RENEGOCIACION', $sql,0,'',false));
		$control->set_onChange("valida_saldo();");
		$this->dws['dw_renegociacion']->add_control(new static_text('COD_DEUDA'));
		$this->dws['dw_renegociacion']->add_control(new edit_text('COD_DEUDA_H', 10, 10, 'hidden'));
		$this->dws['dw_renegociacion']->add_control(new static_text('SIMBOLO'));
		$this->dws['dw_renegociacion']->add_control(new static_text('NOM_CARTERA'));
		$this->dws['dw_renegociacion']->add_control(new static_text('NOM_ESTADO_DEUDA'));
		$this->dws['dw_renegociacion']->add_control(new static_text('CANT_CUOTA_DEUDA'));
		$this->dws['dw_renegociacion']->add_control(new edit_text('PORC_PAGO_SUGERIDO_H', 10, 10, 'hidden'));
		$this->dws['dw_renegociacion']->add_control(new static_num('PAGO_INICIAL_SUGERIDO'));
		$this->dws['dw_renegociacion']->add_control(new edit_text('PAGO_INICIAL_SUGERIDO_H', 10, 10, 'hidden'));
		$this->dws['dw_renegociacion']->add_control($control = new edit_num('PAGO_INICIAL', 10, 10));
		$control->set_onChange("change_pago_inicial(this);valida_saldo();");
		$this->dws['dw_renegociacion']->add_control(new static_num('MONTO_RENEGOCIAR'));
		$this->dws['dw_renegociacion']->add_control(new edit_text('MONTO_RENEGOCIAR_H', 10, 10, 'hidden'));
		$this->dws['dw_renegociacion']->add_control(new static_num('MONTO_RENEGOCIAR_MONEDA', 4));
		$this->dws['dw_renegociacion']->add_control(new edit_text('MONTO_RENEGOCIAR_MONEDA_H', 10, 10, 'hidden'));
		$this->dws['dw_renegociacion']->add_control($control = new edit_ano('ANO_INICIO_COBRO'));
		$control->set_onChange("change_cant_cuota(document.getElementById('CANT_CUOTA_0'));");
		$this->dws['dw_renegociacion']->add_control(new static_num('MONTO_RENEGOCIAR_ACT', 4));
		$this->dws['dw_renegociacion']->add_control(new edit_text('MONTO_RENEGOCIAR_ACT_H', 10, 10, 'hidden'));
		$this->dws['dw_renegociacion']->add_control(new edit_text('VALOR_MONEDA', 10, 10, 'hidden'));
		$aCuotas = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15);
		$this->dws['dw_renegociacion']->add_control($control = new drop_down_list('CANT_CUOTA', $aCuotas, $aCuotas));
		$control->set_onChange("change_cant_cuota(this);");
		$this->dws['dw_renegociacion']->add_control(new static_num('CUOTA_ANUAL'));
		$this->dws['dw_renegociacion']->add_control(new static_num('CUOTA_ANUAL_MONEDA', 4));
		$this->dws['dw_renegociacion']->add_control(new edit_text('CUOTA_ANUAL_MONEDA_H', 10, 10, 'hidden'));
        $this->dws['dw_renegociacion']->add_control(new edit_text_multiline('OBS', 100, 3));
        $this->dws['dw_renegociacion']->add_control(new edit_text('COD_ESTADO_ACTUAL', 10, 10, 'hidden'));
        $this->dws['dw_renegociacion']->add_control(new edit_text('COD_USUARIO_H', 10, 10, 'hidden'));
        $this->dws['dw_renegociacion']->add_control(new edit_text('SALDO_RENEGOCIACION_H', 10, 10, 'hidden'));
        $this->dws['dw_renegociacion']->add_control(new edit_check_box('AUTORIZADO', 'S', 'N', ''));
        $this->dws['dw_renegociacion']->add_control(new static_text('FECHA_AUTORIZA'));
        $this->dws['dw_renegociacion']->add_control(new static_text('USUARIO_AUTORIZA'));

		// dw_plan_pago_renegociacion
		$this->dws['dw_plan_pago_renegociacion'] = new dw_plan_pago_renegociacion();
		$this->dws['dw_suma_plan_pago_rin'] = new dw_suma_plan_pago_rin();

		// asigna los mandatorys
		$this->dws['dw_renegociacion']->set_mandatory('RUT', 'Rut');

		//audoria
		$this->add_auditoria('AUTORIZADO');
		$this->add_auditoria('COD_ESTADO_RENEGOCIACION');

		$this->set_first_focus('RUT');
	}
	function new_record() {
		$this->dws['dw_renegociacion']->insert_row();
		$this->dws['dw_renegociacion']->set_item(0, 'FECHA_RENEGOCIACION', $this->current_date());
		$this->dws['dw_renegociacion']->set_item(0, 'NOM_USUARIO', $this->nom_usuario);
		$this->dws['dw_renegociacion']->set_item(0, 'COD_ESTADO_RENEGOCIACION', self::K_ESTADO_RENEGOCIACION_EMITIDA);
		$this->dws['dw_renegociacion']->set_entrable('COD_ESTADO_RENEGOCIACION', false);
		$this->dws['dw_renegociacion']->set_item(0, 'PORC_PAGO_SUGERIDO', $this->get_parametro(self::K_PARAM_PORC_PAGO_RENEG));
		$this->dws['dw_renegociacion']->set_item(0, 'PORC_PAGO_SUGERIDO_H', $this->get_parametro(self::K_PARAM_PORC_PAGO_RENEG));
		//$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		//$this->dws['dw_renegociacion']->set_item(0, 'ANO_INICIO_COBRO', $db->current_year() + 1);

		// control para seleccionar tipo deuda
		$sql = "SELECT		COD_DEUDA
							,COD_TIPO_DEUDA
				FROM		DEUDA
				where COD_DEUDOR = {KEY1}
				ORDER BY	COD_TIPO_DEUDA";
		$this->dws['dw_renegociacion']->add_control($control = new drop_down_dw('COD_TIPO_DEUDA', $sql));
		$control->set_onChange("change_tipo_deuda(this);");

		// valor UTM
		//$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "select dbo.f_mon_get_valor_moneda(3, getdate()) VALOR_MONEDA";
		//$result = $db->build_results($sql);
		//$this->dws['dw_renegociacion']->set_item(0, 'VALOR_MONEDA', $result[0]['VALOR_MONEDA']);

		$this->dws['dw_suma_plan_pago_rin']->insert_row();

		if (session::is_set("CREADA_DESDE_DEUDA")) {
			$this->b_modify_visible	 	= false;
			$cod_deuda = session::get("CREADA_DESDE_DEUDA");
			session::un_set("CREADA_DESDE_DEUDA");
			$this->crear_desde_deuda($cod_deuda);
		}
		
		$this->dws['dw_renegociacion']->set_entrable('AUTORIZADO', false);
		
	}
	function crear_desde_deuda($cod_deuda) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		if(K_TIPO_BD == "oci"){
			$sql = "falta";
		}
		else if(K_TIPO_BD == "mssql"){
			$sql = "select 	D.RUT
							,D.DIG_VERIF
							,NOMBRE+' '+APELLIDO_PATERNO+' '+APELLIDO_MATERNO NOM_DEUDOR
							,DA.COD_TIPO_DEUDA
							,C.NOM_CARTERA
							,E.NOM_ESTADO_DEUDA
							,DA.CANT_CUOTA
							,M.SIMBOLO
					FROM 	DEUDA DA, DEUDOR D, CARTERA C, ESTADO_DEUDA E, TIPO_DEUDA T, MONEDA M 
					WHERE 	DA.COD_DEUDA = $cod_deuda
					  and 	D.COD_DEUDOR = DA.COD_DEUDOR
					  and 	C.COD_CARTERA = DA.COD_CARTERA
					  and 	E.COD_ESTADO_DEUDA = DA.COD_ESTADO_DEUDA
					  and 	T.COD_TIPO_DEUDA = DA.COD_TIPO_DEUDA 
					  and 	M.COD_MONEDA = T.COD_MONEDA";
		}			
		$result = $db->build_results($sql);
		$this->dws['dw_renegociacion']->set_item(0, 'RUT', $result[0]['RUT']);
		$this->dws['dw_renegociacion']->set_item(0, 'DIG_VERIF', $result[0]['DIG_VERIF']);
		$this->dws['dw_renegociacion']->set_item(0, 'NOM_DEUDOR', $result[0]['NOM_DEUDOR']);

		$cod_tipo_deuda = $result[0]['COD_TIPO_DEUDA'];
		$sql = "select $cod_deuda COD_DEUDA
						,'$cod_tipo_deuda' COD_TIPO_DEUDA";
		$this->dws['dw_renegociacion']->controls['COD_TIPO_DEUDA']->set_sql($sql);
		$this->dws['dw_renegociacion']->controls['COD_TIPO_DEUDA']->retrieve();
		$this->dws['dw_renegociacion']->set_item(0, 'COD_TIPO_DEUDA', $cod_deuda);
		$this->dws['dw_renegociacion']->set_item(0, 'COD_DEUDA', $cod_deuda);
		$this->dws['dw_renegociacion']->set_item(0, 'COD_DEUDA_H', $cod_deuda);
		$this->dws['dw_renegociacion']->set_item(0, 'NOM_CARTERA', $result[0]['NOM_CARTERA']);
		$this->dws['dw_renegociacion']->set_item(0, 'NOM_ESTADO_DEUDA', $result[0]['NOM_ESTADO_DEUDA']);
		$this->dws['dw_renegociacion']->set_item(0, 'CANT_CUOTA_DEUDA', $result[0]['CANT_CUOTA']);
		$this->dws['dw_renegociacion']->set_item(0, 'SIMBOLO', $result[0]['SIMBOLO']);
		
		// cuotas
		$sql = sql_renegociacion::get_sql('AJAX_CHANGE_TIPO_DEUDA',$cod_deuda);
		$result = $db->build_results($sql);
		if (count($result)==0) {
			$this->redraw();
			$this->message('Esta Deuda No contiene Cuotas a Convenir, escoja otro tipo de Deuda');
		}
		else {
			for($i=0; $i < count($result); $i++) {
				$row = $this->dws['dw_plan_pago_renegociacion']->insert_row();
				$this->dws['dw_plan_pago_renegociacion']->set_item($i, 'SELECCION', $result[$i]['SELECCION']);
				$this->dws['dw_plan_pago_renegociacion']->set_item($i, 'ANO_COBRO', $result[$i]['ANO_COBRO']);
				$this->dws['dw_plan_pago_renegociacion']->set_item($i, 'FECHA_VENCTO', $result[$i]['FECHA_VENCTO']);
				$this->dws['dw_plan_pago_renegociacion']->set_item($i, 'SALDO_CUOTA', $result[$i]['SALDO_CUOTA']);
				$this->dws['dw_plan_pago_renegociacion']->set_item($i, 'SALDO_MULTA', $result[$i]['SALDO_MULTA']);
				$this->dws['dw_plan_pago_renegociacion']->set_item($i, 'TOTAL_SALDO_MONEDA', $result[$i]['TOTAL_SALDO_MONEDA']);
				$this->dws['dw_plan_pago_renegociacion']->set_item($i, 'TOTAL_SALDO_PESOS', $result[$i]['TOTAL_SALDO_PESOS']);

				$this->dws['dw_plan_pago_renegociacion']->set_item($i, 'COD_PLAN_PAGO_H', $result[$i]['COD_PLAN_PAGO']);
				$this->dws['dw_plan_pago_renegociacion']->set_item($i, 'SALDO_CUOTA_H', $result[$i]['SALDO_CUOTA']);
				$this->dws['dw_plan_pago_renegociacion']->set_item($i, 'SALDO_MULTA_H', $result[$i]['SALDO_MULTA']);
				$this->dws['dw_plan_pago_renegociacion']->set_item($i, 'TOTAL_SALDO_MONEDA_H', $result[$i]['TOTAL_SALDO_MONEDA']);
				$this->dws['dw_plan_pago_renegociacion']->set_item($i, 'TOTAL_SALDO_PESOS_H', $result[$i]['TOTAL_SALDO_PESOS']);
			}
		}
	}
	function load_record() {
		$COD_RENEGOCIACION = $this->get_item_wo($this->current_record, 'COD_RENEGOCIACION');
		$this->dws['dw_renegociacion']->retrieve($COD_RENEGOCIACION);
		$this->dws['dw_plan_pago_renegociacion']->retrieve($COD_RENEGOCIACION);
		$this->dws['dw_suma_plan_pago_rin']->retrieve($COD_RENEGOCIACION);
		
		$this->dws['dw_renegociacion']->set_item(0,'COD_USUARIO_H',$this->cod_usuario);

		$cod_estado_renegociacion = $this->dws['dw_renegociacion']->get_item(0, 'COD_ESTADO_RENEGOCIACION');
		switch ($cod_estado_renegociacion) {
			case self::K_ESTADO_RENEGOCIACION_ANULADA: 
	            $this->b_print_visible  = false;
	            $this->b_modify_visible  = false;
	            $this->b_save_visible  = false;
	            $this->b_no_save_visible  = false;
	            break;
			case self::K_ESTADO_RENEGOCIACION_CONFIRMADA: 
	            $this->b_print_visible  = true;
	            $this->b_modify_visible  = false;
	            $this->b_save_visible  = false;
	            $this->b_no_save_visible  = false;
	            break;
	         default:
	            $this->b_print_visible  = true;
	            $this->b_modify_visible  = true;
	            $this->b_save_visible  = true;
	            $this->b_no_save_visible  = true;
		}
		
		$this->dws['dw_renegociacion']->set_entrable('COD_ESTADO_RENEGOCIACION', true);
		$this->dws['dw_renegociacion']->set_entrable('RUT', false);
		$this->dws['dw_plan_pago_renegociacion']->set_entrable('SELECCION', false);
		
		// Borra el control de TIPO_DEUDA
		$this->dws['dw_renegociacion']->remove_control('COD_TIPO_DEUDA');
		
		// si el usuario no tiene privilegios de adm. checkbox de autorizacion no modificable
		$autorizado = $this->dws['dw_renegociacion']->get_item(0, 'AUTORIZADO');
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = sql_renegociacion::get_sql('AJAX_VALIDA_APROVADA',$this->cod_usuario,self::K_OPCION_ESPECIAL);
		$result = $db->build_results($sql);

		if(($result[0]['AUTORIZA_MENU']!='E')||($autorizado == 'S')){
			$this->dws['dw_renegociacion']->set_entrable('AUTORIZADO', false);
		}
		else
			$this->dws['dw_renegociacion']->set_entrable('AUTORIZADO', true);
	}
	function get_key() {
		return $this->dws['dw_renegociacion']->get_item(0, 'COD_RENEGOCIACION');
	}
	function procesa_event() {
		parent::procesa_event();
		//muestra mensaje, solo si no esta autorizado, como recordatorio
		if(isset($_POST['b_save_x'])) {
			$pago_inicial_sugerido		= $this->dws['dw_renegociacion']->get_item(0, 'PAGO_INICIAL_SUGERIDO_H');
			$pago_inicial				= $this->dws['dw_renegociacion']->get_item(0, 'PAGO_INICIAL');
			$cod_estado_renegociacion	= $this->dws['dw_renegociacion']->get_item(0, 'COD_ESTADO_RENEGOCIACION');
			
			$autorizado = $this->dws['dw_renegociacion']->get_item(0, 'AUTORIZADO');
			if(($autorizado == 'N')&&($cod_estado_renegociacion==1)){
				$this->message('¡El pago inicial es menor que el sugerido!. Recuerde que es necesario autorizar la renegociación para hacer el pago inicial.');
			}	
		}
	}
		
	
	function save_record($db){
		
		
		$cod_renegociacion			= $this->dws['dw_renegociacion']->get_item(0, 'COD_RENEGOCIACION');
		$cod_estado_renegociacion	= $this->dws['dw_renegociacion']->get_item(0, 'COD_ESTADO_RENEGOCIACION');
		$obs						= $this->dws['dw_renegociacion']->get_item(0, 'OBS');
		$cod_deuda					= $this->dws['dw_renegociacion']->get_item(0, 'COD_DEUDA_H');
		$pago_inicial_sugerido		= $this->dws['dw_renegociacion']->get_item(0, 'PAGO_INICIAL_SUGERIDO_H');
		$pago_inicial				= $this->dws['dw_renegociacion']->get_item(0, 'PAGO_INICIAL');
		$monto_renegociar			= $this->dws['dw_renegociacion']->get_item(0, 'MONTO_RENEGOCIAR_H');
		$monto_renegociar_moneda	= $this->dws['dw_renegociacion']->get_item(0, 'MONTO_RENEGOCIAR_MONEDA_H');
		$ano_inicio_cobro			= $this->dws['dw_renegociacion']->get_item(0, 'ANO_INICIO_COBRO');
		$monto_renegociar_act		= $this->dws['dw_renegociacion']->get_item(0, 'MONTO_RENEGOCIAR_ACT_H');
		$cant_cuota					= $this->dws['dw_renegociacion']->get_item(0, 'CANT_CUOTA');
		$cuota_anual_moneda			= $this->dws['dw_renegociacion']->get_item(0, 'CUOTA_ANUAL_MONEDA_H');
		
		$cod_renegociacion = ($cod_renegociacion =='') ? "null" : $cod_renegociacion;
		$pago_inicial = ($pago_inicial =='') ? "0" : $pago_inicial;
		$porc_pago_inicial = $this->get_parametro(self::K_PARAM_PORC_PAGO_RENEG);
		$obs = ($obs =='') ? "null" : "'$obs'";
		$fecha_inicio_cobro = $this->str2date('01/01/'.$ano_inicio_cobro);

		$sp = 'spu_renegociacion';
		if ($this->is_new_record()){
			$operacion = 'INSERT';
			
			if($pago_inicial < $pago_inicial_sugerido)
		 		$autorizado = 'N';
		 	else
		 		$autorizado = 'S';
		}	
		else{
			$operacion = 'UPDATE';
			$autorizado	= $this->dws['dw_renegociacion']->get_item(0, 'AUTORIZADO');
		}			
			
				
		$param	= "'$operacion'
					,$cod_renegociacion
					,$this->cod_usuario
					,$cod_estado_renegociacion
					,$obs
					,$cod_deuda
					,$porc_pago_inicial
					,$pago_inicial_sugerido
					,$pago_inicial
					,$monto_renegociar
					,$monto_renegociar_moneda
					,$fecha_inicio_cobro
					,$monto_renegociar_act
					,$cant_cuota
					,$cuota_anual_moneda
					,'$autorizado'";
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$COD_RENEGOCIACION = $db->GET_IDENTITY("renegociacion");
				$this->dws['dw_renegociacion']->set_item(0, 'COD_RENEGOCIACION', $COD_RENEGOCIACION);
				
				if (!$this->dws['dw_plan_pago_renegociacion']->update($db, $COD_RENEGOCIACION))
					return false;
			}
			
			return true;
		}
		
		
		return false;

	}
	/*function print_record() {
		$cod_renegociacion = $this->get_key();
		// reporte
		$labels = array();
		$file_name = $this->find_file('renegociacion', 'renegociacion.xml');
		$rpt = new print_renegociacion($cod_renegociacion, $file_name, $labels, "renegociacion".$cod_renegociacion, 0);
		$this->_load_record();
	}	*/
}
?>