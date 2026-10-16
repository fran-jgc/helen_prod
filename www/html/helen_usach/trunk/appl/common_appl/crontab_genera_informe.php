<?php
ini_set('display_errors', 'On');
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once ("../../appl.ini");


session::set('K_ROOT_DIR', K_ROOT_DIR);
session::set('K_ROOT_URL', K_ROOT_URL);
session::set('K_CLIENTE', K_CLIENTE);

class genera_informe {
    CONST K_PROC_GET_MOROSOS 	    = 2;
    CONST K_NOMINA_TGR  	        = 1;
    CONST K_ESTADO_INICIADO	 	    = 1;
    CONST K_ESTADO_PROCESANDO	 	= 2;
	CONST K_ESTADO_TERMINADO	 	= 3;
	CONST K_ESTADO_INFORMADO	 	= 4;
	CONST K_ESTADO_ERROR	 		= 5;

    function __construct() {
        echo "Iniciando proceso de generacion de informes...<br>";

        $sp_bitacora = 'spw_bitacora_transaccion';
        $param = "NULL,'CRONTAB_INFORME','LLAMADO'";
        $db->EXECUTE_SP($sp_bitacora,$param);
        
        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        $estado=self::K_ESTADO_INICIADO;
        $sql ="SELECT  PARAM_GENERA_INFORME
                    ,COD_TIPO_INFORME
                    ,COD_GENERA_INFORME
                    ,MAIL MAIL_USUARIO
                    ,GI.COD_USUARIO_GENERA_INFORME
                FROM GENERA_INFORME GI , USUARIO U
                WHERE COD_ESTADO_INFORME = $estado
                AND GI.COD_USUARIO_GENERA_INFORME=U.COD_USUARIO "; // INICIO

        $result = $db->build_results($sql);
        $count = count($result);
        //echo "<br> Cantidad de informes a generar: ".$count."<br>";
        if ($count > 0){
            foreach ($result as $row) {
                $parametros = json_decode($row['PARAM_GENERA_INFORME'], true);
                $cod_tipo_informe = $row['COD_TIPO_INFORME'];
                $cod_genera_informe = $row['COD_GENERA_INFORME'];
                $mail_usuario= $row['MAIL_USUARIO'];
                $cod_usuario= $row['COD_USUARIO_GENERA_INFORME'];

                $operacion = 'UPDATE_PROCESANDO';
                $cod_estado_informe = self::K_ESTADO_PROCESANDO;


                $sp = "spi_genera_informe_crontab";
                $param = "'$operacion',
                                $cod_genera_informe,
                                NULL ,
                                NULL,
                                NULL ,
                                $cod_estado_informe,
                                NULL,
                                NULL";

               if ($db->EXECUTE_SP($sp, $param)){
                    echo "Generando informe: ".$cod_genera_informe."<br>";
                    $this->genera_informe($cod_genera_informe, $cod_tipo_informe, $parametros, $db, $mail_usuario,$cod_usuario);
               } else {
                   echo "Error al actualizar el estado a procesando para el informe: ".$cod_genera_informe."<br>";
               }
            }
        }else{
            echo "No hay informes para generar.<br>";
        }
    }

    function genera_informe($cod_genera_informe, $cod_tipo_informe, $parametros, $db, $mail_usuario,$cod_usuario){ 
        echo "Generando informe tipo: ".$cod_tipo_informe."<br>";
        $sp = "spi_genera_informe_crontab";
        $ruta_dcto = 'null';
        $obs ='null';

        if (self::K_PROC_GET_MOROSOS == $cod_tipo_informe){
            echo "Generando Informe de Morosos...<br>";
            require_once ("../proc_get_morosos/wo_maestro_moroso.php");
            $w = new wo_maestro_moroso();  

            $fecha = $parametros['fecha'];
            $fecha=str_replace("-","/",$fecha);

            $ruta_dcto = $w->print_txt($fecha);
            $informe= 'Informe de Morosos';
            $modulo= 'Publicación Morosos';
        }

        if (self::K_NOMINA_TGR == $cod_tipo_informe){
             echo "Generando  Nomina TGR...<br>";
             require_once ("../proc_get_nomina_tgr/wo_nomina_tgr.php");
            $w =  new wo_nomina_tgr();
            $ruta_dcto = $w->print_txt($cod_usuario);
            $informe= 'Informe Nomina TGR';
            $modulo= 'Nomina TGR';
        }

echo "Ruta Dcto: ".$ruta_dcto."<br>";
        if (strpos($ruta_dcto, 'ERROR') !== false) {
            $operacion = 'UPDATE_ERROR';
            $cod_estado_informe = self::K_ESTADO_ERROR;
            $obs = "'$ruta_dcto'";
        } elseif (strpos($ruta_dcto, 'documentos') !== false) {
            $operacion = 'UPDATE_RUTA_ARCHIVO';
            $cod_estado_informe = self::K_ESTADO_TERMINADO;
            $ruta_dcto=K_ROOT_DIR.'appl/common_appl/'.$ruta_dcto;
            $ruta_dcto="'$ruta_dcto'";

            
        } else {
            $operacion = 'UPDATE_ERROR';
            $cod_estado_informe = self::K_ESTADO_ERROR;
            $obs = "'$ruta_dcto'";
        }

        $param = "'$operacion',
                    $cod_genera_informe,
                    NULL ,
                    NULL,
                    NULL ,
                    $cod_estado_informe,
                    $ruta_dcto,
                    $obs";

        if ($db->EXECUTE_SP($sp, $param)){
            if ($operacion == 'UPDATE_RUTA_ARCHIVO'){ 
                echo "Enviando mail de informe generado...<br>";
                if ($this->mail($informe,$modulo,$mail_usuario)){ 
                    echo "Mail enviado correctamente.<br>";
                    $operacion = 'UPDATE_MAIL';
                    $cod_estado_informe = self::K_ESTADO_INFORMADO;
                    $param = "'$operacion',
                                $cod_genera_informe,
                                NULL ,
                                NULL,
                                NULL ,
                                $cod_estado_informe,
                                NULL,
                                NULL";
                    $db->EXECUTE_SP($sp, $param);

                }
            }
        }  
                
    }
    function mail ($informe,$modulo,$mail_usuario){
			require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/clases/class_PHPMailer.php");
			
		    $mail = new phpmailer();
		    $mail->PluginDir = '';
		    $mail->Mailer = "smtp";
		    $mail->SMTPAuth = true;
		    $mail->IsHTML(True);
		    
		    $mail->Host = "pro.turbo-smtp.com";
		    $mail->Port = 465;
		    $mail->SMTPSecure = 'ssl';
		    $mail->Username = "1e4763894d83aacd8a7f";
		    $mail->Password = "YHwdcuxGe9Z8y6hObfln";
		    $mail->Timeout=30;
		    
			$mail->From = 'no_reply@usach.cl';
			$mail->FromName = 'Generador de Informes Helen';
			$mail->Subject = 'Estado '.$informe;
			
			//$mail->AddAddress("edocastillo33@hotmail.es");
			$mail->AddAddress($mail_usuario);

			$body = "<html>
						<head>
						</head>
						<body>
						    <br>Estado Informe<br> 
						    <br><br>
						    El ".$informe." esta listo para ser descargado en el modulo ".$modulo." del sistema Helen.
                        
                        <br>
                        <br>
						</body>
					</html>";
			$mail->Body = $body;
				
			$exito = $mail->Send();
            return $exito;
		
    }
}
$w = new genera_informe();
?>