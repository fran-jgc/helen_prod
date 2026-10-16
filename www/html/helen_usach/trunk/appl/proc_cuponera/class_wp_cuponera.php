<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/class_sql_cuponera.php");

ini_set('memory_limit', '480M');
//ini_set('max_execution_time', 900); //900 seconds = 15 minutes 

class wp_cuponera_base extends w_proceso {
	const K_CUPONERA 	= 18;
	const K_PLAN_MOROSA	= 4;
	
	var $fecha_ejecucion;

	function __construct() {
		$this->fecha_ejecucion = $this->str2date($this->current_date(), $this->current_time());
				
   		if(K_TIPO_BD == "oci"){
			$sql = "select	 null COD_TIPO_DEUDA
	    			from DUAL";
 		}
   		else if(K_TIPO_BD == "mssql"){
	   		$sql = "select	 null COD_TIPO_DEUDA";
   		}
 		$this->dws['dw_cuponera'] = new datawindow($sql); 
    	
 		if(K_TIPO_BD == "oci"){
 			$sql = "SELECT	COD_TIPO_DEUDA
							,NOM_TIPO_DEUDA
					FROM	TIPO_DEUDA
					WHERE	COD_TIPO_DEUDA not in('CS', 'CSR')
					UNION
					SELECT	'CS_5-CS_35' COD_TIPO_DEUDA
							,'CS 5% / CS 3,5 UTM'	NOM_TIPO_DEUDA
					FROM DUAL
					UNION
					SELECT	'CS_20'	COD_TIPO_DEUDA
							,'CREDITO SOLIDARIO DPA o 20%' NOM_TIPO_DEUDA
					FROM DUAL
					UNION
					SELECT	'CSR'	COD_TIPO_DEUDA
							,'CREDITO SOLIDARIO REPROGRAMADO' NOM_TIPO_DEUDA
					FROM DUAL
					UNION
					SELECT	'CSR_5-CSR_35'	COD_TIPO_DEUDA
							,'CSR 5% / CSR 3,5 UTM'	NOM_TIPO_DEUDA
					FROM DUAL";

 		}else if(K_TIPO_BD == "mssql"){
	   		$sql = "SELECT	COD_TIPO_DEUDA
							,NOM_TIPO_DEUDA
					FROM	TIPO_DEUDA
					WHERE	COD_TIPO_DEUDA not in('CS', 'CSR')
					UNION
					SELECT	'CS_5-CS_35' COD_TIPO_DEUDA
							,'CS 5% / CS 3,5 UTM'	NOM_TIPO_DEUDA
					UNION
					SELECT	'CS_20'	COD_TIPO_DEUDA
							,'CREDITO SOLIDARIO DPA o 20%' NOM_TIPO_DEUDA
					UNION
					SELECT	'CSR'	COD_TIPO_DEUDA
							,'CREDITO SOLIDARIO REPROGRAMADO' NOM_TIPO_DEUDA
					UNION
					SELECT	'CSR_5-CSR_35'	COD_TIPO_DEUDA
							,'CSR 5% / CSR 3,5 UTM'	NOM_TIPO_DEUDA";
   		}
 		
    	$this->dws['dw_cuponera']->add_control(new drop_down_dw('COD_TIPO_DEUDA', $sql));
		
    	parent::__construct('cuponera', $_REQUEST['cod_item_menu'], self::K_CUPONERA);
   	}
	function load_dws() {
		parent::load_dws();
		$this->dws['dw_cuponera']->retrieve($this->cod_proceso);
	}
	function redraw_dws(&$temp) {
		parent::redraw_dws($temp);
		$this->dws['dw_cuponera']->habilitar($temp, true);
	}
	function make_cmd_sp($cod_ejecucion_proceso) {
		$this->dws['dw_cuponera']->get_values_from_POST();
		$cod_tipo_deuda = $this->dws['dw_cuponera']->get_item(0, 'COD_TIPO_DEUDA');
		$cod_usuario = $this->cod_usuario;

		if (K_TIPO_BD=="oci"){
			return "spdw_cuponera_pdf ($cod_ejecucion_proceso, '$cod_tipo_deuda', $cod_usuario, $this->fecha_ejecucion)";
		}else if (K_TIPO_BD=="mssql"){
			return "spdw_cuponera_pdf $cod_ejecucion_proceso, '$cod_tipo_deuda', $cod_usuario, $this->fecha_ejecucion";
		}
	}
	
	
	/**********************************************************************************/
	/*******************funciones que contectan los procesos en php *******************/
	function f_cancela_proceso($cod_ejecucion_proceso){
		$db_cancel = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		if (K_TIPO_BD=="oci") {
			$sql = "select f_ep_cancela_proceso($cod_ejecucion_proceso) CANCELA from DUAL";
			$result_cancel = $db_cancel->build_results($sql);
		}
		else if (K_TIPO_BD=="mssql") {
			$sql = "select dbo.f_ep_cancela_proceso($cod_ejecucion_proceso) CANCELA";
			$result_cancel = $db_cancel->build_results($sql);
		}
		
		if ($result_cancel[0]['CANCELA'] == 'S')
			return true;
			
		return false;
	}
	
	/**********************************************************************************/
	/**********************************************************************************/
	
	function pdf(){
		$db_2 = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
				
		if ($db_2->EXECUTE_SP('spu_ejecucion_proceso', "'NEW', null, $this->cod_proceso, $this->cod_usuario")) {
			$cod_ejecucion_proceso = $db_2->GET_IDENTITY('ejecucion_proceso');
			$this->dws['dw_ejecucion_actual']->retrieve($this->cod_proceso);
			$this->save_SESSION();
			
			$spu_proceso = $this->make_cmd_sp($cod_ejecucion_proceso);
				
			$db_3 = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$sql = "exec ".$spu_proceso;
			
			//echo $sql;
			$res = $db_3->query($sql);
			$cantidad = $db_3->count_rows();
	
			if($cantidad != 0){
				$labels = array();
				$file_name = $this->find_file('boleta', 'boleta.xml');
				$rpt = new print_boleta_uach($sql, $file_name.'.pdf', $labels, "Cuponeras", 0);										
				$this->redraw();
			}else{
				$this->redraw();
				$this->alert('La deuda Seleccionada no tiene emisión en el año.');
			}
		}
	}

	function carta(){
		$this->dws['dw_cuponera']->get_values_from_POST();
		$cod_tipo_deuda = $this->dws['dw_cuponera']->get_item(0, 'COD_TIPO_DEUDA');
				
		$db_2 = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		
		if ($db_2->EXECUTE_SP('spu_ejecucion_proceso', "'NEW', null, $this->cod_proceso, $this->cod_usuario")) {
			$cod_ejecucion_proceso = $db_2->GET_IDENTITY('ejecucion_proceso');
			$this->dws['dw_ejecucion_actual']->retrieve($this->cod_proceso);
			$this->save_SESSION();
			
			$spu_proceso = $this->make_cmd_sp($cod_ejecucion_proceso);
			 
			$db_3 = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			
			$sql_cod_boleta = "exec ".$spu_proceso;
			echo $sql_cod_boleta.' echo '; 
			
			$res = $db_3->query($sql_cod_boleta);
			$cantidad = $db_3->count_rows();
			
			if($cantidad == 0){
				$this->redraw();
				$this->alert('La deuda Seleccionada no tiene emisión en el año.');
			}else{
				if($cod_tipo_deuda == 'CS_5-CS_35'){
					$nom_tipo_deuda = 'CS_5%_CS_3,5_UTM';
				}
				if($cod_tipo_deuda == 'CSR_5-CSR_35'){
					$nom_tipo_deuda = 'CSR_5%_CSR_3,5_UTM';
				}else if($cod_tipo_deuda == 'CS_20'){
					$nom_tipo_deuda = 'CREDITO_SOLIDARIO_DPA';
				}else{
					$nom_tipo_deuda = $cod_tipo_deuda;
				}
				
				//NOMBRE DE ARCHIVO CARTA COBRANZA
				$name_archivo = "COBRANZA_".$nom_tipo_deuda.".txt";
				$fname = tempnam("/tmp", $name_archivo);		
				
				//$db_2->EXECUTE_SP('spu_ejecucion_proceso', "'SET_TOTAL', $cod_ejecucion_proceso, $this->cod_proceso, $this->cod_usuario, $cantidad, $fname");
				$handle = fopen($fname,"w");
				
				$ENCABEZADO   = "APELLIDO_PATERNO"."\t";
				$ENCABEZADO  .= "APELLIDO_MATERNO"."\t";
				$ENCABEZADO  .= "NOMBRE"."\t";
				$ENCABEZADO  .= "DIRECCION"."\t";
				$ENCABEZADO  .= "NOM_COMUNA"."\t";
				$ENCABEZADO  .= "NOM_CIUDAD"."\t";
				$ENCABEZADO  .= "MONTO_CAPITAL"."\t";
				$ENCABEZADO  .= "CANT_SUB_BOLETA"."\t";
				$ENCABEZADO  .= "CUOTAS_MOROSAS";
				$linea = '';
				
				fwrite($handle, $ENCABEZADO."\r\n");
				$y = 0;
				while($my_row = $db_3->get_row()){
					$cod_boleta = $my_row['COD_BOLETA'];
					//if ($this->cancelar_proceso($cod_ejecucion_proceso))
						//return;

					//////////////// CLASS_SQL = SELECT_CARTAS ///////////////
					$db_carta = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
					$sql_carta= sql_cuponera::get_sql('SELECT_CARTAS', $cod_boleta);
					$result_carta = $db_carta->build_results($sql_carta);
					$count = count($result_carta);
					for($i=0; $i<$count; $i++){
						$linea	 = $result_carta[$i]['APELLIDO_PATERNO']."\t";
						$linea	.= $result_carta[$i]['APELLIDO_MATERNO']."\t";
						$linea	.= $result_carta[$i]['NOMBRE']."\t";
						$linea	.= $result_carta[$i]['DIRECCION']."\t";
						$linea	.= $result_carta[$i]['NOM_COMUNA']."\t";
						$linea	.= $result_carta[$i]['NOM_CIUDAD']."\t";
						$linea	.= $result_carta[$i]['MONTO_CAPITAL']."\t";
						$linea	.= $result_carta[$i]['CANT_SUB_BOLETA']."\t";
						
						// obtiene las cuotas morosas
						$cod_deuda = $result_carta[$i]['CUOTAS_MOROSAS'];	// aqui viene el cod_deuda
						$sql_morosa = "select ANO_COBRO 
										from PLAN_PAGO 
										where COD_DEUDA = $cod_deuda
										AND COD_ESTADO_PLAN_PAGO = ".self::K_PLAN_MOROSA;
						$result_morosa = $db_carta->build_results($sql_morosa);
						$cuotas_morosas = '';
						for($i=0; $i < count($result_morosa); $i++)
							$cuotas_morosas .= ' - '.$result_morosa[$i]['ANO_COBRO'];
						$cuotas_morosas = substr($cuotas_morosas, 3);
						$result_carta[$i]['CUOTAS_MOROSAS'] = $cuotas_morosas;
						////
						
						$linea	.= $result_carta[$i]['CUOTAS_MOROSAS'];
					}
					$y++;
					fwrite($handle, $linea."\r\n");
					//$db_2->EXECUTE_SP('spu_ejecucion_proceso', "'SET_ACTUAL', $cod_ejecucion_proceso, $this->cod_proceso, $this->cod_usuario, $y, $fname");
				}
				fclose($handle);
				//$db_2->EXECUTE_SP('spu_ejecucion_proceso', "'END', $cod_ejecucion_proceso, $this->cod_proceso, $this->cod_usuario, $cantidad, $fname");
				header("Content-type: application/force-download; name=\"$name_archivo\"");
				header("Content-Disposition: inline; filename=\"$name_archivo\"");
				$fh=fopen($fname, "rb");
				$fh=fopen($fname, "rb");
				fpassthru($fh);
			}
		}
	}
	function procesa_event(){
		if(isset($_POST['b_pdf']))
			$this->pdf();
		else if(isset($_POST['b_carta']))
			$this->carta();
		else
			parent::procesa_event();
	}
}

// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wp_cuponera.php";
if (file_exists($file_name)) {
	require_once($file_name);
}
else {
	class wp_cuponera extends wp_cuponera_base{
	    function __construct () {
	        parent::__construct(); 
		}
	}
}
?>