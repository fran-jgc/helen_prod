<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

class wp_usach_cb extends archivo_datos{
	
    public function __construct($file, $dir, $columnas) {
        parent::__construct($file, $dir, $columnas);
	}

	function read_record_cb_santander($num_linea) {
		
		$linea = fgets($this->handle);
		if ($linea=='')
			return false;

		//if(($num_linea = 1)||($num_linea >= 3))
			//return false;
			
		$tipo_registro = substr($linea,0,1);
		
		if($tipo_registro == 2){
			$result = array();
			$result['COD_CLIENTE'] = substr($linea,1,14);
			$result['NO_DEFINIDO'] = substr($linea,15,6);
			$result['FECHA_VENCIMIENTO'] = substr($linea,21,8); 
			$result['ESPACIOS'] = substr($linea,29,50);
			$result['MARCA_INICIAL'] = substr($linea,79,1); 
			$result['MONTO_PESOS'] = substr($linea,80,12);
			$result['NO_DEFINIDO_2'] = substr($linea,92,2); 
			$result['FECHA_PAGO'] = substr($linea,94,6);
			$result['MONTO'] = substr($linea,100,12); 
			$result['NO_DEFINIDO_3'] = substr($linea,112,15);
			$result['TIPO_MONEDA'] = substr($linea,127,2);
			$result['TIPO_PAGO'] = substr($linea,129,1);
		}
		else
			return false;
			
		return $result;
	}
	
	function read_record_cb_desarrollo($num_linea) {
		
		$linea = fgets($this->handle);
		if ($linea=='')
			return false;

		$indice = $num_linea-1;
		$result = array();
			$result[$indice]['COD_REGISTRO'] = substr($linea,0,1);
			$result[$indice]['COD_CONVENIO'] = substr($linea,1,16);
			$result[$indice]['CTA_ABONO'] = substr($linea,17,16);
			$result[$indice]['IDENTIF_DEUDOR'] = substr($linea,33,35); 
			$result[$indice]['F_VENCTO'] = substr($linea,68,8);
			$result[$indice]['F_REAL_PAGO'] = substr($linea,76,8); 
			$result[$indice]['VALOR_CUOTA'] = substr($linea,84,15);
			$result[$indice]['MONTO_RECARGO'] = substr($linea,99,15); 
			$result[$indice]['VALOR_RECAUDADO_EFEC'] = substr($linea,114,15);
			$result[$indice]['VALOR_RECAUDADO_DOC'] = substr($linea,129,15); 
			$result[$indice]['SUCURSAL_RECAUDADOR'] = substr($linea,144,3);
			$result[$indice]['IDENTIF_CAJERO'] = substr($linea,147,10); 
			$result[$indice]['MARCA_MANUAL'] = substr($linea,157,6);
			$result[$indice]['CURSO'] = substr($linea,163,3); 
			$result[$indice]['NRO_CUOTA'] = substr($linea,166,3);
			$result[$indice]['FILLER'] = substr($linea,169,111);
		return $result;
	}
	function read_record_cb_servipag($num_linea) {
		
		$linea = fgets($this->handle);
		if ($linea=='')
			return false;

		$indice = $num_linea-1;
		$result = array();
			$result[$indice]['N_1'] = substr($linea,0,56);
			$result[$indice]['COD_SUB_BOLETA'] = substr($linea,57,39);
			$result[$indice]['N_2'] = substr($linea,97,22); 
			$result[$indice]['MONTO'] = substr($linea,120,7);
			$result[$indice]['N_3'] = substr($linea,128,31);
			$result[$indice]['FECHA_PAGO'] = substr($linea,160,8);
			$result[$indice]['N_4'] = substr($linea,169,14);
			 
		return $result;
	}
	
}

class wp_carga_banco extends wp_carga_banco_base{
	const K_CARGA_BANCO 		= 19;
	const K_ORIGEN_CB_TRANSBANK = 5;
	const K_ORIGEN_CB_DESARROLLO= 1;
	const K_ORIGEN_OTRAS_UES 	= 4;
	const K_ORIGEN_CB_TGR 		= 6;
	const K_ORIGEN_CB_SERVIPAG 	= 15;
	const K_ORIGEN_CB_SANTANDER = 3;
	const K_ORIGEN_CB_ORSAN_CR_CD = 7;
	const K_ORIGEN_CB_ORSAN_CR_CDE = 8;
	const K_ORIGEN_CB_ORSAN_CRUZ19287 = 9;
	const K_ORIGEN_CB_ORSAN_CRUZ19848 = 10;
	const K_ORIGEN_CB_ORSAN_DEU_PROP = 11;
	const K_ORIGEN_CB_ORSAN_MOROSO = 12;
	const K_ORIGEN_CB_ORSAN_REZAGADO = 13;
	const K_ORIGEN_CB_ORSAN_TGR = 14;
	const K_ORIGEN_CB_FUNCIONARIO = 16;
	const K_ORIGEN_CB_MINEDUC = 17;
	const K_ORIGEN_CB_HUARA = 99;
	const K_ORIGEN_ABONO_DESCTO = 20;

	var $archivo_cargado = false;
	
	function __construct(){
	    parent::__construct();
   		
   	}
   	
   	function redraw_dws(&$temp) {
		
		parent::redraw_dws($temp);
		$this->dws['dw_carga_banco']->habilitar($temp, true);
		
		if(isset($_POST['COD_ORIGEN_CARGA_BANCO_0']))
		  $COD_ORIGEN_CARGA_BANCO = $_POST['COD_ORIGEN_CARGA_BANCO_0'];
		else
		    $COD_ORIGEN_CARGA_BANCO = '';
		
		$this->dws['dw_carga_banco']->set_item(0, 'COD_ORIGEN_CARGA_BANCO',$COD_ORIGEN_CARGA_BANCO);
		
		if ($this->archivo_cargado){
		    
		    $temp->setVar("CARGADO", "S");
			$temp->setVar("ARCHIVO_CARGADO", "                    Archivo cargado");
			if ($COD_ORIGEN_CARGA_BANCO == self::K_ORIGEN_CB_MINEDUC){
				$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
				$sql = "SELECT COUNT(*) COUNT 
					FROM USACH_CB_MINEDUC
					WHERE CT_ANO = TO_NUMBER(TO_CHAR(SYSDATE, 'YYYY'), '9999')";
			
				$result = $db->build_results($sql);
				$cantidad = $result[0]['COUNT'];
				$temp->setVar("CARGADO_MINEDUC","                    ".number_format($cantidad, 0, ',', '.')." Registros procesados");
			}
			else
				$temp->setVar("CARGADO_MINEDUC", "");
		}	
		else{
			$temp->setVar("ARCHIVO_CARGADO", "");
			$temp->setVar("CARGADO_MINEDUC", "");
		}
				
		if ($COD_ORIGEN_CARGA_BANCO == self::K_ORIGEN_CB_DESARROLLO)
			$temp->setVar("VISIBLE_CAMPO", "");
		else
			$temp->setVar("VISIBLE_CAMPO", "none");	
				
		if ($COD_ORIGEN_CARGA_BANCO == self::K_ORIGEN_CB_MINEDUC)
			$temp->setVar("TR_VISIBLE_CAMPO", "none");
		else
			$temp->setVar("VISIBLE_CAMPO", "");	
			
	}
	
	function redraw() {
		$this->nom_template = "USACH/wp_carga_banco.htm";
		if ($this->archivo_cargado){
		    $COD_ORIGEN_CARGA_BANCO = $_POST['COD_ORIGEN_CARGA_BANCO_0'];
		    $this->dws['dw_carga_banco']->set_item(0, 'COD_ORIGEN_CARGA_BANCO',$COD_ORIGEN_CARGA_BANCO);
		}
		    
		parent::redraw();
	}
	
	function ejecutar_proceso() {
	    
	    if ($_POST['CARGADO'] != 'S') {
	        $this->redraw();
	        $this->alert("Debe cargar el archivo antes de dar inicio al proceso.");
	    }
	    else
	        parent::ejecutar_proceso();
	}
	
	function make_cmd_sp($cod_ejecucion_proceso) {
	 	$this->dws['dw_carga_banco']->get_values_from_POST();
	 	$cod_origen_carga_banco = $_POST['COD_ORIGEN_CARGA_BANCO_0'];
	 	
	 
		if ($cod_origen_carga_banco == self::K_ORIGEN_CB_DESARROLLO)//BANCO SANTANDER
			$cmd = "spp_usach_cb_desarrollo ($cod_ejecucion_proceso)";// con "()" cuando se oracle
		if ($cod_origen_carga_banco == self::K_ORIGEN_OTRAS_UES)//OTRAS UNIVERSIDADES
			$cmd = "spp_usach_cb_otras_univ ($cod_ejecucion_proceso)";
		if ($cod_origen_carga_banco == self::K_ORIGEN_CB_TRANSBANK)//TRANSBANK
			$cmd = "spp_usach_cb_transbank ($cod_ejecucion_proceso)";
		if ($cod_origen_carga_banco == self::K_ORIGEN_CB_TGR)//TESORERIA GENERAL DE LA REPUBLICA
			$cmd = "spp_usach_cb_tgr ($cod_ejecucion_proceso)";
		if ($cod_origen_carga_banco == self::K_ORIGEN_CB_FUNCIONARIO)//FUNCIONARIO
			$cmd = "spp_usach_cb_funcionario ($cod_ejecucion_proceso)";	
		if ($cod_origen_carga_banco == self::K_ORIGEN_CB_SERVIPAG)//SERVIPAG
			$cmd = "spp_usach_cb_servipag ($cod_ejecucion_proceso)";
		if ($cod_origen_carga_banco == self::K_ORIGEN_CB_SANTANDER)
			$cmd = "spp_usach_cb_santander ($cod_ejecucion_proceso)";
		if ($cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_CR_CD OR $cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_CR_CDE
		OR $cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_CRUZ19287 OR $cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_CRUZ19848
		OR $cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_DEU_PROP OR $cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_MOROSO
		OR $cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_REZAGADO OR $cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_TGR)//ORSAN
			$cmd = "spp_usach_cb_orsan ($cod_ejecucion_proceso, $cod_origen_carga_banco)";
		if ($cod_origen_carga_banco == self::K_ORIGEN_CB_MINEDUC)
			$cmd = "spp_usach_cb_mineduc ($cod_ejecucion_proceso)";
		
		if ($cod_origen_carga_banco == self::K_ORIGEN_ABONO_DESCTO)
			$cmd = "spp_cb_abono_descto $cod_ejecucion_proceso";

		return $cmd;
	}
	
	function cb_abono_descto(){
		
		$columnas = array(	'TIPO_PAGO'				=> array('TIPO' => 'TEXT',		'FORMATO' => '10',		'NULL' => 'S')
							,'RUT'					=> array('TIPO' => 'TEXT',		'FORMATO' => '10',		'NULL' => 'S')
							,'ANO_COBRO' 			=> array('TIPO' => 'NUMBER',   	'FORMATO' => '10',		'NULL' => 'S')
							,'NRO_CUOTA' 			=> array('TIPO' => 'NUMBER',   	'FORMATO' => '10',		'NULL' => 'S')
							,'MONTO_CUOTA'			=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14.4',	'NULL' => 'S')
							,'FECHA_PAGO' 			=> array('TIPO' => 'TEXT',   	'FORMATO' => '10',		'NULL' => 'S')
							,'CAPITAL_UTM' 			=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14.4',	'NULL' => 'S')
							,'INTERES_PENAL' 		=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14.4',	'NULL' => 'S')
							,'TOTAL_UTM' 			=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14.4',	'NULL' => 'S')
							,'TOTAL_PESOS' 			=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14',		'NULL' => 'S')
							,'PORC_CRUZADO' 		=> array('TIPO' => 'NUMBER',   	'FORMATO' => '5.2',		'NULL' => 'S')
							,'CAPITAL_UTM_REND' 	=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14.4',	'NULL' => 'S')
							,'INTERES_PENAL_REND' 	=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14.4',	'NULL' => 'S')
							,'TOTAL_UTM_REND' 		=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14.4',	'NULL' => 'S')
							,'TOTAL_PESOS_REND' 	=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14',		'NULL' => 'S')
							,'COD_TIPO_DEUDA' 		=> array('TIPO' => 'TEXT',   	'FORMATO' => '10',		'NULL' => 'S')
							);
		
		
	   $archivo = new archivo_datos('FILE_NAME', "archivos/", $columnas);
		$u = $archivo->upLoadFile();
		$sp = 'spu_cb_abono_desctos';
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db->BEGIN_TRANSACTION();
		$error = false;
		// procesa el archivo
		$archivo->open();
		$i = 1;
		
		///elimina todos los regisitros where cargado = 'N' and procesado = 'N', ulagos_cb_orsan
		if (!$db->EXECUTE_SP($sp, "'DELETE'")){
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
	    	
	    	//en windows el salto de linea es (\r\n)
			//en linux el salto de linea es (\n)
			/*if ($linea=='' || $linea=="\r\n" || $linea=="\n")
				return false;*/
			//el ultimo registro si tiene un enter o varios enter se sale del bucle pero siempre 
			//muestra el mensaje de error para el ultimo registro que esta entrando.
			if (!is_array($record)){
				$archivo->registra_error("El archivo a Cargar tiene saltos de Lineas en Blanco.");
				break;
			}
			
	    	//print_r($record);
	    	if (is_array($record)) {	// Si es array no existe error
	    		$tipo_pago			= $record['TIPO_PAGO'];
				$rut 				= $record['RUT'];
				$array_rut 	= explode('-', $rut);
				$rut = $array_rut[0];
				
				$rut = str_replace(' ', '',$rut);
				
				$ano_cobro			= $record['ANO_COBRO'];
				$nro_cuota			= $record['NRO_CUOTA'];
				$monto_cuota		= $record['MONTO_CUOTA'];							
				$fecha_pago			= $record['FECHA_PAGO'];			
				$capital_utm		= $record['CAPITAL_UTM'];
				$interes_penal		= $record['INTERES_PENAL'];
				$total_utm			= $record['TOTAL_UTM'];
				$total_pesos		= $record['TOTAL_PESOS'];			
				$porc_cruzado		= $record['PORC_CRUZADO'];
				$capital_utm_rend	= $record['CAPITAL_UTM_REND'];
				$interes_penal_rend	= $record['INTERES_PENAL_REND'];
				$total_utm_rend		= $record['TOTAL_UTM_REND'];
				$total_pesos_rend	= $record['TOTAL_PESOS_REND'];
				$cod_tipo_deuda		= $record['COD_TIPO_DEUDA'];
				
				$tipo_pago			= ($tipo_pago == '') ? "NULL" : "'$tipo_pago'";
				$rut				= ($rut == '') ? "NULL" : $rut;
	        	$ano_cobro			= ($ano_cobro == '') ? "NULL" : $ano_cobro;
	        	$nro_cuota			= ($nro_cuota == '') ? "NULL" : $nro_cuota;
	        	$monto_cuota		= ($monto_cuota == '') ? "NULL" : $monto_cuota;
	        	$capital_utm		= ($capital_utm == '') ? "NULL" : $capital_utm;
	        	$interes_penal		= ($interes_penal == '') ? "NULL" : $interes_penal;
	        	$total_utm			= ($total_utm == '') ? "NULL" : $total_utm;
	        	$total_pesos		= ($total_pesos == '') ? "NULL" : $total_pesos;
	        	$porc_cruzado		= ($porc_cruzado == '') ? "NULL" : $porc_cruzado;
	        	$capital_utm_rend	= ($capital_utm_rend == '') ? "NULL" : $capital_utm_rend;
	        	$interes_penal_rend	= ($interes_penal_rend == '') ? "NULL" : $interes_penal_rend;
	        	$total_utm_rend		= ($total_utm_rend == '') ? "NULL" : $total_utm_rend;
	        	$total_pesos_rend	= ($total_pesos_rend == '') ? "NULL" : $total_pesos_rend;
	        	$cod_tipo_deuda		= ($cod_tipo_deuda == '') ? "NULL" : $cod_tipo_deuda;
							
				
				//$fecha_pago
	        	$fecha_pago 	= $this->str2date($fecha_pago);
				
               	$param =  "'INSERT'
               				,$tipo_pago
							,$rut		
				        	,$ano_cobro	
				        	,$nro_cuota	
				        	,$monto_cuota
				        	,$fecha_pago
				        	,$capital_utm		
				        	,$interes_penal		
				        	,$total_utm			
				        	,$total_pesos		
				        	,$porc_cruzado		
				        	,$capital_utm_rend	
				        	,$interes_penal_rend
				        	,$total_utm_rend	
				        	,$total_pesos_rend
				        	,'$cod_tipo_deuda'";

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
		//BITACORA CARGA ARCHIVO
		$nom_archivo_cargado = $_FILES['FILE_NAME']['name'];
		$sp_carga_archivo 		= 'spu_carga_archivo';
		$param_carga_archivo	= self::K_CARGA_BANCO."
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
	
	function usach_cb_huara(){
		$columnas = array(	 'RUT'					=> array('TIPO' => 'TEXT',   	'FORMATO' => '20',				'NULL' => 'S')
							,'COD_DEUDA'			=> array('TIPO' => 'TEXT',   	'FORMATO' => '20',				'NULL' => 'S')
							,'FECHA_ABONO'			=> array('TIPO' => 'TEXT',		'FORMATO' => '20',				'NULL' => 'S')
							,'FECHA_DEPOSITO'		=> array('TIPO' => 'TEXT',   	'FORMATO' => '20',				'NULL' => 'S')
							,'FOLIO'				=> array('TIPO' => 'TEXT',   	'FORMATO' => '100',				'NULL' => 'S')
							,'MONTO_CAPITAL_UTM'	=> array('TIPO' => 'TEXT',   	'FORMATO' => '20',				'NULL' => 'S')
							,'MONTO_INT_UTM'		=> array('TIPO' => 'TEXT',   	'FORMATO' => '20',				'NULL' => 'S')
							,'MONTO_INT_NORMAL_UTM'	=> array('TIPO' => 'TEXT',   	'FORMATO' => '20',				'NULL' => 'S')
							,'MONTO_PESOS'			=> array('TIPO' => 'TEXT',   	'FORMATO' => '20',				'NULL' => 'S')
							,'MONTO_INT_NORMAL_PESOS'		=> array('TIPO' => 'TEXT',   	'FORMATO' => '20',		'NULL' => 'S')
							,'MONTO_INT_MORATORIO_PESOS'	=> array('TIPO' => 'TEXT',   	'FORMATO' => '20',		'NULL' => 'S')
							,'MONTO_DESCUENTO_UTM'	=> array('TIPO' => 'TEXT',   	'FORMATO' => '20',				'NULL' => 'S')
							,'MONTO_DESCUENTO_PESOS'		=> array('TIPO' => 'TEXT',   	'FORMATO' => '20',		'NULL' => 'S')
							,'COD_COMPONENTE_DEUDA'			=> array('TIPO' => 'TEXT',   	'FORMATO' => '20',		'NULL' => 'S')
							,'COD_ENTIDAD_ORIGEN_PAGO'		=> array('TIPO' => 'TEXT',   	'FORMATO' => '20',		'NULL' => 'S')
							,'FECHA_INGRESO_ABONO'			=> array('TIPO' => 'TEXT',   	'FORMATO' => '20',		'NULL' => 'S')
							,'ANO_PAGO'				=> array('TIPO' => 'TEXT',   			'FORMATO' => '4',		'NULL' => 'S')
							);

		$archivo = new wp_usach_cb('FILE_NAME', "archivos/", $columnas);	
		$u = $archivo->upLoadFile();
		$sp = 'spu_usach_cb_huara';
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		
		$db->BEGIN_TRANSACTION();
		$error = false;
		// procesa el archivo
		$archivo->open();
		
		///elimina todos los regisitros where cargado = 'N' and procesado = 'N', usach_cb_huara
		if (!$db->EXECUTE_SP($sp, "'DELETE'")){
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
	    	//print_r($record);
	    	if (is_array($record)) {	// Si es array no existe error
	    		$rut	= str_replace('.','',$record['RUT']);
				$cod_deuda		= str_replace('.','',$record['COD_DEUDA']);
				$fecha_abono	= $record['FECHA_ABONO'];
				$fecha_deposito	= $record['FECHA_DEPOSITO'];
				$folio			= $record['FOLIO'];
				$monto_capital_utm	= str_replace(',','.',str_replace('.','',$record['MONTO_CAPITAL_UTM']));
				$monto_int_utm		= str_replace(',','.',str_replace('.','',$record['MONTO_INT_UTM']));
				$monto_int_normal_utm	= str_replace(',','.',str_replace('.','',$record['MONTO_INT_NORMAL_UTM']));
				$monto_pesos		= str_replace(',','.',str_replace('.','',$record['MONTO_PESOS']));
				$monto_int_normal_pesos	= str_replace(',','.',str_replace('.','',$record['MONTO_INT_NORMAL_PESOS']));
				$monto_int_moratorio_pesos	= str_replace(',','.',str_replace('.','',$record['MONTO_INT_MORATORIO_PESOS']));
				$monto_descuento_utm	= str_replace(',','.',str_replace('.','',$record['MONTO_DESCUENTO_UTM']));
				$monto_descuento_pesos	= str_replace(',','.',str_replace('.','',$record['MONTO_DESCUENTO_PESOS']));
				$cod_componente_deuda	= str_replace('.','',$record['COD_COMPONENTE_DEUDA']);
				$cod_entidad_origen_pago	= str_replace('.','',$record['COD_ENTIDAD_ORIGEN_PAGO']);   //ORIGEN_BANCO_HUARA
				$fecha_ingreso_abono	= $record['FECHA_INGRESO_ABONO'];
				$ano_pago		= str_replace('.','',$record['ANO_PAGO']);
	    		
				$fecha_abono			= $this->str2date($fecha_abono);
				$fecha_deposito 		= $this->str2date($fecha_deposito);
				$fecha_ingreso_abono 	= $this->str2date($fecha_ingreso_abono);
				
               	$param = "'INSERT'
               			,$rut
						,$cod_deuda
						,$fecha_abono
						,$fecha_deposito
						,'$folio'
						,$monto_capital_utm
						,$monto_int_utm
						,$monto_int_normal_utm
						,$monto_pesos
						,$monto_int_normal_pesos
						,$monto_int_moratorio_pesos
						,$monto_descuento_utm
						,$monto_descuento_pesos
						,$cod_componente_deuda
						,'$cod_entidad_origen_pago'
						,$fecha_ingreso_abono
						,$ano_pago";
				
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
		$param_carga_archivo	= self::K_CARGA_BANCO.", 'K_ORIGEN_CB_HUARA=> $nom_archivo_cargado'";

		if (!$db->EXECUTE_SP($sp_carga_archivo, $param_carga_archivo)){
	    	$error = true;
			$db->ROLLBACK_TRANSACTION();
			$error_sp = $db->GET_ERROR();
			$this->alert('No se pudo grabar En la Bitacora de Archivo Carga.\n\n'.$db->make_msg_error_bd());
			//break;
	    }

	    $cod_usario	= session::get('COD_USUARIO');
	    //ejecuta el proceso con cod_usuario
		$spp_carga_archivo	= 'spp_usach_cb_huara';
		$sp_param	= "$cod_usario";

		if (!$db->EXECUTE_SP($spp_carga_archivo, $sp_param)){
	    	$error = true;
			$db->ROLLBACK_TRANSACTION();
			$error_sp = $db->GET_ERROR();
			$this->alert('No se pudo realizar el Archivo Carga del Huara.\n\n'.$db->make_msg_error_bd());
			//break;
	    }
	    
		$archivo->renFile();
		
		$archivo->display_error();	// Muestra los errores si existen
		
		if (!$error) {
			$db->COMMIT_TRANSACTION();
			$cod_ejecucion_proceso = $db->GET_IDENTITY('EJECUCION_PROCESO');
			$sql_error = "SELECT count(*) CANT
						  FROM	error_proceso
						  WHERE cod_ejecucion_proceso = $cod_ejecucion_proceso";
			$result = $db->build_results($sql_error);
			$cant = $result[0]['CANT'];
			
			if($cant > 0){
				$msg = 'Se cargo el archivo de Huara, pero con errores.\n\nRevise Modulo Auditoria Proceso.';
			}else{
				$msg = 'Se cargo el archivo de Huara con Exito.';
			}

			$this->archivo_cargado = true;
			$this->presentacion($msg);
		}else{
			$this->redraw();
		}
	}
	
	function cb_desarrollo($fecha_deposito){
		$columnas = array(	'DETALLE' 	   			=> array('TIPO' => 'TEXT'		,'FORMATO' => ''			,'NULL' => 'N'));
	 	$archivo = new wp_usach_cb('FILE_NAME', "archivos/", $columnas);	
		$u = $archivo->upLoadFile();
		$sp = 'spu_usach_cb_desarrollo';
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$this->fecha_carga = $this->str2date($db->current_date(), $db->current_time());
		$db->BEGIN_TRANSACTION();
		
		$error = false;
		// procesa el archivo
		$archivo->open();
		$i = 1;
		
		///elimina todos los regisitros where cargado = 'N' and procesado = 'N', usach_cb_servipag
		if (!$db->EXECUTE_SP($sp, "'DELETE'")){
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
	    	$record = $archivo->read_record_cb_desarrollo($i);
	    	
	    	//print_r($record);
	    	//return;
	    	if (is_array($record)) {	// Si es array no existe error
	                $cod_registro		= $record[$i-1]['COD_REGISTRO'];
	                $cod_convenio		= $record[$i-1]['COD_CONVENIO'];
	                $cta_abono			= $record[$i-1]['CTA_ABONO'];
	                $identif_deudor		= $record[$i-1]['IDENTIF_DEUDOR'];
	                $f_vencto			= $record[$i-1]['F_VENCTO']; //fecha vencimiento
	                $f_real_pago		= $record[$i-1]['F_REAL_PAGO']; //fecha real pago
	                $valor_cuota		= $record[$i-1]['VALOR_CUOTA'];
	                $monto_recargo		= $record[$i-1]['MONTO_RECARGO'];
	                $valor_recaudador_efec	= $record[$i-1]['VALOR_RECAUDADO_EFEC'];
	                $valor_recaudador_doc	= $record[$i-1]['VALOR_RECAUDADO_DOC'];
	                $sucursal_recaudador	= $record[$i-1]['SUCURSAL_RECAUDADOR'];
	                $identif_cajero		= $record[$i-1]['IDENTIF_CAJERO'];
	                $marca_manual		= $record[$i-1]['MARCA_MANUAL'];
	                $curso				= $record[$i-1]['CURSO'];
	                $nro_cuota			= $record[$i-1]['NRO_CUOTA'];
	                //$f_deposito			= $record[$i-1]['F_DEPOSITO']; //fecha deposito
	                $filler				= $record[$i-1]['FILLER'];

	                //tratamientos de numeros 11 digitos y 4 decimales 
	                $valor_cuota		= substr($valor_cuota, 0,11).'.'.substr($valor_cuota, -4);
	                $monto_recargo		= substr($monto_recargo, 0,11).'.'.substr($monto_recargo, -4);
	                $valor_recaudador_efec	= substr($valor_recaudador_efec, 0,11).'.'.substr($valor_recaudador_efec, -4);
	                $valor_recaudador_doc	= substr($valor_recaudador_doc, 0,11).'.'.substr($valor_recaudador_doc, -4);
	                
	                //tratamiento de fechas $f_vencto, $f_real_pago
	                //$f_vencto
	                $f_vencto_ano = substr($f_vencto,0,4);
	        		$f_vencto_mes = substr($f_vencto,4,2);
	        		$f_vencto_dia = substr($f_vencto,6,2);
	        		$f_vencto = $f_vencto_dia.'/'.$f_vencto_mes.'/'.$f_vencto_ano;
	        		$f_vencto = $this->str2date($f_vencto);
	        		//$f_real_pago
	        		$f_real_pago_ano = substr($f_real_pago,0,4);
	        		$f_real_pago_mes = substr($f_real_pago,4,2);
	        		$f_real_pago_dia = substr($f_real_pago,6,2);
	        		$f_real_pago = $f_real_pago_dia.'/'.$f_real_pago_mes.'/'.$f_real_pago_ano;
	        		$f_real_pago = $this->str2date($f_real_pago); 
	        		//tratamiento de fechas $f_vencto, $f_real_pago
	                //$fecha_deposito
	                
	                $cod_registro	= ($cod_registro == '') ? "NULL" : $cod_registro;
	        		$cod_convenio	= ($cod_convenio == '') ? "NULL" : $cod_convenio;
	        		$cta_abono		= ($cta_abono == '') ? "NULL" : "'$cta_abono'";
	        		$identif_deudor = ($identif_deudor == '') ? "NULL" : "'$identif_deudor'";
	        		$f_vencto		= ($f_vencto == '') ? "NULL" : $f_vencto;
	        		$f_real_pago	= ($f_real_pago == '') ? "NULL" : $f_real_pago;
	        		$valor_cuota	= ($valor_cuota == '') ? 0.0 : $valor_cuota;
	        		$monto_recargo	= ($monto_recargo == '') ? 0.0 : $monto_recargo;
	        		$valor_recaudador_efec	= ($valor_recaudador_efec == '') ? 0.0 : $valor_recaudador_efec;
	        		$valor_recaudador_doc	= ($valor_recaudador_doc == '') ? 0.0 : $valor_recaudador_doc;
	        		$sucursal_recaudador	= ($sucursal_recaudador == '') ? "NULL" : $sucursal_recaudador;
	        		$identif_cajero = ($identif_cajero == '') ? "NULL" : "'$identif_cajero'";
	        		$marca_manual	= ($marca_manual == '') ? "NULL" : "'$marca_manual'";
	        		$curso			= ($curso == '') ? "NULL" : "'$curso'";
	        		$nro_cuota		= ($nro_cuota == '') ? "NULL" : $nro_cuota;
	        		$f_deposito		= ($f_deposito == '') ? "NULL" : $f_deposito;
	        		$filler			= ($filler == '') ? "NULL" : "'$filler'";

					$param	=	"'INSERT'
								,$cod_registro
				        		,$cod_convenio
				        		,$cta_abono
				        		,$identif_deudor
				        		,$f_vencto
				        		,$f_real_pago
				        		,$valor_cuota
				        		,$monto_recargo
				        		,$valor_recaudador_efec
				        		,$valor_recaudador_doc
				        		,$sucursal_recaudador
				        		,$identif_cajero
				        		,$marca_manual
				        		,$curso
				        		,$nro_cuota
				        		,$fecha_deposito		
				        		,$filler";				
					
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
		$param_carga_archivo	= self::K_CARGA_BANCO."
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

	function cb_transbank(){
		$columnas = array(	'COD_COMERCIO' 		=> array('TIPO' => 'NUMBER',   	'FORMATO' => '10',				'NULL' => 'S')
							,'NOMBRE' 			=> array('TIPO' => 'TEXT',   	'FORMATO' => '100',				'NULL' => 'S')
							,'FECHA_DEPOSITO'	=> array('TIPO' => 'TEXT',		'FORMATO' => '10',				'NULL' => 'S')
							,'COD_SUB_BOLETA' 	=> array('TIPO' => 'NUMBER',   	'FORMATO' => '10',				'NULL' => 'S')
							,'MONTO' 			=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14',				'NULL' => 'S')
							,'TIPO_VENTA' 		=> array('TIPO' => 'TEXT',   	'FORMATO' => '10',				'NULL' => 'S')
							,'NRO_CUOTA' 		=> array('TIPO' => 'NUMBER',   	'FORMATO' => '10',				'NULL' => 'S')
							,'FECHA_ABONO' 		=> array('TIPO' => 'TEXT',   	'FORMATO' => '10',				'NULL' => 'S')
							,'ESTADO' 			=> array('TIPO' => 'TEXT',   	'FORMATO' => '10',				'NULL' => 'S')
							,'MEDIO_PAGO' 		=> array('TIPO' => 'TEXT',   	'FORMATO' => '10',				'NULL' => 'S')
							);
		
		$archivo = new wp_usach_cb('FILE_NAME', "archivos/", $columnas);
		$u = $archivo->upLoadFile();
		$sp = 'spu_usach_cb_transbank';
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db->BEGIN_TRANSACTION();
		
		$error = false;
		// procesa el archivo
		$archivo->open();
		$i = 1;
		
		///elimina todos los regisitros where cargado = 'N' and procesado = 'N', usach_cb_transbank
		if (!$db->EXECUTE_SP($sp, "'DELETE'")){
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
	    	//print_r($record);
	    	if (is_array($record)) {	// Si es array no existe error
				$fecha_deposito	= $record['FECHA_DEPOSITO'];
				$cod_sub_boleta = $record['COD_SUB_BOLETA'];
				$monto			= $record['MONTO'];
				$fecha_abono 	= $record['FECHA_ABONO'];
				
				$fecha_deposito = $this->str2date($fecha_deposito);
				$fecha_abono 	= $this->str2date($fecha_abono);
				
               	$param = "'INSERT'
               			,$fecha_deposito
						,$cod_sub_boleta
                		,$monto
                		,$fecha_abono";

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
		$param_carga_archivo	= self::K_CARGA_BANCO."
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
	////////
	function cb_tgr($fecha_deposito){
		
		$columnas = array(	'RUT_DEUDOR'			=> array('TIPO' => 'TEXT',		'FORMATO' => '20',				'NULL' => 'N')
							,'FOLIO_CARGA' 			=> array('TIPO' => 'TEXT',   	'FORMATO' => '20',				'NULL' => 'S')
							,'MONTO_DEUDA_UTM' 		=> array('TIPO' => 'TEXT',   	'FORMATO' => '14',				'NULL' => 'N')
							,'MONTO_RETENIDO_UTM' 	=> array('TIPO' => 'TEXT',   	'FORMATO' => '14',				'NULL' => 'N')
							,'MONTO_RETENIDO_PESOS' => array('TIPO' => 'TEXT',   	'FORMATO' => '14',				'NULL' => 'N')
							,'FECHA_REAL_PAGO' 		=> array('TIPO' => 'TEXT',   	'FORMATO' => '20',				'NULL' => 'N')
							);
						
		$archivo = new wp_usach_cb('FILE_NAME', "archivos/", $columnas);
		$u = $archivo->upLoadFile();
		
		$sp = 'spu_usach_cb_tgr';
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db->BEGIN_TRANSACTION();
		
		$error = false;
		// procesa el archivo
		$archivo->open();
		$i = 1;
		
		///elimina todos los regisitros where cargado = 'N' and procesado = 'N', usach_cb_tgr
		if (!$db->EXECUTE_SP($sp, "'DELETE'")){
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
	    	//print_r($record);
	    	if (is_array($record)) {	// Si es array no existe error
				$rut_deudor		= $record['RUT_DEUDOR'];
				$folio_carga 	= $record['FOLIO_CARGA'];
				$monto_retenido = $record['MONTO_RETENIDO_PESOS'];
				$fecha_pago 	= $record['FECHA_REAL_PAGO'];
				
				$fecha_pago = $this->str2date($fecha_pago); 
				
				$folio_carga = ($folio_carga == '') ? "null" : $folio_carga;
				
               	$param = "'INSERT'
               			,'$rut_deudor'
						,$monto_retenido
                		,$fecha_deposito
                		,'$folio_carga'
                		,$fecha_pago";
                		
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
		$param_carga_archivo	= self::K_CARGA_BANCO."
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

	function cb_funcionario($fecha_deposito){
	    
		$columnas = array(	'COD_DEUDA'				=> array('TIPO' => 'NUMBER',   	'FORMATO' => '10',				'NULL' => 'S')
							,'FOLIO_CARGA' 			=> array('TIPO' => 'TEXT',   	'FORMATO' => '20',				'NULL' => 'S')
							,'MONTO_RETENIDO_UTM' 	=> array('TIPO' => 'TEXT',   	'FORMATO' => '14',				'NULL' => 'N')
							,'MONTO_RETENIDO_PESOS' => array('TIPO' => 'TEXT',   	'FORMATO' => '14',				'NULL' => 'N')
							,'FECHA_REAL_PAGO' 		=> array('TIPO' => 'TEXT',   	'FORMATO' => '20',				'NULL' => 'N'));


		$archivo = new wp_usach_cb('FILE_NAME', "archivos/", $columnas);
		$u = $archivo->upLoadFile();
		
		$sp = 'spu_usach_cb_funcionario';
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db->BEGIN_TRANSACTION();
		
		$error = false;
		// procesa el archivo
		$archivo->open();
		$i = 1;
		
		///elimina todos los regisitros where cargado = 'N' and procesado = 'N', usach_cb_tgr
		if (!$db->EXECUTE_SP($sp, "'DELETE'")){
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
	    	//print_r($record);
	    	if (is_array($record)) {	// Si es array no existe error
				$cod_deuda		= $record['COD_DEUDA'];
				$folio_carga 	= $record['FOLIO_CARGA'];
				$monto_retenido = $record['MONTO_RETENIDO_PESOS'];
				$fecha_pago 	= $record['FECHA_REAL_PAGO'];
				
				$fecha_pago = $this->str2date($fecha_pago); 
				
				$folio_carga = ($folio_carga == '') ? "null" : $folio_carga;
				
               	$param = "'INSERT'
               			,$cod_deuda
						,$monto_retenido
                		,$fecha_deposito
                		,'$folio_carga'
                		,$fecha_pago";

                		
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
		$param_carga_archivo	= self::K_CARGA_BANCO."
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
	
	function cb_servipag($fecha_deposito){
		$columnas = array(	'DETALLE' 	   			=> array('TIPO' => 'TEXT'		,'FORMATO' => ''			,'NULL' => 'N'));
	 	$archivo = new wp_usach_cb('FILE_NAME', "archivos/", $columnas);
	 	$u = $archivo->upLoadFile();
		$sp = 'spu_usach_cb_servipag';
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$this->fecha_carga = $this->str2date($db->current_date(), $db->current_time());
		$db->BEGIN_TRANSACTION();
		
		$error = false;
		// procesa el archivo
		$archivo->open();
		$i = 1;
		
		///elimina todos los regisitros where cargado = 'N' and procesado = 'N', usach_cb_servipag
		if (!$db->EXECUTE_SP($sp, "'DELETE'")){
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
	    	$record = $archivo->read_record_cb_servipag($i);
	    	
	    	if (is_array($record)) {	// Si es array no existe error
	                $n_1			= $record[$i-1]['N_1'];
	                $cod_sub_boleta	= $record[$i-1]['COD_SUB_BOLETA'];
	                $n_2			= $record[$i-1]['N_2'];
	                $monto			= $record[$i-1]['MONTO'];
	                $n_3			= $record[$i-1]['N_3']; 
	                $fecha_pago		= $record[$i-1]['FECHA_PAGO']; 
	                $n_4			= $record[$i-1]['N_4'];
	                
	                //$f_real_pago
	        		$f_pago_ano = substr($fecha_pago,0,4);
	        		$f_pago_mes = substr($fecha_pago,4,2);
	        		$f_pago_dia = substr($fecha_pago,6,2);
	        		$f_pago = $f_pago_dia.'/'.$f_pago_mes.'/'.$f_pago_ano;
	        		$f_pago = $this->str2date($f_pago); 
	        		
					$param	=	"'INSERT'
								,$cod_sub_boleta
				        		,$monto
				        		,$f_pago
				        		,$fecha_deposito";				
					
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
		$param_carga_archivo	= self::K_CARGA_BANCO."
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
	
	function cb_santander_nuevo($fecha_deposito){
		$columnas = array(	'TIPO_REGISTRO'		=> array('TIPO' => 'NUMBER',	'FORMATO' => '1',				'NULL' => 'S')
							,'COD_CLIENTE' 		=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14',				'NULL' => 'S')
							,'NO_DEFINIDO'		=> array('TIPO' => 'TEXT',   	'FORMATO' => '6',				'NULL' => 'S')
							,'FECHA_VENCIMIENTO'=> array('TIPO' => 'NUMBER',   	'FORMATO' => '8',				'NULL' => 'S')
							,'ESPACIOS' 		=> array('TIPO' => 'TEXT',   	'FORMATO' => '50',				'NULL' => 'S')
							,'MARCA_INICIAL'	=> array('TIPO' => 'TEXT',   	'FORMATO' => '1',				'NULL' => 'S')
							,'MONTO_PESOS'		=> array('TIPO' => 'NUMBER',   	'FORMATO' => '12',				'NULL' => 'S')
							,'NO_DEFINIDO_2'	=> array('TIPO' => 'TEXT',   	'FORMATO' => '2',				'NULL' => 'S')							
							,'FECHA_PAGO'	 	=> array('TIPO' => 'NUMBER',   	'FORMATO' => '6',				'NULL' => 'S')
							,'MONTO' 			=> array('TIPO' => 'NUMBER',   	'FORMATO' => '12',				'NULL' => 'S')
							,'NO_DEFINIDO_3'	=> array('TIPO' => 'TEXT',   	'FORMATO' => '15',				'NULL' => 'S')
							,'TIPO_MONEDA'	 	=> array('TIPO' => 'NUMBER',   	'FORMATO' => '2',				'NULL' => 'S')
							,'TIPO_PAGO' 		=> array('TIPO' => 'TEXT',   	'FORMATO' => '1',				'NULL' => 'S')
							);
		
		$archivo = new wp_usach_cb('FILE_NAME', "archivos/", $columnas);
		$u = $archivo->upLoadFile();
		$sp = 'spu_usach_cb_santander_nuevo';
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db->BEGIN_TRANSACTION();
		
		$error = false;
		// procesa el archivo
		$archivo->open();
		$i = 1;
		
		///elimina todos los regisitros where cargado = 'N' and procesado = 'N', usach_cb_santander
		if (!$db->EXECUTE_SP($sp, "'DELETE'")){
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
	    	$record = $archivo->read_record_cb_santander($i); 
	    	
	    	if (is_array($record)) {	// Si es array no existe error
				$cod_cliente 		= intval($record['COD_CLIENTE']);
				$fecha_vencimiento	= $record['FECHA_VENCIMIENTO'];
				$monto_pesos 		= intval($record['MONTO_PESOS']);
				$fecha_pago		 	= $record['FECHA_PAGO'];
				$monto			 	= intval($record['MONTO']);
				$tipo_moneda	 	= $record['TIPO_MONEDA'];
				$tipo_pago		 	= $record['TIPO_PAGO'];
				
				//$fecha_pago
	        	$f_pago_dia = substr($fecha_pago,0,2);
	        	$f_pago_mes = substr($fecha_pago,2,2);
				$f_pago_ano = substr($fecha_pago,4,2);
	        	
	        	
	        	$f_pago 	= $f_pago_dia.'/'.$f_pago_mes.'/'.'20'.$f_pago_ano;
	        	$f_pago 	= $this->str2date($f_pago); 
	        	
	        	//$fecha_vencimiento
	        	$f_vencimiento_dia = substr($fecha_vencimiento,0,2);
	        	$f_vencimiento_mes = substr($fecha_vencimiento,2,2);
	        	$f_vencimiento_ano = substr($fecha_vencimiento,4,4);
	        	
	        	$f_vencimiento 	= $f_vencimiento_dia.'/'.$f_vencimiento_mes.'/'.$f_vencimiento_ano;
	        	$f_vencimiento 	= $this->str2date($f_vencimiento); 
				
               	$param = "'INSERT'
               			,$cod_cliente
						,$f_vencimiento
                		,$monto_pesos
                		,$f_pago
                		,$monto
                		,$tipo_moneda
                		,'$tipo_pago'
                		,$fecha_deposito";
				
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
		//BITACORA CARGA ARCHIVO
		$nom_archivo_cargado = $_FILES['FILE_NAME']['name'];
		$sp_carga_archivo 		= 'spu_carga_archivo';
		$param_carga_archivo	= self::K_CARGA_BANCO."
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
	
	function cb_santander($fecha_deposito){
		$columnas = array(	'TIPO_REGISTRO'		=> array('TIPO' => 'NUMBER',	'FORMATO' => '10',				'NULL' => 'S')
							,'COD_CLIENTE' 		=> array('TIPO' => 'NUMBER',   	'FORMATO' => '50',				'NULL' => 'S')
							,'NRO_REF' 			=> array('TIPO' => 'NUMBER',   	'FORMATO' => '50',				'NULL' => 'S')
							,'FOLIO_DOC' 		=> array('TIPO' => 'TEXT',   	'FORMATO' => '10',				'NULL' => 'S')
							,'TIPO_DOC' 		=> array('TIPO' => 'TEXT',   	'FORMATO' => '2',				'NULL' => 'S')
							,'CUOTA' 			=> array('TIPO' => 'NUMBER',   	'FORMATO' => '3',				'NULL' => 'S')
							,'COD_BANCO' 		=> array('TIPO' => 'NUMBER',   	'FORMATO' => '5',				'NULL' => 'S')							
							,'MONTO_INTERES' 	=> array('TIPO' => 'NUMBER',   	'FORMATO' => '15',				'NULL' => 'S')
							,'FILLER' 			=> array('TIPO' => 'TEXT',   	'FORMATO' => '10',				'NULL' => 'S')
							,'FECHA_PAGO' 		=> array('TIPO' => 'TEXT',   	'FORMATO' => '10',				'NULL' => 'S')
							,'COD_SUB_BOLETA' 	=> array('TIPO' => 'NUMBER',   	'FORMATO' => '10',				'NULL' => 'S')
							,'MONTO_PAGO' 		=> array('TIPO' => 'NUMBER',   	'FORMATO' => '15',				'NULL' => 'S')
							,'SEPARADOR' 		=> array('TIPO' => 'TEXT',   	'FORMATO' => '1',				'NULL' => 'S')
							,'MEDIO_PAGO' 		=> array('TIPO' => 'TEXT',   	'FORMATO' => '2',				'NULL' => 'S')
							,'INDICADOR_RESULT' => array('TIPO' => 'TEXT',   	'FORMATO' => '1',				'NULL' => 'S')
							);
		
		$archivo = new wp_usach_cb('FILE_NAME', "archivos/", $columnas);
		$u = $archivo->upLoadFile();
		$sp = 'spu_usach_cb_santander';
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db->BEGIN_TRANSACTION();
		
		$error = false;
		// procesa el archivo
		$archivo->open();
		$i = 1;
		
		///elimina todos los regisitros where cargado = 'N' and procesado = 'N', usach_cb_santander
		if (!$db->EXECUTE_SP($sp, "'DELETE'")){
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
	    	//print_r($record);
	    	if (is_array($record)) {	// Si es array no existe error
				$fecha_pago 		= $record['FECHA_PAGO'];
				$cod_sub_boleta		= $record['COD_SUB_BOLETA'];
				$monto_pago 		= $record['MONTO_PAGO'];
				$indicador_result 	= $record['INDICADOR_RESULT'];
				
				//$fecha_pago
	        	$f_pago_ano = substr($fecha_pago,0,4);
	        	$f_pago_mes = substr($fecha_pago,4,2);
	        	$f_pago_dia = substr($fecha_pago,6,2);
	        	$f_pago 	= $f_pago_dia.'/'.$f_pago_mes.'/'.$f_pago_ano;
	        	$f_pago 	= $this->str2date($f_pago); 
				
               	$param = "'INSERT'
               			,$f_pago
						,$cod_sub_boleta
                		,$monto_pago
                		,'$indicador_result'
                		,$fecha_deposito";
				
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
		//BITACORA CARGA ARCHIVO
		$nom_archivo_cargado = $_FILES['FILE_NAME']['name'];
		$sp_carga_archivo 		= 'spu_carga_archivo';
		$param_carga_archivo	= self::K_CARGA_BANCO."
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

	function cb_otras_ues($fecha_deposito){
		$columnas = array(	'RUT'							=> array('TIPO' => 'TEXT',		'FORMATO' => '10',				'NULL' => 'S')
							,'ANO_COBRO' 					=> array('TIPO' => 'NUMBER',   	'FORMATO' => '10',				'NULL' => 'S')
							,'NRO_CUOTA' 					=> array('TIPO' => 'NUMBER',   	'FORMATO' => '10',				'NULL' => 'S')
							,'MONTO_CUOTA' 					=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14.4',			'NULL' => 'S')
							,'FECHA_PAGO' 					=> array('TIPO' => 'TEXT',   	'FORMATO' => '10',				'NULL' => 'S')
							,'MONTO_CAPITAL_UTM'			=> array('TIPO' => 'TEXT',   	'FORMATO' => '14',			'NULL' => 'S')
							,'MONTO_CAPITAL_PESOS'			=> array('TIPO' => 'TEXT',   	'FORMATO' => '14',				'NULL' => 'S')							
							,'MONTO_INTERES_UTM' 			=> array('TIPO' => 'TEXT',   	'FORMATO' => '14',			'NULL' => 'S')
							,'MONTO_INTERES_PESOS' 			=> array('TIPO' => 'TEXT',   	'FORMATO' => '14',				'NULL' => 'S')
							,'TOTAL_UTM' 					=> array('TIPO' => 'TEXT',   	'FORMATO' => '14',			'NULL' => 'S')
							,'TOTAL_PESOS' 					=> array('TIPO' => 'TEXT',   	'FORMATO' => '14',				'NULL' => 'S')
							,'DESCTO_TOTAL_UTM' 			=> array('TIPO' => 'TEXT',   	'FORMATO' => '14',			'NULL' => 'S')
							,'DESCTO_TOTAL_PESOS' 			=> array('TIPO' => 'TEXT',   	'FORMATO' => '14',				'NULL' => 'S')
							,'PORC_USACH' 					=> array('TIPO' => 'TEXT',   	'FORMATO' => '14',				'NULL' => 'S')
							,'MONTO_CAPITAL_UTM_USACH' 		=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14.4',			'NULL' => 'S')
							,'MONTO_CAPITAL_PESOS_USACH' 	=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14',				'NULL' => 'S')
							,'MONTO_INTERES_UTM_USACH' 		=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14.4',			'NULL' => 'S')
							,'MONTO_INTERES_PESOS_USACH' 	=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14',				'NULL' => 'S')
							,'DESCTO_UTM_USACH' 			=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14.4',			'NULL' => 'S')
							,'DESCTO_PESOS_USACH' 			=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14',				'NULL' => 'S')
							,'PORC_OTRAS_UES' 				=> array('TIPO' => 'TEXT',   	'FORMATO' => '14',				'NULL' => 'S')
							,'MONTO_CAPITAL_UTM_OTRAS_UES' 	=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14.4',			'NULL' => 'S')
							,'MONTO_CAPITAL_PESOS_OTRAS_UES'=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14',				'NULL' => 'S')
							,'MONTO_INTERES_UTM_OTRAS_UES' 	=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14.4',			'NULL' => 'S')
							,'MONTO_INTERES_PESOS_OTRAS_UES'=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14',				'NULL' => 'S')
							,'DESCTO_UTM_OTRAS_UES' 		=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14.4',			'NULL' => 'S')
							,'DESCTO_PESOS_OTRAS_UES' 		=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14',				'NULL' => 'S')
							,'VALOR_UTM' 					=> array('TIPO' => 'TEXT',   	'FORMATO' => '14',				'NULL' => 'S')
							,'FORMA_PAGO' 					=> array('TIPO' => 'TEXT',   	'FORMATO' => '10',				'NULL' => 'S')
							);
		
		$archivo = new wp_usach_cb('FILE_NAME', "archivos/", $columnas);
		$u = $archivo->upLoadFile();
		$sp = 'spu_usach_cb_otras_univ';
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db->BEGIN_TRANSACTION();
		$error = false;
		// procesa el archivo
		$archivo->open();
		$i = 1;
		
		///elimina todos los regisitros where cargado = 'N' and procesado = 'N', usach_cb_otras_univ
		if (!$db->EXECUTE_SP($sp, "'DELETE'")){
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
	    	//print_r($record);
	    	if (is_array($record)) {	// Si es array no existe error
				$rut 				= $record['RUT'];
				$ano_cuota 			= $record['ANO_COBRO'];
				$nro_cuota 			= $record['NRO_CUOTA'];											
				$monto_cuota 		= $record['MONTO_CUOTA'];
				$fecha_pago 		= $record['FECHA_PAGO'];								
				$k_utm_usach 		= $record['MONTO_CAPITAL_UTM_USACH'];
				$k_pesos_usach 		= $record['MONTO_CAPITAL_PESOS_USACH'];
				$i_utm_usach 		= $record['MONTO_INTERES_UTM_USACH'];
				$i_pesos_usach 		= $record['MONTO_INTERES_PESOS_USACH'];
				$dscto_usach_utm 	= $record['DESCTO_UTM_USACH'];
				$dscto_usach_pesos 	= $record['DESCTO_PESOS_USACH'];
				$k_utm_otras_ues 	= $record['MONTO_CAPITAL_UTM_OTRAS_UES'];
				$k_pesos_otras_ues 	= $record['MONTO_CAPITAL_PESOS_OTRAS_UES'];
				$i_utm_otras_ues	= $record['MONTO_INTERES_UTM_OTRAS_UES'];
				$i_pesos_otras_ues 	= $record['MONTO_INTERES_PESOS_OTRAS_UES'];
				$dscto_utm_otras_ues= $record['DESCTO_UTM_OTRAS_UES'];
				$dscto_pesos_otras_ues 	= $record['DESCTO_PESOS_OTRAS_UES'];
				
				//$fecha_pago
	        	$fecha_pago 	= $this->str2date($fecha_pago); 
				
               	$param = "'INSERT'
               				,'$rut'
							,$ano_cuota
							,$nro_cuota	
							,$monto_cuota
							,$fecha_pago	
							,$k_utm_usach
							,$k_pesos_usach
							,$i_utm_usach
							,$i_pesos_usach
							,$dscto_usach_utm
							,$dscto_usach_pesos
							,$k_utm_otras_ues
							,$k_pesos_otras_ues
							,$i_utm_otras_ues
							,$i_pesos_otras_ues
							,$dscto_utm_otras_ues
							,$dscto_pesos_otras_ues
							,$fecha_deposito";
				
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
		//BITACORA CARGA ARCHIVO
		$nom_archivo_cargado = $_FILES['FILE_NAME']['name'];
		$sp_carga_archivo 		= 'spu_carga_archivo';
		$param_carga_archivo	= self::K_CARGA_BANCO."
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

	function cb_orsan($fecha_deposito, $cod_origen_carga_banco){
		$columnas = array(	'RUT'							=> array('TIPO' => 'NUMBER',	'FORMATO' => '10',				'NULL' => 'S')
							,'DIG_VERIF' 					=> array('TIPO' => 'TEXT',   	'FORMATO' => '1',				'NULL' => 'S')
							,'NOM_DEUDOR' 					=> array('TIPO' => 'TEXT',   	'FORMATO' => '100',				'NULL' => 'S')
							,'RUT_UNIVERSIDAD'				=> array('TIPO' => 'TEXT',   	'FORMATO' => '15',				'NULL' => 'S')
							,'NOM_UNIVERSIDAD' 				=> array('TIPO' => 'TEXT',   	'FORMATO' => '100',				'NULL' => 'S')
							,'MONTO_UTM'					=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14',				'NULL' => 'S')
							,'ANO_CUOTA'					=> array('TIPO' => 'NUMBER',   	'FORMATO' => '10',				'NULL' => 'S')							
							,'NRO_COMPROBANTE' 				=> array('TIPO' => 'NUMBER',   	'FORMATO' => '15',				'NULL' => 'S')
							,'NRO_CUOTA' 					=> array('TIPO' => 'NUMBER',   	'FORMATO' => '10',				'NULL' => 'S')
							,'FECHA_PAGO' 					=> array('TIPO' => 'TEXT',   	'FORMATO' => '10',				'NULL' => 'S')
							,'PORC_USACH' 					=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14.4',			'NULL' => 'S')
							,'MONTO_CAPITAL_UTM' 			=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14.4',				'NULL' => 'S')
							,'MONTO_CAPITAL_PESOS' 			=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14',				'NULL' => 'S')
							,'MONTO_INTERES_UTM' 			=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14.4',				'NULL' => 'S')
							,'MONTO_INTERES_PESOS' 			=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14',			'NULL' => 'S')
							,'MONTO_DESCTO_UTM' 			=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14.4',				'NULL' => 'S')
							,'MONTO_DESCTO_PESOS' 			=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14',			'NULL' => 'S')
							,'MONTO_TOTAL_PESOS' 			=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14',				'NULL' => 'S')
							,'MONTO_TOTAL_UTM' 				=> array('TIPO' => 'NUMBER',   	'FORMATO' => '14.4',			'NULL' => 'S')
							,'COD_DEUDA' 					=> array('TIPO' => 'NUMBER',   	'FORMATO' => '10',				'NULL' => 'S')
							,'COD_TIPO_DEUDA' 				=> array('TIPO' => 'TEXT',   	'FORMATO' => '10',				'NULL' => 'S')
							,'FECHA_VENCTO' 				=> array('TIPO' => 'TEXT',   	'FORMATO' => '10',			'NULL' => 'S')
							,'TIPO_COBRO'					=> array('TIPO' => 'TEXT',   	'FORMATO' => '100',				'NULL' => 'S')
							);
		
		$archivo = new wp_usach_cb('FILE_NAME', "archivos/", $columnas);
		$u = $archivo->upLoadFile();
		$sp = 'spu_usach_cb_orsan';
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db->BEGIN_TRANSACTION();
		$error = false;
		// procesa el archivo
		$archivo->open();
		$i = 1;
		
		///elimina todos los regisitros where cargado = 'N' and procesado = 'N', usach_cb_orsan
		if (!$db->EXECUTE_SP($sp, "'DELETE'")){
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
	    	//print_r($record);
	    	if (is_array($record)) {	// Si es array no existe error
				$rut 				= $record['RUT'];
				$dig_verif			= $record['DIG_VERIF'];
				$nom_deudor			= $record['NOM_DEUDOR'];
				$rut_universisidad	= $record['RUT_UNIVERSIDAD'];
				$nom_universisidad	= $record['NOM_UNIVERSIDAD'];
				$monto_utm			= $record['MONTO_UTM'];							
				$ano_cuota			= $record['ANO_CUOTA'];			
				$nro_comprobante	= $record['NRO_COMPROBANTE'];
				$nro_cuota			= $record['NRO_CUOTA'];
				$fecha_pago			= $record['FECHA_PAGO'];
				$porc_usach			= $record['PORC_USACH'];
				$monto_capital_utm	= $record['MONTO_CAPITAL_UTM'];			
				$monto_capital_pesos= $record['MONTO_CAPITAL_PESOS'];			
				$monto_interes_utm	= $record['MONTO_INTERES_UTM'];
				$monto_interes_pesos= $record['MONTO_INTERES_PESOS'];
				$monto_descto_utm	= $record['MONTO_DESCTO_UTM'];
				$monto_descto_pesos	= $record['MONTO_DESCTO_PESOS'];
				$monto_total_pesos	= $record['MONTO_TOTAL_PESOS'];
				$monto_total_utm	= $record['MONTO_TOTAL_UTM'];
				$cod_deuda			= $record['COD_DEUDA'];
				$cod_tipo_deuda		= $record['COD_TIPO_DEUDA'];
				$fecha_vencto		= $record['FECHA_VENCTO'];			
				$tipo_cobro			= $record['TIPO_COBRO'];
				
				$rut				= ($rut == '') ? "NULL" : $rut;
	        	$dig_verif			= ($dig_verif == '') ? "NULL" : "'$dig_verif'";
	        	$nom_deudor			= ($nom_deudor == '') ? "NULL" : "'$nom_deudor'";
	        	$rut_universisidad 	= ($rut_universisidad == '') ? "NULL" : "'$rut_universisidad'";
	        	$nom_universisidad	= ($nom_universisidad == '') ? "NULL" : "'$nom_universisidad'";
	        	$monto_utm			= ($monto_utm == '') ? "NULL" : $monto_utm;
	        	$ano_cuota			= ($ano_cuota == '') ? "NULL" : $ano_cuota;
	        	$nro_comprobante	= ($nro_comprobante == '') ? "NULL" : $nro_comprobante;
	        	$nro_cuota			= ($nro_cuota == '') ? "NULL" : $nro_cuota;
	        	$porc_usach			= ($porc_usach == '') ? 0.0 : $porc_usach;
	        	$monto_capital_utm	= ($monto_capital_utm == '') ? 0.0 : $monto_capital_utm;
	        	$monto_capital_pesos= ($monto_capital_pesos == '') ? 0 : $monto_capital_pesos;
	        	$monto_interes_utm	= ($monto_interes_utm == '') ? 0.0 : $monto_interes_utm;
	        	$monto_interes_pesos= ($monto_interes_pesos == '') ? 0 : $monto_interes_pesos;
	        	$monto_descto_utm	= ($monto_descto_utm == '') ? 0.0 : $monto_descto_utm;
	        	$monto_descto_pesos	= ($monto_descto_pesos == '') ? 0 : $monto_descto_pesos;
	        	$monto_total_pesos	= ($monto_total_pesos == '') ? 0 : $monto_total_pesos;
	        	$monto_total_utm	= ($monto_total_utm == '') ? 0.0 : $monto_total_utm;
	        	$cod_deuda			= ($cod_deuda == '') ? 0 : $cod_deuda;
	        	$cod_tipo_deuda		= ($cod_tipo_deuda == '') ? "NULL" : "'$cod_tipo_deuda'";
	        	$tipo_cobro			= ($tipo_cobro == '') ? "NULL" : "'$tipo_cobro'";
							
				
				//$fecha_pago
	        	$fecha_pago 	= $this->str2date($fecha_pago);
	        	$fecha_vencto 	= $this->str2date($fecha_vencto); 
				
               	$param =  "'INSERT'
               				,$rut
							,$dig_verif
							,$nom_deudor			
							,$rut_universisidad	
							,$nom_universisidad	
							,$monto_utm										
							,$ano_cuota						
							,$nro_comprobante	
							,$nro_cuota			
							,$fecha_pago			
							,$porc_usach			
							,$monto_capital_utm				
							,$monto_capital_pesos			
							,$monto_interes_utm	
							,$monto_interes_pesos
							,$monto_descto_utm	
							,$monto_descto_pesos	
							,$monto_total_pesos	
							,$monto_total_utm	
							,$cod_deuda			
							,$cod_tipo_deuda		
							,$fecha_vencto					
							,$tipo_cobro
							,$fecha_deposito
							,$cod_origen_carga_banco";
				
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
		//BITACORA CARGA ARCHIVO
		$nom_archivo_cargado = $_FILES['FILE_NAME']['name'];
		$sp_carga_archivo 		= 'spu_carga_archivo';
		$param_carga_archivo	= self::K_CARGA_BANCO."
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

	function cb_mineduc(){
		$columnas = array(	'CT_RUT'				=> array('TIPO' => 'TEXT',		'FORMATO' => '20',		'NULL' => 'N')
							,'CT_TOT_SOLICITA' 		=> array('TIPO' => 'TEXT',   	'FORMATO' => '20',		'NULL' => 'N')
							,'CT_UNI_SOLICITA' 		=> array('TIPO' => 'TEXT',   	'FORMATO' => '20',		'NULL' => 'N')
							,'CT_PORCEN_UNI'		=> array('TIPO' => 'TEXT',   	'FORMATO' => '20',		'NULL' => 'N')
							,'CT_RUT_UNI_SOL' 		=> array('TIPO' => 'TEXT',   	'FORMATO' => '20',		'NULL' => 'N')
							,'CT_RUT_UNI_RET'		=> array('TIPO' => 'TEXT',   	'FORMATO' => '20',		'NULL' => 'N')
							);
		
		$archivo = new wp_usach_cb('FILE_NAME', "archivos/", $columnas);
		$u = $archivo->upLoadFile();
		$sp = 'spu_usach_cb_mineduc';
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db->BEGIN_TRANSACTION();
		$cod_usario	= session::get('COD_USUARIO');
		$error = false;
		// procesa el archivo
		$archivo->open();
		$i = 1;
		
		///////////////
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT COUNT(*) COUNT 
				FROM USACH_CB_MINEDUC
				WHERE CT_ANO = TO_NUMBER(TO_CHAR(SYSDATE, 'YYYY'), '9999')";
		
		$result = $db->build_results($sql);
		$cantidad = $result[0]['COUNT'];
		
		if($cantidad > 0){
			$this->alert('En un proceso anterior se realizo la carga Mineduc para el año actual.\n\n¡No se puede puede volver a cargar el mismo año!');
			$this->redraw();
			return;
		}
		while (!$archivo->feof()){
			if (count($archivo->error) == $archivo->K_MAX_ERROR) {
				$this->alert('Demasiados errores, no se seguira cargando el archivo.');
				break;
			}
	    	$record = $archivo->read_record($i); 
	    	//print_r($record);
	    	if (is_array($record)) {	// Si es array no existe error
				$ct_rut				= str_replace('.','',$record['CT_RUT']);
				$ct_tot_solicita	= str_replace('.','',$record['CT_TOT_SOLICITA']);
				$ct_tot_solicita	= str_replace(',','.',$ct_tot_solicita);
				
				$ct_uni_solicita	= str_replace('.','',$record['CT_UNI_SOLICITA']);
				$ct_uni_solicita	= str_replace(',','.',$ct_uni_solicita);
				$ct_porcen_uni		= str_replace(',','.',$record['CT_PORCEN_UNI']);
				$ct_rut_uni_sol		= str_replace('.','',$record['CT_RUT_UNI_SOL']);
				$ct_rut_uni_ret		= str_replace('.','',$record['CT_RUT_UNI_RET']);
				
				$ct_rut				= ($ct_rut == '') ? "NULL" : $ct_rut;
	        	$ct_tot_solicita	= ($ct_tot_solicita == '') ? "NULL" : $ct_tot_solicita;
	        	$ct_uni_solicita	= ($ct_uni_solicita == '') ? "NULL" : $ct_uni_solicita;
	        	$ct_porcen_uni 		= ($ct_porcen_uni == '') ? "NULL" : $ct_porcen_uni;
	        	$ct_rut_uni_sol		= ($ct_rut_uni_sol == '') ? "NULL" : $ct_rut_uni_sol;
	        	$ct_rut_uni_ret		= ($ct_rut_uni_ret == '') ? "NULL" : $ct_rut_uni_ret;

	        	$param =  "'INSERT'
               				,$ct_rut
							,$ct_tot_solicita
							,$ct_uni_solicita			
							,$ct_porcen_uni	
							,$ct_rut_uni_sol	
							,$ct_rut_uni_ret";

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
		
		if (!$db->EXECUTE_SP($sp, "'EJEC_PROCESO',NULL,NULL,NULL,NULL,NULL,NULL,$this->cod_proceso,$cod_usario")){
		    		$error = true;
					$db->ROLLBACK_TRANSACTION();
					$error_sp = $db->GET_ERROR();
					$this->alert('No se pudo grabar el registro.\n\n'.$db->make_msg_error_bd());
					//break;				
		 }
						
		$archivo->close();
		//BITACORA CARGA ARCHIVO
		$nom_archivo_cargado = $_FILES['FILE_NAME']['name'];
		$sp_carga_archivo 		= 'spu_carga_archivo';
		$param_carga_archivo	= self::K_CARGA_BANCO."
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
	
	function inicia_proceso(){
	    $cod_origen_carga_banco = $_POST['COD_ORIGEN_CARGA_BANCO_0'];
	    $cod_usuario= $this->cod_usuario;
	    $cod_proceso = 19;
	    
	    $param = "'NEW'
				,null
				,$cod_proceso
				,$cod_usuario
				,null
				,null";
	    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	    $sp="spu_ejecucion_proceso";
	    if ($db->EXECUTE_SP($sp, $param)){
	        $cod_ejecucion_proceso =$db->GET_IDENTITY('ejecucion_proceso');
	    }
	    else{
	        $cod_ejecucion_proceso = "null";
	    }
	    
	    if ($cod_origen_carga_banco == self::K_ORIGEN_CB_DESARROLLO)//BANCO SANTANDER
	        $sp_proceso = "spp_usach_cb_desarrollo ($cod_ejecucion_proceso)";// con "()" cuando se oracle
        if ($cod_origen_carga_banco == self::K_ORIGEN_OTRAS_UES)//OTRAS UNIVERSIDADES
            $sp_proceso = "spp_usach_cb_otras_univ ($cod_ejecucion_proceso)";
        if ($cod_origen_carga_banco == self::K_ORIGEN_CB_TRANSBANK)//TRANSBANK
            $sp_proceso = "spp_usach_cb_transbank ($cod_ejecucion_proceso)";
        if ($cod_origen_carga_banco == self::K_ORIGEN_CB_TGR)//TESORERIA GENERAL DE LA REPUBLICA
            $sp_proceso = "spp_usach_cb_tgr ($cod_ejecucion_proceso)";
        if ($cod_origen_carga_banco == self::K_ORIGEN_CB_FUNCIONARIO)//FUNCIONARIO
            $sp_proceso = "spp_usach_cb_funcionario ($cod_ejecucion_proceso)";
        if ($cod_origen_carga_banco == self::K_ORIGEN_CB_SERVIPAG)//SERVIPAG
            $sp_proceso = "spp_usach_cb_servipag ($cod_ejecucion_proceso)";
        if ($cod_origen_carga_banco == self::K_ORIGEN_CB_SANTANDER)
            $sp_proceso = "spp_usach_cb_santander_nuevo ($cod_ejecucion_proceso)";
        if ($cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_CR_CD OR $cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_CR_CDE
            OR $cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_CRUZ19287 OR $cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_CRUZ19848
            OR $cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_DEU_PROP OR $cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_MOROSO
            OR $cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_REZAGADO OR $cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_TGR)//ORSAN
            $sp_proceso = "spp_usach_cb_orsan ($cod_ejecucion_proceso, $cod_origen_carga_banco)";
        if ($cod_origen_carga_banco == self::K_ORIGEN_CB_MINEDUC)
            $sp_proceso = "spp_usach_cb_mineduc ($cod_ejecucion_proceso)";
            
        if ($cod_origen_carga_banco == self::K_ORIGEN_ABONO_DESCTO)
            $sp_proceso = "spp_cb_abono_descto ($cod_ejecucion_proceso)";
        
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
        $stid = oci_parse($conn, "begin $sp_proceso; end;");
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
		if(isset($_POST['b_cargar'])){ 
			$this->dws['dw_carga_banco']->get_values_from_POST();
			$cod_origen_carga_banco = $_POST['COD_ORIGEN_CARGA_BANCO_0'];
			
			if ($cod_origen_carga_banco == self::K_ORIGEN_CB_HUARA){//BANCO USACH_CB_HUARA
				$this->usach_cb_huara();
			}
			if ($cod_origen_carga_banco == self::K_ORIGEN_CB_DESARROLLO){//BANCO DESARROLLO
				$fecha_deposito = $this->dws['dw_carga_banco']->get_item(0, 'FECHA_DEPOSITO');
				$fecha_deposito = $this->str2date($fecha_deposito);
				$this->cb_desarrollo($fecha_deposito);				 	
			}	
			if ($cod_origen_carga_banco == self::K_ORIGEN_OTRAS_UES){//OTRAS UNIVERSIDADES
				$fecha_deposito = $this->dws['dw_carga_banco']->get_item(0, 'FECHA_DEPOSITO');
				$fecha_deposito = $this->str2date($fecha_deposito);
				$this->cb_otras_ues($fecha_deposito);
			}	
			if ($cod_origen_carga_banco == self::K_ORIGEN_CB_TRANSBANK)
				$this->cb_transbank();
			if ($cod_origen_carga_banco == self::K_ORIGEN_CB_TGR){//TESORERIA GENERAL DE LA REPUBLICA
				$fecha_deposito = $this->dws['dw_carga_banco']->get_item(0, 'FECHA_DEPOSITO');
				$fecha_deposito = $this->str2date($fecha_deposito);
				$this->cb_tgr($fecha_deposito);				 	
			}
			if ($cod_origen_carga_banco == self::K_ORIGEN_CB_FUNCIONARIO){//FUNCIONARIO
			    
				$fecha_deposito = $this->dws['dw_carga_banco']->get_item(0, 'FECHA_DEPOSITO');
				$fecha_deposito = $this->str2date($fecha_deposito);
				$this->cb_funcionario($fecha_deposito);				 	
			}
			if ($cod_origen_carga_banco == self::K_ORIGEN_CB_SERVIPAG){//SERVIPAG
				$fecha_deposito = $this->dws['dw_carga_banco']->get_item(0, 'FECHA_DEPOSITO');
				$fecha_deposito = $this->str2date($fecha_deposito);
				$this->cb_servipag($fecha_deposito);				 	
			}
			if ($cod_origen_carga_banco == self::K_ORIGEN_CB_SANTANDER){//SANTANDER
				$fecha_deposito = $this->dws['dw_carga_banco']->get_item(0, 'FECHA_DEPOSITO');
				$fecha_deposito = $this->str2date($fecha_deposito);
				$this->cb_santander_nuevo($fecha_deposito);				 	
			}
			if ($cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_CR_CD OR $cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_CR_CDE
				OR $cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_CRUZ19287 OR $cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_CRUZ19848
				OR $cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_DEU_PROP OR $cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_MOROSO
				OR $cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_REZAGADO OR $cod_origen_carga_banco == self::K_ORIGEN_CB_ORSAN_TGR){//ORSAN
				$fecha_deposito = $this->dws['dw_carga_banco']->get_item(0, 'FECHA_DEPOSITO');
				$fecha_deposito = $this->str2date($fecha_deposito);
				$this->cb_orsan($fecha_deposito, $cod_origen_carga_banco);				 	
			}
			if ($cod_origen_carga_banco == self::K_ORIGEN_CB_MINEDUC)
				$this->cb_mineduc();
		}else if(isset($_POST['b_procesar'])){
		    $this->inicia_proceso();
		}
		else 
			parent::procesa_event();
	}
}
?>