<?php
ini_set('display_errors', 'On');
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once ("../../appl.ini");
require_once ("funciones.php");

$id_helen = $_REQUEST['id_helen']; 
$monto = $_REQUEST['monto'];
$voucher = $_REQUEST['voucher'];
$num_tarjeta = $_REQUEST['num_tarjeta'];
$cod_autorizacion = $_REQUEST['cod_autorizacion'];
$num_cuota = $_REQUEST['num_cuota'];
$estado = $_REQUEST['estado'];
$estado_transaccion = $_REQUEST['estado_transaccion'];

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
	$param = "'INSERT',
		$id_helen,
		'null' 
		,'$num_tarjeta'
		,null
		,'$fecha_autorizacion'
		,null
		,null
		,null
		,'$cod_autorizacion'
		,null
		,'$estado_transaccion'
		,$monto
		,null
		,$num_cuota
		,'CREDITO INSTITUCIONAL'
		,'$id_helen'";
	
		if($archivo = fopen($nombre_archivo, "a"))
		{
			fwrite($archivo, $param. "\n");
			fclose($archivo);
		}
		
		if(!$db->EXECUTE_SP($sp,$param)){ 
			$data = "sp_wp_pago_transaccion_ws ERROR";
			$sp_bitacora = 'spw_bitacora_transaccion';
			$param = "$id_helen,'$fase','Error sp_wp_pago_transaccion_ws' $param";
			$db->EXECUTE_SP($sp_bitacora,$param);
		}
	
	$sql = "SELECT COUNT(*) CANT
		FROM WP_TRANSACCION
		WHERE COD_WP_TRANSACCION =$id_helen
		AND EXITO = 'S'";
	$result = $db->build_results($sql);
	$cant = $result[0]['CANT'];
	
	// validar monto
	$sql = "SELECT B.MONTO_PESOS
		FROM WP_TRANSACCION  WP
		,BOLETA B
		,WP_SUB_BOLETA WPS
		,SUB_BOLETA SB 
		WHERE WP.COD_WP_TRANSACCION =$id_helen
		AND WPS.COD_WP_TRANSACCION = WP.COD_WP_TRANSACCION
		AND SB.COD_SUB_BOLETA = WPS.COD_SUB_BOLETA
		AND B.COD_BOLETA = SB.COD_BOLETA";
	$result_monto = $db->build_results($sql);
	$monto_guardado = $result_monto[0]['MONTO_PESOS'];
	
	if($cant > 0){//COMPROBAR QUE NO ESTE DUPLICADA
		$urlRedirection = $url_fracaso; 
		$data = "DUPLICADA";
		$sp_bitacora = 'spw_bitacora_transaccion';
		$param = "$id_helen,'$fase','DUPLICADA'";
		$db->EXECUTE_SP($sp_bitacora,$param);
		header ('Location:'."error_portal.php?error=8");
	}else if($monto_guardado != $monto){ 
		$urlRedirection = $url_fracaso; 
		$data = "RECHAZADO";
		 
		$sp_bitacora = 'spw_bitacora_transaccion';
		$param = "$id_helen,'$fase','MONTOS DIFERENTES $monto_guardado VS $monto_pago'";
		$db->EXECUTE_SP($sp_bitacora,$param);
		header ('Location:'."error_portal.php?error=9");
	}else{
		$sp = "spw_web_pay_pago";
		$param = "$id_helen, '1','S'";
		
		if(!$db->EXECUTE_SP($sp,$param)){ 
			$urlRedirection = $url_fracaso;
			$data = "spw_web_pay_pago error";
			$sp_bitacora = 'spw_bitacora_transaccion';
			$param = "$id_helen,'$fase','ERROR spw_web_pay_pago'";
			$db->EXECUTE_SP($sp_bitacora,$param);
			header ('Location:'."error_portal.php?error=10");
		}else{
			$data = "ACEPTADO";
			$sp_bitacora = 'spw_bitacora_transaccion';
			$param = "$id_helen,'$fase','TRANSICION EXITOSA'";
			$db->EXECUTE_SP($sp_bitacora,$param);
			$action = "CONFIRMACION";
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
															<td width="31%">Orden Compra:</td>
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