<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class wp_obtiene_mora extends w_proceso {
	const K_OBTIENE_MORA = 4;
	 
   	function __construct() {
   		// Si estamos en un mes <= 6 sugiere el 31/12/año_anterior
   		// en caso contrario 31/05/año_actual
   		if(K_TIPO_BD == "oci"){
			$sql = "SELECT to_char(Decode(Sign(To_Number(To_Char(SYSDATE, 'mm')) - 6) 
						              , 0, To_Date('31/12/' || To_Char(To_Number(To_Char(SYSDATE, 'yyyy')) - 1), 'dd/mm/yyyy')
						              ,-1, To_Date('31/12/' || To_Char(To_Number(To_Char(SYSDATE, 'yyyy')) - 1), 'dd/mm/yyyy')
						              , 1, To_Date('31/05/' || To_Char(SYSDATE, 'yyyy'),'dd/mm/yyyy'))
						           , 'dd/mm/yyyy') FECHA_VENCTO
						       
					FROM DUAL";
   		}
   		else if(K_TIPO_BD == "mssql"){
   			$sql = "SELECT convert(varchar, case  
												when month(getdate()) <= 6 then dbo.f_makedate(31,12,year(getdate())-1)
												else dbo.f_makedate(31,05,year(getdate()))
						   					end, 103) FECHA_VENCTO";
   		}
   		$this->dws['dw_obtiene_mora'] = new datawindow($sql);
    	
    	//OBTIENE FECHA_ACTUAL
   		if(K_TIPO_BD == "oci"){
			$sql_fecha_actual = "SELECT to_char(SYSDATE, 'dd/mm/yyyy') FECHA_ACTUAL FROM DUAL";
   		}
   		else if(K_TIPO_BD == "mssql"){
   			$sql_fecha_actual = "SELECT convert(varchar(10),getdate(), 103) FECHA_ACTUAL";
   		}
   		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
   		$result = $db->build_results($sql_fecha_actual);
		$fecha_actual = $result[0]['FECHA_ACTUAL'];
    	
		$java_script = "valida_fecha(this, '".$fecha_actual."');";
		$this->dws['dw_obtiene_mora']->add_control($control = new edit_date('FECHA_VENCTO', 10, 10));
		$control->set_onChange($java_script);

		parent::__construct('obtiene_mora', $_REQUEST['cod_item_menu'], self::K_OBTIENE_MORA);
   	}
	function load_dws() {
		parent::load_dws();
		$this->dws['dw_obtiene_mora']->retrieve($this->cod_proceso);
	}
	function redraw_dws(&$temp) {
		parent::redraw_dws($temp);
		$this->dws['dw_obtiene_mora']->habilitar($temp, true);
	}
	
	function inicia_proceso(){
	    $cod_usuario= $this->cod_usuario;
	    $cod_proceso = 4;
	    
	    $this->dws['dw_obtiene_mora']->entrable = true;
	    $this->dws['dw_obtiene_mora']->get_values_from_POST();
	    $fecha_vencto= $this->dws['dw_obtiene_mora']->get_item(0, 'FECHA_VENCTO');
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
	    $stid = oci_parse($conn, "begin spp_om_obtiene_mora ($COD_EJECUCION_PROCESO, $fecha_vencto); end;");
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
	    $last_duracion = $result[0]['LAST_DURACION'];
	    $last_nom_usuario = $result[0]['LAST_NOM_USUARIO'];
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