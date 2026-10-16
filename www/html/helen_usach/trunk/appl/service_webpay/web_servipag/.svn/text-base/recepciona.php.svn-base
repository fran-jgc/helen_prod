<?php
require_once ("../../../appl.ini");
include("BotonPago/BotonPago.php");

/* para revisar si llega a este archivo desde servipag*/
/*$conn = oci_connect(K_USER, K_PASS, K_SERVER);

$sql = "begin UPDATE  DEUDOR
		SET nombre = 'MILLER_MU87777XX'  
		WHERE rut = 89441805; COMMIT; END;";
		
$query_id = oci_parse($conn, $sql);
$resp = oci_execute($query_id, OCI_DEFAULT);*/

 	$matriz_ini = parse_ini_file("BotonPago/config.ini", true);
	$nodo = $matriz_ini['Config_Nodo_XML4'];
	$XML_out=$_POST['xml'];
	
	$BotonDePago = new BotonPago();
 
 	//estableco las rutas de las llaves
 	$BotonDePago->setRutaLlaves($matriz_ini['Config_Llaves']['privada'], $matriz_ini['Config_Llaves']['publica']);
	
	//realizo la comprobación del XML4
 	$result = $BotonDePago->ValidaXml4($XML_out, $nodo);
 
 	//genero codigo y mensaje para  el xml4
 	//0 => positivo
	//1 => no pàgado
 	$codigo = '1';
 	$mensaje= 'Transaccion Mala';
 	if($result){
 		$codigo = '0';
		$mensaje= 'Transaccion OK 10-4';
 	}
	
 	$IdTrxServipag = substr($XML_out, strrpos($XML_out,"<IdTrxServipag>"),(strrpos($XML_out, "</IdTrxServipag>") - strrpos($XML_out,"<IdTrxServipag>"))) ;
 	$IdTxCliente =substr($XML_out, strrpos($XML_out,"<IdTxCliente>"),(strrpos($XML_out, "</IdTxCliente>") - strrpos($XML_out,"<IdTxCliente>"))) ;
	$EstadoPago= substr($XML_out, strrpos($XML_out,"<EstadoPago>"),(strrpos($XML_out, "</EstadoPago>") - strrpos($XML_out,"<EstadoPago>"))) ;
	$Mensaje = substr($XML_out, strrpos($XML_out,"<Mensaje>"),(strrpos($XML_out, "</Mensaje>") - strrpos($XML_out,"<Mensaje>"))) ;
	
	$cod_wp_transaccion = $IdTxCliente;
	$sp = "spw_web_pay_pago";
	
	$display_table_exito = 'none';
	$display_table_facaso = '';
	if($codigo == 1){
 		$param = "$cod_wp_transaccion, '0'"; //transaccion fracaso
 		
 		$TITULO_TRANSACCION = "TRANSACCION RECHAZADA";
 		$MENSAJE_TRANSACCION = "Su transaccion no se ha podido efectuar con exito, si el problema<br>persiste, comuníquese con su banco emisor"; 
 		
 		$conn = oci_pconnect(K_USER, K_PASS, K_SERVER);
		$sql = "BEGIN $sp ($param); COMMIT; END;";
		$query_id = oci_parse($conn, $sql);
		$resp = oci_execute($query_id, OCI_DEFAULT);
	}else if($codigo == 0){
 		$param = "$cod_wp_transaccion, '1'"; //transaccion exito
 		$display_table_exito = '';
		$display_table_facaso = 'none';
 		
		$TITULO_TRANSACCION = "TRANSACCION RECHAZADA";
		$MENSAJE_TRANSACCION = ""; 
		
 		//$conn = oci_connect(K_USER, K_PASS, K_SERVER);
		$conn = oci_pconnect(K_USER, K_PASS, K_SERVER);
		$sql = "BEGIN $sp ($param); COMMIT; END;";
		$query_id = oci_parse($conn, $sql);
		$resp = oci_execute($query_id, OCI_DEFAULT);
			
		//se trae los datos de transaccion cuando se exitosa
		//se puede ocupar el mismo $cod_wp_transaccion pero se esta ocupando el ultimo insert realizado.
		$sql = "SELECT COD_WP_TRANSACCION
						,COD_DEUDOR
				FROM WP_TRANSACCION
				WHERE ROWNUM <=1
				ORDER BY COD_WP_TRANSACCION DESC";
		$query_id = oci_parse($conn, $sql);
		$resp = oci_execute($query_id);
		$row = oci_fetch_array($query_id, OCI_ASSOC+OCI_RETURN_LOBS);
		$cod_transacion = $row['COD_WP_TRANSACCION'];
		$cod_deudor = $row['COD_DEUDOR'];
		
		$sql = "SELECT SB.FECHA_PAGO AS FECHA_PAGO
				        ,B.MONTO_PESOS AS MONTO_PESOS
				        ,B.COD_DEUDA AS COD_DEUDA
				        ,DE.COD_TIPO_DEUDA AS COD_TIPO_DEUDA
		            	,PP.NRO_CUOTA AS NRO_CUOTA
				FROM WP_SUB_BOLETA WSB, SUB_BOLETA SB, BOLETA B, PLAN_PAGO PP
				WHERE WSB.COD_WP_TRANSACCION = $cod_transacion 
				AND WSB.COD_SUB_BOLETA = SB.COD_SUB_BOLETA
				AND SB.COD_BOLETA = B.COD_BOLETA
				AND B.COD_PLAN_PAGO = PP.COD_PLAN_PAGO
				ORDER BY WSB.COD_WP_SUB_BOLETA DESC";
		
		$query_id = oci_parse($conn, $sql);
		$resp = oci_execute($query_id);
		$row = oci_fetch_array($query_id, OCI_ASSOC+OCI_RETURN_LOBS);
		
		//DATOS DE DESPLIEGUE.
		$nro_cuota = $row['NRO_CUOTA'];
		$fecha_pago = $row['FECHA_PAGO'];
		$monto_pesos = $row['MONTO_PESOS'];
		$cod_deuda = $row['COD_DEUDA'];
		$cod_tipo_deuda = $row['COD_TIPO_DEUDA'];
		
		$sql = "SELECT TO_CHAR(RUT) || ' - ' || DIG_VERIF AS RUT_DEUDOR
				FROM DEUDOR
				WHERE COD_DEUDOR = $cod_deudor";
		$query_id = oci_parse($conn, $sql);
		$resp = oci_execute($query_id);
		$row = oci_fetch_array($query_id, OCI_ASSOC+OCI_RETURN_LOBS);
		$rut_deudor = $row['RUT_DEUDOR'];
	} // IF TRANSACCION EXITOSA


print '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento Comprante</title>
<link href="../style_web_pay_usach.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.Estilo1 {color: #FF0000}
-->
</style>
</head>
<body>
<table width="983" height="100%" border="0" align="center" cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<td align="center">
				<table width="80%" height="135" border="1" cellpadding="0" cellspacing="0" class="tabla_deuda" id="table_fracaso" style="display:'.$display_table_facaso.'">
					<tbody>
						<tr>
							<td width="976" height="30" align="center" valign="center">
								'.$TITULO_TRANSACCION.'
						  </td>
						</tr>
						<tr>
							<td align="left" bgcolor="#f3f3f3" height="70" valign="center" color="#FF0000">
						      <blockquote>
						        <p><span class="Estilo1">'.$MENSAJE_TRANSACCION.'								          </span></p>
			          </blockquote>										  </tr>
					</tbody>
			  </table>
			</td>
		</tr>
		<tr>
			<td align="center">
				<table id="table_aprobado" width="80%" border="0" cellpadding="0" cellspacing="0" class="tabla_deuda" style="display:'.$display_table_exito.'">
					<tbody>
						<tr>
							<td width="976" height="30" align="center" valign="center">
								'.$TITULO_TRANSACCION.'
						  </td>
						</tr>
						<tr>
							<td align="left" bgcolor="#f3f3f3" height="70" valign="center" color="#FF0000">
								<table width="80%" border="1" rules="none" align="center">
									<tbody>
										<tr class="encabezado_left">
											<td width="31%">Identificador de Transacción :</td>
											<td width="69%" align="left" class="claro">'.$cod_transacion.'</td>
										</tr>
										<tr class="encabezado_left">
											<td width="31%">Identificador de Cliente :</td>
											<td width="69%" align="left" class="oscuro">'.$IdTxCliente.'</td>
										</tr>
										<tr class="encabezado_left">
											<td width="31%">Estado del Pago :</td>
											<td width="69%" align="left" class="claro">'.$EstadoPago.'</td>
										</tr>
										<tr class="encabezado_left">
											<td width="31%">Mensaje de Transacción :</td>
											<td width="69%" align="left" class="oscuro">'.$Mensaje.'</td>
										</tr>
										<tr class="encabezado_left">
											<td width="31%">XML de confirmación de Pago :</td>
											<td width="69%" align="left" class="claro">'.$XML_out.'</td>
										</tr>
										<tr class="encabezado_left">
											<td width="31%">Rut Deudor :</td>
											<td width="69%" align="left" class="oscuro">'.$rut_deudor.'</td>
										</tr>
										<tr class="encabezado_left">
											<td width="31%">Cod. Deuda :</td>
											<td width="69%" align="left" class="claro" colspan="3">'.$cod_deuda.'</td>
										</tr>
										<tr class="encabezado_left">
											<td width="31%">Tipo Deuda :</td>
											<td width="69%" align="left" class="oscuro" colspan="3">'.$cod_tipo_deuda.'</td>
										</tr>
										<tr class="encabezado_left">
											<td width="31%">Nro Cuota :</td>
											<td width="69%" align="left" class="oscuro">'.$nro_cuota.'</td>
										</tr>
										<tr class="encabezado_left">
											<td width="31%">Fecha Pago :</td>
											<td width="69%" align="left" class="claro" colspan="3">'.$fecha_pago.'</td>
										</tr>
										<tr class="encabezado_left">
											<td width="31%">Monto Pago :</td>
											<td width="69%" align="left" class="oscuro" colspan="3">'.$monto_pesos.'</td>
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
</table>';
?>