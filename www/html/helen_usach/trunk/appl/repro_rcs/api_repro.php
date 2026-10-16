<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../appl.ini");

session::set('K_ROOT_URL', K_ROOT_URL);
session::set('K_ROOT_DIR', K_ROOT_DIR);
session::set('K_CLIENTE', K_CLIENTE);
session::set('K_APPL', K_APPL);

// La commonlib usa estos datos
session::set("COD_USUARIO","2");
session::set("NOM_USUARIO","USUARIO WEB");

$a = new api_repro();
$a->datos_repro();

class api_repro  {
    function datos_repro() {
        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        
        $sql = "select  r.cod_repro_rcs
                        ,r.fecha_repro_rcs
                        ,D.RUT
    				  ,D.NOMBRE
                      ,D.APELLIDO_PATERNO
                      , D.APELLIDO_MATERNO 
                      ,DIRECCION
    				  ,NVL((SELECT NOM_COMUNA FROM COMUNA CO WHERE CO.COD_COMUNA = DI.COD_COMUNA),'SIN_INFORMACION') NOM_COMUNA
    				  ,NVL((SELECT NOM_CIUDAD FROM CIUDAD CI WHERE CI.COD_CIUDAD = DI.COD_CIUDAD),'SIN_INFORMACION') NOM_CIUDAD
				from REPRO_RCS R, DEUDOR D ,DIRECCION DI
				where D.COD_DEUDOR = R.COD_DEUDOR
                AND D.COD_DEUDOR = DI.COD_DEUDOR
                and di.cod_direccion = f_get_direccion_deudor (D.COD_DEUDOR, 'P')
                and extract(YEAR from r.fecha_repro_rcs) = 2022
                ORDER BY R.cod_repro_rcs desc";
               

        $result = $db->build_results($sql);
       // print_r( $result[0]);
        $datos= json_encode($result);
      $this->api($datos);

    }
    function api($datos) {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://simulacion.helen.cl/wan/simulacion/simulacion_produccion/simulacion_RCS/trunk/appl/class_api_log_deudor.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$datos,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Cookie: PHPSESSID=ma4e24375scr9i7v101colfd17'
            ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
		
        echo $response;
	}
}


?>