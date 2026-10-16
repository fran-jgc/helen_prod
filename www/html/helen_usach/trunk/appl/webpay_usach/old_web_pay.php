<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
include("funciones.php");
class w_web_pay_pago{
	function w_web_pay_pago() {
		valida_inicio();
		
		$t = new Template_appl("web_pay.htm");

		$cod_deudor = session::get('COD_DEUDOR');
		$cod_estado_deuda = 2; //ESTADO_COBRANZA_NORMAL

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT	 D.RUT
					    ,D.DIG_VERIF
					    ,D.NOMBRE || ' ' || D.APELLIDO_PATERNO || ' ' || D.APELLIDO_MATERNO NOMBRE
					    ,DIP.DIRECCION  DIRECCION
					    ,DIP.TELEFONO FONO_PARTICULAR
					    ,DIP.EMAIL EMAIL_PARTICULAR
					    ,'' ABONO_SUB_BOLETA
					    ,'' CB_COD_PLAN_PAGO_H
					    ,'' CB_MONTO_PESOS_BOLETA_H
				FROM 	DEUDOR D left outer join DIRECCION DIP 
								on DIP.COD_DIRECCION = f_get_direccion_deudor(D.COD_DEUDOR,'P') 
				WHERE 	D.COD_DEUDOR= $cod_deudor";
					
		$dw_datos_deudor = new datawindow($sql);
		$dw_datos_deudor->add_control(new static_num('RUT'));
		$dw_datos_deudor->add_control(new edit_num('ABONO_SUB_BOLETA', 10, 10));
		$dw_datos_deudor->add_control(new edit_text_hidden('CB_COD_PLAN_PAGO_H', 10, 10));
		$dw_datos_deudor->add_control(new edit_text_hidden('CB_MONTO_PESOS_BOLETA_H', 10, 10));
				
		$dw_datos_deudor->retrieve();
		$dw_datos_deudor->habilitar($t, true);
		
		$sql_deudas = "SELECT	 COD_DEUDA LINK_DEUDA
								,COD_DEUDA
								,NOM_ESTADO_DEUDA
								,D.COD_TIPO_DEUDA
								,SIMBOLO
								,To_Char(FECHA_INICIO_COBRO, 'dd/mm/yyyy') FECHA_INICIO_COBRO
								,f_deuda_saldo_total(COD_DEUDA, sysdate)   SALDO
								,f_deuda_saldo_total(COD_DEUDA, sysdate) * f_mon_get_valor_moneda(td.cod_moneda, sysdate) SALDO_PESOS
						FROM   	DEUDA D
								,ESTADO_DEUDA ED
								,MONEDA M
								,TIPO_DEUDA TD
						WHERE	D.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA
						  AND	M.COD_MONEDA = TD.COD_MONEDA
						  AND	TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA 
						  AND	D.COD_DEUDOR = ".$cod_deudor;

		$dw_deudas = new datawindow($sql_deudas, 'deuda');
		$dw_deudas->add_control(new static_num('SALDO', 4));
		$dw_deudas->add_control(new static_num('SALDO_PESOS'));
		$dw_deudas->retrieve();
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		for($i = 0; $i < $dw_deudas->row_count(); $i++){
			$cod_deuda = $dw_deudas->get_item($i, 'COD_DEUDA');
			
			$sql = "select	count(*) CANTIDAD
					from	DEUDA
					where	COD_DEUDA = $cod_deuda
					and		COD_ESTADO_DEUDA = $cod_estado_deuda";
			
			$result = $db->build_results($sql);
			$cant_deuda = $result[0]['CANTIDAD'];
			
			if ($cant_deuda == 0){
					$link_deuda = $cod_deuda;
			}else{
					$link_deuda = '<a href="#" onclick="display_plan_pago('.$cod_deuda.');">'.$cod_deuda.'</a>';
			}
			
			$dw_deudas->set_item($i, 'LINK_DEUDA', $link_deuda);
		}
		
		$dw_deudas->habilitar($t, false);

		print $t->toString();
	}
}

$w = new w_web_pay_pago();
?>