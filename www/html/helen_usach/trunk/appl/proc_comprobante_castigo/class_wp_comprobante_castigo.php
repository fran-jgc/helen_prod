<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class dw_lista_tipo_deuda extends datawindow {
	const K_ESTADO_SII_IMPRESA 		= 2;
	const K_FAM_FONDO_SOLIDARIO 	= 1;
	
	function __construct() {
		$sql = "select COD_TIPO_DEUDA FROM TIPO_DEUDA WHERE COD_TIPO_DEUDA <> '{KEY1}' AND COD_FAMILIA_DEUDA = ".self::K_FAM_FONDO_SOLIDARIO;
		parent::__construct($sql, 'TIPO_DEUDA');

		$this->add_control(new static_text('COD_TIPO_DEUDA'));
	}
	function fill_record(&$temp, $record) {
		parent::fill_record($temp, $record);
		if ($record < $this->row_count() - 1)
			$temp->setVar($this->label_record.'.TD_SEPARADOR', '-');
	}
}

class dw_tipo_deuda_cs extends datawindow {
	const K_ESTADO_SII_IMPRESA 		= 2;
	
	function __construct() {
		$sql = "select COD_TIPO_DEUDA FROM TIPO_DEUDA WHERE COD_TIPO_DEUDA = '{KEY1}'";
		parent::__construct($sql, 'TIPO_DEUDA_CS');

		$this->add_control(new static_text('COD_TIPO_DEUDA'));
	}
	function fill_record(&$temp, $record) {
		parent::fill_record($temp, $record);
		if ($record < $this->row_count() - 1)
			$temp->setVar($this->label_record.'.TD_SEPARADOR', '-');
	}
}
class wp_comprobante_castigo extends w_proceso {
	const K_COMPROBANTE_CASTIGO = 17;
	 
	function __construct() {
   		// Si estamos en un mes <= 6 sugiere el 31/12/año_anterior
   		// en caso contrario 31/05/año_actual
   		if(K_TIPO_BD == "oci"){
			$sql = "SELECT TO_CHAR (sysdate, 'dd/mm/yyyy') FECHA_VENCTO
				           ,'' VALOR_UTM_MENSUAL
				           ,'' LABEL_UTM	
					FROM DUAL";
   		}
   		else if(K_TIPO_BD == "mssql"){
   			$sql = "SELECT convert(varchar(20), getdate(), 103) FECHA_VENCTO
						   	,'' VALOR_UTM_MENSUAL
						   	,'' LABEL_UTM";
   		}
   		$this->dws['dw_comprobante_castigo'] = new datawindow($sql);
   		
    	$this->dws['dw_comprobante_castigo']->add_control($control = new edit_date('FECHA_VENCTO', 10, 10));
    	$control->set_onChange("actualiza_utm(this);");
    	$this->dws['dw_comprobante_castigo']->add_control(new static_text('VALOR_UTM_MENSUAL', 10, 10, 2));
		$this->dws['dw_comprobante_castigo']->add_control(new static_text('LABEL_UTM'));
		$this->dws['dw_lista_tipo_deuda'] = new dw_lista_tipo_deuda();
		$this->dws['dw_tipo_deuda_cs'] = new dw_tipo_deuda_cs();
   		
		parent::__construct('comprobante_castigo', $_REQUEST['cod_item_menu'], self::K_COMPROBANTE_CASTIGO);
   	}
	function load_dws(){
		parent::load_dws();
		$tipo_deuda = 'CS';
		$this->dws['dw_comprobante_castigo']->retrieve($this->cod_proceso);
		$this->dws['dw_lista_tipo_deuda']->retrieve($tipo_deuda);
		$this->dws['dw_tipo_deuda_cs']->retrieve($tipo_deuda);

		//variable
		$cod_moneda = 3;
		$fecha_actual = $this->current_date();
		if(K_TIPO_BD == "oci"){
			//SETEANDO LA VARIBLE VALOR_UTM_MENSUAL
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$sql = "SELECT f_get_constante('K_CASTIGO_FECHA_MONEDA') CASTIGO_FECHA_MONEDA FROM DUAL";
			$result = $db->build_results($sql);		
			$K_CASTIGO_FECHA_MONEDA = $result[0]['CASTIGO_FECHA_MONEDA'];
			if($K_CASTIGO_FECHA_MONEDA == 'VENCE_3'){
				$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
				$sql = "SELECT f_mon_get_valor_moneda($cod_moneda, Add_Months(To_Date('$fecha_actual','dd/mm/yyyy'), 3)) VALOR_UTM_MENSUAL FROM dual";
				$result_utm = $db->build_results($sql);
				$K_VALOR_UTM_MENSUAL = $result_utm[0]['VALOR_UTM_MENSUAL'];

				$this->dws['dw_comprobante_castigo']->set_item(0, 'VALOR_UTM_MENSUAL', $K_VALOR_UTM_MENSUAL);
			}else if($K_CASTIGO_FECHA_MONEDA == 'ACTUAL'){
				$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
				$sql = "SELECT f_mon_get_valor_moneda($cod_moneda, SYSDATE) VALOR_UTM_MENSUAL FROM DUAL";
				$result = $db->build_results($sql);		
				$K_VALOR_UTM_MENSUAL = $result[0]['VALOR_UTM_MENSUAL'];
				$this->dws['dw_comprobante_castigo']->set_item(0, 'VALOR_UTM_MENSUAL', $K_VALOR_UTM_MENSUAL);
			}else{
				$this->dws['dw_comprobante_castigo']->set_item(0, 'VALOR_UTM_MENSUAL', $K_VALOR_UTM_MENSUAL);
			}
			
			if($K_VALOR_UTM_MENSUAL != ''){
				$this->dws['dw_comprobante_castigo']->set_item(0, 'LABEL_UTM', 'Valor UTM:');
			}else{
				$this->dws['dw_comprobante_castigo']->set_item(0, 'LABEL_UTM', '');
			}
		}
   		else if(K_TIPO_BD == "mssql"){
	   		//SETEANDO LA VARIBLE VALOR_UTM_MENSUAL
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$sql = "SELECT dbo.f_get_constante('K_CASTIGO_FECHA_MONEDA') CASTIGO_FECHA_MONEDA";
			$result = $db->build_results($sql);		
			$K_CASTIGO_FECHA_MONEDA = $result[0]['CASTIGO_FECHA_MONEDA'];
			if($K_CASTIGO_FECHA_MONEDA == 'VENCE_3'){
				$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
				$sql = "SELECT dbo.f_mon_get_valor_moneda($cod_moneda, DATEADD(month, 3, '$fecha_actual')) VALOR_UTM_MENSUAL";
				$result = $db->build_results($sql);		
				$K_VALOR_UTM_MENSUAL = $result[0]['VALOR_UTM_MENSUAL'];
				$this->dws['dw_comprobante_castigo']->set_item(0, 'VALOR_UTM_MENSUAL', $K_VALOR_UTM_MENSUAL);
			}else if($K_CASTIGO_FECHA_MONEDA == 'ACTUAL'){
				$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
				$sql = "SELECT dbo.f_mon_get_valor_moneda($cod_moneda, GETDATE()) VALOR_UTM_MENSUAL";
				$result = $db->build_results($sql);		
				$K_VALOR_UTM_MENSUAL = $result[0]['VALOR_UTM_MENSUAL'];
				$this->dws['dw_comprobante_castigo']->set_item(0, 'VALOR_UTM_MENSUAL', $K_VALOR_UTM_MENSUAL);
			}else{
				$this->dws['dw_comprobante_castigo']->set_item(0, 'VALOR_UTM_MENSUAL', $K_VALOR_UTM_MENSUAL);
			}
			
			if($K_VALOR_UTM_MENSUAL != ''){
				$this->dws['dw_comprobante_castigo']->set_item(0, 'LABEL_UTM', 'Valor UTM:');
			}else{
				$this->dws['dw_comprobante_castigo']->set_item(0, 'LABEL_UTM', '');
			}
   		}
	}

	function redraw_dws(&$temp) {
		parent::redraw_dws($temp);
		$this->dws['dw_comprobante_castigo']->habilitar($temp, true);
		$this->dws['dw_lista_tipo_deuda']->habilitar($temp, true);
		$this->dws['dw_tipo_deuda_cs']->habilitar($temp, true);
	}
	/*
	function make_cmd_sp($cod_ejecucion_proceso) {
		$this->dws['dw_comprobante_castigo']->get_values_from_POST();
		$fecha_vencto= $this->dws['dw_comprobante_castigo']->get_item(0, 'FECHA_VENCTO');
		$fecha_vencto = $this->str2date($fecha_vencto);
		if(K_TIPO_BD == "oci")
			return "spp_comprobante_castigo($cod_ejecucion_proceso, $fecha_vencto)";
		else if(K_TIPO_BD == "mssql")
			return "spp_comprobante_castigo $cod_ejecucion_proceso, $fecha_vencto";
	}*/
	function inicia_proceso(){
	    $cod_usuario= $this->cod_usuario;
	    $cod_proceso = 17;
	    
	    $this->dws['dw_comprobante_castigo']->entrable = true;
	    $this->dws['dw_comprobante_castigo']->get_values_from_POST();
	    $fecha_vencto= $this->dws['dw_comprobante_castigo']->get_item(0, 'FECHA_VENCTO');
	    $fecha_vencto = $this->str2date($fecha_vencto);
	    
	    $param = "'NEW'
			,null
			,$cod_proceso
			,$cod_usuario
			,null
			,null";
	    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	    $sp="spu_ejecucion_proceso";
	    if ($db->EXECUTE_SP($sp, $param)){
	        $COD_EJECUCION_PROCESO =$db->GET_IDENTITY('ejecucion_proceso');
	    }
	    else{
	        $COD_EJECUCION_PROCESO = "null";
	    }
	    
	    $this->redraw();
	    ob_start();
	    for ($i=1; $i<=3; $i++) {
	        print str_pad('',4096); //para que funcione en todos los navegadores
	        ob_end_flush();
	        ob_flush();
	        flush();
	        ob_start();
	        Sleep(1);
	        print "<script>document.getElementById('img_loader').style.display = '';</script>";
	    }
	    ob_end_flush();
	    
	    $error = false;
	    $conn = oci_connect(K_USER, K_PASS, K_SERVER);
	    $stid = oci_parse($conn, "begin spp_comprobante_castigo ($COD_EJECUCION_PROCESO, $fecha_vencto); end;");
	    if (oci_execute($stid)){
	        print "<script>document.getElementById('img_loader').style.display = 'none';</script>";
	        print "<script>document.getElementById('fin_proceso').style.display = '';</script>";
	        oci_free_statement($stid);
	        oci_close($conn);
	    }
	    else{
	        print "<script>document.getElementById('img_loader').style.display = 'none';</script>";
	        print "<script>document.getElementById('fin_proceso').style.display = 'none';</script>";
	        $error = true;
	        ob_end_flush();
	        $e = oci_error($stid);
	        $error_code= $e['code'];
	        $error_sqltext= $e['sqltext'];
	        
	        $this->redraw();
	        $this->alert('Error Oracle texto sql ejecutado: '.$error_sqltext);
	        
	        oci_free_statement($stid);
	        oci_close($conn);
	    }
	    
	    $sql = "select to_char(FECHA_EJECUCION_PROCESO, 'dd/mm/yyyy hh24:mi:ss') LAST_FECHA_EJECUCION_PROCESO
						,to_char(FECHA_TERMINO, 'dd/mm/yyyy hh24:mm:ss') LAST_FECHA_TERMINO
						,Trim(To_Char(trunc(mod((FECHA_TERMINO - FECHA_EJECUCION_PROCESO)*24, 24)), '9900')) || ':' ||
 						 Trim(To_Char(trunc( mod( (FECHA_TERMINO - FECHA_EJECUCION_PROCESO)*24*60, 60)), '00')) || ':'  ||
 						 Trim(To_Char(trunc( mod( (FECHA_TERMINO - FECHA_EJECUCION_PROCESO)*24*60*60, 60)), '00')) LAST_DURACION
						,CANCELA_PROCESO LAST_CANCELA_PROCESO
						,U.NOM_USUARIO LAST_NOM_USUARIO
						,decode(CANCELA_PROCESO, 'N', '', '    (Proceso cancelado por : ' || U2.NOM_USUARIO || ')') LAST_NOM_USUARIO_CANCELA
						,TOT_REGISTRO LAST_TOT_REGISTRO
						,ACTUAL_REGISTRO LAST_ACTUAL_REGISTRO
						,Decode(NVL(To_Char(FECHA_TERMINO, 'yyyy'), 'none'), 'none', 'none', '')  LAST_DISPLAY_EJECUCION
						,f_ep_cant_error(E.COD_EJECUCION_PROCESO, 'E') LAST_CANT_ERROR
						,f_ep_cant_error(E.COD_EJECUCION_PROCESO, 'W') LAST_CANT_WARNING
						,CASE WHEN (TOT_REGISTRO  = 0) THEN 100
				             WHEN (ACTUAL_REGISTRO >= TOT_REGISTRO) THEN 100
				             ELSE (ACTUAL_REGISTRO / TOT_REGISTRO) * 100
				         END ANTERIOR_AVANCE
				        ,E.ARCHIVO_RESULTADO
				from EJECUCION_PROCESO E, USUARIO U, USUARIO U2
				where E.COD_EJECUCION_PROCESO =$COD_EJECUCION_PROCESO
				  and U.COD_USUARIO = E.COD_USUARIO
				  and U2.COD_USUARIO (+)= E.COD_USUARIO_CANCELA";
	    
	    $result = $db->build_results($sql);
	    
	    $last_fecha_ejecucion_proceso = $result[0]['LAST_FECHA_EJECUCION_PROCESO'];
	    $last_fecha_termino = $result[0]['LAST_FECHA_TERMINO'];
	    $last_duracion = $result[0]['LAST_DURACION'];
	    $last_cancela_proceso = $result[0]['LAST_CANCELA_PROCESO'];
	    $last_nom_usuario = $result[0]['LAST_NOM_USUARIO'];
	    $last_nom_usuario_cancela = $result[0]['LAST_NOM_USUARIO_CANCELA'];
	    $last_tot_registro = $result[0]['LAST_TOT_REGISTRO'];
	    $last_actual_registro = $result[0]['LAST_ACTUAL_REGISTRO'];
	    $last_cant_error = $result[0]['LAST_CANT_ERROR'];
	    $last_cant_warning = $result[0]['LAST_CANT_WARNING'];
	    
	    print "<script>document.getElementById('LAST_FECHA_EJECUCION_PROCESO_0').innerHTML ='$last_fecha_ejecucion_proceso';</script>";
	    print "<script>document.getElementById('LAST_DURACION_0').innerHTML ='$last_duracion';</script>";
	    print "<script>document.getElementById('LAST_NOM_USUARIO_0').innerHTML ='$last_nom_usuario';</script>";
	    print "<script>document.getElementById('LAST_ACTUAL_REGISTRO_0').innerHTML ='$last_actual_registro';</script>";
	    print "<script>document.getElementById('LAST_CANT_ERROR_0').innerHTML ='$last_cant_error';</script>";
	    print "<script>document.getElementById('LAST_CANT_WARNING_0').innerHTML ='$last_cant_warning';</script>";
	}
	
	function procesa_event() {
	    if(isset($_POST['b_procesar'])){
	        $this->inicia_proceso();
	    }
	    else
	        parent::procesa_event();
	}	
}
?>