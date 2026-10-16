<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once ("../../appl.ini");
require_once ("funciones.php");

class w_pago_online{
	function w_pago_online() {
		$cod_plan_pago	= $_REQUEST['CB_COD_PLAN_PAGO_H_0'];
		$monto_abono_pesos	= $_REQUEST['CB_MONTO_PESOS_BOLETA_H_0'];
		$cod_tipo_descuento = "NULL";

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT	 COD_DEUDA
						,NRO_CUOTA
						,ANO_COBRO
				FROM PLAN_PAGO
				WHERE COD_PLAN_PAGO = $cod_plan_pago";
		$result = $db->build_results($sql);
		$cod_deuda = $result[0]['COD_DEUDA'];
		$nro_cuota = $result[0]['NRO_CUOTA'];
		$ano_cobro = $result[0]['ANO_COBRO'];

		$sp = 'sp_boleta_calculo_web';
		$param	= "$cod_deuda, $cod_plan_pago, $cod_tipo_descuento, $monto_abono_pesos"; 

		$db->EXECUTE_SP($sp, $param);

		$sql = "SELECT	 D_MONTO_CAPITAL	
					,D_MONTO_INTERES
					,D_MONTO_MULTA
					,D_MONTO_PESOS
					,D_DSCTO_CAPITAL
					,D_DSCTO_INTERES
					,D_DSCTO_MULTA
					,D_DSCTO_PESOS
			FROM	TEMP_TOTAL_BOLETA";
		$db_c = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result_c = $db_c->build_results($sql);
		$db->ROLLBACK_TRANSACTION();

			if (count($result_c) != 0) {
				$tipo_pago	= 'PAGO_ABONO';
				$cod_deudor	= session::get('COD_DEUDOR');
				$cod_sub_boletas	= 'NULL';
				$monto_capital	= $result_c[0]['D_MONTO_CAPITAL'];
				$monto_interes	= $result_c[0]['D_MONTO_INTERES'];
				$monto_multa	= $result_c[0]['D_MONTO_MULTA'];
				$monto_moneda	= $result_c[0]['D_MONTO_CAPITAL'];
				$monto_pesos	= $result_c[0]['D_MONTO_PESOS'];
				$cod_tipo_descuento = 'NULL';
				$dscto_utm		= 0;
				$dscto_pesos	= 0;
			}else{
				$tipo_pago	= '';
				$cod_deudor	= session::get('COD_DEUDOR');
				$cod_sub_boletas	= 'NULL';
				$monto_capital	= 0;
				$monto_interes	= 0;
				$monto_multa	= 0;
				$monto_moneda	= 0;
				$monto_pesos	= 0;
				$cod_tipo_descuento = 'NULL';
				$dscto_utm		= 0;
				$dscto_pesos	= 0;				
			}
		//formatos de montos
		$monto_pesos_display = number_format($monto_pesos, 0, ',', '.');
		$monto_capital_aux	= number_format($monto_capital, 4, ',', '.');
		$monto_interes_aux	= number_format($monto_interes, 4, ',', '.');
		$monto_multa_aux	= number_format($monto_multa, 4, ',', '.');
		$monto_moneda_aux	= number_format($monto_moneda, 4, ',', '.');
		$monto_pesos_aux	= number_format($monto_pesos, 0, ',', '.');
		
		//$TOTAL_PAGO = $_REQUEST['TOTAL_PESOS_H_0'];
		//$TOTAL_PAGO = number_format($TOTAL_PAGO, 0, ',', '.');
		//$TOTAL_PAGO_H = $_REQUEST['TOTAL_PESOS_H_0'];

		$t = new Template_appl("pago_online.htm");
		$cod_deudor = session::get('COD_DEUDOR');

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

		$sql_tbk = "SELECT	 D.NOMBRE TBK_NOMBRE 
						    ,D.APELLIDO_PATERNO || ' ' || D.APELLIDO_MATERNO TBK_APELLIDO
						    ,DIP.DIRECCION TBK_DIRECCION
						    ,DIP.TELEFONO TBK_FONO
						    ,DIP.EMAIL TBK_EMAIL
							,'$monto_pesos' TBK_MONTO
							,'$monto_pesos_display' TBK_MONTO_DISPLAY
							,CI.NOM_CIUDAD TBK_CIUDAD
							,PS.NOM_PAIS TBK_PAIS
							,'$cod_deuda' COD_DEUDA
							,'$nro_cuota' NRO_CUOTA
							,'$ano_cobro' ANO_COBRO
							,'$tipo_pago'	TIPO_PAGO_H
							,'$cod_deudor'		COD_USUARIO_H
							,'$cod_plan_pago'	COD_PLAN_PAGO_H
							,'$monto_pesos'		MONTO_PESOS_H
							,'$monto_capital_aux'	MONTO_CAPITAL
							,'$monto_interes_aux'	MONTO_INTERES
							,'$monto_multa_aux'		MONTO_MULTA
							,'$monto_moneda_aux'	MONTO_MONEDA
							,'$monto_pesos_aux'		MONTO_PESOS
					FROM 	DEUDOR D left outer	join DIRECCION DIP on DIP.COD_DIRECCION = f_get_direccion_deudor(D.COD_DEUDOR,'P')
									 left outer join COMUNA CO on DIP.COD_COMUNA = CO.COD_COMUNA
									 left outer join CIUDAD CI on DIP.COD_CIUDAD = CI.COD_CIUDAD
									 left outer join PAIS PS on DIP.COD_PAIS = PS.COD_PAIS
					WHERE 	D.COD_DEUDOR = $cod_deudor";
					
		$dw_transbank = new datawindow($sql_tbk);
		
		$dw_transbank->retrieve();
		$dw_transbank->habilitar($t, false);

		print $t->toString();
	}
}		
$w = new w_pago_online();
?>