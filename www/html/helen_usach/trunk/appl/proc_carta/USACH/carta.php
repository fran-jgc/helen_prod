<?php
require_once(dirname(__FILE__)."/../../proc_common/class_proceso_php.php");

ini_set('memory_limit', '550M');
ini_set('max_execution_time', 20000); //900 seconds = 15 minutes 

$cod_ejecucion_proceso = $_REQUEST['cod_ejecucion_proceso'];
$cod_carta  = $_REQUEST['cod_carta'];
$cod_usuario  = $_REQUEST['cod_usuario'];
$ano  = $_REQUEST['ano'];


$K_ESTADO_DEUDA_NO_EXIGIBLE = 1;	
$K_ESTADO_DEUDA_EXIGIBLE = 2;

$K_PLAN_EMITIDA 		= 1;
$K_PLAN_ABONADA 		= 2;
$K_PLAN_MOROSO 			= 4;
$K_COMP_VENCIDA			= 2;
$K_BITACORA_ENVIO_CARTA	= 21;

$K_CARTA_MOROSOS 		= 10;
$K_CARTA_COB_E_DICOM 	= 11;
$K_CARTA_COB_E_EXTERNA 	= 12;
$K_CARTA_TESORERIA 		= 13;

$K_EMP_COBR_EXTERNA 	= 1;
$K_EMP_COBR_DICOM 		= 2;
switch ($cod_carta) {
	case 1:
		// Carta a deudores CS que inician cobranza 
		$sql = "select DE.COD_DEUDOR
						,DE.RUT
						,DE.DIG_VERIF
						,DE.APELLIDO_PATERNO
						,DE.APELLIDO_MATERNO
						,DE.NOMBRE 
						,REPLACE(To_Char(Round(D.MONTO_INICIO_COBRO * f_mon_get_valor_moneda(3),0), 'FM999,999,999'), ',', '.') MONTO_INICIO_COBRO_PESOS
						,replace(D.MONTO_INICIO_COBRO, '.',',') MONTO_INICIO_COBRO_UTM
				from 	DEUDA D, DEUDOR DE
				where 	D.COD_TIPO_DEUDA = 'CS'
         		  and 	D.COD_ESTADO_DEUDA = 1
				  and 	to_char(D.FECHA_INICIO_COBRO, 'yyyy') = to_char(sysdate, 'yyyy')
				  and 	DE.COD_DEUDOR = D.COD_DEUDOR
				order by DE.RUT";
		
		break;
		
	case 2:
		// Carta a deudores CS que deben presentar DJ 
		$sql = "select		DE.COD_DEUDOR
							,DE.RUT
							,DE.DIG_VERIF
							,DE.APELLIDO_PATERNO
							,DE.APELLIDO_MATERNO
							,DE.NOMBRE 
				from 		DEUDA D
							,DEUDOR DE
				where 		D.COD_TIPO_DEUDA = 'CS'
								and D.COD_ESTADO_DEUDA =$K_ESTADO_DEUDA_EXIGIBLE
				  			and DE.COD_DEUDOR = D.COD_DEUDOR
				order by 	DE.RUT";
		break;
		
	case 3:
		// Carta a deudores CS con cuota del año 
		$sql = "select		DE.COD_DEUDOR
							,DE.RUT
							,DE.DIG_VERIF
							,DE.APELLIDO_PATERNO
							,DE.APELLIDO_MATERNO
							,DE.NOMBRE 
							,To_Char(P.FECHA_VENCTO, 'DD/MM/YYY') FECHA_VENCIMIENTO
              				,REPLACE(To_Char(Round((P.MONTO_CAPITAL + P.MONTO_INTERES) * f_mon_get_valor_moneda(3),0), 'FM999,999,999'), ',', '.') VALOR_CUOTA_PESOS
              				,replace((P.MONTO_CAPITAL + P.MONTO_INTERES),'.',',') VALOR_CUOTA_UTM
				from 		PLAN_PAGO P
							,DEUDA D
							,DEUDOR DE
				where 		P.ANO_COBRO = to_number(to_char(sysdate, 'yyyy'))
							  and P.COD_ESTADO_PLAN_PAGO in ($K_PLAN_EMITIDA, $K_PLAN_ABONADA)
				  			and D.COD_DEUDA = P.COD_DEUDA  
				  			and D.COD_TIPO_DEUDA = 'CS'
				  			and D.COD_ESTADO_DEUDA =$K_ESTADO_DEUDA_EXIGIBLE
				  			and DE.COD_DEUDOR = D.COD_DEUDOR
				order by 	DE.RUT";
		break;
		
	case 4:
		// Carta a deudores CSN con cuota del año 
		$sql = "select		DE.COD_DEUDOR
							,DE.RUT
							,DE.DIG_VERIF
							,DE.APELLIDO_PATERNO
							,DE.APELLIDO_MATERNO
							,DE.NOMBRE 
							,To_Char(P.FECHA_VENCTO, 'DD/MM/YYY') FECHA_VENCIMIENTO
              				,REPLACE(To_Char(Round((P.MONTO_CAPITAL + P.MONTO_INTERES) * f_mon_get_valor_moneda(3),0), 'FM999,999,999'), ',', '.') VALOR_CUOTA_PESOS
              				,replace((P.MONTO_CAPITAL + P.MONTO_INTERES),'.',',') VALOR_CUOTA_UTM
				from 		PLAN_PAGO P
							,DEUDA D
							,DEUDOR DE
				where 		P.ANO_COBRO = to_number(to_char(sysdate, 'yyyy'))
				  			and P.COD_ESTADO_PLAN_PAGO in ($K_PLAN_EMITIDA, $K_PLAN_ABONADA)
				  			and D.COD_DEUDA = P.COD_DEUDA  
				  			and D.COD_TIPO_DEUDA = 'CSN'
				  			and D.COD_ESTADO_DEUDA = $K_ESTADO_DEUDA_EXIGIBLE
				  			and DE.COD_DEUDOR = D.COD_DEUDOR
				ORDER by 	DE.RUT";
		break;	
		
	case 5:
		// Carta a deudores CSR con cuota del año 
		$sql = "select		DE.COD_DEUDOR
							,DE.RUT
							,DE.DIG_VERIF
							,DE.APELLIDO_PATERNO
							,DE.APELLIDO_MATERNO
							,DE.NOMBRE 
							,To_Char(P.FECHA_VENCTO, 'DD/MM/YYY') FECHA_VENCIMIENTO
              				,REPLACE(To_Char(Round((P.MONTO_CAPITAL + P.MONTO_INTERES) * f_mon_get_valor_moneda(3),0), 'FM999,999,999'), ',', '.') VALOR_CUOTA_PESOS
              				,replace((P.MONTO_CAPITAL + P.MONTO_INTERES),'.',',') VALOR_CUOTA_UTM
				from 		PLAN_PAGO P
							,DEUDA D
							,DEUDOR DE
				where 		P.ANO_COBRO = to_number(to_char(sysdate, 'yyyy'))
							  and P.COD_ESTADO_PLAN_PAGO in ($K_PLAN_EMITIDA, $K_PLAN_ABONADA)
				  			and D.COD_DEUDA = P.COD_DEUDA  
				  			and D.COD_TIPO_DEUDA = 'CSR'
				  			and D.COD_ESTADO_DEUDA = $K_ESTADO_DEUDA_EXIGIBLE
				  			and DE.COD_DEUDOR = D.COD_DEUDOR
				order by 	DE.RUT";
		break;

	case 6:
		// Carta a deudores CU con cuota del año 
		$sql = "select		DE.COD_DEUDOR
							,DE.RUT
							,DE.DIG_VERIF
							,DE.APELLIDO_PATERNO
							,DE.APELLIDO_MATERNO
							,DE.NOMBRE 
							,To_Char(P.FECHA_VENCTO, 'DD/MM/YYY') FECHA_VENCIMIENTO
              				,REPLACE(To_Char(Round((P.MONTO_CAPITAL + P.MONTO_INTERES) * f_mon_get_valor_moneda(3),0), 'FM999,999,999'), ',', '.') VALOR_CUOTA_PESOS
              				,replace((P.MONTO_CAPITAL + P.MONTO_INTERES),'.',',') VALOR_CUOTA_UTM
				from 		PLAN_PAGO P
							,DEUDA D
							,DEUDOR DE
				where 		P.ANO_COBRO = to_number(to_char(sysdate, 'yyyy'))
				  			and P.COD_ESTADO_PLAN_PAGO in (1, 2)
				  			and	rownum < 100
				  			and D.COD_DEUDA = P.COD_DEUDA  
				  			and D.COD_TIPO_DEUDA = 'CU'
				  			and D.COD_ESTADO_DEUDA = 2
				  			and DE.COD_DEUDOR = D.COD_DEUDOR
				order by 	DE.RUT";
		break;

	case 7:
		// Carta a deudores CFCU con cuota del año 
		$sql = "select		DE.COD_DEUDOR
							,DE.RUT
							,DE.DIG_VERIF
							,DE.APELLIDO_PATERNO
							,DE.APELLIDO_MATERNO
							,DE.NOMBRE 
							,To_Char(P.FECHA_VENCTO, 'DD/MM/YYY') FECHA_VENCIMIENTO
              				,REPLACE(To_Char(Round((P.MONTO_CAPITAL + P.MONTO_INTERES) * f_mon_get_valor_moneda(3),0), 'FM999,999,999'), ',', '.') VALOR_CUOTA_PESOS
              				,replace((P.MONTO_CAPITAL + P.MONTO_INTERES),'.',',') VALOR_CUOTA_UTM
				from 		PLAN_PAGO P
							,DEUDA D
							,DEUDOR DE
				where 		P.ANO_COBRO = to_number(to_char(sysdate, 'yyyy'))
				  			and P.COD_ESTADO_PLAN_PAGO in ($K_PLAN_EMITIDA, $K_PLAN_ABONADA)
				  			and D.COD_DEUDA = P.COD_DEUDA  
				  			and D.COD_TIPO_DEUDA = 'CFCU'
				  			and D.COD_ESTADO_DEUDA = $K_ESTADO_DEUDA_EXIGIBLE
				  			and DE.COD_DEUDOR = D.COD_DEUDOR
				order by 	DE.RUT";
		break;	
		
	case 8:
		// Carta a deudores CFU con cuota del año 
		$sql = "select		DE.COD_DEUDOR
							,DE.RUT
							,DE.DIG_VERIF
							,DE.APELLIDO_PATERNO
							,DE.APELLIDO_MATERNO
							,DE.NOMBRE 
							,To_Char(P.FECHA_VENCTO, 'DD/MM/YYY') FECHA_VENCIMIENTO
              				,REPLACE(To_Char(Round((P.MONTO_CAPITAL + P.MONTO_INTERES) * f_mon_get_valor_moneda(3),0), 'FM999,999,999'), ',', '.') VALOR_CUOTA_PESOS
              				,replace((P.MONTO_CAPITAL + P.MONTO_INTERES),'.',',') VALOR_CUOTA_UTM
				from 		PLAN_PAGO P
							,DEUDA D
							,DEUDOR DE
				where 		P.ANO_COBRO = to_number(to_char(sysdate, 'yyyy'))
				  			and P.COD_ESTADO_PLAN_PAGO in ($K_PLAN_EMITIDA, $K_PLAN_ABONADA)
				  			and D.COD_DEUDA = P.COD_DEUDA  
				  			and D.COD_TIPO_DEUDA = 'CFU'
				  			and D.COD_ESTADO_DEUDA = $K_ESTADO_DEUDA_EXIGIBLE
				  			and DE.COD_DEUDOR = D.COD_DEUDOR
				order by 	DE.RUT";
		break;	

	case 9:
		// Carta a deudores CS que inician cobranza el proximo año, para ofrecer descuento 
		
		$sql="select		DE.COD_DEUDOR
							,DE.RUT
							,DE.DIG_VERIF
							,DE.APELLIDO_PATERNO
							,DE.APELLIDO_MATERNO
							,DE.NOMBRE 
							,f_deuda_saldo_total(D.COD_DEUDA) SALDO_DEUDA
							,REPLACE(To_Char(Round((f_deuda_saldo_total(D.COD_DEUDA) * f_mon_get_valor_moneda(3, sysdate)), 0), 'FM999,999,999'), ',', '.') SALDO_DEUDA_PESOS
				from 		DEUDA D
							,DEUDOR DE
				where 		D.COD_TIPO_DEUDA = 'CS'
				  			and D.COD_ESTADO_DEUDA = $K_ESTADO_DEUDA_NO_EXIGIBLE
				  			and to_char(D.FECHA_INICIO_COBRO, 'yyyy') in (to_char(to_number(to_char(sysdate, 'yyyy') + 1)), to_char(to_number(to_char(sysdate, 'yyyy') + 2)))
				  			and DE.COD_DEUDOR = D.COD_DEUDOR
				order by 	DE.RUT";
	
		break;
		
	case 10:
		// Carta a deudores morosos solo rut distintos 
		$sql = "select 		distinct DE.RUT RUT_DEUDOR
							,DE.COD_DEUDOR
							,DE.RUT
							,DE.DIG_VERIF
							,DE.APELLIDO_PATERNO
							,DE.APELLIDO_MATERNO
							,DE.NOMBRE 
							,COD_TIPO_DEUDA
							,0 MORA
							,0 MULTA
							,0 PESOS_MORA
							,0 PESOS_MULTA
				from		DEUDA D
							,DEUDOR DE
				where 		D.COD_ESTADO_DEUDA = $K_ESTADO_DEUDA_EXIGIBLE
				  			and DE.COD_DEUDOR = D.COD_DEUDOR
				order by 	DE.RUT";
		break;
	case 11:
		//DICOM 
		//Carta a deudores morosos solo rut distintos 
		$sql = "select		distinct DE.RUT RUT_DEUDOR
							,DE.COD_DEUDOR
							,DE.RUT
              				,DE.DIG_VERIF
							,DE.APELLIDO_PATERNO
							,DE.APELLIDO_MATERNO
							,DE.NOMBRE 
							      ,PP.ANO_COBRO
				            ,To_Char(PP.FECHA_VENCTO, 'dd/mm/yyyy') FECHA_VENCIMIENTO
				            ,D.COD_TIPO_DEUDA
				            ,REPLACE(To_Char(Round((PP.MONTO_CAPITAL) * f_mon_get_valor_moneda(3),0), 'FM999,999,999'), ',', '.') CAPITAL_MOROSO_PESOS
				            ,PP.MONTO_CAPITAL CAPITAL_MOROSO_UTM
				from 		DEUDA D
							,DEUDOR DE
              				,PLAN_PAGO PP
							,DIRECCION DI
							,COMUNA CO
							,CIUDAD CI
							,PAIS PA
				where 		D.COD_ESTADO_DEUDA = $K_ESTADO_DEUDA_EXIGIBLE
				  			and DE.COD_DEUDOR = D.COD_DEUDOR
                			AND PP.COD_DEUDA = D.COD_DEUDA
                			AND PP.COD_ESTADO_PLAN_PAGO = $K_PLAN_MOROSO
                			AND PP.ANO_COBRO = $ano
				  			and PUBLICA_DICOM = 'S'
				order by 	DE.RUT";
		break;
	case 12:
		//COBRANZA EXTERNA
		//Carta a deudores morosos solo rut distintos 
		$sql = "select		distinct DE.RUT RUT_DEUDOR
							,DE.COD_DEUDOR
							,DE.RUT
							,DE.DIG_VERIF
							,DE.APELLIDO_PATERNO
							,DE.APELLIDO_MATERNO
							,DE.NOMBRE 
							,PP.ANO_COBRO
							,To_Char(PP.FECHA_VENCTO, 'dd/mm/yyyy') FECHA_VENCIMIENTO
				            ,D.COD_TIPO_DEUDA
				            ,REPLACE(To_Char(Round((PP.MONTO_CAPITAL) * f_mon_get_valor_moneda(3),0), 'FM999,999,999'), ',', '.') CAPITAL_MOROSO_PESOS
				            ,(PP.MONTO_CAPITAL) CAPITAL_MOROSO_UTM
				from 		DEUDA D
							,DEUDOR DE
							,PLAN_PAGO PP
				where 		D.COD_ESTADO_DEUDA = $K_ESTADO_DEUDA_EXIGIBLE
				  			and DE.COD_DEUDOR = D.COD_DEUDOR
				  			and PP.COD_DEUDA = D.COD_DEUDA
				  			and PP.COD_ESTADO_PLAN_PAGO = $K_PLAN_MOROSO
				  			AND PP.ANO_COBRO = $ano
				  			and COD_EMPRESA_COBRANZA = $K_EMP_COBR_EXTERNA
				order by 	DE.RUT";
		break;
		
	case 13:
		//TESORERIA
		// Carta a deudores morosos solo rut distintos 
		$sql = "select		distinct DE.RUT RUT_DEUDOR
							,DE.COD_DEUDOR
							,DE.RUT
							,DE.DIG_VERIF
							,DE.APELLIDO_PATERNO
							,DE.APELLIDO_MATERNO
							,DE.NOMBRE 
							,f_pp_saldo_mora (PP.COD_PLAN_PAGO) + f_pp_saldo_multa (PP.COD_PLAN_PAGO) SALDO_MOROSO
				from		DEUDA D
							,DEUDOR DE
							,PLAN_PAGO PP
				where 		f_deuda_saldo_componente(D.COD_DEUDA,$K_COMP_VENCIDA) > 0
				  			and D.COD_ESTADO_DEUDA = $K_ESTADO_DEUDA_EXIGIBLE
						  	and DE.COD_DEUDOR = D.COD_DEUDOR
						  	and PP.COD_DEUDA = D.COD_DEUDA
				order by 	DE.RUT";
		break;

		default:
		base::error("USACH carta.php, Tipo de carta no definido");
}

$fname = tempnam(dirname(__FILE__)."/../../../tmp", "tmp");

$handle = fopen($fname, 'w');
$handle = fopen($fname, 'a');

$conn = proceso_php::connect();

// texto para la bitacora del deudor	
$sql_carta = "select 	NOM_CARTA
				from 	CARTA
				where	COD_CARTA=".$cod_carta;

$query_id = oci_parse($conn, $sql_carta);
oci_execute($query_id, OCI_DEFAULT);
$row = oci_fetch_array($query_id, OCI_ASSOC+OCI_RETURN_NULLS);
$nom_carta = $row['NOM_CARTA']; 
$obs_bitacora = 'Envio carta '.$nom_carta;

// obtiene el count


$sql_count = $sql;
$pos = strpos(strtoupper($sql_count), 'ORDER BY');
$sql_count = substr($sql_count, 0, $pos);
$sql_count = "select count(*) CANT from ($sql_count) A";
$query_id = oci_parse($conn, $sql_count);
oci_execute($query_id, OCI_DEFAULT);
$row = oci_fetch_array($query_id, OCI_ASSOC+OCI_RETURN_NULLS);
$total = $row['CANT']; 

$query_id_main = oci_parse($conn, $sql);
oci_execute($query_id_main, OCI_DEFAULT);

proceso_php::ejecucion_proceso($conn, 'SET_TOTAL', $cod_ejecucion_proceso, $total, $fname);

for ($i=0; $i < $total; $i++) {
	if (proceso_php::f_cancela_proceso($conn, $cod_ejecucion_proceso))
		return;
	
	$my_row = oci_fetch_array($query_id_main, OCI_ASSOC+OCI_RETURN_NULLS);
	$indices = array_keys($my_row);
	
	$cod_deudor = $my_row['COD_DEUDOR'];

	//obtiene el ultimo cod_direccion
	$sql_max_direccion = "SELECT f_get_direccion_deudor($cod_deudor, 'P') MAX_DIRECCION FROM DUAL";
	$query_max_direccion = oci_parse($conn, $sql_max_direccion);
	oci_execute($query_max_direccion, OCI_DEFAULT);
	$my_row_max_direccion = oci_fetch_array($query_max_direccion, OCI_ASSOC+OCI_RETURN_NULLS);
	
	//se obtienen las direcciones
	$max_direccion = $my_row_max_direccion['MAX_DIRECCION'];
	
	$sql_direccion = "SELECT  DI.DIRECCION
                			 ,CO.NOM_COMUNA
                			 ,CI.NOM_CIUDAD
                			 ,PA.NOM_PAIS
                			 ,EMAIL
        			 FROM DEUDOR DR LEFT OUTER JOIN DIRECCION DI ON DR.COD_DEUDOR = DI.COD_DEUDOR
						              LEFT OUTER JOIN COMUNA CO ON DI.COD_COMUNA = CO.COD_COMUNA
						              LEFT OUTER JOIN CIUDAD CI ON DI.COD_CIUDAD = CI.COD_CIUDAD
                              		  LEFT OUTER JOIN PAIS PA ON DI.COD_PAIS = PA.COD_PAIS
        			 WHERE DR.COD_DEUDOR = $cod_deudor
          				AND DI.COD_DIRECCION =$max_direccion";
          
    $query_direccion = oci_parse($conn, $sql_direccion);
	oci_execute($query_direccion, OCI_DEFAULT);
	$my_row_direccion = oci_fetch_array($query_direccion, OCI_ASSOC+OCI_RETURN_NULLS);
	$indices_direccion = array_keys($my_row_direccion);
	
	if ($i==0) {
		if($cod_carta == $K_CARTA_MOROSOS || $cod_carta == $K_CARTA_COB_E_EXTERNA || $cod_carta == $K_CARTA_COB_E_DICOM || $cod_carta == $K_CARTA_TESORERIA){
			
			// cabeceras, j=3 para saltarse el COD_DEUDOR
			$linea = '';
			for ($j=2; $j < count($my_row); $j++) {
				if ($j > 2)
					$linea .= "\t";
					
				$linea .= trim($indices[$j]);
			}
			//se llena las direcciones
			for ($j=0; $j < count($my_row_direccion); $j++) {
				$linea .= "\t";
				$linea .= trim($indices_direccion[$j]);
			}
			
			$linea .= "\r\n";
			fwrite($handle, $linea);
		}
		else
		{
			// cabeceras, j=3 para saltarse el COD_DEUDOR
			$linea = '';
			for ($j=1; $j < count($my_row); $j++) {
				if ($j > 1)
					$linea .= "\t";
				$linea .= trim($indices[$j]);
			}
			//se llena las direcciones
			for ($j=0; $j < count($my_row_direccion); $j++) {
				$linea .= "\t";
				$linea .= trim($indices_direccion[$j]);
			}
			$linea .= "\r\n";
			fwrite($handle, $linea);
		}	
	}

	// Se obtiene lso saldo MORA y MULTA
	if ($cod_carta == $K_CARTA_MOROSOS) {
		$cod_deudor = $my_row['COD_DEUDOR'];		
		$sql_morosa = "SELECT nvl(Sum(f_pp_saldo_mora (P.COD_PLAN_PAGO)),0) MORA		
					      ,nvl(Sum(f_pp_saldo_multa (P.COD_PLAN_PAGO)),0) MULTA
					      ,f_mon_get_valor_moneda(3, sysdate) VALOR_MONEDA
						FROM deudor d, deuda da, plan_pago p
						WHERE d.cod_deudor = $cod_deudor
						  AND da.cod_deudor = d.cod_deudor
						  AND p.cod_deuda = da.cod_deuda";
					  //echo $sql_morosa;
		
		$query_id_morosa = oci_parse($conn, $sql_morosa);
		oci_execute($query_id_morosa, OCI_DEFAULT);
		$my_row_morosa = oci_fetch_array($query_id_morosa, OCI_ASSOC+OCI_RETURN_NULLS);

		if ($my_row_morosa['MORA'] + $my_row_morosa['MULTA'] == 0)
			continue;
			
		$my_row['MORA'] = $my_row_morosa['MORA'];
		$my_row['MULTA'] = $my_row_morosa['MULTA'];
		
		$my_row['PESOS_MORA'] = number_format(round($my_row_morosa['MORA'] * $my_row_morosa['VALOR_MONEDA'],0), 0, ',', '.');
		$my_row['PESOS_MULTA'] = number_format(round($my_row_morosa['MULTA'] * $my_row_morosa['VALOR_MONEDA'], 0), 0, ',', '.');		
	}
	
	// datos, j=3 para saltarse el COD_DEUDOR
	$linea = '';
	if($cod_carta == $K_CARTA_MOROSOS || $cod_carta == $K_CARTA_COB_E_EXTERNA || $cod_carta == $K_CARTA_COB_E_DICOM || $cod_carta == $K_CARTA_TESORERIA){
		for ($j=2; $j < count($my_row); $j++) {
			if ($j > 2)
				$linea .= "\t";
			$linea .= trim($my_row[$indices[$j]]);
		}
		//se llena las direcciones
		for($j=0; $j < count($my_row_direccion); $j++) {
			$linea .= "\t";
			$linea .= trim($my_row_direccion[$indices_direccion[$j]]);
		}	
		
	}
	else{
		for ($j=1; $j < count($my_row); $j++) {
			if ($j > 1)
				$linea .= "\t";
			$linea .= trim($my_row[$indices[$j]]);
		}
		//se llena las direcciones
		for($j=0; $j < count($my_row_direccion); $j++) {
			$linea .= "\t";
			$linea .= trim($my_row_direccion[$indices_direccion[$j]]);
		}
	}
	
	$linea .= "\r\n";
	fwrite($handle, $linea);
	
	///// BITACORA DEUDOR
	$cod_deudor = $my_row['COD_DEUDOR'];
	
	$sql = "BEGIN spu_bitacora_deudor('$obs_bitacora', $K_BITACORA_ENVIO_CARTA,$cod_deudor, $cod_usuario); END;";
	$query_id = oci_parse($conn, $sql);
	oci_execute($query_id, OCI_DEFAULT);
	
	proceso_php::ejecucion_proceso($conn, 'SET_ACTUAL', $cod_ejecucion_proceso, $i);
}

fclose($handle);
proceso_php::ejecucion_proceso($conn, 'END', $cod_ejecucion_proceso, $total);	

?>