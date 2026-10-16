<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../appl.ini");

class wo_boleta_base extends w_output {
    
	function __construct() {		
		
		$sql = sql_boleta::get_sql('OUTPUT');
		if(K_CLIENTE == 'UCM'){
			$sql = sql_boleta::get_sql('OUTPUT_UCM1');
		}else if(K_CLIENTE == 'UCM2'){
			$sql = sql_boleta::get_sql('OUTPUT_UCM2');
		}
		
		parent::__construct('boleta', $sql, $_REQUEST['cod_item_menu']);
		
		//formato numeros
		$this->dw->add_control(new static_num('RUT'));
		$this->dw->add_control(new static_num('MONTO_PESOS'));
		
		// headers
		$COD_BOLETA = new header_num('COD_BOLETA', 'COD_BOLETA', 'N° Boleta');
		$FECHA_BOLETA = new header_date('FECHA_BOLETA', 'FECHA_BOLETA', 'Fecha Boleta');
		$FECHA_VENCTO = new header_date('FECHA_VENCTO', 'PP.FECHA_VENCTO', 'Fecha Vencto');
		$MONTO_PESOS = new header_num('MONTO_PESOS', 'MONTO_PESOS', 'A Pagar');
		$COD_DEUDA = new header_num('COD_DEUDA', 'DEUDA.COD_DEUDA', 'Deuda');
		$RUT = new header_rut('RUT', 'DEUDOR', 'Rut');
		$NOMBRE = new header_text('NOMBRE', 'NOMBRE', 'Nombre');
		$APELLIDO_PATERNO = new header_text('APELLIDO_PATERNO', 'APELLIDO_PATERNO', 'Paterno');
		$APELLIDO_MATERNO = new header_text('APELLIDO_MATERNO', 'APELLIDO_MATERNO', 'Materno');
		
		$this->add_header($COD_BOLETA);
		$this->add_header($FECHA_BOLETA);
		$this->add_header($FECHA_VENCTO);
		$this->add_header($MONTO_PESOS);
		$this->add_header($COD_DEUDA);
		$this->add_header($RUT);
		$this->add_header($NOMBRE);
		$this->add_header($APELLIDO_PATERNO);
		$this->add_header($APELLIDO_MATERNO);
		
		$sql_cartera = sql_boleta::get_sql('DD_CARTERA_CONOCIDA');
		$CARTERA_CONOCIDA = new header_drop_down_string('CARTERA_CONOCIDA', 'B.CARTERA_CONOCIDA', 'Boleta Cartera Conocida', $sql_cartera);
		$this->add_header($CARTERA_CONOCIDA);
      	
	}
	
	function export() {
	    require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/libs/simplexlsxgen/src/SimpleXLSXGen.php";
	    ini_set('memory_limit', '-1');
	    ini_set('max_execution_time', 1800);//30 minutos

	    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	    $this->make_filtros();
	    $sql = $this->dw->get_sql();
	    $res = $db->query($sql);

	    $rows = [['<b>N° Boleta</b>', '<b>Fecha Boleta</b>', '<b>Fecha Vencto</b>', '<b>A Pagar</b>', '<b>Deuda</b>', '<b>Rut</b>', '<b>Nombre</b>', '<b>Paterno</b>', '<b>Materno</b>', '<b>Cartera Conocida</b>']];
	    while ($my_row = $db->get_row()) {
	        $rows[] = [$my_row['COD_BOLETA'], $my_row['FECHA_BOLETA'], $my_row['FECHA_VENCTO'], $my_row['MONTO_PESOS'], $my_row['COD_DEUDA'], $my_row['RUT'], $my_row['NOMBRE'], $my_row['APELLIDO_PATERNO'], $my_row['APELLIDO_MATERNO'], $my_row['CARTERA_CONOCIDA']];
	    }

	    if ($db->database_type == "oci") {
	        oci_free_statement($db->query_id);
	        $db->query_id = false;
	    }

	    \Shuchkin\SimpleXLSXGen::fromArray($rows, 'Boletas')->downloadAs('Boletas.xlsx');
	}
}

// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wo_boleta.php";
if (file_exists($file_name)) 
	require_once($file_name);
else {
	class wo_boleta extends wo_boleta_base {
	    function __construct() {
	        parent::__construct(); 
		}
	}
}

?>