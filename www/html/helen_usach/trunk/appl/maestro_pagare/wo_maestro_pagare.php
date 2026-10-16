<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/**************
Clase : W_CAMBIO_PASSWORD
**************/
class wo_maestro_pagare extends w_base {
	CONST K_RUT_UNIVERSIDAD 	= 4;
	CONST K_NOM_UNIVERSIDAD 	= 5;	
	CONST K_NOM_COORDINADOR 	= 6;
	CONST K_FONO_COORDINADOR 	= 7;
	
	function __construct() {
	    parent::__construct('', '3005');
		$this->work_directory = getcwd();
	}
	function import_txt($ano_buscado) {
		$t = new Template_appl($this->root_dir."/appl/maestro_pagare/maestro_pagare.htm");
		$t->setVar('W_ANO', $ano_buscado);
		
		$t->setVar("WO_RUTA_MENU", $this->ruta_menu);
		$t->setVar("WO_FECHA_ACTUAL", 'Fecha Actual: '.$this->current_date());
		$t->setVar("WO_NOM_USUARIO", $this->nom_usuario);
		
		$menu = unserialize(session::get('menu_appl'));
		$menu->draw($t);
		
		print $t->toString();
	}
	
	function print_txt(){
		$ano_buscado = $_POST['ano'];	
		$archivo = "pagare_".$ano_buscado.".TXT";
		$ruta_dcto = "documentos/".$archivo;		
		$linea = "";
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		// se crea el archivo en documentos/ 
		$file = fopen($ruta_dcto,"w");
		// si se puede escribir en el archivo
		if (is_writable($ruta_dcto)) {	    
		    if (!$handle = fopen($ruta_dcto, 'a')) {
		         print "No se puede Abrir el Archivo: $archivo";
		         exit;
		    }    		
		    // ENCABEZADO DEL ARCHIVO LN 1
		    $fecha = str_pad(date('Ymj'),135);		    
		    $nom_archivo = str_pad($archivo, 20);
		    
		    $result_param = $db->build_results(sql_maestro_pagare::get_sql('RUT_UNIVERSIDAD'));	
		    $rut_universidad = $result_param[0]['RUT_UNIVERSIDAD'];
			$rut_universidad = str_replace(".", "", $rut_universidad);
			$rut_universidad = str_replace("-", "", $rut_universidad);
		    $rut_universidad = str_pad($rut_universidad,10,"0",STR_PAD_LEFT);
		    		    
		    $result_param = $db->build_results(sql_maestro_pagare::get_sql('NOM_UNIVERSIDAD'));		    
		    $nom_universidad = substr(str_pad($result_param[0]['NOM_UNIVERSIDAD'],30),0,30);
		    // se crea la linea encabezado
		    $linea_encabezado = "1";
		    $linea_encabezado = $linea_encabezado.$nom_archivo;
		    $linea_encabezado = $linea_encabezado.$rut_universidad.$nom_universidad.$fecha."1\r\n";		    
			
			if (fwrite($handle, $linea_encabezado) === FALSE) {
				        print "No se puede escribir en el Archivo: $archivo";
				        exit;
			}
			
			//////////////// CLASS_SQL = OUTPUT ///////////////
			$sql = sql_maestro_pagare::get_sql('OUTPUT', $ano_buscado);
					                		
			$res = $db->query($sql);
			$monto_total_pagare = 0;
			$lineas_procesadas = 0;
			while($result = $db->get_row()){
				$rut 		= $result['RUT'].$result['DIG_VERIF'];
				$rut		= str_pad($rut,10,"0",STR_PAD_LEFT);
				$apellido_p = substr(str_pad($result['APELLIDO_PATERNO'], 20),0,20);
				$apellido_m = substr(str_pad($result['APELLIDO_MATERNO'], 20),0,20);									
				$nombre		= str_pad(substr($result['NOMBRE'],0,20),20);
				$direccion	= substr(str_pad($result['DIRECCION'], 40),0,40);
				$comuna		= substr(str_pad($result['NOM_COMUNA'], 20),0,20);
				$ciudad		= substr(str_pad($result['NOM_CIUDAD'], 20),0,20);
				$telefono	= substr(str_pad($result['TELEFONO'], 15),0,15);
				$cod_carrera=  str_pad($result['COD_CARRERA'], 4, "0");
				$folio_pagare= str_pad($result['FOLIO_PAGARE'], 10,"0",STR_PAD_LEFT);				 
				$folio_pagare = substr($folio_pagare,0,10);				 
				$monto_pagare= number_format($result['MONTO_MONEDA'], 4, '', '');
				$monto_pagare= str_pad($monto_pagare, 7," ",STR_PAD_LEFT);
				$fecha_pagare= str_pad($result['FECHA_PAGARE'], 8);
				
				$monto_total_pagare += $monto_pagare; 
				
				$linea 	= "2".$rut;				
				$linea 	= $linea.$apellido_p;
				$linea 	= $linea.$apellido_m;
				$linea 	= $linea.$nombre;
				$linea 	= $linea.$direccion;
				$linea 	= $linea.$comuna;
				$linea 	= $linea.$ciudad;
				$linea 	= $linea.$telefono;
				$linea 	= $linea.$cod_carrera;
				$linea 	= $linea.$folio_pagare;
				$linea 	= $linea.$monto_pagare;
				$linea 	= $linea.$fecha_pagare." 2";			
				
				//if($i < count($result))
				$linea = $linea."\r\n";
				$lineas_procesadas++;
				
			    if (fwrite($handle, $linea) === FALSE) {
			        print "No se puede escribir en el Archivo: $archivo";
			        exit;
			    }	
			}
			
			if($db->database_type=="oci") {
				oci_free_statement($db->query_id);
				$db->query_id = false;
			}
			
			$monto_total_pagare = substr(str_pad($monto_total_pagare,10,"0", STR_PAD_LEFT),0,10);
			$result_param = $db->build_results(sql_maestro_pagare::get_sql('NOM_COORDINADOR'));
			$nombre_coordinador = substr(str_pad($result_param[0]['NOM_COORDINADOR'],30),0,30);
							
			$result_param = $db->build_results(sql_maestro_pagare::get_sql('FONO_COORDINADOR'));
			$fono_coordinador = str_pad($result_param[0]['FONO_COORDINADOR'],15);
				// PIE DEL ARCHIVO LN 3			
			$lineas_procesadas = str_pad($lineas_procesadas,5,"0", STR_PAD_LEFT);		
			$num_final_3 = str_pad('3',136," ", STR_PAD_LEFT);

			$linea_resultado = "3".$lineas_procesadas.$monto_total_pagare.$nombre_coordinador.$fono_coordinador.$num_final_3;
			fwrite($handle, $linea_resultado);
		    fclose($handle);
		}
		else{
		   	print "El Archivo: $archivo no puede ser escrito.";
		}
		header("Content-type: application/octet-stream");
   		header("Content-Disposition: attachment; filename=\"$archivo\"\n");    	    	
   		print file_get_contents($ruta_dcto);
    	// SE BORRA EL ARCHIVO DEL SERVIDOR
   		//unlink('documentos/'.$archivo);
	}
}

$w = new wo_maestro_pagare();
if (isset($_POST['b_generar'])){	
	$w->print_txt();
	}
elseif(isset($_POST['b_cancelar'])){
	$w->presentacion();
	}
else{
	$w->import_txt('');
}
?>