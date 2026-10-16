<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_conciliacion extends sql_tabla {
	/////////////	DECLARACION DE CONSTANTES		/////////////////////////
	static function get_sql($selector_sql) {

	switch ($selector_sql){
		//////////////////////INPUT//////////////////////
		case 'INPUT' :
			if (K_TIPO_BD=="oci"){
				$sql= 	"SELECT	C.COD_CONCILIACION
        						,To_Char(C.FECHA_CONCILIACION,'dd/mm/yyyy') FECHA_CONCILIACION
        						,To_Char(C.FECHA_CONCILIACION,'dd/mm/yyyy') FECHA_CONCILIACION_NO_ING
			  					,C.COD_USUARIO
			  					,U.NOM_USUARIO
			  					,C.COD_ESTADO_CONCILIACION
			  					,C.MES
			  					,C.MES MES_NO_ING
			  					, '' MES_ACTUAL
			  					, '' ANO_ACTUAL
			  					, '' MES_ANTERIOR
			  					, '' ANO_ANTERIOR
			  					,C.ANO
			  					,C.ANO ANO_NO_ING
        						,CASE C.MES
          							WHEN 1  THEN 12
          							ELSE  C.MES - 1  
         						END MES_ANTERIOR
        						,CASE C.MES
          							WHEN 1  THEN C.ANO - 1
          							ELSE  C.ANO  
         						END ANO_ANTERIOR
         						,C.MES	MES_ACTUAL
         						,C.ANO	ANO_ACTUAL
			  					,C.COD_CUENTA_CORRIENTE
			  					,NOM_CUENTA_CORRIENTE
			  					,Nvl(C.SALDO_ANTERIOR,0) MONTO_ANTERIOR_RESUMEN
			  					,Nvl(C.SALDO_ANTERIOR,0) MONTO_ANTERIOR_RESUMEN_H
			  					/*,C.INGRESO_MES
			  					,C.EGRESO_MES
			  					,C.CHEQUES_MES*/
				            	,f_get_monto_comprobante(C.MES,C.ANO,{PARAM1},C.COD_CUENTA_CORRIENTE) INGRESO_MES
				            	,f_get_monto_comprobante(C.MES,C.ANO,{PARAM1},C.COD_CUENTA_CORRIENTE) INGRESO_MES_H
				            	,f_get_monto_comprobante(C.MES,C.ANO,{PARAM1},C.COD_CUENTA_CORRIENTE) INGRESO_MES_RESUMEN
			                    ,f_get_monto_comprobante(C.MES,C.ANO,{PARAM2},C.COD_CUENTA_CORRIENTE) EGRESO_MES
			                    ,f_get_monto_comprobante(C.MES,C.ANO,{PARAM2},C.COD_CUENTA_CORRIENTE) EGRESO_MES_H
			                    ,f_get_monto_comprobante(C.MES,C.ANO,{PARAM2},C.COD_CUENTA_CORRIENTE) EGRESO_MES_RESUMEN
			                    ,f_get_monto_conciliacion(C.COD_CONCILIACION,'{PARAM3}') CARTOLA_INGRESO_MES
			                    ,f_get_monto_conciliacion(C.COD_CONCILIACION,'{PARAM3}') CARTOLA_INGRESO_MES_H
			  					,f_get_monto_conciliacion(C.COD_CONCILIACION,'{PARAM4}') CARTOLA_EGRESO_MES
			  					,f_get_monto_conciliacion(C.COD_CONCILIACION,'{PARAM4}') CARTOLA_EGRESO_MES_H
			  					,f_get_monto_conciliacion(C.COD_CONCILIACION,'{PARAM5}') CARTOLA_CHEQUE_MES
			  					,f_get_monto_conciliacion(C.COD_CONCILIACION,'{PARAM5}') CARTOLA_CHEQUE_MES_H
			  					,CASE 
			    					WHEN INGRESO_MES <> f_get_monto_conciliacion(C.COD_CONCILIACION,'{PARAM3}') THEN  '#FF0000'
			    					ELSE '#000000'
			  					END COLOR_INGRESO
			  					,CASE 
			    					WHEN EGRESO_MES <> f_get_monto_conciliacion(C.COD_CONCILIACION,'{PARAM4}') THEN  '#FF0000'
			    					ELSE '#000000'
			  					END COLOR_EGRESO
              			FROM 	CONCILIACION 		C
              					,USUARIO			U
              					,CUENTA_CORRIENTE 	CC
						WHERE   COD_CONCILIACION = {KEY1}
								AND C.COD_USUARIO = U.COD_USUARIO
								AND C.COD_CUENTA_CORRIENTE = CC.COD_CUENTA_CORRIENTE";
			}
			else if (K_TIPO_BD=="mssql"){
				$sql= "SELECT	C.COD_CONCILIACION
        						,convert(varchar(20), C.FECHA_CONCILIACION, 103) FECHA_CONCILIACION
        						,convert(varchar(20), C.FECHA_CONCILIACION, 103) FECHA_CONCILIACION_NO_ING
			  					,C.COD_USUARIO
			  					,U.NOM_USUARIO
			  					,C.COD_ESTADO_CONCILIACION
			  					,C.MES
			  					,C.MES MES_NO_ING
			  					, '' MES_ACTUAL
			  					, '' ANO_ACTUAL
			  					, '' MES_ANTERIOR
			  					, '' ANO_ANTERIOR
			  					,C.ANO
			  					,C.ANO ANO_NO_ING
        						,CASE C.MES
          							WHEN 1  THEN 12
          							ELSE  C.MES - 1  
         						END MES_ANTERIOR
        						,CASE C.MES
          							WHEN 1  THEN C.ANO - 1
          							ELSE  C.ANO  
         						END ANO_ANTERIOR
         						,C.MES	MES_ACTUAL
         						,C.ANO	ANO_ACTUAL
			  					,C.COD_CUENTA_CORRIENTE
			  					,NOM_CUENTA_CORRIENTE
			  					,isnull(C.SALDO_ANTERIOR,0) MONTO_ANTERIOR_RESUMEN
			  					,isnull(C.SALDO_ANTERIOR,0) MONTO_ANTERIOR_RESUMEN_H
			  					/*,C.INGRESO_MES
			  					,C.EGRESO_MES
			  					,C.CHEQUES_MES*/
				            	,dbo.f_get_monto_comprobante(C.MES,C.ANO,{PARAM1},C.COD_CUENTA_CORRIENTE) INGRESO_MES
				            	,dbo.f_get_monto_comprobante(C.MES,C.ANO,{PARAM1},C.COD_CUENTA_CORRIENTE) INGRESO_MES_H
				            	,dbo.f_get_monto_comprobante(C.MES,C.ANO,{PARAM1},C.COD_CUENTA_CORRIENTE) INGRESO_MES_RESUMEN
			                    ,dbo.f_get_monto_comprobante(C.MES,C.ANO,{PARAM2},C.COD_CUENTA_CORRIENTE) EGRESO_MES
			                    ,dbo.f_get_monto_comprobante(C.MES,C.ANO,{PARAM2},C.COD_CUENTA_CORRIENTE) EGRESO_MES_H
			                    ,dbo.f_get_monto_comprobante(C.MES,C.ANO,{PARAM2},C.COD_CUENTA_CORRIENTE) EGRESO_MES_RESUMEN
			                    ,dbo.f_get_monto_conciliacion(C.COD_CONCILIACION,'{PARAM3}') CARTOLA_INGRESO_MES
			                    ,dbo.f_get_monto_conciliacion(C.COD_CONCILIACION,'{PARAM3}') CARTOLA_INGRESO_MES_H
			  					,dbo.f_get_monto_conciliacion(C.COD_CONCILIACION,'{PARAM4}') CARTOLA_EGRESO_MES
			  					,dbo.f_get_monto_conciliacion(C.COD_CONCILIACION,'{PARAM4}') CARTOLA_EGRESO_MES_H
			  					,dbo.f_get_monto_conciliacion(C.COD_CONCILIACION,'{PARAM5}') CARTOLA_CHEQUE_MES
			  					,dbo.f_get_monto_conciliacion(C.COD_CONCILIACION,'{PARAM5}') CARTOLA_CHEQUE_MES_H
			  					,CASE 
			    					WHEN INGRESO_MES <> dbo.f_get_monto_conciliacion(C.COD_CONCILIACION,'{PARAM3}') THEN  '#FF0000'
			    					ELSE '#000000'
			  					END COLOR_INGRESO
			  					,CASE 
			    					WHEN EGRESO_MES <> dbo.f_get_monto_conciliacion(C.COD_CONCILIACION,'{PARAM4}') THEN  '#FF0000'
			    					ELSE '#000000'
			  					END COLOR_EGRESO
              			FROM 	CONCILIACION 		C
              					,USUARIO			U
              					,CUENTA_CORRIENTE 	CC
						WHERE   COD_CONCILIACION = {KEY1}
								AND C.COD_USUARIO = U.COD_USUARIO
								AND C.COD_CUENTA_CORRIENTE = CC.COD_CUENTA_CORRIENTE";
			}
		break;
				
		//////////////////////OUTPUT//////////////////////
		case 'OUTPUT' :
			if (K_TIPO_BD=="oci"){
				$sql= 	"SELECT  	COD_CONCILIACION
									,NOM_USUARIO
									,to_char(FECHA_CONCILIACION,'dd/mm/yyyy') FECHA_CONCILIACION
									,NOM_CUENTA_CORRIENTE
									,MES
									,ANO
						FROM    	CONCILIACION C
						          	,USUARIO U
						          	,CUENTA_CORRIENTE CC
						WHERE     	C.COD_USUARIO = U.COD_USUARIO
						          	AND CC.COD_CUENTA_CORRIENTE =C.COD_CUENTA_CORRIENTE
						ORDER BY	COD_CONCILIACION DESC";
			}
			else if (K_TIPO_BD=="mssql"){
				$sql= "SELECT  	COD_CONCILIACION
									,NOM_USUARIO
									,CONVERT(VARCHAR(20),FECHA_CONCILIACION,103) FECHA_CONCILIACION
									,NOM_CUENTA_CORRIENTE
									,MES
									,ANO
						FROM    	CONCILIACION C
						          	,USUARIO U
						          	,CUENTA_CORRIENTE CC
						WHERE     	C.COD_USUARIO = U.COD_USUARIO
						          	AND CC.COD_CUENTA_CORRIENTE =C.COD_CUENTA_CORRIENTE
						ORDER BY	COD_CONCILIACION DESC";
			}
		break;
		//////////////////////DD_COD_USUARIO//////////////////////
		case 'DD_COD_USUARIO' :
			if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
				$sql=  "SELECT	COD_USUARIO
								,NOM_USUARIO
						FROM 	USUARIO";
			}
		break;
		//////////////////////DD_CTACTE//////////////////////
		case 'DD_CTACTE' :
			if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
				$sql=  "SELECT	COD_CUENTA_CORRIENTE
								,NOM_CUENTA_CORRIENTE
						FROM 	CUENTA_CORRIENTE";
			}
		break;
		//////////////////////CONCILIACION_INGRESO//////////////////////
		case 'CONCILIACION_ING' :
			if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
				$sql=  "SELECT  COD_CONCILIACION_INGRESO	COD_CONCILIACION_INGRESO_H 
						        ,COD_CONCILIACION			COD_CONCILIACION_ING_H
						        ,NOM_CONCILIACION_INGRESO
						        ,MONTO_INGRESO
						FROM    CONCILIACION_INGRESO
						WHERE   COD_CONCILIACION = {KEY1}";
			}
		break;
		//////////////////////CONCILIACION_EGRESO//////////////////////
		case 'CONCILIACION_EGR' :
			if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
				$sql=  "SELECT  COD_CONCILIACION_EGRESO COD_CONCILIACION_EGRESO_H
						        ,COD_CONCILIACION		COD_CONCILIACION_EGR_H
						        ,NRO_CHEQUE	NRO_CHEQUE_EGRESO
						        ,NOM_CONCILIACION_EGRESO
						        ,MONTO_EGRESO
						FROM    CONCILIACION_EGRESO
						WHERE   COD_CONCILIACION = {KEY1}";
			}
		break;
		//////////////////////CONCILIACION_CHEQUES//////////////////////
		case 'CONCILIACION_CHE' :
			if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
				$sql=  "SELECT  COD_CONCILIACION_CHEQUE	COD_CONCILIACION_CHEQUE_H
						        ,COD_CONCILIACION		COD_CONCILIACION_CHE_H
						        ,NRO_CHEQUE		NRO_CHEQUE_CHEQUE
						        ,NOM_CONCILIACION_CHEQUE
						        ,MONTO_CHEQUE
						FROM    CONCILIACION_CHEQUE
						WHERE   COD_CONCILIACION = {KEY1}";
			}
		break;
		//////////////////////DD_ESTADO_CONCILIACION//////////////////////
		case 'DD_ESTADO_CONCILIACION' :
			if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
				$sql=  "SELECT  COD_ESTADO_CONCILIACION
						        ,NOM_ESTADO_CONCILIACION
						FROM    ESTADO_CONCILIACION";
			}
		break;
		//////////////////////NEW_CONCILIACION_INGRESO//////////////////////
		case 'NEW_CONCILIACION_INGRESO' :
			if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
				$sql=  "SELECT		'' 	COD_CONCILIACION_INGRESO_H 
          							,''	COD_CONCILIACION_ING_H
									,CC.NOM_CONCEPTO_CONCILIACION	  NOM_CONCILIACION_INGRESO
			    					,0	MONTO_INGRESO
						FROM   	  	CONCEPTO_CONCILIACION CC
						WHERE  	  	TIPO_CONCEPTO_CONCILIACION = '{PARAM1}'
						ORDER BY	COD_CONCEPTO_CONCILIACION";
			}
		break;
		//////////////////////NEW_CONCILIACION_EGRESO//////////////////////
		case 'NEW_CONCILIACION_EGRESO' :
			if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
				$sql=  "SELECT		'' 	COD_CONCILIACION_EGRESO_H 
          							,''	COD_CONCILIACION_EGR_H
									,'' NRO_CHEQUE_EGRESO
									,CC.NOM_CONCEPTO_CONCILIACION	NOM_CONCILIACION_EGRESO
			                    	,0 MONTO_EGRESO
			             FROM   	CONCEPTO_CONCILIACION CC
			             WHERE  	TIPO_CONCEPTO_CONCILIACION = '{PARAM1}'
			             ORDER BY	COD_CONCEPTO_CONCILIACION";
			}
		break;
		//////////////////////NEW_CONCILIACION_CHEQUE//////////////////////
		case 'NEW_CONCILIACION_CHEQUE' :
			if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
				$sql=  "SELECT		'' 	COD_CONCILIACION_CHEQUE_H 
          							,''	COD_CONCILIACION_CHE_H
									,'' NRO_CHEQUE_CHEQUE
									,CC.NOM_CONCEPTO_CONCILIACION	NOM_CONCILIACION_CHEQUE
			                    	,0 MONTO_CHEQUE
			             FROM   	CONCEPTO_CONCILIACION CC
			             WHERE  	TIPO_CONCEPTO_CONCILIACION = '{PARAM1}'
			             ORDER BY	COD_CONCEPTO_CONCILIACION";
			}
		break;
		//////////////////////AJAX_EXISTE_CONCILIACION//////////////////////
		case 'AJAX_EXISTE_CONCILIACION' :
			if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
				$sql=  "SELECT  COD_CONCILIACION
						FROM    CONCILIACION
						WHERE   MES = {PARAM1}
						        AND ANO = {PARAM2}
						        AND COD_CUENTA_CORRIENTE = {PARAM3}";
			}
		break;
		//////////////////////AJAX_SET_VALORES//////////////////////
		case 'AJAX_SET_VALORES' :
			if (K_TIPO_BD=="oci"){
				$sql=  "SELECT	f_get_monto_comprobante({PARAM1},{PARAM2},{PARAM3},{PARAM4})  INGRESO_MES
        						,f_get_monto_comprobante({PARAM1},{PARAM2},{PARAM5},{PARAM4}) EGRESO_MES
        						,f_get_conciliacion_anterior({PARAM1},{PARAM2})  CONCILIACION_ANTERIOR
						FROM    DUAL";
			}
			else if (K_TIPO_BD=="mssql"){
				$sql= "SELECT	dbo.f_get_monto_comprobante({PARAM1},{PARAM2},{PARAM3},{PARAM4})  INGRESO_MES
        						,dbo.f_get_monto_comprobante({PARAM1},{PARAM2},{PARAM5},{PARAM4}) EGRESO_MES
        						,dbo.f_get_conciliacion_anterior({PARAM1},{PARAM2})  CONCILIACION_ANTERIOR";
			}
		break;
		//////////////////////ANO_ACTUAL//////////////////////
		case 'ANO_ACTUAL' :
			if (K_TIPO_BD=="oci"){
				$sql=  "SELECT	to_char(sysdate,'yyyy') ANO_ACTUAL
						FROM    DUAL";
			}
			else if (K_TIPO_BD=="mssql"){
				$sql= "SELECT	convert(varchar(4), datepart(year, getdate())) ANO_ACTUAL";
			}
		break;
		///////////////////PRINT_RECORD//////////////////////
		case 'PRINT_RECORD' :
			if (K_TIPO_BD=="oci"){
				$sql= 	"SELECT	C.COD_CONCILIACION
        					,To_Char(C.FECHA_CONCILIACION,'dd/mm/yyyy') FECHA_CONCILIACION
		  					,C.COD_USUARIO
		  					,U.NOM_USUARIO			  					
		  					,C.MES
		  					,C.ANO
        					,CASE C.MES
          						WHEN 1  THEN 12
          						ELSE  C.MES - 1  
         					END MES_ANTERIOR
        					,CASE C.MES
          						WHEN 1  THEN C.ANO - 1
          						ELSE  C.ANO  
         					END ANO_ANTERIOR
         					,C.MES	MES_ACTUAL
         					,C.ANO	ANO_ACTUAL
		  					,NOM_CUENTA_CORRIENTE
		  					,C.SALDO_ANTERIOR MONTO_ANTERIOR_RESUMEN
		  					/*,C.INGRESO_MES
		  					,C.EGRESO_MES
		  					,C.CHEQUES_MES*/
			        	  	,f_get_monto_comprobante(C.MES,C.ANO,{PARAM2},C.COD_CUENTA_CORRIENTE) INGRESO_MES
			       		    ,f_get_monto_comprobante(C.MES,C.ANO,{PARAM2},C.COD_CUENTA_CORRIENTE) INGRESO_MES_RESUMEN
		           		    ,f_get_monto_comprobante(C.MES,C.ANO,{PARAM3},C.COD_CUENTA_CORRIENTE) EGRESO_MES
		          		    ,f_get_monto_comprobante(C.MES,C.ANO,{PARAM3},C.COD_CUENTA_CORRIENTE) EGRESO_MES_RESUMEN
		          		    ,f_get_monto_conciliacion(C.COD_CONCILIACION,'I') CARTOLA_INGRESO_MES
		  				    ,f_get_monto_conciliacion(C.COD_CONCILIACION,'E') CARTOLA_EGRESO_MES
		  			    	,f_get_monto_conciliacion(C.COD_CONCILIACION,'C') CARTOLA_CHEQUE_MES
		  					,CASE 
		    					WHEN INGRESO_MES <> f_get_monto_conciliacion(C.COD_CONCILIACION,'I') THEN  '#FF0000'
		    					ELSE '#000000'
		  					END COLOR_INGRESO
		  					,CASE 
		    					WHEN EGRESO_MES <> f_get_monto_conciliacion(C.COD_CONCILIACION,'E') THEN  '#FF0000'
		    					ELSE '#000000'
		  					END COLOR_EGRESO
			                ,To_Number(SALDO_ANTERIOR + f_get_monto_conciliacion(C.COD_CONCILIACION,'I') - f_get_monto_conciliacion(C.COD_CONCILIACION,'E'))   MONTO_MES_ACTUAL
			                ,To_Number(SALDO_ANTERIOR + f_get_monto_conciliacion(C.COD_CONCILIACION,'I') - f_get_monto_conciliacion(C.COD_CONCILIACION,'E')) + f_get_monto_conciliacion(C.COD_CONCILIACION,'C') SALDO_SEGUN_CARTOLA
	            FROM 	CONCILIACION 		C
	              		,USUARIO			U
	              		,CUENTA_CORRIENTE 	CC
				WHERE    C.COD_CONCILIACION = {PARAM1}
					AND C.COD_USUARIO = U.COD_USUARIO
					AND C.COD_CUENTA_CORRIENTE = CC.COD_CUENTA_CORRIENTE";
			}			
			else if (K_TIPO_BD=="mssql"){
				$sql="SELECT	C.COD_CONCILIACION
        					,convert(varchar(20), C.FECHA_CONCILIACION, 103) FECHA_CONCILIACION
		  					,C.COD_USUARIO
		  					,U.NOM_USUARIO			  					
		  					,C.MES
		  					,C.ANO
        					,CASE C.MES
          						WHEN 1  THEN 12
          						ELSE  C.MES - 1  
         					END MES_ANTERIOR
        					,CASE C.MES
          						WHEN 1  THEN C.ANO - 1
          						ELSE  C.ANO  
         					END ANO_ANTERIOR
         					,C.MES	MES_ACTUAL
         					,C.ANO	ANO_ACTUAL
		  					,NOM_CUENTA_CORRIENTE
		  					,C.SALDO_ANTERIOR MONTO_ANTERIOR_RESUMEN
		  					,dbo.f_get_monto_comprobante(C.MES,C.ANO,{PARAM2},C.COD_CUENTA_CORRIENTE) INGRESO_MES
			       		    ,dbo.f_get_monto_comprobante(C.MES,C.ANO,{PARAM2},C.COD_CUENTA_CORRIENTE) INGRESO_MES_RESUMEN
		           		    ,dbo.f_get_monto_comprobante(C.MES,C.ANO,{PARAM3},C.COD_CUENTA_CORRIENTE) EGRESO_MES
		          		    ,dbo.f_get_monto_comprobante(C.MES,C.ANO,{PARAM3},C.COD_CUENTA_CORRIENTE) EGRESO_MES_RESUMEN
		          		    ,dbo.f_get_monto_conciliacion(C.COD_CONCILIACION,'I') CARTOLA_INGRESO_MES
		  				    ,dbo.f_get_monto_conciliacion(C.COD_CONCILIACION,'E') CARTOLA_EGRESO_MES
		  			    	,dbo.f_get_monto_conciliacion(C.COD_CONCILIACION,'C') CARTOLA_CHEQUE_MES
		  					,CASE 
		    					WHEN INGRESO_MES <> dbo.f_get_monto_conciliacion(C.COD_CONCILIACION,'I') THEN  '#FF0000'
		    					ELSE '#000000'
		  					END COLOR_INGRESO
		  					,CASE 
		    					WHEN EGRESO_MES <> dbo.f_get_monto_conciliacion(C.COD_CONCILIACION,'E') THEN  '#FF0000'
		    					ELSE '#000000'
		  					END COLOR_EGRESO
			                ,SALDO_ANTERIOR + dbo.f_get_monto_conciliacion(C.COD_CONCILIACION,'I') - dbo.f_get_monto_conciliacion(C.COD_CONCILIACION,'E')   MONTO_MES_ACTUAL
			                ,SALDO_ANTERIOR + dbo.f_get_monto_conciliacion(C.COD_CONCILIACION,'I') - dbo.f_get_monto_conciliacion(C.COD_CONCILIACION,'E') + dbo.f_get_monto_conciliacion(C.COD_CONCILIACION,'C') SALDO_SEGUN_CARTOLA
				FROM 	CONCILIACION 		C
	              		,USUARIO			U
	              		,CUENTA_CORRIENTE 	CC
				WHERE    C.COD_CONCILIACION = {PARAM1}
					AND C.COD_USUARIO = U.COD_USUARIO
					AND C.COD_CUENTA_CORRIENTE = CC.COD_CUENTA_CORRIENTE";
			}
		break;	
		
		/////////////////////////////ERROR/////////////////////////////////////
		default:
		base::error("sql_conciliacion::get_sql, selector: '$selector_sql' no definido");
	}
	$keys = func_get_args();
	$sql = sql_tabla::replace_KEYS($sql, $keys);
	return $sql;
	}
}
?>