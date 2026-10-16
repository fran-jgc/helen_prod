<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../proc_common/class_proceso_php.php");

ini_set('memory_limit', '480M');
ini_set('max_execution_time', 900); //900 seconds = 15 minutes 
ini_set('mssql.connect_timeout','300');
ini_set('mssql.timeout','300');


class wp_cartera_conocida_base extends w_proceso {
	const K_CARTERA_CONOCIDA = 11;
	const K_PLAN_MOROSA	= 4;
	var $fecha_ejecucion;

   	function __construct() {
		$this->fecha_ejecucion = $this->str2date($this->current_date(), $this->current_time());

   		if(K_TIPO_BD == "oci"){
			$sql = "select	null COD_TIPO_DEUDA
	    			from DUAL";
 		}
   		else if(K_TIPO_BD == "mssql"){
	   		$sql = "select	null COD_TIPO_DEUDA";
   		}
 		$this->dws['dw_cartera_conocida'] = new datawindow($sql); 
    	
 		parent::__construct('cartera_conocida', $_REQUEST['cod_item_menu'], self::K_CARTERA_CONOCIDA);
   	}
	function load_dws() {
		parent::load_dws();
		$this->dws['dw_cartera_conocida']->retrieve($this->cod_proceso);
	}
	function make_cmd_sp($cod_ejecucion_proceso) {
		$cod_usuario = $this->cod_usuario;

		if (K_TIPO_BD=="oci"){
			return "spp_cartera_conocida($cod_ejecucion_proceso, $cod_usuario, $this->fecha_ejecucion)";
		}else if (K_TIPO_BD=="mssql"){
			return "spp_cartera_conocida $cod_ejecucion_proceso, $cod_usuario, $this->fecha_ejecucion";
		}
	}

	function file(){
		
		$space_cero = 0;
		$i = 0; 
		while($i<=210){
			$space_cero .= 0;
		$i++;
		}
		$space_vacio = ' ';
		$i = 0; 
		while($i<=210){
			$space_vacio .= ' ';
		$i++;
		}
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "EXEC spdw_cartera_conocida";
		$res = $db->query($sql);
		$count = $db->count_rows();
		
		if($count != 0){
			$COD_UNIVERSIDAD = 4;
			$db_uni = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$sql_uni = "SELECT	 SUBSTRING(REPLACE(dbo.f_get_parametro($COD_UNIVERSIDAD),'.',''),0,9) RUT_UNI
								,SUBSTRING(REPLACE(dbo.f_get_parametro($COD_UNIVERSIDAD),'.',''),10,11) DIG_VERIF_UNI
								,CONVERT(VARCHAR(10),GETDATE(),112) FECHA_ARCHIVO";
			$result_uni = $db_uni->build_results($sql_uni);
			
			
			$tipo_registro = 1;
			$rut_empresa = $result_uni[0]['RUT_UNI'];
			$dig_verif_empresa = $result_uni[0]['DIG_VERIF_UNI'];
			$fecha_archivo_envio =  $result_uni[0]['FECHA_ARCHIVO'];
			
			$ENCABEZADO  = substr($tipo_registro.$space_cero, 0, 1);				//1 TIPO REGISTRO ENCABEZADO
			$ENCABEZADO .= substr($space_cero.$rut_empresa, -10);					//2 RUT DE LA EMPRESA PRESTA SERVICIO
			$ENCABEZADO .= substr($dig_verif_empresa.$space_cero, 0, 1);			//3 DIGITO RUT DE LA EMPRESA PRESTA SERVICIO
			$ENCABEZADO .= substr($space_cero.$fecha_archivo_envio, -8);			//4 FECHA DE CREACION DE RESGITROS
			$ENCABEZADO .= substr($space_vacio, 0, 190);
			
			$ENCABEZADO = substr($ENCABEZADO,0,210);
			
			//creacion de archivo
			$name_archivo = "cuponeras.txt";
			$fname = tempnam("/tmp", $name_archivo);
			$handle = fopen($fname,"w");
			fwrite($handle, $ENCABEZADO);
			fwrite($handle, "\r\n");

			//BOLETAS
			$i = 0;
			$MONTO_TOTAL = 0;
			while($my_row = $db->get_row()){
				$tipo_registo_cuota = $my_row['TIPO_REGISTRO_CUOTA'];	
				$nro_contrato 		= $my_row['LLAVE_OPERACION_DIG_VERIF'];   //SE CAMBIO $result[$i]['NRO_CONTRATO'];
				$rut_aceptante 		= $my_row['RUT_DEUDOR'];
				$dig_verif 			= $my_row['DIG_VERIF_DEUDOR'];
				
				$cod_moneda = $my_row['COD_MONEDA'];
				
				if($cod_moneda == 1)
					$cod_moneda = '0'; // PESOS
				else if($cod_moneda == 2)
					$cod_moneda = '1'; // UF
				else if($cod_moneda == 3)
					$cod_moneda = '4'; // UTM
				else
					$cod_moneda = '8'; // DOLAR
				
				$cod_moneda = $cod_moneda; 
				$monto_cuota1 = $my_row['MONTO_CUOTA'];
				$monto_cuota = number_format($monto_cuota1, 4, '.', '');
				$fecha_vencimiento = $my_row['FECHA_VENCIMIENTO'];
				$descripcion_cuota = $my_row['DESCRIPCION_CUOTA'];
				$cod_convenio = $my_row['COD_CONVENIO'];
				$nom_deudor = $my_row['NOM_DEUDOR'];
				$calle_deudor = $my_row['DIRECCION_DEUDOR'];
				$numero_direc_deudor = $space_vacio;			//CAMPO no existe
				$block_direc_deudor = $space_vacio;			//CAMPO no existe
				$departamento_direc_deudor = $space_vacio;		//CAMPO no existe
				$comuna_deudor = $my_row['COMUNA_DEUDOR'];
				$ciudad_deudor = $my_row['CIUDAD_DEUDOR'];
				$llave_operacion_dig_verif = $my_row['LLAVE_OPERACION_DIG_VERIF'];
				$llave_operacion = substr($llave_operacion_dig_verif, 0, -1);
				$dig_verif_operacion = substr($llave_operacion_dig_verif, -1);
				$nro_cuota = $my_row['NRO_CUOTA'];
				$resultado_carga = $space_vacio;				//CAMPO UTILIZADO POR EL BANCO SE DEJA EN BLANCO
				$space = $space_vacio;
				
				$linea  = substr($space_cero.$tipo_registo_cuota, -1);
				$linea .= substr($space_cero.$nro_contrato, -20);
				$linea .= substr($space_cero.$rut_aceptante, -10);
				$linea .= substr($dig_verif.$space_cero, 0, 1);
				$linea .= substr($space_cero.$cod_moneda, -1);
				$linea .= substr($space_cero.str_replace(".", "", $monto_cuota), -14);
				$linea .= substr($space_cero.$fecha_vencimiento, -8);
				$linea .= substr($descripcion_cuota.$space_vacio, 0, 20);
				$linea .= substr($space_cero.$cod_convenio, -5);
				$linea .= substr($nom_deudor.$space_vacio, 0, 40);
				$linea .= substr($calle_deudor.$space_vacio, 0, 25);
				$linea .= substr($numero_direc_deudor.$space_vacio, 0, 6);
				$linea .= substr($block_direc_deudor.$space_vacio, 0, 5);
				$linea .= substr($departamento_direc_deudor.$space_vacio, 0, 5);
				$linea .= substr($comuna_deudor.$space_vacio, 0, 15);
				$linea .= substr($ciudad_deudor.$space_vacio, 0, 15);
				$linea .= substr($space_cero.$llave_operacion, -11);
				$linea .= substr($dig_verif_operacion.$space_cero, 0, 1);
				$linea .= substr($space_cero.$nro_cuota, -3);
				$linea .= substr($space_vacio.$resultado_carga, -1);			//CAMPO UTILIZADO POR EL BANCO SE DEJA EN BLANCO
				$linea .= substr($space.$space_vacio, 0, 3);
				
				$linea = substr($linea, 0, 210);

				$MONTO_TOTAL = $MONTO_TOTAL + $monto_cuota1;
				
				fwrite($handle, $linea);
				fwrite($handle, "\r\n");
			}
			$MONTO_TOTAL = number_format($MONTO_TOTAL, 4, '.', '');
			$MONTO_TOTAL = substr($space_cero.str_replace(".", "", $MONTO_TOTAL), -18);
			
			$tipo_registro = 3;
			$cantidad_registros = $count;		//count a result de select  
			$space = $space_vacio;
			
			$TIPO_REGISTRO		= substr($space_cero.$tipo_registro, -1);
			$CANTIDAD_REGISTROS	= substr($space_cero.$cantidad_registros, -6);
			
			$SPACIO				= substr($space.$space_vacio, 0, 185);

			$TOTALES  = $TIPO_REGISTRO;
			$TOTALES .= $CANTIDAD_REGISTROS;
			$TOTALES .= $MONTO_TOTAL;
			$TOTALES .= $SPACIO;
			
			$TOTALES = substr($TOTALES,0,210);
			fwrite($handle, $TOTALES);
			
			fclose($handle);
			header("Content-type: application/force-download; name=\"$name_archivo\"");
			header("Content-Disposition: inline; filename=\"$name_archivo\"");
			$fh=fopen($fname, "rb");
			$fh=fopen($fname, "rb");
			fpassthru($fh);
		}else{
			$this->redraw();
			$this->alert('No Existen boletas creadas desde cuponera');
		}
	}

	function inicia_proceso(){
	    $cod_usuario= $this->cod_usuario;
	    $cod_proceso = 11;
	    
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
	    $stid = oci_parse($conn, "begin spp_cartera_conocida ($COD_EJECUCION_PROCESO, $cod_usuario, $this->fecha_ejecucion); end;");
	    if (oci_execute($stid)){
	        print "<script>document.getElementById('img_loader').style.display = 'none';</script>";
	        print "<script>document.getElementById('fin_proceso').style.display = '';</script>";
	        oci_free_statement($stid);
	        oci_close($conn);
	    }
	    else{
	        print "<script>document.getElementById('img_loader').style.display = 'none';</script>";
	        print "<script>document.getElementById('fin_proceso').style.display = 'none';</script>";
	        $error = true;
	        ob_end_flush();
	        $e = oci_error($stid);
	        $error_code= $e['code'];
	        $error_sqltext= $e['sqltext'];
	        
	        $this->redraw();
	        $this->alert('Error Oracle texto sql ejecutado: '.$error_sqltext);
	        
	        oci_free_statement($stid);
	        oci_close($conn);
	    }
	}
	
	
	function procesa_event(){
		if(isset($_POST['b_file'])){
			$this->file();
		}else if(isset($_POST['b_procesar']))
		    $this->inicia_proceso();
		else
			parent::procesa_event();
	}
}
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wp_cartera_conocida.php";
if (file_exists($file_name)) {
	require_once($file_name);
}
else {
	class wp_cartera_conocida extends wp_cartera_conocida_base {
	    function __construct() {
	        parent::__construct(); 
		}
	}
}

?>