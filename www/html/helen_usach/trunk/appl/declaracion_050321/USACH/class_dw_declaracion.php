<?php
////////////////////////////////////////
/////////////// USACH //////////////////
////////////////////////////////////////
class dw_declaracion extends dw_declaracion_base {
    function __construct() {
        parent::__construct();
		$sql = "SELECT 	COD_DECLARACION
							     	, COD_ESTADO_DECLARACION
							     	, COD_ESTADO_DECLARACION COD_ESTADO_DECLARACION_NO_ING
							     	, DEUDOR.COD_DEUDOR
							     	, DEUDOR.RUT RUT
							     	, DEUDOR.RUT RUT_NO_ING
							     	, DEUDOR.DIG_VERIF
							     	, DEUDOR.DIG_VERIF DIG_VERIF_NO_ING
							     	, DEUDOR.NOMBRE || ' ' || DEUDOR.APELLIDO_PATERNO || ' ' || APELLIDO_MATERNO NOMBRE
							     	, DEUDOR.NOMBRE || ' ' || DEUDOR.APELLIDO_PATERNO || ' ' || APELLIDO_MATERNO NOMBRE_NO_ING
							     	, DECLARACION.COD_VIA_DECLARACION
							     	, TO_CHAR (FECHA_CONFIRMA, 'DD/MM/YYYY') FECHA_CONFIRMA
							     	, TO_CHAR (FECHA_CONFIRMA, 'DD/MM/YYYY') FECHA_CONFIRMA_NO_ING
							     	, COD_USUARIO_CONFIRMA COD_USUARIO_CONFIRMA_NO_ING
							     	, USUARIO_CONFIRMA.NOM_USUARIO NOM_USUARIO_CONFIRMA
							     	, USUARIO_CONFIRMA.NOM_USUARIO NOM_USUARIO_CONFIRMA_NO_ING
							     	, USUARIO.NOM_USUARIO
							     	, TO_CHAR (FECHA_DECLARACION, 'DD/MM/YYYY') FECHA_DECLARACION
							     	, ANO ANO_DECLARACION
							     	, ANO ANO_DECLARACION_NO_ING
							     	, CON_HIJO
							     	, COD_AFP
							     	, DECLARACION.COD_ESTADO_CIVIL COD_ESTADO_CIVIL
							     	, RUT_CONYUGE
							     	, DIG_VERIF_CONYUGE
							     	, NOM_CONYUGE
							     	, CONYUGE_ES_DEUDOR
							     	, CONYUGE_DECLARA
							     	, ENTREGA_F22
							     	, 'none' VISIBLE_TABLE
							     	, 'none' VISIBLE_TABLE_CARTERA
							     	, (ANO + 1) ANO_COBRO
							     	, (ANO + 1) ANO_COBRO_H
							     	, 'none' VISIBLE_BOTON
							     	, '' VISIBLE_TD
							     	, '' DISABLE_BUTTON_CREA_CUOTA
							     	, '' VISIBLE_TR_NO_APROBADA
							     	, 'none' VISIBLE_TR_APROBADA
							     	, 'SIN CUOTA' LABEL_CUOTA
							     	, '' VALOR_CUOTA
							     	, '' TD_LABEL_CUOTA
							     	, 'none' TD_VALOR_CUOTA
							     	, COD_AFP_CONYUGE		
							     	, ENTREGA_F22_CONYUGE
							     	, f_get_constante('K_CHILE') K_CHILE_H 
							     	, CERT_SUELDO 
									, CERT_HONORARIO
									, CERT_AFP
									, DECLARA_RENTA
									, CERT_NOTARIAL_ING
									, ANTEC_CONYUGE
									, CERT_ALUM_REGULAR	
									, PRESENTA_POSTGRADO	
							  FROM 	DECLARACION
							     	, DEUDOR
							     	, USUARIO
							     	, USUARIO USUARIO_CONFIRMA
							 WHERE 	DECLARACION.COD_DEUDOR = DEUDOR.COD_DEUDOR
							   		AND DECLARACION.COD_USUARIO_CONFIRMA = USUARIO_CONFIRMA.COD_USUARIO(+)
							   		AND DECLARACION.COD_USUARIO = USUARIO.COD_USUARIO(+)
							   		AND DECLARACION.COD_DECLARACION = {KEY1}";
		$this->set_sql($sql);							   		
		
		$this->add_control(new edit_check_box('ENTREGA_F22_CONYUGE', 'S', 'N'));
		$this->add_control(new edit_check_box('PRESENTA_POSTGRADO', 'S', 'N'));
		
		$sql_afp= sql_declaracion::get_sql ('DD_COD_AFP');
		$this->add_control(new drop_down_dw('COD_AFP_CONYUGE', $sql_afp, 180));
		
	}  
}
?>