<?php
ini_set('display_errors', 'On');
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../declaracion/class_sql_declaracion.php");
require_once(dirname(__FILE__)."/../declaracion/class_dw_declaracion.php");
require_once(dirname(__FILE__)."/../declaracion/class_dw_declaracion_mes.php");
include("funciones.php");
include("w_mail_class/class_email.php");
include("imprime_declaracion.php");

function get_popup_demanda_declaracion_script() {
	return <<<HTML
<script type="text/javascript">
(function () {
	function cerrarPopupDemandaDeclaracion() {
		var popup = document.getElementById('popupDemandaDeclaracion');
		var backdrop = document.getElementById('popupDemandaDeclaracionBackdrop');

		if (popup) {
			popup.style.display = 'none';
		}
		if (backdrop) {
			backdrop.style.display = 'none';
		}
	}

	function crearPopupDemandaDeclaracion() {
		var popup = document.getElementById('popupDemandaDeclaracion');
		var backdrop = document.getElementById('popupDemandaDeclaracionBackdrop');
		var style = document.getElementById('popupDemandaDeclaracionStyle');
		var button;

		if (!style) {
			style = document.createElement('style');
			style.id = 'popupDemandaDeclaracionStyle';
			style.type = 'text/css';
			style.appendChild(document.createTextNode(
				'.popup-demanda-backdrop{background-color:rgba(0,0,0,.55);height:100%;left:0;position:fixed;top:0;width:100%;z-index:9998;}' +
				'.popup-demanda-box{background-color:#fff;border-radius:4px;box-shadow:0 12px 40px rgba(0,0,0,.35);left:50%;max-width:760px;padding:30px;position:fixed;top:50%;transform:translate(-50%,-50%);width:90%;z-index:9999;}' +
				'.popup-demanda-box h3{margin-top:0;}' +
				'.popup-demanda-box p{font-size:16px;line-height:1.6;margin-bottom:15px;}' +
				'.popup-demanda-actions{margin-top:20px;text-align:right;}' +
				'.popup-demanda-button{background-color:#337ab7;border:1px solid #2e6da4;border-radius:4px;color:#fff;cursor:pointer;padding:8px 18px;}'
			));
			document.getElementsByTagName('head')[0].appendChild(style);
		}

		if (!backdrop) {
			backdrop = document.createElement('div');
			backdrop.id = 'popupDemandaDeclaracionBackdrop';
			backdrop.className = 'popup-demanda-backdrop';
			document.body.appendChild(backdrop);
		}

		if (!popup) {
			popup = document.createElement('div');
			popup.id = 'popupDemandaDeclaracion';
			popup.className = 'popup-demanda-box';
			popup.innerHTML =
				'<h3>Informacion Importante</h3>' +
				'<p>Debido a la alta demanda y al exceso de uso que estan presentando actualmente nuestros servidores por la temporada de Declaracion Jurada, podrian generarse mensajes de error o lentitud en la plataforma.</p>' +
				'<p>En caso de haber realizado la subida de documentos y posteriormente visualizar un mensaje de error, solicitamos no volver a cargar los archivos, ya que estos podrian haber sido recibidos correctamente por el sistema.</p>' +
				'<p>Recomendamos esperar unos minutos y luego verificar nuevamente el estado de la carga en la plataforma.</p>' +
				'<p>Agradecemos su comprension.</p>' +
				'<div class="popup-demanda-actions"><button type="button" id="cerrarPopupDemandaDeclaracion" class="popup-demanda-button">Cerrar</button></div>';
			document.body.appendChild(popup);
		}

		backdrop.style.display = 'block';
		popup.style.display = 'block';

		button = document.getElementById('cerrarPopupDemandaDeclaracion');
		if (button) {
			button.onclick = cerrarPopupDemandaDeclaracion;
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', crearPopupDemandaDeclaracion);
	}
	else {
		crearPopupDemandaDeclaracion();
	}
})();
</script>
HTML;
}

function inject_popup_demanda_declaracion_html($html) {
	$popup_html = <<<HTML
<div id="popupDemandaDeclaracionBackdrop" class="popup-demanda-backdrop"></div>
<div id="popupDemandaDeclaracion" class="popup-demanda-box">
	<h3>Informacion Importante</h3>
	<p>Debido a la alta demanda y al exceso de uso que estan presentando actualmente nuestros servidores por la temporada de Declaracion Jurada, podrian generarse mensajes de error o lentitud en la plataforma.</p>
	<p>En caso de haber realizado la subida de documentos y posteriormente visualizar un mensaje de error, solicitamos no volver a cargar los archivos, ya que estos podrian haber sido recibidos correctamente por el sistema.</p>
	<p>Recomendamos esperar unos minutos y luego verificar nuevamente el estado de la carga en la plataforma.</p>
	<p>Agradecemos su comprension.</p>
	<div class="popup-demanda-actions">
		<button type="button" class="popup-demanda-button" onclick="cerrarPopupDemandaDeclaracion();">Cerrar</button>
	</div>
</div>
HTML;

	if (strpos($html, 'popupDemandaDeclaracion') !== false) {
		return $html;
	}

	if (strpos($html, '<body >') !== false) {
		return str_replace('<body >', "<body >\n".$popup_html, $html);
	}

	if (strpos($html, '<body>') !== false) {
		return str_replace('<body>', "<body>\n".$popup_html, $html);
	}

	return $popup_html.$html;
}

$cod_deudor_direccion = session::get('cod_deudor');

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

// AJAX check endpoint: devuelve JSON indicando si está permitido ingresar DJ
if (isset($_GET['ajax_check'])) {
	header('Content-Type: application/json; charset=utf-8');
	$ano_actual = $db->current_year();

	$allowed = true;
	$message = '';

	// Buscar deuda CS vigente
	$sql_de = "SELECT\tCOD_DEUDA\n\t\t\tFROM\tDEUDA\n\t\t\tWHERE\tCOD_DEUDOR = $cod_deudor_direccion\n\t\t\t  AND\tCOD_TIPO_DEUDA = 'CS'\n\t\t\t  AND\tCOD_ESTADO_DEUDA = 2\n\t\t\t  AND\tFECHA_INICIO_COBRO <= SYSDATE\n\t\t\t  AND\tNOT EXISTS (SELECT * FROM CRUZADO WHERE CRUZADO.COD_DEUDA = DEUDA.COD_DEUDA)";
	$result_de = $db->build_results($sql_de);

	if (count($result_de) > 0) {
		$cod_deuda = $result_de[0]['COD_DEUDA'];

		// Verifica plan_pago cuota 99 con ano_cobro = 9999 y montos = 0
		if (K_TIPO_BD=="oci"){
			$sql_zero_pp = "SELECT count(*) CANT FROM PLAN_PAGO WHERE cod_deuda = $cod_deuda AND nro_cuota = 99 AND ano_cobro = 9999 AND NVL(monto_capital,0) = 0 AND NVL(monto_interes,0) = 0";
		} else {
			$sql_zero_pp = "SELECT count(*) CANT FROM PLAN_PAGO WHERE cod_deuda = $cod_deuda AND nro_cuota = 99 AND ano_cobro = 9999 AND ISNULL(monto_capital,0) = 0 AND ISNULL(monto_interes,0) = 0";
		}
		$result_zero_pp = $db->build_results($sql_zero_pp);
		if (count($result_zero_pp) > 0 && $result_zero_pp[0]['CANT'] > 0) {
			$allowed = false;
			$message = "Estimado deudor:<br><br>Le informamos que no es posible ingresar su declaración de ingresos, debido a que actualmente no registra saldo futuro que permita generar nuevas cuotas.<br><br>No obstante, su deuda se mantiene vigente, por lo que le invitamos a continuar cumpliendo oportunamente con el pago de las cuotas que se encuentran adeudadas.<br>Saludos cordiales.";
		}

		// Si aún permitido, verificar si tiene DJ anulada para el año relevante
		if ($allowed) {
			$sql_ann = "SELECT count(*) CANT FROM DECLARACION WHERE COD_DEUDOR = $cod_deudor_direccion AND ANO = " . ($ano_actual - 1) . " AND COD_ESTADO_DECLARACION = 3";
			$res_ann = $db->build_results($sql_ann);
			if (count($res_ann) > 0 && $res_ann[0]['CANT'] > 0) {
				$allowed = false;
				$message = "Estimado deudor:<br><br>Le informamos que no es posible ingresar su declaración de ingresos, debido a que actualmente no registra saldo futuro que permita generar nuevas cuotas.<br><br>No obstante, su deuda se mantiene vigente, por lo que le invitamos a continuar cumpliendo oportunamente con el pago de las cuotas que se encuentran adeudadas.<br><br>Saludos cordiales.";
			}
		}
	}

	echo json_encode(array('allowed' => $allowed, 'message' => $message));
	exit;
}

// VALIDA SI EL DEUDOR TIENE DECLARACIONES JURADA DEL AÑO //
$sql_declaracion = "SELECT Count(*) TIENE_DJ
					FROM DECLARACION
					WHERE COD_DEUDOR = $cod_deudor_direccion
					AND To_Char(FECHA_REGISTRO,'YYYY') = To_Char(SYSDATE,'YYYY')";
$result_declaracion = $db->build_results($sql_declaracion);

// REVISA SI TIENE DIRECCION ACTUALIZADA EN EL AÑO	
if($result_declaracion[0]['TIENE_DJ']==0){
	$sql_direccion ="SELECT count(*) CANT_DIRECCION
 					FROM DIRECCION
 					WHERE COD_DEUDOR = $cod_deudor_direccion
 						AND COD_DIRECCION = F_GET_DIRECCION_DEUDOR($cod_deudor_direccion,'P')
 						AND To_Char(FECHA_DIRECCION,'YYYY') = To_Char(SYSDATE,'YYYY')";
	$result_direccion = $db->build_results($sql_direccion);

// VALIDA SI EL DEUDOR SE ENCUENTRA EN COBRANZA //
$sql_de	=  "SELECT	COD_DEUDA
			FROM	DEUDA
			WHERE	COD_DEUDOR = $cod_deudor_direccion
			  AND	COD_TIPO_DEUDA = 'CS'
			  AND	COD_ESTADO_DEUDA = 2
			  AND   FECHA_INICIO_COBRO <= SYSDATE /*COBRANZA_NORMAL*/
			  AND NOT EXISTS (SELECT * FROM CRUZADO WHERE CRUZADO.COD_DEUDA = DEUDA.COD_DEUDA)";/* NUEVO 18/04*/
$result_de = $db->build_results($sql_de);

// SI EL DEUDOR ESTA EN COBRANZA, REVISA SI ES CRUZADO O TIENE CUOTA FIJA
if(count($result_de) > 0){
	$cod_deuda = $result_de[0]['COD_DEUDA'];
	$sql_pp = "SELECT	count(*) CANT_PP
				FROM	plan_pago
				WHERE	cod_deuda = $cod_deuda
				  AND	nro_cuota = 99
				  AND NOT EXISTS (SELECT * FROM CRUZADO WHERE CRUZADO.COD_DEUDA = PLAN_PAGO.COD_DEUDA)
				    AND NOT EXISTS (SELECT * FROM CONDONACION_CS WHERE CONDONACION_CS.COD_DEUDA = PLAN_PAGO.COD_DEUDA)";
	$result_pp = $db->build_results($sql_pp);

	// VALIDA SI EL SALDO FUTURO (CUOTA 99 / ANO_COBRO 9999) YA SE ENCUENTRA AGOTADO
	// Si esta en 0 no quedan cuotas futuras por calcular en base a ingresos declarados,
	// por lo tanto no corresponde permitir el ingreso de una nueva Declaracion Jurada,
	// aun cuando la deuda siga vigente y el deudor deba seguir pagando.
	$sql_saldo_futuro = "SELECT	f_pp_saldo_plan_pago(cod_plan_pago, sysdate) SALDO_FUTURO
				FROM	plan_pago
				WHERE	cod_deuda = $cod_deuda
				  AND	nro_cuota = 99
				  AND NOT EXISTS (SELECT * FROM CRUZADO WHERE CRUZADO.COD_DEUDA = PLAN_PAGO.COD_DEUDA)
				    AND NOT EXISTS (SELECT * FROM CONDONACION_CS WHERE CONDONACION_CS.COD_DEUDA = PLAN_PAGO.COD_DEUDA)";
	$result_saldo_futuro = $db->build_results($sql_saldo_futuro);

	// VALIDA especificamente si la cuota 99 (ano_cobro = 9999) tiene montos 0 -> bloquear ingreso DJ
	if (K_TIPO_BD=="oci"){
		$sql_zero_pp = "SELECT count(*) CANT FROM PLAN_PAGO WHERE cod_deuda = $cod_deuda AND nro_cuota = 99 AND ano_cobro = 9999 AND NVL(monto_capital,0) = 0 AND NVL(monto_interes,0) = 0";
	}else if (K_TIPO_BD=="mssql"){
		$sql_zero_pp = "SELECT count(*) CANT FROM PLAN_PAGO WHERE cod_deuda = $cod_deuda AND nro_cuota = 99 AND ano_cobro = 9999 AND ISNULL(monto_capital,0) = 0 AND ISNULL(monto_interes,0) = 0";
	}
	$result_zero_pp = $db->build_results($sql_zero_pp);
	if (count($result_zero_pp) > 0 && $result_zero_pp[0]['CANT'] > 0){
		header ('Location:'."error.php?error=11");
		exit;
	}

	if($result_direccion[0]['CANT_DIRECCION']==0){
		header ('Location:'."error.php?error=6");
		exit;
	}

	if (count($result_saldo_futuro) > 0 && (float)$result_saldo_futuro[0]['SALDO_FUTURO'] <= 0){
		header ('Location:'."error.php?error=11");
		exit;
	}
}

if($result_pp[0]['CANT_PP'] == 0){
		header ('Location:'."error.php?error=7");
		exit;
	}
}


//valida que tenga DJ del a�o anterior 
//Requerimiento: Que los deudores pertenecientes al CS, y que no hayan presentado DJ el a�o 2012 (Ingresos 2011),
//y que pasaron a cuota fija no puedan ingresar DJ nunca mas. Haciendo la salvedad con los deudores suspendidos
//durante el a�o 2012, que aun cuando no hayan presentado DJ, si puedan hacerlo.


	


class w_declaracion_jurada {
	//const K_VIA_DECLARACION_DIGITADA	= 1;
	const K_EST_DECL_INGRESADA			= 1;
	const K_EST_DECL_APROBADA			= 2;
	//const K_EST_DECL_ANULADA			= 3;
	//const K_AUTORIZA_MODIFICA_DECL_APROBADA = '991005';
	//const K_CARTERA_UACH				= 7;
	const K_DEUDOR_WEB = 2;
	
	var $dws = array();
	var $cod_deudor;
	var $is_new_record = false;
	var $cod_declaracion = '';
	
	function __construct() {
		valida_inicio();
		$this->cod_deudor = session::get('cod_deudor');
				
		$t = new Template_appl("declara_jurada.html");
		
		// Valida si existe declaracion ingresada para el a�o actual
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$ano_actual = $db->current_year();		
		
		// busca DECLARACION confirmada => si tiene, ya no puede modificarla 
		$sql = "select count(*) CANT
				from DECLARACION
				where COD_DEUDOR = $this->cod_deudor
				  and ANO = $ano_actual - 1
				  and COD_ESTADO_DECLARACION = ".self::K_EST_DECL_APROBADA;
		$result = $db->build_results($sql);
		
		if ($result[0]['CANT'] > 0) {
			header ('Location:'."error.php?error=4");
			return true;
		}

		$this->crea_dws();
		$t->setVar("LINK_ARCHIVOS", '');
		$t->setVar("COD_DECLARA", '');

		$cod_declaracion = $this->cod_declaracion;
		if (empty($cod_declaracion) && isset($_REQUEST['p']) && ctype_digit((string)$_REQUEST['p'])) {
		    $cod_declaracion_req = (int)$_REQUEST['p'];
		    $sql_cod_dj_req = "select count(*) CANT
		    				from DECLARACION
		    				where COD_DECLARACION = $cod_declaracion_req
		    				  and COD_DEUDOR = $this->cod_deudor";
		    $result_cod_dj_req = $db->build_results($sql_cod_dj_req);
		    if ($result_cod_dj_req[0]['CANT'] > 0) {
		        $cod_declaracion = $cod_declaracion_req;
		        $this->cod_declaracion = $cod_declaracion_req;
		    }
		}

		$sql_estado = "select count(*) CANT
				from DECLARACION
				where COD_DEUDOR = $this->cod_deudor
				  and ANO = $ano_actual - 1
				  and COD_ESTADO_DECLARACION = ".self::K_EST_DECL_INGRESADA;
		$result_estado = $db->build_results($sql_estado);
		
		
		
		
		session::set('w_declaracion_jurada', serialize($this));
		
		///////////////////
		// draw
		$this->dws['dw_declaracion']->habilitar($t, true);
		$this->dws['dw_declaracion_mes']->habilitar($t, true);
		

		if ($result_estado[0]['CANT'] > 0) {
			 $t->setVar("GUARDAR", '');
			 $t->setVar("IMPRIMIR", '');

			 $sql_cod_dj = "select COD_DECLARACION
				from DECLARACION
				where COD_DEUDOR = $this->cod_deudor
				  and ANO = $ano_actual - 1
				  and COD_ESTADO_DECLARACION = ".self::K_EST_DECL_INGRESADA;

			$result_cod_dj = $db->build_results($sql_cod_dj);
			$cod_dj_ingresada = $result_cod_dj[0]['COD_DECLARACION'];
			$cod_declaracion = $cod_dj_ingresada;
			$this->cod_declaracion = $cod_dj_ingresada;

			 $t->setVar("COD_DECLARA",$cod_dj_ingresada);
		}
		else{
			$t->setVar("GUARDAR", '');
			$t->setVar("IMPRIMIR", 'none');
		}

		if(!empty($cod_declaracion)){
		    $t->setVar("COD_DECLARA", $cod_declaracion);
		    $ARCH_SUBIDOS = comprobar_declaracion($cod_declaracion);

		    if($ARCH_SUBIDOS == 'SIN_ARCHIVOS' || $ARCH_SUBIDOS == 'PLAZO_VIGENTE'){
		        $cod_declaracion_enc = encriptar_url($cod_declaracion,'ENCRIPTACION_DJ');
		        $cod_declaracion_enc64 = base64url_encode($cod_declaracion_enc);
		        $link = '<a href="'.K_ROOT_URL.'appl/webpay_usach/archivos_dj/archivos_dj.php?cdj='.$cod_declaracion_enc64.'&build=20260529" class="button" style="border: none; display: table-cell;text-align: center;vertical-align: middle;">Subir Archivos</a>';
		        $t->setVar("LINK_ARCHIVOS", $link);
		    }
		}

		
		// computeds (como no se hereda desde w_input, se deben incluir el codigo de los computeds) 
		$script  = '<script type="text/javascript">';
		$script .= 'var computeds = new Array();';
		$script .= $this->dws['dw_declaracion_mes']->computeds();
		$script .= '</script>';
		// Popup deshabilitado temporalmente.
		// $script .= get_popup_demanda_declaracion_script();
		$t->setVar("WI_JAVA_SCRIPT", $script);
		
		// Popup deshabilitado temporalmente.
		// print inject_popup_demanda_declaracion_html($t->toString());
		print $t->toString();
		
		if(isset($_REQUEST['p'])){
		    $cod_dj = $_REQUEST['p'];
		    $this->imprime($cod_dj);
		}
	}
	function crea_dws() {
		$this->dws['dw_declaracion'] = new dw_declaracion();
		$this->dws['dw_declaracion']->remove_control('ANO_DECLARACION');
		$this->dws['dw_declaracion']->remove_control('RUT');
		
		$this->dws['dw_declaracion']->remove_control('NOM_CONYUGE');
		$this->dws['dw_declaracion']->add_control(new edit_text('NOM_CONYUGE', 80, 200));
		//$this->add_control(new edit_text('NOM_CONYUGE',90,90));
		
		$this->dws['dw_declaracion_mes'] = new dw_declaracion_mes();

		// busca DECLARACION en estado SOLICITADA, si encuentra es es la que se va a modificar, en caso contrario se crea una nueva
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$ano_actual = $db->current_year();		
		$sql = "select COD_DECLARACION
				from DECLARACION
				where COD_DEUDOR = $this->cod_deudor
				  and ANO = $ano_actual - 1
				  and COD_ESTADO_DECLARACION = ".self::K_EST_DECL_INGRESADA;
		$result = $db->build_results($sql);
		if (count($result)==0) {
			$this->is_new_record = true;
			$this->dws['dw_declaracion']->new_declaracion($ano_actual - 1);
			$this->dws['dw_declaracion_mes']->new_declaracion($ano_actual - 1);
		}
		else {
			$cod_declaracion = $result[0]['COD_DECLARACION'];
			$sql2 = "select COD_ESTADO_DECLARACION
				from DECLARACION
				where COD_DECLARACION =$cod_declaracion";
			$result2 = $db->build_results($sql2);
			
			/*if ($result2[0]['COD_ESTADO_DECLARACION'] == 0) {
				$this->dws['dw_declaracion']->set_entrable_dw(true);
				$this->dws['dw_declaracion_mes']->set_entrable_dw(true);
			}
			else if ($result2[0]['COD_ESTADO_DECLARACION'] == 1) {
				$this->dws['dw_declaracion']->set_entrable_dw(false);
				$this->dws['dw_declaracion_mes']->set_entrable_dw(false);
	
			}*/
		
			$this->is_new_record = false;
			$this->cod_declaracion = $result[0]['COD_DECLARACION'];
			$this->dws['dw_declaracion']->retrieve($this->cod_declaracion);
			$this->dws['dw_declaracion_mes']->retrieve($this->cod_declaracion);
		}
	}
	function save_declaracion() {
	    
		/////////////////////////
		// VMC, 18-02-2011
		// por ennredos de autoload �? no reconoce la definicion de dw, por eso se vuelve a crear el objeto como medida parche
		$this->crea_dws();
		$this->dws['dw_declaracion']->entrable = true;
		$this->dws['dw_declaracion_mes']->entrable = true;
		/////////////////////////
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		
		$this->dws['dw_declaracion']->get_values_from_POST();
		$this->dws['dw_declaracion_mes']->get_values_from_POST();
		
		$cod_estado_declaracion	= self::K_EST_DECL_INGRESADA;
		$cod_usuario            = $this->dws['dw_declaracion']->cod_usuario;
		$cod_deudor				= $this->cod_deudor;
		$cod_via_declaracion    = self::K_DEUDOR_WEB;
		$fecha_confirma         = $this->dws['dw_declaracion']->get_item(0, 'FECHA_CONFIRMA');
		$cod_usuario_confirma   = $this->dws['dw_declaracion']->cod_usuario;
		$ano_declaracion        = $this->dws['dw_declaracion']->get_item(0, 'ANO_DECLARACION');
		$con_hijo               = $this->dws['dw_declaracion']->get_item(0, 'CON_HIJO');
		$cod_afp                = $this->dws['dw_declaracion']->get_item(0, 'COD_AFP');
		$cod_estado_civil       = $this->dws['dw_declaracion']->get_item(0, 'COD_ESTADO_CIVIL');
		$rut_conyuge            = $this->dws['dw_declaracion']->get_item(0, 'RUT_CONYUGE');
		$dig_verif_conyuge      = $this->dws['dw_declaracion']->get_item(0, 'DIG_VERIF_CONYUGE');
		$nom_conyuge            = $this->dws['dw_declaracion']->get_item(0, 'NOM_CONYUGE');
		$conyuge_es_deudor      = $this->dws['dw_declaracion']->get_item(0, 'CONYUGE_ES_DEUDOR');
		$conyuge_declara        = $this->dws['dw_declaracion']->get_item(0, 'CONYUGE_DECLARA');
		$entrega_f22            = $this->dws['dw_declaracion']->get_item(0, 'ENTREGA_F22');
		
		$entrega_f22_conyuge    = $this->dws['dw_declaracion']->get_item(0, 'ENTREGA_F22_CONYUGE');
		$cod_afp_conyuge        = $this->dws['dw_declaracion']->get_item(0, 'COD_AFP_CONYUGE');

		$cod_declaracion		= ($this->cod_declaracion=='') ? "null" : $this->cod_declaracion;
		$cod_usuario			= ($cod_usuario=='') ? "null" : $cod_usuario;
		$fecha_confirma			= ($fecha_confirma=='') ? "null" : $fecha_confirma;
		$cod_usuario_confirma	= ($cod_usuario_confirma=='') ? "null" : $cod_usuario_confirma;
		$cod_estado_civil		= ($cod_estado_civil=='') ? "null" : $cod_estado_civil;
		$rut_conyuge			= ($rut_conyuge=='') ? "null" : $rut_conyuge;
		$dig_verif_conyuge		= ($dig_verif_conyuge=='') ? "null" : "'$dig_verif_conyuge'";
		$nom_conyuge			= ($nom_conyuge=='') ? "null" : "'$nom_conyuge'";
		$cod_afp_conyuge		= ($cod_afp_conyuge=='') ? "null" : $cod_afp_conyuge;
		
		$fecha_declaracion = base::str2date($db->current_date(), $db->current_time());
		
		if ($this->is_new_record)
			$operacion = 'INSERT';
		else
			$operacion = 'UPDATE';
		
		$sp = 'spu_declaracion';
		$param	= "'$operacion',
					$cod_declaracion,
					$cod_estado_declaracion,
					$cod_usuario,
					$cod_deudor,
					$cod_via_declaracion,
					$cod_usuario_confirma,
					$fecha_declaracion,
					$ano_declaracion,
					'$con_hijo',
					$cod_afp,
					$cod_estado_civil,
					$rut_conyuge,
					$dig_verif_conyuge,
					$nom_conyuge,
					'$conyuge_es_deudor',
					'$conyuge_declara',
					'$entrega_f22',
					$cod_afp_conyuge,
					'$entrega_f22_conyuge',
					'N',
					'N',
					'N',
					'N',
					'N',
					'N',
					'N'";
					
		$db->BEGIN_TRANSACTION();
		
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record) {
				$this->cod_declaracion = $db->GET_IDENTITY("declaracion");
				$this->dws['dw_declaracion']->set_item(0, 'COD_DECLARACION', $this->cod_declaracion);
			}
			if($this->dws['dw_declaracion_mes']->update($db,$this->cod_declaracion,$operacion)){
			     $db->COMMIT_TRANSACTION();
			     
			     if ($this->is_new_record){
				     if($this->envia_mail_dj($cod_deudor)){
				         header ('Location:'."declara_jurada.php?p=$this->cod_declaracion");
				     }
			     }
			     
			     header ('Location:'."declara_jurada.php?p=$this->cod_declaracion");
			     return true;
			}
		}
		$db->ROLLBACK_TRANSACTION();
		header ('Location:'."error.php?error=3");
		return false;
	}
	
	function envia_mail_dj($cod_deudor){
	    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	    
	    $sql_cd = "SELECT f_get_direccion_deudor($cod_deudor,'P')DIR  FROM DUAL";
	    $result_direccion = $db->build_results($sql_cd);
	    $cod_direccion = $result_direccion[0]['DIR'];
	    
	    
	    $sql_dir = "SELECT EMAIL FROM direccion WHERE COD_DIRECCION = $cod_direccion";
	    $result_mail = $db->build_results($sql_dir);
	    
	    $dir_email = $result_mail[0]['EMAIL'];
	    $asunto 			 = "Declaracion Jurada ingresada, suba sus documentos de respaldos";
	    $address             = $dir_email;//'ibrito@integrasystem.cl';//
	    
	    $sql = "SELECT 	NOMBRE ||' '|| APELLIDO_PATERNO ||' '|| APELLIDO_MATERNO as NOMBRE
			FROM 	DEUDOR
			WHERE 	COD_DEUDOR = $cod_deudor";
	    $result = $db->build_results($sql);
	    $nombre= $result[0]['NOMBRE'];
	    
	    if(!empty($address)){
	        $mail = new Email();
	        $cod_declaracion_enc = encriptar_url($this->cod_declaracion,'ENCRIPTACION_DJ');
	        $cod_declaracion_enc64 = base64url_encode($cod_declaracion_enc);
	        $enlace= K_ROOT_URL."appl/webpay_usach/archivos_dj/archivos_dj.php?cdj=$cod_declaracion_enc64&build=20260529";
	        
	        $temp = new Template('archivos_dj/mail_dj.htm');
	        $temp->setVar("NOMBRE", "$nombre");
	        $temp->setVar("LINK", "$enlace");
	        
	        $Print_declaracion = new print_declaracion_web($this->cod_declaracion,true);
	        $pdf = true;
	        
	        $body = $temp->toString();
	        if($mail->dj_mail($address,$body,$asunto,$pdf)){
	            return true;
	        }else{
	            echo 'error al enviar mail';
	        }
	    }
	}
	
	function imprime($cod_declaracion) {
	    print	"<script>
						popup_window = window.open('imprime_declaracion.php?cod_declaracion=$cod_declaracion');
				</script>";
	    return true;
	}
}
		
if (isset($_POST['b_enviar'])){
	$w = unserialize(session::get('w_declaracion_jurada'));
	$w->save_declaracion();
}
else{
	new w_declaracion_jurada();
}
?>
