<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

        $COD_DEVOLUCION_PAGARE	= $_REQUEST['COD_DEV_PAGARE'];

        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

        $sql = "SELECT  COALESCE (COD_ESTADO_DEVOLUCION_PAGARE , 0) COD_ESTADO_DEV_PAGARE
						, COALESCE (COD_ESTADO_ENVIO_DOMICILIO , 0)  COD_ESTADO_ENVIO_DOM
                FROM 	DEVOLUCION_PAGARE
				WHERE COD_DEVOLUCION_PAGARE = $COD_DEVOLUCION_PAGARE";
		$result = $db->build_results($sql);
		
		$COD_ESTADO_DEV_PAGARE = $result[0]['COD_ESTADO_DEV_PAGARE'];
        $COD_ESTADO_ENVIO_DOM  = $result[0]['COD_ESTADO_ENVIO_DOM'];

        print urlencode($COD_ESTADO_DEV_PAGARE."|".	$COD_ESTADO_ENVIO_DOM);


?>