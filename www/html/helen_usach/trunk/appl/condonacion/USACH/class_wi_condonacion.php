<?php
////////////////////////////////////////
/////////////// USACH //////////////////
////////////////////////////////////////
/*
 Clase : WI_CONDONACION
 */
class dw_detalle_deuda extends dw_detalle_deuda_base{
    function __construct() {
		$sql = sql_condonacion::get_sql('INPUT_DETALLE_DEUDA');
		parent::__construct($sql, 'DETALLE_DEUDA');
		
		$this->add_control(new edit_check_box('SELECCION', 'S', 'N'));
		$this->add_control(new static_text('COD_TIPO_DEUDA'));
		$this->add_control(new edit_text_hidden('COD_DEUDA_H', 10,10));
	}
	function update($db,$cod_condonacion, $operacion,$cod_usuario_confirma,$cod_estado_condonacion) {
		$sp = 'sp_condonacion_deuda';

		// Al aprobar (APROBADA), SELECCION queda no-entrable y no se envía en el POST,
		// por lo que el filtro por SELECCION solo aplica durante la solicitud (INSERT).
		$es_aprobacion = ($cod_estado_condonacion == self::K_ESTADO_CONDONACION_APROBADA);

		for ($i = 0; $i < $this->row_count(); $i++) {
			
			$cod_deuda_condonacion	= $this->get_item($i, 'COD_DEUDA_CONDONACION_H');
			$cod_deuda 				= $this->get_item($i, 'COD_DEUDA');
			$no_exigible 			= $this->get_item($i, 'NO_EXIGIBLE_H');
			$vencida				= $this->get_item($i, 'VENCIDA_H');
			$vigente 				= $this->get_item($i, 'VIGENTE_H');
			$futura 				= $this->get_item($i, 'FUTURA_H');
			$cod_comprobante		= $this->get_item($i, 'COD_COMPROBANTE');
			$seleccion 				= $this->get_item($i, 'SELECCION');
				
			if ($es_aprobacion){
				$confirma ='S';
			}
			else{
				$confirma ='N';
			}
			
			if(K_TIPO_BD == "oci"){
				$cod_deuda_condonacion	= ($cod_deuda_condonacion=='') ? "null" : $cod_deuda_condonacion;
			}
			else if(K_TIPO_BD == "mssql"){
				$cod_deuda_condonacion	= ($cod_deuda_condonacion==' ') ? "null" : $cod_deuda_condonacion;
			}

			// En aprobación se procesan TODAS las filas (SELECCION no es confiable porque
			// el campo queda no-entrable y el checkbox no se envía en el POST).
			// En solicitud se respeta la selección del usuario.
			if ($es_aprobacion || $seleccion == 'S'){
				if (!$db->EXECUTE_SP($sp, "'$operacion',$cod_deuda_condonacion,$cod_condonacion,$cod_deuda,$no_exigible,$vencida,$vigente,$futura,'$confirma',$cod_usuario_confirma"))
					return false;
			}
		}
		return true;
	}
}

/*
 * $this->dws['dw_condonacion']->add_control($control = new drop_down_dw('COD_TIPO_CONDONACION', $sql, 140));
	$control->set_onChange("change_simulacion_empresa(this);");
 * 
 */
class wi_condonacion extends wi_condonacion_base {
    
	const K_PARAM_CARTERA = 2;
	const K_TIPO_CONDONACION_UNO = 1;
	const K_TIPO_CONDONACION_DEFUNCION = 2;
	const K_TIPO_CONDONAICON_INC_TRABAJAR = 3;
	const K_TIPO_CONDONAICON_LEY_CRUZADO = 7;
	
	function __construct($cod_item_menu) {
	    parent::__construct($cod_item_menu); 
	}
	function new_record(){
		parent::new_record();
		$this->dws['dw_condonacion']->set_item(0, 'VISIBLE_CAMPO', 'none');
		
		$sql = 'SELECT 	COD_TIPO_CONDONACION
						,NOM_TIPO_CONDONACION
				FROM 	TIPO_CONDONACION		
				WHERE 	COD_TIPO_CONDONACION in ('.self::K_TIPO_CONDONACION_UNO.','.self::K_TIPO_CONDONACION_DEFUNCION.','.self::K_TIPO_CONDONAICON_INC_TRABAJAR.','.self::K_TIPO_CONDONAICON_LEY_CRUZADO.')'; //sql_condonacion::get_sql('DD_TIPO_CONDONACION');
// 		ECHO $sql;
		//$this->dws['dw_condonacion']->add_control($control = new drop_down_dw('COD_TIPO_CONDONACION', $sql, 140));
		//$control->set_onChange("deuda_cs_x_ley(this);");
		$this->dws['dw_condonacion']->add_control($control = new drop_down_dw('COD_TIPO_CONDONACION', $sql, 140));
		$control->set_onChange("is_cruzado();");
	}
	function load_record(){
		parent::load_record();
		$cod_condonacion	= $this->get_item_wo($this->current_record, 'COD_CONDONACION');
		
		$cartera_defecto = base::get_parametro(self::K_PARAM_CARTERA);
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql ="SELECT count(*) CUANTO 
				FROM CONDONACION C, DEUDA DA
				WHERE C.COD_DEUDOR = DA.COD_DEUDOR
				AND COD_CONDONACION = $cod_condonacion
				AND COD_CARTERA <> $cartera_defecto";
		$result = $db->build_results($sql);
		if ($result[0]['CUANTO']!= 0)
			$this->dws['dw_condonacion']->set_item(0, 'VISIBLE_CAMPO', '');
		else
			$this->dws['dw_condonacion']->set_item(0, 'VISIBLE_CAMPO', 'none');

		$cod_estado_condonacion = $this->dws['dw_condonacion']->get_item(0, 'COD_ESTADO_CONDONACION');
		$nombre_usuario = $this->nom_usuario;
		if ($cod_estado_condonacion == self::K_ESTADO_CONDONACION_SOLICITADA){
			
			$this->b_print_visible  = false;
			
			$sql = sql_condonacion::get_sql('DD_TIPO_CONDONACION_USACH', self::K_TIPO_CONDONACION_UNO, self::K_TIPO_CONDONACION_DEFUNCION, self::K_TIPO_CONDONAICON_INC_TRABAJAR,self::K_TIPO_CONDONAICON_LEY_CRUZADO);
			
			unset($this->dws['dw_condonacion']->controls['COD_TIPO_CONDONACION']);
			$this->dws['dw_condonacion']->add_control($control = new drop_down_dw('COD_TIPO_CONDONACION',$sql,140));
			$control->set_onChange("is_cruzado();");
			
			$this->dws['dw_condonacion']->set_entrable('COD_ESTADO_CONDONACION', true);
			$fecha_confirma = $this->current_date();
			
			$sql = sql_condonacion::get_sql('DD_ESTADO_CONDONACION_1');

			unset($this->dws['dw_condonacion']->controls['COD_ESTADO_CONDONACION']);
			$this->dws['dw_condonacion']->add_control($control = new drop_down_dw('COD_ESTADO_CONDONACION',$sql,125));
			$control->set_onChange("f_usuario_confirma('$nombre_usuario','$fecha_confirma');");
			
			$this->dws['dw_detalle_deuda']->set_entrable('SELECCION', false);
		}
	}
}	
?>