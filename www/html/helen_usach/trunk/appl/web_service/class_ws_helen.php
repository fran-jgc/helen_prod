<?php
ini_set('display_errors', 'On');
//desarrollo
define ("urlCliente",'http://sishelen.usach.cl/new_helen_pro/helen/trunk/appl/web_service/ws_server_helen.wsdl');

class wakeUp_helen {
    
	function inicio_repro($ve_cod_deudor) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_inicio_repro", array('ve_cod_deudor' => "$ve_cod_deudor"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function inicio_repro_2($ve_cod_deudor) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_inicio_repro_2", array('ve_cod_deudor' => "$ve_cod_deudor"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function inicio_repro_3($ve_cod_deudor) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_inicio_repro_3", array('ve_cod_deudor' => "$ve_cod_deudor"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function inicio_repro_4($ve_cod_deudor) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_inicio_repro_4", array('ve_cod_deudor' => "$ve_cod_deudor"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function detalle_deuda($ve_cod_deuda) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_detalle_deuda", array('ve_cod_deuda' => "$ve_cod_deuda"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function datos_direccion_p($ve_cod_deudor) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_datos_direccion_p", array('ve_cod_deudor' => "$ve_cod_deudor"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function datos_inicio($ve_cod) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_datos_inicio", array('ve_cod' => "$ve_cod"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function actualiza_direccion($ve_cod_direccion,$cod_deudor,$tipo_direccion,$direccion,$cod_comuna,$cod_ciudad,$cod_pais,$email,$telefono,$celular) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_actualiza_direccion", array('ve_cod_direccion' => "$ve_cod_direccion"
        															,'ve_cod_deudor' => "$cod_deudor"
        															,'ve_tipo_direccion' => "$tipo_direccion"
        															,'ve_direccion' => "$direccion"
        															,'ve_cod_comuna' => "$cod_comuna"
        															,'ve_cod_ciudad' => "$cod_ciudad"
        															,'ve_cod_pais' => "$cod_pais"
        															,'ve_email' => "$email"
        															,'ve_telefono' => "$telefono"
        															,'ve_celular' => "$celular"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function datos_direccion_c($ve_cod_deudor) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_datos_direccion_c", array('ve_cod_deudor' => "$ve_cod_deudor"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function datos_webpay_cupon($tbk_rut) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_datos_webpay_cupon", array('ve_rut' => "$tbk_rut"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function datos_comuna($ve_cod_ciudad) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_datos_comuna", array('ve_cod_ciudad' => "$ve_cod_ciudad"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function datos_region($ve_cod_pais) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_datos_region", array('ve_cod_pais' => "$ve_cod_pais"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function cupon_pago_1($ve_cod_deudor) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_cupon_pago_1", array('ve_cod_deudor' => "$ve_cod_deudor"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function cupon_pago_cuotas($ve_cod_deudor) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_cupon_pago_cuotas", array('ve_cod_deudor' => "$ve_cod_deudor"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function cambio_clave($cod_deudor,$clave_nueva) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_cambio_clave", array('ve_cod_deudor' => "$cod_deudor",'ve_clave_nueva' => "$clave_nueva"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function cupon_pago_boleta($ve_cod_plan_pago) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_cupon_pago_boleta", array('ve_cod_plan_pago' => "$ve_cod_plan_pago"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function datos_ciudad($ve_cod_region) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_datos_ciudad", array('ve_cod_region' => "$ve_cod_region"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function init_transaccion($wp_transaccion,$token) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_init_transaccion", array('ve_cod_wp_transaction' => "$wp_transaccion",'ve_token' => "$token"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function pago_confirmacion($token) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_pago_confirmacion", array('ve_token' => "$token"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function datos_exito($cod_wp_transaccion) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_datos_exito", array('ve_cod_transaccion' => "$cod_wp_transaccion"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function pago_transaccion($operacion
							,$orden_compra
							,$sessionId
							,$numeroTarjeta
							,$expiracion_tarjeta
							,$fecha_autorizacion
							,$fecha_hora_autorizacion
							,$resultado_autenticacion
							,$urlRedirection
							,$codigo_autorizacion
							,$tipo_transaccion
							,$respuesta_pago
							,$monto_pago
							,$null
							,$cantidad_cuota
							,$codigo_comercio
							,$orden_compra_tienda
							,$token) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_pago_transaccion", array('ve_operacion' => "$operacion"
        														,'ve_orden_compra' => "$orden_compra"
        														,'ve_sessionId' => "$sessionId"
        														,'ve_numeroTarjeta' => "$numeroTarjeta"
        														,'ve_expiracion_tarjeta' => "$expiracion_tarjeta"
        														,'ve_fecha_autorizacion' => "$fecha_autorizacion"
        														,'ve_fecha_hora_autorizacion' => "$fecha_hora_autorizacion"
        														,'ve_resultado_autenticacion' => "$resultado_autenticacion"
        														,'ve_urlRedirection' => "$urlRedirection"
        														,'ve_codigo_autorizacion' => "$codigo_autorizacion"
        														,'ve_tipo_transaccion' => "$tipo_transaccion"
        														,'ve_respuesta_pago' => "$respuesta_pago"
        														,'ve_monto_pago' => "$monto_pago"
        														,'ve_null' => "$null"
        														,'ve_cantidad_cuota' => "$cantidad_cuota"
        														,'ve_codigo_comercio' => "$codigo_comercio"
        														,'ve_orden_compra_tienda' => "$orden_compra_tienda"
        														,'ve_token' => "$token"));
        														
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function bitacora_transaccion($wp_transaccion,$fase,$msg) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_bitacora_transaccion", array('ve_cod_wp_transaction' => "$wp_transaccion",'ve_fase' => "$fase",'ve_msg' => "$msg"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function validationResult($wp_transaction) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_validationResult", array('ve_cod_wp_transaction' => "$wp_transaction"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function crea_sub_boleta($tipo_pago,$cod_deudor,$cod_sub_boletas,$cod_plan_pago,$monto_capital,$monto_interes,$monto_multa,$monto_moneda,$monto_pesos,$cod_tipo_descuento,$dscto_utm,$dscto_pesos,$tipo_pago_web) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_crea_sub_boleta", array('ve_tipo_pago' => "$tipo_pago",'ve_cod_deudor' => $cod_deudor,'ve_cod_sub_boletas' => $cod_sub_boletas,'ve_cod_plan_pago' => $cod_plan_pago,'ve_monto_capital' => $monto_capital,'ve_monto_interes' => $monto_interes,'ve_monto_multa' => $monto_multa,'ve_monto_moneda' => $monto_moneda,'ve_monto_pesos' => $monto_pesos,'ve_cod_tipo_descuento' => $cod_tipo_descuento,'ve_dscto_utm' => $dscto_utm,'ve_dscto_pesos' => $dscto_pesos,'ve_tipo_pago_web' => $tipo_pago_web));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function ajax_plan_pago_1($ve_cod_deuda) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_ajax_plan_pago_1", array('ve_cod_deuda' => "$ve_cod_deuda"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function ajax_plan_pago_2($ve_cod_deuda) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_ajax_plan_pago_2", array('ve_cod_deuda' => "$ve_cod_deuda"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function ajax_plan_pago_3($ve_cod_deuda) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_ajax_plan_pago_3", array('ve_cod_deuda' => "$ve_cod_deuda"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function ajax_plan_pago_4($ve_cod_deuda, $ve_cod_plan_pago) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_ajax_plan_pago_4", array('ve_cod_deuda' => "$ve_cod_deuda",'ve_cod_plan_pago' => "$ve_cod_plan_pago"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function cant_cruzado($ve_cod_deuda) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_cant_cruzado", array('ve_cod_deuda' => "$ve_cod_deuda"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function pago_online_1($ve_cod_plan_pago) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_pago_online_1", array('ve_cod_plan_pago' => "$ve_cod_plan_pago"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function pago_online_2($cod_deuda, $cod_plan_pago, $cod_tipo_descuento, $monto_abono_pesos) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_pago_online_2", array('ve_cod_deuda' => "$cod_deuda",'ve_cod_plan_pago' => "$cod_plan_pago",'ve_cod_tipo_descuento' => "$cod_tipo_descuento",'ve_monto_abono_pesos' => "$monto_abono_pesos"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function pago_online_3($cod_deuda) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_pago_online_3", array('ve_cod_deuda' => "$cod_deuda"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function pago_online_4($cod_deudor) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_pago_online_4", array('ve_cod_deudor' => "$cod_deudor"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
    function listado_deudas($ve_cod_deudor) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_obtiene_deuda", array('ve_rut' => "$ve_cod_deudor"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function datos_web_pay($ve_cod_deudor) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas cxxxxxxzzzxzzzzzz
        $result = $cliente->call("ws_datos_web_pay", array('ve_cod_deudor' => "$ve_cod_deudor"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
    
	function obtiene_plan_pago($ve_cod_deuda) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        $result = $cliente->call("ws_obtiene_plan_pago", array('ve_cod_deuda' => "$ve_cod_deuda"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                
                return unserialize(base64_decode($result));
				
			}
        }
    }
    
	function obtiene_boleta($ve_cod_plan_pago) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        $result = $cliente->call("ws_obtiene_boleta", array('ve_cod_plan_pago' => "$ve_cod_plan_pago"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                
                return unserialize(base64_decode($result));
				
			}
        }
    }
    
	function obtiene_ult_boleta($ve_cod_plan_pago) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        $result = $cliente->call("ws_obtiene_boleta", array('ve_cod_plan_pago' => "$ve_cod_plan_pago"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                
                return unserialize(base64_decode($result));
				
			}
        }
    }
    
	function valida_rut($ve_rut,$ve_dig_verif) {
        $resp = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
		$result = $cliente->call("ws_valida_deudor", array('ve_rut' => "$ve_rut",'ve_dig_verif' => "$ve_dig_verif"));
        
		if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
            }
        }
    }
    
    function get_datos_deudor($ve_rut) {
        $resp = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        $result = $cliente->call("ws_datos_deudor", array('ve_rut' => "$ve_rut"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
            }
        }//cierre deudas
    }
    
	function get_datos_deuda($ve_cod_deuor) {
        $resp = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        $result = $cliente->call("ws_datos_deuda", array('ve_rut' => "$ve_cod_deuor"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
            }
        }//cierre deudas
    }
    
	function crea_transaccion($ve_operacion,$ve_monto_abono,$ve_ip,$ve_browser,$ve_so) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas
        $result = $cliente->call("ws_crea_transaccion", array('ve_operacion' => "$ve_operacion",'ve_monto_abono' => "$ve_monto_abono",'ve_ip' => "$ve_ip",'ve_browser' => "$ve_browser",'ve_so' => "$ve_so"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
	
	function deuda_transaccion($ve_operacion,$ve_cod_wp_transaccion,$ve_nro_deuda,$ve_monto_asignado,$ve_tipo_abono) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        
        //WS_Deudas
        $result = $cliente->call("ws_deuda_transaccion", array('ve_operacion' => "$ve_operacion",'ve_cod_wp_transaccion' => "$ve_cod_wp_transaccion",'ve_nro_deuda' => "$ve_nro_deuda",'ve_monto_asignado' => "$ve_monto_asignado",'ve_tipo_abono' => "$ve_tipo_abono"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
				
			}
        }//cierre deudas
    }
	
	function pago_abono($orden_compra) {
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        $result = $cliente->call("ws_pago_abono", array('orden_compra' => "$orden_compra"));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
            }
        }
    }
    
	function respuesta_transaccion($ve_operacion
								   ,$orden_compra
								   ,$sessionId
								   ,$numeroTarjeta
								   ,$expiracion_tarjeta
								   ,$fecha_autorizacion
								   ,$fecha_hora_autorizacion
								   ,$resultado_autenticacion
								   ,$urlRedirection 
								   ,$codigo_autorizacion 
								   ,$tipo_transaccion 
								   ,$respuesta_pago
								   ,$monto_pago
								   ,$valor_cuota
								   ,$cantidad_cuota
								   ,$codigo_comercio
								   ,$orden_compra_tienda
								   ,$token){
        $xml = "";
        include_once(dirname(__FILE__)."/nusoap/lib/nusoap.php");
        $cliente = new nusoap_client(urlCliente,true);
        
        $cliente->setCredentials("CLAVE_AL", "basic");
        
        if ($cliente->getError()) {
            print_r($cliente->getError());
            
            return false;
        }
        $result = $cliente->call("ws_wp_pago_transaccion_ws", array('ve_operacion' => "$ve_operacion"
																	,'ve_orden_compra' => "$orden_compra"
																	,'ve_sessionId' => "$sessionId"
																	,'ve_numeroTarjeta' => "$numeroTarjeta"
																	,'ve_expiracion_tarjeta' => "$expiracion_tarjeta"
																	,'ve_fecha_autorizacion' => "$fecha_autorizacion"
																	,'ve_fecha_hora_autorizacion' => "$fecha_hora_autorizacion"
																	,'ve_resultado_autenticacion' => "$resultado_autenticacion"
																	,'ve_urlRedirection' => "$urlRedirection"
																	,'ve_codigo_autorizacion' => "$codigo_autorizacion"
																	,'ve_tipo_transaccion' => "$tipo_transaccion"
																	,'ve_respuesta_pago' => "$respuesta_pago"
																	,'ve_monto_pago' => "$monto_pago"
																	,'ve_valor_cuota' => "$valor_cuota"
																	,'ve_cantidad_cuota' => "$cantidad_cuota"
																	,'ve_codigo_comercio' => "$codigo_comercio"
																	,'ve_orden_compra_tienda' => "$orden_compra_tienda"
																	,'ve_token' => "$token"
        ));
        
        if ($cliente->fault) {
            print_r($result);
            return false;
        }
        else {
            if ($cliente->getError()) {
                print_r($cliente->getError());
                return false;
            }
            else {
                
                //$xml= simplexml_load_string(base64_decode(utf8_decode($result)));
                
                return unserialize(base64_decode($result));
            }
        }
    }
}
?>