<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_moneda extends sql_tabla {
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			
			/////////////////////		INPUT		//////////////////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= 	"SELECT     {KEY2} MES,
										{KEY3} ANO,
										'01/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA1,
					             		Nvl(f_mon_get_valor_moneda({KEY1}, to_date('01/'|| to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR1,
					            		'02/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA2,
					            		Nvl(f_mon_get_valor_moneda({KEY1}, to_date('02/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR2,
					            		'03/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA3,
					            		Nvl(f_mon_get_valor_moneda({KEY1}, to_date('03/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR3,
					            		'04/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA4,
							            Nvl(f_mon_get_valor_moneda({KEY1}, to_date('04/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR4,
							            '05/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA5,
							            Nvl(f_mon_get_valor_moneda({KEY1}, to_date('05/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR5,
							            '06/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA6,
					    		        Nvl(f_mon_get_valor_moneda({KEY1}, to_date('06/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR6,
					            		'07/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA7,
							            Nvl(f_mon_get_valor_moneda({KEY1}, to_date('07/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR7,
							            '08/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA8,
							            Nvl(f_mon_get_valor_moneda({KEY1}, to_date('08/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR8,
					    		        '09/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA9,
					            		Nvl(f_mon_get_valor_moneda({KEY1}, to_date('09/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR9,
							            '10/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA10,
							            Nvl(f_mon_get_valor_moneda({KEY1}, to_date('10/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR10,
							            '11/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA11,
					    		        Nvl(f_mon_get_valor_moneda({KEY1}, to_date('11/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR11,
					            		'12/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA12,
							            Nvl(f_mon_get_valor_moneda({KEY1}, to_date('12/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR12,
							            '13/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA13,
							            Nvl(f_mon_get_valor_moneda({KEY1}, to_date('13/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR13,
					    		        '14/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA14,
					            		Nvl(f_mon_get_valor_moneda({KEY1}, to_date('14/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR14,
							            '15/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA15,
							            Nvl(f_mon_get_valor_moneda({KEY1}, to_date('15/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR15,
							            '16/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA16,
					    		        Nvl(f_mon_get_valor_moneda({KEY1}, to_date('16/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR16,
					            		'17/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA17,
							            Nvl(f_mon_get_valor_moneda({KEY1}, to_date('17/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR17,
							            '18/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA18,
							            Nvl(f_mon_get_valor_moneda({KEY1}, to_date('18/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR18,
					    		        '19/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA19,
					            		Nvl(f_mon_get_valor_moneda({KEY1}, to_date('19/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR19,
					            		'20/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA20,
					            		Nvl(f_mon_get_valor_moneda({KEY1}, to_date('20/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR20,
					            		'21/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA21,
					            		Nvl(f_mon_get_valor_moneda({KEY1}, to_date('21/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR21,
							            '22/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA22,
							            Nvl(f_mon_get_valor_moneda({KEY1}, to_date('22/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR22,
							            '23/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA23,
					    		        Nvl(f_mon_get_valor_moneda({KEY1}, to_date('23/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR23,
					            		'24/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA24,
							            Nvl(f_mon_get_valor_moneda({KEY1}, to_date('24/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR24,
							            '25/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA25,
							            Nvl(f_mon_get_valor_moneda({KEY1}, to_date('25/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR25,
					    		        '26/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA26,
					            		Nvl(f_mon_get_valor_moneda({KEY1}, to_date('26/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR26,
							            '27/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA27,
							            Nvl(f_mon_get_valor_moneda({KEY1}, to_date('27/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR27,
							            '28/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000') DIA28,
					    		        Nvl(f_mon_get_valor_moneda({KEY1}, to_date('28/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0) VALOR28,
									    Nvl(DECODE(f_ultimo_dia({KEY2},{KEY3}), 28, '','29/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000')),' ') DIA29,
								  		Nvl(DECODE(f_ultimo_dia({KEY2},{KEY3}), 28, 0,
				            			Nvl(f_mon_get_valor_moneda({KEY1}, to_date(DECODE(f_ultimo_dia({KEY2},{KEY3}), 28, '28', '29') || '/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0)),0) VALOR29,
									  	Nvl(DECODE(f_ultimo_dia({KEY2},{KEY3}), 28, '', 29, '','30/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000')),' ') DIA30,
									  	Nvl(DECODE(f_ultimo_dia({KEY2},{KEY3}), 28, '', 29, '',
			            				Nvl(f_mon_get_valor_moneda({KEY1}, to_date(DECODE(f_ultimo_dia({KEY2},{KEY3}), 28, '28', 29, '29', '30') || '/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0)),0) VALOR30,
								  		Nvl(DECODE(f_ultimo_dia({KEY2},{KEY3}), 28, '', 29, '', 30, '','31/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000')),' ') DIA31,
								  		Nvl(DECODE(f_ultimo_dia({KEY2},{KEY3}), 28, '', 29, '', 30, '',
			            				Nvl(f_mon_get_valor_moneda({KEY1}, to_date(DECODE(f_ultimo_dia({KEY2},{KEY3}), 28, '28', 29, '29', 30, '30', '31') || '/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')),0)),0) VALOR31
						FROM dual" ;
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= 	"SELECT     {KEY2} MES,
										{KEY3} ANO,
										Convert(varchar,dbo.f_makedate(01,{KEY2},{KEY3}),103) DIA1,
					             		IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(01,{KEY2},{KEY3})),0) VALOR1,
					            		Convert(varchar,dbo.f_makedate(02,{KEY2},{KEY3}),103) DIA2,
					            		IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(02,{KEY2},{KEY3})),0) VALOR2,
					            		Convert(varchar,dbo.f_makedate(03,{KEY2},{KEY3}),103) DIA3,
					            		IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(03,{KEY2},{KEY3})),0) VALOR3,
					            		Convert(varchar,dbo.f_makedate(04,{KEY2},{KEY3}),103) DIA4,
							            IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(04,{KEY2},{KEY3})),0) VALOR4,
							            Convert(varchar,dbo.f_makedate(05,{KEY2},{KEY3}),103) DIA5,
							            IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(05,{KEY2},{KEY3})),0) VALOR5,
							            Convert(varchar,dbo.f_makedate(06,{KEY2},{KEY3}),103) DIA6,
					    		        IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(06,{KEY2},{KEY3})),0) VALOR6,
					            		Convert(varchar,dbo.f_makedate(07,{KEY2},{KEY3}),103) DIA7,
							            IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(07,{KEY2},{KEY3})),0) VALOR7,
							            Convert(varchar,dbo.f_makedate(08,{KEY2},{KEY3}),103) DIA8,
							            IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(08,{KEY2},{KEY3})),0) VALOR8,
					    		        Convert(varchar,dbo.f_makedate(09,{KEY2},{KEY3}),103) DIA9,
					            		IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(09,{KEY2},{KEY3})),0) VALOR9,
							            Convert(varchar,dbo.f_makedate(10,{KEY2},{KEY3}),103) DIA10,
							            IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(10,{KEY2},{KEY3})),0) VALOR10,
							            Convert(varchar,dbo.f_makedate(11,{KEY2},{KEY3}),103) DIA11,
					    		        IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(11,{KEY2},{KEY3})),0) VALOR11,
					            		Convert(varchar,dbo.f_makedate(12,{KEY2},{KEY3}),103) DIA12,
							            IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(12,{KEY2},{KEY3})),0) VALOR12,
							            Convert(varchar,dbo.f_makedate(13,{KEY2},{KEY3}),103) DIA13,
							            IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(13,{KEY2},{KEY3})),0) VALOR13,
					    		        Convert(varchar,dbo.f_makedate(14,{KEY2},{KEY3}),103) DIA14,
					            		IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(14,{KEY2},{KEY3})),0) VALOR14,
							            Convert(varchar,dbo.f_makedate(15,{KEY2},{KEY3}),103) DIA15,
							            IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(15,{KEY2},{KEY3})),0) VALOR15,
							            Convert(varchar,dbo.f_makedate(16,{KEY2},{KEY3}),103) DIA16,
					    		        IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(16,{KEY2},{KEY3})),0) VALOR16,
					            		Convert(varchar,dbo.f_makedate(17,{KEY2},{KEY3}),103) DIA17,
							            IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(17,{KEY2},{KEY3})),0) VALOR17,
							            Convert(varchar,dbo.f_makedate(18,{KEY2},{KEY3}),103) DIA18,
							            IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(18,{KEY2},{KEY3})),0) VALOR18,
					    		        Convert(varchar,dbo.f_makedate(19,{KEY2},{KEY3}),103) DIA19,
					            		IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(19,{KEY2},{KEY3})),0) VALOR19,
					            		Convert(varchar,dbo.f_makedate(20,{KEY2},{KEY3}),103) DIA20,
					            		IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(20,{KEY2},{KEY3})),0) VALOR20,
					            		Convert(varchar,dbo.f_makedate(21,{KEY2},{KEY3}),103) DIA21,
					            		IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(21,{KEY2},{KEY3})),0) VALOR21,
							            Convert(varchar,dbo.f_makedate(22,{KEY2},{KEY3}),103) DIA22,
							            IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(22,{KEY2},{KEY3})),0) VALOR22,
							            Convert(varchar,dbo.f_makedate(23,{KEY2},{KEY3}),103) DIA23,
					    		        IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(23,{KEY2},{KEY3})),0) VALOR23,
					            		Convert(varchar,dbo.f_makedate(24,{KEY2},{KEY3}),103) DIA24,
							            IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(24,{KEY2},{KEY3})),0) VALOR24,
							            Convert(varchar,dbo.f_makedate(25,{KEY2},{KEY3}),103) DIA25,
							            IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(25,{KEY2},{KEY3})),0) VALOR25,
					    		        Convert(varchar,dbo.f_makedate(26,{KEY2},{KEY3}),103) DIA26,
					            		IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(26,{KEY2},{KEY3})),0) VALOR26,
							            Convert(varchar,dbo.f_makedate(27,{KEY2},{KEY3}),103) DIA27,
							            IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(27,{KEY2},{KEY3})),0) VALOR27,
							            Convert(varchar,dbo.f_makedate(28,{KEY2},{KEY3}),103) DIA28,
					    		        IsNull(dbo.f_mon_get_valor_moneda({KEY1}, dbo.f_makedate(28,{KEY2},{KEY3})),0) VALOR28,
									    Convert(varchar,CASE dbo.f_ultimo_dia({KEY2},{KEY3}) WHEN 28 THEN null ELSE dbo.f_makedate(29,1,2010) END,103) DIA29,
										IsNull(	CASE dbo.f_ultimo_dia({KEY2},{KEY3})
													WHEN 28 THEN null
													ELSE IsNull(dbo.f_mon_get_valor_moneda({KEY1},	CASE dbo.f_ultimo_dia({KEY2},{KEY3})
																										WHEN 28 THEN  dbo.f_makedate(28,{KEY2},{KEY3})
																										ELSE dbo.f_makedate(29,{KEY2},{KEY3})
																									END)
																,0)
													END
												,0) VALOR29,
									  	Convert(varchar,CASE	dbo.f_ultimo_dia({KEY2},{KEY3})WHEN 28 THEN null WHEN 29 THEN null ELSE dbo.f_makedate(30,{KEY2},{KEY3}) END,103) DIA30,
									  	IsNull(CASE dbo.f_ultimo_dia({KEY2},{KEY3})
													WHEN 28 THEN null
													WHEN 29 THEN null
													ELSE IsNull(
																dbo.f_mon_get_valor_moneda(
																							{KEY1}, CASE dbo.f_ultimo_dia({KEY2},{KEY3})
																										WHEN 28 THEN dbo.f_makedate(28,{KEY2},{KEY3})
																										WHEN 29 THEN dbo.f_makedate(29,{KEY2},{KEY3})
																										ELSE dbo.f_makedate(30,{KEY2},{KEY3})
																									END
																							)
																,0)
												END
											 ,0) VALOR30,
			            				Convert(varchar,CASE	dbo.f_ultimo_dia({KEY2},{KEY3})WHEN 28 THEN null WHEN 29 THEN null WHEN 30 THEN null ELSE dbo.f_makedate(31,{KEY2},{KEY3}) END,103) DIA31,
								  		IsNull(CASE dbo.f_ultimo_dia({KEY2},{KEY3})
													WHEN 28 THEN null
													WHEN 29 THEN null
													WHEN 30 THEN null
													ELSE IsNull(
																dbo.f_mon_get_valor_moneda(
																							{KEY1}, CASE dbo.f_ultimo_dia({KEY2},{KEY3})
																										WHEN 28 THEN dbo.f_makedate(28,{KEY2},{KEY3})
																										WHEN 29 THEN dbo.f_makedate(29,{KEY2},{KEY3})
																										WHEN 30 THEN dbo.f_makedate(30,{KEY2},{KEY3})
																										ELSE dbo.f_makedate(31,{KEY2},{KEY3})
																									END
																							)
																,0)
												END
											 ,0) VALOR31";
				}
				break;
				////////////////////////////		INPUT_DD_MES		///////////////////
				case 'INPUT_DD_MES' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_MES, 
										UPPER(NOM_MES) 
							FROM 		MES";
				}
				break;
				
				//////////////////		INPUT_DD_MONEDA		//////////////////////
				case 'INPUT_DD_MONEDA' :
				if (K_TIPO_BD=="oci"){
					$sql= 	"SELECT  	COD_MONEDA, 
										NOM_MONEDA,
										SIMBOLO,
										VALOR_MENSUAL,
										f_mon_get_valor_moneda(COD_MONEDA, to_date('01/' || to_char({KEY2}, '00') || '/' || to_char({KEY3}, '0000'), 'dd/mm/yyyy')) MONTO_VALOR_MENSUAL,
										'' DIV_MONTO_VALOR_MENSUAL,
										'' TABLA_VALORES,
										PORC_PROYECTADA						
							FROM 		MONEDA
							WHERE 		COD_MONEDA = {KEY1}";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= 	"SELECT  	COD_MONEDA, 
										NOM_MONEDA,
										SIMBOLO,
										VALOR_MENSUAL,
										dbo.f_mon_get_valor_moneda(COD_MONEDA, dbo.f_makedate(01,{KEY2},{KEY3})) MONTO_VALOR_MENSUAL,
										'' DIV_MONTO_VALOR_MENSUAL,
										'' TABLA_VALORES,
										PORC_PROYECTADA						
							FROM 		MONEDA
							WHERE 		COD_MONEDA = {KEY1}";
				}
				break;
				
				///////////////////////		OUTPUT		//////////////////////////////
				case 'OUTPUT' :
					if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_MONEDA,
		               					NOM_MONEDA,
					   					SIMBOLO					 		
		        			FROM 		MONEDA
							ORDER BY 	COD_MONEDA";
					}
				break;
				//////////////////		ERROR		///////////////////////
				default:
				base::error("sql_moneda::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>