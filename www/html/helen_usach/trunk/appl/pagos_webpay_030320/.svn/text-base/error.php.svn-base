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


<nav class="navbar navbar-default">
		<div class="container">
			    <div class="navbar-header">

						       <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"  data-target="#myNavbar"  aria-expanded="false">
								       
								        <span class="icon-bar"></span>
								        <span class="icon-bar"></span>
								        <span class="icon-bar"></span>                       
						       </button>
				</div>
				
			<div class="collapse navbar-collapse" id ="myNavbar">

				  <ul class="nav navbar-nav">
				  	  
					  <li><a href="inicio_sesion.php" ><span class="glyphicon glyphicon-user"></span> Inicio Sesión</a></li>
			

				  </ul>
			</div>
			
		</div>
	</nav>


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
	<script charset="iso-8859-1" src="{K_ROOT_URL}../../commonlib_seven/trunk/script_js/general.js" type="text/javascript"></script>
	<script charset="iso-8859-1" src="{K_ROOT_URL}../../commonlib_seven/trunk/script_js/edit_filter.js" type="text/javascript"></script>-->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>


</body>
</html>