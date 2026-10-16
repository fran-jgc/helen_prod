<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/nusoap-0.7.3/lib/nusoap.php");
require("xml2array.php");

class ws_cliente extends nusoap_client {
	var $serverURL;
	var $serverScript;
	var $metodoALlamar;
	var $cod_reprogramacion;
	var $msg_error = '';
	var $return_xml = '';

	function ws_cliente($serverURL) {

		/*
		$serverScript, funcion� con 'WS_server.php', extra�amente. IS 14/09/2012
		*/
		//$serverScript = 'WS_server.php';
		$serverScript = 'getDeudor.php';
		$metodoALlamar = 'getDeudor';
		parent::nusoap_client("$serverURL/$serverScript?wsdl", 'wsdl');
		$this->serverURL = $serverURL;
		$this->serverScript = $serverScript;
		$this->metodoALlamar = $metodoALlamar;

		$error = $this->getError();

		if ($error) {
			echo '<pre style="color: red">' . $error . '</pre>';
			echo '<p style="color:red;'>htmlspecialchars($this->getDebug(), ENT_QUOTES).'</p>';
			die();
		}
	}

	function get_value_data($data) {
		if (is_array($data)) {
			$count = count($data);
			if($count == 0){
				return '';
			}else{
				return $data;
			}
		}else{
			return $data;
		}
	}

	function set_repro_confirma($param=array()){
		// => http://fscu.demo.exec.cl/ws/reprogramar.php?wsdl
		//confirmar la reprogramacion 
		/*No olvidar que para hacer v�lida la reprogramaci�n se debe:
		1. Firmar y legalizar ante notario el pagar� generado en este sitio.
		2. Firmar y legalizar ante notario el Manifiesto de la Voluntad de Reprogramaci�n.
		3. Realizar el Pago Inicial correspondiente.
		4. Presentar en la universidad correspondiente los documentos legalizados del Manifiesto y del Pagar�
			,junto al comprobante del pago inicial.
		*/
		$serverScript = 'reprogramar.php';
		$metodoALlamar = 'reprogramar';

		$result = $this->call("$metodoALlamar"		// Funcion a llamar
							  ,$param					// Parametros pasados a la funcion
							  ,"uri:$this->serverURL/$serverScript"					// namespace
							  ,"uri:$this->serverURL/$serverScript/$metodoALlamar"	// SOAPAction
							  );
		// Verificacion que los parametros estan ok, y si lo estan. mostrar rta.
		if ($this->fault) {
			return ($result);
		} else {
			$error = $this->getError();
			if ($error) {
				return '<b style="color: red">-Error: ' . $error . '</b>';
			} else {
				
				$xml_result = $result;
				return xml2array("<?xml version='1.0' encoding='iso-8859-1'?>".$xml_result);
				//return $xml_result ;
			}
		}		
	}
	
	function get_ws_result($param=array(), $metodoALlamar = ''){
		if($metodoALlamar == ""){
			$metodoALlamar = $this->metodoALlamar;
		}
		$result = $this->call("$metodoALlamar",			// Funcion a llamar
		$param,    				// Parametros pasados a la funcion
							 "uri:$this->serverURL/$this->serverScript",                     // namespace
							 "uri:$this->serverURL/$this->serverScript/$this->metodoALlamar" // SOAPAction
		);
		// Verificacion que los parametros estan ok, y si lo estan. mostrar rta.
		if ($this->fault) {
			return ($result);
		} else {
			$error = $this->getError();
			if ($error) {
				return '<b style="color: red">-Error: ' . $error . '</b>';
			} else {
				//return  base64_decode($result);  //si es de forma local
				$this->return_xml = $result;
				/*
				$result = str_replace('�','A',$result);
				$result = str_replace('�','E',$result);
				$result = str_replace('�','I',$result);
				$result = str_replace('�','O',$result);
				$result = str_replace('�','U',$result);

				*/
				$xml_result = $result;
				//return xml2array($xml_result);
				return xml2array("<?xml version='1.0' encoding='iso-8859-1'?>".$xml_result);
			}
		}
	}

	function get_cant_tags($find_tags) {
		return substr_count($this->return_xml, "<$find_tags>");		
	}
	
	function WS_save($param=array(), $metodoALlamar = '', $nro_folio){
		/*por_borrar*/
		//$param_aux = $param;
		/*$param2 = array('Universidad'=>60910000, 'clave'=>123, 'rutDeudor'=>'700653', 'idPagare'=>'');
		 $resultado = $this->get_ws_result($param2, $metodoALlamar);*/
		/*por_borrar*/
		//correcto
		// => $resultado = $this->get_ws_result($param, $metodoALlamar);
		//correcto

		$var_folio = false;

		$resultado = $this->get_ws_result($param, $metodoALlamar);
		//print_r($resultado);
		$error = $resultado['RESPUESTA']['MENSAJE'];
		if($error == 'Datos de autenticaci�n incorrectos'){
			$this->msg_error = 'ERROR: Autentificacion Universidad. Contactarse con Administrador del sistema';
			return false;
		}else if($error == 'Datos de deudor incorrecto'){
			$this->msg_error = 'ERROR: Rut del Deudor Incorrecto.';
			return false;
		}else{
			$count_pagare = $this->get_cant_tags('PAGARE');
			//validacion de pagares devueltops con el digitado por el user
			if($count_pagare==1){
				$id_pagare_folio = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["ID_PAGARE"];
				if($id_pagare_folio == $nro_folio){
					$var_folio = true;
				}
			}
			else if($count_pagare > 1){
				for($h=0;$h<$count_pagare;$h++){
					$id_pagare_folio = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["ID_PAGARE"];
					if($id_pagare_folio == $nro_folio){
						$var_folio = true;
					}
				}
			}
			
			if($var_folio == false){
				$this->msg_error = 'ERROR: El Folio del Pagare ingresado no existe.';
				return false;
			}
		}
		
		/*restriccion para el Rut cuando tenga una Repro emitida pero con pago (Abono. Caja o Webpay). 
		sin confirmar*/
		$db_sql = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$rut_deudor = $resultado["REPROGRAMACION"]["DEUDOR"]["RUT"];
		//no crea  nada si tiene una repro CONFIRMADA
		$sql = "SELECT DE.COD_DEUDA 
				FROM DEUDOR DO, DEUDA DE, ITEM_INGRESO IT, INGRESO I
				WHERE DO.RUT = $rut_deudor
				AND DO.COD_DEUDOR = DE.COD_DEUDOR
				AND DE.COD_TIPO_DEUDA = 'CRPI'
				AND DE.COD_ESTADO_DEUDA = 2 --EN COBRANZA NORMAL
				AND DE.COD_DEUDA = IT.COD_DEUDA 
				AND IT.COD_INGRESO = I.COD_INGRESO
				AND I.COD_ESTADO_INGRESO <> 2"; //2=>ANULADO
		$result = $db_sql->build_results($sql);
		$cant_abonos = $db_sql->count_rows();
		if($cant_abonos > 0){
			$nro_deuda = $result[0]['COD_DEUDA'];
			$this->msg_error = "El Deudor con Rut: $rut_deudor, tiene una Reprogramacion con Pagos Asociados.\nNro. Deuda: $nro_deuda";
			return false;
		}
		/*********************/

		if($resultado != ''){
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			if(isset($_SESSION["COD_USUARIO"])){
				$cod_usuario = session::get("COD_USUARIO");
				if ($cod_usuario ==2)
					$cod_usuario = 0;
			}else{
				$cod_usuario = '0'; //USUARIO REPRO
			}
			
			$db->BEGIN_TRANSACTION();
			$sp = "spu_reprogramacion";
			$param	=  "'INSERT'
					,NULL
					,NULL 
					,$cod_usuario 
					,1
					,'N'
					,'N'
					,'N'
					,NULL
					,NULL";
			if (K_CLIENTE == 'SURCOB_UACH')
				$param	.=  ",'SURCOB_UACH'";
			else if (K_CLIENTE == 'SURCOB_UCM')
				$param	.=  ",'SURCOB_UCM'";
			else if (K_CLIENTE == 'MASCOB_UCM')
				$param	.=  ",'MASCOB_UCM'";
			else
				$param	.=  ",NULL";
			
			if(!$db->EXECUTE_SP($sp, $param)) {
				$this->msg_error = 'ERROR: spu_reprogramacion ';
				//goto Error_BD;
				$db->ROLLBACK_TRANSACTION();
				return false;
			}
			$cod_reprogramacion = $db->GET_IDENTITY("reprogramacion");
			$this->cod_reprogramacion = $cod_reprogramacion;
			
			//array datos_deudor
			$rut = $resultado["REPROGRAMACION"]["DEUDOR"]["RUT"];
			$rut = str_replace('.', '', $rut);
			$dig_verif = $resultado["REPROGRAMACION"]["DEUDOR"]["DV"];
			$nombre = $resultado["REPROGRAMACION"]["DEUDOR"]["NOMBRES"];
			$nombre = str_replace("'", "''", $nombre);
			$apellido_paterno = $resultado["REPROGRAMACION"]["DEUDOR"]["APELLIDO_PATERNO"];
			$apellido_paterno = str_replace("'", "''", $apellido_paterno);
			$apellido_materno = $resultado["REPROGRAMACION"]["DEUDOR"]["APELLIDO_MATERNO"];
			$apellido_materno = str_replace("'", "''", $apellido_materno);
			$sexo = $resultado["REPROGRAMACION"]["DEUDOR"]["SEXO"];
			$fecha_nacimiento = $resultado["REPROGRAMACION"]["DEUDOR"]["FECHA_NACIMIENTO"];
			$codigo_area1 = $resultado["REPROGRAMACION"]["DEUDOR"]["CODIGO_AREA1"];
			$telefono1 = $resultado["REPROGRAMACION"]["DEUDOR"]["TELEFONO1"];
			$codigo_area2 = $resultado["REPROGRAMACION"]["DEUDOR"]["CODIGO_AREA2"];
			$telefono2 = $resultado["REPROGRAMACION"]["DEUDOR"]["TELEFONO2"];
			$email = $resultado["REPROGRAMACION"]["DEUDOR"]["EMAIL"];
			$direccion = $resultado["REPROGRAMACION"]["DEUDOR"]["DIRECCION"];
			$direccion = str_replace("'", "''", $direccion);
			$comuna = $resultado["REPROGRAMACION"]["DEUDOR"]["COMUNA"];
			$comuna = str_replace("'", "''", $comuna);
			$ciudad = $resultado["REPROGRAMACION"]["DEUDOR"]["CIUDAD"];
			$ciudad = str_replace("'", "''", $ciudad);
			$region = $resultado["REPROGRAMACION"]["DEUDOR"]["REGION"];
			$region = str_replace("'", "''", $region);
			
			//***valida los vacios ********///
			$codigo_area1	= $this->get_value_data($codigo_area1);
			$codigo_area2	= $this->get_value_data($codigo_area2);
			$telefono1	= $this->get_value_data($telefono1);
			$telefono2	= $this->get_value_data($telefono2);
			$email	= $this->get_value_data($email);
			
			$fecha_nacimiento = base::str2date($fecha_nacimiento);
				
			$sp = "spu_deudor_reprogramacion";
			$param	= "'INSERT'
			,NULL
			,$cod_reprogramacion
			,$rut
			,'$dig_verif'
			,'$nombre'
			,'$apellido_paterno'
			,'$apellido_materno'
			,'$sexo'
			,$fecha_nacimiento
			,'$codigo_area1'
			,'$telefono1'
			,'$codigo_area2'
			,'$telefono2'
			,'$email'
			,'$direccion'
			,'$comuna'
			,'$ciudad'
			,'$region'";

			if(!$db->EXECUTE_SP($sp, $param)) {
				$this->msg_error = 'ERROR: spu_deudor_reprogramacion';
				//goto Error_BD;
				$db->ROLLBACK_TRANSACTION();
				return false;
			}
				
			/*para poder determinar cuantos arreglos tiene ["deuda"], se implementa este truco para arreglos bidimencionales  16-05-2012   IS*/
			$count_deuda = $this->get_cant_tags('DEUDA');

			if($count_deuda==1){

				$cod_universidad_deuda = $resultado["REPROGRAMACION"]["DEUDAS_REPROGRAMADAS"]["DEUDA"]["COD_UNIVERSIDAD_DEUDA"];
				$cod_deuda = $resultado["REPROGRAMACION"]["DEUDAS_REPROGRAMADAS"]["DEUDA"]["NRO_DEUDA"];
				$cod_deuda	= $this->get_value_data($cod_deuda);
				$cod_deuda	= ($cod_deuda=='') ? "NULL" : $cod_deuda;
				
				if(!is_numeric($cod_deuda)){
					$cod_deuda = "NULL";
				}

				$cod_tipo_deuda = $resultado["REPROGRAMACION"]["DEUDAS_REPROGRAMADAS"]["DEUDA"]["TIPO_DEUDA"];

				$saldo_capital = $resultado["REPROGRAMACION"]["DEUDAS_REPROGRAMADAS"]["DEUDA"]["SALDO_CAPITAL"];
				$saldo_interes_penal = $resultado["REPROGRAMACION"]["DEUDAS_REPROGRAMADAS"]["DEUDA"]["SALDO_INTERES_PENAL"];
				//se elimina las comas por puntos
				$saldo_capital = str_replace(',', '.', $saldo_capital);
				$saldo_interes_penal = str_replace(',', '.', $saldo_interes_penal);
				
				$sp = "spu_deuda_reprogramacion";
				$param	= "'INSERT'
				,NULL
				,$cod_reprogramacion
				,$cod_universidad_deuda
				,$cod_deuda
				,'$cod_tipo_deuda'
				,$saldo_capital
				,$saldo_interes_penal";
				
				if(!$db->EXECUTE_SP($sp, $param)) {
					$this->msg_error = 'ERROR: spu_deuda_reprogramacion';
					//goto Error_BD;
					$db->ROLLBACK_TRANSACTION();
					return false;
				}
			}
			else{				
				for($i=0 ; $i < $count_deuda; $i++){
					$cod_universidad_deuda = $resultado["REPROGRAMACION"]["DEUDAS_REPROGRAMADAS"]["DEUDA"][$i]["COD_UNIVERSIDAD_DEUDA"];
					$cod_deuda = $resultado["REPROGRAMACION"]["DEUDAS_REPROGRAMADAS"]["DEUDA"][$i]["NRO_DEUDA"];
					$cod_deuda	= $this->get_value_data($cod_deuda);
					$cod_deuda	= ($cod_deuda=='') ? "NULL" : "$cod_deuda";
				
					if(!is_numeric($cod_deuda)){
						$cod_deuda = "NULL";
					}
				
					$cod_tipo_deuda = $resultado["REPROGRAMACION"]["DEUDAS_REPROGRAMADAS"]["DEUDA"][$i]["TIPO_DEUDA"];
					

					$saldo_capital = $resultado["REPROGRAMACION"]["DEUDAS_REPROGRAMADAS"]["DEUDA"][$i]["SALDO_CAPITAL"];
					$saldo_interes_penal = $resultado["REPROGRAMACION"]["DEUDAS_REPROGRAMADAS"]["DEUDA"][$i]["SALDO_INTERES_PENAL"];
						
					//se elimina las comas por puntos
					$saldo_capital = str_replace(',', '.', $saldo_capital);
					$saldo_interes_penal = str_replace(',', '.', $saldo_interes_penal);

					$sp = "spu_deuda_reprogramacion";
					$param	= "'INSERT'
					,NULL
					,$cod_reprogramacion
					,$cod_universidad_deuda
					,$cod_deuda
					,'$cod_tipo_deuda'
					,$saldo_capital
					,$saldo_interes_penal";

					if(!$db->EXECUTE_SP($sp, $param)) {
						$this->msg_error = 'ERROR: spu_deuda_reprogramacion';
						//goto Error_BD;
						$db->ROLLBACK_TRANSACTION();
						return false;
					}
				}
			}
				
			//array manifiesto  => spu_manifiesto
			$id_manifiesto = $resultado["REPROGRAMACION"]["MANIFIESTO"]["ID_MANIFIESTO"];
			$fecha_manifiesto = $resultado["REPROGRAMACION"]["MANIFIESTO"]["FECHA_MANIFIESTO"];
			$fecha_recepcion_manifiesto = $resultado["REPROGRAMACION"]["MANIFIESTO"]["FECHA_RECEPCION_MANIFIESTO"];
			$estado_manifiesto = $resultado["REPROGRAMACION"]["MANIFIESTO"]["ESTADO_MANIFIESTO"];
				
			$id_manifiesto		= $this->get_value_data($id_manifiesto);
			$fecha_manifiesto	= $this->get_value_data($fecha_manifiesto);
			$fecha_recepcion_manifiesto	= $this->get_value_data($fecha_recepcion_manifiesto);
			$estado_manifiesto	= $this->get_value_data($estado_manifiesto);
				
			$id_manifiesto		= ($id_manifiesto=='') ? "NULL" : "'$id_manifiesto'";
			$fecha_manifiesto	= ($fecha_manifiesto=='') ? "NULL" : base::str2date($fecha_manifiesto);
			$fecha_recepcion_manifiesto	= ($fecha_recepcion_manifiesto=='') ? "NULL" : base::str2date($fecha_recepcion_manifiesto);
			$estado_manifiesto	= ($estado_manifiesto=='') ? "NULL" : "$estado_manifiesto";
			//$db->COMMIT_TRANSACTION();

			$sp = "spu_manifiesto";
			$param	= "'INSERT'
			,NULL
			,$cod_reprogramacion
			,$id_manifiesto
			,$fecha_manifiesto
			,$fecha_recepcion_manifiesto
			,$estado_manifiesto";
				
			if(!$db->EXECUTE_SP($sp, $param)) {
				$this->msg_error = 'ERROR: spu_manifiesto';
				//goto Error_BD;
				$db->ROLLBACK_TRANSACTION();
				return false;
			}
			//array pago_inicial  => spu_cuota_pago_inicial
			/*pago_inicial*/
			
			$count_cuota = $resultado["REPROGRAMACION"]["PAGO_INICIAL"]["NUMERO_CUOTAS"];
			
			for($i=0 ; $i < $count_cuota ; $i++){
				if($count_cuota == 1){
					$nro_cuota = $resultado["REPROGRAMACION"]["PAGO_INICIAL"]["CUOTAS"]["CUOTA"]["NRO"];
					$fecha_vencimiento = $resultado["REPROGRAMACION"]["PAGO_INICIAL"]["CUOTAS"]["CUOTA"]["FECHA_VCTO"];
					$monto_cuota = $resultado["REPROGRAMACION"]["PAGO_INICIAL"]["CUOTAS"]["CUOTA"]["MONTO"];
				}else{
					$nro_cuota = $resultado["REPROGRAMACION"]["PAGO_INICIAL"]["CUOTAS"]["CUOTA"][$i]["NRO"];
					$fecha_vencimiento = $resultado["REPROGRAMACION"]["PAGO_INICIAL"]["CUOTAS"]["CUOTA"][$i]["FECHA_VCTO"];
					$monto_cuota = $resultado["REPROGRAMACION"]["PAGO_INICIAL"]["CUOTAS"]["CUOTA"][$i]["MONTO"];
				}

				$monto_cuota = str_replace(',', '.', $monto_cuota);
				
				$fecha_vencimiento = base::str2date(str_replace('-','/',$fecha_vencimiento));
				

				$sp = "spu_cuota_pago_inicial";
				$param	= "'INSERT'
				,NULL
				,$cod_reprogramacion
				,$nro_cuota
				,$fecha_vencimiento
				,$monto_cuota";
				
				if(!$db->EXECUTE_SP($sp, $param)) {
					$this->msg_error = 'ERROR: spu_cuota_pago_inicial';
					//goto Error_BD;
					$db->ROLLBACK_TRANSACTION();
					return false;
				}
			}
				
			//array plan_pago  => spu_cuota_anual
			$count_pagare = $this->get_cant_tags('PAGARE');
			for($h=0 ; $h < $count_pagare; $h++){

				/*por_borrar*/
				if($count_pagare == 1){
					$count = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["NUMERO_CUOTAS"];
					
					$id_pagare = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["ID_PAGARE"];			//PAGARES
					$monto_pago_inicial = $resultado["REPROGRAMACION"]["PAGO_INICIAL"]["MONTO"];				//PAGO_INICIAL
					$nro_cuotas_pago_inicial = $resultado["REPROGRAMACION"]["PAGO_INICIAL"]["NUMERO_CUOTAS"];	//PAGO_INICIAL
					$fecha_pago_inicial = $resultado["REPROGRAMACION"]["PAGO_INICIAL"]["FECHA_PAGO"];			//PAGO_INICIAL
					$estado_pago_inicial = $resultado["REPROGRAMACION"]["PAGO_INICIAL"]["ESTADO_PAGO"];			//PAGO_INICIAL
					$cod_universidad = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["COD_UNIVERSIDAD"];	//PAGARES
					$monto_pagare = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["MONTO"];					//PAGARES
					$saldo_capital = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["SALDO_CAPITAL"];		//PAGARES
					$intereses = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["INTERESES"];				//PAGARES
					$fecha_generacion_pagare = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["FECHA_GENERACION_PAGARE"];	//PAGARES
					$fecha_recepcion_pagare = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["FECHA_RECEPCION_PAGARE"];		//PAGARES
					$porc_condonacion_interes_penal = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PORCENTAJE_CONDONACION_INTERES_PENAL"];	//PAGARES
					$estado_pagare = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["ESTADO_PAGARE"];	//PAGARES
					$monto_reprogramado = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["MONTO_REPROGRAMADO"];		//PLAN_PAGO
					$nro_cuotas_reprogramado = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["NUMERO_CUOTAS"];		//PLAN_PAGO
						
				}else{
					$count = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["NUMERO_CUOTAS"];
					
					/*por_borrar*/

					$id_pagare = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["ID_PAGARE"];			//PAGARES
					$monto_pago_inicial = $resultado["REPROGRAMACION"]["PAGO_INICIAL"]["MONTO"];				//PAGO_INICIAL
					$nro_cuotas_pago_inicial = $resultado["REPROGRAMACION"]["PAGO_INICIAL"]["NUMERO_CUOTAS"];	//PAGO_INICIAL
					$fecha_pago_inicial = $resultado["REPROGRAMACION"]["PAGO_INICIAL"]["FECHA_PAGO"];			//PAGO_INICIAL
					$estado_pago_inicial = $resultado["REPROGRAMACION"]["PAGO_INICIAL"]["ESTADO_PAGO"];			//PAGO_INICIAL
					$cod_universidad = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["COD_UNIVERSIDAD"];	//PAGARES
					$monto_pagare = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["MONTO"];					//PAGARES
					$saldo_capital = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["SALDO_CAPITAL"];		//PAGARES
					$intereses = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["INTERESES"];				//PAGARES
					$fecha_generacion_pagare = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["FECHA_GENERACION_PAGARE"];	//PAGARES
					$fecha_recepcion_pagare = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["FECHA_RECEPCION_PAGARE"];		//PAGARES
					$porc_condonacion_interes_penal = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PORCENTAJE_CONDONACION_INTERES_PENAL"];	//PAGARES
					$estado_pagare = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["ESTADO_PAGARE"];	//PAGARES
					$monto_reprogramado = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["MONTO_REPROGRAMADO"];		//PLAN_PAGO
					$nro_cuotas_reprogramado = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["NUMERO_CUOTAS"];		//PLAN_PAGO
				}

				$monto_pago_inicial = str_replace(',', '.', $monto_pago_inicial);
				$monto_pagare = str_replace(',', '.', $monto_pagare);
				$saldo_capital = str_replace(',', '.', $saldo_capital);
				$intereses = str_replace(',', '.', $intereses);
				$porc_condonacion_interes_penal = str_replace(',', '.', $porc_condonacion_interes_penal);
				$monto_reprogramado = str_replace(',', '.', $monto_reprogramado);

				$id_pagare	= $this->get_value_data($id_pagare);
				$monto_pago_inicial	= $this->get_value_data($monto_pago_inicial);
				$nro_cuotas_pago_inicial	= $this->get_value_data($nro_cuotas_pago_inicial);
				$fecha_pago_inicial	= $this->get_value_data($fecha_pago_inicial);
				$estado_pago_inicial	= $this->get_value_data($estado_pago_inicial);
				$cod_universidad	= $this->get_value_data($cod_universidad);
				$monto_pagare	= $this->get_value_data($monto_pagare);
				$saldo_capital	= $this->get_value_data($saldo_capital);
				$intereses	= $this->get_value_data($intereses);
				$fecha_generacion_pagare	= $this->get_value_data($fecha_generacion_pagare);
				$fecha_recepcion_pagare	= $this->get_value_data($fecha_recepcion_pagare);
				$porc_condonacion_interes_penal	= $this->get_value_data($porc_condonacion_interes_penal);
				$estado_pagare	= $this->get_value_data($estado_pagare);
				$monto_reprogramado	= $this->get_value_data($monto_reprogramado);
				$nro_cuotas_reprogramado	= $this->get_value_data($nro_cuotas_reprogramado);

				$id_pagare		= ($id_pagare=='') ? "NULL" : "'$id_pagare'";
				$monto_pago_inicial	= ($monto_pago_inicial=='') ? "NULL" : "$monto_pago_inicial";
				$nro_cuotas_pago_inicial	= ($nro_cuotas_pago_inicial=='') ? "NULL" : "'$nro_cuotas_pago_inicial'";
				//$fecha_pago_inicial	= ($fecha_pago_inicial=='') ? "NULL" : base::str2date($fecha_pago_inicial);
				$cod_universidad	= ($cod_universidad=='') ? "NULL" : "$cod_universidad";
				$monto_pagare	= ($monto_pagare=='') ? "NULL" : "$monto_pagare";
				$saldo_capital	= ($saldo_capital=='') ? "NULL" : "$saldo_capital";
				$estado_pago_inicial	= ($estado_pago_inicial=='') ? "NULL" : "$estado_pago_inicial";
				$intereses	= ($intereses=='') ? "NULL" : "$intereses";
				//null =>   por el momento se soluciona
				$fecha_generacion_pagare	= ($fecha_generacion_pagare=='') ? base::str2date('25/06/2012') : base::str2date($fecha_generacion_pagare);
				//null =>   por el momento se soluciona
				$fecha_recepcion_pagare	= ($fecha_recepcion_pagare=='') ? base::str2date('25/06/2012') : base::str2date($fecha_recepcion_pagare);
				$porc_condonacion_interes_penal	= ($porc_condonacion_interes_penal=='') ? "NULL" : "$porc_condonacion_interes_penal";
				$estado_pagare	= ($estado_pagare=='') ? "NULL" : "$estado_pagare";
				$monto_reprogramado	= ($monto_reprogramado=='') ? "NULL" : "$monto_reprogramado";
				$nro_cuotas_reprogramado	= ($nro_cuotas_reprogramado=='') ? "NULL" : "$nro_cuotas_reprogramado";

				$sp = "spu_pagare_reprogramacion";
				$param	= "'INSERT'
				,NULL
				,$cod_reprogramacion
				,$id_pagare
				,$monto_pago_inicial
				,$nro_cuotas_pago_inicial
				,$estado_pago_inicial
				,$cod_universidad
				,$monto_pagare
				,$saldo_capital
				,$intereses
				,$fecha_generacion_pagare
				,$fecha_recepcion_pagare
				,$porc_condonacion_interes_penal
				,$estado_pagare
				,$monto_reprogramado
				,$nro_cuotas_reprogramado
				,'N'"; //RECIBIDO
				if(!$db->EXECUTE_SP($sp, $param)) {
					$this->msg_error = 'ERROR: spu_pagare_reprogramacion';
					//goto Error_BD;
					$db->ROLLBACK_TRANSACTION();
					return false;
				}

				$cod_pagare_reprogramacion = $db->GET_IDENTITY("pagare_reprogramacion");

				for($i=0 ; $i < $count; $i++){
					if($count_pagare == 1){
						if($count == 1){
							$monto_reprogramado = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"]["MONTO_REPROGRAMADO"];
							$numero_cuotas = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"]["NUMERO_CUOTAS"];
								
							$nro_cuota = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"]["NRO"];
							$fecha_vencimiento = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"]["FECHA_VCTO"];
							$monto_cuota_pp = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"]["MONTO"];
							$deuda_capital = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"]["DEUDA_CAPITAL"];
							$capital = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"]["CAPITAL"];
							$interes = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"]["INTERES"];
							$interes_penal_capitalizado = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"]["INTERES_PENAL_CAPITALIZADO"];
							$interes_penal = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"]["INTERES_PENAL"];
							$interes_normal = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"]["INTERES_NORMAL"];
							$valor_cuota_cuponera = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"]["VALOR_CUOTA_CUPONERA"];
							$dscto_int_penal_adeudado = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"]["DSCTO_INT_PENAL_ADEUDADO"];
							$saldo_int_penal_adeudado = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"]["SALDO_INT_PENAL_ADEUDADO"];
							$acumumacion_dscto_siguientes_periodo = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"]["ACUMULACION_DSCTO_SIGUIENTES_PERIODO"];
						}else{
							$monto_reprogramado = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"][$i]["MONTO_REPROGRAMADO"];
							$numero_cuotas = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"][$i]["NUMERO_CUOTAS"];
								
							$nro_cuota = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"][$i]["NRO"];
							$fecha_vencimiento = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"][$i]["FECHA_VCTO"];
							$monto_cuota_pp = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"][$i]["MONTO"];
							$deuda_capital = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"][$i]["DEUDA_CAPITAL"];
							$capital = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"][$i]["CAPITAL"];
							$interes = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"][$i]["INTERES"];
							$interes_penal_capitalizado = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"][$i]["INTERES_PENAL_CAPITALIZADO"];
							$interes_penal = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"][$i]["INTERES_PENAL"];
							$interes_normal = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"][$i]["INTERES_NORMAL"];
							$valor_cuota_cuponera = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"][$i]["VALOR_CUOTA_CUPONERA"];
							$dscto_int_penal_adeudado = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"][$i]["DSCTO_INT_PENAL_ADEUDADO"];
							$saldo_int_penal_adeudado = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"][$i]["SALDO_INT_PENAL_ADEUDADO"];
							$acumumacion_dscto_siguientes_periodo = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"]["PLAN_PAGO"]["CUOTA"][$i]["ACUMULACION_DSCTO_SIGUIENTES_PERIODO"];
						}
					}else{
						if($count == 1){
							$monto_reprogramado = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"]["MONTO_REPROGRAMADO"];
							$numero_cuotas = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"]["NUMERO_CUOTAS"];
							$nro_cuota = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"]["NRO"];
							$fecha_vencimiento = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"]["FECHA_VCTO"];
							$monto_cuota_pp = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"]["MONTO"];
							$deuda_capital = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"]["DEUDA_CAPITAL"];
							$capital = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"]["CAPITAL"];
							$interes = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"]["INTERES"];
							$interes_penal_capitalizado = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"]["INTERES_PENAL_CAPITALIZADO"];
							$interes_penal = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"]["INTERES_PENAL"];
							$interes_normal = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"]["INTERES_NORMAL"];
							$valor_cuota_cuponera = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"]["VALOR_CUOTA_CUPONERA"];
							$dscto_int_penal_adeudado = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"]["DSCTO_INT_PENAL_ADEUDADO"];
							$saldo_int_penal_adeudado = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"]["SALDO_INT_PENAL_ADEUDADO"];
							$acumumacion_dscto_siguientes_periodo = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"]["ACUMULACION_DSCTO_SIGUIENTES_PERIODO"];
						}else{
							$monto_reprogramado = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"][$i]["MONTO_REPROGRAMADO"];
							$numero_cuotas = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"][$i]["NUMERO_CUOTAS"];
							$nro_cuota = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"][$i]["NRO"];
							$fecha_vencimiento = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"][$i]["FECHA_VCTO"];
							$monto_cuota_pp = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"][$i]["MONTO"];
							$deuda_capital = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"][$i]["DEUDA_CAPITAL"];
							$capital = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"][$i]["CAPITAL"];
							$interes = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"][$i]["INTERES"];
							$interes_penal_capitalizado = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"][$i]["INTERES_PENAL_CAPITALIZADO"];
							$interes_penal = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"][$i]["INTERES_PENAL"];
							$interes_normal = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"][$i]["INTERES_NORMAL"];
							$valor_cuota_cuponera = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"][$i]["VALOR_CUOTA_CUPONERA"];
							$dscto_int_penal_adeudado = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"][$i]["DSCTO_INT_PENAL_ADEUDADO"];
							$saldo_int_penal_adeudado = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"][$i]["SALDO_INT_PENAL_ADEUDADO"];
							$acumumacion_dscto_siguientes_periodo = $resultado["REPROGRAMACION"]["PAGARES"]["PAGARE"][$h]["PLAN_PAGO"]["CUOTA"][$i]["ACUMULACION_DSCTO_SIGUIENTES_PERIODO"];
						}
					}
						
					$monto_cuota_pp = str_replace(',', '.', $monto_cuota_pp);
					$deuda_capital = str_replace(',', '.', $deuda_capital);
					$capital = str_replace(',', '.', $capital);
					$interes = str_replace(',', '.', $interes);
					$interes_penal_capitalizado = str_replace(',', '.', $interes_penal_capitalizado);
					$interes_penal = str_replace(',', '.', $interes_penal);
					$interes_normal = str_replace(',', '.', $interes_normal);
					$valor_cuota_cuponera = str_replace(',', '.', $valor_cuota_cuponera);
					$dscto_int_penal_adeudado = str_replace(',', '.', $dscto_int_penal_adeudado);
					$saldo_int_penal_adeudado = str_replace(',', '.', $saldo_int_penal_adeudado);
					$acumumacion_dscto_siguientes_periodo = str_replace(',', '.', $acumumacion_dscto_siguientes_periodo);
					$fecha_vencimiento	= $this->get_value_data($fecha_vencimiento);
						
					//se agrega una fecha por no acepta null fecha_vcto
					$fecha_vencimiento	= ($fecha_vencimiento=='') ? base::str2date('25/06/2012') : base::str2date($fecha_vencimiento);
						
					$sp = "spu_cuota_anual";
					$param	= "'INSERT'
					,NULL
					,$cod_pagare_reprogramacion
					,$nro_cuota
					,$fecha_vencimiento
					,$monto_cuota_pp
					,$deuda_capital
					,$capital
					,$interes
					,$interes_penal_capitalizado
					,$interes_penal
					,$interes_normal
					,$valor_cuota_cuponera
					,$dscto_int_penal_adeudado
					,$saldo_int_penal_adeudado
					,$acumumacion_dscto_siguientes_periodo";
					if(!$db->EXECUTE_SP($sp, $param)) {
						$this->msg_error = 'ERROR: spu_cuota_anual';
						//goto Error_BD;
						$db->ROLLBACK_TRANSACTION();
						return false;
					}
				}
			}
					
			//GUARDA DATOS A HELEN
			$sp = "sp_deuda_repro_crpi";
			$param	=  "'INSERT'
			,$cod_reprogramacion";
			
			if(!$db->EXECUTE_SP($sp, $param)) {
				$this->msg_error = 'ERROR: sp_deuda_repro_crpi';
				//goto Error_BD;
				$db->ROLLBACK_TRANSACTION();
				return false;
			}
			
			$db->COMMIT_TRANSACTION();
			return true;
		}else{
			return false;
		}
	}

	function GET_ERROR(){
		return $this->msg_error;
	}

	function get_cod_reprogramacion(){
		return $this->cod_reprogramacion;
	}
	/*****************************/
}
?>