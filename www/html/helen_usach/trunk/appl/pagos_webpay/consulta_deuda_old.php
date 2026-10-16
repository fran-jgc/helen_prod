<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
include_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/PHP_XLSXWriter_master/xlsxwriter.class.php");
ini_set('memory_limit', '480M');
ini_set('max_execution_time', 900);
include("funciones.php");

class w_detalle_deuda extends w_base {
	function __construct($exel='N') {
		valida_inicio();
	
		$t = new Template_appl("consulta_deuda.html");
		

		
		$sql_plan_pago = "SELECT t.cod_wp_transaccion, d.rut, d.nombre, d.apellido_paterno, d.apellido_materno, t.tipo, t.fecha, t.exito, t.tipo_pago_web, 
			p.CODIGO_RESPUESTA, p.CODIGO_AUTORIZACION, t.cod_deudor, bo.cod_boleta, bo.fecha_boleta, bo.cod_deuda, bo.cod_plan_pago, bo.monto_pesos, sb.nro_sub_boleta, sb.fecha_vencto, 
			ab.COD_ABONO, ab.FECHA_ABONO as FECH_ABONO, ab.MONTO_PESOS as MONTO, ab.COD_DEUDA as CODIGO_DEUDA, ab.COD_PLAN_PAGO as CODIGO_PLAN_PAGO  ,sysdate AS FECHA_DIA
            FROM wp_transaccion t, WP_PAGO_TRANSACCION_WS p, deudor d, wp_sub_boleta wpsb, sub_boleta sb, boleta bo, abono ab 
			WHERE P.COD_WP_TRANSACCION = t.cod_wp_transaccion 
			AND P.CODIGO_AUTORIZACION <> '000000' 
			AND P.CODIGO_RESPUESTA = '0' 
			AND t.exito is not NULL 
			AND TO_DATE(t.fecha,'DD/MM/YYYY') > TO_DATE(SYSDATE, 'DD/MM/YYYY')-60
	  		AND wpsb.cod_wp_transaccion = t.cod_wp_transaccion 
			AND sb.cod_sub_boleta = wpsb.cod_sub_boleta 
			AND bo.cod_boleta = sb.cod_boleta 
			AND d.cod_deudor = t.cod_deudor 
			AND ab.cod_sub_boleta(+) = wpsb.cod_sub_boleta 
			AND ab.COD_ESTADO_PAGO = 1 
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
		    		    
		    $this->exportar();
		}

		print $t->toString();
	}
	
	function exportar() {
	   
	    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	    
	    $filename = "Abonos.xlsx";
	    header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	    header('Content-Transfer-Encoding: binary');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    
	    $writer = new XLSXWriter();
	    $writer->setAuthor('Integrasystem');
	    $sheet_name = "Abonos";
	    
	    
	   // $this->make_filtros();
	    $sql = "SELECT t.cod_wp_transaccion TRANSACCION
                    , d.rut                 RUT
                    , d.nombre              NOMBRE
                    , d.apellido_paterno    AP_PATERNO
                    , d.apellido_materno    AP_MATERNO     
                    , t.exito               EXITO
                    , p.CODIGO_RESPUESTA    RESPUESTA
                    , p.CODIGO_AUTORIZACION AUTORIZACION
                    , t.cod_deudor          DEUDOR
                    ,ab.COD_ABONO           COD_ABONO
                    , ab.FECHA_ABONO as FECH_ABONO
                    , ab.MONTO_PESOS as MONTO
                    , ab.COD_DEUDA as CODIGO_DEUDA
                    , ab.COD_PLAN_PAGO as CODIGO_PLAN_PAGO  
                    ,sysdate AS FECHA_DIA
        	        
        			FROM wp_transaccion t, WP_PAGO_TRANSACCION_WS p, deudor d, wp_sub_boleta wpsb, sub_boleta sb, boleta bo, abono ab
        			WHERE P.COD_WP_TRANSACCION = t.cod_wp_transaccion 
			AND P.CODIGO_AUTORIZACION <> '000000' 
			AND P.CODIGO_RESPUESTA = '0' 
			AND t.exito is not NULL 
			AND t.fecha > TO_DATE(SYSDATE, 'DD/MM/YYYY')-60
	  		AND wpsb.cod_wp_transaccion = t.cod_wp_transaccion 
			AND sb.cod_sub_boleta = wpsb.cod_sub_boleta 
			AND bo.cod_boleta = sb.cod_boleta 
			AND d.cod_deudor = t.cod_deudor 
			AND ab.cod_sub_boleta(+) = wpsb.cod_sub_boleta 
			AND ab.COD_ESTADO_PAGO = 1 
      		ORDER BY fecha DESC";
	    
	    $res = $db->query($sql);
	    
	    $header_style = array(
	        
	        'font'=>'Arial',
	        'font-size'=>10,
	        'wrap_text'=>true,
	        'border'=>'left,right,top,bottom',
	        'border-style'=>'medium',
	        'border-color'=>'black',
	        'valign'=>'top',
	        'color'=>'#FFFFFF',
	        'fill'=>'#0101fa',
	        'font-style' => 'bold',
	        'widths'=>[15,10,10,25,20,20,20,10,10,10,15,15,15,20],
	        'halign'=>'center'
	        
	    );
	    
	    $header = array(
	        'TRANSACCION' => '0',
	        'RUT' => '0',
	        'DEUDOR' => '0',
	        'NOMBRE' => 'string',
	        'AP_PATERNO' => 'string',
	        'AP_MATERNO' => 'string',
	        'FECH_ABONO' => 'string',
	        'MONTO' => '0',
	        'EXITO' => 'string',
	        'RESPUESTA' => '0',
	        'AUTORIZACION' => 'string',	        
	        'CODIGO_DEUDA' => '0',
	        'CODIGO_PLAN_PAGO' => '0',
            'COD_ABONO' => '0'
	    );
	    //$writer->writeSheetRow($sheet_name, $header);
	    $writer->writeSheetHeader($sheet_name, $header,$header_style );
	   
	    $data1 = array();
	    while($my_row = $db->get_row()){
	        $array_row = array(
	            $my_row['TRANSACCION']
	            ,$my_row['RUT']
	            ,$my_row['DEUDOR']
	            ,utf8_decode($my_row['NOMBRE'])
	            ,utf8_decode($my_row['AP_PATERNO'])
	            ,utf8_decode($my_row['AP_MATERNO'])
	            ,$my_row['FECH_ABONO']
	            ,$my_row['MONTO']
	            ,$my_row['EXITO']
	            ,$my_row['RESPUESTA']
	            ,$my_row['AUTORIZACION']
	            ,$my_row['CODIGO_DEUDA']
	            ,$my_row['CODIGO_PLAN_PAGO']
	            ,$my_row['COD_ABONO']

	        );
	        
	        $writer->writeSheetRow($sheet_name, $array_row);
	    }
	    $writer->writeToStdOut();
	    exit(0);
	}
}


if (isset($_POST['EXPORTAR'])){
    
    $w = new w_detalle_deuda('S');
   
}
else{
    $w = new w_detalle_deuda();
} 

?>