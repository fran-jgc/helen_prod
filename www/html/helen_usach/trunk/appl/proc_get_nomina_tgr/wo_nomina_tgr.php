<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

ini_set('memory_limit', '800M');
ini_set('max_execution_time', 1200); //1200 seconds = 40 minutes 
//ini_set('mssql.connect_timeout','400');
//ini_set('mssql.timeout','400');
ini_set('display_errors', 'On');  

class wo_nomina_tgr extends w_base {
		CONST K_NOMINA_TGR 	= 1;
		CONST K_ESTADO_INICIO	 	= 1;
    function __construct() {
	    parent::__construct('', '2521');		
		$this->work_directory = getcwd();
	}
	function import_txt() {
		$temp = new Template_appl('wo_nomina_tgr.htm');	
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		//$sql = "SELECT	null MONTO_MINIMO   FROM	DUAL";
		//$dw_datos = new datawindow($sql);

		//$entrable = false;
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
				AND GI.COD_TIPO_INFORME = ".self::K_NOMINA_TGR."
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
					$tabla .= '<a href="wo_nomina_tgr.php?descarga_archivo='.$cod_genera_informe.'">';
					$tabla .= '<button type="button">Descargar</button> </a></td>';
				}
				if ($nom_estado_informe == 'INICIADO' || $nom_estado_informe == 'PROCESANDO' ){
					$abilita_boton = false;
				}
				$tabla .= '</tr>';
		}
		if ($abilita_boton) {
			$boton_ejecucion = '<input type="submit" name="b_file_xls" id="b_file_xls" value="Generar Archivo" class="button"/>';
		}else{
			$boton_ejecucion = '<label style="margin-right: 10px;">Informe en proceso..</label>';
		}
		$temp->setVar("BOTON_EJECUCION", $boton_ejecucion);
		$temp->setVar("TABLA", $tabla);

		$temp->setVar("COD_USUARIO", session::get("COD_USUARIO"));

		//$dw_datos->habilitar($temp, $entrable);
		$menu = unserialize(session::get('menu_appl'));
		$menu->draw($temp,$db);
		print $temp->toString();
	}

    function print_txt($cod_usuario ){
		$archivo = "DEUDORES_NOMINA_TGR_".K_CLIENTE.".txt";
		$ruta_dcto = "documentos/nomina_tgr/".$archivo;
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
				
			///////////////// CLASS_SQL = OUTPUT  ///////////////

			$db->EXECUTE_SP('spi_nomina_tgr', "$cod_usuario");
			$sql = "SELECT	LINEA
					FROM	TEMP_NOMINA_TGR
					ORDER BY LINEA";	
			$result = $db->build_results($sql);				

			for($i = 0; $i < count($result); $i++){
				$linea 		= $result[$i][0];
				
				if($i < count($result)){
					if($i == (count($result)-1))
					{
						$linea = $linea;	
					}else{
						$linea = $linea."\r\n";
					}
				}
				if (fwrite($handle, $linea) === FALSE) {
					$this->alert("No se puede escribir en el Archivo: $archivo");
					exit;
				}
			}
		}
		else{
			$this->alert("El Archivo: $archivo no puede ser escrito.");
		}
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=\"$archivo\"\n");
		//$this->alert(file_get_contents($ruta_dcto));
		//print file_get_contents($ruta_dcto);
		
		// SE BORRA EL ARCHIVO DEL SERVIDOR
		//unlink('documentos/'.$archivo);
		return $ruta_dcto;
	}
	function crontab(){
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	
		$parametros = '';
		
		$operacion = 'INSERT';
		$cod_tipo_informe  = self::K_NOMINA_TGR;
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
			$w = new wo_nomina_tgr();
			$w->import_txt();
			return false;
		}
		$this->alert('El informe esta siendo procesado , cuando finalice se le informara vía email ');
		$w = new wo_nomina_tgr();
		$w->import_txt();
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
			
			$w = new wo_nomina_tgr();
			$w->import_txt();
		}

	}
}

$w = new wo_nomina_tgr();
if (isset($_POST['b_file_xls'])) {
	$w->crontab();
}elseif(isset($_GET['cod_item_menu'])){
	$w->import_txt();
}elseif(isset($_GET['descarga_archivo'])){
	$w->descraga_archivo($_GET['descarga_archivo']);
}
?>