<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_cartola_banco extends w_output {
    function __construct() {
		$this->b_add_visible  = false;
		$sql = sql_cartola_banco::get_sql('OUTPUT');
		parent::__construct('cartola_banco', $sql, $_REQUEST['cod_item_menu']);
		
		// headers
		$COD_CARTOLA = new header_num('COD_CARTOLA','COD_CARTOLA', 'Código');
		$FECHA_REGISTRO = new header_date('FECHA_REGISTRO','FECHA_REGISTRO','Fecha Registro');
		$NOM_USUARIO = new header_text('NOM_USUARIO','U.NOM_USUARIO','Usuario');
		$NOM_CUENTA_CORRIENTE = new header_text('NOM_CUENTA_CORRIENTE','CTA.NOM_CUENTA_CORRIENTE','Cta. Corriente');
		$NRO_CARTOLA = new header_num('NRO_CARTOLA','NRO_CARTOLA','Nro. Cartola');
		$FECHA_CARTOLA = new header_date('FECHA_CARTOLA','FECHA_CARTOLA','Fecha Cartola');
		
		$this->add_header($COD_CARTOLA);
		$this->add_header($FECHA_REGISTRO);
		$this->add_header($NOM_USUARIO);
		$this->add_header($NOM_CUENTA_CORRIENTE);
		$this->add_header($NRO_CARTOLA);
		$this->add_header($FECHA_CARTOLA);
		
	}
	
	function habilita_boton(&$temp, $boton, $habilita) {
		if ($boton=='load' && $habilita)
			$temp->setVar("WO_".strtoupper($boton), '<input name="b_'.$boton.'" id="b_'.$boton.'" src="../../../../commonlib_seven/trunk/images/b_'.$boton.'.jpg" type="image" '.
																							'onMouseDown="MM_swapImage(\'b_'.$boton.'\',\'\',\'../../../../commonlib_seven/trunk/images/b_'.$boton.'_click.jpg\',1)" '.
																							'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
																							'onMouseOver="MM_swapImage(\'b_'.$boton.'\',\'\',\'../../../../commonlib_seven/trunk/images/b_'.$boton.'_over.jpg\',1)" '.
																							'onClick="if(validate_load()==false) return false; if(dlg_cta_corriente()==false) return false;"/>');
		else
			parent::habilita_boton($temp, $boton, $habilita);
	}
	
	function redraw(&$temp) {
		parent::redraw($temp);
		$this->habilita_boton($temp, 'load', true);		
	}
	
	function load_cartola() {
		$cod_cta_corriente = $_POST['wo_hidden'];
		
		$columnas = array('NRO_CARTOLA' 		=> array('TIPO' => 'NUMBER', 'FORMATO' => '10'	,'NULL' => 'N')
						,'FECHA_CARTOLA' 		=> array('TIPO' => 'DATE',   'FORMATO' => 'dd/mm/yyyy',	'NULL' => 'N')
				 		,'DETALLE_MOVIMIENTO'	=> array('TIPO' => 'TEXT',   'FORMATO' => '10'	,'NULL' => 'S')
				 		,'CHEQUE_CARGO'			=> array('TIPO' => 'NUMBER', 'FORMATO' => '10'	,'NULL' => 'S')
				 		,'DEPOSITO_ABONO'		=> array('TIPO' => 'NUMBER', 'FORMATO' => '10'	,'NULL' => 'S')
				 		,'SALDO'				=> array('TIPO' => 'NUMBER', 'FORMATO' => '10'	,'NULL' => 'S')
				 		,'SUCURSAL'				=> array('TIPO' => 'TEXT',	 'FORMATO' => '10'  ,'NULL' => 'S'));
	 	
				 		
		$archivo = new archivo_datos('FILE_NAME', "archivos/", $columnas);
		$u = $archivo->upLoadFile();
		$sp = 'sp_cartola';
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db->BEGIN_TRANSACTION();
							 		
	 	$error = false;
		// procesa el archivo
		$archivo->open();
		$i = 1;
		
		
		if (!$db->EXECUTE_SP($sp, "'DELETE'")){
	    			$error = true;
					$db->ROLLBACK_TRANSACTION();
					$error_sp = $db->GET_ERROR();
					$this->alert('No se pudo grabar el registro.\n\n'.$db->make_msg_error_bd());
					break;				
	    }	
		
		while (!$archivo->feof()) {
			if (count($archivo->error) == $archivo->K_MAX_ERROR) {
				$this->alert('Demasiados errores, no se seguira cargando el archivo.');
				break;
			}
	    	$record = $archivo->read_record($i);
	    	if (is_array($record)) {	// Si es array no existe error
		    	$nro_cartola 		= $record['NRO_CARTOLA'];
		  		$fecha_cartola 		= $record['FECHA_CARTOLA'];
		  		$detalle_movimiento	= $record['DETALLE_MOVIMIENTO'];
		  		$cheque_cargo	= $record['CHEQUE_CARGO'];
		  		$deposito_abono	= $record['DEPOSITO_ABONO'];
		  		$saldo		= $record['SALDO'];
		  		$sucursal	= $record['SUCURSAL'];
		  		
				$fecha_cartola	= ($fecha_cartola =='') ? "NULL" : "$fecha_cartola";
				$detalle_movimiento	= ($detalle_movimiento =='') ? "NULL" : "$detalle_movimiento";
				$cheque_cargo	= ($cheque_cargo =='') ? "NULL" : "$cheque_cargo";
				$deposito_abono	= ($deposito_abono =='') ? "NULL" : "$deposito_abono";
				$saldo	= ($saldo =='') ? "NULL" : "$saldo";
				$sucursal	= ($sucursal =='') ? "NULL" : "$sucursal";
		  		
				$fecha_cartola = $this->str2date($fecha_cartola);

				$operacion = 'UPDATE';
				
				$param = "'$operacion' 
						,$nro_cartola
						,$fecha_cartola
						,'$detalle_movimiento'
						,$cheque_cargo
						,$deposito_abono
						,$saldo
						,'$sucursal'";
						
	    		if (!$db->EXECUTE_SP($sp, $param)) {
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
		if (!$error) {
			$cod_usario = $this->cod_usuario;
			$sp = 'sp_item_cartola';
			$param = "$cod_usario
					 ,'$cod_cta_corriente'";
			if (!$db->EXECUTE_SP($sp, $param)) {
    			$error = true;
				$db->ROLLBACK_TRANSACTION();
				$error_sp = $db->GET_ERROR();
				$this->alert('No se pudo grabar el registro.\n\n'.$db->make_msg_error_bd());
				break;
    		}
			
			$db->COMMIT_TRANSACTION();
		}
		$archivo->delFile();
		$this->retrieve();
		$archivo->display_error();	// Muestra los errores si existen
		
	}
	
	function procesa_event() {		
		if(isset($_POST['b_load_x'])){
			$this->load_cartola();
		}else
			parent::procesa_event();
	}
	
}
?>