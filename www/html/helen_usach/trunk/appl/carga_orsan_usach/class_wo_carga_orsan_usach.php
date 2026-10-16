<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../appl.ini");

class wo_carga_orsan_usach extends w_output {
	function __construct() {
		
	$sql = sql_carga_orsan_usach::get_sql('OUTPUT'); 
	parent::__construct('carga_orsan_usach', $sql, $_REQUEST['cod_item_menu']);
	
		// headers
	   $COD_CARGA_ORSAN_USACH = new header_num('COD_CARGA_ORSAN_USACH', 'COD_CARGA_ORSAN_USACH', 'Carga Orsan');
	   $FECHA_REGISTRO = new header_date('FECHA_REGISTRO', 'FECHA_REGISTRO', 'Fecha Registro');
	   $FECHA_CARGA_ORSAN = new header_date('FECHA_CARGA_ORSAN', 'FECHA_CARGA_ORSAN', 'Fecha Carga');
	   $TOTAL_REG_CARGA = new header_num('TOTAL_REG_CARGA', 'TOTAL_REG_CARGA', 'Total Registros');
	   $TOTAL_REG_EXITOSO = new header_num('TOTAL_REG_EXITOSO', 'TOTAL_REG_EXITOSO', 'Total Exitoso');
	   $TOTAL_REG_PDTE = new header_num('TOTAL_REG_PDTE', 'TOTAL_REG_PDTE', 'Total Pendientes');
	   $NOM_USUARIO = new header_text('NOM_USUARIO', 'NOM_USUARIO', 'Usuario');
	   
	   $this->add_header($COD_CARGA_ORSAN_USACH);
	   $this->add_header($FECHA_REGISTRO);
	   $this->add_header($FECHA_CARGA_ORSAN);
	   $this->add_header($TOTAL_REG_CARGA);		
	   $this->add_header($TOTAL_REG_EXITOSO);
	   $this->add_header($TOTAL_REG_PDTE);
	   $this->add_header($NOM_USUARIO);
	}
	
	function cb_new(){
		//FORMATO ARCHIVO RECAUDACION  		
		$columnas = array( 	'rut'							=> array('TIPO' => 'TEXT',		'FORMATO' => '12',			'NULL' => 'N')
							,'folio'						=> array('TIPO' => 'TEXT',		'FORMATO' => '100',			'NULL' => 'S')
							,'ano_cuota'					=> array('TIPO' => 'NUMBER',	'FORMATO' => '4',			'NULL' => 'S')
							,'cod_deuda'					=> array('TIPO' => 'NUMBER',	'FORMATO' => '10',			'NULL' => 'N')
							,'monto_utm'					=> array('TIPO' => 'NUMBER',	'FORMATO' => '14.4',		'NULL' => 'S')
							,'monto_pesos'					=> array('TIPO' => 'NUMBER',	'FORMATO' => '14',			'NULL' => 'N')
							,'fecha_pago'					=> array('TIPO' => 'DATE',		'FORMATO' => 'dd/mm/yyyy',	'NULL' => 'S')
							,'cod_origen_pago'				=> array('TIPO' => 'NUMBER',	'FORMATO' => '10',			'NULL' => 'N')
							,'fecha_deposito'				=> array('TIPO' => 'DATE',		'FORMATO' => 'dd/mm/yyyy',	'NULL' => 'N')
							,'nro_cuota'					=> array('TIPO' => 'NUMBER',	'FORMATO' => '10', 			'NULL' => 'S')
							,'monto_descto_utm'				=> array('TIPO' => 'NUMBER',	'FORMATO' => '14.4', 		'NULL' => 'S')
							,'monto_descto_pesos'			=> array('TIPO' => 'NUMBER',	'FORMATO' => '14', 			'NULL' => 'S')
							);
	
		$archivo = new archivo_datos('FILE_NAME', "archivos/", $columnas);	
		$u = $archivo->upLoadFile();
		$error = false;
		//comprobar el formato del archivo antes de cargar 
		$archivo->open();
		$x = 1;
		$lista_error = "";
		while (!$archivo->feof()){
			$record = $archivo->read_record($x);			
			if (!is_array($record)){
				$error = true;
				if($record == ""){
				   $record ="El registro esta vacío, borrar salto de linea";
				}
				$error= "<tr><td>ERROR FORMATO $x : $record</td></tr>";
				$lista_error = $lista_error.''.$error;
			}
			$x++;
		}
		$archivo->close();
		
		if($lista_error != ""){
			session::set('lista_error_formato', $lista_error);
			$this->_redraw();
			print "<script>document.getElementById('lista_errores').style.display='';</script>";
			print "<script>document.getElementById('origen_pago').style.display='none';</script>";
			$this->alert('El archivo presenta errores de formato, ver "Lista errores"');
		}
		else
		{
			print "<script>document.getElementById('lista_errores').style.display='none';</script>";
			print "<script>document.getElementById('origen_pago').style.display='';</script>";
		}
		
		if (!$error) {
			//--------insert a carga banco (TABLA PADRE)
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$param = "'INSERT'
					  ,1
					  ,$this->cod_usuario
	                  ,null
	                  ,0
	                  ,0
	                  ,0
	                  ,null";
	                  
	     	$sp="spu_carga_orsan_usach";
			if (!$db->EXECUTE_SP($sp, $param)){
	    		$error = true;
				$db->ROLLBACK_TRANSACTION();
				$error_sp = $db->GET_ERROR();
				$this->alert('No se pudo grabar el registro.\n\n'.$db->make_msg_error_bd());
							
			}
	    	//---------------OBTENGO EL COD_CARGA_BANCO(RECIEN CREADO)	
			//$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$sql= 	"SELECT max(COD_CARGA_ORSAN_USACH) COD_CARGA_ORSAN_USACH
					FROM CARGA_ORSAN_USACH";
			$result = $db->build_results($sql); 
			$COD_CARGA_ORSAN = $result[0]['COD_CARGA_ORSAN_USACH'];
			
			//---------INSERT A LA TABLA ITEM_CARGA_ORSAN(HIJO)
			$sp = 'spu_item_carga_orsan_usach';
			$db->BEGIN_TRANSACTION();
			$error = false;
			$cod_usuario= $this->cod_usuario;
			
			// procesa el archivo
			//$fp_r = fopen($archivo->directorio.$archivo->nombre.".".$archivo->tipoArchivo, "r");//$archivo->open();
			$archivo->open();
			$x=0;
			$i = 1;
			while (!$archivo->feof()){
				$record = $archivo->read_record($i);			
				
				if (is_array($record)) {
					
					$rut					= $record['rut'];
					$cod_deuda				= $record['cod_deuda'];
					$ano_cuota				= $record['ano_cuota'];
					$monto_utm				= $record['monto_utm'];
					$monto_pesos			= $record['monto_pesos'];
					$monto_descto_utm		= $record['monto_descto_utm'];
					$monto_descto_pesos		= $record['monto_descto_pesos'];
					$fecha_pago				= $record['fecha_pago'];
					$cod_origen_pago		= $record['cod_origen_pago'];
					$fecha_deposito			= $record['fecha_deposito'];
					$nro_cuota				= $record['nro_cuota'];
					$folio					= $record['folio'];
					
					$monto_utm 		= str_replace(',', '.', $monto_utm);
					
					//seperar el dig_verif si viene en el rut
					$array_rut = explode("-", $rut);
					$rut =  $array_rut[0]; 
					$dig_verif =  $array_rut[1]; 
					
					$cod_deuda= ($cod_deuda=='') ? "null" : $cod_deuda;
					$ano_cuota= ($ano_cuota=='') ? "null" : $ano_cuota;
					$monto_utm= ($monto_utm=='') ? "null" : $monto_utm;
					$fecha_pago= ($fecha_pago=='') ? "null" : $fecha_pago;
					$fecha_deposito= ($fecha_deposito=='') ? "null" : $fecha_deposito;
					$nro_cuota= ($nro_cuota=='') ? "null" : $nro_cuota;
					$dig_verif= ($dig_verif=='') ? "null" : "'$dig_verif'";
					$cod_origen_pago= ($cod_origen_pago=='') ? "null" : "'$cod_origen_pago'";
					$monto_descto_utm= ($monto_descto_utm=='') ? 0 : "$monto_descto_utm";
					$monto_descto_pesos= ($monto_descto_pesos=='') ? 0 : "$monto_descto_pesos";
					$folio= ($folio=='') ? "null" : "'$folio'";
					
					if($fecha_pago != "null"){
						$fecha_pago 	= str_replace('-', '/', $fecha_pago);
						$fecha_pago 	= "to_date('$fecha_pago', 'dd/mm/yyyy')";
					}
					if($fecha_deposito != "null"){
						$fecha_deposito = str_replace('-', '/', $fecha_deposito);
						$fecha_deposito = "to_date('$fecha_deposito', 'dd/mm/yyyy')";
					}

					$param = 	"'INSERT'
								,null
								,$COD_CARGA_ORSAN
								,1
								,$rut
								,$dig_verif
								,null
								,null
								,null
								,null
								,$ano_cuota
								,null
								,$nro_cuota
								,$fecha_pago
								,null
								,null
								,null
								,null
								,null
								,$monto_descto_utm
								,$monto_descto_pesos
								,$monto_pesos
								,$monto_utm
								,$cod_deuda
								,null
								,null
								,null
								,$cod_origen_pago
								,$fecha_deposito
								,$cod_usuario
								,$folio";
					
					//echo $sp.' '.$param;
					if (!$db->EXECUTE_SP($sp, $param)){
			    		$error = true;
						$db->ROLLBACK_TRANSACTION();
						$error_sp = $db->GET_ERROR();
						$this->alert('No se pudo grabar el registro.\n\n'.$db->make_msg_error_bd());
						break;				
			    	}
		    	}					    	
	    		$i++;
			} //while (!feof($fp_r))
			$archivo->close();
			$param = "'ACTUALIZA_TOTALES'
							,$COD_CARGA_ORSAN
							,$this->cod_usuario
							,$fecha_carga
			                ,0
			                ,0
			                ,0
			                ,null";
			
			/*
			$sp="spu_carga_orsan_usach";
			if (!$db->EXECUTE_SP($sp, $param)){
						    		$error = true;
									$db->ROLLBACK_TRANSACTION();
									$error_sp = $db->GET_ERROR();
									$this->alert('No se pudo grabar el registro.\n\n'.$db->make_msg_error_bd());
									break;				
			}
			
			*/
			                
			if (!$error) {
				$db->COMMIT_TRANSACTION();
				$this->detalle_record_desde(true);// este metodo me envia al input desde el output despues de registrar la carga
			}
		}
		
	}
	
	function procesa_event() {		
		if(isset($_POST['b_load_x'])){
			$this->cb_new();
		}else{
			parent::procesa_event();
		}
	}
	
	function redraw($temp) {
		parent::redraw($temp);
		$this->habilita_boton($temp, 'load', true);		
	}
	
	function habilita_boton(&$temp, $boton, $habilita) {
		if ($boton=='load' && $habilita)
			$temp->setVar("WO_".strtoupper($boton), '<input name="b_'.$boton.'" id="b_'.$boton.'" src="../../../../commonlib_seven/trunk/images/b_'.$boton.'.jpg" type="image" '.
																							'onMouseDown="MM_swapImage(\'b_'.$boton.'\',\'\',\'../../../../commonlib_seven/trunk/images/b_'.$boton.'_click.jpg\',1)" '.
																							'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
																							'onMouseOver="MM_swapImage(\'b_'.$boton.'\',\'\',\'../../../../commonlib_seven/trunk/images/b_'.$boton.'_over.jpg\',1)" '.
																							'onClick="return validate_load();" />');
		else
			parent::habilita_boton($temp, $boton, $habilita);
	
	}
	
}
class archivo_cb extends archivo_datos {
	function __construct($file, $dir, $columnas) {
	    parent::__construct($file, $dir, $columnas);
	}
	function valida_record($record, $num_linea) {
		$record = parent::valida_record($record, $num_linea);
		if (!is_array($record))
			return $record;
	}
}
?>