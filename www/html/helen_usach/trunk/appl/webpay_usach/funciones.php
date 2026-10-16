<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

function valida_ultimo_pago_segic($cod_deudor){
	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	$k_ruta_peticion = "https://webpay-server.usach.cl/api/common/default/get-transaction-detail";
	$k_store_id = 10; //parametro fijo que identifica a los pagos institucionales
	$cant_pagados = 0;
	$sum_monto_total = 0;
	
	$sp_bitacora = 'spw_bitacora_transaccion';

	$sql = "SELECT COD_WP_TRANSACCION
				,TOKEN
				,MONTO
				,TO_CHAR(DR.RUT)||DR.DIG_VERIF RUT
                ,EXITO
		FROM WP_TRANSACCION WP, DEUDOR DR
        WHERE TIPO_PAGO_WEB = 'PAGO_SEGIC'
        AND WP.COD_DEUDOR = DR.COD_DEUDOR
        AND WP.COD_DEUDOR =$cod_deudor
        AND EXITO is null
        AND TOKEN IS NOT NULL
        AND ROWNUM = 1
        ORDER BY COD_WP_TRANSACCION DESC";

    $result = $db->build_results($sql);
	$numTransaction = $result[0]['TOKEN'];
	$COD_WP_TRANSACCION = $result[0]['COD_WP_TRANSACCION'];
	$MONTO_BD = $result[0]['MONTO'];
	$RUT = $result[0]['RUT'];

        $ch = curl_init();
        // definimos la URL a la que hacemos la petici�n
        curl_setopt($ch, CURLOPT_URL,$k_ruta_peticion);
        // indicamos el tipo de petici�n: POST
        curl_setopt($ch, CURLOPT_POST, TRUE);
        // definimos cada uno de los par�metros
        curl_setopt($ch, CURLOPT_POSTFIELDS, "store_id=$k_store_id&numTransaction=$numTransaction");
        // recibimos la respuesta y la guardamos en una variable
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $remote_server_output = curl_exec ($ch);
        /*posibles respuestas
         * {"tipo":1,"success":false,"error":"El usuario cancela la transacci�n N� 307044","estado":"no"}
         * {"tipo":1,"success":true,"estado":2,"monto":"10","voucher":"307048","numTarjeta":"2345","codigoAuth":"966087","numCota":0}
         * */
        // cerramos la sesi�n cURL
        curl_close ($ch);
        
        //objeto $json_decode devuelve la transaccion ejemplo:
        //Array ( [tipo] => 1 [success] => 1 [estado] => 2 [monto] => 45186 [voucher] => 925984 [numTarjeta] => 8910 [codigoAuth] => 008414 [numCota] => 0 )
        //Array ( [tipo] => 1 [success] => [error] => El usuario cancela la transacción N° 925529 [estado] => no )
        $json_decode = json_decode($remote_server_output,true);
        
        $resp_json = '';
	    while (list($key,$val) = each($json_decode))
		{
			$resp_json.= "$key=$val"."->";
		}
		
        $sp_bitacora = 'spw_bitacora_transaccion';
		$param = "$COD_WP_TRANSACCION,'VALIDA PAGO PORTAL','RESP_JSON: $resp_json'";
		$db->EXECUTE_SP($sp_bitacora,$param);
		
        
        if (array_key_exists('success', $json_decode)) {
			
			$monto = $json_decode['detail']['details'][0]['amount'];
            $numTarjeta = $json_decode['detail']['cardNumber'];
            $codigoAuth = $json_decode['detail']['details'][0]['authorizationCode'];
            $numCota = $json_decode['detail']['details'][0]['installmentsNumber'];
            $estado = $json_decode['estado'];
            $succes = $json_decode['success'];
            
			
			if($succes){
				if($estado == 2){ //aprobado
				
					if($MONTO_BD == $monto){
						$sp = "spw_web_pay_pago";
						$param = "$COD_WP_TRANSACCION,'1','S'";
						$db->EXECUTE_SP($sp,$param);
						
						$sql_count = "SELECT count(*) COUNT
							FROM WP_TRANSACCION_WS
							WHERE COD_WP_TRANSACCION =$COD_WP_TRANSACCION";
				
						$result_count = $db->build_results($sql_count);
						$count = $result_count[0]['COUNT'];
						
						if($count == 0){
							$sp = 'sp_wp_pago_transaccion_ws';
							$param = "'INSERT',$COD_WP_TRANSACCION,'null','$numTarjeta',null,null,null,null,null,'$codigoAuth',null,'$estado',$monto,null,$numCota,'CREDITO INSTITUCIONAL','$numTransaction'";
							
							if(!$db->EXECUTE_SP($sp,$param)){ 
								$sp_bitacora = 'spw_bitacora_transaccion';
								$param = "$COD_WP_TRANSACCION,'VALIDA PAGO PORTAL','Error sp_wp_pago_transaccion_ws' $param";
								$db->EXECUTE_SP($sp_bitacora,$param);
							}
						}
						
						$sql = "SELECT f_get_matricula(D.COD_DEUDOR, 3000) COD_CARRERA
							,SB.MONTO_CAPITAL * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA) MONTO_CAPITAL
							,SB.MONTO_INTERES * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA) MONTO_INTERES
							,SB.MONTO_MULTA * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA) MONTO_MULTA
							,To_Char(WP.FECHA,'DD/MM/YYYY') FECHA_TRANSACCION
							,To_Char(WP.FECHA,'HH24:MI:SS') HORA_TRANSACCION
							FROM WP_TRANSACCION  WP
							,BOLETA B
							,WP_SUB_BOLETA WPS
							,SUB_BOLETA SB 
							,PLAN_PAGO PP
							,DEUDOR D
							,DEUDA DEU
							,TIPO_DEUDA TD
							WHERE WP.COD_WP_TRANSACCION =$COD_WP_TRANSACCION
							AND WPS.COD_WP_TRANSACCION = WP.COD_WP_TRANSACCION
							AND SB.COD_SUB_BOLETA = WPS.COD_SUB_BOLETA
							AND B.COD_BOLETA = SB.COD_BOLETA
							AND PP.COD_PLAN_PAGO = B.COD_PLAN_PAGO
							AND DEU.COD_DEUDA = B.COD_DEUDA
							AND D.COD_DEUDOR = DEU.COD_DEUDOR
							AND TD.COD_TIPO_DEUDA = DEU.COD_TIPO_DEUDA";
						$result_monto = $db->build_results($sql);
						
					
						$COD_CARRERA = $result_monto[0]['COD_CARRERA'];
						$MONTO_CAPITAL = $result_monto[0]['MONTO_CAPITAL'];
						$MONTO_INTERES = $result_monto[0]['MONTO_INTERES'];
						$MONTO_MULTA = $result_monto[0]['MONTO_MULTA'];
						$FECHA_TRANSACCION = $result_monto[0]['FECHA_TRANSACCION'];
						$HORA_TRANSACCION = $result_monto[0]['HORA_TRANSACCION'];
							
						$K_TIPO_BD = "oci";
						$K_SERVER = "(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=bdprdlegacy.usach.cl)(PORT = 1521)))(CONNECT_DATA=(SERVICE_NAME=FINA)))";
						$K_BD = "FINA";
						$K_USER ="CONS_INTERNET_EX";
						$K_PASS = "consulta2019";
						
						$db_segic = new database($K_TIPO_BD, $K_SERVER, $K_BD, $K_USER, $K_PASS);
						
						$cont_intentos = 0;
						$respuesta_segic = false;
						$error_segic = false;
			
					
						
						//se consulta a Legacy el pago
			        	$sql = "Paq_FondoCredito.pro_ConsPagoWebFC('$RUT','$numTransaction',{RESULT})";
			        	//echo $sql;
			        	//echo '<br>';
						$result_consulta = $db_segic->build_results2($sql);
						
						
						if(count($result_consulta)>0){
							//echo 'vienen registros de legacy';
							//echo '<br>';
							$NUM_TRANS_CONS = $result_consulta[0]['NUM_TRANS'];
							$FECPAGO_CONS = $result_consulta[0]['FEC_PAGO'];
							$RUT_CONS = $result_consulta[0]['RUT_ALUMNO'];
							$CARR_CONS = $result_consulta[0]['CARRERA'];
							$COD_COMERCIO_CONS = $result_consulta[0]['COD_COMERCIO'];
							$CAPITAL_CONS = $result_consulta[0]['CAPITAL'];
							$INTERES_CONS = $result_consulta[0]['INTERES'];
							$MULTA_CONS = $result_consulta[0]['MULTA'];
							$ESTADO_CONS = $result_consulta[0]['ESTADO'];
							
							$respuesta_segic = true;
							
						}
						else{
							//echo 'NO vienen, se debe registrar';
							//echo '<br>';

							$sql = "SELECT sum(SB.MONTO_CAPITAL * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA))  MONTO_CAPITAL
											,sum(SB.MONTO_INTERES * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA))  MONTO_INTERES
											,Sum(SB.MONTO_MULTA * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA))  MONTO_MULTA
										FROM WP_TRANSACCION  WP
										,BOLETA B
										,WP_SUB_BOLETA WPS
										,SUB_BOLETA SB 
										,PLAN_PAGO PP
										,DEUDOR D
										,DEUDA DEU
										,TIPO_DEUDA TD
										WHERE WP.COD_WP_TRANSACCION =$COD_WP_TRANSACCION
										AND WPS.COD_WP_TRANSACCION = WP.COD_WP_TRANSACCION
										AND SB.COD_SUB_BOLETA = WPS.COD_SUB_BOLETA
										AND B.COD_BOLETA = SB.COD_BOLETA
										AND PP.COD_PLAN_PAGO = B.COD_PLAN_PAGO
										AND DEU.COD_DEUDA = B.COD_DEUDA
										AND D.COD_DEUDOR = DEU.COD_DEUDOR
										AND TD.COD_TIPO_DEUDA = DEU.COD_TIPO_DEUDA";
							$result_monto = $db->build_results($sql);
									
								
							$MONTO_CAPITAL = $result_monto[0]['MONTO_CAPITAL'];
							$MONTO_INTERES = $result_monto[0]['MONTO_INTERES'];
							$MONTO_MULTA = $result_monto[0]['MONTO_MULTA'];

							$COD_COMERCIO = 597032905501;
							$fecha_pago = base::str2date($FECHA_TRANSACCION, $HORA_TRANSACCION);
							$sql = "Paq_FondoCredito.Pro_RegPagoWebFC($numTransaction, $fecha_pago, '$RUT',$COD_CARRERA,$COD_COMERCIO,$MONTO_CAPITAL,$MONTO_INTERES,$MONTO_MULTA,{RESULT})";
							
							$result_registro = $db_segic->build_results2($sql);
							$respuesta_registro = $result_registro[0][0];
							
							if($respuesta_registro == 'OK'){
								//echo 'Registro insertado en Legacy';
								//echo '<br>';
								$respuesta_segic = true; 
								
								$sp_aux = 'spw_wp_aux';
								$param = "'ACTUALIZA_LEGACY',$COD_WP_TRANSACCION";
								$db->EXECUTE_SP($sp_aux,$param);
							}
							else{
								
								$sp_bitacora = 'spw_bitacora_transaccion';
								$param = "$COD_WP_TRANSACCION, 'VALIDA PAGO PORTAL', 'ERROR COMUNCIONACION SEGIC $numTransaction $RUT $COD_CARRERA $COD_COMERCIO $MONTO_CAPITAL $MONTO_INTERES $MONTO_MULTA RESPUESTA_REG= $respuesta_registro'";
								$db->EXECUTE_SP($sp_bitacora,$param);
							}
						}
					
		        }
		        else {
		        	$sp_bitacora = 'spw_bitacora_transaccion';
					$param = "$COD_WP_TRANSACCION,'VALIDA PAGO PORTAL','Montos Diferentes' $MONTO_BD - $monto";
					$db->EXECUTE_SP($sp_bitacora,$param);
				
					//echo '<br>';
		            //echo "Montos Diferentes $MONTO_BD - $monto";
		            //echo '<br>';
		        }
	        }
        }
        else if($succes == FALSE){
        	$sp_bitacora = 'spw_bitacora_transaccion';
			$param = "$COD_WP_TRANSACCION,'VALIDA PAGO PORTAL','ESTADO TRANSACCION SUCCES FALSE";
			$db->EXECUTE_SP($sp_bitacora,$param);

			//TRASNACCION FRACASADA
			$sp = "spw_web_pay_pago";
            $param = "$COD_WP_TRANSACCION,'0','S'";
            $db->EXECUTE_SP($sp,$param);
        }
		else{
        	$sp_bitacora = 'spw_bitacora_transaccion';
			$param = "$COD_WP_TRANSACCION,'VALIDA PAGO PORTAL','SIN ESTADO SUCCES";
			$db->EXECUTE_SP($sp_bitacora,$param);

			//TRASNACCION FRACASADA
			$sp = "spw_web_pay_pago";
            $param = "$COD_WP_TRANSACCION,'0','S'";
            $db->EXECUTE_SP($sp,$param);
        }
	}
	else{
        $sp_bitacora = 'spw_bitacora_transaccion';
		$param = "$COD_WP_TRANSACCION,'VALIDA PAGO PORTAL','SIN ESTADO'";
		$db->EXECUTE_SP($sp_bitacora,$param);	
	}

}

function valida_inicio(){
	if (!session::is_set('cod_deudor')){
		header ('Location:'."error.php?error=1");/*header ('Location:'."error.php?error=1");*/
	}
}

function encriptar_url($txt_input, $key){	
	$result = '';
	for($i=0; $i<strlen($txt_input); $i++) {
		$char = substr($txt_input, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)+ord($keychar));
		$result.=$char;
	}
	$txt_ouput = base64_encode($result);

	return $txt_ouput;
}

function dencriptar_url($txt_input, $key){	
	$result = '';
	$string = base64_decode($txt_input);
	for($i=0; $i<strlen($string); $i++) {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)-ord($keychar));
		$result.=$char;
	}
	$txt_ouput = $result;
	
	return $txt_ouput;
}
function get_date(){
	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	if (K_TIPO_BD=='oci')
		$sql = "SELECT	To_Char(sysdate, 'dd/mm/yyyy') FECHA_ACTUAL FROM DUAL";
	else if (K_TIPO_BD=='mssql')
		$sql = "SELECT	convert(varchar, getdate(), 103) FECHA_ACTUAL";
		
	$result_fecha = $db-> build_results($sql);
	$fecha_actual = $result_fecha[0]['FECHA_ACTUAL'];
	
	return $fecha_actual;	
}
function get_valor_utm($cod_moneda){
	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	if (K_TIPO_BD=='oci')
		$sql = "SELECT f_mon_get_valor_moneda($cod_moneda, SYSDATE) VALOR_UTM_MENSUAL FROM DUAL";
	else if (K_TIPO_BD=='mssql')
		$sql = "SELECT dbo.f_mon_get_valor_moneda($cod_moneda, GETDATE()) VALOR_UTM_MENSUAL";
		
	$result_moneda = $db-> build_results($sql);
	$valor_utm = $result_moneda[0]['VALOR_UTM_MENSUAL'];
	
	return $valor_utm;
}
class edit_pass extends edit_password {
	var $tabindex;
	var $size;
	var $maxlen;
	
	function edit_pass($field, $size, $maxlen) {
		parent::edit_password($field, $size, $maxlen);
	}
	function draw_entrable($dato, $record) {
		$field = $this->field.'_'.$record;
		if ($this->readonly)
			$ctrl = '<input class="campoformulario" name="'.$field.'" id="'.$field.'" type="'.$this->type.'" readonly="true" value="'.$dato.'" size="'.$this->size.'" maxLength="'.$this->maxlen.'" ';
		else
			$ctrl = '<input class="campoformulario" name="'.$field.'" id="'.$field.'" type="'.$this->type.'" value="'.$dato.'" size="'.$this->size.'" maxLength="'.$this->maxlen.'" ';
		
		if ($this->type != 'hidden' || $this->forzar_js)
			$ctrl .= $this->make_java_script();

		if ($this->style!='')
			$ctrl .= ' style="'.$this->style.'"';			
		// Se asigna el tabindex
		$ctrl .= ' tabindex="'.($this->tabindex + $record).'"';
		
		$ctrl .= '/>';
			
		return $ctrl;
	}
}
function pass_aleatorio(){
	//GENERA EL ALFANUMERICO ALETORIO Y LLENA LA VARIABLE $RES = ALETORIO
	$length = 6;
	$source = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$source .= '1234567890';
	
	if($length>0){
        $RES = "";
        $source = str_split($source,1);
        for($i=1; $i<=$length; $i++){
            mt_srand((double)microtime() * 1000000);
            $num	= mt_rand(1,count($source));
            $RES	.= $source[$num-1];
        }	 
    }
    return $RES;
}
function base64url_encode($data) { 
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
} 

function base64url_decode($data) {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

function comprobar_declaracion($cod_dj, $minutos_plazo = 14400){
    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    $cod_dj = (int)$cod_dj;
    $minutos_plazo = (int)$minutos_plazo;

    // Mantener visible el boton por 10 dias desde la ultima subida.
    $sql = "SELECT CASE
                    WHEN COUNT(*) = 0 THEN 'SIN_ARCHIVOS'
                    WHEN SYSDATE <= MAX(FECHA_SUBIDA) + ($minutos_plazo / 1440) THEN 'PLAZO_VIGENTE'
                    ELSE 'TIENE_ARCHIVOS'
                END ESTADO
            FROM DECLARACION_ARCHIVO
            WHERE COD_DECLARACION = $cod_dj";
    $result = $db->build_results($sql);

    return $result[0]['ESTADO'];
}

?>
