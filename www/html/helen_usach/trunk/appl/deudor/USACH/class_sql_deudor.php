<?php

class sql_deudor extends sql_deudor_base {
	static function get_sql($selector_sql) {
		switch ($selector_sql) {
			/////////////////  INPUT ///////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	COD_DEUDOR
									,RUT
									,DIG_VERIF 
			   						,NOMBRE
			   						,APELLIDO_PATERNO
			   						,APELLIDO_MATERNO
			   						,SEXO
			   						,D.COD_ESTADO_CIVIL
			   						,NOM_ESTADO_CIVIL
			   						,TO_CHAR(FECHA_NACIM,'dd/mm/yyyy') FECHA_NACIM
			   						,TO_CHAR(FECHA_DEFUNCION,'dd/mm/yyyy') FECHA_DEFUNCION
			   						,0 ANO_DECLARACION_SELECCIONADA
			   						,'' TITULO_DEUDOR_CRUZADO
									,'none' VISIBLE_DEUDOR_CRUZADO
			   						,'none' VISIBLE_CAMPO
			   						,'DEUDA INTERNALIZADA' TITULO_DEUDOR_MOROSO
			   						, 'none' VISIBLE_CAMPO_PROTES
			   						, 'DEUDOR CON DOCUMENTOS PROTESTADOS' TITULO_DOC_PROTES
			   						, f_get_constante('K_CHILE') K_CHILE_H
			   						, D.PASSWORD PASSWORD_DEUDOR
			   						, D.COD_SITUACION_ACADEMICA
			   						, D.CANDIDATO_17_BIS CANDIDATO_17_BIS_S
			   						, D.CANDIDATO_17_BIS CANDIDATO_17_BIS_N 
			   						,case
			   						    when  f_repro_rcs_es_candidato(COD_DEUDOR,'WEBPAY') = 'N' THEN 'Deudor no cumple con los requisitos legales para ser candidato a reprogramar'
			   						    when  f_repro_rcs_es_candidato(COD_DEUDOR,'WEBPAY') = 'S' THEN 'Legalmente es candidato a la reprogramaci&oacute;n' 
			   						end MENSAJE
                    
							FROM 	DEUDOR D
									,ESTADO_CIVIL EC
							WHERE 	COD_DEUDOR = {KEY1}
									AND D.COD_ESTADO_CIVIL = EC.COD_ESTADO_CIVIL";
				}
				break;
				////////////////  ERROR ///////////////
			default: 
				$sql = sql_deudor_base::get_sql($selector_sql);
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>