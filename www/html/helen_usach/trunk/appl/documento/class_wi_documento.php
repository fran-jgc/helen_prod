<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class dw_prorroga_documento_pago extends datawindow{
    
	const K_PARAM_MAX_PRORROGA = 1;

	function __construct(){
		$sql = sql_documento::get_sql('DW_PRORROGA_DOCUMENTO_PAGO');

		parent::__construct($sql, 'PRORROGA_DOCUMENTO_PAGO', true, true);

		$this->add_control($control = new edit_date('FECHA_NUEVA', 10, 10));
		$this->set_protect('FECHA_NUEVA', "[IS_NEW]=='N'");
		//$control->set_onChange("f_valida_max_fecha(this);f_valida_largo(this);");

		$this->add_control(new edit_text('CANT_H',10, 10, 'hidden'));
		$this->add_control(new edit_text('CANT_MAX_PRORROGA_H',10, 10, 'hidden'));
		$this->add_control(new edit_text('COD_PRORROGA_DOCUMENTO_PAGO',10, 10, 'hidden'));
		$this->add_control(new edit_text('COD_DOCUMENTO_PAGO',10, 10, 'hidden'));
		$this->add_control(new edit_text('COD_USUARIO',10, 10, 'hidden'));
		$this->add_control(new static_text('FECHA_ANTERIOR',10,10));
		$this->add_control(new edit_text('FECHA_ANTERIOR_H',10,10, 'hidden'));

		// asigna los mandatorys
		$this->set_mandatory('FECHA_NUEVA', 'Fecha Nueva');
		$this->set_first_focus('FECHA_NUEVA');
	}

	function insert_row($row = -1){
		$row = parent::insert_row($row);
		$this->set_item($row, 'IS_NEW', 'S');
		$this->set_item($row, 'NRO_PRORROGA_DOCUMENTO_PAGO', $this->row_count() * 10);
		$this->set_item($row, 'COD_USUARIO', $this->cod_usuario);

		if($row>0){
			//SETEO VALORES
			$FECHA_NUEVA=$this->get_item($row - 1, 'FECHA_NUEVA');
			$this->set_item($row, 'FECHA_ANTERIOR', $FECHA_NUEVA);

			$COD_PRORROGA_DOCUMENTO_PAGO=$this->get_item($row - 1, 'COD_PRORROGA_DOCUMENTO_PAGO');
			$this->set_item($row, 'COD_PRORROGA_DOCUMENTO_PAGO', $COD_PRORROGA_DOCUMENTO_PAGO);
				
			$COD_DOCUMENTO_PAGO=$this->get_item($row - 1, 'COD_DOCUMENTO_PAGO');
			$this->set_item($row, 'COD_DOCUMENTO_PAGO', $COD_DOCUMENTO_PAGO);
			$this->set_first_focus('FECHA_NUEVA');

			return $row;
		}
		else{
			$this->set_item($row, 'NRO_PRORROGA_DOCUMENTO_PAGO', 10);
			$this->set_item($row, 'COD_USUARIO', $this->cod_usuario);
			return $row;
		}
	}

	function fill_template(&$temp){
		parent::fill_template($temp);

		if ($this->b_add_line_visible) {

			if ($this->entrable){
				$COD_PRORROGA_DOCUMENTO_PAGO = '';		// debe ser == '' para que se agregue el boton "+"
				if ($COD_PRORROGA_DOCUMENTO_PAGO != '')
					$agregar = '<img src="../../../../commonlib_seven/trunk/images/b_add_line.jpg" onClick="add_line_fecha(\''.$this->label_record.'\', \''.$this->nom_tabla.'\');" style="cursor:pointer">';
				else{
					$agregar = '<img src="../../../../commonlib_seven/trunk/images/b_add_line.jpg" onClick="add_line_fecha(\''.$this->label_record.'\', \''.$this->nom_tabla.'\');" style="cursor:pointer">';
					$temp->setVar("AGREGAR_".strtoupper($this->label_record), $agregar);
				}
			}
		}
		else
			$agregar = '<img src="../../../../commonlib_seven/trunk/images/b_add_line.jpg" onClick="add_line_fecha(\''.$this->label_record.'\', \''.$this->nom_tabla.'\');" style="cursor:pointer">';
	}
	
	function update($db, $COD_DEUDOR){
		$sp = 'spu_prorroga_docpago';
		for ($i = 0; $i < $this->row_count(); $i++){
			$statuts = $this->get_status_row($i);
				
			if ($statuts == K_ROW_NOT_MODIFIED || $statuts == K_ROW_NEW)
				continue;
				
			$COD_PRORROGA_DOCUMENTO_PAGO = $this->get_item($i, 'COD_PRORROGA_DOCUMENTO_PAGO');
			$COD_USUARIO				 = $this->get_item($i, 'COD_USUARIO');
			$COD_DOCUMENTO_PAGO			 = $this->get_item($i, 'COD_DOCUMENTO_PAGO');
			$NRO_PRORROGA_DOCUMENTO_PAGO = $this->get_item($i, 'NRO_PRORROGA_DOCUMENTO_PAGO');
			$FECHA_ANTERIOR 			 = $this->get_item($i, 'FECHA_ANTERIOR');
			if ($FECHA_ANTERIOR=='') $FECHA_ANTERIOR= $this->get_item($i, 'FECHA_ANTERIOR_H');
				$FECHA_NUEVA 				 = $this->get_item($i, 'FECHA_NUEVA');

			if (K_TIPO_BD == "oci"){
				$FECHA_ANTERIOR = "'$FECHA_ANTERIOR'";
				$FECHA_NUEVA = "'$FECHA_NUEVA'";
			}
			if (K_TIPO_BD == "mssql"){
				$FECHA_ANTERIOR = $this->str2date($FECHA_ANTERIOR);
				$FECHA_NUEVA = $this->str2date($FECHA_NUEVA);
			}
				
			if ($statuts == K_ROW_NEW_MODIFIED){$operacion = 'INSERT';$COD_PRORROGA_DOCUMENTO_PAGO='NULL';}
			elseif ($statuts == K_ROW_MODIFIED)$operacion = 'UPDATE';
				
			$param = "	'$operacion',$COD_PRORROGA_DOCUMENTO_PAGO, $COD_USUARIO, $COD_DOCUMENTO_PAGO,$NRO_PRORROGA_DOCUMENTO_PAGO, $FECHA_ANTERIOR, $FECHA_NUEVA, $COD_DEUDOR";
			
			
			if (!$db->EXECUTE_SP($sp, $param))
				return false;
		}
		return true;
	}
}


class wi_documento extends w_input{
	const K_ESTADO_DOCUMENTO_POR_DEPOSITAR = 1;
	const K_ESTADO_DOCUMENTO_DEPOSITADO = 2;
	const K_ESTADO_DOCUMENTO_PROTESTADO = 3;
	const K_ESTADO_DOCUMENTO_ACLARADO = 4;
	const K_ESTADO_DOCUMENTO_CAMBIADO = 5; // NUEVO
	const K_ESTADO_DOCUMENTO_COBRANZA = 6;
	const K_ESTADO_DOCUMENTO_SIMULACION = 7; // NUEVO
	const K_ESTADO_DOCUMENTO_ANULADO = 8; // NUEVO
	const K_ESTADO_DOCUMENTO_CONDONADO = 9;  
	
	function __construct($cod_item_menu){
	    
	    parent::__construct('documento', $cod_item_menu);
		$this->b_delete_visible  = false;

		$sql = sql_documento::get_sql('INPUT');

		$this->dws['dw_documento'] = new datawindow($sql);
		$this->dws['dw_prorroga_documento_pago'] = new dw_prorroga_documento_pago($sql);

		//CONTROLES
		$this->dws['dw_documento']->add_control(new edit_text('FECHA_DOCUMENTO_PAGO_H',10, 10, 'hidden'));
		$this->dws['dw_documento']->add_control(new edit_text('COD_DOCUMENTO_PAGO_PP',10, 10, 'hidden'));
		$this->dws['dw_documento']->add_control(new edit_text('USUARIO_ACTUAL_H',10, 10, 'hidden'));
		$this->dws['dw_documento']->add_control(new edit_text('COD_DEUDOR',20, 20, 'hidden'));
		$this->dws['dw_documento']->add_control(new static_text('NOM_FAMILIA_DEUDA'));
		
		$this->dws['dw_documento']->add_control(new static_num('RUT_GIRADOR'));
		$this->dws['dw_documento']->add_control(new static_text('DIG_VERIF_GIRADOR'));
		
		$sql_est = sql_documento::get_sql('DD_COD_ESTADO_DOCUMENTO_PAGO');
		$this->dws['dw_documento']->add_control(new drop_down_dw('COD_ESTADO_DOCUMENTO_PAGO', $sql_est, 150, '', false));		
		$this->dws['dw_documento']->add_control(new static_num('MONTO'));
		
	}
	function load_record(){
		$cod_documentos	= $this->get_item_wo($this->current_record, 'COD_DOCUMENTO_PAGO');
		$this->dws['dw_documento']->retrieve($cod_documentos);
		
		$this->dws['dw_prorroga_documento_pago']->retrieve($cod_documentos);

		$this->dws['dw_documento']->set_item(0, 'USUARIO_ACTUAL_H', $this->cod_usuario);
				
		$COD_ESTADO_DOCUMENTO_PAGO = $this->dws['dw_documento']->get_item(0, 'COD_ESTADO_DOCUMENTO_PAGO');
		$this->b_no_save_visible = false; 
		$this->b_save_visible 	 = false;
		$this->b_modify_visible	 = false;
		if ($COD_ESTADO_DOCUMENTO_PAGO == self::K_ESTADO_DOCUMENTO_POR_DEPOSITAR ||
			$COD_ESTADO_DOCUMENTO_PAGO == self::K_ESTADO_DOCUMENTO_DEPOSITADO ||
			$COD_ESTADO_DOCUMENTO_PAGO == self::K_ESTADO_DOCUMENTO_PROTESTADO ||
			$COD_ESTADO_DOCUMENTO_PAGO == self::K_ESTADO_DOCUMENTO_COBRANZA){
			$this->b_no_save_visible = true;
			$this->b_save_visible 	 = true;
			$this->b_modify_visible	 = true;
		}

		
		if ($this->modify == false){
			$sql = "SELECT 	COD_ESTADO_DOCUMENTO_PAGO, 
							NOM_ESTADO_DOCUMENTO_PAGO
					FROM   	ESTADO_DOCUMENTO_PAGO
					ORDER BY COD_ESTADO_DOCUMENTO_PAGO";
		}
		else {			
			if ($COD_ESTADO_DOCUMENTO_PAGO == self::K_ESTADO_DOCUMENTO_DEPOSITADO){
				$sql = "SELECT	COD_ESTADO_DOCUMENTO_PAGO, 
								NOM_ESTADO_DOCUMENTO_PAGO
					   FROM   	ESTADO_DOCUMENTO_PAGO 
					   where COD_ESTADO_DOCUMENTO_PAGO IN (".self::K_ESTADO_DOCUMENTO_DEPOSITADO.",".self::K_ESTADO_DOCUMENTO_PROTESTADO.")";
			}
			elseif ($COD_ESTADO_DOCUMENTO_PAGO == self::K_ESTADO_DOCUMENTO_PROTESTADO){
				$sql = "SELECT	COD_ESTADO_DOCUMENTO_PAGO, 
								NOM_ESTADO_DOCUMENTO_PAGO
					   FROM   	ESTADO_DOCUMENTO_PAGO 
					   where COD_ESTADO_DOCUMENTO_PAGO IN (".self::K_ESTADO_DOCUMENTO_PROTESTADO.",".self::K_ESTADO_DOCUMENTO_ACLARADO.",".self::K_ESTADO_DOCUMENTO_COBRANZA.",".self::K_ESTADO_DOCUMENTO_CONDONADO.")";
			}
			elseif ($COD_ESTADO_DOCUMENTO_PAGO == self::K_ESTADO_DOCUMENTO_COBRANZA){
				$sql = "SELECT	COD_ESTADO_DOCUMENTO_PAGO, 
								NOM_ESTADO_DOCUMENTO_PAGO
					   FROM   	ESTADO_DOCUMENTO_PAGO 
					   where COD_ESTADO_DOCUMENTO_PAGO IN (".self::K_ESTADO_DOCUMENTO_ACLARADO." ,".self::K_ESTADO_DOCUMENTO_COBRANZA.",".self::K_ESTADO_DOCUMENTO_CONDONADO.")";
			}
			elseif ($COD_ESTADO_DOCUMENTO_PAGO == self::K_ESTADO_DOCUMENTO_POR_DEPOSITAR){
				$sql = "SELECT	COD_ESTADO_DOCUMENTO_PAGO, 
								NOM_ESTADO_DOCUMENTO_PAGO
					   FROM   	ESTADO_DOCUMENTO_PAGO 
					   where COD_ESTADO_DOCUMENTO_PAGO IN (".self::K_ESTADO_DOCUMENTO_POR_DEPOSITAR.",".self::K_ESTADO_DOCUMENTO_CONDONADO.")";
			}
			else {
				$sql = "SELECT 	COD_ESTADO_DOCUMENTO_PAGO, 
								NOM_ESTADO_DOCUMENTO_PAGO
						FROM   	ESTADO_DOCUMENTO_PAGO
						where COD_ESTADO_DOCUMENTO_PAGO = $COD_ESTADO_DOCUMENTO_PAGO";
				}
		}
		
		
		$this->dws['dw_documento']->remove_control('COD_ESTADO_DOCUMENTO_PAGO');
		$this->dws['dw_documento']->add_control(new drop_down_dw('COD_ESTADO_DOCUMENTO_PAGO', $sql, 110, '', false));
	 
	}
	function get_key(){
		return $this->dws['dw_documento']->get_item(0, 'COD_DOCUMENTO_PAGO');
	}
	
	function save_record($db){
		$COD_DOCUMENTO_PAGO = $this->dws['dw_documento']->get_item(0, 'COD_DOCUMENTO_PAGO');
		$COD_ESTADO_DOCUMENTO_PAGO = $this->dws['dw_documento']->get_item(0, 'COD_ESTADO_DOCUMENTO_PAGO');
		$COD_USUARIO = $this->cod_usuario;

		if (!$db->EXECUTE_SP('spu_documento_pago', "'CAMBIO_ESTADO', $COD_DOCUMENTO_PAGO,$COD_ESTADO_DOCUMENTO_PAGO,$COD_USUARIO"))
			return false;
		
		
		$COD_DEUDOR	= $this->dws['dw_documento']->get_item(0, 'COD_DEUDOR');
		for ($i=0; $i<$this->dws['dw_documento']->row_count(); $i++){
			if (!$this->dws['dw_prorroga_documento_pago']->update($db, $COD_DEUDOR)){ 
				return false;
			}			
			return true;
		}
	}
}
?>