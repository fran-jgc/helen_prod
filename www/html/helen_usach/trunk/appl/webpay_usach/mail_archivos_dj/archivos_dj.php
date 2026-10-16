<?php 
ini_set('display_errors', 'Off');
include("funciones.php");


//session_start();

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$cod_dj = $_REQUEST['cdj'];;
$cod_dj_decrypt = base64url_decode($cod_dj);
$cod_dj_decrypt = dencriptar_url($cod_dj_decrypt,'ENCRIPTACION_DJ');


$sql =  "SELECT DEU.NOMBRE,
DEU.APELLIDO_PATERNO,
D.COD_DEUDOR
FROM DECLARACION D,DEUDOR DEU
WHERE COD_DECLARACION = $cod_dj_decrypt
AND DEU.COD_DEUDOR = D.COD_DEUDOR ";

$result_deudor = $db->build_results($sql);
$cod_deudor = $result_deudor[0]['COD_DEUDOR'];
$nombre = $result_deudor[0]['NOMBRE'].' '.$result_deudor[0]['APELLIDO_PATERNO'];
//echo session::get('cod_dj').' pagina';




$cod_pers = $cod_deudor;
    
    $sql = "SELECT COD_TIPO_ARCHIVO,
        NOM_TIPO_ARCHIVO,
        AYUDA,
        CASE CARDINALIDAD WHEN 'N'
          THEN (SELECT valor FROM parametro WHERE cod_parametro = 35)
          ELSE CARDINALIDAD
        END CARDINALIDAD
        FROM   TIPO_ARCHIVO
        ORDER BY COD_TIPO_ARCHIVO ASC";
    
    $resultado = $db->build_results($sql);
    
    $tp_cod_dj =  $resultado[0]['COD_TIPO_ARCHIVO'];
    $cardinalidad_dj =  $resultado[0]['CARDINALIDAD'];
    $ayuda_dj =  $resultado[0]['AYUDA'];
    
    $tp_f22 =  $resultado[1]['COD_TIPO_ARCHIVO'];
    $cardinalidad_f22 =  $resultado[1]['CARDINALIDAD'];
    $ayuda_f22 =  $resultado[1]['AYUDA'];
    
    $tp_ar =  $resultado[2]['COD_TIPO_ARCHIVO'];
    $cardinalidad_ar =  $resultado[2]['CARDINALIDAD'];
    $ayuda_ar =  $resultado[2]['AYUDA'];
    
    $tp_ccp =  $resultado[3]['COD_TIPO_ARCHIVO'];
    $cardinalidad_ccp =  $resultado[3]['CARDINALIDAD'];
    $ayuda_ccp =  $resultado[3]['AYUDA'];
    
    $tp_cr =  $resultado[4]['COD_TIPO_ARCHIVO'];
    $cardinalidad_cr =  $resultado[4]['CARDINALIDAD'];
    $ayuda_cr =  $resultado[4]['AYUDA'];
    
    $tp_cn =  $resultado[5]['COD_TIPO_ARCHIVO'];
    $cardinalidad_cn =  $resultado[5]['CARDINALIDAD'];
    $ayuda_cn =  $resultado[5]['AYUDA'];
    
    $tp_cm =  $resultado[6]['COD_TIPO_ARCHIVO'];
    $cardinalidad_cm =  $resultado[6]['CARDINALIDAD'];
    $ayuda_cm =  $resultado[6]['AYUDA'];
    
    $tp_otros =  $resultado[7]['COD_TIPO_ARCHIVO'];
    $cardinalidad_otros =  $resultado[7]['CARDINALIDAD'];
    $ayuda_ot =  $resultado[7]['AYUDA'];
    
    $tp_lq =  $resultado[8]['COD_TIPO_ARCHIVO'];
    $cardinalidad_lq =  $resultado[8]['CARDINALIDAD'];
    $ayuda_lq =  $resultado[8]['AYUDA'];
    
    $K_PEDIDO_POR_CORREO = 4;
    $sql_1 = "select count(*) COUNT_DJ 
    		from declaracion_archivo 
    		where cod_declaracion = $cod_dj_decrypt
    		and cod_tipo_archivo = 1
    		and cod_revisado_archivo = $K_PEDIDO_POR_CORREO";
    $result = $db->build_results($sql_1);
    $count_dj = $result[0]['COUNT_DJ'];
    
    if($count_dj > 0){ $display_dj = '';} else{ $display_dj = 'none';}
    
    $sql_2 = "select count(*) COUNT_F22 
    		from declaracion_archivo 
    		where cod_declaracion = $cod_dj_decrypt
    		and cod_tipo_archivo = 2
    		and cod_revisado_archivo = $K_PEDIDO_POR_CORREO";
    $result = $db->build_results($sql_2);
    $count_f22 = $result[0]['COUNT_F22'];
    
 	if($count_f22 > 0){ $display_f22 = '';} else{ $display_f22 = 'none';}
    
    $sql_3 = "select count(*) COUNT_RETENEDORES 
    		from declaracion_archivo 
    		where cod_declaracion = $cod_dj_decrypt
    		and cod_tipo_archivo = 3
    		and cod_revisado_archivo = $K_PEDIDO_POR_CORREO";
    $result = $db->build_results($sql_3);
    $count_retenedores = $result[0]['COUNT_RETENEDORES'];
    
 	if($count_retenedores > 0){ $display_retenedores = '';} else{ $display_retenedores = 'none';}
 	
 	$sql_4 = "select count(*) COUNT_COTIZACIONES 
    		from declaracion_archivo 
    		where cod_declaracion = $cod_dj_decrypt
    		and cod_tipo_archivo = 4
    		and cod_revisado_archivo = $K_PEDIDO_POR_CORREO";
    $result = $db->build_results($sql_4);
    $count_cotizaciones = $result[0]['COUNT_COTIZACIONES'];
    
 	if($count_cotizaciones > 0){ $display_cotizaciones = '';} else{ $display_cotizaciones = 'none';}
 	
 	$sql_5 = "select count(*) COUNT_REMUNERACIONES 
    		from declaracion_archivo 
    		where cod_declaracion = $cod_dj_decrypt
    		and cod_tipo_archivo = 5
    		and cod_revisado_archivo = $K_PEDIDO_POR_CORREO";
    $result = $db->build_results($sql_5);
    $count_remuneraciones = $result[0]['COUNT_REMUNERACIONES'];
    
 	if($count_remuneraciones > 0){ $display_remuneraciones = '';} else{ $display_remuneraciones = 'none';}
 	
    $sql_6 = "select count(*) COUNT_NACIMIENTO 
    		from declaracion_archivo 
    		where cod_declaracion = $cod_dj_decrypt
    		and cod_tipo_archivo = 6
    		and cod_revisado_archivo = $K_PEDIDO_POR_CORREO";
    $result = $db->build_results($sql_6);
    $count_nacimiento = $result[0]['COUNT_NACIMIENTO'];
    
 	if($count_nacimiento > 0){ $display_nacimiento = '';} else{ $display_nacimiento = 'none';}
    
    $sql_7 = "select count(*) COUNT_MATRIMONIO 
    		from declaracion_archivo 
    		where cod_declaracion = $cod_dj_decrypt
    		and cod_tipo_archivo = 7
    		and cod_revisado_archivo = $K_PEDIDO_POR_CORREO";
    $result = $db->build_results($sql_7);
    $count_matrimonio = $result[0]['COUNT_MATRIMONIO'];
    
 	if($count_matrimonio > 0){ $display_matrimonio = '';} else{ $display_matrimonio = 'none';}
 	
    $sql_8 = "select count(*) COUNT_OTROS
    		from declaracion_archivo 
    		where cod_declaracion = $cod_dj_decrypt
    		and cod_tipo_archivo = 8
    		and cod_revisado_archivo = $K_PEDIDO_POR_CORREO";
    $result = $db->build_results($sql_8);
    $count_otros = $result[0]['COUNT_OTROS'];
    
 	if($count_otros > 0){ $display_otros = '';} else{ $display_otros = 'none';}
   
    $sql_9 = "select count(*) COUNT_LIQUIDACIONES
    		from declaracion_archivo 
    		where cod_declaracion = $cod_dj_decrypt
    		and cod_tipo_archivo = 9
    		and cod_revisado_archivo = $K_PEDIDO_POR_CORREO";
    $result = $db->build_results($sql_9);
    $count_liquidaciones = $result[0]['COUNT_LIQUIDACIONES'];
    
 	if($count_liquidaciones > 0){ $display_liquidaciones = '';} else{ $display_liquidaciones = 'none';}
    
    $class_li_dj = '';
    $class_tab_dj = '';
    $class_li_f22 = '';
    $class_tab_f22 = '';
    $class_li_retenedores = '';
    $class_tab_retenedores = '';
    $class_li_cotizaciones = '';
    $class_tab_cotizaciones = '';
    $class_li_remuneraciones = '';
    $class_tab_remuneraciones = '';
    $class_li_nacimiento = '';
    $class_tab_nacimiento = '';
    $class_li_matrimonio = '';
    $class_tab_matrimonio = '';
    $class_li_liquidaciones = '';
    $class_tab_liquidaciones = '';
    $class_li_otros = '';
    $class_tab_otros = '';
    
    
    if($display_dj == ''){
    	$class_li_dj = 'active';
    	$class_tab_dj = 'in active';
    }
    else if($display_f22 == ''){
    	$class_li_f22 = 'active';
    	$class_tab_f22 = 'in active';
    }
    else if($display_retenedores == ''){
    	$class_li_retenedores = 'active';
    	$class_tab_retenedores = 'in active';
    }
    else if($display_cotizaciones == ''){
    	$class_li_cotizaciones = 'active';
    	$class_tab_cotizaciones = 'in active';
    }
    else if($display_remuneraciones == ''){
    	$class_li_remuneraciones = 'active';
    	$class_tab_remuneraciones = 'in active';
    }
    else if($display_nacimiento == ''){
    	$class_li_nacimiento = 'active';
    	$class_tab_nacimiento = 'in active';
    }
    else if($display_matrimonio == ''){
    	$class_li_matrimonio = 'active';
    	$class_tab_matrimonio = 'in active';
    }
    else if($display_liquidaciones == ''){
    	$class_li_liquidaciones = 'active';
    	$class_tab_liquidaciones = 'in active';
    }
    else if($display_otros == ''){
    	$class_li_otros = 'active';
    	$class_tab_otros = 'in active';
    }
    
    
    $cod_param_max_archivos=36;
    $max_peso = $db->get_parametro($cod_param_max_archivos);
    
    
    $t = new Template("archivos_dj.html");
    
    $t->setVar("cod_pers", $cod_pers);
   
    $t->setVar("DISPLAY_DJ", $display_dj);
    $t->setVar("DISPLAY_F22", $display_f22);
    $t->setVar("DISPLAY_RETENEDORES", $display_retenedores);
    $t->setVar("DISPLAY_COTIZACIONES", $display_cotizaciones);
    $t->setVar("DISPLAY_REMUNERACIONES", $display_remuneraciones);
    $t->setVar("DISPLAY_NACIMIENTO", $display_nacimiento);
    $t->setVar("DISPLAY_MATRIMONIO", $display_matrimonio);
    $t->setVar("DISPLAY_OTROS", $display_otros);
    $t->setVar("DISPLAY_LIQUIDACIONES", $display_liquidaciones);
    
    $t->setVar("class_li_dj", $class_li_dj);
    $t->setVar("class_tab_dj", $class_tab_dj);
    $t->setVar("class_li_f22", $class_li_f22);
    $t->setVar("class_tab_f22", $class_tab_f22);
    $t->setVar("class_li_retenedores", $class_li_retenedores);
    $t->setVar("class_tab_retenedores", $class_tab_retenedores);
    $t->setVar("class_li_cotizaciones", $class_li_cotizaciones);
    $t->setVar("class_tab_cotizaciones", $class_tab_cotizaciones);
    $t->setVar("class_li_remuneraciones", $class_li_remuneraciones);
    $t->setVar("class_tab_remuneraciones", $class_tab_remuneraciones);
    $t->setVar("class_li_nacimiento", $class_li_nacimiento);
    $t->setVar("class_tab_nacimiento", $class_tab_nacimiento);
    $t->setVar("class_li_matrimonio", $class_li_matrimonio);
    $t->setVar("class_tab_matrimonio", $class_tab_matrimonio);
    $t->setVar("class_li_liquidaciones", $class_li_liquidaciones);
    $t->setVar("class_tab_liquidaciones", $class_tab_liquidaciones);
    $t->setVar("class_li_otros", $class_li_otros);
    $t->setVar("class_tab_otros", $class_tab_otros);
    
    $t->setVar("max_peso", $max_peso);
    $t->setVar("cod_dj", $cod_dj_decrypt);
    
    $t->setVar("tp_cod_dj", $tp_cod_dj);
    $t->setVar("cardinalidad_dj", $cardinalidad_dj);
    $t->setVar("ayuda_dj", $ayuda_dj);
    
    $t->setVar("tp_f22", $tp_f22);
    $t->setVar("cardinalidad_f22", $cardinalidad_f22);
    $t->setVar("ayuda_f22", $ayuda_f22);
    
    $t->setVar("tp_ar", $tp_ar);
    $t->setVar("cardinalidad_ar", $cardinalidad_ar);
    $t->setVar("ayuda_ar", $ayuda_ar);
    
    $t->setVar("tp_ccp", $tp_ccp);
    $t->setVar("cardinalidad_ccp", $cardinalidad_ccp);
    $t->setVar("ayuda_ccp", $ayuda_ccp);
    
    $t->setVar("tp_cr", $tp_cr);
    $t->setVar("cardinalidad_cr", $cardinalidad_cr);
    $t->setVar("ayuda_cr", $ayuda_cr);
    
    $t->setVar("tp_cn", $tp_cn);
    $t->setVar("cardinalidad_cn", $cardinalidad_cn);
    $t->setVar("ayuda_cn", $ayuda_cn);
    
    $t->setVar("tp_cm", $tp_cm);
    $t->setVar("cardinalidad_cm", $cardinalidad_cm);
    $t->setVar("ayuda_cm", $ayuda_cm);
    
    $t->setVar("tp_otros", $tp_otros);
    $t->setVar("cardinalidad_otros", $cardinalidad_otros);
    $t->setVar("ayuda_ot", $ayuda_ot);
    
    $t->setVar("tp_lq", $tp_lq);
    $t->setVar("cardinalidad_lq", $cardinalidad_lq);
    $t->setVar("ayuda_lq", $ayuda_lq);
    
    $t->setVar("NOM_DEUDOR",$nombre);
    $t->setVar('K_ROOT_URL',$_SESSION['K_ROOT_URL']);
    
    //$link_exito = '<a href="https://fscu-userena.helen.cl/helen_userena/trunk/appl/webpay_userena/consulta-tu-deuda.php" class="btn btn-default" style="background: rgb(220, 45, 50) none repeat;border: medium none; color: white;">Volver al Portal</a>';
    $link_exito = '';

    $t->setVar('LINK_EXITO',$link_exito);
   
    print $t->toString();

?>