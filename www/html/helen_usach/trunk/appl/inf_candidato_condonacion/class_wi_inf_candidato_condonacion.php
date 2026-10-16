<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");

class wi_inf_candidato_condonacion extends w_param_informe_helen{
    function __construct($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_candidato_condonacion/inf_candidato_condonacion.xml';
		$sql_informe = sql_inf_candidato_condonacion::get_sql('INPUT');
		parent::__construct('inf_candidato_condonacion', $cod_item_menu, 'Candidatos condonacion', $xml, $sql_informe, 'spi_candidato_condonacion');
		

		$fecha = getdate();
  		$ano   = $fecha["year"];
  		
		$sql = sql_inf_candidato_condonacion::get_sql('PARAMETRO_CONDONACION', $ano);
		$this->dws['dw_param'] = new datawindow($sql);
		
		//$this->dws['dw_param']->add_control(new edit_date('FECHA_INICIO'));
		
	}
function genera_xls() {
	    ini_set('memory_limit', '-1');
	    ini_set('max_execution_time', 1800);//30 minutos

	    require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/libs/simplexlsxgen/src/SimpleXLSXGen.php";

	    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	    $this->sp = "spi_candidato_condonacion";
	    $this->param = "";
	    $db->EXECUTE_SP($this->sp, $this->param);
	    $sql = "SELECT x.RUT
	                  ,x.DIG_VERIF
	                  ,x.COD_TIPO_DEUDA
	                  ,x.ANO_INICIO
	                  ,x.MONTO_INICIO_COBRO
	                  ,x.NOM_ESTADO_DEUDA
	                  ,x.NOM_CARTERA
	                  ,x.NOM_UNIVERSIDAD
	                  ,x.SALDO_99 SALDO_FUT
	                  ,x.EMAIL
	                  ,x.ANO_ULTIMO_PP
	            FROM TEMP_CANDIDATO_CONDONACION x
	            WHERE ES_CAND_CONDONA = 'S'
	            ORDER BY RUT";
	    $res = $db->query($sql);

	    $rows = [['<b>RUT</b>', '<b>DIG_VERIF</b>', '<b>TIPO DEUDA</b>', '<b>INICIO COBRO</b>', '<b>MONTO INICIO COBRO</b>', '<b>ESTADO DEUDA</b>', '<b>CARTERA</b>', '<b>UNIVERSIDAD</b>', '<b>SALDO 99</b>', '<b>EMAIL</b>', '<b>AÑO ULTIMO PLAN PAGO</b>']];
	    while ($my_row = $db->get_row()) {
	        $rows[] = [$my_row[0], $my_row[1], $my_row[2], $my_row[3], $my_row[4], $my_row[5], $my_row[6], $my_row[7], $my_row[8], $my_row[9], $my_row[10]];
	    }

	    if ($db->database_type == "oci") {
	        oci_free_statement($db->query_id);
	        $db->query_id = false;
	    }

	    \Shuchkin\SimpleXLSXGen::fromArray($rows, 'Informe Candidatos condonacion')->downloadAs($this->nom_tabla.'.xlsx');
	}
	
}
?>