<?php
ini_set('display_errors', 'On');
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/clases/class_PHPMailer.php");
require_once ("../../../appl.ini");

session::set('K_ROOT_URL', K_ROOT_URL);
session::set('K_ROOT_DIR', K_ROOT_DIR);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$k_ruta_peticion = "https://webpay-server.usach.cl/api/common/default/get-transaction-detail";
$k_store_id = 10; //parametro fijo que identifica a los pagos institucionales
$cant_pagados = 0;
$sum_monto_total = 0;
$string_pendiente = '';
$sql = "SELECT COD_REVISION_TRANSBANK
				,TO_CHAR(FECHA_VENTA,'DD/MM/YYYY') FECHA_VENTA
				,IDENTIFICADOR
                ,MONTO
		FROM REVISION_TRANSBANK
		ORDER BY COD_REVISION_TRANSBANK ASC";

$result = $db->build_results($sql);
if(count($result)>0){
    for ($i = 0; $i < count($result); $i++) {
        $COD_REVISION_TRANSBANK = $result[$i]['COD_REVISION_TRANSBANK'];
        $FECHA_VENTA = $result[$i]['FECHA_VENTA'];
        $IDENTIFICADOR = $result[$i]['IDENTIFICADOR'];
        $MONTO = $result[$i]['MONTO'];
        
        //echo "$FECHA_VENTA"."--".$MONTO."<br>";
        
        $sql_wp = "SELECT COD_WP_TRANSACCION
				,TO_CHAR(FECHA,'DD/MM/YYYY') FECHA
				,MONTO
				,COD_DEUDOR
				,TOKEN
		FROM WP_TRANSACCION
		WHERE TIPO_PAGO_WEB = 'PAGO_SEGIC'
		AND TO_CHAR(FECHA,'DD/MM/YYYY') = '$FECHA_VENTA'
		AND MONTO = $MONTO";
		//echo "$sql_wp"."<br>";
        echo $i;
        echo '<br>';
		$result_wp = $db->build_results($sql_wp);
		
		
		if(count($result_wp)>1){
			
			$sql_estado = "SELECT COD_WP_TRANSACCION
					,TO_CHAR(FECHA,'DD/MM/YYYY') FECHA
					,MONTO
					,COD_DEUDOR
					,TOKEN
			FROM WP_TRANSACCION
			WHERE TIPO_PAGO_WEB = 'PAGO_SEGIC'
			AND TO_CHAR(FECHA,'DD/MM/YYYY') = '$FECHA_VENTA'
			AND MONTO = $MONTO
			AND EXITO = 'S'
			AND TOKEN = '$IDENTIFICADOR'";
			$result_estado = $db->build_results($sql_estado);
			
			if(count($result_estado)==1){
				echo $IDENTIFICADOR.'--'.$FECHA_VENTA.'--'.$MONTO.' PAGADO <br>';
			}
			else{
				$sql_estado_pagado = "SELECT COD_WP_TRANSACCION
						,TO_CHAR(FECHA,'DD/MM/YYYY') FECHA
						,MONTO
						,COD_DEUDOR
						,TOKEN
				FROM WP_TRANSACCION
				WHERE TIPO_PAGO_WEB = 'PAGO_SEGIC'
				AND TO_CHAR(FECHA,'DD/MM/YYYY') = '$FECHA_VENTA'
				AND MONTO = $MONTO
				AND EXITO = 'S'
				AND TOKEN is NULL";
				$result_estado_pagado = $db->build_results($sql_estado_pagado);
				
				if(count($result_estado_pagado)>1){
					echo $IDENTIFICADOR.'--'.$FECHA_VENTA.'--'.$MONTO.' REVISAR <br>';
				}
				else if(count($result_estado_pagado)==1){
					echo $IDENTIFICADOR.'--'.$FECHA_VENTA.'--'.$MONTO.' *PAGADO SIN TOKEN* <br>';
				}
				else{
					echo $IDENTIFICADOR.'--'.$FECHA_VENTA.'--'.$MONTO.' **NO ENCONTRADO EN VARIOS** <br>';
				}
			}
		}
		else if(count($result_wp)==1){
			$sql_estado = "SELECT COD_WP_TRANSACCION
					,TO_CHAR(FECHA,'DD/MM/YYYY') FECHA
					,MONTO
					,COD_DEUDOR
					,TOKEN
			FROM WP_TRANSACCION
			WHERE TIPO_PAGO_WEB = 'PAGO_SEGIC'
			AND TO_CHAR(FECHA,'DD/MM/YYYY') = '$FECHA_VENTA'
			AND MONTO = $MONTO
			AND EXITO = 'S'
			AND TOKEN = '$IDENTIFICADOR'";
			$result_estado = $db->build_results($sql_estado);
			
			if(count($result_estado)==1){
				echo $IDENTIFICADOR.'--'.$FECHA_VENTA.'--'.$MONTO.' **PAGADO** <br>';
			}
			else{
				
				$sql_estado_fail = "SELECT COD_WP_TRANSACCION
						,TO_CHAR(FECHA,'DD/MM/YYYY') FECHA
						,MONTO
						,COD_DEUDOR
						,TOKEN
				FROM WP_TRANSACCION
				WHERE TIPO_PAGO_WEB = 'PAGO_SEGIC'
				AND TO_CHAR(FECHA,'DD/MM/YYYY') = '$FECHA_VENTA'
				AND MONTO = $MONTO
				AND EXITO = 'N'";
				$result_estado_fail = $db->build_results($sql_estado_fail);
			
				if(count($result_estado_fail)==1){
					echo $IDENTIFICADOR.'--'.$FECHA_VENTA.'--'.$MONTO.' NO PROCESADO <br>';
				}
				else{

					$sql_estado_pendiente = "SELECT COD_WP_TRANSACCION
							,TO_CHAR(FECHA,'DD/MM/YYYY') FECHA
							,MONTO
							,COD_DEUDOR
							,TOKEN
					FROM WP_TRANSACCION
					WHERE TIPO_PAGO_WEB = 'PAGO_SEGIC'
					AND TO_CHAR(FECHA,'DD/MM/YYYY') = '$FECHA_VENTA'
					AND MONTO = $MONTO
					AND EXITO is null
					and TOKEN IS NULL";
					$result_estado_pendiente = $db->build_results($sql_estado_pendiente);
					
					if(count($result_estado_pendiente)==1){
						echo $IDENTIFICADOR.'--'.$FECHA_VENTA.'--'.$MONTO.' PENDIENTE <br>';
						$string_pendiente.= ','.$result_wp[0]['COD_WP_TRANSACCION'].'<br>';
					}
					else{
						$sql_estado_pagado = "SELECT COD_WP_TRANSACCION
								,TO_CHAR(FECHA,'DD/MM/YYYY') FECHA
								,MONTO
								,COD_DEUDOR
								,TOKEN
						FROM WP_TRANSACCION
						WHERE TIPO_PAGO_WEB = 'PAGO_SEGIC'
						AND TO_CHAR(FECHA,'DD/MM/YYYY') = '$FECHA_VENTA'
						AND MONTO = $MONTO
						AND EXITO = 'S'
						AND TOKEN is NULL";
						$result_estado_pagado = $db->build_results($sql_estado_pagado);
						
						if(count($result_estado_pagado)>1){
							echo $IDENTIFICADOR.'--'.$FECHA_VENTA.'--'.$MONTO.' *REVISAR* <br>';
						}
						else if(count($result_estado_pagado)==1){
							echo $IDENTIFICADOR.'--'.$FECHA_VENTA.'--'.$MONTO.' PAGADO SIN TOKEN <br>';
						}
						else{
							echo $IDENTIFICADOR.'--'.$FECHA_VENTA.'--'.$MONTO.' **NO ENCONTRADO** <br>';
							echo '<br>';
						}
					}
				}
			}
		}
		else{
			echo 'NO ENCONTRADO '.$IDENTIFICADOR.'<br>';
			echo '<br>';
		}
    }
}

echo $string_pendiente;
?>