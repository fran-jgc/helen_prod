<?php
ini_set('display_errors', 'ON');
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once ("../../appl.ini");
require_once ("funciones.php");


class w_webpay_cupon {
    function __construct() {
        
        session::set('K_ROOT_URL', K_ROOT_URL);
        session::set('K_ROOT_DIR', K_ROOT_DIR);
        
        $ID_TRANSBANK = $_GET['RESULTADO'] ?? $_POST['RESULTADO'] ?? null;
        
        $ID_TRANSBANK = dencriptar_url($ID_TRANSBANK,'Encript_Code_Compra');
        
        $t = new Template_appl("webpay_cupon.html");
        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        
        $cod_deudor = session::get('COD_DEUDOR');
        
        $sql= "SELECT count(*) COUNT
				FROM WP_TRANSACCION WP, WP_PAGO_TRANSACCION_WS WPT
				WHERE WP.COD_WP_TRANSACCION='$ID_TRANSBANK'
				AND WP.COD_WP_TRANSACCION = WPT.COD_WP_TRANSACCION";
        
        $db->query($sql);
        $result_transaccion = $db->get_row();
        $count_respuesta = $result_transaccion['COUNT'];
        
        $codigo_respuesta = '';
        
        if($count_respuesta > 0){
            $sql = "SELECT CODIGO_RESPUESTA
				FROM WP_TRANSACCION WP, WP_PAGO_TRANSACCION_WS WPT
				WHERE WP.COD_WP_TRANSACCION='$ID_TRANSBANK'
				AND WP.COD_WP_TRANSACCION = WPT.COD_WP_TRANSACCION";
            
            $db->query($sql);
            $result_transaccion = $db->get_row();
            $codigo_respuesta = $result_transaccion['CODIGO_RESPUESTA'];
        }
        else {
            $codigo_respuesta = '-1';
        }
        
        if ($codigo_respuesta == '0') { //ok
            
            $MENSAJE_TRANSACCION = "";
            
            $sql_pago = "SELECT
							CODIGO_AUTORIZACION TBK_CODIGO_AUTORIZACION
							,NUMERO_TARJETA TBK_FINAL_NUMERO_TARJETA
							,TIPO_PAGO TBK_TIPO_PAGO
							,CANTIDAD_CUOTA TBK_NUMERO_CUOTAS
						FROM WP_PAGO_TRANSACCION_WS
						WHERE COD_WP_TRANSACCION =$ID_TRANSBANK";
        
            $db->query($sql_pago);
            $result = $db->get_row();
            
            $cod_autorizacion 	= $result['TBK_CODIGO_AUTORIZACION'];
            $tarjeta_final 		= $result['TBK_FINAL_NUMERO_TARJETA'];
            $nro_cuota 			= $result['TBK_NUMERO_CUOTAS'];
            $ve_tipo_pago 		= $result['TBK_TIPO_PAGO'];
            
            $sql_deudor	=  "SELECT DE.RUT RUT 
                                    ,DE.DIG_VERIF DIG_VERIF 
                                    ,DE.NOMBRE||' '|| DE.APELLIDO_PATERNO ||' '|| DE.APELLIDO_MATERNO NOMBRE 
                                    ,B.MONTO_PESOS 
                                    ,WT.COD_WP_TRANSACCION 
                                    ,To_Char(WT.FECHA, 'dd/mm/yyyy') FECHA
                                    ,To_Char(WT.FECHA, 'HH24:MI:SS') HORA 
                                    ,TD.NOM_TIPO_DEUDA 
                                    ,TD.COD_TIPO_DEUDA 
                                    ,WT.MONTO 
                                    ,WT.NRO_CUOTA 
                             FROM WP_TRANSACCION WT, WP_SUB_BOLETA WS, SUB_BOLETA S, BOLETA B, DEUDOR DE, DEUDA D, TIPO_DEUDA TD 
                             WHERE WT.COD_WP_TRANSACCION =$ID_TRANSBANK 
                             AND WS.COD_WP_TRANSACCION = WT.COD_WP_TRANSACCION 
                             AND S.COD_SUB_BOLETA = WS.COD_SUB_BOLETA 
                             AND B.COD_BOLETA = S.COD_BOLETA 
                             AND WT.COD_DEUDOR = DE.COD_DEUDOR 
                             AND D.COD_DEUDA = B.COD_DEUDA 
                             AND TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA";
            
          
            $db->query($sql_deudor);
            $result = $db->get_row();
            
            $fecha_transaccion = $result['FECHA'];
            $hora_transaccion = $result['HORA'];
            $tbk_nro_cuota = $result['NRO_CUOTA'];
            $tbk_tipo_deuda = $result['NOM_TIPO_DEUDA'];
            $tbk_cod_tipo_deuda = $result['COD_TIPO_DEUDA'];
            $nombre= $result['NOMBRE'];
            if($tbk_cod_tipo_deuda == 'CS'){
                $tbk_tipo_deuda = 'Cr&eacute;dito Solidario';
            }
            else if($tbk_cod_tipo_deuda == 'CSI'){
                $tbk_tipo_deuda = 'Cr&eacute;dito Solidario Institucional';
            }
            else if($tbk_cod_tipo_deuda == 'CI'){
                $tbk_tipo_deuda = 'Cr&eacute;dito Institucional';
            }
            else if($tbk_cod_tipo_deuda == 'CY'){
                $tbk_tipo_deuda = 'Cr&eacute;dito Institucional';
            }
            else if($tbk_cod_tipo_deuda == 'CF'){
                $tbk_tipo_deuda = 'Cr&eacute;dito Fiscal';
            }
            else{
                $tbk_tipo_deuda = $result['COD_TIPO_DEUDA'];
            }
            
            $descripcion = "Pago de la Cuota N&ordm; $tbk_nro_cuota, del $tbk_tipo_deuda";
            
            $tbk_rut = number_format($result['RUT'], 0, ',', '.');
            $dig_verif = $result['DIG_VERIF'];
            $tbk_monto_pago = $result['MONTO'];
            $tbk_cod_transacion = $result['COD_WP_TRANSACCION'];
            $nom_deudor = $result['NOMBRE'];
            
            $monto_pesos = number_format($tbk_monto_pago, 0, ',', '.');
            
            if($ve_tipo_pago=='VD'){
                $tipo_pago = 'Red Compra';
                $tipo_cuotas = 'D&eacute;bito';
            }
            else if($ve_tipo_pago=='CI'){
                $tipo_pago = 'Cr&eacute;dito';
                $tipo_cuotas = 'Cuotas Comercio';
            }
            else if($ve_tipo_pago=='SI'){
                $tipo_pago = 'Cr&eacute;dito';
                $tipo_cuotas = 'Sin inter&eacute;s';
            }
            else if($ve_tipo_pago=='VC'){
                $tipo_pago = 'Cr&eacute;dito';
                $tipo_cuotas = 'Cuotas normales';
            }
            else if($ve_tipo_pago=='VN'){
                $tipo_pago = 'Cr&eacute;dito';
                $tipo_cuotas = 'Sin Cuotas';
            }
            else {
                $tipo_pago = 'Tipo pago desconocido';
                $tipo_cuotas = 'Desconocido';
            }
            
            $sql_tbk = "select  'none' DISPLAY_FRACASO
							    ,'' DISPLAY_APROBADO
								,'Pago de la Cuota N°; $tbk_nro_cuota, del $tbk_tipo_deuda ' TITULO_TRANSACCION
							    ,'' MENSAJE_TRANSACCION
							    ,'$tbk_cod_transacion' NRO_COMPRA
							    ,'$nombre' NOM_COMPRADOR
							    ,'************$tarjeta_final' NRO_TARJETA
							    ,'$monto_pesos' MONTO_PAGO
							    ,'$fecha_transaccion'  FECHA_TRANSACCION
							    ,'$hora_transaccion' HORA_TRANSACCION
							    ,'$cod_autorizacion' CODIGO_TRANSACCION
							    ,'PAGO' TIPO_TRANSACCION
							    ,'$nro_cuota' NRO_CUOTAS
							    ,'$tipo_cuotas' TIPO_CUOTAS
							    ,'$tipo_pago' TIPO_PAGO
							    ,'$descripcion' DESCRIPCION
                           from dual";
            
            
        }else if (($codigo_respuesta == '')||($codigo_respuesta != '0')) { //fracaso
            
            $MENSAJE_TRANSACCION  = "Transacción Fracasada<br>";
            $MENSAJE_TRANSACCION .= "Las posibles causas de este rechazo son: <br><br>";
            $MENSAJE_TRANSACCION .= "- Error en el ingreso de los datos de su tarjeta de crédito (fecha y/o código de seguridad).<br>";
            $MENSAJE_TRANSACCION .= "- Su tarjeta de crédito/débito no cuenta con el cupo necesario para cancelar la compra.<br>";
            $MENSAJE_TRANSACCION .= "- Tarjeta aún no habilitada en el sistema financiero.<br>";
            $MENSAJE_TRANSACCION .= "- Si el problema persiste favor comunicarse con su Banco emisor..<br>";
            
            $fase = "WEBPAY CUPON RECHAZADO";
            $sql = "SELECT COD_DEUDOR
						,COD_WP_TRANSACCION
					FROM WP_TRANSACCION
					where COD_WP_TRANSACCION ='$ID_TRANSBANK'";
            $db->query($sql);
            $result = $db->get_row();
            
            $cod_deudor = $result['COD_DEUDOR'];
            $nro_orden_compra = $result['COD_WP_TRANSACCION'];
            
            $sql = "UPDATE WP_TRANSACCION SET EXITO = 'N' WHERE COD_WP_TRANSACCION ='$ID_TRANSBANK'";
            
            if(!$db->query($sql)){
                $sp_bitacora = 'spw_bitacora_transaccion';
                $param = "'$nro_orden_compra','$fase','Error UPDATE WP_TRANSACCION'";
                $dbc->EXECUTE_SP($sp_bitacora,$param);
                
            }
            
            //bitacora deudor
            $K_INTENTO_PAGO_WEBPAY	= 40;
            $K_USUARIO_WEB	= 2;
            $sql = "INSERT INTO BITACORA_DEUDOR VALUES('Intento pago vía webpay',getdate(),$K_INTENTO_PAGO_WEBPAY,$cod_deudor,$K_USUARIO_WEB)";
            
           // $db->query($sql);
            
            $sql_tbk = "select  '' DISPLAY_FRACASO
							    ,'none' DISPLAY_APROBADO
								,'Fondo Solidario de Crédito Universitario' TITULO_TRANSACCION
							    ,'' MENSAJE_TRANSACCION
							    ,'$tbk_cod_transacion' NRO_COMPRA
							    ,'' NOM_COMPRADOR
							    ,'' NRO_TARJETA
							    ,'' MONTO_PAGO
							    ,'' FECHA_TRANSACCION
							    ,'' HORA_TRANSACCION
							    ,'' CODIGO_TRANSACCION
							    ,'' NRO_TRANSACCION
							    ,'' TIPO_TRANSACCION
							    ,'' NRO_CUOTAS
							    ,'' TIPO_CUOTAS
                                ,'' TIPO_PAGO
                                ,'' DESCRIPCION
							    ,'' MSG_REEMBOLSO
                        from dual";
            
        }
        
        $dw_transbank = new datawindow($sql_tbk);
        
        $dw_transbank->retrieve();
        $dw_transbank->habilitar($t, false);
        
        $t->setVar("MENSAJE_TRANSACCION", $MENSAJE_TRANSACCION);
        
        print $t->toString();
    }
}
$w = new w_webpay_cupon();
?>