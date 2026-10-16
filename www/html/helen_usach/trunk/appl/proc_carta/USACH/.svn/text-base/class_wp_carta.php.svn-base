<?php
//////////////////////////////////////////////////////
////////////////////// USACH   ////////////////////
//////////////////////////////////////////////////////
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

class wp_carta extends wp_carta_base {
    function __construct() {
   		$sql = "select 	null COD_CARTA
   						,To_Number(to_char(sysdate,'yyyy')) ANO
   						,'none' VISIBLE_CAMPO
    			from 	DUAL";
 		$this->dws['dw_carta'] = new datawindow($sql); 
    	
 		$this->dws['dw_carta']->add_control(new edit_num('ANO', 4, 4));
 		
 		$sql = "select COD_CARTA
    					,NOM_CARTA
    			from CARTA";
    	$this->dws['dw_carta']->add_control($control = new drop_down_dw('COD_CARTA', $sql));
    	$control->set_onChange("visible_year();");
    	parent::__construct('carta', $_REQUEST['cod_item_menu'], self::K_CARTA, 'php');
   	}
	function redraw() {
		$this->nom_template = "USACH/wp_carta.htm";
		parent::redraw();
	}
   	function redraw_dws(&$temp) {
		$cod_carta = $this->dws['dw_carta']->get_item(0, 'COD_CARTA');
		if($cod_carta == 6 || $cod_carta == 7)
			$this->dws['dw_carta']->set_item(0, 'VISIBLE_CAMPO', '');
   		parent::redraw_dws($temp);
   		/*
		parent::redraw_dws($temp);
		$cod_carta = $this->dws['dw_carta']->get_item(0, 'COD_CARTA');
		if($cod_carta == 6 || $cod_carta == 7)
			$this->dws['dw_carta']->set_item(0, 'VISIBLE_CAMPO', '');
		
		$this->dws['dw_carta']->habilitar($temp, true);
*/
	}
	function make_cmd_php($cod_ejecucion_proceso) {
		$this->dws['dw_carta']->get_values_from_POST();
		$cod_carta = $this->dws['dw_carta']->get_item(0, 'COD_CARTA');
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$ano = $db->current_year();
		
		// Debe retornar el php y sus parametros para ser ejecutado
		return $this->root_url."appl/proc_carta/".K_CLIENTE."/carta.php?cod_ejecucion_proceso=$cod_ejecucion_proceso&cod_carta=$cod_carta&cod_usuario=$this->cod_usuario&ano=$ano";
	}
	
	function inicia_proceso(){
	    $cod_usuario= $this->cod_usuario;
	    $cod_proceso = 12;
	    
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
	    $proceso = $this->make_cmd_php($COD_EJECUCION_PROCESO);
	    
	    // 	Ejecuta en una nueva ventana
	    print " <script>
                            alert('".$proceso."')
							popup_window = window.open('".$proceso."')
							popup_window.close ();
					</script>";
	    /*
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
	    $stid = oci_parse($conn, "begin spp_cartera_conocida ($COD_EJECUCION_PROCESO, $cod_usuario , sysdate); end;");
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
	    }*/
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