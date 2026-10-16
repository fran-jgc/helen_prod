<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_prorroga extends sql_tabla {
	
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			///////////////// INPUT ///////////////
			case 'INPUT' :
				///////////////// ORACLE ///////////////
				if (K_TIPO_BD=="oci"){
					$sql =	"SELECT	P.COD_PRORROGA
									,To_Char(P.FECHA_PRORROGA, 'dd/mm/yyyy') FECHA_PRORROGA
									,P.COD_USUARIO
									,U.NOM_USUARIO
									,DR.RUT
									,DR.DIG_VERIF
									,DR.NOMBRE || ' ' || DR.APELLIDO_PATERNO || ' '  || DR.APELLIDO_MATERNO NOMBRE
									,DA.COD_TIPO_DEUDA
									,DA.COD_DEUDA
									,DA.COD_DEUDA COD_DEUDA_H
									,To_Char(P.FECHA_INICIO_COBRO, 'dd/mm/yyyy') FECHA_INICIO_COBRO
									,P.COD_ESTADO_PRORROGA
									,EP.NOM_ESTADO_PRORROGA
									,P.COD_COMPROBANTE
									,P.OBS
									,P.COD_USUARIO_CONFIRMA
									,UC.NOM_USUARIO NOM_USUARIO_CONFIRMA
									,To_Char(P.FECHA_CONFIRMA, 'dd/mm/yyyy') FECHA_CONFIRMA
									, 'none' VISIBLE_CAMPO								-- SOLO_USACH
									, TO_CHAR(P.FECHA_SOLICITUD_TRAMITE,'dd/mm/yyyy') FECHA_SOLICITUD_TRAMITE
							FROM	PRORROGA P, USUARIO U, DEUDOR DR, DEUDA DA, ESTADO_PRORROGA EP, USUARIO UC
							WHERE	P.COD_PRORROGA = {KEY1}
							AND		P.COD_DEUDA = DA.COD_DEUDA
							AND		DA.COD_DEUDOR = DR.COD_DEUDOR
							AND		P.COD_USUARIO = U.COD_USUARIO
							AND		P.COD_ESTADO_PRORROGA = EP.COD_ESTADO_PRORROGA
							AND		UC.COD_USUARIO (+)= P.COD_USUARIO_CONFIRMA";
				}
				///////////////// SQL-SERVER ///////////////
				else if (K_TIPO_BD=="mssql"){
					if(K_CLIENTE == 'UTEM'){
						$sql = "SELECT	P.COD_PRORROGA
									,convert(varchar(20), P.FECHA_PRORROGA, 103) FECHA_PRORROGA
									,DR.COD_DEUDOR
									,P.COD_USUARIO
									,U.NOM_USUARIO
									,DR.RUT
									,DR.DIG_VERIF
									,DR.NOMBRE + ' ' + DR.APELLIDO_PATERNO + ' '  + DR.APELLIDO_MATERNO NOMBRE
									,DA.COD_TIPO_DEUDA
									,DA.COD_TIPO_DEUDA COD_TIPO_DEUDA_H
									,DA.COD_DEUDA
									,DA.COD_DEUDA COD_DEUDA_H
									,convert(varchar(20), P.FECHA_INICIO_COBRO, 103) FECHA_INICIO_COBRO
									,convert(varchar(20), P.FECHA_INICIO_COBRO, 103) FECHA_INICIO_COBRO_H
									,P.COD_ESTADO_PRORROGA
									,EP.NOM_ESTADO_PRORROGA
									,P.COD_COMPROBANTE
									,P.OBS
									,P.COD_USUARIO_CONFIRMA
									,UC.NOM_USUARIO NOM_USUARIO_CONFIRMA
									,convert(varchar(20), P.FECHA_CONFIRMA, 103) FECHA_CONFIRMA
									,convert(varchar(20), P.FECHA_SOLICITUD_TRAMITE, 103) FECHA_SOLICITUD_TRAMITE
									,P.COD_UNIVERSIDAD
									,P.NOM_UNIVERSIDAD_OTRAS
									,P.ANO_MATRICULA
									,'' ULTIMA_MATRICULA_H
									,CASE ISNULL(P.NOM_UNIVERSIDAD_OTRAS,'0')
										when '0' then 'none'
										else ''
									end VISIBLE_CAMPO
							FROM	PRORROGA P left outer join USUARIO UC on P.COD_USUARIO_CONFIRMA = UC.COD_USUARIO
									, USUARIO U, DEUDOR DR, DEUDA DA, ESTADO_PRORROGA EP 
							WHERE	P.COD_PRORROGA = {KEY1}
							AND		P.COD_DEUDA = DA.COD_DEUDA
							AND		DA.COD_DEUDOR = DR.COD_DEUDOR
							AND		P.COD_USUARIO = U.COD_USUARIO
							AND		P.COD_ESTADO_PRORROGA = EP.COD_ESTADO_PRORROGA";
					}
					else{
						$sql = "SELECT	P.COD_PRORROGA
									,convert(varchar(20), P.FECHA_PRORROGA, 103) FECHA_PRORROGA
									,P.COD_USUARIO
									,U.NOM_USUARIO
									,DR.RUT
									,DR.DIG_VERIF
									,DR.NOMBRE + ' ' + DR.APELLIDO_PATERNO + ' '  + DR.APELLIDO_MATERNO NOMBRE
									,DA.COD_TIPO_DEUDA
									,DA.COD_DEUDA
									,DA.COD_DEUDA COD_DEUDA_H
									,convert(varchar(20), P.FECHA_INICIO_COBRO, 103) FECHA_INICIO_COBRO
									,P.COD_ESTADO_PRORROGA
									,EP.NOM_ESTADO_PRORROGA
									,P.COD_COMPROBANTE
									,P.OBS
									,P.COD_USUARIO_CONFIRMA
									,UC.NOM_USUARIO NOM_USUARIO_CONFIRMA
									,convert(varchar(20), P.FECHA_CONFIRMA, 103) FECHA_CONFIRMA
									,convert(varchar(20), P.FECHA_SOLICITUD_TRAMITE, 103) FECHA_SOLICITUD_TRAMITE
									,dbo.f_monto_deuda_prorroga(P.COD_DEUDA, P.COD_COMPROBANTE) MONTO_DEUDA
							FROM	PRORROGA P left outer join USUARIO UC on P.COD_USUARIO_CONFIRMA = UC.COD_USUARIO
									, USUARIO U, DEUDOR DR, DEUDA DA, ESTADO_PRORROGA EP 
							WHERE	P.COD_PRORROGA = {KEY1}
							AND		P.COD_DEUDA = DA.COD_DEUDA
							AND		DA.COD_DEUDOR = DR.COD_DEUDOR
							AND		P.COD_USUARIO = U.COD_USUARIO
							AND		P.COD_ESTADO_PRORROGA = EP.COD_ESTADO_PRORROGA";
					}
				}
			break;
				
			///////////////// INPUT_DD_COD_DEUDA ///////////////
			case 'INPUT_DD_COD_DEUDA' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	D.COD_DEUDA
									, D.COD_TIPO_DEUDA V
							FROM	DEUDA D, DEUDOR DR
									, TIPO_DEUDA TD
							WHERE	DR.RUT = {KEY1}
							AND		D.COD_DEUDOR = DR.COD_DEUDOR
							AND 	TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA";
				}
			break;
				
			///////////////// OUTPUT///////////////
			case 'OUTPUT' :
				///////////////// ORACLE ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	P.COD_PRORROGA
									, TO_CHAR(P.FECHA_CONFIRMA,'dd/mm/yyyy') FECHA_CONFIRMA
									, TO_CHAR(P.FECHA_PRORROGA,'dd/mm/yyyy') FECHA_PRORROGA 
									, TO_CHAR(P.FECHA_INICIO_COBRO,'dd/mm/yyyy') FECHA_INICIO_COBRO
									, DR.RUT
									, DR.DIG_VERIF
									, DR.NOMBRE
									, DR.APELLIDO_PATERNO
									, DR.APELLIDO_MATERNO
									, EP.NOM_ESTADO_PRORROGA
									, TO_CHAR(P.FECHA_SOLICITUD_TRAMITE,'dd/mm/yyyy') FECHA_SOLICITUD_TRAMITE
									, EP.COD_ESTADO_PRORROGA
							FROM	PRORROGA P
									, DEUDOR DR
									, DEUDA D
									, ESTADO_PRORROGA EP
							WHERE	P.COD_DEUDA = D.COD_DEUDA
							AND		DR.COD_DEUDOR = D.COD_DEUDOR
							AND		P.COD_ESTADO_PRORROGA = EP.COD_ESTADO_PRORROGA
							ORDER BY	P.COD_PRORROGA DESC";
				}
				///////////////// SQL-SERVER  ///////////////
				else if (K_TIPO_BD=="mssql"){
						$sql = "SELECT	P.COD_PRORROGA
										, convert (varchar (10), P.FECHA_PRORROGA,103) FECHA_PRORROGA
										, DR.RUT
										, DR.DIG_VERIF
										, DR.NOMBRE
										, DR.APELLIDO_PATERNO
										, DR.APELLIDO_MATERNO
										,convert(varchar(20), P.FECHA_INICIO_COBRO, 103) FECHA_INICIO_COBRO
										, EP.NOM_ESTADO_PRORROGA
										, convert (varchar (10), P.FECHA_SOLICITUD_TRAMITE,103) FECHA_SOLICITUD_TRAMITE
										, dbo.f_monto_deuda_prorroga(P.COD_DEUDA, P.COD_COMPROBANTE) MONTO_DEUDA
										, EP.COD_ESTADO_PRORROGA
								FROM	PRORROGA P
										, DEUDOR DR
										, DEUDA D
										, ESTADO_PRORROGA EP
								WHERE	P.COD_DEUDA = D.COD_DEUDA
								AND		DR.COD_DEUDOR = D.COD_DEUDOR
								AND		P.COD_ESTADO_PRORROGA = EP.COD_ESTADO_PRORROGA
								ORDER BY	P.COD_PRORROGA DESC";
				}
			break;				
				
			///////////////// OUTPUT_DD_COD_ESTADO ///////////////
			case 'OUTPUT_DD_COD_ESTADO' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT		COD_ESTADO_PRORROGA
										,NOM_ESTADO_PRORROGA
							FROM		ESTADO_PRORROGA
							ORDER BY	COD_ESTADO_PRORROGA";
				}
			break;
			
			///////////////// AJAX_DEUDOR ///////////////
			case 'AJAX_DEUDOR' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	count(*) CUANTO
							FROM	DEUDOR
							WHERE	RUT = {PARAM1}";
				}
			break;
			
			case 'AJAX_EXIGIBLE_NO_EXIGIBLE' :
				///////////////// ORACLE ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT COUNT(*) CANT
							FROM   DEUDOR DR, 
								   DEUDA DA
							WHERE  DR.RUT = {PARAM1}
							AND    DA.COD_TIPO_DEUDA  <> 'CSR'
							AND    DA.COD_ESTADO_DEUDA  IN ({PARAM2}, {PARAM3})
							AND    DR.COD_DEUDOR = DA.COD_DEUDOR";
				}
			break;		
			
			///////////////// AJAX_SOLICITUD ///////////////
			case 'AJAX_SOLICITUD' :
				///////////////// ORACLE ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT COUNT(*) CANT
							FROM   DEUDOR DR, 
								   DEUDA DA,
								   PRORROGA P
							WHERE  DR.RUT = {PARAM1}
							AND	   P.COD_ESTADO_PRORROGA = {PARAM4}
							AND    DR.COD_DEUDOR = DA.COD_DEUDOR
							AND    P.COD_DEUDA = DA.COD_DEUDA";
				}
			break;		
			
			///////////////// AJAX_NOMBRE ///////////////
			case 'AJAX_NOMBRE' :
				///////////////// ORACLE ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	DR.NOMBRE || ' ' || DR.APELLIDO_PATERNO || ' ' ||DR.APELLIDO_MATERNO  NOMBRE
									,DIG_VERIF
									,DA.COD_TIPO_DEUDA
									,DA.COD_DEUDA
							FROM	DEUDOR DR
									, DEUDA DA
									, ESTADO_DEUDA ED
							WHERE	DR.RUT = {PARAM1}
							AND		DR.COD_DEUDOR = DA.COD_DEUDOR
							AND		DA.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA
							AND     DA.COD_ESTADO_DEUDA  IN ({PARAM2}, {PARAM3})
							AND		DA.COD_TIPO_DEUDA <> 'CSR'";
				}
				///////////////// SQL-SERVER  ///////////////
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	DR.NOMBRE + ' ' + DR.APELLIDO_PATERNO + ' ' + DR.APELLIDO_MATERNO  NOMBRE
									,DIG_VERIF
									,DA.COD_TIPO_DEUDA
									,DA.COD_DEUDA
							FROM	DEUDOR DR
									, DEUDA DA
									, ESTADO_DEUDA ED
							WHERE	DR.RUT = {PARAM1}
							AND		DR.COD_DEUDOR = DA.COD_DEUDOR
							AND		DA.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA
							AND     DA.COD_ESTADO_DEUDA  IN ({PARAM2}, {PARAM3})
							AND		DA.COD_TIPO_DEUDA <> 'CSR'";
				}
			break;				
			///////////////////print//////////////////////
			case 'PRINT_RECORD' :
				if (K_TIPO_BD=="oci"){
					
				/*	$sql=	"SELECT		P.COD_PRORROGA  
										, D.RUT
										, D.DIG_VERIF		
										, D.APELLIDO_PATERNO||' '||D.APELLIDO_MATERNO||' '|| D.NOMBRE	NOMBRE
                    					, DIR.DIRECCION||', '||CO.NOM_COMUNA    DIRECCION
					          			, DE.COD_DEUDA
					          			, f_get_parametro({PARAM2}) NOM_ADMIN
                    					, TD.NOM_TIPO_DEUDA
                    					, CO.COD_COMUNA
                    					, DIR.COD_DIRECCION
                    					, To_Char(P.FECHA_PRORROGA, 'yyyy') ANO
		                                , U.COD_UNIVERSIDAD
		                                , U.NOM_UNIVERSIDAD
		                                , To_Char(P.FECHA_PRORROGA, 'dd')  DIA
							            , f_get_nom_mes(to_number(To_Char(P.FECHA_PRORROGA, 'mm'))) MES
					                    , To_Char(SYSDATE, 'yyyy') ANO
							FROM 		DEUDOR  D left outer join DIRECCION dir on d.COD_DEUDOR = dir.cod_deudor AND ES_VALIDA = 'S'
												  left outer join COMUNA CO ON DIR.COD_COMUNA = CO.COD_COMUNA
										, PRORROGA P,  DEUDA DE
					                	,  TIPO_DEUDA TD, UNIVERSIDAD U
							WHERE   	P.COD_PRORROGA = {PARAM1}
								AND		P.COD_DEUDA = DE.COD_DEUDA
								AND		DE.COD_DEUDOR = D.COD_DEUDOR
								AND		TD.COD_TIPO_DEUDA =   DE.COD_TIPO_DEUDA
								AND		U.COD_UNIVERSIDAD = DE.COD_UNIVERSIDAD";*/

$sql=	"SELECT	 	P.COD_PRORROGA  
										, D.RUT
										, D.DIG_VERIF		
										, D.NOMBRE || ' ' || D.APELLIDO_PATERNO || ' ' ||D.APELLIDO_MATERNO  NOMBRE
                    					, DIR.DIRECCION||', '||CO.NOM_COMUNA    DIRECCION
					          			, DE.COD_DEUDA
					          			, f_get_parametro({PARAM2}) NOM_ADMIN
                    					, TD.NOM_TIPO_DEUDA
                    					, CO.COD_COMUNA
                    					, DIR.COD_DIRECCION
                    					, To_Char(P.FECHA_PRORROGA, 'yyyy') ANO
		                                , U.COD_UNIVERSIDAD
		                                , U.NOM_UNIVERSIDAD
		                                , To_Char(P.FECHA_PRORROGA, 'dd')  DIA
							            , f_get_nom_mes(to_number(To_Char(P.FECHA_PRORROGA, 'mm'))) MES
					                    , To_Char(SYSDATE, 'yyyy') ANO_SYSTEM
					                    , MATRICULA.ANO_MATRICULA ULT_MAT
                            FROM 		DEUDOR  D left outer join DIRECCION dir on d.COD_DEUDOR = dir.cod_deudor AND ES_VALIDA = 'S'
												  left outer join COMUNA CO ON DIR.COD_COMUNA = CO.COD_COMUNA
										, PRORROGA P,  DEUDA DE
					                	,  TIPO_DEUDA TD, UNIVERSIDAD U, MATRICULA
							WHERE   	P.COD_PRORROGA = {PARAM1}
								AND		P.COD_DEUDA = DE.COD_DEUDA
								AND		DE.COD_DEUDOR = D.COD_DEUDOR
								AND		TD.COD_TIPO_DEUDA =   DE.COD_TIPO_DEUDA
								AND     MATRICULA.COD_DEUDOR = DE.COD_DEUDOR
								AND     MATRICULA.COD_UNIVERSIDAD = U.COD_UNIVERSIDAD
                                AND     MATRICULA.ANO_MATRICULA = (SELECT MAX(MATRICULA.ANO_MATRICULA) 
                                        FROM MATRICULA WHERE MATRICULA.COD_DEUDOR = D.COD_DEUDOR ) ";
								
								










				}
				else if (K_TIPO_BD=="mssql"){
					$sql="SELECT	TOP 1	P.COD_PRORROGA  
										, D.RUT
										, D.DIG_VERIF		
										, D.APELLIDO_PATERNO + ' ' + D.APELLIDO_MATERNO + ' ' + D.NOMBRE	NOMBRE
                    					, DIR.DIRECCION + ', ' + CO.NOM_COMUNA    DIRECCION
					          			, DE.COD_DEUDA
					          			, dbo.f_get_parametro({PARAM2}) NOM_ADMIN
                    					, TD.NOM_TIPO_DEUDA
                    					, CO.COD_COMUNA
                    					, DIR.COD_DIRECCION
		                                , U.COD_UNIVERSIDAD
		                                , U.NOM_UNIVERSIDAD
		                                , convert(varchar(2), datepart(day, getdate()))  DIA
										, dbo.f_get_nom_mes(month(getdate())) MES
					                    , YEAR(getdate()) ANO
							FROM 		DEUDOR  D left outer join DIRECCION dir on d.COD_DEUDOR = dir.cod_deudor
												  left outer join COMUNA CO ON DIR.COD_COMUNA = CO.COD_COMUNA
										, PRORROGA P,  DEUDA DE
					                	,  TIPO_DEUDA TD, UNIVERSIDAD U
							WHERE   	P.COD_PRORROGA = {PARAM1}
								AND		P.COD_DEUDA = DE.COD_DEUDA
								AND		DE.COD_DEUDOR = D.COD_DEUDOR
								AND		TD.COD_TIPO_DEUDA =   DE.COD_TIPO_DEUDA
								AND		U.COD_UNIVERSIDAD = DE.COD_UNIVERSIDAD
								AND		DIR.ES_VALIDA='S'
								ORDER BY DIR.FECHA_DIRECCION DESC";
				}
			break;

			//////////////// PRINT_PARAM ////////////////
			case 'PRINT_PARAM' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	f_get_parametro(5) NOM_UNIVERSIDAD
					                ,f_get_parametro(13) MAIL_UNIVERSIDAD
					                ,f_get_parametro(9) DIR_UNIVERSIDAD
					                ,f_get_parametro(10) FONO_UNIVERSIDAD
					                ,f_get_parametro(15) FAX_UNIVERSIDAD
							FROM 	DUAL";
				}else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	dbo.f_get_parametro(5) NOM_UNIVERSIDAD
					                ,dbo.f_get_parametro(13) MAIL_UNIVERSIDAD
					                ,dbo.f_get_parametro(9) DIR_UNIVERSIDAD
					                ,dbo.f_get_parametro(10) FONO_UNIVERSIDAD
					               ,dbo.f_get_parametro(15) FAX_UNIVERSIDAD";
				}
			break;
			
			//////////////////////////		DEUDA_CARTERA_DIFERENTE		//////////////////////
			case 'DEUDA_CARTERA_DIFERENTE' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = 	"SELECT COUNT(*) CANT
								FROM DEUDOR DR, DEUDA DA 
								WHERE DR.COD_DEUDOR = DA.COD_DEUDOR 
								AND DA.COD_CARTERA  <> {PARAM1}
								AND DR.RUT = {PARAM2}
								AND COD_TIPO_DEUDA IN ('CS', 'CSR')";
				}
			break;
			
			//////////////////////////		DD_UNIVERSIDADES		//////////////////////
			case 'DD_UNIVERSIDADES' :
				if (K_TIPO_BD=="mssql"){
					$sql = 	"SELECT COD_UNIVERSIDAD
									,NOM_UNIVERSIDAD
							FROM UNIVERSIDAD 
							WHERE COD_UNIVERSIDAD <= 26
							


							/*UNION 
							SELECT 26 COD_UNIVERSIDAD
									,'OTRA UNIVERSIDAD' NOM_UNIVERSIDAD*/";
				}
			break;
			/////////////////////// OBTIENE_MATRICULA		//////////////////////////////
			case 'OBTIENE_MATRICULA' :
				if (K_TIPO_BD=="mssql"){
					$sql = 	"SELECT	SEMESTRE
									,NOM_CARRERA 
									,U.NOM_UNIVERSIDAD
									,ANO_MATRICULA ANO_MATRICULA_K
							FROM DEUDOR D,MATRICULA M , UNIVERSIDAD U
							WHERE D.COD_DEUDOR = M.COD_DEUDOR
							AND	M.COD_UNIVERSIDAD= U.COD_UNIVERSIDAD
							AND	D.RUT = {PARAM1}
							ORDER BY 	COD_MATRICULA ASC";
				}
			break;
			/////////////////  ERROR ///////////////
			default: 
			base::error("sql_prorroga::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>