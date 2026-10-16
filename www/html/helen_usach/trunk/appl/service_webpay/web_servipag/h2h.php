<?php
 include("BotonPago/BotonPago.php");
 $matriz_ini = parse_ini_file("BotonPago/config.ini", true);
 $Xml_out = $_POST['XML'];
 $Xml_Resultado = '';
 $nodo = $matriz_ini['Config_Nodo_XML2'];

 $BotonDePago = new BotonPago();
 
 //estableco las rutas de las llaves
 $BotonDePago->setRutaLlaves($matriz_ini['Config_Llaves']['privada'], $matriz_ini['Config_Llaves']['publica']);
 
 //realizo la comprobación del XML2
 $result =  $BotonDePago->CompruebaXML2($Xml_out, $nodo);
 
 //genero codigo y mensaje para  el xml3
 $codigo = '1';
 $mensaje= 'Transaccion Mala';
 if($result){
 	$codigo = '0';
	$mensaje= 'Transaccion OK 10-4';
 }
 //genero el xml3
 $Xml_Resultado = $BotonDePago->GeneraXML3($codigo,$mensaje);
 
echo($Xml_Resultado);
?>