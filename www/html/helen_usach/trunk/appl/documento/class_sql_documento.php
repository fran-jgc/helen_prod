<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_documento extends sql_tabla {
	const K_PARAM_MAX_PRORROGA = 1;
	const K_EST_SIMULACION = 7;
	const K_EST_ANULADO = 8;
	const K_ESTADO_POR_DEPOSITAR = 1;
	
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			///////////////// INPUT /////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	DP.COD_DOCUMENTO_PAGO
									, DP.COD_DOCUMENTO_PAGO COD_DOCUMENTO_PAGO_PP
									, DP.NRO_DOCUMENTO_PAGO
							      	, f_doc_nro_ingreso(DP.COD_DOCUMENTO_PAGO) NRO_INGRESO
									, TO_CHAR(DP.FECHA_REGISTRO,'dd/mm/yyyy') FECHA_REGISTRO
									, TO_CHAR(DP.FECHA_DOCUMENTO_PAGO,'dd/mm/yyyy')FECHA_DOCUMENTO_PAGO
									, TO_CHAR (DP.FECHA_DOCUMENTO_PAGO, 'dd/mm/yyyy') FECHA_DOCUMENTO_PAGO_H
									, '' USUARIO_ACTUAL_H
									, DE.COD_DEUDOR
									, DE.RUT
									, DE.DIG_VERIF
									, DE.NOMBRE || ' ' || DE.APELLIDO_PATERNO || ' ' || DE.APELLIDO_MATERNO NOMBRE
									, TDP.NOM_TIPO_DOCUMENTO_PAGO
									, B.NOM_BANCO
									, P.NOM_PLAZA
									, DP.MONTO
									, COD_ESTADO_DOCUMENTO_PAGO
									, to_char(f_doc_fecha_deposito(DP.COD_DOCUMENTO_PAGO),'dd/mm/yyyy') FECHA_DEPOSITO
          							, NOM_FAMILIA_DEUDA
          							, RUT_GIRADOR
          							, DIG_VERIF_GIRADOR
          							, decode(COD_ESTADO_DOCUMENTO_PAGO, ".self::K_ESTADO_POR_DEPOSITAR.", '', 'none') VISIBLE_AGREGAR
							FROM 	DOCUMENTO_PAGO DP LEFT OUTER JOIN PLAZA P ON DP.COD_PLAZA = P.COD_PLAZA,
									DEUDOR DE, 
									BANCO B, 
									TIPO_DOCUMENTO_PAGO TDP, 
									FAMILIA_DEUDA FD
							WHERE 	DP.COD_DOCUMENTO_PAGO = {KEY1}
							AND 	DE.COD_DEUDOR = DP.COD_DEUDOR
							AND 	B.COD_BANCO = DP.COD_BANCO
							AND 	TDP.COD_TIPO_DOCUMENTO_PAGO = DP.COD_TIPO_DOCUMENTO_PAGO
							AND 	DP.COD_FAMILIA_DEUDA = FD.COD_FAMILIA_DEUDA";
          		}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 	DP.COD_DOCUMENTO_PAGO
									, DP.COD_DOCUMENTO_PAGO COD_DOCUMENTO_PAGO_PP
									, DP.NRO_DOCUMENTO_PAGO
							      	, dbo.f_doc_nro_ingreso(DP.COD_DOCUMENTO_PAGO) NRO_INGRESO
									, convert(varchar(20), DP.FECHA_REGISTRO, 103) FECHA_REGISTRO
									, convert(varchar(20), DP.FECHA_DOCUMENTO_PAGO, 103)FECHA_DOCUMENTO_PAGO
									, convert(varchar(20), DP.FECHA_DOCUMENTO_PAGO, 103) FECHA_DOCUMENTO_PAGO_H
									, '' USUARIO_ACTUAL_H
									, DE.COD_DEUDOR
									, DE.RUT
									, DE.DIG_VERIF
									, DE.NOMBRE + ' ' + DE.APELLIDO_PATERNO + ' ' + DE.APELLIDO_MATERNO NOMBRE
									, TDP.NOM_TIPO_DOCUMENTO_PAGO
									, B.NOM_BANCO
									, P.NOM_PLAZA
									, DP.MONTO
									, COD_ESTADO_DOCUMENTO_PAGO
									, convert(varchar(20), dbo.f_doc_fecha_deposito(DP.COD_DOCUMENTO_PAGO), 103) FECHA_DEPOSITO
									, NOM_FAMILIA_DEUDA
									, RUT_GIRADOR
									, DIG_VERIF_GIRADOR
          							,case COD_ESTADO_DOCUMENTO_PAGO
										when ".self::K_ESTADO_POR_DEPOSITAR." then ''
										else 'none'
									end VISIBLE_AGREGAR
							FROM 	DOCUMENTO_PAGO DP LEFT OUTER JOIN PLAZA P ON DP.COD_PLAZA = P.COD_PLAZA,
									DEUDOR DE, 
									BANCO B, 
									TIPO_DOCUMENTO_PAGO TDP, 
									FAMILIA_DEUDA FD
							WHERE 	DP.COD_DOCUMENTO_PAGO = {KEY1}
							AND 	DE.COD_DEUDOR = DP.COD_DEUDOR
							AND 	B.COD_BANCO = DP.COD_BANCO
							AND 	TDP.COD_TIPO_DOCUMENTO_PAGO = DP.COD_TIPO_DOCUMENTO_PAGO
							AND 	DP.COD_FAMILIA_DEUDA = FD.COD_FAMILIA_DEUDA";
				}
				break;
				
			//////////////////// OUTPUT //////////////////	
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	DP.COD_DOCUMENTO_PAGO
							      , DP.NRO_DOCUMENTO_PAGO
						      	  , f_doc_nro_ingreso(DP.COD_DOCUMENTO_PAGO) NRO_INGRESO
							      , TO_CHAR(DP.FECHA_REGISTRO,'dd/mm/yyyy') FECHA_REGISTRO
							      , TO_CHAR(DP.FECHA_DOCUMENTO_PAGO,'dd/mm/yyyy')FECHA_DOCUMENTO_PAGO
							      , D.RUT
							      , D.DIG_VERIF
								  , D.NOMBRE || ' ' || D.APELLIDO_PATERNO || ' ' || D.APELLIDO_MATERNO NOMBRE
							      , B.NOM_BANCO
							      , DP.MONTO
							      , TDP.NOM_TIPO_DOCUMENTO_PAGO
							      , EDP.NOM_ESTADO_DOCUMENTO_PAGO
							FROM 	DOCUMENTO_PAGO DP, DEUDOR D, BANCO B,
										TIPO_DOCUMENTO_PAGO TDP, ESTADO_DOCUMENTO_PAGO EDP
							WHERE D.COD_DEUDOR = DP.COD_DEUDOR
							      AND B.COD_BANCO = DP.COD_BANCO
							      AND TDP.COD_TIPO_DOCUMENTO_PAGO = DP.COD_TIPO_DOCUMENTO_PAGO
							      AND EDP.COD_ESTADO_DOCUMENTO_PAGO = DP.COD_ESTADO_DOCUMENTO_PAGO
								  AND DP.COD_ESTADO_DOCUMENTO_PAGO <> ".self::K_EST_SIMULACION."
								  AND DP.COD_ESTADO_DOCUMENTO_PAGO <> ".self::K_EST_ANULADO."
							ORDER BY COD_DOCUMENTO_PAGO DESC";
											}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 	DP.COD_DOCUMENTO_PAGO
							      	, DP.NRO_DOCUMENTO_PAGO
							      	, dbo.f_doc_nro_ingreso(DP.COD_DOCUMENTO_PAGO) NRO_INGRESO
							      	, convert(varchar(20), DP.FECHA_REGISTRO, 103) FECHA_REGISTRO
							      	, convert(varchar(20), DP.FECHA_DOCUMENTO_PAGO, 103)FECHA_DOCUMENTO_PAGO
							      	, D.RUT
							      	, D.DIG_VERIF
									, D.NOMBRE + ' ' + D.APELLIDO_PATERNO + ' ' + D.APELLIDO_MATERNO NOMBRE
							      	, B.NOM_BANCO
							      	, DP.MONTO
							      	, TDP.NOM_TIPO_DOCUMENTO_PAGO
							      	, EDP.NOM_ESTADO_DOCUMENTO_PAGO
							FROM 	DOCUMENTO_PAGO DP, DEUDOR D, BANCO B,
									TIPO_DOCUMENTO_PAGO TDP, ESTADO_DOCUMENTO_PAGO EDP
							WHERE 	D.COD_DEUDOR = DP.COD_DEUDOR
							AND 	B.COD_BANCO = DP.COD_BANCO
							AND 	TDP.COD_TIPO_DOCUMENTO_PAGO = DP.COD_TIPO_DOCUMENTO_PAGO
							AND 	EDP.COD_ESTADO_DOCUMENTO_PAGO = DP.COD_ESTADO_DOCUMENTO_PAGO
							AND		DP.COD_ESTADO_DOCUMENTO_PAGO <> ".self::K_EST_SIMULACION."
							AND		DP.COD_ESTADO_DOCUMENTO_PAGO <> ".self::K_EST_ANULADO."
						ORDER BY COD_DOCUMENTO_PAGO DESC";
				}
				break;
					
			/////////////// DD_COD_ESTADO_DOCUMENTO_PAGO ////////////////////	
			case 'DD_COD_ESTADO_DOCUMENTO_PAGO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
						$sql = "SELECT   COD_ESTADO_DOCUMENTO_PAGO, NOM_ESTADO_DOCUMENTO_PAGO
								FROM   ESTADO_DOCUMENTO_PAGO
								WHERE COD_ESTADO_DOCUMENTO_PAGO <> ".self::K_EST_SIMULACION."
								  AND COD_ESTADO_DOCUMENTO_PAGO <> ".self::K_EST_ANULADO."
								ORDER BY COD_ESTADO_DOCUMENTO_PAGO";
				}
				break;

			///////////////////// DW_PRORROGA_DOCUMENTO_PAGO //////////////////////////	
			case 'DW_PRORROGA_DOCUMENTO_PAGO' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT COD_PRORROGA_DOCUMENTO_PAGO
									,NRO_PRORROGA_DOCUMENTO_PAGO
									, TO_CHAR(FECHA_ANTERIOR,'dd/mm/yyyy') FECHA_ANTERIOR
									, TO_CHAR(FECHA_ANTERIOR,'dd/mm/yyyy') FECHA_ANTERIOR_H
									, TO_CHAR(FECHA_NUEVA,'dd/mm/yyyy') FECHA_NUEVA
									, 'N' IS_NEW
									, (	SELECT Count(COD_PRORROGA_DOCUMENTO_PAGO)
										FROM PRORROGA_DOCUMENTO_PAGO PDP, DOCUMENTO_PAGO DP
										WHERE PDP.COD_DOCUMENTO_PAGO=DP.COD_DOCUMENTO_PAGO AND DP.COD_DOCUMENTO_PAGO={KEY1})CANT_H
									, PDP.COD_DOCUMENTO_PAGO COD_DOCUMENTO_PAGO
									, PDP.COD_USUARIO COD_USUARIO
									, (SELECT f_get_parametro(".self::K_PARAM_MAX_PRORROGA.") FROM DUAL) CANT_MAX_PRORROGA_H
							FROM 	PRORROGA_DOCUMENTO_PAGO PDP, DOCUMENTO_PAGO DP
							WHERE 	PDP.COD_DOCUMENTO_PAGO=DP.COD_DOCUMENTO_PAGO AND DP.COD_DOCUMENTO_PAGO={KEY1}
							ORDER BY NRO_PRORROGA_DOCUMENTO_PAGO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT COD_PRORROGA_DOCUMENTO_PAGO
									,NRO_PRORROGA_DOCUMENTO_PAGO
									, convert(varchar(20), FECHA_ANTERIOR, 103) FECHA_ANTERIOR
									, convert(varchar(20), FECHA_ANTERIOR, 103) FECHA_ANTERIOR_H
									, convert(varchar(20), FECHA_NUEVA, 103) FECHA_NUEVA
									, 'N' IS_NEW
									, (	SELECT Count(COD_PRORROGA_DOCUMENTO_PAGO)
										FROM PRORROGA_DOCUMENTO_PAGO PDP, DOCUMENTO_PAGO DP
										WHERE PDP.COD_DOCUMENTO_PAGO=DP.COD_DOCUMENTO_PAGO AND DP.COD_DOCUMENTO_PAGO={KEY1})CANT_H
									, PDP.COD_DOCUMENTO_PAGO COD_DOCUMENTO_PAGO
									, PDP.COD_USUARIO COD_USUARIO
									, dbo.f_get_parametro(1) CANT_MAX_PRORROGA_H
							FROM 	PRORROGA_DOCUMENTO_PAGO PDP, DOCUMENTO_PAGO DP
							WHERE 	PDP.COD_DOCUMENTO_PAGO = DP.COD_DOCUMENTO_PAGO 
							AND		DP.COD_DOCUMENTO_PAGO = {KEY1}
							ORDER BY NRO_PRORROGA_DOCUMENTO_PAGO";
				}
				break;
				
			

			//////////////////// DD_COD_BANCO //////////////////////	
			case 'DD_COD_BANCO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select 	COD_BANCO 
								  	,NOM_BANCO 
							from	BANCO 
							order by COD_BANCO";
				}
				break;
				
			///////////////////// DD_COD_TIPO_DOCUMENTO_PAGO ////////////////	
			case 'DD_COD_TIPO_DOCUMENTO_PAGO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select 	COD_TIPO_DOCUMENTO_PAGO 
									,NOM_TIPO_DOCUMENTO_PAGO 
							from 	TIPO_DOCUMENTO_PAGO 
							order by COD_TIPO_DOCUMENTO_PAGO";
				}
				break;
				
			/////////////////  ERROR ///////////////
			default: 
				base::error("sql_documento::get_sql, selector: '$selector_sql' no definido");	
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>