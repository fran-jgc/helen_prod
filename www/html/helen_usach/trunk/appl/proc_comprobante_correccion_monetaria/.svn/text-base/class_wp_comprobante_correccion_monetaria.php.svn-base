<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wp_comprobante_correccion_monetaria extends w_proceso {
	const K_COMPROBANTE_CORRECCION_MONETARIA	= 8;
	const K_ESTADO_COMP_PENDIENTE				= 1;
	const K_ESTADO_COMP_APROBADO				= 2;
	 
	function __construct() {
	    parent::__construct('comprobante_correccion_monetaria', $_REQUEST['cod_item_menu'], self::K_COMPROBANTE_CORRECCION_MONETARIA);
   	}
	function make_cmd_sp($cod_ejecucion_proceso) {
		if (K_TIPO_BD=="oci")
			return "spp_ccm_correcion_monetaria($cod_ejecucion_proceso)";
		else if (K_TIPO_BD=="mssql")	
			return "spp_ccm_correcion_monetaria $cod_ejecucion_proceso";
	}
	function ejecutar_proceso() {
		$sql = "SELECT  Count(*) COUNT
				FROM    comprobante
				WHERE   Upper(nom_comprobante) = 'COMPROBANTE CORRECCIÓN MONETARIA'
						AND cod_estado_comprobante IN (".self::K_ESTADO_COMP_PENDIENTE.",".self::K_ESTADO_COMP_APROBADO.")
						AND To_Char(fecha_comprobante,'yyyy') = To_Char(sysdate,'yyyy')";
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

		$result	= $db->build_results($sql);
		$COUNT	= $result[0]['COUNT'];

		if($COUNT > 0){
			$this->redraw();
			$this->alert('Existen Comprobantes en estado pendiente o aprobados para este año');
		}
		else{
			parent::ejecutar_proceso();
		}
	}
	
	function inicia_proceso(){
	    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	    
	    $sql = "SELECT  Count(*) COUNT
				FROM    comprobante
				WHERE   Upper(nom_comprobante) = 'COMPROBANTE CORRECCIÓN MONETARIA'
						AND cod_estado_comprobante IN (".self::K_ESTADO_COMP_PENDIENTE.",".self::K_ESTADO_COMP_APROBADO.")
						AND To_Char(fecha_comprobante,'yyyy') = To_Char(sysdate,'yyyy')";
	    
	    $result	= $db->build_results($sql);
	    $COUNT	= $result[0]['COUNT'];
	    
	    if($COUNT > 0){
	        $this->redraw();
	        $this->alert('Existen Comprobantes en estado pendiente o aprobados para este año');
	    }
	    
	    $cod_usuario= $this->cod_usuario;
	    $cod_proceso = 8;
	    
	    $param = "'NEW'
				,null
				,$cod_proceso
				,$cod_usuario
				,null
				,null";
	    
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
	    $stid = oci_parse($conn, "begin spp_ccm_correcion_monetaria ($COD_EJECUCION_PROCESO); end;");
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