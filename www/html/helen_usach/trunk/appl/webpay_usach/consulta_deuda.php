<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
include("funciones.php");
class w_consulta_deuda{
	function __construct() {
		valida_inicio();
		
		$t = new Template_appl("consulta_deuda.html");

		$cod_deudor = session::get('cod_deudor');

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db1 = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

		$sql = "
				SELECT	D.RUT
					    ,D.DIG_VERIF
					    ,D.NOMBRE||' '||D.APELLIDO_PATERNO||' '||D.APELLIDO_MATERNO NOMBRE
					    ,DIP.DIRECCION  DIRECCION
					    ,DIP.TELEFONO FONO_PARTICULAR
					    ,DIP.EMAIL EMAIL_PARTICULAR
						,DIP.CELULAR CELULAR_PARTICULAR
					    ,DIC.DIRECCION DIRECCION_COMERCIAL
					    ,DIC.TELEFONO FONO_COMERCIAL
					    ,DIC.EMAIL EMAIL_COMERCIAL
						,DIC.CELULAR CELULAR_COMERCIAL
					    ,CASE
							WHEN CANDIDATO_17_BIS = 'N' THEN 'none'
							WHEN CANDIDATO_17_BIS = 'S' THEN
								case 
									when (select count(*) from deudor dr, deuda de where dr.cod_deudor = de.cod_deudor and dr.cod_deudor = D.cod_deudor and de.cod_tipo_deuda = 'CS' and de.cod_cartera in(6,26) and cod_deuda not in(select cod_deuda from cruzado)) > 0
									then 
										case
											WHEN f_repro_rcs_es_candidato(D.COD_DEUDOR,'WEBPAY') = 'N' THEN 'none'
											WHEN f_repro_rcs_es_candidato(D.COD_DEUDOR,'WEBPAY') = 'S' THEN ''
										END
									else 'none'
                                    end                                    
							WHEN CANDIDATO_17_BIS IS null THEN
								case
									when (select count(*) from deudor dr, deuda de where dr.cod_deudor = de.cod_deudor and dr.cod_deudor = D.cod_deudor and de.cod_tipo_deuda = 'CS' and de.cod_cartera in(6,26) and cod_deuda not in(select cod_deuda from cruzado)) > 0
									then 
										case  
											WHEN f_repro_rcs_es_candidato(D.COD_DEUDOR,'WEBPAY') = 'N' THEN 'none'
											WHEN f_repro_rcs_es_candidato(D.COD_DEUDOR,'WEBPAY') = 'S' THEN ''
										END
                                    else 'none'
									end
							END DIV_REPRO
						,CASE
							WHEN CANDIDATO_17_BIS = 'N' THEN ''
							WHEN CANDIDATO_17_BIS = 'S' THEN
								case
									when (select count(*) from deudor dr, deuda de where dr.cod_deudor = de.cod_deudor and dr.cod_deudor = D.cod_deudor and de.cod_tipo_deuda = 'CS' and de.cod_cartera in(6,26) and cod_deuda not in(select cod_deuda from cruzado)) > 0
									then 
										case
											WHEN f_repro_rcs_es_candidato(D.COD_DEUDOR,'WEBPAY') = 'N' THEN ''
											WHEN f_repro_rcs_es_candidato(D.COD_DEUDOR,'WEBPAY') = 'S' THEN 'href=\"simulacion_RCS/inicio.php?cod_deudor='|| To_Char(D.COD_DEUDOR) ||'\"'
										END
                                    else 'none'
								end
							WHEN CANDIDATO_17_BIS IS null THEN
								case
									when (select count(*) from deudor dr, deuda de where dr.cod_deudor = de.cod_deudor and dr.cod_deudor = D.cod_deudor and de.cod_tipo_deuda = 'CS' and de.cod_cartera in(6,26) and cod_deuda not in(select cod_deuda from cruzado)) > 0
									then 
										case
											WHEN f_repro_rcs_es_candidato(D.COD_DEUDOR,'WEBPAY') = 'N' THEN ''
											WHEN f_repro_rcs_es_candidato(D.COD_DEUDOR,'WEBPAY') = 'S' THEN 'href=\"simulacion_RCS/inicio.php?cod_deudor='|| To_Char(D.COD_DEUDOR) ||'\"'
										end
                                    else 'none'
								end
								
						END URL
				FROM 	DEUDOR D
					    ,DIRECCION DIP
					    ,DIRECCION DIC
				WHERE 	D.COD_DEUDOR=$cod_deudor
					    AND D.COD_DEUDOR = DIP.COD_DEUDOR(+)
					    AND D.COD_DEUDOR = DIC.COD_DEUDOR(+)
					    AND DIP.TIPO_DIRECCION(+) = 'P'
					    AND DIC.TIPO_DIRECCION(+)= 'C'
				        AND DIP.COD_DIRECCION(+) = f_get_direccion_deudor(D.COD_DEUDOR,'P')
				        AND DIC.COD_DIRECCION(+) = f_get_direccion_deudor(D.COD_DEUDOR,'C')";

		
		
		$dw_datos_deudor = new datawindow($sql);
		$dw_datos_deudor->add_control(new static_num('RUT'));
		$dw_datos_deudor->retrieve();
		$dw_datos_deudor->habilitar($t, false);
		
		$sql_deudas = "SELECT	COD_DEUDA
								,'' COD_DEUDA_DECODE
								,D.COD_ESTADO_DEUDA
,'' COD_DEUDA_PORTAL
						        ,NOM_ESTADO_DEUDA
						        ,D.COD_TIPO_DEUDA
						        ,SIMBOLO
						        ,To_Char(FECHA_INICIO_COBRO,'dd/mm/yyyy') FECHA_INICIO_COBRO
						        ,f_deuda_saldo_total(COD_DEUDA, sysdate)   SALDO
						        ,f_deuda_saldo_total(COD_DEUDA, sysdate) * f_mon_get_valor_moneda(td.cod_moneda) SALDO_PESOS
					   FROM    	DEUDA D
						        ,ESTADO_DEUDA ED
						        ,MONEDA M
						        ,TIPO_DEUDA TD
					   WHERE   	D.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA
						        AND M.COD_MONEDA = TD.COD_MONEDA
						        AND TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA 
						               						AND D.COD_DEUDA NOT IN (SELECT COD_DEUDA 
			                                            FROM REPRO_RCS_DEUDA 
			                                            WHERE COD_REPRO_RCS IN (SELECT MAX (COD_REPRO_RCS) 
			                                                                  FROM REPRO_RCS 
			                                                                  WHERE COD_ESTADO_REPRO_RCS<>3
			                                                                  AND COD_DEUDOR =$cod_deudor))
						        AND COD_DEUDOR =  ".$cod_deudor;
			
		$dw_deudas = new datawindow($sql_deudas, 'deuda');
		$dw_deudas->add_control(new static_num('SALDO', 4));
		$dw_deudas->add_control(new static_num('SALDO_PESOS'));
		$dw_deudas->retrieve();
		
		for($i = 0; $i < $dw_deudas->row_count(); $i++){
			$cod_deuda = $dw_deudas->get_item($i, 'COD_DEUDA');
			$key = 'helen';
			$cod_deuda_decode = encriptar_url($cod_deuda, $key);
			$dw_deudas->set_item($i, 'COD_DEUDA_DECODE', urlencode($cod_deuda_decode));
			$codigo_visible = htmlspecialchars((string)$cod_deuda, ENT_QUOTES, 'UTF-8');

if ((int)$dw_deudas->get_item($i, 'COD_ESTADO_DEUDA') === 33) {
    $codigo_portal = $codigo_visible;
} else {
    $codigo_portal = '<a href="detalle_deuda.php?id='
        .urlencode($cod_deuda_decode)
        .'" align="center">'.$codigo_visible.'</a>';
}

$dw_deudas->set_item($i, 'COD_DEUDA_PORTAL', $codigo_portal);
		}
				
		$dw_deudas->habilitar($t, false);
		
		 print $t->toString();
	}
}

$w = new w_consulta_deuda();
?>