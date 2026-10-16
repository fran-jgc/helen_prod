<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
/*
 Clase : WI_PAGARE
 */

class wi_pagare_base extends w_input {
	var   $K_ESTADO_PAGARE_PROTESTADO;
	
	function __construct($cod_item_menu) {
		$this->K_ESTADO_PAGARE_PROTESTADO = $this->get_constante('K_EST_PAGARE_PROTESTADO');
		
		parent::__construct('pagare', $cod_item_menu);
		$this->b_delete_visible  = false;
		$this->b_modify_visible  = true;
		$this->b_save_visible  = true;
		$this->b_no_save_visible  = true;
		
		//////////////// CLASS_SQL = INPUT ///////////////
		$sql = sql_pagare::get_sql('INPUT'); 

		$this->dws['dw_pagare'] = new datawindow($sql);
		//CONTROLES-estado, folio, fecha_suscripcion, monto moneda, monto pesos
		
		$this->dws['dw_pagare']->add_control(new static_num('MONTO_MONEDA', 4));
		$this->dws['dw_pagare']->add_control(new static_num('MONTO_PESOS'));
		
		//////////////// CLASS_SQL = INPUT_DD_ESTADO ///////////////
		$sql_estado= sql_pagare::get_sql('INPUT_DD_ESTADO');
		$this->dws['dw_pagare']->add_control(new drop_down_dw('COD_ESTADO_PAGARE', $sql_estado, 140, '', false));
		$this->dws['dw_pagare']->set_entrable('FOLIO_PAGARE'			, false);
		$this->dws['dw_pagare']->set_entrable('RUT'			, false);
		$this->dws['dw_pagare']->set_entrable('FECHA_PAGARE'			, false);
		$this->dws['dw_pagare']->set_entrable('MONTO_MONEDA'			, false);
		$this->dws['dw_pagare']->set_entrable('MONTO_PESOS'			, false);
		$this->dws['dw_pagare']->add_control(new edit_text('ESTADO_DEUDA_H', 10,10, 'hidden'));
		$this->dws['dw_pagare']->add_control(new static_link('COD_DEUDA', '../../../../commonlib_seven/trunk/php/link_wi.php?modulo_origen=pagare&modulo_destino=deuda&cod_modulo_destino=[COD_DEUDA]&cod_item_menu=1010'));
		$this->dws['dw_pagare']->set_entrable('FECHA_PROTESTO'			, false);
		$this->dws['dw_pagare']->add_control(new edit_text('FECHA_PROTESTO_H', 10,10, 'hidden'));
		
		$this->dws['dw_pagare']->set_mandatory('COD_ESTADO_PAGARE', 'Estado');

		//audoria
		$this->add_auditoria('COD_ESTADO_PAGARE');
	}
	function new_record() {
		$this->dws['dw_pagare']->insert_row();
	}
	function load_record() {
		$cod_pagare	= $this->get_item_wo($this->current_record, 'COD_PAGARE');
		$this->dws['dw_pagare']->retrieve($cod_pagare);
		
		$ESTADO_DEUDA=$this->dws['dw_pagare']->get_item(0, 'ESTADO_DEUDA_H');
	
		IF ($ESTADO_DEUDA == 1)
		{	
			
			$this->b_save_visible = TRUE;
			$this->b_no_save_visible = TRUE;
			$this->b_modify_visible = TRUE;
		}
		else {
			
			$this->b_save_visible = TRUE;
			$this->b_no_save_visible = TRUE;
			$this->b_modify_visible = TRUE;
		}
		
	}
	function get_key() {
		return $this->dws['dw_pagare']->get_item(0, 'COD_PAGARE');
	}
	function save_record($db) 
	{
		$COD_PAGARE				= $this->dws['dw_pagare']->get_item(0, 'COD_PAGARE');
		$FECHA_PAGARE			= $this->dws['dw_pagare']->get_item(0, 'FECHA_PAGARE');
		$FOLIO_PAGARE			= $this->dws['dw_pagare']->get_item(0, 'FOLIO_PAGARE');
		$MONTO_MONEDA			= $this->dws['dw_pagare']->get_item(0, 'MONTO_MONEDA');
		$MONTO_PESOS			= $this->dws['dw_pagare']->get_item(0, 'MONTO_PESOS');
		$COD_ESTADO_PAGARE		= $this->dws['dw_pagare']->get_item(0, 'COD_ESTADO_PAGARE');
		$COD_DEUDA				= $this->dws['dw_pagare']->get_item(0, 'COD_DEUDA');
		$COD_USUARIO			= $this->cod_usuario;
		$FECHA_PAGARE 			= $this->str2date($FECHA_PAGARE);
		$FECHA_PROTESTO			='';
		$COD_USUARIO_PROTESTO	='';
		$operacion 			= 'UPDATE';
		
		$sp = 'spu_pagare';
		
		if($COD_ESTADO_PAGARE == $this->K_ESTADO_PAGARE_PROTESTADO){
			$FECHA_PROTESTO = $this->str2date($this->current_date(), $this->current_time());
			$COD_USUARIO_PROTESTO = $this->cod_usuario;
		}
		
		$FECHA_PROTESTO 		= ($FECHA_PROTESTO=='') ? "null" : $FECHA_PROTESTO;
		$COD_USUARIO_PROTESTO	= ($COD_USUARIO_PROTESTO=='') ? "null" : $COD_USUARIO_PROTESTO;
			
		$param	= "'$operacion',
					$COD_PAGARE,  
					$FECHA_PAGARE, 
					'$FOLIO_PAGARE', 
					$MONTO_MONEDA, 
					$MONTO_PESOS, 
					$COD_ESTADO_PAGARE,
					$COD_DEUDA,
					$COD_USUARIO,
					$FECHA_PROTESTO,
					$COD_USUARIO_PROTESTO";
						
		if ($db->EXECUTE_SP($sp, $param)){
			return true;
		}
		return false;
	}
}
/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD

$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wi_pagare.php";
if (file_exists($file_name)) 
	require_once($file_name);
else {
	class wi_pagare extends wi_pagare_base {
		function __construct($cod_item_menu) {
		    parent::__construct($cod_item_menu); 
		}
	}
}
?>