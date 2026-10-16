<?php
require_once(dirname(__FILE__)."/../proc_common/class_proceso_php.php");

$cod_ejecucion_proceso = $_REQUEST['cod_ejecucion_proceso'];
$cod_carta  = $_REQUEST['cod_carta'];
echo $cod_carta;

$K_ESTADO_RENEG_CONFIR= 2;
switch ($cod_carta) {
	case 1:
		$sql = "SELECT		rut
				          	,dig_verif
				          	,apellido_paterno
				          	,apellido_materno
				          	,nombre
				          	,direccion
				          	,nom_comuna
				          	,nom_ciudad
				          	,nom_pais
				FROM    	renegociacion r
				        	,plan_pago_renegociacion ppr
				        	,deuda d
				        	,deudor dr
				        	,direccion dir
				        	,boleta b
				        	,sub_boleta sb
				        	,comuna c
          					,ciudad ci
          					,pais pa
				WHERE   	cod_estado_renegociacion = ".$K_ESTADO_RENEG_CONFIR."
				        	AND ppr.cod_renegociacion = r.cod_renegociacion
				        	AND r.cod_deuda = d.cod_deuda
				        	AND d.cod_deudor = dr.cod_deudor
				        	AND b.cod_boleta = ppr.cod_boleta
				        	AND b.cod_boleta = sb.cod_boleta
				        	AND dir.cod_direccion (+) = f_get_direccion_deudor(d.cod_deudor,'P')
				        	AND c.cod_comuna (+) = dir.cod_comuna
          					AND ci.cod_ciudad (+)  = dir.cod_ciudad
          					AND pa.cod_pais (+) = dir.cod_pais
				        	AND fecha_carta1 IS NULL
				        	AND fecha_carta2 IS NULL
				        	AND fecha_carta3 IS NULL
				        	AND FECHA_VENCTO < SYSDATE
				        	AND FECHA_PAGO IS NULL
				GROUP BY	rut
				          	,dig_verif
				          	,apellido_paterno
				          	,apellido_materno
				          	,nombre
				          	,direccion
				          	,nom_comuna
				          	,nom_ciudad
				          	,nom_pais";
		break;	
	case 2:
		$sql = "SELECT		rut
				          	,dig_verif
				          	,apellido_paterno
				          	,apellido_materno
				          	,nombre
				          	,direccion
				          	,nom_comuna
				          	,nom_ciudad
				          	,nom_pais
				FROM    	renegociacion r
				        	,plan_pago_renegociacion ppr
				        	,deuda d
				        	,deudor dr
				        	,direccion dir
				        	,boleta b
				        	,sub_boleta sb
				        	,comuna c
          					,ciudad ci
          					,pais pa
				WHERE   	cod_estado_renegociacion = ".$K_ESTADO_RENEG_CONFIR."
				        	AND ppr.cod_renegociacion = r.cod_renegociacion
				        	AND r.cod_deuda = d.cod_deuda
				        	AND d.cod_deudor = dr.cod_deudor
				        	AND b.cod_boleta = ppr.cod_boleta
				        	AND b.cod_boleta = sb.cod_boleta
				        	AND dir.cod_direccion (+) = f_get_direccion_deudor(d.cod_deudor,'P')
				        	AND c.cod_comuna (+) = dir.cod_comuna
          					AND ci.cod_ciudad (+)  = dir.cod_ciudad
          					AND pa.cod_pais (+) = dir.cod_pais
				        	AND fecha_carta1 IS NOT NULL
				        	AND fecha_carta2 IS NULL
				        	AND fecha_carta3 IS NULL
				        	AND FECHA_VENCTO < SYSDATE
				        	AND FECHA_PAGO IS NULL
				GROUP BY	rut
				          	,dig_verif
				          	,apellido_paterno
				          	,apellido_materno
				          	,nombre
				          	,direccion
				          	,nom_comuna
				          	,nom_ciudad
				          	,nom_pais";
		break;
	case 3:
		$sql = "SELECT		rut
				          	,dig_verif
				          	,apellido_paterno
				          	,apellido_materno
				          	,nombre
				          	,direccion
				          	,nom_comuna
				          	,nom_ciudad
				          	,nom_pais
				FROM    	renegociacion r
				        	,plan_pago_renegociacion ppr
				        	,deuda d
				        	,deudor dr
				        	,direccion dir
				        	,boleta b
				        	,sub_boleta sb
				        	,comuna c
          					,ciudad ci
          					,pais pa
				WHERE   	cod_estado_renegociacion = ".$K_ESTADO_RENEG_CONFIR."
				        	AND ppr.cod_renegociacion = r.cod_renegociacion
				        	AND r.cod_deuda = d.cod_deuda
				        	AND d.cod_deudor = dr.cod_deudor
				        	AND b.cod_boleta = ppr.cod_boleta
				        	AND b.cod_boleta = sb.cod_boleta
				        	AND dir.cod_direccion (+) = f_get_direccion_deudor(d.cod_deudor,'P')
				        	AND c.cod_comuna (+) = dir.cod_comuna
          					AND ci.cod_ciudad (+)  = dir.cod_ciudad
          					AND pa.cod_pais (+) = dir.cod_pais
				        	AND fecha_carta1 IS NOT NULL
				        	AND fecha_carta2 IS NOT NULL
				        	AND fecha_carta3 IS NULL
				        	AND FECHA_VENCTO < SYSDATE
				        	AND FECHA_PAGO IS NULL
				GROUP BY	rut
				          	,dig_verif
				          	,apellido_paterno
				          	,apellido_materno
				          	,nombre
				          	,direccion
				          	,nom_comuna
				          	,nom_ciudad
				          	,nom_pais";
		break;
	default:
		$sql = "select 	null RUT
						,null DIG_VERIF
						,'Tipo carta: $cod_carta.- no implementado' APELLIDO_PATERNO
						,null APELLIDO_MATERNO
						,null NOMBRE 
						,null DIRECCION
						,null NOM_COMUNA
						,null NOM_CIUDAD
						,null NOM_PAIS
				from 	DUAL";
}

$conn = proceso_php::connect();
$sql_cant = "select count(*) CANT
			 from ($sql)";
$query_id = oci_parse($conn, $sql_cant);
oci_execute($query_id, OCI_DEFAULT);
$row = oci_fetch_array($query_id, OCI_ASSOC+OCI_RETURN_NULLS);
$total = $row['CANT'];

$query_id_main = oci_parse($conn, $sql);
oci_execute($query_id_main, OCI_DEFAULT);

$fname = tempnam("/tmp", "direccion.txt");
$handle = fopen($fname, 'w');
$handle = fopen($fname, 'a');
$linea = 'RUT';
$linea .= "\tDIG_VERIF";
$linea .= "\tAPELLIDO_PATERNO";
$linea .= "\tAPELLIDO_MATERNO";
$linea .= "\tNOMBRE";
$linea .= "\tDIRECCION";
$linea .= "\tNOM_COMUNA";
$linea .= "\tNOM_CIUDAD";
$linea .= "\tNOM_PAIS";
$linea .= "\r\n";
fwrite($handle, $linea);

proceso_php::ejecucion_proceso($conn, 'SET_TOTAL', $cod_ejecucion_proceso, $total, $fname);
for ($i=0; $i < $total; $i++) {
	if (proceso_php::f_cancela_proceso($conn, $cod_ejecucion_proceso))
		return;

	// MAIN
	$row = oci_fetch_array($query_id_main, OCI_ASSOC+OCI_RETURN_NULLS);
	$linea = $row['RUT'];
	$linea .= "\t".$row['DIG_VERIF'];
	$linea .= "\t".$row['APELLIDO_PATERNO'];
	$linea .= "\t".$row['APELLIDO_MATERNO'];
	$linea .= "\t".$row['NOMBRE'];
	$linea .= "\t".$row['DIRECCION'];
	$linea .= "\t".$row['NOM_COMUNA'];
	$linea .= "\t".$row['NOM_CIUDAD'];
	$linea .= "\t".$row['NOM_PAIS'];
	$linea .= "\r\n";
	fwrite($handle, $linea);
	/////
	proceso_php::ejecucion_proceso($conn, 'SET_ACTUAL', $cod_ejecucion_proceso, $i);
}
fclose($handle);
proceso_php::ejecucion_proceso($conn, 'END', $cod_ejecucion_proceso, $total);
?>