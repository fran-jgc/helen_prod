<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");
        $RUT	= $_REQUEST['rut'];
        $cod_estado_deuda_cancelada = 4;
        $cod_pagare_vigente = 1;
        $cod_pagare_proc_dev = 6;
        $cod_cartera = 6;
        $count_result_dv=0;
        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

        $sql = "SELECT DE.COD_DEUDA
                        ,ED.NOM_ESTADO_DEUDA
                        ,DEU.RUT
                        ,DE.COD_TIPO_DEUDA
                FROM  DEUDA DE,ESTADO_DEUDA ED, DEUDOR DEU 
                WHERE ED.COD_ESTADO_DEUDA=DE.COD_ESTADO_DEUDA
                AND DEU.COD_DEUDOR = DE.COD_DEUDOR
                AND DE.COD_ESTADO_DEUDA = $cod_estado_deuda_cancelada
                AND COD_CARTERA IN (6,26) --$cod_cartera
                AND DE.COD_DEUDA NOT IN(SELECT COD_DEUDA FROM DEVOLUCION_PAGARE)
                AND DEU.RUT =  $RUT
                AND 0<(SELECT  COUNT(*) FROM PAGARE P 
                        WHERE  P.COD_ESTADO_PAGARE  IN($cod_pagare_vigente,$cod_pagare_proc_dev) 
                        AND P.COD_DEUDA = DE.COD_DEUDA)
                GROUP BY DE.COD_DEUDA, ED.NOM_ESTADO_DEUDA, DEU.RUT, DE.COD_TIPO_DEUDA";
		$result = $db->build_results($sql);

        $count_result=count($result);
        if($count_result==0){
                $sql_deuda_dev = "SELECT COD_DEUDA FROM DEVOLUCION_PAGARE
                                WHERE COD_ESTADO_DEVOLUCION_PAGARE IS NULL
                                AND COD_DEUDA IN(SELECT COD_DEUDA FROM DEUDA D,DEUDOR DEU 
                                                WHERE D.COD_DEUDOR=DEU.COD_DEUDOR 
                                                AND DEU.RUT=$RUT)";
                $result_dv = $db->build_results($sql_deuda_dev);
                $count_result_dv = count($result_dv);
        }
        
        if($count_result_dv>0){
                print 'DEUDAS_SIN_ESTADO_DEV_PAG';
        }elseif($count_result_dv == 0 && $count_result==0){
                print 'SIN_DEUDAS_CANCELADAS';
        }else{
                print urlencode(json_encode($result));  
        }
        


?>