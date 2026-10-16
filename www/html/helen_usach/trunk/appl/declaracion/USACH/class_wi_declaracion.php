<?php
require_once(dirname(__FILE__)."/class_print_declaracion.php");
////////////////////////////////////////
/////////////// USACH //////////////////
////////////////////////////////////////
class wi_declaracion extends wi_declaracion_base {
    
	const K_PARAM_NOM_UNIVERSIDAD		= 5;
	
	function __construct($cod_item_menu) {
	    parent::__construct($cod_item_menu);
	}
		function save_record($db) {
		$cod_declaracion        = $this->get_key();
		$cod_estado_declaracion	= $this->dws['dw_declaracion']->get_item(0, 'COD_ESTADO_DECLARACION');
		$cod_usuario            = $this->cod_usuario;
		$cod_deudor				= $this->dws['dw_declaracion']->get_item(0, 'COD_DEUDOR');
		$cod_via_declaracion    = $this->dws['dw_declaracion']->get_item(0, 'COD_VIA_DECLARACION');
		$fecha_confirma         = $this->dws['dw_declaracion']->get_item(0, 'FECHA_CONFIRMA');
		$cod_usuario_confirma   = $this->cod_usuario;
		$fecha_declaracion      = $this->dws['dw_declaracion']->get_item(0, 'FECHA_DECLARACION');
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
		$cod_afp_conyuge        = $this->dws['dw_declaracion']->get_item(0, 'COD_AFP_CONYUGE');
		$entrega_f22_conyuge    = $this->dws['dw_declaracion']->get_item(0, 'ENTREGA_F22_CONYUGE');
		
		$cert_sueldo            = $this->dws['dw_declaracion']->get_item(0, 'CERT_SUELDO');
		$cert_honorario         = $this->dws['dw_declaracion']->get_item(0, 'CERT_HONORARIO');
		$cert_afp				= $this->dws['dw_declaracion']->get_item(0, 'CERT_AFP');
		$declara_renta 			= $this->dws['dw_declaracion']->get_item(0, 'DECLARA_RENTA');
		$cert_notarial_ing 		= $this->dws['dw_declaracion']->get_item(0, 'CERT_NOTARIAL_ING');
		$antec_conyuge 			= $this->dws['dw_declaracion']->get_item(0, 'ANTEC_CONYUGE');
		$cert_alum_regular		= $this->dws['dw_declaracion']->get_item(0, 'CERT_ALUM_REGULAR');
		$presenta_postgrado		= $this->dws['dw_declaracion']->get_item(0, 'PRESENTA_POSTGRADO');

		$cod_declaracion		= ($cod_declaracion=='') ? "null" : $cod_declaracion;
		$cod_usuario			= ($cod_usuario=='') ? "null" : $cod_usuario;
		$fecha_confirma			= ($fecha_confirma=='') ? "null" : $fecha_confirma;
		$cod_usuario_confirma	= ($cod_usuario_confirma=='') ? "null" : $cod_usuario_confirma;
		$cod_estado_civil		= ($cod_estado_civil=='') ? "null" : $cod_estado_civil;
		$rut_conyuge			= ($rut_conyuge=='') ? "null" : $rut_conyuge;
		$dig_verif_conyuge		= ($dig_verif_conyuge=='') ? "null" : "'$dig_verif_conyuge'";
		$nom_conyuge			= ($nom_conyuge=='') ? "null" : "'$nom_conyuge'";
		$cod_afp_conyuge		= ($cod_afp_conyuge=='') ? "null" : $cod_afp_conyuge;
		
		$fecha_declaracion =$this->str2date($fecha_declaracion);

		// Obtiene los valores que inciden en el CALCULO_CUOTA antes de grabar 
		$result_antes = $this->datos_calcula_cuota($cod_declaracion);
		
		$sp = 'spu_declaracion';
		if ($this->is_new_record()){
			$operacion = 'INSERT';
			$cod_estado_ant = 0;	// cualquier cosa <> APROBADA 		
		}
		else{
			$operacion = 'UPDATE';
			// obtiene estado anterior
			if (K_TIPO_BD=="oci"){ 
				$sql = "select COD_ESTADO_DECLARACION
						from DECLARACION
						where COD_DECLARACION = $cod_declaracion";
			}else if (K_TIPO_BD=="mssql"){
				$sql = "select COD_ESTADO_DECLARACION
						from DECLARACION
						where COD_DECLARACION = $cod_declaracion";				
			}
			
			$result = $db->build_results($sql);
			$cod_estado_ant = $result[0]['COD_ESTADO_DECLARACION']; 		
		}
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
					'$cert_sueldo',
					'$cert_honorario',
					'$cert_afp',
					'$declara_renta',
					'$cert_notarial_ing',
					'$antec_conyuge',
					'$cert_alum_regular',
					'$presenta_postgrado'";
		
		if ($this->dws['dw_declaracion']->is_modified) {
			if ($db->EXECUTE_SP($sp, $param)){
				if ($this->is_new_record()) {
					$cod_declaracion = $db->GET_IDENTITY("declaracion");
					$this->dws['dw_declaracion']->set_item(0, 'COD_DECLARACION', $cod_declaracion);
				}
			}
			else
				return false;
		}

		if (!$this->dws['dw_direccion_deudor_particular']->update($db,$cod_deudor)){
			return false;
		}
		if (!$this->dws['dw_direccion_deudor_comercial']->update($db,$cod_deudor)){
			return false;
		}
		if (!$this->dws['dw_declaracion_mes']->update($db,$cod_declaracion,$operacion)){
			return false;
		}
		if (!$this->dws['dw_declaracion_archivo']->update($db,$cod_declaracion)){
			return false;
		}

		$recalcula = false;
		$cuota_calculada = $this->cuota_calculada($cod_declaracion);
		if ($cuota_calculada=='S') {
			if ($cod_estado_declaracion==self::K_EST_DECL_ANULADA &&  $result_antes[0]['COD_ESTADO_DECLARACION']==self::K_EST_DECL_APROBADA) {
				// se anula una DJ aprobada
				$recalcula = true;
			}
			else if ($cod_estado_declaracion==self::K_EST_DECL_APROBADA) {
				$result_despues = $this->datos_calcula_cuota($cod_declaracion);
				if ($result_antes[0]['COD_ESTADO_DECLARACION'] != $result_despues[0]['COD_ESTADO_DECLARACION'] ||
					$result_antes[0]['CON_HIJO'] != $result_despues[0]['CON_HIJO'] ||
					$result_antes[0]['COD_ESTADO_CIVIL'] != $result_despues[0]['COD_ESTADO_CIVIL'] ||
					$result_antes[0]['CONYUGE_ES_DEUDOR'] != $result_despues[0]['CONYUGE_ES_DEUDOR'] ||
					$result_antes[0]['CONYUGE_DECLARA'] != $result_despues[0]['CONYUGE_DECLARA'] ||
					$result_antes[0]['MONTO_DEUDOR'] != $result_despues[0]['MONTO_DEUDOR'] ||
					$result_antes[0]['MONTO_CONYUGE'] != $result_despues[0]['MONTO_CONYUGE'])
					$recalcula = true;
			}
		}
			// Obtiene los valores que inciden en el CALCULO_CUOTA antes de grabar 
		if ($recalcula) {
			$cod_usuario = $this->cod_usuario;
			if (!$db->EXECUTE_SP($sp, "'RE_CALCULO_CUOTA', $cod_declaracion, null, $cod_usuario"))
				return false;
			session::set("RE_CALCULO_CUOTA", "true");
		}
			
		return true;
	} 
	function print_record() {
		$cod_declaracion = $this->get_key();
		$sql= "SELECT 	DR.RUT
										,DR.DIG_VERIF
										,DR.NOMBRE
										,DR.APELLIDO_PATERNO
										,DR.APELLIDO_MATERNO
										,D.COD_DECLARACION
										,D.FECHA_REGISTRO
										,U1.NOM_USUARIO
										,D.COD_DEUDOR
										,VD.NOM_VIA_DECLARACION
										,TO_CHAR(D.FECHA_CONFIRMA, 'dd/mm/yyyy') FECHA_CONFIRMA 
										,D.COD_ESTADO_DECLARACION
										,ED.NOM_ESTADO_DECLARACION
										,U2.NOM_USUARIO NOM_USUARIO_CONFIRMA
										,TO_CHAR(D.FECHA_DECLARACION, 'dd/mm/yyyy') FECHA_DECLARACION
										,D.ANO
										,D.ANO + 1 ANO_DECLARACION
										,D.CON_HIJO
										,D.COD_AFP
										,A.NOM_AFP
										,EC.NOM_ESTADO_CIVIL
										,D.COD_ESTADO_CIVIL
										,D.RUT_CONYUGE
										,D.DIG_VERIF_CONYUGE
										,D.NOM_CONYUGE
										,D.CONYUGE_ES_DEUDOR
										,D.CONYUGE_DECLARA
										,D.ENTREGA_F22
										,f_get_parametro(".self::K_PARAM_NOM_UNIVERSIDAD.") NOM_UNIVERSIDAD
										,to_char(FECHA_DECLARACION,'DD/MM/YYYY') FECHA_DECLARACION
										,'N' ES_WEB_DEUDOR
                    					,ENTREGA_F22_CONYUGE 
                    					,AC.NOM_AFP NOM_AFP_CONYUGE
                    					,PRESENTA_POSTGRADO
								FROM 	DECLARACION D LEFT OUTER JOIN USUARIO U1 ON D.COD_USUARIO = U1.COD_USUARIO
											LEFT OUTER JOIN USUARIO U2 ON D.COD_USUARIO_CONFIRMA = U2.COD_USUARIO 
                      							LEFT OUTER JOIN AFP AC ON D.COD_AFP_CONYUGE = AC.COD_AFP,
										DEUDOR DR, VIA_DECLARACION VD, ESTADO_DECLARACION ED, AFP A, ESTADO_CIVIL EC
								WHERE 	D.COD_DECLARACION = $cod_declaracion
								AND 	D.COD_DEUDOR = DR.COD_DEUDOR
								AND 	D.COD_VIA_DECLARACION = VD.COD_VIA_DECLARACION
								AND 	D.COD_ESTADO_DECLARACION = ED.COD_ESTADO_DECLARACION
								AND 	D.COD_AFP = A.COD_AFP
								AND 	D.COD_ESTADO_CIVIL = EC.COD_ESTADO_CIVIL";//sql_declaracion::get_sql('PRINT_RECORD',$cod_declaracion);
		
		// reporte
		$labels = array();
		$file_name = $this->find_file('declaracion', 'declaracion.xml');
		$rpt = new print_declaracion_usach($sql, $file_name, $labels, "declaracion".$cod_declaracion.'.pdf', 0);
		$this->_load_record();
	}
	 
}
?>