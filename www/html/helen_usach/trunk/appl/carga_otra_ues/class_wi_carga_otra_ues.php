<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");


class wi_carga_otra_ues extends w_input{
	
	function __construct($cod_item_menu){
	    parent::__construct('carga_otra_ues', $cod_item_menu);
		$this->b_delete_visible  = false;
		$this->hide_menu_when_from = false;		// Cuando se crear desde no se debe esconder menu
		
		///////////////// CLASS_SQL = INPUT ///////////////
		$sql = sql_carga_otra_ues::get_sql('INPUT');
		$this->dws['dw_carga_otra_ues'] = new datawindow($sql);
		$this->dws['dw_item_carga_otra_ues'] = new dw_item_carga_otra_ues();
		//CONTROLES
		$this->dws['dw_carga_otra_ues']->add_control(new	static_text('FECHA_REGISTRO'));
		$this->dws['dw_carga_otra_ues']->add_control(new	edit_date('FECHA_CARGA_OTRA_UES'));
		$this->dws['dw_carga_otra_ues']->add_control(new	static_text('COD_CARGA_OTRA_UES'));
		$this->dws['dw_carga_otra_ues']->add_control(new	static_num('TOTAL_REG_CARGA'));
		$this->dws['dw_carga_otra_ues']->add_control(new	static_num('TOTAL_REG_EXITOSO'));
		$this->dws['dw_carga_otra_ues']->add_control(new	static_num('TOTAL_REG_PDTE'));
		$this->dws['dw_carga_otra_ues']->add_control(new	static_num('MONTO_REG_CARGA'));
		$this->dws['dw_carga_otra_ues']->add_control(new	static_num('MONTO_REG_EXITOSO'));
		$this->dws['dw_carga_otra_ues']->add_control(new	static_num('MONTO_REG_PDTE'));
		
	}
	
	
	function new_record(){
		$this->dws['dw_carga_otra_ues']->insert_row();
		$this->dws['dw_item_carga_otra_ues']->insert_row();

		$this->b_print_visible =false;	

	}
	function load_record()
	{
		$K_ESTADO_CARGA_EXITOSO = 2; 
		$K_ESTADO_CARGA_SALDO_NEGATIVO = 15;
 		$COD_CARGA_OTRA_UES	=$this->get_item_wo($this->current_record, 'COD_CARGA_OTRA_UES');
		$this->dws['dw_carga_otra_ues']->retrieve($COD_CARGA_OTRA_UES);
		$this->dws['dw_item_carga_otra_ues']->retrieve($COD_CARGA_OTRA_UES);
		$this->b_no_save_visible	= true;
		$this->b_save_visible		= true;
		$this->b_modify_visible		= true;	
		$this->b_print_visible		= false;
		//$this->dws['dw_carga_banco']->set_item(0, 'OCULTA_PROCESAR', '');
		if(isset($_POST['b_modify_x'])){
			$this->dws['dw_carga_otra_ues']->set_item(0, 'OCULTA_PROCESAR', 'none');
		}
		
		$count_pendiente= 0;
		for($i = 0; $i < $this->dws['dw_item_carga_otra_ues']->row_count(); $i++){

			if($this->dws['dw_item_carga_otra_ues']->get_item($i,'COD_ESTADO_CARGA') != $K_ESTADO_CARGA_EXITOSO 
			&& $this->dws['dw_item_carga_otra_ues']->get_item($i,'COD_ESTADO_CARGA') != $K_ESTADO_CARGA_SALDO_NEGATIVO){
				$count_pendiente++;
			}
		}
		if($count_pendiente == 0){
			$this->b_modify_visible		= false;
			$this->dws['dw_carga_otra_ues']->set_item(0, 'OCULTA_PROCESAR', 'none');
		}
		
	}

	function get_key()	{
		return $this->dws['dw_carga_otra_ues']->get_item(0, 'COD_CARGA_OTRA_UES');
		
	}
	function save_record($db)	{	
		
		$COD_CARGA_OTRA_UES			= $this->get_key();
		$FECHA_CARGA				= $this->dws['dw_carga_otra_ues']->get_item(0,'FECHA_CARGA_OTRA_UES');
		$TOTAL_REG_CARGA			= $this->dws['dw_carga_otra_ues']->get_item(0,'TOTAL_REG_CARGA');
		$TOTAL_REG_EXITOSO			= $this->dws['dw_carga_otra_ues']->get_item(0,'TOTAL_REG_EXITOSO');
		$TOTAL_REG_PDTE				= $this->dws['dw_carga_otra_ues']->get_item(0,'TOTAL_REG_PDTE');
		$ORIGEN_PAGO				= $this->dws['dw_carga_otra_ues']->get_item(0,'COD_ORIGEN_PAGO');
		
		
		$COD_CARGA_OTRA_UES			= ($COD_CARGA_OTRA_UES=='') ? "null" : $COD_CARGA_OTRA_UES;
		$TOTAL_REG_CARGA			= ($TOTAL_REG_CARGA=='') ? "null" : $TOTAL_REG_CARGA;
		$TOTAL_REG_EXITOSO			= ($TOTAL_REG_EXITOSO=='') ? "null" : $TOTAL_REG_EXITOSO;
		$TOTAL_REG_PDTE				= ($TOTAL_REG_PDTE=='') ? "null" : $TOTAL_REG_PDTE;
		$ORIGEN_PAGO				= ($ORIGEN_PAGO=='') ? "null" : $ORIGEN_PAGO;			
		$FECHA_CARGA 				= "to_date('$FECHA_CARGA', 'dd/mm/yyyy')";
		
         
		
		$sp = 'spu_carga_otra_ues';
		if ($this->is_new_record()){
			$operacion = 'INSERT';
	
		}
		else{
			$operacion = 'UPDATE';		
		}			
		$param = " '$operacion'
					,$COD_CARGA_OTRA_UES			
					,$this->cod_usuario				
					,$FECHA_CARGA
					,$TOTAL_REG_CARGA
					,$TOTAL_REG_EXITOSO
					,$TOTAL_REG_PDTE
					,$ORIGEN_PAGO";	
		
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$COD_CARGA_OTRA_UES = $db->GET_IDENTITY('CARGA_OTRA_UES');
				$this->dws['dw_carga_otra_ues']->set_item(0, 'COD_CARGA_OTRA_UES', $COD_CARGA_OTRA_UES);
			}
			    
			if (!$this->dws['dw_item_carga_otra_ues']->update($db,$COD_CARGA_OTRA_UES)){
				return false;
			}
					
			return true;
		}
		return false;
		
	}
	function procesa_carga(){
		$COD_CARGA_OTRA_UES	=$this->get_item_wo($this->current_record, 'COD_CARGA_OTRA_UES');
		$cod_usuario= $this->cod_usuario;
		$cod_proceso = 22; 
		
		$param = "'NEW'
				,null	
				,$cod_proceso
				,$cod_usuario
				,null
				,null";			
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);			
	    $sp="spu_ejecucion_proceso";
		if ($db->EXECUTE_SP($sp, $param)){
			$COD_EJECUCION_PROCESO =$db->GET_IDENTITY('ejecucion_proceso');			
	 	}
	 	else{
	 		$COD_EJECUCION_PROCESO = "null";
	 	}
	 	
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
		$stid = oci_parse($conn, "begin spp_usach_carga_otra_ues ($COD_EJECUCION_PROCESO, $COD_CARGA_OTRA_UES, $cod_usuario); end;");
		if (oci_execute($stid)){
			print "<script>document.getElementById('img_loader').style.display = 'none';</script>"; 
			oci_free_statement($stid);
			oci_close($conn);
			$this->dws['dw_carga_otra_ues']->retrieve($COD_CARGA_OTRA_UES);
			$this->dws['dw_item_carga_otra_ues']->retrieve($COD_CARGA_OTRA_UES);
			$this->redraw();

			$modulo = 'carga_otra_ues';
			session::set('DESDE_wo_'.$modulo, 'desde output');	// para indicar que viene del output
			$rec_no = $_REQUEST['rec_no'];
			$cod_item_menu= $_REQUEST['cod_item_menu'];
			$url= K_ROOT_URL.'appl/carga_otra_ues/wi_carga_otra_ues.php?rec_no='.$rec_no.'&cod_item_menu='.$cod_item_menu;
			print "<script>location.assign('$url');</script>"; 
		}
	 	else{
	 		print "<script>document.getElementById('img_loader').style.display = 'none';</script>"; 
	 		$error = true;
	 		ob_end_flush();
	 		$e = oci_error($stid);
	 		$error_code= $e['code'];
	 		$error_sqltext= $e['sqltext'];
	 		//$error_message= $e['message'];
	 		//$error_message = str_replace('"', '', $error_message);
	 		//$error_message = str_replace("'", "", $error_message);
	 		
	 		$this->alert('Error Oracle codigo error: '.$error_code);
	 		$this->alert('Error Oracle texto sql ejecutado: '.$error_sqltext);
	 		//$this->alert($error_message); no funciona
			
	 		oci_free_statement($stid);
			oci_close($conn);
			$this->dws['dw_carga_otra_ues']->retrieve($COD_CARGA_OTRA_UES);
			$this->dws['dw_item_carga_otra_ues']->retrieve($COD_CARGA_OTRA_UES);
		}
	}
	
	function genera_xls() {
		$COD_CARGA_OTRA_UES = $this->get_item_wo($this->current_record, 'COD_CARGA_OTRA_UES');

		require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/libs/simplexlsxgen/src/SimpleXLSXGen.php";
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 1800);//30 minutos

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT RUT
					,ANO_CUOTA
					,NRO_CUOTA
					,TO_CHAR(FECHA_PAGO, 'dd/mm/yyyy') FECHA_PAGO
					,MONTO_PESOS
					,MONTO_UTM
					,COD_DEUDA
					,null FECHA_VENCTO
					,COD_ORIGEN_PAGO
					,TO_CHAR(FECHA_DEPOSITO, 'dd/mm/yyyy') FECHA_DEPOSITO
					,EC.NOM_ESTADO_CARGA
				FROM	ITEM_CARGA_OTRA_UES IT, ESTADO_CARGA EC
				WHERE IT.COD_ESTADO_CARGA= EC.COD_ESTADO_CARGA
				AND IT.COD_CARGA_OTRA_UES= $COD_CARGA_OTRA_UES
				ORDER BY fecha_pago, cod_item_carga_otra_ues";
		$res = $db->query($sql);

		$rows = [['<b>RUT</b>', '<b>ANO_CUOTA</b>', '<b>NRO_CUOTA</b>', '<b>FECHA_PAGO</b>', '<b>MONTO_TOTAL_PESOS</b>', '<b>MONTO_TOTAL_UTM</b>', '<b>COD_DEUDA</b>', '<b>FECHA_VENCTO</b>', '<b>COD_ORIGEN_PAGO</b>', '<b>FECHA_DEPOSITO</b>', '<b>ESTADO</b>']];
		while ($my_row = $db->get_row()) {
			$rows[] = [$my_row[0], $my_row[1], $my_row[2], $my_row[3], $my_row[4], $my_row[5], $my_row[6], $my_row[7], $my_row[8], $my_row[9], $my_row[10]];
		}

		if ($db->database_type == "oci") {
			oci_free_statement($db->query_id);
			$db->query_id = false;
		}

		\Shuchkin\SimpleXLSXGen::fromArray($rows, 'Registros Carga Otras Ues')->downloadAs($this->nom_tabla.'.xlsx');
	}
	
	function procesa_event() {
		if(isset($_POST['b_procesar_x'])){	
			$this->procesa_carga();
		}
		else if(isset($_POST['b_export_x'])){	
			$this->genera_xls();
		}else
			parent::procesa_event();
	}
}
class dw_item_carga_otra_ues extends datawindow{
    function __construct() {
		
	$sql= 	"select CB.COD_CARGA_OTRA_UES
									,CB.TOTAL_REG_CARGA
									,CB.TOTAL_REG_EXITOSO
									,CB.TOTAL_REG_PDTE
									,TO_CHAR(CB.FECHA_CARGA_OTRA_UES,'dd/mm/yyyy') FECHA_CARGA_OTRA_UES
									,IT.COD_ESTADO_CARGA
									,EC.NOM_ESTADO_CARGA
									,0 NRO_ITEM_CARGA_ORSAN
									,IT.COD_ITEM_CARGA_OTRA_UES COD_ITEM_CARGA_OTRA_UES_H
									,IT.RUT 
									,IT.COD_DEUDA
									,IT.ANO_CUOTA
									,IT.NRO_CUOTA
									,TO_CHAR(IT.FECHA_DEPOSITO, 'dd/mm/yyyy') FECHA_DEPOSITO
									,TO_CHAR(IT.FECHA_PAGO, 'dd/mm/yyyy') FECHA_PAGO
									,IT.COD_ORIGEN_PAGO
									,IT.MONTO_UTM 
									,IT.MONTO_PESOS 
									,IT.COD_ITEM_CARGA_OTRA_UES
									,IT.MONTO_DESCTO_UTM
									,IT.MONTO_DESCTO_PESOS
									FROM ITEM_CARGA_OTRA_UES IT 
									 	left OUTER JOIN CARGA_OTRA_UES CB  ON  IT.COD_CARGA_OTRA_UES= CB.COD_CARGA_OTRA_UES
									 	left OUTER JOIN ESTADO_CARGA EC  ON  IT.COD_ESTADO_CARGA= EC.COD_ESTADO_CARGA
					                WHERE IT.COD_CARGA_OTRA_UES= {KEY1}
					                AND IT.COD_CARGA_OTRA_UES= CB.COD_CARGA_OTRA_UES
					                AND IT.COD_ESTADO_CARGA= EC.COD_ESTADO_CARGA
									ORDER BY fecha_pago, cod_item_carga_otra_ues";
	$this->b_delete_visible  = false;		
	parent::__construct($sql, 'ITEM_CARGA_OTRA_UES', true, true);
	$this->add_control($control=new edit_num('RUT',10,10));
	$control->set_onKeypress("return onlyNumbers(this, event,0, 1);");
	$this->add_control($control= new static_link('COD_ITEM_CARGA_OTRA_UES', '#'));
	$control->set_onClick("abrirVentanaBitacora(this)");
	$this->add_control(new edit_date('FECHA_DEPOSITO'));
	$this->add_control(new edit_date('FECHA_PAGO'));
	$this->add_control(new edit_text_hidden('COD_ITEM_CARGA_OTRA_UES_H'));
	$this->add_control(new static_text('NOM_ESTADO_CARGA'));
	$this->add_control(new edit_text('COD_ESTADO_CARGA',10,10,'hidden'));
	$this->add_control(new static_num('MONTO_UTM', 4));
	$this->add_control(new static_num('MONTO_PESOS'));	
	$this->add_control(new edit_num('COD_ORIGEN_PAGO',8,4));
	$this->add_control($control=new edit_text('COD_DEUDA',10,10));
	$control->set_onKeypress("return onlyNumbers(this, event,0, 1);");
	$this->add_control($control=new edit_text('ANO_CUOTA',8,4));
	$control->set_onKeypress("return onlyNumbers(this, event,0, 1);");
	$this->add_control(new edit_num('NRO_CUOTA',8,4));
	$this->add_control(new static_num('MONTO_DESCTO_UTM', 4));
	$this->add_control(new static_num('MONTO_DESCTO_PESOS'));
	
	$this->set_protect('RUT', "[COD_ESTADO_CARGA]==2","[COD_ESTADO_CARGA]==15");
	$this->set_protect('COD_DEUDA', "[COD_ESTADO_CARGA]==2","[COD_ESTADO_CARGA]==15");
	$this->set_protect('FECHA_PAGO', "[COD_ESTADO_CARGA]==2","[COD_ESTADO_CARGA]==15");
	$this->set_protect('FECHA_DEPOSITO', "[COD_ESTADO_CARGA]==2","[COD_ESTADO_CARGA]==15");
	$this->set_protect('COD_ORIGEN_PAGO', "[COD_ESTADO_CARGA]==2","[COD_ESTADO_CARGA]==15");
    $this->set_protect('ANO_CUOTA', "[COD_ESTADO_CARGA]==2","[COD_ESTADO_CARGA]==15");
    $this->set_protect('NRO_CUOTA', "[COD_ESTADO_CARGA]==2","[COD_ESTADO_CARGA]==15");

    $this->set_mandatory('RUT','Rut');
    $this->set_mandatory('COD_DEUDA','N° Deuda');
    $this->set_mandatory('COD_ORIGEN_PAGO','Origen');
    $this->set_mandatory('FECHA_DEPOSITO','Fecha Deposito');

	}
	function update($db, $COD_CARGA_OTRA_UES) {
		$cod_usuario= $this->cod_usuario;
		$sp = 'spu_item_carga_otra_ues';		
			for ($i = 0; $i < $this->row_count(); $i++)		{
				$statuts = $this->get_status_row($i);
				if ($statuts == K_ROW_NOT_MODIFIED || $statuts == K_ROW_NEW)
					continue;
				
				$COD_ITEM_CARGA			= $this->get_item($i, 'COD_ITEM_CARGA_OTRA_UES_H');
				$RUT					= $this->get_item($i, 'RUT');
				$ANO_CUOTA				= $this->get_item($i, 'ANO_CUOTA');
				$COD_DEUDA				= $this->get_item($i, 'COD_DEUDA');
				$FECHA_PAGO				= $this->get_item($i, 'FECHA_PAGO');
				$COD_ORIGEN_PAGO		= $this->get_item($i, 'COD_ORIGEN_PAGO');	
				$FECHA_DEPOSITO			= $this->get_item($i, 'FECHA_DEPOSITO');	
				$NRO_CUOTA				= $this->get_item($i, 'NRO_CUOTA');

				$RUT		= ($RUT=='') ? "null" : $RUT;
				$FECHA_PAGO	= ($FECHA_PAGO=='') ? "null" : $FECHA_PAGO;
				$COD_ORIGEN_PAGO= ($COD_ORIGEN_PAGO=='') ? "null" : $COD_ORIGEN_PAGO;
				$FECHA_DEPOSITO = ($FECHA_DEPOSITO=='') ? "null" : $FECHA_DEPOSITO;
				$NRO_CUOTA = ($NRO_CUOTA=='') ? "null" : $NRO_CUOTA;
				$ANO_CUOTA = ($ANO_CUOTA=='') ? "null" : $ANO_CUOTA;
				$COD_DEUDA = ($COD_DEUDA=='') ? "null" : $COD_DEUDA;

				if($FECHA_PAGO != "null"){
					$FECHA_PAGO = "to_date('$FECHA_PAGO', 'dd/mm/yyyy')";
				}
				if($FECHA_DEPOSITO != "null"){
					$FECHA_DEPOSITO = "to_date('$FECHA_DEPOSITO', 'dd/mm/yyyy')";
				}
				
				
				$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
				$sql= 	"SELECT COD_CARGA_OTRA_UES
						FROM ITEM_CARGA_OTRA_UES
						WHERE COD_ITEM_CARGA_OTRA_UES=$COD_ITEM_CARGA";
				$result = $db->build_results($sql); 
				
				$COD_CARGA_OTRA_UES = $result[0]['COD_CARGA_OTRA_UES'];
					
				if ($statuts == K_ROW_NEW_MODIFIED)
					$operacion = 'INSERT';
				elseif ($statuts == K_ROW_MODIFIED)
					$operacion = 'UPDATE';
					
				//operacion siempre es UPDATE
				$param = "'$operacion'
							,$COD_ITEM_CARGA
							,$COD_CARGA_OTRA_UES
							,null	
							,$RUT
							,null
							,null
							,$COD_DEUDA
							,$ANO_CUOTA
							,null
							,null
							,$FECHA_PAGO
							,$COD_ORIGEN_PAGO
							,$FECHA_DEPOSITO
							,$cod_usuario
							,$NRO_CUOTA";
							
					echo $sp.' '.$param.'<br>';		
				if (!$db->EXECUTE_SP($sp, $param))
					return false;
				}
		return true;
		
	}

}
?>