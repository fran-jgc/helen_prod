<?php
require_once(dirname(__FILE__)."/../../../../../commonlib/trunk/php/auto_load.php");
require_once ("../../../appl.ini");
require_once ("../funciones.php");
include("BotonPago/BotonPago.php");
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$matriz_ini = parse_ini_file("BotonPago/config.ini", true);

$parametros = $_REQUEST['param_servipag'];
$usuario = "fondoCreditoWebPay";  ///usuario de conexion
$password = "WebPay";	///password de conexion

$datos = base64_decode($parametros);

//esto se cambia 
//$fp=fopen("../web_pay/privada.pem","r");  //prueba local integrasystem
$fp=fopen("/var/www/cgi_helen/cgi-bin/maestros/privada.pem","r"); //local servidor en biggi
$pub_key=fread ($fp,8192);

if(!openssl_get_privatekey($pub_key)) {
	$error = 1;	//echo"Error abriendo clave";
	$param_descripter = "ERROR_CLAVE";
}else if(!openssl_private_decrypt($datos, &$param_descripter, openssl_get_privatekey($pub_key))) {
	$error = 2; //echo "Error encriptando datos";
	$param_descripter = "ERROR_ENCRIPTADO";
}

$dato = explode('&', $param_descripter);
$usuario_webpay = $dato[0]; //rut+dig sin gion
$pass_webpay = $dato[1];
$rut_deudor = $dato[2];
$monto_abono_pesos = $dato[3];
$codigo_trans = $dato[4];
/***falta de descriptacion de datos**/

if($usuario_webpay == $usuario && $pass_webpay == $password){
	$sql = "SELECT	 WT.COD_WP_TRANSACCION
					,To_Char(WT.FECHA,'yyyymmdd') FECHA
					,WB.COD_SUB_BOLETA
					,WT.COD_DEUDOR
					,To_Char(WT.FECHA,'dd/mm/yyyy') FECHA_DISPLAY
			FROM	WP_TRANSACCION WT, WP_SUB_BOLETA WB
			WHERE	WT.COD_WP_TRANSACCION = $codigo_trans
			AND		WT.COD_WP_TRANSACCION = WB.COD_WP_TRANSACCION";
		$result = $db->build_results($sql);
		$fecha_trans = $result[0]['FECHA'];
		$fecha_display = $result[0]['FECHA_DISPLAY'];
		$nro_boleta  = $result[0]['COD_SUB_BOLETA'];
		$cod_deudor  = $result[0]['COD_DEUDOR'];
	
	$sql = "SELECT	 RUT
					,DIG_VERIF 
					,NOMBRE || ' ' || APELLIDO_PATERNO || ' ' || APELLIDO_MATERNO NOMBRE
			FROM	DEUDOR
			WHERE	COD_DEUDOR = $cod_deudor";
		$result = $db->build_results($sql);
		$rut_deudor = $result[0]['RUT'];
		$verif_deudor = $result[0]['DIG_VERIF'];
		$nombre_deudor = $result[0]['NOMBRE'];
		
	//Instancio la clase BotonPago;
	$BotonPago = new BotonPago();
	
	//estableco las llaves
	$BotonPago->setRutaLlaves($matriz_ini['Config_Llaves']['privada'], $matriz_ini['Config_Llaves']['publica']);
	$BotonPago->setArrayOrdenamiento($matriz_ini['Config_Nodo']);
	
	//$IdTxCliente => es el COD_WP_TRANSACCION => retornado en xml4 confirmacion de pago
	//$CodigoIdentificador => es el COD_WP_TRANSACCION
	$CodigoCanaldePago = '683';
	$IdTxCliente = $codigo_trans; 
	$FechaPago = $fecha_trans;  
	$MontoTotalDeuda = $monto_abono_pesos;
	$NumeroBoleta = '1';
	$IdSubTrx = '1';
	$CodigoIdentificador = $codigo_trans;
	$Boleta = $nro_boleta;
	$Monto = $monto_abono_pesos;
	$FechaVencimiento = $fecha_trans;
	
	//se crea el XML-1 para servipag
	$xml = $BotonPago->GeneraXML($CodigoCanaldePago, $IdTxCliente, $FechaPago, $MontoTotalDeuda, $NumeroBoleta, $IdSubTrx, $CodigoIdentificador, $Boleta, $Monto, $FechaVencimiento);
	
	//print urlencode($xml);
	//print '?/<br>'.$xml.'<br>/?';
	//print '<input id="XML_0" type="text" size="150" value="'.$xml.'">'
	//print '<textarea id="OBS_0" rows="2" cols="50" name="OBS_0">'.$xml.'</textarea>';
	//$url  = 'https://200.54.71.237/bpe/BPE_Inicio.asp?xml='.$xml;
	
	//header ('Location: '.$url);
	
	print '<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
				<title>Bienvenidos Administraci&oacute;n de Fondos de Cr&eacute;dito Universitario</title>
				<link href="../style_web_pay_usach.css" rel="stylesheet" type="text/css" />
				<script language="JavaScript"> 
					function boton(){ 
						x=window.open("https://200.54.71.237/bpe/BPE_Inicio.asp","xml","width=700,height=550,top=100,left=100,resizable=no,scrollbars=no,menubar=no,toolbar=no,status=no,location=no");
						document.formto.target="xml";
						document.formto.action="https://200.54.71.237/bpe/BPE_Inicio.asp";
						document.formto.submit();
						document.getElementById('."'td_crear_boleta'".').style.display = '."'none'".';
						
						return true;
					}
				</script>
			</head>
			<body>
				<table id="table_aprobado" style="display:{DISPLAY_APROBADO}" width="80%" border="0" cellpadding="0" cellspacing="0" class="tabla_deuda">
					<tbody>
						<tr>
							<td width="976" height="30" align="center" valign="center">
								Confirmación de Pago Servipag
						  </td>
						</tr>
						<tr>
							<br>
							<td align="left" bgcolor="#f3f3f3" height="70" valign="center" color="#FF0000">
								<table width="80%" border="1" rules="none" align="center">
									<tbody>
										<tr class="encabezado_left">
											<td width="31%">Nro. Boleta:</td>
											<td width="69%" align="left" class="claro">'.$nro_boleta.'</td>
										</tr>
										<tr class="encabezado_left">
											<td width="31%">Rut Comprador:</td>
											<td width="69%" align="left" class="claro">'.number_format($rut_deudor, 0, ',', '.').'-'.$verif_deudor.'</td>
										</tr>
										<tr class="encabezado_left">
											<td width="31%">Comprador:</td>
											<td width="69%" align="left" class="oscuro">'.$nombre_deudor.'</td>
										</tr>
										<tr class="encabezado_left">
											<td width="31%">Monto $:</td>
											<td width="69%" align="left" class="oscuro">'.number_format($monto_abono_pesos, 0, ',', '.').'</td>
										</tr>
										<tr class="encabezado_left">
											<td width="31%">Fecha Transaccion:</td>
											<td width="69%" align="left" class="claro">'.$fecha_display.'</td>
										</tr>
										<tr class="encabezado_left">
											<td width="31%">Codigo de Transaccion:</td>
											<td width="69%" align="left" class="claro" colspan="3">'.$codigo_trans.'</td>
										</tr>
									</tbody>
							  </table>
						    </td>										  
						</tr>
					</tbody>
				</table>
				<form  name="formto" method="post" action="https://200.54.71.237/bpe/BPE_Inicio.asp">
					<table id="table_servipag" name="table_servipag" width="80%" border="0" cellpadding="0" cellspacing="0" class="tabla_deuda" rules="all" style="display:">
						<tbody>
							<tr style="display:none">
								<td align="center">
									<input id="xml" name="xml" type="text" size="150" value="'.$xml.'">
								</td>
							</tr>
							<tr>
								<td align="center" id="td_crear_boleta" bgcolor="#f3f3f3" height="40" valign="center" color="#FF0000" style="display:">
									<input id="sp_boleta_servipag" name="sp_boleta_servipag" value="Pagar =>" onclick="javascript:boton()" class="boton" type="submit">
								</td>
							</tr>
						</tbody>
					</table>
				</form>
			</body>
			</html>
			';
	
}else{
	$error = 1;
	echo "error al encriptar";
}
?>