<?php
require_once(dirname(__FILE__)."/class_print_pagare.php");
////////////// USACH /////////////////

class wi_pagare extends wi_pagare_base {
	function __construct($cod_item_menu) {		
	    parent::__construct($cod_item_menu); 
	}
	function load_record() {
		$cod_pagare	= $this->get_item_wo($this->current_record, 'COD_PAGARE');
		$this->dws['dw_pagare']->retrieve($cod_pagare);
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql="SELECT * FROM 
			  PAGARE pp, DEUDA de
			  WHERE COD_PAGARE = $cod_pagare
			  AND pp.COD_DEUDA = de.COD_DEUDA
			  AND de.COD_TIPO_DEUDA = 'CR12'
			  AND FOLIO_PAGARE_SUSPENSION IS NOT NULL";
		$result = $db->build_results($sql);
		
		//existe una suspension para la deuda CR12
		if(count($result) > 0)
			$this->b_print_visible = true;
		
	}
	function print_record() {
		$cod_pagare = $this->get_key();
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql= sql_pagare::get_sql('PRINT_RECORD',$cod_pagare);
	 	
		// reporte
		$labels = array();
		
		$file_name = $this->find_file('pagare', 'USACH/pagare.xml');					
		$rpt = new print_pagare($sql, $file_name, $labels, "pagare".$cod_pagare.'.pdf', 0);										
		$this->_load_record();
	}
}
?>