<?php
ini_set('display_errors', 'Off');
header('Content-Type: application/json; charset=utf-8');

require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../../appl.ini");

function responder_intento($ok, $mensaje, $cod_intento = null, $numero_intento = null){
    echo json_encode(array(
        'ok' => $ok,
        'mensaje' => $mensaje,
        'cod_carga_intento' => $cod_intento,
        'numero_intento' => $numero_intento
    ));
    exit;
}

$cod_declaracion = isset($_POST['cod_dj']) ? (int)$_POST['cod_dj'] : 0;
$cod_deudor = isset($_POST['cod_pers']) ? (int)$_POST['cod_pers'] : 0;

if($cod_declaracion <= 0 || $cod_deudor <= 0){
    responder_intento(false, 'Datos de la declaracion incompletos.');
}

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$sql = "SELECT COD_DECLARACION
          FROM DECLARACION
         WHERE COD_DECLARACION = $cod_declaracion
           AND COD_DEUDOR = $cod_deudor";
$declaracion = $db->build_results($sql);
if(count($declaracion) == 0){
    responder_intento(false, 'La declaracion no pertenece al deudor indicado.');
}

// Un intento EN_CARGA se reutiliza para tolerar recargas o fallas de red.
$sql = "SELECT COD_CARGA_INTENTO, NUMERO_INTENTO
          FROM DECLARACION_CARGA_INTENTO
         WHERE COD_DECLARACION = $cod_declaracion
           AND ESTADO = 'EN_CARGA'
         ORDER BY NUMERO_INTENTO DESC";
$activos = $db->build_results($sql);
if(count($activos) > 0){
    responder_intento(true, 'Intento de carga recuperado.',
        $activos[0]['COD_CARGA_INTENTO'], $activos[0]['NUMERO_INTENTO']);
}

$sql = "SELECT NVL(MAX(NUMERO_INTENTO), 0) ULTIMO_INTENTO,
               MAX(ESTADO) KEEP (DENSE_RANK LAST ORDER BY NUMERO_INTENTO) ULTIMO_ESTADO
          FROM DECLARACION_CARGA_INTENTO
         WHERE COD_DECLARACION = $cod_declaracion";
$estado = $db->build_results($sql);
$ultimo_intento = (int)$estado[0]['ULTIMO_INTENTO'];
$ultimo_estado = $estado[0]['ULTIMO_ESTADO'];

if($ultimo_intento >= 2){
    responder_intento(false, 'Se agotaron los dos intentos permitidos.');
}
if($ultimo_intento > 0 && $ultimo_estado != 'RECHAZADO'){
    responder_intento(false, 'La documentacion ya se encuentra en revision o fue aprobada.');
}

$numero_intento = $ultimo_intento + 1;
$secuencia = $db->build_results("SELECT DECL_CARGA_INTENTO_SQ.NEXTVAL CODIGO FROM DUAL");
$cod_intento = (int)$secuencia[0]['CODIGO'];
$sql = "BEGIN
            INSERT INTO DECLARACION_CARGA_INTENTO (
                COD_CARGA_INTENTO, COD_DECLARACION, NUMERO_INTENTO,
                ESTADO, FECHA_INICIO
            ) VALUES (
                $cod_intento, $cod_declaracion, $numero_intento,
                'EN_CARGA', SYSDATE
            );
            COMMIT;
        END;";

if(!$db->query($sql)){
    responder_intento(false, 'No fue posible iniciar el intento de carga. Recargue la pagina.');
}

responder_intento(true, 'Intento iniciado.', $cod_intento, $numero_intento);
?>
