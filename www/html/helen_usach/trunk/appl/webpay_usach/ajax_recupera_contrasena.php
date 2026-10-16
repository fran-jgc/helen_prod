<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/w_mail_class/class_PHPMailer.php");
require_once(dirname(__FILE__)."/../../appl.ini");

session::set('K_ROOT_URL', K_ROOT_URL);
session::set('K_ROOT_DIR', K_ROOT_DIR);

$rut = isset($_REQUEST['rut']) ? preg_replace('/[^0-9]/', '', trim($_REQUEST['rut'])) : '';
$email = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : '';
$fecha_nacim = isset($_REQUEST['fecha_nacimiento']) ? trim($_REQUEST['fecha_nacimiento']) : '';
$email_sql = str_replace("'", "''", $email);

if ($rut == '') {
	print urlencode('RUT_INVALIDO');
	return;
}

if ($email == '') {
	print urlencode('EMAIL_INVALIDO');
	return;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	print urlencode('EMAIL_INVALIDO');
	return;
}

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$sql_rut = "select RUT
			from DEUDOR
			where RUT = $rut";
$result_rut = $db->build_results($sql_rut);

if (count($result_rut) == 0) {
	print urlencode('RUT_INVALIDO');
	return;
}

$sql_f_nacim = "select nvl(to_char(FECHA_NACIM,'dd/mm/yyyy'),'01/01/1900') FECHA_NACIM
				from DEUDOR DE
				where DE.RUT = $rut";
$res_fecha = $db->build_results($sql_f_nacim);
$param_fecha = "null";

if ($res_fecha[0]['FECHA_NACIM'] != '01/01/1900') {
	if ($fecha_nacim == '') {
		print urlencode('FECHA_INVALIDA');
		return;
	}

	if (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $fecha_nacim)) {
		print urlencode('FECHA_INVALIDA');
		return;
	}

	$sql_fecha = "select FECHA_NACIM
				from DEUDOR DE
				where DE.RUT = $rut
				and DE.FECHA_NACIM = to_date('$fecha_nacim','dd/mm/yyyy')";
	$sql_res = $db->build_results($sql_fecha);

	if (count($sql_res) == 0) {
		print urlencode('FECHA_INVALIDA');
		return;
	}

	$param_fecha = "to_date('$fecha_nacim','dd/mm/yyyy')";
}

$sql_email = "select DE.RUT
				, DE.NOMBRE ||' '|| DE.APELLIDO_PATERNO NOMBRE
				, DIR.EMAIL
			from DEUDOR DE, DIRECCION DIR
			where DE.COD_DEUDOR = DIR.COD_DEUDOR
			and DE.RUT = $rut
			and upper(DIR.EMAIL) = upper('$email_sql')
			and ROWNUM = 1";
$result_email = $db->build_results($sql_email);

if (count($result_email) == 0) {
	print urlencode('EMAIL_INVALIDO');
	return;
}

$sp = "spu_recupera_clave";
$param = "'MAIL'
			,$rut
			,$param_fecha
			,'$email_sql'";

$db_mail = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$db_mail->BEGIN_TRANSACTION();
$exec_sp = $db_mail->EXECUTE_SP($sp, $param);
if (!$exec_sp) {
	$db_mail->ROLLBACK_TRANSACTION();
	error_log('recupera_contrasena sp error rut '.$rut.' email '.$email_sql);
	print urlencode('ERROR_ENVIA_CORREO');
	return;
}
$db_mail->COMMIT_TRANSACTION();

$sql_password = "select PASSWORD
				from DEUDOR
				where RUT = $rut";
$result_password = $db->build_results($sql_password);

if (count($result_password) == 0) {
	print urlencode('ERROR_ENVIA_CORREO');
	return;
}

$EMAIL = $result_email[0]['EMAIL'];
$PASSWORD = $result_password[0]['PASSWORD'];
$NOMBRE = $result_email[0]['NOMBRE'];
$RUT = $result_email[0]['RUT'];

$mail = new phpmailer();
$mail->PluginDir = '';
$mail->Mailer = "smtp";
$mail->SMTPAuth = true;
$mail->IsHTML(true);
$mail->Host = "smtp.gmail.com";
$mail->Port = 465;
$mail->SMTPSecure = 'ssl';
$mail->Username = "agfcu@usach.cl";
$mail->Password = "airulamfuybsqcpe";
$mail->Timeout = 30;
$mail->SMTPOptions = array(
	'ssl' => array(
		'verify_peer' => false,
		'verify_peer_name' => false,
		'allow_self_signed' => true,
	),
);

$mail->From = 'no-reply@helen.cl';
$mail->FromName = "Portal FSCU USACH";
$mail->Sender = 'agfcu@usach.cl';
$mail->setFrom('agfcu@usach.cl', 'Portal FSCU USACH');
$mail->AddReplyTo('agfcu@usach.cl', 'Portal FSCU USACH');
$mail->Subject = utf8_decode('Universidad Santiago de Chile - solicitud de recuperacion de contrasena');
$mail->AddAddress($EMAIL);

$body = utf8_decode("<html>
			<head>
			</head>
			<body>
			    <br>Estimado Ex-Alumno(a)<br>
			    $NOMBRE
			    <br><br>
			    Presente:<br><br>
			    Le informamos que se proceso exitosamente su requerimiento de recuperacion de contrasena.<br>
			    Favor ingrese nuevamente al portal: <span style='size:5px;color:blue'>https://www.fondocredito.usach.cl/</span><br><br>
			    Rut: $RUT<br>
			    Contrasena: $PASSWORD<br><br>
			    Una vez que ingrese al portal, se recomienda modificar la contrasena enviada por una mas segura,<br>
			    en la opcion: 'Cambio de clave'<br><br><br>
			    Atte. <br>
				Fondo Solidario de Credito Universitario de la Universidad de Santiago de Chile <br>
				<span style='size:5px;color:blue'>Este es un mensaje generado de forma automatica. Por favor, no responder.</span>
			</body>
		</html>");

$mail->Body = $body;

if (!$mail->Send()) {
	$smtp_error = '';
	if (isset($mail->smtp) && method_exists($mail->smtp, 'getError')) {
		$smtp_error = print_r($mail->smtp->getError(), true);
	}
	error_log('recupera_contrasena mail error rut '.$rut.' email '.$EMAIL.' error '.$mail->ErrorInfo.' smtp '.$smtp_error);
	print urlencode('ERROR_ENVIA_CORREO');
	return;
}

print urlencode('ENVIADO_CORRECTAMENTE');
return;
?>
