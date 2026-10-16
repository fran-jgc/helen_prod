<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

 class dlg_carga_banco_base  extends wi_carga_banco{
 	 	
 	function __construct(){
 		
	$temp = new Template_appl('dlg_carga_banco.htm');		
	print $temp->toString();
 	
 	}
 	
 	
  function cb_banco(){
  		$columnas = array(	'COD_ITEM_CARGA_BANCO' 		=> array('TIPO' => 'NUMBER',	'FORMATO' => '12',			'NULL' => 'N')
							,'FECHA_CARGA_BANCO'		=> array('TIPO' => 'DATE',   	'FORMATO' => '3',			'NULL' => 'N')
							,'RUT_ARCHIVO'		 		=> array('TIPO' => 'NUMBER',	'FORMATO' => '12',			'NULL' => 'N')
							,'FOLIO'			 		=> array('TIPO' => 'NUMBER',	'FORMATO' => '12',			'NULL' => 'N')
							,'COD_ESTADO_CARGA' 		=> array('TIPO' => 'NUMBER',	'FORMATO' => '12',			'NULL' => 'N')
							,'MONTO_CAPITAL'			=> array('TIPO' => 'NUMBER',	'FORMATO' => '12',			'NULL' => 'N')
							,'MONTO_INTERES'			=> array('TIPO' => 'NUMBER',	'FORMATO' => '12',			'NULL' => 'N')
							,'MONTO_PENAL'				=> array('TIPO' => 'NUMBER',	'FORMATO' => '12',			'NULL' => 'N')
							,'OTROS_COBROS'				=> array('TIPO' => 'NUMBER',	'FORMATO' => '12',			'NULL' => 'N')
							,'FECHA_VENCTO'				=> array('TIPO' => 'NUMBER',	'FORMATO' => '12',			'NULL' => 'N')
							,'FECHA_PAGO'				=> array('TIPO' => 'NUMBER',	'FORMATO' => '12',			'NULL' => 'N')
							,'MONTO_PESOS'				=> array('TIPO' => 'NUMBER',	'FORMATO' => '12',			'NULL' => 'N')
							
							);
	
		$archivo = new archivo_datos('FILE_NAME', "archivos/", $columnas);	
		$u = $archivo->upLoadFile();
		$sp = 'spu_carga_deudor_cartera';//hacer spu 
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$this->fecha_carga = $this->str2date($db->current_date(), $db->current_time());
		$db->BEGIN_TRANSACTION();
		
		$error = false;
		// procesa el archivo
		$archivo->open();
		$i = 1;
		
		/*if (!$db->EXECUTE_SP($sp, "'DELETE'")){
	    			$error = true;
					$db->ROLLBACK_TRANSACTION();
					$error_sp = $db->GET_ERROR();
					$this->alert('No se pudo grabar el registro.\n\n'.$db->make_msg_error_bd());
					break;				
	    }	*/
				        
		while (!$archivo->feof()){
			/*if (count($archivo->error) == $archivo->K_MAX_ERROR) {
				//$this->alert('Demasiados errores, no se seguira cargando el archivo.');
				//break;
			}*/
	    	$record = $archivo->read_record($i); 
			/*if (!is_array($record)){
				//$archivo->registra_error("El archivo a Cargar tiene saltos de Lineas en Blanco.");
			}*/
			
	    	//print_r($record);
	    	if (is_array($record)) {	// Si es array no existe error
				$cod_carga_banco		= $record['COD_CARGA_BANCO'];
				$fecha_carg_banco		= $record['FECHA_CARGA_BANCO'];
				$rut_archivo			= $record['RUT_ARCHIVO'];
				$folio					= $record['FOLIO'];
				$cod_estado_carga		= $record['COD_ESTADO_CARGA'];
				$monto_capital			= $record['MONTO_CAPITAL'];
				$monto_interes			= $record['MONTO_INTERES'];
				$monto_penal			= $record['MONTO_PENAL'];
				$otros_cobros			= $record['OTROS_COBROS'];
				$fecha_vencto			= $record['FECHA_VENCTO'];
				$fecha_pago				= $record['FECHA_PAGO'];
				$monto_pesos			= $record['MONTO_PESOS'];
				
	
				
				
				//$cod_deudor			= ($cod_deudor == '') ? 0 : $cod_deudor;
				//$cod_universidad	= ($cod_universidad == '') ? 0 : $cod_universidad;
				
				if(is_numeric($cod_deudor) and is_numeric($cod_universidad)){
			/*		// VERIFICA SI EL DEUDOR ESTA EN ALGUNA CARTERA VIGENTE
					$sql = "SELECT COUNT(*) CANT 
							FROM CARTERA C, DEUDOR_CARTERA DC, DEUDOR_ALIANSE DA
							WHERE C.COD_CARTERA = DC.COD_CARTERA 
							AND   DA.COD_DEUDOR_ALIANSE = DC.COD_DEUDOR_ALIANSE
							AND   (C.FECHA_INICIO <= GETDATE() AND C.FECHA_TERMINO >=GETDATE())
							AND   C.COD_ESTADO_CARTERA = 2
							AND   DA.COD_DEUDOR = $cod_deudor
							AND   DA.COD_UNIVERSIDAD = $cod_universidad";
		      		$result = $db->build_results($sql);
		      		if($result[0]['CANT']==0){
						if($cod_universidad <> $cod_universidad_input){
							print "<script language='JavaScript'>
					                alert('Deudores no pertenece a la Universidad.');
					                </script>";
							break;	
						}else{
							$cod_cartera  = $this->get_key();			
		               		$param =   "'INSERT'
		               				,$cod_deudor
						        	,$cod_cartera";
						
							if (!$db->EXECUTE_SP($sp, $param)){
					    		$error = true;
								$db->ROLLBACK_TRANSACTION();
								$error_sp = $db->GET_ERROR();
								$this->alert('No se pudo grabar el registro.\n\n'.$db->make_msg_error_bd());
								break;				
					    	}
						}
		      		}*/
				}
	    	}
    		$i++;
		}
						
		$archivo->close();
		
		$archivo->renFile();
		
		if (!$error) {
			$db->COMMIT_TRANSACTION();
		}

		$archivo->display_error();	// Muestra los errores si existen
		
		$this->archivo_cargado = true;
		//$this->current_tab_page = 1;
		$this->redraw();	
  	
  	}
 }
 
$clase = new dlg_carga_banco_base();

  	if(isset($_POST['b_cargar'])){
  		//header("Location: class_wi_carga_banco.php");
  		//$clase->cb_banco();//funcion carga archivo
  		//obtener el codigo de la tabla padre
  		//$this->dws['dw_cambio_documento']->retrieve($cod_cambio_documento);
  		$clase->load_record();		
  	}
  	

 
 
?>