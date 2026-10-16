<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

ini_set('max_execution_time', 900); //900 seconds = 15 minutes

class wp_carga_matricula extends w_proceso{
	const K_CARGA_MATRICULA = 2;
	var $archivo_cargado = false;
	var $fecha_carga = '';
	 
   	function __construct(){
   	    parent::__construct('carga_matricula', $_REQUEST['cod_item_menu'], self::K_CARGA_MATRICULA);
   	}
   	/*
	function make_cmd_sp($cod_ejecucion_proceso) {
		if (K_TIPO_BD=="oci")
			$cmd = "spp_cm_carga_matricula($cod_ejecucion_proceso, $this->fecha_carga)";
		else if (K_TIPO_BD=="mssql")	
			$cmd = "spp_cm_carga_matricula $cod_ejecucion_proceso, $this->fecha_carga";
		
		return $cmd;
	}*/
		
	function redraw_dws(&$temp) {
		parent::redraw_dws($temp);
		if ($this->archivo_cargado)
			$temp->setVar("ARCHIVO_CARGADO", "                    Archivo cargado");
		else
			$temp->setVar("ARCHIVO_CARGADO", "");		
	}
	function cargar(){
		$columnas = array(	'COD_CARRERA' 		=> array('TIPO' => 'NUMBER',	'FORMATO' => '10',			'NULL' => 'S')
							,'RUT' 				=> array('TIPO' => 'NUMBER',   	'FORMATO' => '10',			'NULL' => 'N')
							,'NOM_CARRERA' 	   	=> array('TIPO' => 'TEXT', 		'FORMATO' => '',			'NULL' => 'N')
						 	,'ANO_MATRICULA'	=> array('TIPO' => 'NUMBER',   	'FORMATO' => '10',			'NULL' => 'N')
						 	,'SEMESTRE'			=> array('TIPO' => 'NUMBER',   	'FORMATO' => '1',			'NULL' => 'N')
						 	);
						 	
/*
OJO: 
Se debe aumentar el tamaño permitido para cargar archivos en el php.ini
ya que el definido por defecto es de 2M y muestra warning al querer cargar archivos de mayor tamaño
La línea que se debe modificar es:

ejemplo:
upload_max_filesize = 16M
 
 * */						 	
	 	$archivo = new archivo_datos('FILE_NAME', "archivos/", $columnas);	
		$u = $archivo->upLoadFile();
		
		$sp = 'spu_carga_matricula';
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$this->fecha_carga = $this->str2date($db->current_date(), $db->current_time());
		$db->BEGIN_TRANSACTION();
		
		$error = false;
		// procesa el archivo
		$archivo->open();
		$i = 1;
		while (!$archivo->feof()){
			if (count($archivo->error) == $archivo->K_MAX_ERROR) {
				$this->alert('Demasiados errores, no se seguira cargando el archivo.');
				break;
			}
	    	$record = $archivo->read_record($i);
	    	if (is_array($record)) {	// Si es array no existe error
                $cod_carrera	= $record['COD_CARRERA'];
                $rut      		= $record['RUT'];
                $nom_carrera    = $record['NOM_CARRERA'];
                $ano_matricula  = $record['ANO_MATRICULA'];
                $semestre  		= $record['SEMESTRE'];
                
				$cod_carrera = ($cod_carrera == '') ? "null" : $cod_carrera;
        		$semestre = ($semestre == '') ? "null" : $semestre;               	
	
				$param = "'INSERT'
						,$cod_carrera
						,$rut
						,'$nom_carrera'
                		,$ano_matricula
                		,$semestre
		                ,'N'
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
		$param_carga_archivo	= self::K_CARGA_MATRICULA."
				,'$nom_archivo_cargado'";

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
		$this->redraw();
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
	    $cod_proceso = 2;
	    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	    $this->fecha_carga = $this->str2date($db->current_date(), $db->current_time());
	    
	    
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
	    $stid = oci_parse($conn, "begin spp_cm_carga_matricula ($COD_EJECUCION_PROCESO, $this->fecha_carga); end;");
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
	
	function procesa_event(){
	    
	    if(isset($_POST['b_cargar']))
	        $this->cargar();
	        else if(isset($_POST['b_procesar'])){
	            $this->inicia_proceso();
	        }
	        else
	            parent::procesa_event();
	}
}
?>