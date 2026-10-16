<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wp_genera_plan extends w_proceso {
	const K_GENERA_PLAN = 5;
	 
	function __construct() {
		if (K_TIPO_BD=="oci")
	   		$sql = "SELECT	to_char(sysdate,'dd/mm/yyyy') FECHA_INICIO_COBRO
	    			FROM 	DUAL";
		else if (K_TIPO_BD=="mssql")	
	   		$sql = "SELECT	convert(varchar, getdate(), 103) FECHA_INICIO_COBRO";
		
		$this->dws['dw_genera_plan'] = new datawindow($sql);
   		
    	$this->dws['dw_genera_plan']->add_control(new edit_date('FECHA_INICIO_COBRO', 10, 10));
    	
    	
    	parent::__construct('genera_plan', $_REQUEST['cod_item_menu'], self::K_GENERA_PLAN);
   	}
   	
	function load_dws() {
		parent::load_dws();
		$this->dws['dw_genera_plan']->retrieve($this->cod_proceso);
	}
	
	function redraw_dws(&$temp) {
		parent::redraw_dws($temp);
		$this->dws['dw_genera_plan']->habilitar($temp, true);
	}
	
	function inicia_proceso(){
	    $cod_usuario= $this->cod_usuario;
	    $cod_proceso = 5;
	    
	    $this->dws['dw_genera_plan']->entrable = true;
	    $this->dws['dw_genera_plan']->get_values_from_POST();
	    $fecha_inicio_cobro= $this->dws['dw_genera_plan']->get_item(0, 'FECHA_INICIO_COBRO');
	    
	    $fecha_inicio_cobro = $this->str2date($fecha_inicio_cobro);
	    
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
	    $stid = oci_parse($conn, "begin spp_genera_plan ($COD_EJECUCION_PROCESO, $fecha_inicio_cobro); end;");
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