<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");


class wi_carga_orsan_usach extends w_input{
	
    function __construct($cod_item_menu){
        parent::__construct('carga_orsan_usach', $cod_item_menu);
		$this->b_delete_visible  = false;
		$this->hide_menu_when_from = false;		// Cuando se crear desde no se debe esconder menu
		
		///////////////// CLASS_SQL = INPUT ///////////////
		$sql = sql_carga_orsan_usach::get_sql('INPUT');
		$this->dws['dw_carga_orsan_usach'] = new datawindow($sql);
		$this->dws['dw_item_carga_orsan_usach'] = new dw_item_carga_orsan_usach();
		//CONTROLES
		$this->dws['dw_carga_orsan_usach']->add_control(new	static_text('FECHA_REGISTRO'));
		$this->dws['dw_carga_orsan_usach']->add_control(new	edit_date('FECHA_CARGA_ORSAN'));
		$this->dws['dw_carga_orsan_usach']->add_control(new	static_text('COD_CARGA_ORSAN'));
		$this->dws['dw_carga_orsan_usach']->add_control(new	static_num('TOTAL_REG_CARGA'));
		$this->dws['dw_carga_orsan_usach']->add_control(new	static_num('TOTAL_REG_EXITOSO'));
		$this->dws['dw_carga_orsan_usach']->add_control(new	static_num('TOTAL_REG_PDTE'));
		$this->dws['dw_carga_orsan_usach']->add_control(new	static_num('MONTO_REG_CARGA'));
		$this->dws['dw_carga_orsan_usach']->add_control(new	static_num('MONTO_REG_EXITOSO'));
		$this->dws['dw_carga_orsan_usach']->add_control(new	static_num('MONTO_REG_PDTE'));
		
		//ORSAN y TGR ORSAN
		$sql_origen ="select COD_ORIGEN_PAGO
							,NOM_ORIGEN_PAGO
					  from ORIGEN_PAGO
					  where COD_ORIGEN_PAGO in (4)
					  order by COD_ORIGEN_PAGO"; 
		$this->dws['dw_carga_orsan_usach']->add_control($control= new drop_down_dw('ORIGEN_PAGO', $sql_origen, 120));
		$control->set_onChange("set_forma_pago(this);");
		$this->dws['dw_carga_orsan_usach']->add_control(new edit_text_hidden('ORIGEN_PAGO_H'));
		
		//mandatorys
		$this->dws['dw_carga_orsan_usach']->set_mandatory('FECHA_CARGA_ORSAN','Fecha Carga');

	}
	
	
	function new_record(){
		$this->dws['dw_carga_orsan_usach']->insert_row();
		$this->dws['dw_item_carga_orsan_usach']->insert_row();

		$this->b_print_visible =false;	

	}
	function load_record()
	{
		$K_ESTADO_CARGA_EXITOSO = 2; 
		$K_ESTADO_CARGA_SALDO_NEGATIVO = 15;
 		$COD_CARGA_ORSAN	=$this->get_item_wo($this->current_record, 'COD_CARGA_ORSAN_USACH');
		$this->dws['dw_carga_orsan_usach']->retrieve($COD_CARGA_ORSAN);
		$this->dws['dw_item_carga_orsan_usach']->retrieve($COD_CARGA_ORSAN);
		$this->b_no_save_visible	= true;
		$this->b_save_visible		= true;
		$this->b_modify_visible		= true;	
		$this->b_print_visible		= false;
		//$this->dws['dw_carga_banco']->set_item(0, 'OCULTA_PROCESAR', '');
		if(isset($_POST['b_modify_x'])){
			$this->dws['dw_carga_orsan_usach']->set_item(0, 'OCULTA_PROCESAR', 'none');
		}
		
		$count_pendiente= 0;
		for($i = 0; $i < $this->dws['dw_item_carga_orsan_usach']->row_count(); $i++){

			if($this->dws['dw_item_carga_orsan_usach']->get_item($i,'COD_ESTADO_CARGA') != $K_ESTADO_CARGA_EXITOSO 
			&& $this->dws['dw_item_carga_orsan_usach']->get_item($i,'COD_ESTADO_CARGA') != $K_ESTADO_CARGA_SALDO_NEGATIVO){
				$count_pendiente++;
			}
		}
		if($count_pendiente == 0){
			$this->b_modify_visible		= false;
			$this->dws['dw_carga_orsan_usach']->set_item(0, 'OCULTA_PROCESAR', 'none');
		}
	}

	function get_key()	{
		return $this->dws['dw_carga_orsan_usach']->get_item(0, 'COD_CARGA_ORSAN_USACH');
		
	}
	function save_record($db)	{	
		
		$COD_CARGA_ORSAN_USACH		= $this->get_key();
		$FECHA_CARGA_ORSAN			= $this->dws['dw_carga_orsan_usach']->get_item(0,'FECHA_CARGA_ORSAN');
		$TOTAL_REG_CARGA			= $this->dws['dw_carga_orsan_usach']->get_item(0,'TOTAL_REG_CARGA');
		$TOTAL_REG_EXITOSO			= $this->dws['dw_carga_orsan_usach']->get_item(0,'TOTAL_REG_EXITOSO');
		$TOTAL_REG_PDTE				= $this->dws['dw_carga_orsan_usach']->get_item(0,'TOTAL_REG_PDTE');
		$ORIGEN_PAGO				= $this->dws['dw_carga_orsan_usach']->get_item(0,'COD_ORIGEN_PAGO');
		
		
		$COD_CARGA_ORSAN_USACH		= ($COD_CARGA_ORSAN_USACH=='') ? "null" : $COD_CARGA_ORSAN_USACH;
		$TOTAL_REG_CARGA			= ($TOTAL_REG_CARGA=='') ? "null" : $TOTAL_REG_CARGA;
		$TOTAL_REG_EXITOSO			= ($TOTAL_REG_EXITOSO=='') ? "null" : $TOTAL_REG_EXITOSO;
		$TOTAL_REG_PDTE				= ($TOTAL_REG_PDTE=='') ? "null" : $TOTAL_REG_PDTE;
		$ORIGEN_PAGO				= ($ORIGEN_PAGO=='') ? "null" : $ORIGEN_PAGO;			
		$FECHA_CARGA_ORSAN 			= "to_date('$FECHA_CARGA_ORSAN', 'dd/mm/yyyy')";
		
         
		
		$sp = 'spu_carga_orsan_usach';
		if ($this->is_new_record()){
			$operacion = 'INSERT';
	
		}
		else{
			$operacion = 'UPDATE';		
		}			
		$param = " '$operacion'
					,$COD_CARGA_ORSAN_USACH			
					,$this->cod_usuario				
					,$FECHA_CARGA_ORSAN
					,$TOTAL_REG_CARGA
					,$TOTAL_REG_EXITOSO
					,$TOTAL_REG_PDTE
					,$ORIGEN_PAGO";	
		
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$COD_CARGA_ORSAN_USACH = $db->GET_IDENTITY('CARGA_ORSAN_USACH');
				$this->dws['dw_carga_orsan_usach']->set_item(0, 'COD_CARGA_ORSAN_USACH', $COD_CARGA_ORSAN_USACH);
			}
			    
			if (!$this->dws['dw_item_carga_orsan_usach']->update($db,$COD_CARGA_ORSAN_USACH)){
				return false;
			}
					
			return true;
		}
		return false;
		
	}
	function procesa_carga(){
		$COD_CARGA_ORSAN_USACH	=$this->get_item_wo($this->current_record, 'COD_CARGA_ORSAN_USACH');
		$cod_usuario= $this->cod_usuario;
		$cod_proceso = 21; 
		
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
		$stid = oci_parse($conn, "begin spp_usach_carga_orsan ($COD_EJECUCION_PROCESO, $COD_CARGA_ORSAN_USACH, $cod_usuario); end;");
		if (oci_execute($stid)){
			print "<script>document.getElementById('img_loader').style.display = 'none';</script>"; 
			oci_free_statement($stid);
			oci_close($conn);
			$this->dws['dw_carga_orsan_usach']->retrieve($COD_CARGA_ORSAN_USACH);
			$this->dws['dw_item_carga_orsan_usach']->retrieve($COD_CARGA_ORSAN_USACH);
			
			$modulo = 'carga_orsan_usach';
			session::set('DESDE_wo_'.$modulo, 'desde output');	// para indicar que viene del output
			$rec_no = $_REQUEST['rec_no'];
			$cod_item_menu= $_REQUEST['cod_item_menu'];
			$url= K_ROOT_URL.'appl/carga_orsan_usach/wi_carga_orsan_usach.php?rec_no='.$rec_no.'&cod_item_menu='.$cod_item_menu;
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
			$this->dws['dw_carga_orsan_usach']->retrieve($COD_CARGA_ORSAN_USACH);
			$this->dws['dw_item_carga_orsan_usach']->retrieve($COD_CARGA_ORSAN_USACH);
			//$this->redraw(); falta implementar otra recarga
		}
	}
	
	function genera_xls() {
		$COD_CARGA_ORSAN_USACH = $this->get_item_wo($this->current_record, 'COD_CARGA_ORSAN_USACH');

		require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/libs/simplexlsxgen/src/SimpleXLSXGen.php";
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 1800);//30 minutos

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT   RUT
					,ANO_CUOTA
					,NRO_CUOTA
					,TO_CHAR(FECHA_PAGO, 'dd/mm/yyyy') FECHA_PAGO
					,MONTO_TOTAL_PESOS
					,MONTO_TOTAL_UTM
					,COD_DEUDA
					,TO_CHAR(FECHA_VENCTO, 'dd/mm/yyyy') FECHA_VENCTO
					,COD_ORIGEN_PAGO
					,TO_CHAR(FECHA_DEPOSITO, 'dd/mm/yyyy') FECHA_DEPOSITO
					,EC.NOM_ESTADO_CARGA
				FROM	ITEM_CARGA_ORSAN_USACH IT, ESTADO_CARGA EC
				WHERE IT.COD_ESTADO_CARGA= EC.COD_ESTADO_CARGA
				AND IT.COD_CARGA_ORSAN_USACH= $COD_CARGA_ORSAN_USACH
				ORDER BY fecha_pago, cod_deuda, ano_cuota, cod_item_carga_orsan_usach";
		$res = $db->query($sql);

		$rows = [['<b>RUT</b>', '<b>ANO_CUOTA</b>', '<b>NRO_CUOTA</b>', '<b>FECHA_PAGO</b>', '<b>MONTO_TOTAL_PESOS</b>', '<b>MONTO_TOTAL_UTM</b>', '<b>COD_DEUDA</b>', '<b>FECHA_VENCTO</b>', '<b>COD_ORIGEN_PAGO</b>', '<b>FECHA_DEPOSITO</b>', '<b>ESTADO</b>']];
		while ($my_row = $db->get_row()) {
			$rows[] = [$my_row[0], $my_row[1], $my_row[2], $my_row[3], $my_row[4], $my_row[5], $my_row[6], $my_row[7], $my_row[8], $my_row[9], $my_row[10]];
		}

		if ($db->database_type == "oci") {
			oci_free_statement($db->query_id);
			$db->query_id = false;
		}

		\Shuchkin\SimpleXLSXGen::fromArray($rows, 'Registros Carga Orsan')->downloadAs($this->nom_tabla.'.xlsx');
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
class dw_item_carga_orsan_usach extends datawindow{
    function __construct() {
		
	$sql = sql_carga_orsan_usach::get_sql('ITEM_CARGA_ORSAN');
	$this->b_delete_visible  = false;	
	
	parent::__construct($sql, 'ITEM_CARGA_ORSAN', true, true);
	$this->add_control($control=new edit_num('RUT',10,10));
	$this->add_control($control=new edit_text('ANO_CUOTA',8,4));
	$control->set_onKeypress("return onlyNumbers(this, event,0, 1);");
	$this->add_control($control=new edit_text('COD_DEUDA',10,10));
	$control->set_onKeypress("return onlyNumbers(this, event,0, 1);");
	$this->add_control($control= new static_link('COD_ITEM_CARGA_ORSAN_USACH', '#'));
	$control->set_onClick("abrirVentanaBitacora(this)");
	$this->add_control(new edit_date('FECHA_DEPOSITO'));
	$this->add_control(new edit_date('FECHA_PAGO'));
	$this->add_control(new edit_text_hidden('COD_ITEM_CARGA_ORSAN_H'));
	$this->add_control(new static_text('NOM_ESTADO_CARGA'));
	$this->add_control(new edit_text('COD_ESTADO_CARGA',10,10,'hidden'));
	$this->add_control(new static_num('MONTO_TOTAL_UTM', 4));
	$this->add_control(new static_num('MONTO_TOTAL_PESOS'));
	$this->add_control(new static_num('MONTO_DESCTO_UTM', 4));
	$this->add_control(new static_num('MONTO_DESCTO_PESOS'));
	$this->add_control(new edit_num('COD_ORIGEN_PAGO',8,4));
	$this->add_control(new edit_num('NRO_CUOTA',8,4));
	
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
	function update($db, $COD_CARGA_ORSAN_USACH) {
		$cod_usuario= $this->cod_usuario;
		$sp = 'spu_item_carga_orsan_usach';		
			for ($i = 0; $i < $this->row_count(); $i++)		{
				$statuts = $this->get_status_row($i);
				if ($statuts == K_ROW_NOT_MODIFIED || $statuts == K_ROW_NEW)
					continue;
				
				$COD_ITEM_CARGA_ORSAN	= $this->get_item($i, 'COD_ITEM_CARGA_ORSAN_H');
				$RUT					= $this->get_item($i, 'RUT');
				$ANO_CUOTA				= $this->get_item($i, 'ANO_CUOTA');
				$COD_DEUDA				= $this->get_item($i, 'COD_DEUDA');
				$FECHA_PAGO				= $this->get_item($i, 'FECHA_PAGO');
				$COD_ORIGEN_PAGO		= $this->get_item($i, 'COD_ORIGEN_PAGO');	
				$FECHA_DEPOSITO			= $this->get_item($i, 'FECHA_DEPOSITO');	
				$NRO_CUOTA				= $this->get_item($i, 'NRO_CUOTA');	
				
				$RUT		= ($RUT=='') ? "null" : $RUT;
				$ANO_CUOTA	= ($ANO_CUOTA=='') ? "null" : $ANO_CUOTA;
				$COD_DEUDA	= ($COD_DEUDA=='') ? "null" : $COD_DEUDA;
				$FECHA_PAGO	= ($FECHA_PAGO=='') ? "null" : $FECHA_PAGO;
				$COD_ORIGEN_PAGO= ($COD_ORIGEN_PAGO=='') ? "null" : $COD_ORIGEN_PAGO;
				$FECHA_DEPOSITO = ($FECHA_DEPOSITO=='') ? "null" : $FECHA_DEPOSITO;
				$NRO_CUOTA = ($NRO_CUOTA=='') ? "null" : $NRO_CUOTA;

				if($FECHA_PAGO != "null"){
					$FECHA_PAGO = "to_date('$FECHA_PAGO', 'dd/mm/yyyy')";
				}
				if($FECHA_DEPOSITO != "null"){
					$FECHA_DEPOSITO = "to_date('$FECHA_DEPOSITO', 'dd/mm/yyyy')";
				}
				
				
				$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
				$sql= 	"SELECT COD_CARGA_ORSAN_USACH
						FROM ITEM_CARGA_ORSAN_USACH
						WHERE COD_ITEM_CARGA_ORSAN_USACH=$COD_ITEM_CARGA_ORSAN";
				$result = $db->build_results($sql); 
				
				$COD_CARGA_ORSAN_USACH = $result[0]['COD_CARGA_ORSAN_USACH'];
					
				if ($statuts == K_ROW_NEW_MODIFIED)
					$operacion = 'INSERT';
				elseif ($statuts == K_ROW_MODIFIED)
					$operacion = 'UPDATE';
					
				//operacion siempre es UPDATE
				$param = "'$operacion'
							,$COD_ITEM_CARGA_ORSAN
							,$COD_CARGA_ORSAN_USACH
							,null	
							,$RUT
							,null
							,null
							,null
							,null
							,null
							,$ANO_CUOTA
							,null
							,$NRO_CUOTA
							,$FECHA_PAGO
							,null
							,null
							,null
							,null
							,null
							,null
							,null
							,null
							,null
							,$COD_DEUDA
							,null
							,null
							,null
							,$COD_ORIGEN_PAGO
							,$FECHA_DEPOSITO
							,$cod_usuario";
							
				if (!$db->EXECUTE_SP($sp, $param))
					return false;
				}
		return true;
		
	}

}
?>