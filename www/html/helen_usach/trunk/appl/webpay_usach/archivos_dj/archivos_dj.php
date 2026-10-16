<?php
ini_set('display_errors', 'On');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');
include("../funciones.php");
require_once (dirname(__FILE__)."/../../../appl.ini");

if ( !isset( $_SESSION ['ready'] ) )
{
    $_SESSION['ready'] = TRUE;
}
if(!isset($_SESSION['COD_USUARIO']))
    $_SESSION['COD_USUARIO'] = "2";
if(!isset($_SESSION['NOM_USUARIO']))
    $_SESSION['NOM_USUARIO'] = "USUARIO WEB";
if(!isset($_SESSION['K_ROOT_URL']))
    $_SESSION['K_ROOT_URL'] = K_ROOT_URL;
if(!isset($_SESSION['K_ROOT_DIR']))
    $_SESSION['K_ROOT_DIR'] = K_ROOT_DIR;
if(!isset($_SESSION['K_APPL_INI']))
    $_SESSION['K_APPL_INI'] = K_ROOT_DIR."/appl.ini";
if(!isset($_SESSION['K_CLIENTE']))
    $_SESSION['K_CLIENTE'] = K_CLIENTE;
if(!isset($_SESSION['K_APPL']))
    $_SESSION['K_APPL'] = K_CLIENTE;
    
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

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

$modo_debug = 'N';


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

$sql = "SELECT CASE nvl(CON_HIJO,'N')
              WHEN 'N' THEN 'none'
              ELSE ''
            END DISPLAY_HIJO,
            CASE nvl(COD_ESTADO_CIVIL,1)
              WHEN 1 THEN 'none'
              ELSE ''
            END DISPLAY_CONYUGE
            FROM DECLARACION WHERE
            cod_declaracion = $cod_dj_decrypt";
$result = $db->build_results($sql);

$display_conyuge = $result[0]['DISPLAY_CONYUGE'];
$display_hijo = $result[0]['DISPLAY_HIJO'];

$cod_param_max_archivos=36;
$max_peso = $db->get_parametro($cod_param_max_archivos);


$t = new Template("archivos_dj.html");

$t->setVar("cod_pers", $cod_pers);
if($display_conyuge == ''){
    $display_hijo = 'none';
}

$t->setVar("display_conyuge", $display_conyuge);
$t->setVar("display_hijo", $display_hijo);

$t->setVar("max_peso", $max_peso);


$t->setVar("modo_debug", $modo_debug);
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

$sql = "SELECT I.COD_CARGA_INTENTO, I.NUMERO_INTENTO, I.ESTADO
          FROM DECLARACION_CARGA_INTENTO I
         WHERE I.COD_DECLARACION = $cod_dj_decrypt
         ORDER BY I.NUMERO_INTENTO";
$intentos = $db->build_results($sql);
$intentos_usados = count($intentos);
$ultimo_estado = $intentos_usados > 0 ? $intentos[$intentos_usados - 1]['ESTADO'] : '';
$cod_intento_activo = '';
$puede_subir = ($intentos_usados == 0);
if($intentos_usados > 0){
    $ultimo = $intentos[$intentos_usados - 1];
    if($ultimo['ESTADO'] == 'EN_CARGA'){
        $puede_subir = true;
        $cod_intento_activo = $ultimo['COD_CARGA_INTENTO'];
    }elseif($ultimo['ESTADO'] == 'RECHAZADO' && $intentos_usados < 2){
        $puede_subir = true;
    }
}

$mensajes_estado = array(
    '' => 'Aun no ha enviado documentacion.',
    'EN_CARGA' => 'Tiene una carga pendiente. Puede continuarla sin consumir otro intento.',
    'EN_REVISION' => 'Sus documentos fueron recibidos y se encuentran en revision.',
    'APROBADO' => 'Sus documentos fueron aprobados.',
    'RECHAZADO' => $intentos_usados < 2
        ? 'La primera carga fue observada. Dispone de un segundo y ultimo intento.'
        : 'Los dos intentos fueron utilizados. Contacte a la administracion para recibir asistencia.'
);
$mensaje_estado = isset($mensajes_estado[$ultimo_estado]) ? $mensajes_estado[$ultimo_estado] : 'Estado no disponible.';

$sql = "SELECT TA.NOM_TIPO_ARCHIVO, DA.F_NAME, I.NUMERO_INTENTO,
               TO_CHAR(DA.FECHA_SUBIDA, 'DD/MM/YYYY HH24:MI') FECHA_SUBIDA,
               RA.NOM_REVISADO_ARCHIVO, DA.OBS_REVISADO
          FROM DECLARACION_ARCHIVO DA
          JOIN TIPO_ARCHIVO TA ON TA.COD_TIPO_ARCHIVO = DA.COD_TIPO_ARCHIVO
          LEFT JOIN REVISADO_ARCHIVO RA ON RA.COD_REVISADO_ARCHIVO = DA.COD_REVISADO_ARCHIVO
          LEFT JOIN DECLARACION_CARGA_INTENTO I ON I.COD_CARGA_INTENTO = DA.COD_CARGA_INTENTO
         WHERE DA.COD_DECLARACION = $cod_dj_decrypt
         ORDER BY NVL(I.NUMERO_INTENTO, 1), DA.COD_TIPO_ARCHIVO, DA.COD_DECLARACION_ARCHIVO";
$documentos = $db->build_results($sql);
$filas_estado = '';
for($i = 0; $i < count($documentos); $i++){
    $doc = $documentos[$i];
    $intento_doc = $doc['NUMERO_INTENTO'] == '' ? '1' : $doc['NUMERO_INTENTO'];
    $filas_estado .= '<tr>'
        .'<td>'.htmlspecialchars($doc['NOM_TIPO_ARCHIVO'], ENT_QUOTES).'</td>'
        .'<td>'.htmlspecialchars($doc['F_NAME'], ENT_QUOTES).'</td>'
        .'<td>'.$intento_doc.' de 2</td>'
        .'<td>'.htmlspecialchars($doc['FECHA_SUBIDA'], ENT_QUOTES).'</td>'
        .'<td>'.htmlspecialchars($doc['NOM_REVISADO_ARCHIVO'], ENT_QUOTES).'</td>'
        .'<td>'.htmlspecialchars($doc['OBS_REVISADO'], ENT_QUOTES).'</td>'
        .'</tr>';
}
if($filas_estado == ''){
    $filas_estado = '<tr><td colspan="6">No hay documentos enviados.</td></tr>';
}

$numero_proximo_intento = $cod_intento_activo != '' ? $intentos[$intentos_usados - 1]['NUMERO_INTENTO'] : $intentos_usados + 1;
$texto_boton = $numero_proximo_intento == 2 ? 'Realizar segundo y ultimo intento' : 'Subir archivos - intento 1 de 2';
$t->setVar('INTENTOS_USADOS', $intentos_usados);
$t->setVar('MENSAJE_ESTADO', $mensaje_estado);
$t->setVar('FILAS_ESTADO_DOCUMENTOS', $filas_estado);
$t->setVar('DISPLAY_CARGA', $puede_subir ? '' : 'none');
$t->setVar('COD_CARGA_INTENTO', $cod_intento_activo);
$t->setVar('NUMERO_PROXIMO_INTENTO', $numero_proximo_intento);
$t->setVar('TEXTO_BOTON_CARGA', $texto_boton);

// Evita el bloqueo anterior basado solo en la existencia de archivos.
$ARCH_SUBIDOS = 'PLAZO_VIGENTE';

if($ARCH_SUBIDOS == 'TIENE_ARCHIVOS'){
    $t = new Template("mensaje.html");
    $mensaje = "Su declaración ya esta en proceso de validación, no puede subir nuevos documentos.";
    $t->setVar("MENSAJE", $mensaje);
}

$link_exito = '<a href="'.K_ROOT_URL.'appl/webpay_usach/inicio_sesion.php" class="btn btn-default" style="background: rgb(220, 45, 50) none repeat;border: medium none; color: white;">Volver al Portal</a>';

$t->setVar('LINK_EXITO',$link_exito);

print $t->toString();

?>
