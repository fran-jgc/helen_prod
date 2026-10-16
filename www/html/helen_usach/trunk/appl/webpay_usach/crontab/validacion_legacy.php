<?php
ini_set('display_errors', 'On');
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/clases/class_PHPMailer.php");
require_once ("../../../appl.ini");

session::set('K_ROOT_URL', K_ROOT_URL);
session::set('K_ROOT_DIR', K_ROOT_DIR);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$k_ruta_peticion = "https://webpay-server.usach.cl/api/default/get-transaction-detail";
$k_store_id = 12; //parametro fijo que identifica a los pagos institucionales
$cant_pagados = 0;
$sum_monto_total = 0;

$sp_bitacora = 'spw_bitacora_transaccion';
$param = "NULL,'CRONTAB_LEGACY','LLAMADO'";
$db->EXECUTE_SP($sp_bitacora,$param);

$sql = "SELECT COD_WP_TRANSACCION
				,TOKEN
				,MONTO
				,TO_CHAR(DR.RUT)||DR.DIG_VERIF RUT
                ,EXITO
		FROM WP_TRANSACCION WP, DEUDOR DR
        WHERE TIPO_PAGO_WEB = 'PAGO_SEGIC'
        AND WP.COD_DEUDOR = DR.COD_DEUDOR
        AND EXITO = 'S'
        AND TOKEN IS NOT NULL
        AND INGRESO_LEGACY IS NULL
        AND COD_WP_TRANSACCION >= 323685
        ORDER BY COD_WP_TRANSACCION ASC";

$result = $db->build_results($sql);
if(count($result)>0){
    for ($i = 0; $i < count($result); $i++) {
        $numTransaction = $result[$i]['TOKEN'];
        $COD_WP_TRANSACCION = $result[$i]['COD_WP_TRANSACCION'];
        $MONTO_BD = $result[$i]['MONTO'];
        $RUT = $result[$i]['RUT'];
        
        $sql = "SELECT f_get_matricula(D.COD_DEUDOR, 3000) COD_CARRERA
				,SB.MONTO_CAPITAL * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA) MONTO_CAPITAL
				,SB.MONTO_INTERES * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA) MONTO_INTERES
				,SB.MONTO_MULTA * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA) MONTO_MULTA
				,To_Char(WP.FECHA,'DD/MM/YYYY') FECHA_TRANSACCION
				,To_Char(WP.FECHA,'HH24:MI:SS') HORA_TRANSACCION
				FROM WP_TRANSACCION  WP
				,BOLETA B
				,WP_SUB_BOLETA WPS
				,SUB_BOLETA SB 
				,DEUDOR D
				,DEUDA DEU
				,TIPO_DEUDA TD
				WHERE WP.COD_WP_TRANSACCION =$COD_WP_TRANSACCION
				AND WPS.COD_WP_TRANSACCION = WP.COD_WP_TRANSACCION
				AND SB.COD_SUB_BOLETA = WPS.COD_SUB_BOLETA
				AND B.COD_BOLETA = SB.COD_BOLETA
				AND DEU.COD_DEUDA = B.COD_DEUDA
				AND D.COD_DEUDOR = DEU.COD_DEUDOR
				AND TD.COD_TIPO_DEUDA = DEU.COD_TIPO_DEUDA";
			$result_monto = $db->build_results($sql);
			
		
		$COD_CARRERA = $result_monto[0]['COD_CARRERA'];
		$MONTO_CAPITAL = $result_monto[0]['MONTO_CAPITAL'];
		$MONTO_INTERES = $result_monto[0]['MONTO_INTERES'];
		$MONTO_MULTA = $result_monto[0]['MONTO_MULTA'];
		$FECHA_TRANSACCION = $result_monto[0]['FECHA_TRANSACCION'];
		$HORA_TRANSACCION = $result_monto[0]['HORA_TRANSACCION'];
		
		$K_TIPO_BD = "oci";
		$K_SERVER = "(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=bdprdlegacy.usach.cl)(PORT = 1521)))(CONNECT_DATA=(SERVICE_NAME=FINA)))";
		$K_BD = "FINA";
		$K_USER ="CONS_INTERNET_EX";
		$K_PASS = "consulta2019";
		
		$db_segic = new database($K_TIPO_BD, $K_SERVER, $K_BD, $K_USER, $K_PASS);
		
		$cont_intentos = 0;
		$respuesta_segic = false;
		$error_segic = false;
		
		while(!$respuesta_segic){
			
			//se consulta a Legacy el pago
			$sql = "Paq_FondoCredito.pro_ConsPagoWebFC('$RUT','$numTransaction',{RESULT})";
			//echo $sql;
			//echo '<br>';
			$result_consulta = $db_segic->build_results2($sql);
			
			
			if(count($result_consulta)>0){
				//echo 'vienen registros de legacy';
				//echo '<br>';
				$NUM_TRANS_CONS = $result_consulta[0]['NUM_TRANS'];
				$FECPAGO_CONS = $result_consulta[0]['FEC_PAGO'];
				$RUT_CONS = $result_consulta[0]['RUT_ALUMNO'];
				$CARR_CONS = $result_consulta[0]['CARRERA'];
				$COD_COMERCIO_CONS = $result_consulta[0]['COD_COMERCIO'];
				$CAPITAL_CONS = $result_consulta[0]['CAPITAL'];
				$INTERES_CONS = $result_consulta[0]['INTERES'];
				$MULTA_CONS = $result_consulta[0]['MULTA'];
				$ESTADO_CONS = $result_consulta[0]['ESTADO'];
				
				$respuesta_segic = true;

				$sp_bitacora = 'spw_bitacora_transaccion';
				$param = "$COD_WP_TRANSACCION, 'CRONTAB_LEGACY', 'TRANSACCION YA EXISTE EN LEGACY= $numTransaction fecha_pago= $fecha_pago RUT= $RUT COD_CARRERA= $COD_CARRERA COD_COMERCIO= $COD_COMERCIO MONTO_CAPITAL= $MONTO_CAPITAL MONTO_INTERES= $MONTO_INTERES MONTO_MULTA= $MONTO_MULTA'";
				$db->EXECUTE_SP($sp_bitacora,$param);
				
			}
			else{
				//echo 'NO vienen, se debe registrar';
				//echo '<br>';

				$sql = "SELECT sum(SB.MONTO_CAPITAL * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA))  MONTO_CAPITAL
							,sum(SB.MONTO_INTERES * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA))  MONTO_INTERES
							,Sum(SB.MONTO_MULTA * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA))  MONTO_MULTA
						FROM WP_TRANSACCION  WP
						,BOLETA B
						,WP_SUB_BOLETA WPS
						,SUB_BOLETA SB 
						,DEUDOR D
						,DEUDA DEU
						,TIPO_DEUDA TD
						WHERE WP.COD_WP_TRANSACCION =$COD_WP_TRANSACCION
						AND WPS.COD_WP_TRANSACCION = WP.COD_WP_TRANSACCION
						AND SB.COD_SUB_BOLETA = WPS.COD_SUB_BOLETA
						AND B.COD_BOLETA = SB.COD_BOLETA
						AND DEU.COD_DEUDA = B.COD_DEUDA
						AND D.COD_DEUDOR = DEU.COD_DEUDOR
						AND TD.COD_TIPO_DEUDA = DEU.COD_TIPO_DEUDA";
					$result_monto = $db->build_results($sql);
					
				
				$MONTO_CAPITAL = $result_monto[0]['MONTO_CAPITAL'];
				$MONTO_INTERES = $result_monto[0]['MONTO_INTERES'];
				$MONTO_MULTA = $result_monto[0]['MONTO_MULTA'];
				
				

				$COD_COMERCIO = 597032905501;
				$fecha_pago = base::str2date($FECHA_TRANSACCION, $HORA_TRANSACCION);

				if(($numTransaction == '')||($fecha_pago == '')||($RUT == '')||($COD_CARRERA == '')||($COD_COMERCIO == '')||($MONTO_CAPITAL == '')||($MONTO_INTERES == '')||($MONTO_MULTA == '')){
					echo 'ERROR VARIABLES';
					$sp_bitacora = 'spw_bitacora_transaccion';
					$param = "$COD_WP_TRANSACCION, 'CRONTAB_LEGACY', 'ERROR VARIABLES SEGIC numTransaction= $numTransaction fecha_pago= $fecha_pago RUT= $RUT COD_CARRERA= $COD_CARRERA COD_COMERCIO= $COD_COMERCIO MONTO_CAPITAL= $MONTO_CAPITAL MONTO_INTERES= $MONTO_INTERES MONTO_MULTA= $MONTO_MULTA'";
					$db->EXECUTE_SP($sp_bitacora,$param);
				}
				else{

					$sql = "Paq_FondoCredito.Pro_RegPagoWebFC($numTransaction, $fecha_pago, '$RUT',$COD_CARRERA,$COD_COMERCIO,$MONTO_CAPITAL,$MONTO_INTERES,$MONTO_MULTA,{RESULT})";
					
					$result_registro = $db_segic->build_results2($sql);
					$respuesta_registro = $result_registro[0][0];
					
					//print_r($result_registro);
					if($respuesta_registro == 'OK'){
						$sp_bitacora = 'spw_bitacora_transaccion';
						$param = "$COD_WP_TRANSACCION, 'Registro insertado en Legacy'";
						$db->EXECUTE_SP($sp_bitacora,$param);
						$respuesta_segic = true; 
						
						$sp_aux = 'spw_wp_aux';
						$param = "'ACTUALIZA_LEGACY',$COD_WP_TRANSACCION";
						$db->EXECUTE_SP($sp_aux,$param);
					}
					else{
						echo 'NO Registro';
					}
				}
			}
			
			$cont_intentos++;
			
			if(($cont_intentos > 3)&&(!$respuesta_segic)){
				//no se pudo registrar el pago despues de 3 intentos, mandar correo, salir del buckle.
				//echo 'No se pudo obtener respuesta de legacy, informar a SEGIC';
				//echo '<br>';
				$error_segic = true;
				$respuesta_segic = true;
			}
		}
		
		/*
		if($error_segic == true){

			$sp_bitacora = 'spw_bitacora_transaccion';
			$param = "$COD_WP_TRANSACCION, 'CRONTAB_LEGACY', 'ERROR COMUNCIONACION SEGIC $numTransaction $fecha_pago $RUT $COD_CARRERA $COD_COMERCIO $MONTO_CAPITAL $MONTO_INTERES $MONTO_MULTA'";
			$db->EXECUTE_SP($sp_bitacora,$param);

			//correo
			$mail_from = 'validacion.segic@integrasystem.cl';
			$mail_fromname = 'validacion segic';
			$mail_to = "francisco.gatica.c@usach.cl";
			$mail_cc = '';
			$mail_bcc = '';
			$mail_subject = 'Validacion Pagos Webpay';
			$mail_body = 'Cuerpo del Correo';
				
			//ENVIO MAIL
			// smpt
			$email                = new phpmailer();
			//$email->PluginDir = dirname(__FILE__)."/";
			// $mail->PluginDir = dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/clases/";
			$email->PluginDir = dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/clases/";
			$email->Mailer        = "smtp";
			$email->SMTPAuth        = true;
			$email->IsHTML(True);
			//pagina oficial del smtp: http://www.serversmtp.com/es
			
			$email->Host            = "pro.turbo-smtp.com";
			$email->Port            = 465;
			$email->SMTPSecure      = "ssl";
			$email->Username        = "vmelo@integrasystem.cl";
			$email->Password        = "KsVjKzdY";
			$email->Timeout         = 30;
			$email->From            = $mail_from;
			$email->FromName        = $mail_fromname;
			$email->Subject = $mail_subject;
			$email->ClearAddresses();
			$email->AddAddress($mail_to);
			$email->AddBCC("evergara.lda@gmail.com");
			
			
			$email->Body = "Pago Webpay: $COD_WP_TRANSACCION, Orden compra segic: $numTransaction";
			$res = $email->Send(); 	

		}
		*/
    }
}
else{
	echo 'nada encontrado';
}

$sp_bitacora = 'spw_bitacora_transaccion';
$param = "NULL,'CRONTAB_LEGACY','FINALIZADO'";
$db->EXECUTE_SP($sp_bitacora,$param);

?>