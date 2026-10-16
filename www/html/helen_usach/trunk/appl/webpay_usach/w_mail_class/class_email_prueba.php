<?php

ini_set('display_errors', 'On');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
require_once("class_PHPMailer.php");
//require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/clases/class_PHPMailer.php");

    
		
		function dj_mail($address,$body,$subject,$pdf){
		    //////////////// ENVIA EMAIL /////////////////////////////////////////////////
		    
		    $mail = new phpmailer();
		    $mail->PluginDir = '';
		    $mail->Mailer = "smtp";
		    $mail->SMTPAuth = true;
		    $mail->IsHTML(True);
		    
		    $mail->Host = "pro.turbo-smtp.com";
		    $mail->Port = 465;
		    $mail->SMTPSecure = 'ssl';
		    $mail->Username = "vmelo@integrasystem.cl";
		    $mail->Password = "KsVjKzdY";
		    $mail->Timeout=30;
		    
		    
		    $mail->From = 'no-reply@helen.cl';
		    $mail->FromName = "Portal FSCU USACH";
		    $mail->Subject = $subject;
		    $mail->AddAddress($address);
		    //$mail->AddAddress('evergara.lda@gmail.com');
		    //$mail->AddBCC('ibrito@integrasystem.cl');
		    //$mail->AddBCC('lfuenzalida@integrasystem.cl');
		    $mail->AddEmbeddedImage(dirname(__FILE__)."/logo_universidad.jpg", 'logo_ua');
		    $mail->Body = $body;
		    if($pdf)
		      $mail->AddAttachment(dirname(__FILE__)."/../../../tmp/declaracion.pdf","declaracion.pdf");
		  
		    $exito = $mail->Send();
		    
		    if(!$exito)
		    {
		        
		        $mail->ClearAddresses();
		        $mail->ClearAttachments();
		        $mail->Body = '';
		        
		        echo 'hubor error class mail <br>';
		        echo $mail->smtp->getError();
		        return false;
		    }
		    else{
		        $mail->ClearAddresses();
		        $mail->ClearAttachments();
		        $mail->Body = '';
		        
		        return true;
		    }
		}
		
	


dj_mail('evergara.lda@gmail.com','prueba','asunto prueba',false)

?>