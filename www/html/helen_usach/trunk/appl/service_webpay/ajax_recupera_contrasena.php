<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/clases/class_PHPMailer.php");

$rut = $_REQUEST['rut'];
$email = $_REQUEST['email'];
$fecha_nacim=$_REQUEST['fecha_nacim'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql_rut = "select RUT 
			from DEUDOR 
			where RUT = $rut";
$result_rut = $db->build_results($sql_rut);

if (count($result_rut) == 0) {
	print urlencode('RUT_INVALIDO');
	return;
}
	$sql_f_nacim = "select nvl(to_char(FECHA_NACIM,'dd/mm/yyyy'),'01/01/1900')FECHA_NACIM
					from DEUDOR DE
					where DE.RUT = $rut";
	$res_fecha = $db->build_results($sql_f_nacim);
		//deudor con fecha_nacim
		if ($res_fecha[0]['FECHA_NACIM'] != '01/01/1900'){			
			
			$sql_fecha = "select FECHA_NACIM
			from DEUDOR DE
			where DE.RUT = $rut
			and DE.FECHA_NACIM=to_date('$fecha_nacim','dd/mm/yyyy')";
			$sql_res = $db->build_results($sql_fecha);
						
			if (count($sql_res) == 0){
				print urlencode('FECHA_INVALIDA');
				return;
			}else{
				
	$sql_email = "select DE.RUT
				, DE.NOMBRE ||' '|| DE.APELLIDO_PATERNO NOMBRE 
				, DIR.EMAIL
				, DE.PASSWORD
		from DEUDOR DE, DIRECCION DIR
		where DE.COD_DEUDOR = DIR.COD_DEUDOR
		AND DE.RUT = $rut
		AND DIR.EMAIL='$email'
		AND ROWNUM=1
		order by dir.cod_direccion desc";
	$result_email = $db->build_results($sql_email);

	if (count($result_email) == 0){
		$sp = "spu_recupera_clave";
		$param = "'MAIL'
					,$rut
					,to_date('$fecha_nacim','dd/mm/yyyy')
					,'$email'";

		$db_mail = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db_mail->BEGIN_TRANSACTION();
		$db_mail->EXECUTE_SP($sp, $param); 
		$db_mail->COMMIT_TRANSACTION();
		
		$sql_email = "select DE.RUT
				, DE.NOMBRE ||' '|| DE.APELLIDO_PATERNO NOMBRE 
				, DIR.EMAIL
				, DE.PASSWORD
		from DEUDOR DE, DIRECCION DIR
		where DE.COD_DEUDOR = DIR.COD_DEUDOR
		AND DE.RUT = $rut
		and dir.email='$email'
		AND ROWNUM=1";
		$result_email = $db->build_results($sql_email);	
	}
	if (count($result_email) == 0)
		$EMAIL = $email;
	else
		$EMAIL = $result_email[0]['EMAIL'];	
	
		$PASSWORD = $result_email[0]['PASSWORD']; //se supone que todos los deudores siempre tienen password
		$NOMBRE = $result_email[0]['NOMBRE'];
		$RUT = $result_email[0]['RUT'];
		
		
		$mail = new phpmailer();
		$mail->PluginDir = dirname(__FILE__)."/../../../../commonlib/trunk/php/clases/";
		$mail->Mailer = "smtp";
		$mail->SMTPAuth = true;
		$mail->IsHTML(True);
		
		//Si necesitamos una conexion con SSL, por ejemplo para enviar un mail desde PHP con gmail:
		$mail->Mailer = "smtp";
	    $mail->Host = "ssl://smtp.gmail.com";
	    $mail->Port = 465;
	    $mail->SMTPAuth = true;
	    $mail->Username = "uteminfo.prueba@gmail.com";
		$mail->Password = "integra1234";
	    $mail->Timeout=30;
		$mail->From = 'fscu@ucm.cl';
		$mail->FromName = 'clave FSCU';
	/*
		//$mail->Host = "mail.integrasystem.cl";
		$mail->Host = "190.98.219.16";
		$mail->Username = "evergara@integrasystem.cl";
		$mail->Password = "!.E*VG14#*";		
		$mail->From = 'fscu@ucm.cl';
		$mail->FromName = 'fscu@ucm.cl';
	*/
		$mail->Subject = 'Universidad Católica del Maule - solicitud de recuperación de contraseña';
					
		$mail_para = "$EMAIL";
		$mail->AddAddress($mail_para);
			
		$body = "<html>
					<head>
					</head>
					<body>
					    <br>Estimado Deudor(a) del Fondo Solidario de Crédito Universitario <br> 
					    $NOMBRE 
					    <br><br>
					    Presente:<br><br>
					    Le informamos que se procesó exitosamente su requerimiento de recuperación de contraseña.<br>
					    Favor ingrese nuevamente al portal: <span style='size:5px;color:blue'>http://fscu-helen.ucm.cl/portal_helen/</span><br><br>
					    Rut: $RUT<br>
					    Contraseña: $PASSWORD<br><br>
					    Una vez que ingrese al portal, se recomienda modificar la contraseña enviada por una más segura,<br>
					    en la opción: 'Cambio de clave'<br><br><br>
					    Atte. <br>
						Fondo Solidario de Crédito Universitario de la Universidad Católica del Maule <br>
						<span style='size:5px;color:blue'>Este es un mensaje generado de forma automático. Por favor, no responder.</span>
					</body>
				</html>";
			
		$mail->Body = $body;
			
		$exito = $mail->Send();
			
		if(!$exito){
			print urlencode('ERROR_ENVIA_CORREO');
			return;
		}
		else{  
			print urlencode('ENVIADO_CORRECTAMENTE');
			return;
		}
	}
}else{			
	$sp = "spu_recupera_clave";
		$param = "'FECHA_NACIM'
					,$rut
					,to_date('$fecha_nacim','dd/mm/yyyy')
					,'$email'";

		$db_mail = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db_mail->BEGIN_TRANSACTION();
		$db_mail->EXECUTE_SP($sp, $param); 
		$db_mail->COMMIT_TRANSACTION();

	$sql_email = "select DE.RUT
				, DE.NOMBRE ||' '|| DE.APELLIDO_PATERNO NOMBRE 
				, DIR.EMAIL
				, DE.PASSWORD
		from DEUDOR DE, DIRECCION DIR
		where DE.COD_DEUDOR = DIR.COD_DEUDOR
		AND DE.RUT = $rut
		and dir.email='$email'
		AND ROWNUM=1";
	$result_email = $db->build_results($sql_email);

	if (count($result_email) == 0){
		$sp = "spu_recupera_clave";
		$param = "'MAIL'
					,$rut
					,to_date('$fecha_nacim','dd/mm/yyyy')
					,'$email'";

		$db_mail = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db_mail->BEGIN_TRANSACTION();
		$db_mail->EXECUTE_SP($sp, $param); 
		$db_mail->COMMIT_TRANSACTION();
		
		$sql_email = "select DE.RUT
				, DE.NOMBRE ||' '|| DE.APELLIDO_PATERNO NOMBRE 
				, DIR.EMAIL
				, DE.PASSWORD
		from DEUDOR DE, DIRECCION DIR
		where DE.COD_DEUDOR = DIR.COD_DEUDOR
		AND DE.RUT = $rut
		and dir.email='$email'
		AND ROWNUM=1";
		$result_email = $db->build_results($sql_email);	
	}
		if (count($result_email) == 0)
			$EMAIL = $email;
		else
			$EMAIL = $result_email[0]['EMAIL'];	
		
		$PASSWORD = $result_email[0]['PASSWORD']; //se supone que todos los deudores siempre tienen password
		$NOMBRE = $result_email[0]['NOMBRE'];
		$RUT = $result_email[0]['RUT'];
		
		
		$mail = new phpmailer();
		$mail->PluginDir = dirname(__FILE__)."/../../../../commonlib/trunk/php/clases/";
		$mail->Mailer = "smtp";
		$mail->SMTPAuth = true;
		$mail->IsHTML(True);
		
		//Si necesitamos una conexion con SSL, por ejemplo para enviar un mail desde PHP con gmail:
		$mail->Mailer = "smtp";
	    $mail->Host = "ssl://smtp.gmail.com";
	    $mail->Port = 465;
	    $mail->SMTPAuth = true;
	    $mail->Username = "uteminfo.prueba@gmail.com";
		$mail->Password = "integra1234";
	    $mail->Timeout=30;
		$mail->From = 'fscu@ucm.cl';
		$mail->FromName = 'clave FSCU';
	/*		
		//$mail->Host = "mail.integrasystem.cl";
		$mail->Host = "190.98.219.16";
		$mail->Username = "evergara@integrasystem.cl";
		$mail->Password = "!.E*VG14#*";		
		$mail->From = 'fscu@ucm.cl';
		$mail->FromName = 'fscu@ucm.cl';
	*/	
		
		$mail->Subject = 'Universidad Católica del Maule - solicitud de recuperación de contraseña';					
		$mail_para = "$EMAIL";
		$mail->AddAddress($mail_para);			
		$body = "<html>
					<head>
					</head>
					<body>
					    <br>Estimado Deudor(a) del Fondo Solidario de Crédito Universitario <br> 
					    $NOMBRE 
					    <br><br>
					    Presente:<br><br>
					    Le informamos que se procesó exitosamente su requerimiento de recuperación de contraseña.<br>
					    Favor ingrese nuevamente al portal: <span style='size:5px;color:blue'>http://fscu-helen.ucm.cl/portal_helen/</span><br><br>
					    Rut: $RUT<br>
					    Contraseña: $PASSWORD<br><br>
					    Una vez que ingrese al portal, se recomienda modificar la contraseña enviada por una más segura,<br>
					    en la opción: 'Cambio de clave'<br><br><br>
					    Atte. <br>
						Fondo Solidario de Crédito Universitario de la Universidad Católica del Maule <br>
						<span style='size:5px;color:blue'>Este es un mensaje generado de forma automático. Por favor, no responder.</span>
					</body>
				</html>";
			
		$mail->Body = $body;
			
		$exito = $mail->Send();
			
		if(!$exito){
			print urlencode('ERROR_ENVIA_CORREO');
			return;
		}
		else{
			print urlencode('ENVIADO_CORRECTAMENTE');
			return;
		}
}	
?>