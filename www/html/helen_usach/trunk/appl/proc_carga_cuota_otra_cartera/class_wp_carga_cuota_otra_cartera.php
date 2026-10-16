<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

ini_set('memory_limit', '750M');
ini_set('max_execution_time', 1200); //1200 seconds = 40 minutes 
ini_set('mssql.connect_timeout','400');
ini_set('mssql.timeout','400');

class wp_carga_cuota_otra_cartera extends w_proceso {
	const K_CARGA_CUOTA_OTRA_CARTERA = 20;
	var $archivo_cargado = false;
	var $fecha_carga = '';
	 
   	function __construct() {
   	    parent::__construct('carga_cuota_otra_cartera', $_REQUEST['cod_item_menu'], self::K_CARGA_CUOTA_OTRA_CARTERA);
   	}
   	
   	//se comenta por temas de reimplementacion manual
   	
	/*function make_cmd_sp($cod_ejecucion_proceso) {
		echo $cod_ejecucion_proceso;
		if (K_TIPO_BD=="oci")
			return "spp_carga_cuota_otra_cartera($cod_ejecucion_proceso)";
		else if (K_TIPO_BD=="mssql")	
			return "spp_carga_cuota_otra_cartera $cod_ejecucion_proceso";
			
	}*/
	function redraw_dws(&$temp) {
		parent::redraw_dws($temp);
		if ($this->archivo_cargado)
			$temp->setVar("ARCHIVO_CARGADO", "                    Archivo cargado");
		else
			$temp->setVar("ARCHIVO_CARGADO", "");		
	}
	function ejecutar_proceso() {
		if (!$this->archivo_cargado) {
			$this->redraw();
			$this->alert("Debe cargar el archivo antes de dar inicio al proceso.");			
		}
		else 
			parent::ejecutar_proceso();
	}
	
	function inicia_proceso(){
		$cod_usuario= $this->cod_usuario;
	    $cod_proceso = self::K_CARGA_CUOTA_OTRA_CARTERA;
	    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	    $this->fecha_carga = $this->str2date($db->current_date());
	    
	    //validar si existe carga para el dia actual.
	    //esto permite no iniciar el proceso si no hay datos
	    
	    $sql = "SELECT COUNT(*) CANT 
	    		FROM CARGA_CUOTA_OTRA_CARTERA
				WHERE To_Char(fecha_carga,'dd/mm/yyyy') =  To_Char($this->fecha_carga,'dd/mm/yyyy')";
	    
	    $result = $db->build_results($sql);
		$cant = $result[0]['CANT'];
	    
	    if($cant == 0){
		    $this->redraw();
		    print " <script>alert('No se ha cargado ningun archivo.');</script>";
		    return;
	    }
	    
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
	    $stid = oci_parse($conn, "begin spp_carga_cuota_otra_cartera ($COD_EJECUCION_PROCESO); end;");
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
	
	/******************/
	function cargar_cuota_otra_cartera(){
		$columnas = array('RUT' 				=> array('TIPO' => 'NUMBER', 'FORMATO' => '10'			,'NULL' => 'N')
						 ,'DIG_VERIF' 	   		=> array('TIPO' => 'TEXT', 	 'FORMATO' => ''			,'NULL' => 'N')
						 ,'MONTO_CAPITAL' 		=> array('TIPO' => 'NUMBER', 'FORMATO' => '14.4'		,'NULL' => 'N')
						 ,'MONTO_INTERES' 		=> array('TIPO' => 'NUMBER', 'FORMATO' => '14.4'		,'NULL' => 'N')
						 ,'FECHA_VENCTO'	 	=> array('TIPO' => 'DATE'  , 'FORMATO' => 'dd/mm/yyyy'	,'NULL' => 'N')
						 ,'COD_CONCEPTO_COBRO'	=> array('TIPO' => 'NUMBER', 'FORMATO' => '10'			,'NULL' => 'N')
						 ,'ANO_COBRO'		 	=> array('TIPO' => 'NUMBER', 'FORMATO' => '4'			,'NULL' => 'N')
						 );
						 	
		/*
		OJO: 
		Se debe aumentar el tamano permitido para cargar archivos en el php.ini
		ya que el definido por defecto es de 2M y muestra warning al querer cargar archivos de mayor tamaño
		La línea que se debe modificar es:
		
		ejemplo:
		upload_max_filesize = 16M
		 
		 * */						 	
	 	$archivo = new archivo_datos('FILE_NAME', "archivos/", $columnas);	
		$u = $archivo->upLoadFile();
				
		$sp = 'spu_carga_cuota_otra_cartera';
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$this->fecha_carga = $this->str2date($db->current_date(), $db->current_time());
		$db->BEGIN_TRANSACTION();
		$error = false;
		// procesa el archivo
		$archivo->open();
		$i = 1;

		//elimina todos los regisitros antiguos tabla CARGA_CUOTA_OTRA_CARTERA
		if (!$db->EXECUTE_SP($sp, "'BORRA_CARGA_CUOTA_OTRA_CARTERA'")){
    		$error = true;
			$db->ROLLBACK_TRANSACTION();
			$error_sp = $db->GET_ERROR();
			$this->alert('No se pudo grabar el registro.\n\n'.$db->make_msg_error_bd());
			//break;
	    }
	    
		while (!$archivo->feof()){
			if (count($archivo->error) == $archivo->K_MAX_ERROR) {
				$this->alert('Demasiados errores, no se seguira cargando el archivo.');
				break;
			}
	    	$record = $archivo->read_record($i);
	    	if (is_array($record)) {	// Si es array no existe error
                $rut = $record['RUT'];
		  		$dig_verif = $record['DIG_VERIF'];
	    		$monto_capital = $record['MONTO_CAPITAL'];
		    	$monto_interes = $record['MONTO_INTERES'];
		    	$fecha_vencto = $record['FECHA_VENCTO'];
		    	$cod_concepto_cobro = $record['COD_CONCEPTO_COBRO'];
		    	$ano_cobro = $record['ANO_COBRO'];

		    	$fecha_vencto = $this->str2date($fecha_vencto);
		    	
				$param = "'INSERT'
						,$rut
						,'$dig_verif'
						,$monto_capital
                		,$monto_interes
                		,$fecha_vencto
                		,$cod_concepto_cobro
                		,$ano_cobro
		                ,$this->fecha_carga";

	    		if (!$db->EXECUTE_SP($sp, $param)){
	    			$error = true;
					$db->ROLLBACK_TRANSACTION();
					$error_sp = $db->GET_ERROR();
					$this->alert('No se pudo grabar el registro.\n\n'.$db->make_msg_error_bd());
					break;				
	    		}
	    	}
    		$i++;
		}
		$archivo->close();
		/*BITACORA CARGA ARCHIVO*/
		$nom_archivo_cargado = $_FILES['FILE_NAME']['name'];
		$sp_carga_archivo 		= 'spu_carga_archivo';
		$param_carga_archivo	= self::K_CARGA_CUOTA_OTRA_CARTERA.",'$nom_archivo_cargado'";

		if (!$db->EXECUTE_SP($sp_carga_archivo, $param_carga_archivo)){
	    	$error = true;
			$db->ROLLBACK_TRANSACTION();
			$error_sp = $db->GET_ERROR();
			$this->alert('No se pudo grabar En la Bitacora de Archivo Carga.\n\n'.$db->make_msg_error_bd());
			//break;
	    }
		$archivo->renFile();
		
		if (!$error) {
			$db->COMMIT_TRANSACTION();
		}
		$archivo->display_error();	// Muestra los errores si existen
		
		$this->archivo_cargado = true;
		
		
	}
	/******************/
	/*function procesa_event(){
		if(isset($_POST['b_cargar']))
			$this->cargar_cuota_otra_cartera();
		else
			parent::procesa_event();
	}*/
	
	function procesa_event(){
	    
	    if(isset($_POST['b_cargar'])){
	        $this->cargar_cuota_otra_cartera();
	        $this->redraw();
	    }
	    else if(isset($_POST['b_procesar'])){
	    	$this->inicia_proceso();
		}
	    else
	    	parent::procesa_event();
	}
}
?>