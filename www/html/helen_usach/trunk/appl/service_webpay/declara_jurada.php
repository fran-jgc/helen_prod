<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../declaracion/class_sql_declaracion.php");
require_once(dirname(__FILE__)."/../declaracion/class_dw_declaracion.php");
require_once(dirname(__FILE__)."/../declaracion/class_dw_declaracion_mes.php");
include("funciones.php");

$cod_deudor_direccion = session::get('cod_deudor');
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql_declaracion = "SELECT Count(*) TIENE_DJ
					FROM DECLARACION
					WHERE COD_DEUDOR = $cod_deudor_direccion
						AND To_Char(FECHA_REGISTRO,'YYYY') = To_Char(SYSDATE,'YYYY')";
$result_declaracion = $db->build_results($sql_declaracion);
	
if($result_declaracion[0]['TIENE_DJ']==0){
	$sql_direccion ="SELECT count(*) CANT_DIRECCION
 					FROM DIRECCION
 					WHERE COD_DEUDOR = $cod_deudor_direccion
 						AND COD_DIRECCION = F_GET_DIRECCION_DEUDOR($cod_deudor_direccion,'P')
 						AND To_Char(FECHA_DIRECCION,'DD/MM/YYYY') = To_Char(SYSDATE,'DD/MM/YYYY')";
	$result_direccion = $db->build_results($sql_direccion);
	
	if($result_direccion[0]['CANT_DIRECCION']==0){
		header ('Location:'."error.php?error=6");
	}
}

//valida que tenga DJ del año anterior 
//Requerimiento: Que los deudores pertenecientes al CS, y que no hayan presentado DJ el año 2012 (Ingresos 2011),
//y que pasaron a cuota fija no puedan ingresar DJ nunca mas. Haciendo la salvedad con los deudores suspendidos
//durante el año 2012, que aun cuando no hayan presentado DJ, si puedan hacerlo.
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql_de	=  "SELECT	COD_DEUDA
			FROM	DEUDA
			WHERE	COD_DEUDOR = $cod_deudor_direccion
			  AND	COD_TIPO_DEUDA = 'CS'
			  --AND   COD_UNIVERSIDAD = 6
			  --AND   COD_CARTERA = 6
			  AND	COD_ESTADO_DEUDA = 2
			  
			   "; //COBRANZA_NORMAL

$result_de = $db->build_results($sql_de);

if(count($result_de) > 0){
	$cod_deuda = $result_de[0]['COD_DEUDA'];
	$sql_pp = "SELECT	count(*) CANT_PP
				FROM	plan_pago
				WHERE	cod_deuda = $cod_deuda
				  AND	nro_cuota = 99
				  AND NOT EXISTS (SELECT * FROM CRUZADO WHERE CRUZADO.COD_DEUDA = plan_pago.COD_DEUDA)";
	$result_pp = $db->build_results($sql_pp);
	
if($result_pp[0]['CANT_PP'] == 0){
		header ('Location:'."error.php?error=7");
	}
}

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
	var $cod_declaracion;
	
	function w_declaracion_jurada() {
		valida_inicio();
		$this->cod_deudor = session::get('cod_deudor');
				
		$t = new Template_appl("declara_jurada.html");
		
		// Valida si existe declaracion ingresada para el año actual
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
		
		serialize(session::set('w_declaracion_jurada', $this));
		
		///////////////////
		// draw
		$this->dws['dw_declaracion']->habilitar($t, true);
		$this->dws['dw_declaracion_mes']->habilitar($t, true);
		
		// computeds (como no se hereda desde w_input, se deben incluir el codigo de los computeds) 
		$script  = '<script type="text/javascript">';
		$script .= 'var computeds = new Array();';
		$script .= $this->dws['dw_declaracion_mes']->computeds();
		$script .= '</script>';
		$t->setVar("WI_JAVA_SCRIPT", $script);
		
		print $t->toString();
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
			$this->is_new_record = false;
			$this->cod_declaracion = $result[0]['COD_DECLARACION'];
			$this->dws['dw_declaracion']->retrieve($this->cod_declaracion);
			$this->dws['dw_declaracion_mes']->retrieve($this->cod_declaracion);
		}
	}
	function save_declaracion() {
		/////////////////////////
		// VMC, 18-02-2011
		// por ennredos de autoload ¿? no reconoce la definicion de dw, por eso se vuelve a crear el objeto como medida parche
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
			if ($this->dws['dw_declaracion_mes']->update($db,$this->cod_declaracion,$operacion)){
				$db->COMMIT_TRANSACTION();
				
				print " <script>
							popup_window = window.open('imprime_declaracion.php?cod_declaracion=$this->cod_declaracion')
					</script>";
						
				/* VMC, 24-02-2011, no se usa "error.php", porque al usarlo no abre la nueva ventana con el pdf 
				//header ('Location:'."error.php?error=2");
				*/
				print '<table width="800" border="0">
						<tr>
							<td bgcolor="#FFFFFF" align="center">SE HA ACTUALIZADO SU INFORMACIÓN.</td>
						</tr>
						</table>';
			
			
			
				return true;
			}
		}
		$db->ROLLBACK_TRANSACTION();
		header ('Location:'."error.php?error=3");
		return false;
	}
}
		
if (isset($_POST['b_enviar'])){
	$w = session::get('w_declaracion_jurada');
	$w->save_declaracion();
}
else{
	new w_declaracion_jurada();
}
?>