<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
ini_set('memory_limit', '480M');
ini_set('max_execution_time', 900);

class wo_abono extends w_output {
	const K_VIGENTE = 1;
	function __construct() {
		$this->b_add_visible  = false;
		$sql = sql_abono::get_sql('OUTPUT');
		//echo $sql;
		parent::__construct('abono', $sql, $_REQUEST['cod_item_menu']);
		
		//formato numeros
		$RUT_S = new static_num('RUT');
		$MONTO_MONEDA_S = new static_num('MONTO_MONEDA',4);
		$MONTO_PESOS_S = new static_num('MONTO_PESOS'); 
		
		$this->dw->add_control($RUT_S);
		$this->dw->add_control($MONTO_MONEDA_S);
		$this->dw->add_control($MONTO_PESOS_S);
		
		// headers
		$COD_INGRESO = new header_num('COD_INGRESO', 'AI.COD_INGRESO', 'N° Ingreso');
		$COD_ABONO = new header_num('COD_ABONO', 'A.COD_ABONO', 'N° Abono');
		$RUT = new header_rut('RUT', 'DEU', 'Rut');
		$COD_DEUDA = new header_num('COD_DEUDA', 'A.COD_DEUDA', 'N° Deuda');
		
		$sql_tipo_deuda = sql_abono::get_sql('DD_COD_TIPO_DEUDA');
		$NOM_TIPO_DEUDA = new header_drop_down_string('NOM_TIPO_DEUDA', 'TD.COD_TIPO_DEUDA', 'Tipo Deuda', $sql_tipo_deuda);
		$ANO_COBRO = new header_num('ANO_COBRO', 'ANO_COBRO', 'Año');
		
		$sql_estado_pp = sql_abono::get_sql('DD_COD_ESTADO_PAGO');
		$NOM_ESTADO_PAGO = new header_drop_down('NOM_ESTADO_PAGO', 'A.COD_ESTADO_PAGO', 'Estado Abono', $sql_estado_pp);
		
		$FECHA_ABONO = new header_date('FECHA_ABONO', 'FECHA_ABONO', 'Fecha Abono');
		$FECHA_REGISTRO = new header_date('FECHA_REGISTRO', 'FECHA_REGISTRO', 'Fecha Registro');
		$MONTO_MONEDA = new header_num('MONTO_MONEDA', 'MONTO_MONEDA', 'Abono UTM',4);
		$MONTO_PESOS = new header_num('MONTO_PESOS', 'MONTO_PESOS', 'Abono $');
		$NOM_ORIGEN_PAGO = new header_text('NOM_ORIGEN_PAGO', 'NOM_ORIGEN_PAGO', 'Origen Pago');
		$MONTO_CAPITAL = new header_num('MONTO_CAPITAL', 'MONTO_CAPITAL', 'Capital UTM',4);
		$MONTO_INTERES = new header_num('MONTO_INTERES', 'MONTO_INTERES', 'Interés UTM',4);
		$MONTO_MULTA = new header_num('MONTO_MULTA', 'MONTO_MULTA', 'Multa UTM',4);
		$MONTO_CAPITAL_PESOS = new header_num('MONTO_CAPITAL_PESOS', 'MONTO_CAPITAL_PESOS', 'Capital $',4);
		$MONTO_INTERES_PESOS = new header_num('MONTO_INTERES_PESOS', 'MONTO_INTERES_PESOS', 'Interés $',4);
		$MONTO_MULTA_PESOS = new header_num('MONTO_MULTA_PESOS', 'MONTO_MULTA_PESOS', 'Multa $',4);
		$COD_COMPROBANTE = new header_num('COD_COMPROBANTE', 'COD_COMPROBANTE', 'Cod. Comprob.');
		
		$this->add_header($COD_INGRESO);
		$this->add_header($COD_ABONO);
		$this->add_header($RUT);
		$this->add_header($COD_DEUDA);
		$this->add_header($NOM_TIPO_DEUDA);
		$this->add_header($ANO_COBRO);
		$this->add_header($NOM_ESTADO_PAGO);
		$this->add_header($FECHA_ABONO);
		$this->add_header($FECHA_REGISTRO);
		$this->add_header($MONTO_MONEDA);
		$this->add_header($MONTO_PESOS);
		$this->add_header($NOM_ORIGEN_PAGO);
		$this->add_header($MONTO_CAPITAL);
		$this->add_header($MONTO_INTERES);
		$this->add_header($MONTO_MULTA);
		$this->add_header($MONTO_CAPITAL_PESOS);
		$this->add_header($MONTO_INTERES_PESOS);
		$this->add_header($MONTO_MULTA_PESOS);
        $this->add_header($COD_COMPROBANTE);

		$NOM_ESTADO_PAGO->valor_filtro = self::K_VIGENTE;
		$this->make_filtros();
	}
	
	function redraw($temp) {
		parent::redraw($temp);
		$this->habilita_boton($temp, 'load', true);		
	}
	function export() {
	    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	    $this->make_filtros();
	    $sql = $this->dw->get_sql();
	   
		$wo_export = new abono_export($sql, 'abono', $this->cod_item_menu);
		$wo_export->export();

	}
}
class abono_export extends w_output {
	function __construct($sql, $nom_tabla, $cod_item_menu) {
		// cod_item_menu real (no '') porque w_base::__construct() destruye la sesion y redirige
		// al login si viene vacio (chequeo de seguridad de class_w_base.php, no es un bug de este archivo)
		parent::__construct($nom_tabla, $sql, $cod_item_menu);
	    
		$campos = array(
			'COD_INGRESO'          	 => 'N° Ingreso',
			'COD_ABONO'              => 'N° Abono',
			'COD_DEUDA'        		 => 'N° Deuda',
			'RUT'					 => 'Rut',
			'COD_TIPO_DEUDA'         => 'Tipo Deuda',
			'ANO_COBRO'            	 => 'Año',
			'NOM_ESTADO_PAGO'        => 'Estado Abono',
			'FECHA_ABONO'            => 'Fecha Abono',
			'FECHA_REGISTRO'         => 'Fecha Registro',
			'MONTO_MONEDA'           => 'Abono UTM',
			'MONTO_PESOS'            => 'Abono $',
			'NOM_ORIGEN_PAGO'        => 'Origen Pago',
			'MONTO_CAPITAL'          => 'Capital UTM',
			'MONTO_INTERES'          => 'Interés UTM',
			'MONTO_MULTA'            => 'Multa UTM',
			'MONTO_CAPITAL_PESOS'    => 'Capital $',
			'MONTO_INTERES_PESOS'    => 'Interés $',
			'MONTO_MULTA_PESOS'      => 'Multa $',
			'COD_COMPROBANTE'        => 'Cod. Comprob.',
		);
		foreach ($campos as $field => $nom_header) {
			$this->dw->add_field($field);
			$this->add_header(new header_text($field, $field, $nom_header));
		}
	}
}
?>