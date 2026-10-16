<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$ve_campo 					= $_REQUEST['campo'];
$ve_fecha_repro 			= $_REQUEST['fecha_repro_rcs'];
$ve_saldo_capital 			= $_REQUEST['saldo_capital'];
$ve_saldo_multa 			= $_REQUEST['saldo_multa'];
$ve_porc_pago_inicial 		= $_REQUEST['porc_pago_inicial'];
$ve_monto_pago_inicial 		= $_REQUEST['monto_pago_inicial'];
$ve_monto_pago_inicial_p 	= $_REQUEST['monto_pago_inicial_p'];
$ve_cant_cuota 				= $_REQUEST['cant_cuota'];

//////////////// valiables locales \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$vl_monto_pago_inicial		= 0;
$vl_monto_pago_inicial_p	= 0;
$vl_porc_dscto_multa		= 0;
$vl_monto_dscto_multa		= 0;
$vl_monto_dscto_multa_p		= 0;
$vl_monto_a_reprogramar		= 0;  
$vl_monto_a_reprogramar_p	= 0;
$vl_cant_utm_deu_repro		= 0;
$vl_cant_cuota				= $ve_cant_cuota;
$vl_mens_monto_pago_inicial	= '';
$vl_mens_porc_dscto_multa	= '';
$vl_mens_porc_inicial		= '';
 

///////////SE OBTIENE EL MONTO UTM DE LA FECHA_REPRO ///////////////////
$sql_monto = sql_repro_rcs::get_sql('AJAX_MONTO_UTM',$vl_fecha_repro);
$res_monto = $db->build_results($sql_monto);
$vl_monto_utm = $res_monto[0]['MONTO']; 
////////////////////////////////////////////////////////////////////////

$vl_porc_pago_inicial 	= ($ve_porc_pago_inicial =='') ? 10 : $ve_porc_pago_inicial;

if ($ve_campo == 'RUT_H_0' )
{
	$saldo_capital_10 = round($ve_saldo_capital*0.1,4); 
	
	if($ve_saldo_capital < 4)
	{
		$vl_monto_pago_inicial = $ve_saldo_capital;
		$ve_monto_pago_inicial = $ve_saldo_capital;
	}
	else if($saldo_capital_10 >= 20){
		$vl_monto_pago_inicial = 20.00;
		$ve_monto_pago_inicial = 20.00;
	}
	else if($saldo_capital_10<= 4){
		$vl_monto_pago_inicial = 4.00;
		$ve_monto_pago_inicial = 4.00;
	}
	else{
		$vl_monto_pago_inicial = $saldo_capital_10;
		$ve_monto_pago_inicial = $saldo_capital_10;
	}
		
	$vl_porc_pago_inicial		= ($ve_monto_pago_inicial*100)/($ve_saldo_capital);
	$vl_monto_pago_inicial_p	= $ve_monto_pago_inicial * $vl_monto_utm;
	
	//validar ingreso de % de pago incial este no puede ser menor al 10%	
	$vl_mens_porc_inicial	= ($vl_porc_pago_inicial > 100) ? 'No se puede ingresar más del 100%' : $vl_mens_porc_inicial;
}
else if ($ve_campo == 'RUT_0' )
{
	$saldo_capital_10 = round($ve_saldo_capital*0.1,4); 
	
	if($ve_saldo_capital < 4)
	{
		$vl_monto_pago_inicial = $ve_saldo_capital;
		$ve_monto_pago_inicial = $ve_saldo_capital;
	}
	else if($saldo_capital_10 >= 20){
		$vl_monto_pago_inicial = 20.00;
		$ve_monto_pago_inicial = 20.00;
	}
	else if($saldo_capital_10<= 4){
		$vl_monto_pago_inicial = 4.00;
		$ve_monto_pago_inicial = 4.00;
	}
	else{
		$vl_monto_pago_inicial = $saldo_capital_10;
		$ve_monto_pago_inicial = $saldo_capital_10;
	}
		
	$vl_porc_pago_inicial		= ($ve_monto_pago_inicial*100)/($ve_saldo_capital);
	$vl_monto_pago_inicial_p	= $ve_monto_pago_inicial * $vl_monto_utm;
	
	//validar ingreso de % de pago incial este no puede ser menor al 10%	
	$vl_mens_porc_inicial	= ($vl_porc_pago_inicial > 100) ? 'No se puede ingresar m�s del 100%' : $vl_mens_porc_inicial;
} 
else if($ve_campo == 'PORC_PAGO_INICIAL_0' )
{
	$vl_monto_pago_inicial		= ($vl_porc_pago_inicial*$ve_saldo_capital)/100;
	$vl_monto_pago_inicial_p	= $vl_monto_pago_inicial * $vl_monto_utm;
	
	//validar ingreso de % de pago incial este no puede ser menor al 10%	
	$vl_mens_porc_inicial	= ($vl_porc_pago_inicial > 100) ? 'No se puede ingresar m�s del 100%' : $vl_mens_porc_inicial;
}
else if($ve_campo == 'MONTO_PAGO_INICIAL_0' )
{
	$vl_porc_pago_inicial		= ($ve_monto_pago_inicial*100)/($ve_saldo_capital);
	$vl_monto_pago_inicial		= $ve_monto_pago_inicial;
	$vl_monto_pago_inicial_p	= $ve_monto_pago_inicial * $vl_monto_utm;
	
	//validar ingreso de % de pago incial este no puede ser menor al 10%	
	$vl_mens_porc_inicial	= ($vl_porc_pago_inicial > 100) ? 'No se puede ingresar m�s del 100%' : $vl_mens_porc_inicial;
}
else if($ve_campo == 'MONTO_PAGO_INICIAL_P_0' )
{
	$vl_porc_pago_inicial		= (($ve_monto_pago_inicial_p*100)/($ve_saldo_capital*$vl_monto_utm));
	$vl_monto_pago_inicial		= ($ve_saldo_capital*$vl_porc_pago_inicial)/100;
	$vl_monto_pago_inicial_p	= $ve_monto_pago_inicial_p;
	
	//validar ingreso de % de pago incial este no puede ser menor al 10%	
	$vl_mens_porc_inicial	= ($vl_porc_pago_inicial > 100) ? 'No se puede ingresar m�s del 100%' : $vl_mens_porc_inicial;
}
else if($ve_campo == 'CANT_CUOTA_0' )
{
	$vl_monto_pago_inicial		= ($ve_saldo_capital*$vl_porc_pago_inicial)/100;
	$vl_monto_pago_inicial_p	= $vl_monto_pago_inicial * $vl_monto_utm;
}
////////////////////////////////////////////////////////////////////////////
//porc_dscto_multa corresponde a (porc_pago_inicial - 10)
$vl_porc_dscto_multa		= $vl_porc_pago_inicial - 10;
////////////////////////////////////////////////////////////////////////////
	
	$saldo_capital_10 = round($ve_saldo_capital*0.1,4); 
	//Si el monto del pago inicial da menos de 4 UTM que quede en 4 Si da  mas de 20 UTM que quede en 20
	/*if($vl_monto_pago_inicial < 4 && $saldo_capital_10 <20){
		$vl_monto_pago_inicial		= 4;
		$vl_monto_pago_inicial_p	= $vl_monto_pago_inicial * $vl_monto_utm;
		$vl_porc_pago_inicial		= ($vl_monto_pago_inicial*100)/($ve_saldo_capital);
		$vl_mens_monto_pago_inicial = 'Su pago inicial minimo es de 4 UTM.';
	}
	else if($vl_porc_pago_inicial <10 && $vl_monto_pago_inicial < 20 && $saldo_capital_10 >= 20)
	{
		$vl_monto_pago_inicial		= 20;
		$vl_monto_pago_inicial_p	= $vl_monto_pago_inicial * $vl_monto_utm;
		$vl_porc_pago_inicial		= ($vl_monto_pago_inicial*100)/($ve_saldo_capital);
		$vl_mens_monto_pago_inicial = 'Su pago inicial minimo es de 20 UTM';
	}elseif($saldo_capital_10 <20 && $saldo_capital_10 > 4){
		
		$vl_monto_pago_inicial		= $saldo_capital_10;
		$vl_monto_pago_inicial_p	= $vl_monto_pago_inicial * $vl_monto_utm;
		$vl_porc_pago_inicial		= ($vl_monto_pago_inicial*100)/($ve_saldo_capital);
		$vl_mens_monto_pago_inicial = 'Su pago inicial minimo es de '.$vl_monto_pago_inicial.' UTM';
	}
	*/
	

	if($ve_saldo_capital < 4){
		$vl_monto_pago_inicial		= $ve_saldo_capital;
		$vl_monto_pago_inicial_p	= $vl_monto_pago_inicial * $vl_monto_utm;
		$vl_porc_pago_inicial		= ($vl_monto_pago_inicial*100)/($ve_saldo_capital);
		$vl_mens_monto_pago_inicial = 'Su pago inicial m�nimo es el saldo capital.';
	}else if($saldo_capital_10 >= 20 && $vl_monto_pago_inicial <20){
		$vl_monto_pago_inicial		= 20;
		$vl_monto_pago_inicial_p	= $vl_monto_pago_inicial * $vl_monto_utm;
		$vl_porc_pago_inicial		= ($vl_monto_pago_inicial*100)/($ve_saldo_capital);
		$vl_mens_monto_pago_inicial = 'Su pago inicial m�nimo es de 20 UTM.';
	}else if($saldo_capital_10 <= 4 && $vl_monto_pago_inicial  <4){
		$vl_monto_pago_inicial		= 4;
		$vl_monto_pago_inicial_p	= $vl_monto_pago_inicial * $vl_monto_utm;
		$vl_porc_pago_inicial		= ($vl_monto_pago_inicial*100)/($ve_saldo_capital);
		$vl_mens_monto_pago_inicial = 'Su pago inicial m�nimo es de 4 UTM.';
	}else if($saldo_capital_10 < 20 && $saldo_capital_10 > 4 && $vl_monto_pago_inicial  <$saldo_capital_10){
		$vl_monto_pago_inicial		= $saldo_capital_10;
		$vl_monto_pago_inicial_p	= $vl_monto_pago_inicial * $vl_monto_utm;
		$vl_porc_pago_inicial		= ($vl_monto_pago_inicial*100)/($ve_saldo_capital);
		$vl_mens_monto_pago_inicial = "Su pago inicial m�nimo es de $vl_monto_pago_inicial UTM.";
	}
	//El porcentaje de descuento de interes penal debe esta entre 20 y 80
	if($vl_porc_dscto_multa < 20){
		$vl_porc_dscto_multa = 20;
		$vl_mens_porc_dscto_multa	= '';
	}
	else if($vl_porc_dscto_multa > 80){
		$vl_porc_dscto_multa = 80;
		$vl_mens_porc_dscto_multa	= 'Según disposición legal, el descuento máximo de interés moratorio corresponde a un 80%';
	}
	
	if ($ve_campo == 'CANT_CUOTA_0' )
	{
		$vl_mens_porc_dscto_multa	= '';
	}
////////////////////////////////////////////////////////////////////////////	
$vl_monto_dscto_multa		= (($vl_porc_dscto_multa * $ve_saldo_multa)/100);
$vl_monto_dscto_multa_p		= $vl_monto_dscto_multa *  $vl_monto_utm;
$vl_monto_a_reprogramar		= ($ve_saldo_capital+$ve_saldo_multa) - $vl_monto_pago_inicial - $vl_monto_dscto_multa;  
$vl_monto_a_reprogramar_p	= $vl_monto_a_reprogramar *  $vl_monto_utm;

/////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////

//se calacula la cantidad de utm que que tiene $vl_monto_a_reprogramar para calcular 
//el plazo de pago del remanente de la deuda
//si $vl_monto_a_reprogramarinferior o igual 150 utm el plazo es de 10 a�os 
//si $vl_monto_a_reprogramar superior a 150 utm el plazo es de 15 a�os

if($vl_monto_a_reprogramar <= 150){
	$vl_cant_utm_deu_repro = 10;
	$vl_cant_cuota = ($ve_cant_cuota == 0 ) ? 10 :$vl_cant_cuota;
}
else if ($vl_monto_a_reprogramar > 150){
	$vl_cant_utm_deu_repro = 15;
	$vl_cant_cuota = ($ve_cant_cuota == 0 ) ? 15 :$vl_cant_cuota;
}
/*
if ($ve_campo != 'CANT_CUOTA_0' )
{
	$vl_monto_cuota				= round($vl_monto_a_reprogramar * ((pow((1.02), $vl_cant_utm_deu_repro)) * 0.02)/((pow((1.02),$vl_cant_utm_deu_repro))-1) , 4);	
}
else{
	$vl_monto_cuota				= round($vl_monto_a_reprogramar * ((pow((1.02), $vl_cant_cuota)) * 0.02)/((pow((1.02),$vl_cant_cuota))-1) , 4);
}

$vl_monto_cuota_p			= $vl_monto_cuota *  $vl_monto_utm;
*/

$mes_actual = date ("m");
$cant_meses = 12 - $mes_actual; 
$vl_monto_a_reprogramar_cuota = $vl_monto_a_reprogramar * pow(1.02,$cant_meses/12);
if ($ve_campo != 'CANT_CUOTA_0' )
{
	$vl_monto_cuota				= 0.02 * pow(1.02,$vl_cant_utm_deu_repro) * $vl_monto_a_reprogramar_cuota/(pow(1.02,$vl_cant_utm_deu_repro)-1);	
}
else{
	$vl_monto_cuota				= 0.02 * pow(1.02,$vl_cant_cuota) * $vl_monto_a_reprogramar_cuota/(pow(1.02,$vl_cant_cuota)-1);
}

$vl_monto_cuota_p			= $vl_monto_cuota *  $vl_monto_utm;

///////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
//si el monto inicial es 0 quiere decir que no trajo deudas el cual no retornara valores 
if($vl_monto_pago_inicial == 0)
{
	$vl_monto_pago_inicial		= '';
	$vl_monto_pago_inicial_p	= '';
	$vl_porc_dscto_multa		= '';
	$vl_monto_dscto_multa		= '';
	$vl_monto_dscto_multa_p		= '';
	$vl_monto_a_reprogramar		= '';
	$vl_monto_a_reprogramar_p	= '';
	$vl_monto_cuota				= '';
	$vl_monto_cuota_p			= '';
	$vl_porc_pago_inicial		= '';
	$vl_cant_cuota 				= 0;
}
else
{
	$vl_monto_pago_inicial		= number_format($vl_monto_pago_inicial, 4, ',', '.');
	$vl_monto_pago_inicial_p	= number_format($vl_monto_pago_inicial_p, 0, ',', '.');
	$vl_porc_dscto_multa		= number_format($vl_porc_dscto_multa, 2, ',', '.');
	$vl_monto_dscto_multa		= number_format($vl_monto_dscto_multa, 4, ',', '.');
	$vl_monto_dscto_multa_p		= number_format($vl_monto_dscto_multa_p, 0, ',', '.');
	$vl_monto_a_reprogramar		= number_format($vl_monto_a_reprogramar, 4, ',', '.');
	$vl_monto_a_reprogramar_p	= number_format($vl_monto_a_reprogramar_p, 0, ',', '.');
	$vl_monto_cuota				= number_format($vl_monto_cuota, 4, ',', '.');
	$vl_monto_cuota_p			= number_format($vl_monto_cuota_p, 0, ',', '.');
	$vl_porc_pago_inicial		= number_format($vl_porc_pago_inicial, 2, ',', '.');
}

/////////////////////////////////////////////////////////////////////////////////////////////
$result = 	$vl_monto_pago_inicial.'|'.				// $result 0 
				$vl_monto_pago_inicial_p.'|'.		// $result 1
				$vl_porc_dscto_multa.'|'.			// $result 2
				$vl_monto_dscto_multa.'|'.			// $result 3
				$vl_monto_dscto_multa_p.'|'.		// $result 4
				$vl_monto_a_reprogramar.'|'.		// $result 5
				$vl_monto_a_reprogramar_p.'|'.		// $result 6
				$vl_monto_cuota.'|'.				// $result 7
				$vl_monto_cuota_p.'|'.				// $result 8
				$vl_porc_pago_inicial.'|'.			// $result 9
				$vl_cant_utm_deu_repro.'|'.			// $result 10
				$vl_cant_cuota.'|'.					// $result 11
				$vl_mens_monto_pago_inicial.'|'.	// $result 12
				$vl_mens_porc_dscto_multa.'|'.		// $result 13
				$vl_mens_porc_inicial;				// $result 14

print urlencode($result);
?>
