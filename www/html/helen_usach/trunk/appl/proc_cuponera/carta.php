<?php
require_once(dirname(__FILE__)."/../../proc_common/class_proceso_php.php");
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_ejecucion_proceso = $_REQUEST['cod_ejecucion_proceso'];
$cod_tipo_deuda  = $_REQUEST['cod_tipo_deuda'];
$cod_usuario  = $_REQUEST['cod_usuario'];
$sql_count = '';

if (K_TIPO_BD=="oci"){
	$sql = "SELECT	CONVERT(VARCHAR(10),B.FECHA_BOLETA,103) FECHA_BOLETA
					,U.NOM_USUARIO
			FROM	BOLETA B, USUARIO U
			WHERE	B.COD_USUARIO = U.COD_USUARIO
			  AND	B.COD_BOLETA = {PARAM1}";
	
	$sql_count	= "SELECT	count(*)
					FROM	BOLETA B
							,DEUDA DE
					WHERE	YEAR(B.FECHA_BOLETA) = YEAR(GETDATE())
					  AND	B.CARTERA_CONOCIDA = 'S'
					  AND	B.COD_DEUDA = DE.COD_DEUDA
		 			  AND	DE.COD_TIPO_DEUDA = '{PARAM1}'";
}
else if (K_TIPO_BD=="mssql"){
	$sql = "SELECT	B.COD_BOLETA
			  FROM  BOLETA B
					,DEUDA DE
			WHERE	YEAR(B.FECHA_BOLETA) = YEAR(GETDATE())
			  AND	B.CARTERA_CONOCIDA = 'S'
			  AND	B.COD_DEUDA = DE.COD_DEUDA
 			  AND	DE.COD_TIPO_DEUDA = '{PARAM1}'";
	
	$sql_count	= "SELECT	count(*)
					FROM	BOLETA B
							,DEUDA DE
					WHERE	YEAR(B.FECHA_BOLETA) = YEAR(GETDATE())
					  AND	B.CARTERA_CONOCIDA = 'S'
					  AND	B.COD_DEUDA = DE.COD_DEUDA
		 			  AND	DE.COD_TIPO_DEUDA = '{PARAM1}'";
	
}

$fname = tempnam("/tmp", "");

$handle = fopen($fname, 'w');
$handle = fopen($fname, 'a');

$conn = proceso_php::connect();

$res = mssql_query($sql_count, $conn);
$row = mssql_fetch_array($res);
$total = $row[0]; 

proceso_php::ejecucion_proceso($conn, 'SET_TOTAL', $cod_ejecucion_proceso, $total, $fname);

$query_id_main = mssql_query($sql, $conn);
for ($i=0; $i < $total; $i++) {
	if (proceso_php::f_cancela_proceso($conn, $cod_ejecucion_proceso))
		return;
	
	$my_row = mssql_fetch_array($query_id_main);
		
	$indices = array_keys($my_row);
	if ($i==0) {		
		// cabeceras, j=3 para saltarse el COD_DEUDOR
		$linea = '';
		for ($j=3; $j < count($my_row); $j+=2) {
			if ($j > 3)
				$linea .= "\t";
			$linea .= $indices[$j];
		}
		$linea .= "\r\n";
		fwrite($handle, $linea);
	}

	// Para obtener la cuotas morosas se hace dentro del loop
	// porque sino el select queda demasiado lento
	if ($cod_carta <> 3) {	// distinto de inicio cobranza
		$cod_deuda = $my_row['CUOTAS_MOROSAS'];		// aqui viene el cod_deua
		
		
		$sql_morosa = "select ANO_COBRO 
						from PLAN_PAGO 
						where COD_DEUDA = $cod_deuda
						AND COD_ESTADO_PLAN_PAGO = $K_PLAN_MOROSA";
		$query_id_morosa = mssql_query($sql_morosa, $conn);
		$cuotas_morosas = '';
		while ($my_row_morosa = mssql_fetch_array($query_id_morosa)) {
			$cuotas_morosas .= ' - '.$my_row_morosa['ANO_COBRO'];
		}
		$cuotas_morosas = substr($cuotas_morosas, 3);
		$my_row['CUOTAS_MOROSAS'] = $cuotas_morosas;
	}
	
	// datos, j=3 para saltarse el COD_DEUDOR
	$linea = '';
	for ($j=3; $j < count($my_row); $j+=2) {
		if ($j > 3)
			$linea .= "\t";
		$linea .= $my_row[$indices[$j]];
	}
	$linea .= "\r\n";
	fwrite($handle, $linea);
	
	$cod_deudor = $my_row['COD_DEUDOR'];
	$sql = "exec spu_bitacora_deudor '$obs_bitacora', $K_BITACORA_ENVIO_CARTA,$cod_deudor, $cod_usuario";
	$res = mssql_query($sql, $conn);
	
	proceso_php::ejecucion_proceso($conn, 'SET_ACTUAL', $cod_ejecucion_proceso, $i);
}
fclose($handle);
proceso_php::ejecucion_proceso($conn, 'END', $cod_ejecucion_proceso, $total);				
?>