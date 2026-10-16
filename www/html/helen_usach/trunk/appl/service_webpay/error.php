<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
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
		<h4> Administraci&oacute;n General del Fondo Solidario</h4><br><br><br></p>
		
			

	</div>
	</header>


<body>


<div class="col-sm-3 sidenav" >
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			    <div class="navbar-header">

						       <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"  data-target="#myNavbar"  aria-expanded="false">
								       
								        <span class="icon-bar"></span>
								        <span class="icon-bar"></span>
								        <span class="icon-bar"></span>                       
						       </button>

						     
			     </div>
				
			<div class="collapse navbar-collapse" id ="myNavbar">

				  <ul class="sidenav">
				  	  <li><a href="index.html" ><span class="glyphicon glyphicon-home"></span> Inicio</a></li>
					  <li><a href="inicio_sesion.php" ><span class="glyphicon glyphicon-user"></span> Inicio Sesi&oacute;n</a></li>
					  <li><a href="consulta_deuda.php" > <span class="glyphicon glyphicon-education"></span> Consulta Deuda</a></li>
					  <li><a href="actualiza_informacion.php"><span class="glyphicon glyphicon-education"></span> Actualizacion de Informacion</a></li>
					  <li><a href="cupon_pago.php" ><span class="glyphicon glyphicon-education"></span> Cupon de Pago</a></li>
					  <li><a href="ingresa_declaracion.php" ><span class="glyphicon glyphicon-education"></span> Declaracion Jurada</a></li>
					  <li><a href="web_pay.php" ><span class="glyphicon glyphicon-education"></span> Pago Internet</a></li>
					   <li><a href="#" data-toggle="collapse" data-target="#toggleDemo" data-parent="#sidenav01" class="collapsed">
         			  	<span class="glyphicon glyphicon-education"></span> Informaci&oacute;n <span class="caret pull-right"></span></a>
				          <div class="collapse" id="toggleDemo" style="height: 0px;">
					            <ul class="nav nav-list">
						            <li><a href="informacion.html" id="informacion">Informaci&oacute;n</a></li>
									<li><a  href="quienes-somos.html">Quienes Somos</a></li>	
									<li><a  href="leyes.html">Leyes</a></li>
									<li><a  href="fechas.html">Fechas Importantes</a></li>
									<li><a  href="faq.html">Preguntas Frecuentes</a></li>
									<li><a  href="beneficios.html">Beneficios</a></li>
									<li><a  href="perdida.html">P&eacute;rdida de Beneficio</a></li>
									<li><a  href="noticias.html">Noticias</a></li>
									<li><a  href="solicitudes.html">Solicitudes Varias</a></li>
									<li><a  href="horarios.html">Horarios de Atenci&oacute;n</a></li>
								</ul>
				           </div>
				        </li>
					  <li><a href="cambio_clave.php" ><span class="glyphicon glyphicon-edit"></span> Cambio de clave</a></li>
					  <li><a href="cerrar_sesion.php"><span class="glyphicon glyphicon-remove"></span> Cerrar sesi&oacute;n</a></li>
				  </ul>
			</div>
		</div>
	</nav>
</div>

<br>
	
	<div class="row content">
		<div class="col-sm-6 text-left"> 
			<div class="container">

				<table>
					<tr>
						<td align="center">
						<?php
						$error = 1;
						$error = $_REQUEST['error'];
							
						if($error == 1){
							echo "USUARIO NO HA INICIADO SESSION.";
						}
						else if($error == 2){
							echo "SE HA ACTUALIZADO SU INFORMACIÓN.";
						}
						else if($error == 3){
							echo "COMPLETE LOS DATOS SOLICITADOS, VUELVA A INTENTAR NUEVAMENTE.";
						}
						else if($error == 4){
							echo "SU DECLARACION YA ESTA CONFIRMADA, NO PUEDE SER MODIFICADA.";
						}
						else if($error == 5){
							echo "SU CLAVE FUE CAMBIADA.";
						}
						else if($error == 6){
							echo "Debe Actualizar su dirección antes de Continuar.";
							
							$script  = '<table width="90%" align="center">
											<tr class="camp_sesion">
												<td colspan="3" height="26"></td>
											</tr>
											<tr>
												<td></td>
												<td align="center">
												<input type="submit" name="b_ingresar" id="b_ingresar" value="Actualizar Dirección" onclick=window.location="actualiza_informacion.php" class="button" />
												</td>							
											</tr>
										</table>';
							echo $script;
						}
						
						
							
						
						
						
						
						
						
						
						else if($error == 7){
							$script  = '<br><br><br>
							<table width="90%" align="center">
											<tr>
												<td class="deuda_resultado" height="40" align="center">
													<span style="color: Maroon; font-size: 15px;">
														<font face="Comic Sans MS,arial,verdana">
														"No tiene permitido ingresar Declaraci&oacute;n Jurada contactarse con FSCU, Fonos de Mesa Central."
														</font>
													</span>
												</td>
											</tr>
											<tr>
												<td class="deuda_resultado" height="40" align="center">
													<input type="submit" name="b_ingresar" id="b_ingresar" value="Volver" onclick=window.location="consulta_deuda.php" class="button" />
												</td>							
											</tr>
										</table>';
							echo $script;
						}
						?></td>
					</tr>
				</table>

				<br>
				<br>
				<br>
				<br>
				<br>
				<br>
				<br>
	

			</div>
		</div>
	</div>





<footer>
			<div class="container">

			<h3>Universidad de Santiago de Chile.</h3>
			<h4> Avenida Libertador Bernardo O'Higgins n 3363. Estaci&oacute;n Central. Santiago. Chile.</h4>
			<h5>Mesa Central (+56) 2 718 00 00 - <a href="http://www.usach.cl/contacto">Oficina de Informaciones, Reclamos y Sugerencias (OIRS)</a></h>
		</div>
	</footer>

	<!--<script scr = "js/bootstrap.min.js" ></script>-->
	<!-- Latest compiled and minified JavaScript -->
	<style type="text/css"></style>
	<script scr = "js/jquery.js" ></script>
	<script language="javascript" type="text/javascript"></script>
	<!--<script src="https://code.jquery.com/jquey-latest.js"></script>
	<script src="Scripts/AC_RunActiveContent.js" type="text/javascript"></script>-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<!--<script charset="iso-8859-1" src="../declaracion/declaracion.js" type="text/javascript"></script>
	<script charset="iso-8859-1" src="{K_ROOT_URL}../../commonlib/trunk/script_js/general.js" type="text/javascript"></script>
	<script charset="iso-8859-1" src="{K_ROOT_URL}../../commonlib/trunk/script_js/edit_filter.js" type="text/javascript"></script>-->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>


</body>
</html>