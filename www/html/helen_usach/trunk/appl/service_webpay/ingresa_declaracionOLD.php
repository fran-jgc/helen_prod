<?php
	include("funciones.php");
	valida_inicio(); 
?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<title>Sistema Helen</title>
<link href="style_web_pay_usach.css" rel="stylesheet" type="text/css" />
<script charset="iso-8859-1" src="{K_ROOT_URL}../../commonlib/trunk/script_js/edit_filter.js" type="text/javascript"></script>
<script charset="iso-8859-1" src="{K_ROOT_URL}../../commonlib/trunk/script_js/general.js" type="text/javascript"></script>
<script charset="iso-8859-1" src="../declaracion/declaracion.js" type="text/javascript"></script>

</head>
<body>	
<form action="" method="post" name="input" id="input" enctype="multipart/form-data">
	<table width="983" height="100%" border="0" cellpadding="0" cellspacing="0">
		<tr valign="top">
			<td valign="top" bgcolor="#FFFFFF">
			<br>
				<div class="tit_superior">Declare sus Ingresos</div>						
				
				<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
					<tr>						
						<td>&nbsp;</td>
					</tr>
				</table>
				
				<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
					<tr>
						<td>
							<ul>Antes de Ingresar al <a href="declara_jurada.php" target="mainFrame">Formulario de Declaración de Ingresos</a> lea los documentos que se muestran acontinuación:</ul>
						</td>
					</tr>
					<tr>
						<td>
							<ul><li>Instructivo de llenado Formulario Ley 20572 (<a href="descarga_archivo.php?archivo=Formulario_Ley_20572">Descargar PDF</a>)</li></ul>
						</td>
					</tr>
					<tr>
						<td>
							<ul><li>Instructivo de llenado Formulario Ley 19848 (<a href="descarga_archivo.php?archivo=Formulario_Ley_19848">Descargar PDF</a>)</li></ul>
						</td>
					</tr>
					<tr>
						<td>
							<ul><li>Instructivo de llenado Formulario Ley 19287 (<a href="descarga_archivo.php?archivo=Formulario_Ley_19287">Descargar PDF</a>)</li></ul>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>				
					<tr>
						<td class="deuda_resultado" bgcolor="#F3F3F3" height="40">
							<ul><span style="color: Maroon;"><font face="Comic Sans MS,arial,verdana">"NOTA: Para imprimir su declaración jurada es necesario tener instalado Acrobat Reader."  => (<a href="descarga_archivo.php?archivo=adobe_reader">Bajar Aqui</a>)</font></span></ul>
						</td>
					</tr>
					<tr>
						<td class="deuda_resultado" bgcolor="#F3F3F3" height="5" align="center">
							<ul><span style="color: #3366FF;"><font face="Comic Sans MS,arial,verdana">"NO OLVIDAR HABILITAR VENTANAS EMERGENTES (POP-UP)."</font></span></ul>
						</td>
					</tr>
					<tr>
						<td class="deuda_resultado" bgcolor="#F3F3F3" height="5" align="center">
							<ul><span style="color: #3366FF;"><font face="Comic Sans MS,arial,verdana">"Si tiene dudas con respecto a la habilitación de ventanas emergentes. => (<a href="http://support.mozilla.org/es/kb/Ventanas%20emergentes#os=win&browser=fx35" target="_blank">ingrese aquí)</a>"</font></span></ul>
						</td>
					</tr>
				</table>
								
			</td>
		</tr>
	</table>
</form>
</body>				
</html>