<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
require_once ("../../../appl.ini");

class w_inicio extends w_base {
	
	function w_inicio() {
		$cod_deudor = $_REQUEST['cod_deudor'];
		$temp = new Template_appl("inicio.htm");
		
		$sql =  "select RUT STATIC_RUT
						,(SELECT f_get_parametro(3) from dual) COD_UNIVERSIDAD_DEUDOR
						,RUT
						,DIG_VERIF
						,NOMBRE
						,APELLIDO_PATERNO
						,APELLIDO_MATERNO
						,DIRECCION
						,(SELECT NOM_COMUNA FROM COMUNA CO WHERE CO.COD_COMUNA = DI.COD_COMUNA) NOM_COMUNA
						,(SELECT NOM_CIUDAD FROM CIUDAD CI WHERE CI.COD_CIUDAD = DI.COD_CIUDAD) NOM_CIUDAD
						,EMAIL
						,TELEFONO
						,CELULAR
				from DEUDOR DE,DIRECCION DI
				where DE.COD_DEUDOR = DI.COD_DEUDOR
				and de.COD_DEUDOR = $cod_deudor
				and di.cod_direccion = f_get_direccion_deudor (de.COD_DEUDOR, 'P')
				ORDER BY FECHA_DIRECCION DESC
		";
				
		$dw_inicio = new datawindow($sql);
		
		$dw_inicio->add_control(new static_text('STATIC_RUT',15,15));
		$dw_inicio->add_control(new edit_text('COD_UNIVERSIDAD_DEUDOR',10,10));
		$dw_inicio->add_control(new edit_text('NOMBRE',10,10));
		$dw_inicio->add_control(new edit_text('RUT',10,10));
		$dw_inicio->add_control(new edit_text('DIG_VERIF',10,10));
		$dw_inicio->add_control(new edit_text('APELLIDO_PATERNO',10,10));
		$dw_inicio->add_control(new edit_text('APELLIDO_MATERNO',10,10));
		$dw_inicio->add_control(new edit_text('DIRECCION',10,10));
		$dw_inicio->add_control(new edit_text('NOM_COMUNA',10,10));
		$dw_inicio->add_control(new edit_text('NOM_CIUDAD',10,10));
		$dw_inicio->add_control(new edit_text('EMAIL',10,10));
		$dw_inicio->add_control(new edit_text('TELEFONO',10,10));
		$dw_inicio->add_control(new edit_text('CELULAR',10,10));
		
		$dw_inicio->retrieve();
		$dw_inicio->habilitar($temp, true);
				
		$sql = $this->select_deuda($cod_deudor);

		$dw_deuda = new datawindow($sql,"DEUDAS");

		$dw_deuda->add_control(new edit_text('COD_DEUDA',10,10));
		$dw_deuda->add_control(new edit_text('COD_TIPO_DEUDA',10,10));
		$dw_deuda->add_control(new edit_text('COD_UNIVERSIDAD',10,10));
		$dw_deuda->add_control(new edit_text('NOM_CORTO_UNIVERSIDAD',10,10));
		$dw_deuda->add_control(new edit_text('NOM_UNIVERSIDAD',10,10));
		$dw_deuda->add_control(new edit_text('SALDO_CAPITAL',10,10));
		$dw_deuda->add_control(new edit_text('SALDO_MULTA',10,10));

		$dw_deuda->retrieve();
		$dw_deuda->habilitar($temp, true);
		print $temp->toString();
		
		
	}
function select_deuda($cod_deudor)
	{
		$sql = "";
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql_es_cruzado ="SELECT COUNT(*) CANTIDAD
							FROM DEUDA DA,CRUZADO C,TIPO_DEUDA t
							WHERE COD_DEUDOR = $cod_deudor
							AND DA.COD_DEUDA = C.COD_DEUDA
							and t.COD_TIPO_DEUDA = da.COD_TIPO_DEUDA
							and da.COD_ESTADO_DEUDA = 2 --EXIGIBLE
							and da.COD_TIPO_DEUDA not in ('CSR', 'CRPI', 'CR12','CFR','CRI')
							AND (SELECT  nvl(SUM (f_pp_saldo_mora(pp.cod_plan_pago, sysdate)), 0) SALDO_MORA
				        	FROM plan_pago pp
						    WHERE cod_deuda = da.COD_DEUDA
						    and FECHA_VENCTO < add_months(sysdate,-18)) > 0.0000
							and t.COD_FAMILIA_DEUDA = 1 --FONDO_SOLIDARIO";
		$result_es_cruzado	= $db->build_results($sql_es_cruzado);
		
		if($result_es_cruzado[0]["CANTIDAD"] > 1)
		{
			$sql = "select da.COD_DEUDA
							 ,DA.COD_TIPO_DEUDA
							 ,CR.COD_UNIVERSIDAD
							 ,(SELECT U.NOM_CORTO_UNIVERSIDAD FROM UNIVERSIDAD U WHERE U.COD_UNIVERSIDAD = CR.COD_UNIVERSIDAD) NOM_UNIVERSIDAD
							 ,ROUND((CR.PORC*sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)))/100,4) SALDO_CAPITAL
							 ,ROUND((f_mon_get_valor_moneda(3, sysdate) * ROUND((CR.PORC*sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)))/100,4)),0) SALDO_CAPITAL_P
							 ,ROUND((CR.PORC*sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)))/100,4) SALDO_MULTA
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) *ROUND((CR.PORC*sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)))/100,4) ,0) SALDO_MULTA_P
							 ,ROUND((CR.PORC*sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)))/100,4)+
							  ROUND((CR.PORC*sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)))/100,4) SALDO_TOTAL
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) *
							  (ROUND((CR.PORC*sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)))/100,4)+
							  ROUND((CR.PORC*sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)))/100,4)),0) SALDO_TOTAL_P
							  ,CR.PORC
					from TIPO_DEUDA t, PLAN_PAGO p,
					DEUDA da LEFT OUTER JOIN CRUZADO CR ON CR.COD_DEUDA = DA.COD_DEUDA
					where da.COD_DEUDOR = $cod_deudor
					and da.COD_ESTADO_DEUDA = 2 --EXIGIBLE
					and da.COD_TIPO_DEUDA not in ('CSR', 'CRPI', 'CR12','CFR','CRI') 
					and t.COD_TIPO_DEUDA = da.COD_TIPO_DEUDA
					and t.COD_FAMILIA_DEUDA = 1 --FONDO_SOLIDARIO
					and p.COD_DEUDA = da.COD_DEUDA
					AND (SELECT  nvl(SUM (f_pp_saldo_mora(pp.cod_plan_pago, sysdate)), 0) SALDO_MORA
				        FROM plan_pago pp
					    WHERE cod_deuda = da.COD_DEUDA
					    and FECHA_VENCTO < add_months(sysdate,-18)) > 0.0000
					group by da.COD_DEUDA,DA.COD_TIPO_DEUDA,CR.PORC, CR.COD_UNIVERSIDAD
					UNION
					select da.COD_DEUDA
							 ,DA.COD_TIPO_DEUDA
							 ,U.COD_UNIVERSIDAD
							 ,U.NOM_CORTO_UNIVERSIDAD NOM_UNIVERSIDAD
							 ,sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)) SALDO_CAPITAL
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) * 
									(sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end))),0) SALDO_CAPITAL_P
							 ,sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)) SALDO_MULTA 
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) *(sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate))) ,0) SALDO_MULTA_P
							 ,sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)) +
							  sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)) SALDO_TOTAL
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) *
							  (sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)) +
							  sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate))),0) SALDO_TOTAL_P
							  ,NULL
					from DEUDA da, TIPO_DEUDA t, PLAN_PAGO p,UNIVERSIDAD U
					where da.COD_DEUDOR = $cod_deudor
					and da.COD_ESTADO_DEUDA = 2 --EXIGIBLE
					and da.COD_TIPO_DEUDA not in ('CSR', 'CRPI', 'CR12','CFR','CRI') 
					and t.COD_TIPO_DEUDA = da.COD_TIPO_DEUDA
					AND DA.COD_UNIVERSIDAD = U.COD_UNIVERSIDAD
					and t.COD_FAMILIA_DEUDA = 1 --FONDO_SOLIDARIO
					and p.COD_DEUDA = da.COD_DEUDA
					AND DA.COD_DEUDA NOT IN (SELECT COD_DEUDA FROM CRUZADO)
					AND (SELECT  nvl(SUM (f_pp_saldo_mora(pp.cod_plan_pago, sysdate)), 0) SALDO_MORA
				        FROM plan_pago pp
					    WHERE cod_deuda = da.COD_DEUDA
					    and FECHA_VENCTO < add_months(sysdate,-18)) > 0.0000
					group by da.COD_DEUDA,DA.COD_TIPO_DEUDA,U.NOM_CORTO_UNIVERSIDAD,U.COD_UNIVERSIDAD";
		}
		else
		{
			$sql = "select da.COD_DEUDA
							 ,DA.COD_TIPO_DEUDA
							 ,U.COD_UNIVERSIDAD
							 ,U.NOM_CORTO_UNIVERSIDAD NOM_UNIVERSIDAD
							 ,sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)) SALDO_CAPITAL
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) * 
									(sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end))),0) SALDO_CAPITAL_P
							 ,sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)) SALDO_MULTA 
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) *(sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate))) ,0) SALDO_MULTA_P
							 ,sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)) +
							  sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)) SALDO_TOTAL
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) *
							  (sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)) +
							  sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate))),0) SALDO_TOTAL_P
					from DEUDA da, TIPO_DEUDA t, PLAN_PAGO p,UNIVERSIDAD U
					where da.COD_DEUDOR = $cod_deudor
					and da.COD_ESTADO_DEUDA = 2 --EXIGIBLE
					and da.COD_TIPO_DEUDA not in ('CSR', 'CRPI', 'CR12','CFR','CRI') 
					and t.COD_TIPO_DEUDA = da.COD_TIPO_DEUDA
					AND DA.COD_UNIVERSIDAD = U.COD_UNIVERSIDAD
					and t.COD_FAMILIA_DEUDA = 1 --FONDO_SOLIDARIO
					and p.COD_DEUDA = da.COD_DEUDA
					AND (SELECT  nvl(SUM (f_pp_saldo_mora(pp.cod_plan_pago, sysdate)), 0) SALDO_MORA
				        FROM plan_pago pp
					    WHERE cod_deuda = da.COD_DEUDA
					    and FECHA_VENCTO < add_months(sysdate,-18)) > 0.0000
					group by da.COD_DEUDA,DA.COD_TIPO_DEUDA,U.NOM_CORTO_UNIVERSIDAD,U.COD_UNIVERSIDAD";
		}
		return $sql;
	}
}
session::set('simulacion_cri', 'simulacion');
$w = new w_inicio();
?>