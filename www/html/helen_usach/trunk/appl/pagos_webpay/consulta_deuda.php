<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 1800);//30 minutos
include("funciones.php");

class w_detalle_deuda extends w_base {
	function __construct($exel='N',$tipo_filtro="RUT",$filtro = "", $tipo_deuda= "Todos") {
		valida_inicio();
	
		$t = new Template_appl("consulta_deuda.html");
		

		
		$sql_plan_pago = "SELECT t.cod_wp_transaccion, d.rut, d.dig_verif, d.nombre, d.apellido_paterno, d.apellido_materno, t.tipo, 
                            to_char(t.fecha, 'dd/mm/yyyy') fecha,
                            t.exito, t.tipo_pago_web, 
			                p.CODIGO_RESPUESTA, p.CODIGO_AUTORIZACION, t.cod_deudor, bo.cod_boleta, bo.fecha_boleta, bo.cod_deuda, bo.cod_plan_pago, bo.monto_pesos, sb.nro_sub_boleta, sb.fecha_vencto, 
			ab.COD_ABONO, ab.FECHA_ABONO as FECH_ABONO, ab.MONTO_PESOS as MONTO, ab.COD_DEUDA as CODIGO_DEUDA, ab.COD_PLAN_PAGO as CODIGO_PLAN_PAGO  ,sysdate AS FECHA_DIA
            ,CASE t.TIPO_PAGO_WEB 
                WHEN 'PAGO_TRANSBANK' THEN 'Solidario'
                WHEN 'PAGO_SEGIC' THEN 'Institucional'
              END TIPO_PAGO
            FROM wp_transaccion t, WP_PAGO_TRANSACCION_WS p, deudor d, wp_sub_boleta wpsb, sub_boleta sb, boleta bo, abono ab 
			WHERE P.COD_WP_TRANSACCION = t.cod_wp_transaccion 
			AND P.CODIGO_AUTORIZACION <> '000000' 
			AND P.CODIGO_RESPUESTA in( '0', '2') 
			AND t.exito is not NULL 
	  --AND TO_CHAR (t.fecha, 'mm') =  TO_CHAR (SYSDATE,'mm')
      --AND TO_CHAR (t.fecha, 'yyyy') =  TO_CHAR (SYSDATE,'yyyy')
      AND t.cod_wp_transaccion > 227146
			AND wpsb.cod_wp_transaccion = t.cod_wp_transaccion 
			AND sb.cod_sub_boleta = wpsb.cod_sub_boleta 
			AND bo.cod_boleta = sb.cod_boleta 
			AND d.cod_deudor = t.cod_deudor 
			AND ab.cod_sub_boleta(+) = wpsb.cod_sub_boleta 
			AND ab.COD_ESTADO_PAGO = 1 
            AND to_date(t.fecha)  >= sysdate-7
            
      ORDER BY fecha DESC";

		$dw_plan_pago = new datawindow($sql_plan_pago, 'detalle_cuotas');
		
		$dw_plan_pago->retrieve();

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		for($i = 0; $i < $dw_plan_pago->row_count(); $i++){

		

			/////nuevas validaciones
			
			$num_cuota = $dw_plan_pago->get_item($i, 'COD_WP_TRANSACCION');
			
			}
		
		$dw_plan_pago->habilitar($t, false);
		
		if($exel=='S'){
		    		    
		    $this->exportar($tipo_filtro,$filtro, $tipo_deuda);
		}

		print $t->toString();
	}
	
	function exportar($tipo_filtro, $filtro, $tipo_deuda) {
	    require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/libs/simplexlsxgen/src/SimpleXLSXGen.php";

	    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

	    $sql = "SELECT t.cod_wp_transaccion TRANSACCION
	                , d.rut                 RUT
	                , d.dig_verif           DV
	                , d.nombre              NOMBRE
	                , d.apellido_paterno    AP_PATERNO
	                , d.apellido_materno    AP_MATERNO
	                , t.exito               EXITO
	                , p.CODIGO_RESPUESTA    RESPUESTA
	                , p.CODIGO_AUTORIZACION AUTORIZACION
	                , t.cod_deudor          DEUDOR
	                ,ab.COD_ABONO           COD_ABONO
	                , to_char(ab.FECHA_ABONO, 'dd/mm/yyyy') FECH_ABONO
	                , ab.MONTO_PESOS as MONTO
	                , ab.COD_DEUDA as CODIGO_DEUDA
	                , ab.COD_PLAN_PAGO as CODIGO_PLAN_PAGO
	                ,sysdate AS FECHA_DIA
	                ,CASE t.TIPO_PAGO_WEB
	                    WHEN 'PAGO_TRANSBANK' THEN 'Solidario'
	                    WHEN 'PAGO_SEGIC' THEN 'Institucional'
	                  END TIPO_PAGO
	                FROM wp_transaccion t, WP_PAGO_TRANSACCION_WS p, deudor d, wp_sub_boleta wpsb, sub_boleta sb, boleta bo, abono ab
	                WHERE P.COD_WP_TRANSACCION = t.cod_wp_transaccion
	                AND P.CODIGO_AUTORIZACION <> '000000'
	                AND P.CODIGO_RESPUESTA = '0'
	                AND t.exito is not NULL
	          AND t.cod_wp_transaccion > 227146
	                AND wpsb.cod_wp_transaccion = t.cod_wp_transaccion
	                AND sb.cod_sub_boleta = wpsb.cod_sub_boleta
	                AND bo.cod_boleta = sb.cod_boleta
	                AND d.cod_deudor = t.cod_deudor
	                AND ab.cod_sub_boleta(+) = wpsb.cod_sub_boleta
	                AND ab.COD_ESTADO_PAGO = 1
	                AND to_date(t.fecha)  >= sysdate-7";

	    if ($tipo_deuda != 'Todos') {
	        if ($tipo_deuda == 'Solidario') {
	            $sql = $sql." and t.TIPO_PAGO_WEB = 'PAGO_TRANSBANK'";
	        } else if ($tipo_deuda == 'Institucional') {
	            $sql = $sql." and t.TIPO_PAGO_WEB = 'PAGO_SEGIC'";
	        }
	    }

	    if (strlen($filtro) > 0) {
	        if ($tipo_filtro == 'RUT') {
	            $sql = $sql." and d.rut like '%$filtro%'";
	        } else if ($tipo_filtro == 'FECHA') {
	            $sql = $sql." and to_char(t.fecha, 'dd/mm/yyyy') = '$filtro' ";
	        }
	    }
	    $sql = $sql." ORDER BY fecha DESC";

	    $res = $db->query($sql);

	    $rows = [['<b>TRANSACCION</b>', '<b>RUT</b>', '<b>DV</b>', '<b>DEUDOR</b>', '<b>NOMBRE</b>', '<b>AP_PATERNO</b>', '<b>AP_MATERNO</b>', '<b>FECH_ABONO</b>', '<b>MONTO</b>', '<b>EXITO</b>', '<b>RESPUESTA</b>', '<b>AUTORIZACION</b>', '<b>CODIGO_DEUDA</b>', '<b>CODIGO_PLAN_PAGO</b>', '<b>COD_ABONO</b>', '<b>TIPO_PAGO</b>']];
	    while ($my_row = $db->get_row()) {
	        $rows[] = [$my_row['TRANSACCION'], $my_row['RUT'], $my_row['DV'], $my_row['DEUDOR'], $my_row['NOMBRE'], $my_row['AP_PATERNO'], $my_row['AP_MATERNO'], $my_row['FECH_ABONO'], $my_row['MONTO'], $my_row['EXITO'], $my_row['RESPUESTA'], $my_row['AUTORIZACION'], $my_row['CODIGO_DEUDA'], $my_row['CODIGO_PLAN_PAGO'], $my_row['COD_ABONO'], $my_row['TIPO_PAGO']];
	    }

	    if ($db->database_type == "oci") {
	        oci_free_statement($db->query_id);
	        $db->query_id = false;
	    }

	    \Shuchkin\SimpleXLSXGen::fromArray($rows, 'Abonos')->downloadAs('Abonos.xlsx');
	}
}


if (isset($_POST['EXPORTAR'])){
    $tipo_filtro = $_POST['tipo_filtro'];
    $filtro = $_POST['myInput'];
    $tipo_deuda = $_POST['tipo'];
    
    $w = new w_detalle_deuda('S',$tipo_filtro,$filtro, $tipo_deuda);
   
}
else{
    $w = new w_detalle_deuda();
} 

?>