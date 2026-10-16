<?php
ini_set('display_errors', 'Off');
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



function get_date(){
    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    if (K_TIPO_BD=='oci')
        $sql = "SELECT	To_Char(sysdate, 'dd/mm/yyyy') FECHA_ACTUAL FROM DUAL";
    else if (K_TIPO_BD=='mssql')
        $sql = "SELECT	convert(varchar, getdate(), 103) FECHA_ACTUAL";
        
    $result_fecha = $db-> build_results($sql);
    $fecha_actual = $result_fecha[0]['FECHA_ACTUAL'];
    
    return $fecha_actual;
}

function get_valor_utm($cod_moneda){
    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    if (K_TIPO_BD=='oci')
        $sql = "SELECT f_mon_get_valor_moneda($cod_moneda, SYSDATE) VALOR_UTM_MENSUAL FROM DUAL";
        else if (K_TIPO_BD=='mssql')
            $sql = "SELECT dbo.f_mon_get_valor_moneda($cod_moneda, GETDATE()) VALOR_UTM_MENSUAL";
            
            $result_moneda = $db-> build_results($sql);
            $valor_utm = $result_moneda[0]['VALOR_UTM_MENSUAL'];
            
            return $valor_utm;
}

function valida_inicio(){
    if (!session::is_set('COD_DEUDOR')){
        header ('Location:'."error.php?error=1");
    }
   /* if(!isset($_SESSION['K_ROOT_URL']))
        header ('Location:'."error.php?error=1");
    if(!isset($_SESSION['K_ROOT_DIR']))
        header ('Location:'."error.php?error=1");
    if(!isset($_SESSION['K_APPL_INI']))
        header ('Location:'."error.php?error=1");
    if(!isset($_SESSION['K_CLIENTE']))
        header ('Location:'."error.php?error=1");
    if(!isset($_SESSION['K_APPL']))
        header ('Location:'."error.php?error=1");*/
}



function go_boton($image,$link,$mensaje,$class = ''){
    $go_boton = '<a class="'.$class.'" href="'.$link.'">'.$mensaje.'</a>';
    
    return $go_boton;
}

function go_boton_js($image,$link,$mensaje){
    $go_boton = '<span class="itemtablas">
					<b>
						<font color="#041CF4">
							<input type="image" onclick="'.$link.'" id="go_back" name="go_back" src="images/'.$image.'.png"/>'.$mensaje.'
						</font>
					</b>
				</span>';
    
    return $go_boton;
}

class edit_pass extends edit_password {
    var $tabindex;
    var $size;
    var $maxlen;
    
    function __construct($field, $size, $maxlen) {
        parent::__construct($field, $size, $maxlen);
    }
    function draw_entrable($dato, $record) {
        $field = $this->field.'_'.$record;
        if ($this->readonly)
            $ctrl = '<input class="campoformulario" name="'.$field.'" id="'.$field.'" type="'.$this->type.'" readonly="true" value="'.$dato.'" size="'.$this->size.'" maxLength="'.$this->maxlen.'" ';
            else
                $ctrl = '<input class="campoformulario" name="'.$field.'" id="'.$field.'" type="'.$this->type.'" value="'.$dato.'" size="'.$this->size.'" maxLength="'.$this->maxlen.'" ';
                
                if ($this->type != 'hidden' || $this->forzar_js)
                    $ctrl .= $this->make_java_script();
                    
                    if ($this->style!='')
                        $ctrl .= ' style="'.$this->style.'"';
                        
                        // Se asigna el tabindex
                        $ctrl .= ' tabindex="'.($this->tabindex + $record).'"';
                        
                        $ctrl .= '/>';
                        
                        return $ctrl;
    }
}

function pass_aleatorio(){
    //GENERA EL ALFANUMERICO ALETORIO Y LLENA LA VARIABLE $RES = ALETORIO
    $length = 6;
    $source = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $source .= '1234567890';
    
    if($length>0){
        $RES = "";
        $source = str_split($source,1);
        for($i=1; $i<=$length; $i++){
            mt_srand((double)microtime() * 1000000);
            $num	= mt_rand(1,count($source));
            $RES	.= $source[$num-1];
        }
        
    }
    return $RES;
}

function encriptar_url($txt_input, $key){
    $result = '';
    for($i=0; $i<strlen($txt_input); $i++) {
        $char = substr($txt_input, $i, 1);
        $keychar = substr($key, ($i % strlen($key))-1, 1);
        $char = chr(ord($char)+ord($keychar));
        $result.=$char;
    }
    $txt_ouput = base64_encode($result);
    
    return $txt_ouput;
}

function dencriptar_url($txt_input, $key){
    $result = '';
    $string = base64_decode($txt_input);
    for($i=0; $i<strlen($string); $i++) {
        $char = substr($string, $i, 1);
        $keychar = substr($key, ($i % strlen($key))-1, 1);
        $char = chr(ord($char)-ord($keychar));
        $result.=$char;
    }
    $txt_ouput = $result;
    
    return $txt_ouput;
}

function base64url_decode($data) {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function comprobar_declaracion($cod_dj){
    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    
    $sql_decl = "SELECT NVL(CON_HIJO,'N') CON_HIJO,
                        NVL(COD_ESTADO_CIVIL,1) COD_ESTADO_CIVIL
                 FROM DECLARACION
                 WHERE COD_DECLARACION = $cod_dj";
    $result_decl = $db->build_results($sql_decl);
    
    if (count($result_decl) == 0) return 'SIN_ARCHIVOS';
    
    // Cambiar TRIM(F_ERROR) = '0' por F_ERROR IS NULL
    $sql = "SELECT NVL(SUM(CASE
                        WHEN COD_TIPO_ARCHIVO = 1 AND F_ERROR IS NULL THEN 1
                        ELSE 0
                    END), 0) CANT_DJ
                    ,NVL(SUM(CASE
                        WHEN COD_TIPO_ARCHIVO IN (2,3,4,5,8,9) AND F_ERROR IS NULL THEN 1
                        ELSE 0
                    END), 0) CANT_RESPALDO
                    ,NVL(SUM(CASE
                        WHEN COD_TIPO_ARCHIVO = 6 AND F_ERROR IS NULL THEN 1
                        ELSE 0
                    END), 0) CANT_HIJO
                    ,NVL(SUM(CASE
                        WHEN COD_TIPO_ARCHIVO = 7 AND F_ERROR IS NULL THEN 1
                        ELSE 0
                    END), 0) CANT_CONYUGE
                FROM DECLARACION_ARCHIVO
                WHERE COD_DECLARACION = $cod_dj";
    $result = $db->build_results($sql);
    
    $con_hijo = $result_decl[0]['CON_HIJO'];
    $cod_estado_civil = $result_decl[0]['COD_ESTADO_CIVIL'];
    $cant_dj = $result[0]['CANT_DJ'];
    $cant_respaldo = $result[0]['CANT_RESPALDO'];
    $cant_hijo = $result[0]['CANT_HIJO'];
    $cant_conyuge = $result[0]['CANT_CONYUGE'];
    
    $cumple_minimo = ($cant_dj > 0 && $cant_respaldo > 0);
    if ($cumple_minimo && $con_hijo != 'N' && $cant_hijo == 0) $cumple_minimo = false;
    if ($cumple_minimo && $cod_estado_civil != 1 && $cant_conyuge == 0) $cumple_minimo = false;
    
    return $cumple_minimo ? 'TIENE_ARCHIVOS' : 'SIN_ARCHIVOS';
}
?>
