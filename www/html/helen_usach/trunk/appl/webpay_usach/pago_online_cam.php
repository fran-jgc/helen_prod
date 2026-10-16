<?php
require 'vendor/autoload.php';

use segic\cps\components\CommonCall;
use segic\cps\components\php\BasePagoWidget;

$pago = new BasePagoWidget();
//$pago->init('https://webpay-server.usach.cl/api');
$pago->init('https://webpay-server.usach.cl/api');

$pago->type = CommonCall::TIPO_WEBPAY;
$pago->storeId = '12'; // StoreID : Numero de tienda del servidor de pago SEGIC
$pago->fieldId = "total"; // FieldID : ID del campo con el monto del pago.
$pago->formType = '_self'; // FormType : frame : integrado a la pagina corriente en una iframe | _blank : nueva pagina para el pago | _self : adentro de la misma pagina
$pago->size = 'small'; // Tamaï¿½o del botï¿½n : small | large

$pago->scriptHook = ''
    . 'var rut = $("#data-form").find(\'input[name="rut"]\').val();'
        . 'var monto = $("#data-form").find(\'input[name="total"]\').val();'
            . 'var carrera = $("#data-form").find(\'select[name="carrera"]\').val();'
                . 'var idTransaction = InitResult.idTransaction;'
                    . 'var cod_wp_transacction = $("#cod_wp_tran").val();'
                        . 'var aceptado = false;'
                            . '$.ajax({
        	type: "POST",
        	url: "util.php",
        	data: {"rut" : rut, "monto" : monto, "carrera": carrera, "idTransaction" : idTransaction, "cod_wpt" : cod_wp_transacction },
        	async: false,
        	success: function(data){
        		if(data == "correcto"){
        			aceptado = true;
        		}
        	}
    	});
    	if(!aceptado){return false;}';

$monto_abono_pesos	= $_REQUEST['MONTO_PESOS_H'];

$parametros = $_REQUEST['param_pago_cam'];
$usuario = "fondoCreditoWebPay";  ///usuario de conexion
$password = "WebPay";	///password de conexion

$datos = base64_decode($parametros);

$fp=fopen("../../../../../llaves_portal/webpay_rest/key_privada.pem","r"); 
$pub_key=fread ($fp,8192);

if(!openssl_get_privatekey($pub_key)) {
	$param_descripter = "ERROR_CLAVE";
}else if(!openssl_private_decrypt($datos, $param_descripter, openssl_get_privatekey($pub_key))) {
	$param_descripter = "ERROR_ENCRIPTADO";
}

$dato = explode('&', $param_descripter);
$usuario_webpay = $dato[0]; //rut+dig sin gion  
$pass_webpay = $dato[1];
$monto_abono_pesos = $dato[3];
$codigo_trans = $dato[4]; //representa el identity de la taba WP_TRANSACCION

$_SESSION['id_helen'] = $codigo_trans; //esto genera la session con la variable que quieras

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

	<meta charset="UTF-8">
	<link rel="icon" type="image/png" href="imagen/logo-usach.png" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>


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
		</div>
	</nav>
</div>

 
	<div class="row content">
		<div class="col-sm-6 text-left"> 
			<div class="container">

			<table>
			<td >
				<h1></h1>
			</td>
			</table>
	

				
	
			<tr>
				<td>
					<div class="table-responsive">
							<table class="table table-striped" class="tabla_deuda">

							<tbody>
		<tr>
			<td class="parrafos" align="center" valign="top">
				<table border="0" bordercolor="#ffffff" cellpadding="0" cellspacing="0" width="90%" height="500px" rules="all">
					<tbody>
						<tr>
							<td align="center">
								<table id="Panel1" width="100%" border="0" cellpadding="0" cellspacing="0">
								<thead>
									<tbody>
										<tr>
											<td>
												<div class="sub_tit_superior"></div>
													<table id="Table2" width="100%" >
														<tbody>
															<tr>
																<td id="td_alerta_parcial2" class="sub_tit_superior" height="30" align="left"><br>Importante: </td>
															</tr>
															<tr>
																<td align="left" style="font-size:140%;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Una vez ingresado los datos de su tarjeta, en caso de cualquier problema, verifique en su cuenta si el pago fue descontado. <br>
																&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Si ya fue descontado, no vuelva a realizar otro pago y espere 24Hrs h&aacute;biles, el sistema del FSCU comprobar&aacute; el estado de su pago.</td>
															</tr>
															<tr>
																<td id="td_alerta_parcial" align="center" class="sub_tit_superior">
																	Haga Click en Boton WebPay para redirigir a Transbank.
																	<br>
																	<br>
																</td>
															</tr>
															<tr>
																<td align="center" id="td_crear_boleta"  bgcolor="#f3f3f3" height="40" valign="center" color="#FF0000" style="display:">
																	<input name="cod_wp_tran" id="cod_wp_tran" value="<?php echo $codigo_trans; ?>" type="hidden">
																	<input name="total" id="total" value="<?php echo $monto_abono_pesos; ?>" type="hidden">
																	<?= $pago->generateButton(); ?>
																</td>
															</tr>
															
														</tbody>
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
	<style type="text/css"></style>
	<script scr = "js/jquery.js" ></script>
	<script language="javascript" type="text/javascript"></script>
	<!-- <script src="https://code.jquery.com/jquey-latest.js"></script>
	<script src="Scripts/AC_RunActiveContent.js" type="text/javascript"></script>-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<!-- <script charset="iso-8859-1" src="../declaracion/declaracion.js" type="text/javascript"></script>
	<script charset="iso-8859-1" src="{K_ROOT_URL}../../commonlib_seven/trunk/script_js/general.js" type="text/javascript"></script>
	<script charset="iso-8859-1" src="{K_ROOT_URL}../../commonlib_seven/trunk/script_js/edit_filter.js" type="text/javascript"></script>-->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<script charset="iso-8859-1" src="general.js" type="text/javascript"></script>
<script charset="iso-8859-1" src="web_pay.js" type="text/javascript"></script>

<script>
    jQuery(function ($) {
        <?= $pago->javascriptForm(); ?>
        <?= $pago->javascriptClickButton(); ?>
    });
</script>
</body>
</html>