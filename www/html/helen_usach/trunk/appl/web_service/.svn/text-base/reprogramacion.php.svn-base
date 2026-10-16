<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/nusoap-0.7.3/lib/nusoap.php");
require_once ("../../appl.ini");
require_once ("class_ws_cliente.php");
require_once(dirname(__FILE__)."/class_database.php");

session::set('K_ROOT_URL', K_ROOT_URL);
session::set('K_ROOT_DIR', K_ROOT_DIR);
session::set('K_APPL', K_APPL);
session::set('K_CLIENTE', K_CLIENTE);

class w_reprogramacion extends w_base{
	function w_reprogramacion() {
		// No llama al parent, sino q llama al abuelo
		parent::base();
		$this->work_directory = getcwd();
	}
	function wakeup_id($wakeup_id) {
		$K_PARAM_RUT_UNI = 4;
		$K_PARAM_MINEDUC = 33;
		
		$t = new Template_appl(K_CLIENTE."/repro_index.htm");
		
		/* se ocupa cuando es WS de mineduc 
		demo => "fscu.demo.exec.cl" 
		oficial => reprogramar.creditosolidario.cl*/
		//$serverURL = "http://fscu.demo.exec.cl/ws/getDeudor.php";
		//$serverURL = "http://fscu.curie.exec.cl/login/index.php"; // otro ambiente
		$serverURL = "http://reprogramar.creditosolidario.cl/ws/getDeudor.php";
		
		$serverScript = 'getDeudor.php';
		$metodoALlamar = 'getDeudor';
		
		$rut_deudor = $wakeup_id;
		$nro_folio = '';
		$rut_universidad = str_replace('.', '', base::get_parametro($K_PARAM_RUT_UNI)); //$K_PARAM_RUT_UNI = 4
		$array = explode('-', $rut_universidad);
		$rut_universidad = $array[0];
		$div_universidad = $array[1];
		$clave_ws_mineduc = base::get_parametro($K_PARAM_MINEDUC); //$K_PARAM_MINEDUC = 33
		
		$ws_client = new ws_cliente($serverURL);
		$param = array('Universidad'=>$rut_universidad, 'clave'=>$clave_ws_mineduc, 'rutDeudor'=>$rut_deudor, 'idPagare'=>$nro_folio);
		$resultado = $ws_client->get_ws_result($param, $metodoALlamar);
	
		$error = $resultado['RESPUESTA']['MENSAJE'];
		if($error == 'Datos de autenticación incorrectos'){
			$msg_error = 'ERROR: LOGIN UNIVERSIDAD Incorrecto.';
			$t->setVar('MENSAJE_DEUDOR', $msg_error);
			$t->setVar('DISPLAY_TABLE_FRACASO', '');
			$t->setVar('DISPLAY_TABLE_EXITO', 'none');
			
		}else if($error == 'Datos de deudor incorrecto'){
			$msg_error = 'ERROR: Rut del Deudor Incorrecto.';
			$t->setVar('MENSAJE_DEUDOR', $msg_error);
			$t->setVar('DISPLAY_TABLE_FRACASO', '');
			$t->setVar('DISPLAY_TABLE_EXITO', 'none');
		}else{
			/*
			CONFIRMADA
			*/
			$db_deudor = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			if (K_TIPO_BD=="oci"){
				$sql_deudor = "SELECT	NOMBRE||' '||APELLIDO_PATERNO||' '||APELLIDO_MATERNO NOM_DEUDOR
							FROM 	DEUDOR
							WHERE	RUT = $rut_deudor";
			}else if (K_TIPO_BD=="mssql"){
				$sql_deudor = "SELECT	NOMBRE + ' ' + APELLIDO_PATERNO + ' ' + APELLIDO_MATERNO NOM_DEUDOR
							FROM 	DEUDOR
							WHERE	RUT = $rut_deudor";
			}
	
			$result_deudor = $db_deudor->build_results($sql_deudor);
			$nom_deudor	= $result_deudor[0]['NOM_DEUDOR'];
			
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			//no crea  nada si tiene una repro CONFIRMADA
			$sql = "SELECT	RE.COD_REPROGRAMACION
						FROM	REPROGRAMACION RE, DEUDOR_REPROGRAMACION DRE
						WHERE	DRE.RUT = $rut_deudor
							AND RE.COD_REPROGRAMACION = DRE.COD_REPROGRAMACION
							AND RE.COD_ESTADO_REPROGRAMACION = 2"; //CONFIRMADA
			$result = $db->build_results($sql);
			$cant_confirmada = $db->count_rows();

			/*
			CON PAGOS
			*/
			$cant_pago_pp = 0;
			$cant_pago_abono = 0;
			
			$db2 = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

			$sql2 = "select D.COD_DEUDOR 
			from DEUDOR D, DEUDA DA, PLAN_PAGO PP
			where D.RUT = $rut_deudor
				AND D.COD_DEUDOR = DA.COD_DEUDOR
				AND DA.COD_TIPO_DEUDA = 'CRPI'
				AND PP.COD_DEUDA = DA.COD_DEUDA
				AND PP.COD_ESTADO_PLAN_PAGO IN (2, 3)"; //ABONADA, PAGADA
			
			$result2 = $db2->build_results($sql2);
			$cant_pago_pp = $db2->count_rows();

			$db3 = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$sql3 = "select D.COD_DEUDOR
			from DEUDOR D, DEUDA DA, ABONO A
			where D.RUT = $rut_deudor
				AND D.COD_DEUDOR = DA.COD_DEUDOR
				AND DA.COD_TIPO_DEUDA = 'CRPI'
				AND A.COD_DEUDA = DA.COD_DEUDA
				AND A.COD_ESTADO_PAGO = 1"; //VIGENTE
			
			$result3 = $db3->build_results($sql3);
			$cant_pago_abono = $db3->count_rows();
			
			if($cant_confirmada > 0){
				$table_deudor = 'Estimado '.$nom_deudor.' Lo sentimos. Usted tiene una Reprogramacion ya Confirmada.';
				$t->setVar('MENSAJE_DEUDOR', $table_deudor);
				$t->setVar('DISPLAY_TABLE_FRACASO', '');
				$t->setVar('DISPLAY_TABLE_EXITO', 'none');
			}
			else if ($cant_pago_pp > 0 || $cant_pago_abono > 0){
				//direcciona al webpay correspondiente al K_CLIENTE
				$K_CLIENTE = session::get('K_CLIENTE');
				switch($K_CLIENTE) {
					case "UCM": $ruta_webpay = 'http://fscu.helen.cl/web_ucm/'; break;
					case "UACH": $ruta_webpay = '../webpay_uach/index.html'; break;
					case "USACH": $ruta_webpay = 'http://fondocredito.usach.cl/helen/trunk/appl/webpay_usach/'; break;
					case "UV": $ruta_webpay = 'http://fondocredito.uv.cl/fscu/'; break;
					case "UCT": $ruta_webpay = '../webpay_uct/index.html'; break;
					case "ULAGOS": $ruta_webpay = '../webpay_uv/index.html'; break; //no tiene Webpay hay que direccionar
				}
				
				$table_deudor  = $nom_deudor.' Lo sentimos.<br>Usted tiene una Reprogramación con Pagos Aprobados.';
				$table_deudor .= '<br>Acerquese a la Universidad a regularizár su Reprogramación. Llevando su Pagaré marcado como definitivo en Mineduc al día de Hoy.';
				$table_deudor .= '<br><br><br><br>
									<form id="form3" name="form3" method="post" action="'.$ruta_webpay.'">
										<input type="submit" style="width:70px;height:24px;background:#16264A;color:white;" id="b_ingresar" name="b_ingresar" value="Continuar" />
									</form>';
				$t->setVar('MENSAJE_DEUDOR', $table_deudor);
				$t->setVar('DISPLAY_TABLE_FRACASO', '');
				$t->setVar('DISPLAY_TABLE_EXITO', 'none');
				
				//se obtiene la ultima repro que tiene el Deudor
				$sql = "SELECT	Max(COD_REPROGRAMACION) COD_REPROGRAMACION
						FROM	DEUDOR_REPROGRAMACION
						WHERE	RUT = $rut_deudor";
				$result = $db->build_results($sql);
				$cod_reprogramacion	= $result[0]['COD_REPROGRAMACION'];
				
				//graba el usuario => reprogramacion web = 0
				$db->BEGIN_TRANSACTION();
				$sp = 'spu_log_reprogramacion';
				$param ="$cod_reprogramacion
						,0
						,'REPROGRAMACION'
						,'cod_reprogramacion'
						,'Deudor Rut:$rut_deudor intento crear una Repro y anular otra Repro con su pago asociado.'";
				$exec = $db->EXECUTE_SP($sp, $param);
				$db->COMMIT_TRANSACTION();
			}
			else{				
				$count_pagare = $ws_client->get_cant_tags('PAGARE');
				//validacion de pagares devueltops con el digitado por el user
				if($count_pagare==1){
					$nro_folio = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["ID_PAGARE"];
				}else if($count_pagare > 1){
					$nro_folio = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][0]["ID_PAGARE"];
				}
				if($ws_client->WS_save($param, $metodoALlamar,$nro_folio)){
					$resultado = $ws_client->get_ws_result($param, $metodoALlamar);
					$cod_reprogramacion_ws = $ws_client->get_cod_reprogramacion();

					//graba el usuario => reprogramacion web = 0
					$sp = 'spu_log_reprogramacion';
					$param ="$cod_reprogramacion_ws
							,0
							,'REPROGRAMACION'
							,'cod_reprogramacion'
							,'Creacion Reprogramacion Via Boton Mineduc. Rut:$rut_deudor'";
					$exec = $db->EXECUTE_SP($sp, $param);
					
					//marca  como anulada las repros previas  a la creada como EMITIDA.
					$sql = "SELECT	RE.COD_REPROGRAMACION
							FROM	REPROGRAMACION RE, DEUDOR_REPROGRAMACION DRE
							WHERE	DRE.RUT = $rut_deudor
								AND RE.COD_REPROGRAMACION = DRE.COD_REPROGRAMACION
								AND RE.COD_ESTADO_REPROGRAMACION = 1
							ORDER BY RE.COD_REPROGRAMACION DESC"; //EMITIDA
					$result = $db->build_results($sql);
					$cant_emitida = $db->count_rows();
					
					if($cant_emitida > 1){
						$db->BEGIN_TRANSACTION();
						for($x=0;$x<$cant_emitida;$x++){
							$cod_reprogramacion	= $result[$x]['COD_REPROGRAMACION'];
							//ANULA LAS REPRO. deja  afuera del update el ultimo creado
							if($cod_reprogramacion_ws != $cod_reprogramacion){
								$db->query("UPDATE REPROGRAMACION
											SET COD_ESTADO_REPROGRAMACION = 3
											WHERE COD_REPROGRAMACION = $cod_reprogramacion");
								
								if(!$db->EXECUTE_SP('sp_deuda_repro_crpi', "'ANULA', $cod_reprogramacion")) {
									$db->ROLLBACK_TRANSACTION();
								}
								//graba el usuario => reprogramacion web = 0
								$sp = 'spu_log_reprogramacion';
								$param ="$cod_reprogramacion
										,0
										,'REPROGRAMACION'
										,'cod_reprogramacion'
										,'Anulacion de Reprogramacion Via Boton Mineduc. Rut:$rut_deudor'";
								$exec = $db->EXECUTE_SP($sp, $param);
							}
						}
						$db->COMMIT_TRANSACTION();
					}
					
					//direcciona al webpay correspondiente al K_CLIENTE
					$K_CLIENTE = session::get('K_CLIENTE');
					//define("K_CLIENTE", "UCM");
					//define("K_CLIENTE", "UACH");
					//define("K_CLIENTE", "USACH");
					//define("K_CLIENTE", "UV");
					//define("K_CLIENTE", "UCT");
					//define("K_CLIENTE", "ULAGOS");
					switch($K_CLIENTE) {
						case "UCM": $ruta_webpay = 'http://fscu.helen.cl/web_ucm/'; break;
						case "UACH": $ruta_webpay = '../webpay_uach/index.html'; break;
						case "USACH": $ruta_webpay = 'http://fondocredito.usach.cl/helen/trunk/appl/webpay_usach/'; break;
						case "UV": $ruta_webpay = 'http://fondocredito.uv.cl/fscu/'; break;
						case "UCT": $ruta_webpay = '../webpay_uct/index.html'; break;
						case "ULAGOS": $ruta_webpay = '../webpay_uv/index.html'; break; //no tiene Webpay hay que direccionar
					}
					$t->setVar('DISPLAY_TABLE_FRACASO', 'none');
					$t->setVar('DISPLAY_TABLE_EXITO', '');
				}else{
					$t->setVar('MENSAJE_DEUDOR', $ws_client->GET_ERROR());
					$t->setVar('DISPLAY_TABLE_FRACASO', '');
					$t->setVar('DISPLAY_TABLE_EXITO', 'none');
				}
			}
		}
		
		$t->setVar('K_ROOT_URL', K_ROOT_URL);
		$t->setVar('W_RUTA_PAGO', $ruta_webpay);	
		$t->setVar("W_FECHA_ACTUAL", $this->current_date());		
		print $t->toString();
	}
	function sin_wakeup_id() {
		$t = new Template_appl(K_CLIENTE."/repro_index.htm");
		
		$table_deudor = 'Estimado Usuario Lo sentimos. Intente más tarde.';
		
		$t->setVar('DISPLAY_TABLE_FRACASO', '');
		$t->setVar('DISPLAY_TABLE_EXITO', 'none');
		$t->setVar('MENSAJE_DEUDOR', $table_deudor);
		
		$t->setVar("W_FECHA_ACTUAL", $this->current_date());	
		$t->setVar('W_RUTA_PAGO', $ruta_webpay);		
		print $t->toString();
		
		print '<script type="text/javascript">
						alert("sin_wakeup_id");
					 </script>';
	}
	function procesa_event() {		
		if(isset($_POST['b_ingresar_x'])){
			//$this->crear_desde_WS($_POST['wo_hidden']);
			$wakeup_id = $_GET['WAKEUP_ID'];
			if ($wakeup_id != ''){
				$w->wakeup_id($wakeup_id);
			}else
				$w->sin_wakeup_id();
		}else{
			parent::procesa_event();
		}
	}
}

$wakeup_id = $_GET['WAKEUP_ID'];
$w = new w_reprogramacion();
if ($wakeup_id != ''){
	$w->wakeup_id($wakeup_id);
}else
	$w->sin_wakeup_id();
?>