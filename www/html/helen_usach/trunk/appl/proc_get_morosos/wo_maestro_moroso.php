<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/**************
 Clase : W_CAMBIO_PASSWORD
 **************/
/*SC Se agrega la siguiente linea para aumentar la memoria limite de php*/
ini_set('memory_limit', '800M');
//ini_set('max_execution_time', 900); //900 seconds = 15 minutes

class wo_maestro_moroso extends w_base {
    
	CONST K_RUT_UNIVERSIDAD 	= 4;
	CONST K_NOM_UNIVERSIDAD 	= 5;
	CONST K_NOM_COORDINADOR 	= 6;
	CONST K_FONO_COORDINADOR 	= 7;
	CONST K_PROC_GET_MOROSOS 	= 2;
	CONST K_ESTADO_INICIO	 	= 1;


	function __construct() {
	    parent::__construct('', '2575');
		$this->work_directory = getcwd();
	}
	function import_txt($fecha_buscada) {
		$t = new Template_appl($this->root_dir."/appl/proc_get_morosos/maestro_moroso.htm");
		$fecha_buscada = $this->current_date();
		$t->setVar('W_FECHA', $fecha_buscada);
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql= "select to_char(sysdate, 'yyyy') ANIO_ACTUAL  FROM dual";
		$result = $db->build_results($sql);
		$t->setVar('W_ANIO_ACTUAL', $result[0]['ANIO_ACTUAL']);

		$t->setVar("WO_RUTA_MENU", $this->ruta_menu);
		$t->setVar("WO_FECHA_ACTUAL", 'Fecha Actual: '.$this->current_date());
		$t->setVar("WO_NOM_USUARIO", $this->nom_usuario);

		$sql= "SELECT COD_GENERA_INFORME
					,TO_CHAR(FECHA_GENERA_INFORME, 'dd/mm/yyyy hh24:mi:ss') as FECHA_GENERA_INFORME
					,NOM_ESTADO_INFORME
					,RUTA_ARCHIVO
					,NOM_USUARIO
				FROM GENERA_INFORME GI
					,ESTADO_INFORME EI
					,USUARIO U
				WHERE GI.COD_ESTADO_INFORME = EI.COD_ESTADO_INFORME
				AND U.COD_USUARIO=GI.COD_USUARIO_GENERA_INFORME
				AND GI.COD_TIPO_INFORME = ".self::K_PROC_GET_MOROSOS."
				AND EXTRACT(YEAR FROM FECHA_GENERA_INFORME) = EXTRACT(YEAR FROM SYSDATE)
				AND ROWNUM <= 5
				ORDER BY COD_GENERA_INFORME DESC";
		
		$result = $db->build_results($sql);
		$tabla='';
		$abilita_boton=true;
		$boton_ejecucion = '';
		for($i = 0; $i < count($result); $i++){

			$cod_genera_informe	= $result[$i]['COD_GENERA_INFORME'];
			$fecha_genera_informe	= $result[$i]['FECHA_GENERA_INFORME'];
			$nom_estado_informe	= $result[$i]['NOM_ESTADO_INFORME'];
			$nom_usuario	= $result[$i]['NOM_USUARIO'];

			if ($i % 2 == 0){
				$tabla.='<tr class="oscuro" >';
			}
			else{
				$tabla.='<tr class="claro" >';
			}
				$tabla .= '<td width="85px" align="center">'.$cod_genera_informe.'</td>';
				$tabla .= '<td width="100px" align="center" >'.$fecha_genera_informe.'</td>';
				$tabla .= '<td width="100px" align="center">'.$nom_estado_informe.'</td>';
				$tabla .= '<td width="100px" align="center">'.$nom_usuario.'</td>';
				$tabla .= '<td width="100px" align="center">';

				if (($nom_estado_informe == 'TERMINADO' || $nom_estado_informe == 'INFORMADO') && $i == 0 ){ 
					$tabla .= '<a href="wo_maestro_moroso.php?descarga_archivo='.$cod_genera_informe.'">';
        			$tabla .= '<button type="button">Descargar</button> </a></td>';
				}
				if ($nom_estado_informe == 'INICIADO' || $nom_estado_informe == 'PROCESANDO' ){
					$abilita_boton = false;
				}
				
				$tabla .= '</tr>';
		}
		if ($abilita_boton) {
			$boton_ejecucion = '<input type="submit" onClick="if(valida_fecha() == false) return false;" name="b_crontab" id="b_crontab" value="Generar Archivo" class="button" />';
		}else{
			$boton_ejecucion = '<label style="margin-right: 80px;">Informe en proceso..</label>';
		}
		$t->setVar("BOTON_EJECUCION", $boton_ejecucion);
		$t->setVar("TABLA", $tabla);
		$menu = unserialize(session::get('menu_appl'));
		$menu->draw($t,$db);

		print $t->toString();
	}

	function print_txt($fecha){
		//$fecha = $_POST['fecha'];
		 
		$arrFecha = explode('/', $fecha);
		$ano = $arrFecha[2]; 
		$archivo = "".K_CLIENTE."_morosos_".$ano.".TXT";
		$ruta_dcto = "documentos/proc_get_morosos/".$archivo;
		$linea = "";
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		// se crea el archivo en documentos/
		$file = fopen($ruta_dcto,"w");
		// si se puede escribir en el archivo
		if (is_writable($ruta_dcto)) {
			if (!$handle = fopen($ruta_dcto, 'a')) {
				return "ERROR : No se puede Abrir el Archivo: $archivo";
			}			
				
			///////////////// CLASS_SQL = OUTPUT  ///////////////
			if (K_TIPO_BD == "mssql"){
				$fecha = $this->str2date($fecha);
				$sql= "exec spi_get_morosos $fecha";
				$result = $db->build_results($sql);
			}
			else if (K_TIPO_BD == "oci"){
				//$fecha = str_replace("/","-",$fecha);
				//$fecha = $this->str2date($fecha);
				$fecha = $this->str2date($fecha);
				$db->EXECUTE_SP("spi_get_morosos","$fecha");
				IF(K_CLIENTE == "UCM"){
						$sql = "SELECT	rut 
										,dig_verif  
										,paterno 
										,materno 
										,nombre 
										,REPLACE(monto_moroso_utm, '.',',')  monto_moroso_utm
										
								FROM	TEMP_MOROSO
								ORDER BY RUT";	
				}else{
						$sql = "SELECT	rut 
										,dig_verif  
										,paterno 
										,materno 
										,nombre 
										,REPLACE(monto_moroso_utm, '.',',')  monto_moroso_utm
								FROM	TEMP_MOROSO
								ORDER BY RUT";
				}
				
				$result = $db->build_results($sql);				
			}
			for($i = 0; $i < count($result); $i++){
				$rut 		= $result[$i][0];
				$dig_verif  = $result[$i][1];
				$apellido_p = $result[$i][2];
				$apellido_m = $result[$i][3];
				$nombre	    = $result[$i][4];
				$utm	    = $result[$i][5];
				
				
				IF(K_CLIENTE == "UCM"){
					$direccion = $result[$i][6];
					$comuna = $result[$i][7];
					$ciudad = $result[$i][8];
					
					$linea 	= $rut."	".$dig_verif."	".$apellido_p."	".$apellido_m."	".$nombre."	".$direccion."	".$comuna."	".$ciudad."	".$utm;
				}
				else
				{
					$linea 	= $rut."	".$dig_verif."	".$apellido_p."	".$apellido_m."	".$nombre."	".$utm;	
				}

				
				if($i < count($result)){
					if($i == (count($result)-1))
					{
						$linea = $linea;	
					}else{
						$linea = $linea."\r\n";
					}
				}
				if (fwrite($handle, $linea) === FALSE) {
					return "ERROR : No se puede escribir en el Archivo: $archivo";
				}
			}
		}
		else{
			return "ERROR : El Archivo: $archivo no puede ser escrito.";

		}
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=\"$archivo\"\n");
		//$this->alert(file_get_contents($ruta_dcto));
		// file_get_contents($ruta_dcto);
		return $ruta_dcto;
		// SE BORRA EL ARCHIVO DEL SERVIDOR
		//unlink('documentos/'.$archivo);
	}
	function crontab(){
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$fecha = $_POST['fecha'];
		$fecha=str_replace("/","-",$fecha);
		//$fecha = $this->str2date($fecha);

		$parametros = array('fecha' => $fecha);
		
		$operacion = 'INSERT';
		$cod_tipo_informe  = self::K_PROC_GET_MOROSOS;
		$param_genera_informe = json_encode($parametros);
		$cod_usuario_genera_informe= $this->cod_usuario;
		$cod_estado_informe = self::K_ESTADO_INICIO ; 

		$sp= "spi_genera_informe_crontab";

		 $param = "'$operacion',
					NULL,
					$cod_tipo_informe ,
					'$param_genera_informe',
					$cod_usuario_genera_informe ,
					$cod_estado_informe,
					NULL,
					NULL ";

		if (!$db->EXECUTE_SP($sp,$param)){
			return false;
		}
		$this->alert('El informe esta siendo procesado , cuando finalice se le informara vía email ');
		$w = new wo_maestro_moroso();
		$w->import_txt('');
	}
	function descraga_archivo($cod_genera_informe){
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql= "SELECT RUTA_ARCHIVO 
				FROM GENERA_INFORME 
				WHERE COD_GENERA_INFORME = $cod_genera_informe";
		$result = $db->build_results($sql);
		$ruta_archivo = $result[0]['RUTA_ARCHIVO'];

		if (file_exists($ruta_archivo)) {
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . basename($ruta_archivo) . '"');
			header('Content-Length: ' . filesize($ruta_archivo));
			header('Pragma: no-cache');
			header('Expires: 0');
			// Leer y enviar el archivo
			readfile($ruta_archivo);
			exit;
		} else {
			$this->alert('El archivo no existe.');
			
			$w = new wo_maestro_moroso();
			$w->import_txt('');
		}

	}
		
}

$w = new wo_maestro_moroso();
if (isset($_POST['b_generar'])){
	$w->print_txt($fecha);
}
elseif(isset($_POST['b_crontab'])){
	$w->crontab();
}
elseif(isset($_POST['b_cancelar'])){
	$w->presentacion();
}
elseif(isset($_GET['cod_item_menu'])){
	$w->import_txt('');
}elseif(isset($_GET['descarga_archivo'])){
	$w->descraga_archivo($_GET['descarga_archivo']);
}else{
	//$w->print_txt();
}
?>