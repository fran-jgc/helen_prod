<?php
ini_set('display_errors', 'ON');
include("../../../appl.ini");
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

session::set('K_ROOT_URL', K_ROOT_URL);
session::set('K_ROOT_DIR', K_ROOT_DIR);

function get_valor_moneda($fecha, $fecha_termino, $serie){
    
    $user = '171884721';
    $password='QXgQoRdfAesN';
    $frequencyCode ='DAILY';
    
    $wsdl="https://si3.bcentral.cl/sietews/sietews.asmx?wsdl";
    
    $client = new soapclient($wsdl);
    $params = new stdClass;
    $params->user = $user;
    $params->password = $password;
    $params->frequencyCode = $frequencyCode;
    $result = $client->SearchSeries($params)->SearchSeriesResult;

    
    
    $seriesIds = array ($serie);
    $firstDate = $fecha;
    $lastDate = $fecha_termino;

    $client = new soapclient($wsdl);
    $params = new stdClass;
    $params->user = $user;
    $params->password = $password;
    $params->firstDate = $firstDate;
    $params->lastDate = $lastDate;
    $params->seriesIds = $seriesIds;

    $result = $client->GetSeries($params)->GetSeriesResult;

     
   
   
     return $fameSeries->obs->value;

    }

function get_rango_valor_moneda($fecha_inicio, $fecha_termino, $serie){
    
   /* $user = '77753520K';
    $password='1e6mI9wS';*/
    
    
    $user = '171884721';
    $password='QXgQoRdfAesN';
    $frequencyCode ='DAILY';

    $wsdl="https://si3.bcentral.cl/sietews/sietews.asmx?wsdl";

    $client = new soapclient($wsdl);
    $params = new stdClass;
    $params->user = $user;
    $params->password = $password;
    $params->frequencyCode = $frequencyCode;
    $result = $client->SearchSeries($params)->SearchSeriesResult;
    
    $seriesIds = array ($serie);    
    $firstDate = $fecha_inicio;
    $lastDate = $fecha_termino;
    
    $client = new soapclient($wsdl);
    $params = new stdClass;
    $params->user = $user;
    $params->password = $password;
    $params->firstDate = $firstDate;
    $params->lastDate = $lastDate;
    $params->seriesIds = $seriesIds;

    $result = $client->GetSeries($params)->GetSeriesResult;

    $fameSeries =$result->Series->fameSeries;


    
    
    return $fameSeries;
}

function insert_valor_moneda($serie,$cod_moneda){
    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    $b = new base();
    $cod_moneda_utm = 3;
    
    $sql_dia_actual = "select to_char(sysdate, 'dd') MES from DUAL";
    $result_dia = $db->build_results($sql_dia_actual);
    $dia_actual = $result_dia[0]['MES'];
    
    $mes_i = $db->current_month();
    $mes_f = $mes_i;
    $ano_i = $db->current_year();
    $ano_f = $ano_i;
    if($dia_actual >= 10){
        if($mes_i == 12){
            $mes_f = 2;
            $ano_f+=1;
        }
        else{
            $mes_f+=2;
        }
    }
    else if($dia_actual < 10){
        if($mes_i == 12){
            $mes_f = 1;
            $ano_f+=1;
        }
        else{
            $mes_f+=1;
        }
    }
    
    $long_mes_i = strlen($mes_i);
    if($long_mes_i == 1)
        $mes_i = '0'.$mes_i;
        $long_mes_f = strlen($mes_f);
        if($long_mes_f == 1)
            $mes_f = '0'.$mes_f;
            
            $f_inicio = $ano_i.'-'.$mes_i.'-'.$dia_actual;
            $f_final = $ano_f.'-'.$mes_f.'-09';
            
            $fameSeries = get_rango_valor_moneda("$f_inicio","$f_final",$serie);
            
            print_r($fameSeries);
            echo '<br>';
            echo $serie;


            if(is_array($fameSeries)!= 1){
                $fameSeries = array($fameSeries);
                foreach ($fameSeries as $serieFame)
                {
                    
                    /*se debe validar antes de comparar la respesta del arreglo obs
                     * para UF trae un arreglo, pero para UTM que es solo un resultado trae un objeto tipo stdClass*/
                    if (array_key_exists('obs', $serieFame)) {
                        
                        //si arraykey_exists = 0, quiere decir que viene un arreglo de varias dimenciones
                        if (array_key_exists('0', $serieFame->obs)) {
                            foreach ($serieFame->obs as $obs)
                            {
                                if(trim($obs->value) !='NAN'){
                                    $sql_v = "select COUNT(*) AS CANT
        								  from VALOR_MONEDA
        								  where COD_MONEDA = $cod_moneda and
        								  to_char(FECHA_VALOR_MONEDA, 'dd-mm-yyyy') = '$obs->indexDateString'";
                                    
                                    $result_v = $db->build_results($sql_v);
                                    $cant = $result_v[0]['CANT'];
                                    if($cant == 0){
                                        $sp = 'spu_ingreso_valor_moneda';
                                        if($cod_moneda==$cod_moneda_utm){
                                            $array_fecha = explode("-", $obs->indexDateString);
                                            $mes =  $array_fecha[1];
                                            $anio = $array_fecha[2];
                                            $cantidadDias = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
                                            
                                            for ($i = 1; $i <= $cantidadDias; $i++) {
                                                if(strlen($i)==1){
                                                    $FECHA = "0$i/$mes/$anio";
                                                }else{
                                                    $FECHA = "$i/$mes/$anio";
                                                }
                                                $FECHA = $b->str2date($FECHA);
                                                $param ="$cod_moneda,$obs->value,$FECHA";
                                                
//                                                 echo $sp.' '.$param.'<br>';
                                                $db->BEGIN_TRANSACTION();
                                                if (!$db->EXECUTE_SP($sp,$param))
                                                {
                                                    $error_sp = $db->GET_ERROR();
                                                    $db->ROLLBACK_TRANSACTION();
                                                    break;
                                                }
                                                else{
                                                    $db->COMMIT_TRANSACTION();
                                                }
                                            }
                                        }else{
                                            $FECHA = str_replace("-","/",$obs->indexDateString);
                                            $FECHA = $b->str2date($FECHA);
                                            $param ="$cod_moneda,$obs->value,$FECHA";
//                                             echo 'a2//'.$sp.' '.$param.'<br>';
                                            $db->BEGIN_TRANSACTION();
                                            if (!$db->EXECUTE_SP($sp,$param))
                                            {
                                                $error_sp = $db->GET_ERROR();
                                                $db->ROLLBACK_TRANSACTION();
                                                break;
                                            }
                                            else{
                                                $db->COMMIT_TRANSACTION();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        else{
                            if(trim($serieFame->obs->value) !='NAN'){
                                
                                $fecha_busqueda = $serieFame->obs->indexDateString;
                                $sql_v = "select COUNT(*) AS CANT
        								  from VALOR_MONEDA
        								  where COD_MONEDA = $cod_moneda and
        								  to_char(FECHA_VALOR_MONEDA, 'dd-mm-yyyy') = '$fecha_busqueda'";
                              
                                $result_v = $db->build_results($sql_v);
                                $cant = $result_v[0]['CANT'];
                                if($cant == 0){
                                    $sp = 'spu_ingreso_valor_moneda';
                                    if($cod_moneda==$cod_moneda_utm){
                                        $array_fecha = explode("-", $serieFame->obs->indexDateString);
                                        $mes =  $array_fecha[1];
                                        $anio = $array_fecha[2];
                                        $cantidadDias = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
                                        for ($i = 1; $i <= $cantidadDias; $i++) {
                                            if(strlen($i)==1){
                                                $FECHA = "0$i/$mes/$anio";
                                            }else{
                                                $FECHA = "$i/$mes/$anio";
                                            }
                                            $FECHA = $b->str2date($FECHA);
                                            $value = $serieFame->obs->value;
                                            $param ="$cod_moneda,$value,$FECHA";
//                                             echo '3//'.$sp.' '.$param.'<br>';
                                            $db->BEGIN_TRANSACTION();
                                            if (!$db->EXECUTE_SP($sp,$param))
                                            {
                                                $error_sp = $db->GET_ERROR();
                                                $db->ROLLBACK_TRANSACTION();
                                                break;
                                            }
                                            else{
                                                $db->COMMIT_TRANSACTION();
                                            }
                                        }
                                    }else{
                                        $FECHA = str_replace("-","/",$fecha_busqueda);
                                        $FECHA = $b->str2date($FECHA);
                                        $value = $serieFame->obs->value;
                                        $param ="$cod_moneda,$value,$FECHA";
//                                         echo '3b//'.$sp.' '.$param.'<br>';
                                        $db->BEGIN_TRANSACTION();
                                        if (!$db->EXECUTE_SP($sp,$param))
                                        {
                                            $error_sp = $db->GET_ERROR();
                                            $db->ROLLBACK_TRANSACTION();
                                            break;
                                        }
                                        else{
                                            $db->COMMIT_TRANSACTION();
                                        }
                                    }
                                }
                            }
                        }
                        
                        
                    }
                    
                }
            }
}
//UF
$serie = 'F073.UFF.PRE.Z.D';
$cod_moneda = 2;
insert_valor_moneda($serie,$cod_moneda);

//utm
$serie = 'F073.UTR.PRE.Z.M';
$cod_moneda = 3;
insert_valor_moneda($serie,$cod_moneda);

//dolar
$serie = 'F073.TCO.PRE.Z.D';
$cod_moneda = 4;
insert_valor_moneda($serie,$cod_moneda);

//euro
$serie = 'F072.CLP.EUR.N.O.D';
$cod_moneda = 5;
insert_valor_moneda($serie,$cod_moneda);

?>