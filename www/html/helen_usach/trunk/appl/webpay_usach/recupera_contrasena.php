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


	<link href="style_web_pay_usach.css" rel="stylesheet" type="text/css">


	<script charset="utf-8" src="https://sishelen.usach.cl/commonlib_seven/trunk/script_js/general.js" type="text/javascript"></script>

	<script charset="utf-8" src="recupera_contrasena.js" type="text/javascript"></script>

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
					  <li><a href="web_pay.php" ><span class="glyphicon glyphicon-education"></span> Pago Internet</a></li>
					   <li><a href="#" data-toggle="collapse" data-target="#toggleDemo" data-parent="#sidenav01" class="collapsed">
         			  	<span class="glyphicon glyphicon-education"></span> Información <span class="caret pull-right"></span></a>
				          <div class="collapse" id="toggleDemo" style="height: 0px;">
					            <ul class="nav nav-list">
						            <li><a href="informacion.html" id="informacion">Información</a></li>
									<li><a  href="quienes-somos.html">Quienes Somos</a></li>	
									<li><a  href="leyes.html">Leyes</a></li>
									<li><a  href="fechas.html">Fechas Importantes</a></li>
									<li><a  class="active" href="faq.html">Preguntas Frecuentes</a></li>
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

<br>
	
	<div class="row content">
		<div class="col-sm-5"> 
			<div class="container-fluid">
				
			          <h2>Recuperar contraseña</h2>
					       <form class="form-horizontal" action="" method="post">


					       	<div class="form-group">
					       		<label class="control-label " for="Rut" >Rut:</label>
					       		<input placeholder="sin dígito verificador, puntos, ni guión" 
					       		class="form-control" 
					       		name="RUT_0" 
					       		id="RUT_0" 
					       		type="text" 
					       		value="" 
					       		size="25" 
					       		maxLength="10"  
					       		onKeyPress = "return onlyNumbers(this, event,0, 1);" 
					       		onBlur = "this.style.borderColor = this.style.borderWidth = this.style.borderStyle = '';" 
					       		onFocus = "this.style.border='1px solid #FF0000'"/>

					       	</div>
					       	<div class="form-group">


					       		<label class="control-label " for="E-Mail" >E-Mail:</label>
					       		<input placeholder="usach@usach.cl" 
					       		class="form-control" 
					       		name="EMAIL_0" 
					       		id="EMAIL_0" 
					       		type="text" 
					       		value="" 
					       		size="25" 
					       		maxLength="50"  
					       		onKeyUp = "this.value = filter_edit_mail(this.value);"
					       		onBlur = "this.style.borderColor = this.style.borderWidth = this.style.borderStyle = '';" 
					       		onChange = "validate_mail(this);" 
					       		onFocus = "this.style.border='1px solid #FF0000'"/>

					       	</div>
					       	  	<div class="form-group">


					       		<label class="control-label" for="Fecha Nacimiento :" >Fecha Nacimiento:</label>
					       		<input placeholder="01/01/1999"
					       		class="form-control" 
					       		name="FECHA_NACIMIENTO_0" 
					       		id="FECHA_NACIMIENTO_0" 
					       		type="text" 
					       		value="" 
					       		size="25" 
					       		maxLength="50"  
					       		onBlur = "this.style.borderColor = this.style.borderWidth = this.style.borderStyle = '';" 
					       		onFocus = "this.style.border='1px solid #FF0000'"/>




					       	</div>


		            	    <div class="form-group">        
								<div >
									<input class="btn btn-primary btn-block" onclick="return validate();" type="button" name="b_cambiar" id="b_cambiar" value="Aceptar" />
								</div>
							</div>


						     


					  </form>


					  
			</div>

					
		</div>

		
	</div>




<footer>
			<div class="container">

			<h3>Universidad de Santiago de Chile.</h3>
			<h5> Avenida Libertador Bernardo O'Higgins nº 3363. Estación Central. Santiago. Chile.</h5>
			<h5>Mesa Central (+56) 2 718 00 00 - <a href="http://www.usach.cl/contacto">Oficina de Informaciones, Reclamos y Sugerencias (OIRS)</a></h5>
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

	<script type="text/javascript">
	jQuery(document).ready(function(){
  $(".oculto").hide();              
    $(".inf").click(function(){
          var nodo = $(this).attr("href");  
 
          if ($(nodo).is(":visible")){
               $(nodo).hide();
               return false;
          }else{
        $(".oculto").hide("slow");                             
        $(nodo).fadeToggle("fast");
        return false;
          }
    });
}); 
</script>

</body>
</html>
