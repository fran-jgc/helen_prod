<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wp_condonacion_cs extends w_proceso {
	const K_CONDONACION_CS = 13;
	 
   	function __construct(){
   		$sql = "SELECT	to_char(sysdate,'dd/mm/yyyy') FECHA_INICIO_COBRO FROM 	DUAL";
		$this->dws['dw_condonacion_cs'] = new datawindow($sql);
    	parent::__construct('condonacion_cs', $_REQUEST['cod_item_menu'], self::K_CONDONACION_CS);
   	}
	
	function load_dws() {
		parent::load_dws();
		$this->dws['dw_condonacion_cs']->retrieve($this->cod_proceso);
	}
	
	function redraw_dws(&$temp) {
		parent::redraw_dws($temp);
		$this->dws['dw_condonacion_cs']->habilitar($temp, true);
	}
	
	function inicia_proceso(){
	    $cod_usuario= $this->cod_usuario;
	    $cod_proceso = 13;
	    
	    $this->dws['dw_condonacion_cs']->entrable = true;
	    $this->dws['dw_condonacion_cs']->get_values_from_POST();

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
	    $stid = oci_parse($conn, "begin spp_condonacion_cs ($COD_EJECUCION_PROCESO); end;");
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