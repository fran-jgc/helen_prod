<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/**************
 Clase : W_CAMBIO_PASSWORD
 **************/
/*SC Se agrega la siguiente linea para aumentar la memoria limite de php*/
ini_set('memory_limit', '500M');

class wo_maestro_matricula extends w_base {
	CONST K_RUT_UNIVERSIDAD 	= 4;
	CONST K_NOM_UNIVERSIDAD 	= 5;
	CONST K_NOM_COORDINADOR 	= 6;
	CONST K_FONO_COORDINADOR 	= 7;

	function __construct() {
	    parent::__construct('', '3010');
		$this->work_directory = getcwd();
	}
	function import_txt($ano_buscado) {
		$t = new Template_appl($this->root_dir."/appl/maestro_matricula/maestro_matricula.htm");
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
		$archivo = "matricula_".$ano_buscado.".TXT";
		$ruta_dcto = "documentos/".$archivo;
		$linea = "";
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		// se crea el archivo en documentos/
		$file = fopen($ruta_dcto,"w");
		// si se puede escribir en el archivo
		if (is_writable($ruta_dcto)) {
			if (!$handle = fopen($ruta_dcto, 'a')) {
				$this->alert("No se puede Abrir el Archivo: $archivo");
				exit;
			}
			// ENCABEZADO DEL ARCHIVO LN 1
			$fecha = str_pad(date('Ymj'),14);
			$nom_archivo = str_pad($archivo, 20);

			$result_param = $db->build_results(sql_maestro_matricula::get_sql('RUT_UNIVERSIDAD'));
			$rut_universidad = $result_param[0]['RUT_UNIVERSIDAD'];
			$rut_universidad = str_replace(".", "", $rut_universidad);
			$rut_universidad = str_replace("-", "", $rut_universidad);
			$rut_universidad = str_pad($rut_universidad,10,"0",STR_PAD_LEFT);

			$result_param = $db->build_results(sql_maestro_matricula::get_sql('NOM_UNIVERSIDAD'));
			$nom_universidad = substr(str_pad($result_param[0]['NOM_UNIVERSIDAD'],30),0,30);
			// se crea la linea encabezado
			$linea_encabezado = "1";
			$linea_encabezado = $linea_encabezado.$nom_archivo.$ano_buscado;
			$linea_encabezado = $linea_encabezado.$rut_universidad.$nom_universidad.$fecha."1\r\n";
				
			if (fwrite($handle, $linea_encabezado) === FALSE) {
				$this->alert("No se puede escribir en el Archivo: $archivo");
				exit;
			}
				
			///////////////// CLASS_SQL = OUTPUT  ///////////////
			if (K_TIPO_BD == "mssql"){
				$sql = sql_maestro_matricula::get_sql('OUTPUT', $ano_buscado);
				$result = $db->build_results($sql);
			}
			else if (K_TIPO_BD == "oci"){
				$db->EXECUTE_SP('spi_project_matricula', "$ano_buscado");
				$sql = "SELECT	RUT
								,DIG_VERIF
				    			,APELLIDO_PATERNO
				    			,APELLIDO_MATERNO
				    			,NOMBRE
      						FROM TEMP_PROJECT_MATRICULA";
				$result = $db->build_results($sql);				
			}

			// CUERPO DEL ARCHIVO LN 2
			for($i = 0; $i < count($result); $i++){
				$cero 		= "0";
				$rut 		= $result[$i]['RUT'].$result[$i]['DIG_VERIF'];
				$rut		= str_pad($rut,10,"0",STR_PAD_LEFT);
				$apellido_p = substr(str_pad($result[$i]['APELLIDO_PATERNO'], 20),0,20);
				$apellido_m = substr(str_pad($result[$i]['APELLIDO_MATERNO'], 20),0,20);
				$nombre		= str_pad(substr($result[$i]['NOMBRE'],0,20),28);

				$linea 	= "2".$rut.$apellido_p.$apellido_m.$nombre."2";
				if($i < count($result))
				$linea = $linea."\r\n";
				if (fwrite($handle, $linea) === FALSE) {
					$this->alert("No se puede escribir en el Archivo: $archivo");
					exit;
				}
			}
				
			$result_param = $db->build_results(sql_maestro_matricula::get_sql('NOM_COORDINADOR'));
			$nombre_coordinador = substr(str_pad($result_param[0]['NOM_COORDINADOR'],30),0,30);
				
			$result_param = $db->build_results(sql_maestro_matricula::get_sql('FONO_COORDINADOR'));
			$fono_coordinador = str_pad($result_param[0]['FONO_COORDINADOR'],15);
			// PIE DEL ARCHIVO LN 3
			$lineas_procesadas = str_pad(count($result),5,"0", STR_PAD_LEFT);
			$num_final_3 = str_pad('3',24," ", STR_PAD_LEFT);

			$linea_resultado = "3".$lineas_procesadas.$lineas_procesadas.$nombre_coordinador.$fono_coordinador.$num_final_3;
			fwrite($handle, $linea_resultado);
			fclose($handle);
		}
		else{
			$this->alert("El Archivo: $archivo no puede ser escrito.");
		}
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"$archivo\"\n");
		//$this->alert(file_get_contents($ruta_dcto));
		print file_get_contents($ruta_dcto);
		
		// SE BORRA EL ARCHIVO DEL SERVIDOR
		//unlink('documentos/'.$archivo);
	}
}

$w = new wo_maestro_matricula();
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