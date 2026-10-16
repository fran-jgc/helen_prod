<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_inf_arqueo_caja extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	EFECTIVO
								  	,CHEQUE_AL_DIA
								  	,CHEQUE_FECHA
								  	,OTROS
								  	,DESCUENTO
								  	,DEPOSITO
								  	,SUBTOTAL1
								  	,SUBTOTAL2
								  	,TOTAL
								  	,COUNT_CH_AL_DIA
								  	,COUNT_CH_A_FECHA
								  	,COUNT_OTROS
								  	,COUNT_DESCUENTO
								  	,COUNT_DEPOSITO
								  	,TOTAL_COUNT1
								  	,TOTAL_COUNT2
								  	,NOM_ESTADO_INGRESO
								  	,FECHA_INGRESO
								  	,COD_INGRESO
								  	,SUM_EFECTIVO
									,SUM_CH_AL_DIA
									,SUM_CH_FECHA
									,SUM_OTROS
									,SUM_DEPOSITOS
									,SUM_TOTAL
									,SUM_DESCUENTO
						  	FROM 	TEMP_ARQUEO_CAJA
						  	ORDER 	BY COD_INGRESO ASC";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "spi_arqueo_caja";
				}
			break;
			
			case 'SELECT' :
					if(K_TIPO_BD == "oci"){
						$sql = "SELECT   to_char(sysdate, 'dd/mm/yyyy') FECHA_INICIO
										, to_char(sysdate, 'dd/mm/yyyy') FECHA_TERMINO
										,  {PARAM1} COD_USUARIO
										, 'RESUMEN' RESUMEN
										, 'RESUMEN' DETALLE
										, 1 COD_FAMILIA_DEUDA
								FROM 	dual";
					}
					else if(K_TIPO_BD == "mssql"){
						$sql = "SELECT   convert(varchar(20), GETDATE(), 103) FECHA_INICIO
										, convert(varchar(20), GETDATE(), 103) FECHA_TERMINO
										,  {PARAM1} COD_USUARIO
										, 'RESUMEN' RESUMEN
										, 'RESUMEN' DETALLE
										, 1 COD_FAMILIA_DEUDA";
					}
			break;
			

			case 'DD_USUARIO' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	COD_USUARIO
									, NOM_USUARIO 
							FROM 	USUARIO 
							where 	f_get_tiene_acceso ({PARAM1}, COD_USUARIO) = 1
							UNION 
							SELECT 	0 COD_USUARIO
									, 'TODOS' NOM_USUARIO 
							FROM 	dual 
							where 	(select sum(f_get_tiene_acceso ({PARAM1}, COD_USUARIO)) from USUARIO) = (select count(*) from USUARIO)
							order by COD_USUARIO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select 	COD_USUARIO
									, NOM_USUARIO 
							from 	USUARIO
							where 	dbo.f_get_tiene_acceso ({PARAM1}, COD_USUARIO) = 1
							UNION 
							SELECT 	0 COD_USUARIO
									, 'TODOS' NOM_USUARIO 
							where 	(select sum(dbo.f_get_tiene_acceso ({PARAM1}, COD_USUARIO)) from USUARIO) = (select count(*) from USUARIO)
							order by COD_USUARIO";
				}
			break;
			
			case 'DD_FAMILIA_DEUDA' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	COD_FAMILIA_DEUDA
									, NOM_FAMILIA_DEUDA
							FROM 	FAMILIA_DEUDA
							UNION 
							SELECT 	0 COD_FAMILIA_DEUDA
									, 'TODOS' NOM_FAMILIA_DEUDA
							FROM 	dual 
							order by COD_FAMILIA_DEUDA";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_FAMILIA_DEUDA
									, NOM_FAMILIA_DEUDA
							FROM 	FAMILIA_DEUDA
							UNION 
							SELECT 	0 COD_FAMILIA_DEUDA
									, 'TODOS' NOM_FAMILIA_DEUDA
							order by COD_FAMILIA_DEUDA";
				}
			break;

			////////////////////			ERROR		//////////////////////////////////
			default:
			base::error("sql_inf_arqueo_caja::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>