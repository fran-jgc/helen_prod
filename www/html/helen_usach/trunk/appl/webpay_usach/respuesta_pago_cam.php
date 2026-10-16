<?php
ini_set('display_errors', 'Off');
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/clases/class_PHPMailer.php");
require_once ("../../appl.ini");
require_once ("funciones.php");

$id_helen = $_REQUEST['id_helen']; //representa el identity de la taba WP_TRANSACCION
$monto = $_REQUEST['monto'];
$voucher = $_REQUEST['voucher'];
$num_tarjeta = $_REQUEST['num_tarjeta'];
$cod_autorizacion = $_REQUEST['cod_autorizacion'];
$num_cuota = $_REQUEST['num_cuota'];
$estado = $_REQUEST['estado'];
$estado_transaccion = $_REQUEST['estado_transaccion'];

$fase = 'RESPUESTA PAGO CAM';


if($estado_transaccion == 1){
	$display_aprobado = "none";
	$display_fracasado = "";
	$MENSAJE_TRANSACCION = "Su transacci&oacute;n no se ha podido efectuar con &eacute;xito, si el problema<br>persiste, comun&iacute;quese con su banco emisor";
}
else if($estado_transaccion == 0){
	$estado = $result['estado']; 
	$display_aprobado = "";
	$display_fracasado = "none";
	
	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	
	$fecha_autorizacion = $db->current_date();
	
	$nombre_archivo = "logs.txt";
	
	$sp = 'sp_wp_pago_transaccion_ws';
	$param = "'INSERT',$id_helen,'null','$num_tarjeta',null,'$fecha_autorizacion',null,null,null,'$cod_autorizacion',null,'$estado_transaccion',$monto,null,$num_cuota,'CREDITO INSTITUCIONAL','$voucher'";
	
	if($archivo = fopen($nombre_archivo, "a"))
	{
		fwrite($archivo, $param. "\n");
		fclose($archivo);
	}
		
	if(!$db->EXECUTE_SP($sp,$param)){ 
		$sp_bitacora = 'spw_bitacora_transaccion';
		$param = "$id_helen,'RESPUESTA PAGO CAM','Error sp_wp_pago_transaccion_ws' $param";
		$db->EXECUTE_SP($sp_bitacora,$param);
	}
	
	$sql = "SELECT COUNT(*) CANT
		FROM WP_TRANSACCION
		WHERE COD_WP_TRANSACCION =$id_helen
		AND EXITO = 'S'";
	$result = $db->build_results($sql);
	$cant = $result[0]['CANT'];
	
	// validar monto
	$sql_monto = "SELECT sum(B.MONTO_PESOS) MONTO_PESOS
		FROM WP_TRANSACCION  WP
		,BOLETA B
		,WP_SUB_BOLETA WPS
		,SUB_BOLETA SB 
        ,PLAN_PAGO PP
        ,DEUDOR D
        ,DEUDA DEU
        ,TIPO_DEUDA TD
        WHERE WP.COD_WP_TRANSACCION = $id_helen
		AND WPS.COD_WP_TRANSACCION = WP.COD_WP_TRANSACCION
		AND SB.COD_SUB_BOLETA = WPS.COD_SUB_BOLETA
		AND B.COD_BOLETA = SB.COD_BOLETA
        AND PP.COD_PLAN_PAGO = B.COD_PLAN_PAGO
        AND DEU.COD_DEUDA = B.COD_DEUDA
        AND D.COD_DEUDOR = DEU.COD_DEUDOR
        AND TD.COD_TIPO_DEUDA = DEU.COD_TIPO_DEUDA";
	$result_monto_2 = $db->build_results($sql_monto);
	$monto_guardado = $result_monto_2[0]['MONTO_PESOS'];

	$sql = "SELECT B.MONTO_PESOS
        ,PP.NRO_CUOTA
		,TO_CHAR(D.RUT)||D.DIG_VERIF RUT
        ,D.NOMBRE ||' '|| D.APELLIDO_PATERNO ||' '|| D.APELLIDO_MATERNO NOM_DEUDOR
        ,DEU.COD_DEUDA
		,f_get_matricula(D.COD_DEUDOR, 3000) COD_CARRERA
		,SB.MONTO_CAPITAL * f_mon_get_valor_moneda(TD.COD_MONEDA) MONTO_CAPITAL
		,SB.MONTO_INTERES * f_mon_get_valor_moneda(TD.COD_MONEDA) MONTO_INTERES
		,SB.MONTO_MULTA * f_mon_get_valor_moneda(TD.COD_MONEDA) MONTO_MULTA
		FROM WP_TRANSACCION  WP
		,BOLETA B
		,WP_SUB_BOLETA WPS
		,SUB_BOLETA SB 
        ,PLAN_PAGO PP
        ,DEUDOR D
        ,DEUDA DEU
        ,TIPO_DEUDA TD
		WHERE WP.COD_WP_TRANSACCION = $id_helen
		AND WPS.COD_WP_TRANSACCION = WP.COD_WP_TRANSACCION
		AND SB.COD_SUB_BOLETA = WPS.COD_SUB_BOLETA
		AND B.COD_BOLETA = SB.COD_BOLETA
        AND PP.COD_PLAN_PAGO = B.COD_PLAN_PAGO
        AND DEU.COD_DEUDA = B.COD_DEUDA
        AND D.COD_DEUDOR = DEU.COD_DEUDOR
        AND TD.COD_TIPO_DEUDA = DEU.COD_TIPO_DEUDA";
	$result_monto = $db->build_results($sql);
	
	
	$NRO_CUOTA = $result_monto[0]['NRO_CUOTA'];
	$COD_DEUDA = $result_monto[0]['COD_DEUDA'];
	$RUT = $result_monto[0]['RUT'];
	$NOM_DEUDOR = $result_monto[0]['NOM_DEUDOR'];
	$COD_CARRERA = $result_monto[0]['COD_CARRERA'];
	$MONTO_CAPITAL = $result_monto[0]['MONTO_CAPITAL'];
	$MONTO_INTERES = $result_monto[0]['MONTO_INTERES'];
	$MONTO_MULTA = $result_monto[0]['MONTO_MULTA'];
	
	if($cant > 0){//COMPROBAR QUE NO ESTE DUPLICADA
		$sp_bitacora = 'spw_bitacora_transaccion';
		$param = "$id_helen,'$fase','DUPLICADA'";
		$db->EXECUTE_SP($sp_bitacora,$param);
		header ('Location:'."error_portal.php?error=8");
	}else if($monto_guardado != $monto){ 
		$sp_bitacora = 'spw_bitacora_transaccion';
		$param = "$id_helen,'$fase','MONTOS DIFERENTES $monto_guardado VS $monto'";
		$db->EXECUTE_SP($sp_bitacora,$param);
		header ('Location:'."error_portal.php?error=9");
	}else{
		$sp = "spw_web_pay_pago";
		$param = "$id_helen, '1','S'";
		
		if(!$db->EXECUTE_SP($sp,$param)){ 
			$sp_bitacora = 'spw_bitacora_transaccion';
			$param = "$id_helen,'$fase','ERROR spw_web_pay_pago'";
			$db->EXECUTE_SP($sp_bitacora,$param);
			header ('Location:'."error_portal.php?error=10");
		}else{
			$sp_bitacora = 'spw_bitacora_transaccion';
			$param = "$id_helen,'$fase','TRANSICION EXITOSA'";
			$db->EXECUTE_SP($sp_bitacora,$param);
			
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
	        	$sql = "Paq_FondoCredito.pro_ConsPagoWebFC('$RUT','$voucher',{RESULT})";
	        	$result_consulta = $db_segic->build_results2($sql);
				
				
				if(count($result_consulta)>0){
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
					
				}
				else{

					$sql = "SELECT sum(SB.MONTO_CAPITAL * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA))  MONTO_CAPITAL
								,sum(SB.MONTO_INTERES * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA))  MONTO_INTERES
								,Sum(SB.MONTO_MULTA * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA))  MONTO_MULTA
							FROM WP_TRANSACCION  WP
							,BOLETA B
							,WP_SUB_BOLETA WPS
							,SUB_BOLETA SB 
							,PLAN_PAGO PP
							,DEUDOR D
							,DEUDA DEU
							,TIPO_DEUDA TD
							WHERE WP.COD_WP_TRANSACCION =$id_helen
							AND WPS.COD_WP_TRANSACCION = WP.COD_WP_TRANSACCION
							AND SB.COD_SUB_BOLETA = WPS.COD_SUB_BOLETA
							AND B.COD_BOLETA = SB.COD_BOLETA
							AND PP.COD_PLAN_PAGO = B.COD_PLAN_PAGO
							AND DEU.COD_DEUDA = B.COD_DEUDA
							AND D.COD_DEUDOR = DEU.COD_DEUDOR
							AND TD.COD_TIPO_DEUDA = DEU.COD_TIPO_DEUDA";
					$result_monto = $db->build_results($sql);
							
						
					$MONTO_CAPITAL = $result_monto[0]['MONTO_CAPITAL'];
					$MONTO_INTERES = $result_monto[0]['MONTO_INTERES'];
					$MONTO_MULTA = $result_monto[0]['MONTO_MULTA'];
					
					$COD_COMERCIO = 597032905501;
					$fecha_pago = base::str2date($db->current_date(), $db->current_time());
					$sql = "Paq_FondoCredito.Pro_RegPagoWebFC($voucher, $fecha_pago, '$RUT',$COD_CARRERA,$COD_COMERCIO,$MONTO_CAPITAL,$MONTO_INTERES,$MONTO_MULTA,{RESULT})";
					
					$result_registro = $db_segic->build_results2($sql);
					$respuesta_registro = $result_registro[0][0];
					
					if($respuesta_registro == 'OK'){
						$respuesta_segic = true; 
						
						$sp_aux = 'spw_wp_aux';
						$param = "'ACTUALIZA_LEGACY',$id_helen";
						$db->EXECUTE_SP($sp_aux,$param);
					}
				}
				
				$cont_intentos++;
				
				if(($cont_intentos > 3)&&(!$respuesta_segic)){
					//no se pudo registrar el pago despues de 3 intentos, mandar correo, salir del buckle.
					$error_segic = true;
					$respuesta_segic = true;
				}
			}
			
			//if($error_segic == true){
			//	$api = new api_integra('https://i-service.prod.integrasystem.cl', 'helen_usach', 'I5i5u4FKn%!Z^X8Bl7Ku', '');
			//	$body = "Pago Webpay: $id_helen, Registro pago: $respuesta_registro, Orden compra cegic: $orden_compra_cegic";
			//	$r = $api->send_mail('Validacion Pagos Webpay - ABONO en PORTAL', "FSCU USACH <no-reply@usach.cl>", ["evergara@integrasystem.cl", "francisco.rojas.o@usach.cl", "jose.morey@segic.usach.cl"], $body);
			//	$status_code = http_response_code();

			if($error_segic == true){
				$sp_bitacora = 'spw_bitacora_transaccion';
				$param = "$COD_WP_TRANSACCION, 'CRONTAB', 'ERROR COMUNCIONACION SEGIC $voucher $RUT $COD_CARRERA $COD_COMERCIO $MONTO_CAPITAL $MONTO_INTERES $MONTO_MULTA'";
				$db->EXECUTE_SP($sp_bitacora,$param);
				
				//correo
				$mail_from = 'validacion.segic@integrasystem.cl';
				$mail_fromname = 'validacion segic';
				$mail_to = "francisco.rojas.o@usach.cl";
				$mail_cc = '';
				$mail_bcc = '';
				$mail_subject = 'Validacion Pagos Webpay - ABONO en PORTAL';
				$mail_body = 'Cuerpo del Correo';
					
				//ENVIO MAIL
				// smpt
	            $email                = new phpmailer();
	            //$email->PluginDir = dirname(__FILE__)."/";
	            $email->PluginDir = dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/clases/";
	            $email->Mailer        = "smtp";
	            $email->SMTPAuth        = true;
	            
	            $email->IsHTML(True);
	            //pagina oficial del smtp: http://www.serversmtp.com/es
	            

			    $email->Host            = "pro.turbo-smtp.com";
			    $email->Port            = 465;
			    $email->SMTPSecure      = "ssl";
	            $email->Username        = "1e4763894d83aacd8a7f";
	            $email->Password        = "YHwdcuxGe9Z8y6hObfln";
                $email->Timeout         = 30;

				$email->From            = $mail_from;
	            $email->FromName        = $mail_fromname;
	
	
	            $email->Subject = $mail_subject;
	
	            $email->ClearAddresses();
	            
	            $email->AddAddress($mail_to);
	            
	            $email->AddBCC("evergara@integrasystem.cl");
	            //$email->AddBCC("vmelo@integrasystem.cl");
	
	            $email->Body = "Pago Webpay: $id_helen, Registro pago: $respuesta_registro, Orden compra Segic: $orden_compra_cegic";
	            $res = $email->Send();
			}
		}
	}
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-QGNNW6PP2C"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-QGNNW6PP2C');
</script>

	<meta charset="utf-8">
	<link rel="icon" type="image/png" href="imagen/logo-usach.png" />

	<title>Universidad de Santiago de Chile</title>
	
	<meta name="viewport" content="width= device-width, user-scalable = no">
	<!--<link rel="stylesheet" href="css/bootstrap.min.css" >-->

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

	<link rel="stylesheet"  href="estilo.css">
	<link href="style_web_pay_usach.css" rel="stylesheet" type="text/css"></link>



</head>
	<header> 
	<div class="container">
		
		<p><br><img src="imagen/logo-usach.png" class="img-responsive" align="left" width="110" ><h1> Universidad de Santiago de Chile </h1>
		<h4> Administración General del Fondo Solidario</h4><br><br><br></p>

	</div>
	</header>


<body>



<div class="col-sm-3 sidenav" >
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			    <div class="navbar-header">
						       <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"  data-target="#myNavbar"  aria-expanded="false">
								        <span class="sr-only">Toggle navigation</span>
								        <span class="icon-bar"></span>
								        <span class="icon-bar"></span>
								        <span class="icon-bar"></span>                       
						       </button>
						     
			     </div>
			
			<div class="collapse navbar-collapse" id ="myNavbar">

				  <ul class="sidenav">
				  	  <li><a href="index.html" ><span class="glyphicon glyphicon-home"></span> Inicio</a></li>
					  <li><a href="inicio_sesion.php" ><span class="glyphicon glyphicon-user"></span> Inicio Sesión</a></li>
					  <li><a href="consulta_deuda.php" > <span class="glyphicon glyphicon-education"></span> Consulta Deuda</a></li>
					  <li><a href="actualiza_informacion.php"><span class="glyphicon glyphicon-education"></span> Actualizacion de Informacion</a></li>
					  <li><a href="cupon_pago.php" ><span class="glyphicon glyphicon-education"></span> Cupon de Pago</a></li>
					  <li><a href="ingresa_declaracion.php" ><span class="glyphicon glyphicon-education"></span> Declaracion Jurada</a></li>
					  <li><a class="active" href="web_pay.php" ><span class="glyphicon glyphicon-education"></span> Pago Internet</a></li>
					   <li><a href="#" data-toggle="collapse" data-target="#toggleDemo" data-parent="#sidenav01" class="collapsed">
         			  	<span class="glyphicon glyphicon-education"></span> Información <span class="caret pull-right"></span></a>
				          <div class="collapse" id="toggleDemo" style="height: 0px;">
					            <ul class="nav nav-list">
						            <li><a href="informacion.html" id="informacion">Información</a></li>
									<li><a  href="quienes-somos.html">Quienes Somos</a></li>	
									<li><a  href="leyes.html">Leyes</a></li>
									<li><a  href="fechas.html">Fechas Importantes</a></li>
									<li><a  href="faq.html">Preguntas Frecuentes</a></li>
									<li><a  href="beneficios.html">Beneficios</a></li>
									<li><a  href="perdida.html">Pérdida de Beneficio</a></li>
									<li><a  href="noticias.html">Noticias</a></li>
									<li><a  href="solicitudes.html">Solicitudes Varias</a></li>
									<li><a  href="horarios.html">Horarios de Atención</a></li>
								</ul>
				           </div>
				        </li>
					  <li><a href="cambio_clave.php" ><span class="glyphicon glyphicon-edit"></span> Cambio de clave</a></li>
					  <li><a href="cerrar_sesion.php"><span class="glyphicon glyphicon-remove"></span> Cerrar sesión</a></li>
				  </ul>
			</div>
	</nav>
</div>

 
	<div class="row content">
		<div class="col-sm-6 text-left"> 
			<div class="container">

				<table>
			<td >
				<h1>Transacción</h1>
			</td>
			</table>
	

				
	
			<tr>
				<td>
					<div class="table-responsive">
							<table class="table table-striped" class="tabla_deuda">

							<tbody>
				
		
	
			<tr>
			<td align="center" valign="top">
				<table width="800" height="100%" border="0" align="center" cellpadding="0" cellspacing="0">
					<tbody>
						<tr>
							<td align="center">
								<table id="table_banner" width="100%" border="0" cellpadding="0" cellspacing="0">
									<tbody>
										<tr>
											<td align="center">
												<img src="imagen/transbank.png" width="250" height="60"/></td>
										</tr>
									</tbody>
								</table>
								<p>
								</p>
							</td>
						</tr>
						<tr>
							<td align="center">
								<table class="table table-striped" id="table_fracaso" style="display:<?php echo $display_fracasado; ?>">
									<tbody>
										<tr>
											<td width="976" height="30" align="center" valign="center">
												Resultado Transaccion
										  </td>
										</tr>
										<tr>
											<td align="left" bgcolor="#f3f3f3" height="70" valign="center" color="#FF0000">
										      <blockquote>
										        <p><span class="Estilo1"><?php echo $MENSAJE_TRANSACCION; ?>								          </span></p>
							          </blockquote>										  </tr>
										<tr>
											<td align="center" height="40" valign="center">
												<input class="boton" value="Cerrar" name="Submit2" type="button" onclick="javascript:window.location.href='web_pay.php';">
										  </td>
										</tr>
									</tbody>
							  </table>
							</td>
						</tr>
						<tr>
							<td align="center">
								<table id="table_aprobado" style="display:<?php echo $display_aprobado; ?>" width="80%" border="0" cellpadding="0" cellspacing="0" class="table table-striped">
									<tbody>
										<tr>
										</tr>
										<tr>
											<td align="left" bgcolor="#f3f3f3" height="70" valign="center" color="#FF0000">
												<table width="80%" border="1" rules="none" align="center">
													<tbody>
														<tr class="encabezado_left">
															<td width="31%">Numero de operacion:</td>
															<td width="69%" align="left" class="claro"><?php echo $voucher; ?></td>
														</tr>
														<tr class="encabezado_left">
															<td width="31%">Numero de Tarjeta:</td>
															<td width="69%" align="left" class="claro"><?php echo $num_tarjeta; ?></td>
														</tr>
														<tr class="encabezado_left">
															<td width="31%">Monto $:</td>
															<td width="69%" align="left" class="oscuro"><?php echo $monto; ?></td>
														</tr>
														<tr class="encabezado_left">
															<td width="31%">Codigo de Autorizacion:</td>
															<td width="69%" align="left" class="claro" colspan="3"><?php echo $cod_autorizacion; ?></td>
														</tr>
														<tr class="encabezado_left">
															<td width="31%">Numero de Cuotas:</td>
															<td width="69%" align="left" class="oscuro" colspan="3"><?php echo $num_cuota; ?></td>
														</tr>
														<tr class="encabezado_left">
															<td width="31%">Numero de Deuda:</td>
															<td width="69%" align="left" class="oscuro" colspan="3"><?php echo $COD_DEUDA; ?></td>
														</tr>
														<tr class="encabezado_left">
															<td width="31%">Deudor:</td>
															<td width="69%" align="left" class="oscuro" colspan="3"><?php echo $NOM_DEUDOR; ?></td>
														</tr>
														<tr class="encabezado_left">
															<td width="31%">Descripcion del pago:</td>
															<td width="69%" align="left" class="oscuro" colspan="3">Pago de la cuota Nº <?php echo $NRO_CUOTA; ?> del Credito Institucional</td>
														</tr>
														<tr class="encabezado_left">
															<td width="31%">Comercio:</td>
															<td width="69%" align="left" class="oscuro" colspan="3">UNIVERSIDAD DE SANTIAGO DE CHILE DEPARTAMENTO DE FINANZAS</td>
														</tr>
														<tr class="encabezado_left">
															<td width="31%">Imprimir Comprobante:</td>
															<td width="69%" align="left" class="claro" colspan="3">
																<input type="button" value="Imprimir" onclick="window.open('print_comprobante.php?voucher=<?php echo $voucher;?>&monto=<?php echo $monto;?>&nt=<?php echo $num_tarjeta;?>&n_cuota=<?php echo $num_cuota;?>&fecha=<?php echo $fecha_autorizacion;?>&autorizacion=<?php echo $cod_autorizacion;?>&nro_cuota=<?php echo $NRO_CUOTA;?>&deudor=<?php echo $NOM_DEUDOR
																;?>&deuda=<?php echo $COD_DEUDA;?>');">
															</td>
														</tr>
													</tbody>
											  </table>
										    </td>										  
										</tr>
										<tr>
											<td align="center" height="40" valign="center">
												<input class="boton" value="Volver" name="Submit2" type="button" onclick="javascript:window.location.href='web_pay.php';">
										  </td>
										</tr>
									</tbody>
							  </table>
							</td>
						</tr>
						<tr>
							<td>
							</td>
						</tr>
						<tr>
							<td align="center">
								<table width="50%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td>
											<img src="imagen/3cuotas.gif" width="108" height="51"/>										</td>
										<td>
											<img src="imagen/webpayplus.jpg"/>
										</td>
										<td>
											<img src="imagen/lgt_verisign.png" width="124" height="71"/>										</td>
									</tr>
								</table>
							</td>
						</tr>
					</tbody>
			  </table>
			</td>
		</tr>
		</tbody>
									</table>
									</div>
	
				</table>

			</div>
		</div>
	</div>

	





<footer>
		<div class="container">
			<h3>Universidad de Santiago de Chile.</h3>
			<h4>Avenida Libertador Bernardo O'Higgins nº 3363, Estacion Central, Santiago, Chile</h4>
			<h5>Mesa Central (+56) 2 718 00 00</h>
		</div>
	</footer>

	
	<!--<script scr = "js/bootstrap.min.js" ></script>-->
	<!-- Latest compiled and minified JavaScript -->
	<style type="text/css">.Estilo1 {color: #FF0000}</style>
	<script scr = "js/jquery.js" ></script>
	<script language="javascript" type="text/javascript"></script>
	<!--<script src="https://code.jquery.com/jquey-latest.js"></script>
	<script src="Scripts/AC_RunActiveContent.js" type="text/javascript"></script>-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<!--<script charset="iso-8859-1" src="../declaracion/declaracion.js" type="text/javascript"></script>-->
	<!--<script charset="iso-8859-1" src="{K_ROOT_URL}../../commonlib_seven/trunk/script_js/general.js" type="text/javascript"></script>
	<script charset="iso-8859-1" src="{K_ROOT_URL}../../commonlib_seven/trunk/script_js/edit_filter.js" type="text/javascript"></script>-->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>



</body>
</html>