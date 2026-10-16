<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wp_comprobante_traspaso_cuota extends w_proceso {
	const K_COMPROBANTE_TRASPASO_CUOTA 	= 6;
	const K_ESTADO_COMP_PENDIENTE		= 1;
	const K_ESTADO_COMP_APROBADO		= 2;

	function __construct() {
	    parent::__construct('comprobante_traspaso_cuota', $_REQUEST['cod_item_menu'], self::K_COMPROBANTE_TRASPASO_CUOTA);
   	}
	function make_cmd_sp($cod_ejecucion_proceso) {
		if (K_TIPO_BD=="oci")
			return "spp_ctc_traspaso_cuota_vigente($cod_ejecucion_proceso)";
		else if (K_TIPO_BD=="mssql")	
			
			return "spp_ctc_traspaso_cuota_vigente $cod_ejecucion_proceso";
	}
	
	function inicia_proceso(){
	    $cod_usuario= $this->cod_usuario;
	    $cod_proceso = 6;
	    
	    $param = "'NEW'
				,null
				,$cod_proceso
				,$cod_usuario
				,null
				,null";
	    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	    $sp="spu_ejecucion_proceso";
	    if ($db->EXECUTE_SP($sp, $param)){
	        $cod_ejecucion_proceso =$db->GET_IDENTITY('ejecucion_proceso');
	    }
	    else{
	        $cod_ejecucion_proceso = "null";
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
	    $stid = oci_parse($conn, "begin spp_ctc_traspaso_cuota_vigente ($cod_ejecucion_proceso); end;");
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