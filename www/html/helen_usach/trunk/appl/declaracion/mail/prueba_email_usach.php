<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
require_once("class_PHPMailer.php");
//require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/clases/class_PHPMailer.php");

    class Email {
		
		function __construct(){}
		
		function dj_mail(){
		    //////////////// ENVIA EMAIL /////////////////////////////////////////////////
		    
		    $mail = new phpmailer();
			$mail->SMTPDebug = 2;                // Nivel de detalle del debug
			$mail->Debugoutput = 'html';        // Formato de salida
			$mail->PluginDir = '';
			$mail->Mailer = "smtp";
			$mail->SMTPAuth = true;
			$mail->IsHTML(true);

			//$mail->Host = "pro.turbo-smtp.com";
		    //$mail->Port = 465;
		    //$mail->SMTPSecure = 'ssl';
		    //$mail->Username = "vmelo@integrasystem.cl";
		    //$mail->Password = "KsVjKzdY";
		    //$mail->Timeout=30;

			$mail->Host = "smtp.gmail.com";
            $mail->Port = 465;
            $mail->SMTPSecure = 'ssl';
            $mail->Username = "agfcu@usach.cl";
            $mail->Password = "airulamfuybsqcpe"; 
            $mail->Timeout= 30;
		    

		    $mail->From = 'no-reply@helen.cl'; // REMITENTE
		    //$mail->AddBCC('frojas@gmail.com');
		    $mail->FromName = "Prueba mail";
		    $mail->Subject = 'prueba';
		    $mail->AddAddress('evergara@hotmail.com');

		    
			$body = "<html>
						<head>
						</head>
						<body>
							<br>Prueba envio mail <br> 
						</body>
					</html>";
			$mail->Body = $body;

		    $exito = $mail->Send();
		    
		    if(!$exito)
		    {
		        
		        $mail->ClearAddresses();
		        $mail->ClearAttachments();
		        $mail->Body = 'prueba envio error';
		        $error = 'Error al enviar: ' . $mail->ErrorInfo;
		        echo $error;
		    }
		    else{
		        $mail->ClearAddresses();
		        $mail->ClearAttachments();
		        $mail->Body = 'prueba envio  ok';
		        
		        echo ' envio ok ';
		    }
		}
		
	}
	$mail = new Email();
	$mail->dj_mail();
?>