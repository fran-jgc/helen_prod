<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

        $COD_DEUDOR	= $_REQUEST['COD_DEUDOR'];

        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

        $sql = "SELECT	COD_DIRECCION 			COD_DIRECCION
                        , DIRECCION 	
                        , CM.NOM_COMUNA 	
                        , C.NOM_CIUDAD
                        , R.NOM_REGION
                FROM 	DIRECCION   DIR
                        ,CIUDAD         C
                        ,COMUNA CM
                        ,REGION R
                WHERE 	COD_DEUDOR = $COD_DEUDOR
                        AND DIR.COD_CIUDAD= C.COD_CIUDAD (+)
                        AND DIR.COD_COMUNA= CM.COD_COMUNA (+)
                        AND C.COD_REGION= R.COD_REGION (+)
                        AND COD_DIRECCION = F_GET_DIRECCION_DEUDOR (COD_DEUDOR, 'P')";
		$result = $db->build_results($sql);
		
		$DIRECCION = $result[0]['DIRECCION'];
        $NOM_COMUNA  = $result[0]['NOM_COMUNA'];
        $NOM_CIUDAD  = $result[0]['NOM_CIUDAD'];
        $NOM_REGION  = $result[0]['NOM_REGION'];

        print urlencode( utf8_decode($DIRECCION.", ".$NOM_COMUNA.", ".$NOM_CIUDAD.", ".$NOM_REGION));


?>