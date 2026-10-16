<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once ("../../appl.ini");
include("funciones.php");

class w_cupon_pago extends w_base {
	var $dws = array();
	var $cod_deudor;
	
	function w_cupon_pago() {
		valida_inicio();
		$this->cod_deudor = session::get('cod_deudor');
		
		$t = new Template_appl("cupon_pago.html");

		if (K_TIPO_BD=='oci')
			$sql = "SELECT  RUT
							,DIG_VERIF
					        ,NOMBRE||' '||APELLIDO_PATERNO||' '||APELLIDO_MATERNO NOMBRE
					FROM    DEUDOR 
					WHERE	COD_DEUDOR = $this->cod_deudor";
		else if (K_TIPO_BD=='mssql')
			$sql= "	SELECT  RUT
							,DIG_VERIF
					        ,NOMBRE + ' ' + APELLIDO_PATERNO + ' ' + APELLIDO_MATERNO NOMBRE
					FROM    DEUDOR
					WHERE	COD_DEUDOR = $this->cod_deudor";

		$this->dws['dw_cabecera'] = new datawindow($sql);
		$this->dws['dw_cabecera']->add_control(new static_num('RUT'));
		$this->dws['dw_cabecera']->retrieve();
		$this->dws['dw_cabecera']->habilitar($t, false);

		if (K_TIPO_BD=='oci')
			$sql = "SELECT  D.COD_DEUDA
							,D.COD_TIPO_DEUDA
							,NRO_CUOTA
							,ANO_COBRO
							,to_char(FECHA_VENCTO,'dd/mm/yyyy') FECHA_VENCTO
							,f_pp_saldo_plan_pago(COD_PLAN_PAGO, sysdate) SALDO
							,round(f_pp_saldo_plan_pago(COD_PLAN_PAGO, sysdate) * f_mon_get_valor_moneda(cod_moneda, sysdate),0) SALDO_PESOS
							,NOM_ESTADO_PLAN_PAGO
							,'' IMPRIMIR
							,To_Number(To_Char(FECHA_INICIO_COBRO, 'yyyy')) ANO_INICIO_COBRO
							,PP.COD_PLAN_PAGO
							,TD.COD_FAMILIA_DEUDA
							,D.COD_TIPO_DEUDA
					FROM    DEUDOR DE
							,PLAN_PAGO PP
			        		,DEUDA    D
			      			,TIPO_DEUDA TD
			      			,ESTADO_PLAN_PAGO EPP
					WHERE	DE.COD_DEUDOR = $this->cod_deudor
							AND D.COD_DEUDOR = DE.COD_DEUDOR 
			        		AND PP.COD_DEUDA = D.COD_DEUDA
			        		AND D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
			        		AND EPP.COD_ESTADO_PLAN_PAGO = PP.COD_ESTADO_PLAN_PAGO
			        order by NRO_CUOTA";
		else if (K_TIPO_BD=='mssql')
			$sql = "SELECT  D.COD_DEUDA
							,D.COD_TIPO_DEUDA
							,NRO_CUOTA
							,ANO_COBRO
							,convert(varchar, FECHA_VENCTO,103) FECHA_VENCTO
							,dbo.f_pp_saldo_plan_pago(COD_PLAN_PAGO, getdate()) SALDO
							,round(dbo.f_pp_saldo_plan_pago(COD_PLAN_PAGO, getdate()) * dbo.f_mon_get_valor_moneda(cod_moneda, getdate()),0) SALDO_PESOS
							,NOM_ESTADO_PLAN_PAGO
							,'' IMPRIMIR
							,PP.COD_PLAN_PAGO
							,TD.COD_FAMILIA_DEUDA
							,D.COD_TIPO_DEUDA
					FROM    DEUDOR DE
							,PLAN_PAGO PP
			        		,DEUDA    D
			      			,TIPO_DEUDA TD
			      			,ESTADO_PLAN_PAGO EPP
					WHERE   DE.COD_DEUDOR = $this->cod_deudor
							AND D.COD_DEUDOR = DE.COD_DEUDOR
			        		AND PP.COD_DEUDA = D.COD_DEUDA
			        		AND D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
			        		AND EPP.COD_ESTADO_PLAN_PAGO = PP.COD_ESTADO_PLAN_PAGO
			        order by NRO_CUOTA";
		$this->dws['dw_plan_pago'] = new datawindow($sql, 'detalle_cuotas');
		$this->dws['dw_plan_pago']->add_control(new static_num('SALDO', 4));
		$this->dws['dw_plan_pago']->add_control(new static_num('SALDO_PESOS'));
		$this->dws['dw_plan_pago']->retrieve();
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$imprimir = '';
		for($i = 0; $i < $this->dws['dw_plan_pago']->row_count(); $i++){
			$saldo_pesos = $this->dws['dw_plan_pago']->get_item($i, 'SALDO_PESOS');
			$cod_plan_pago = $this->dws['dw_plan_pago']->get_item($i, 'COD_PLAN_PAGO');
			$cod_familia_deuda = $this->dws['dw_plan_pago']->get_item($i, 'COD_FAMILIA_DEUDA');
			
			if ($saldo_pesos <= 0){ 
				$cod_tipo_deuda = $this->dws['dw_plan_pago']->get_item($i, 'COD_TIPO_DEUDA');
				if ($cod_tipo_deuda == 'CRPI' OR $cod_tipo_deuda == 'CR12')
					$imprimir = ' ';
				else
					$imprimir = '';
			}else {
				if ($cod_familia_deuda == 1){ //1 DEUDA = FONDO SOLIDARIO
					//valida  CS no cruzados con año inicio de cobro >= 2006 y los CSR no cruzados con año inicio cobro >=2004
					$cod_tipo_deuda = $this->dws['dw_plan_pago']->get_item($i, 'COD_TIPO_DEUDA');
					$ano_inicio_cobro = $this->dws['dw_plan_pago']->get_item($i, 'ANO_INICIO_COBRO');
					
					if ($cod_tipo_deuda == 'CU'){
						$imprimir = '';
					}else if ($cod_tipo_deuda == 'CRPI' OR $cod_tipo_deuda == 'CR12'){
						$sql = "select max(B.COD_BOLETA) COD_BOLETA
								from  BOLETA B
								where B.COD_PLAN_PAGO = $cod_plan_pago
								  and B.CARTERA_CONOCIDA = 'S'";
						$result = $db->build_results($sql);
						$cod_boleta = $result[0]['COD_BOLETA'];
						if ($cod_boleta=='')
							$imprimir = '';
						else
							$imprimir = '<a href="#" onclick="window.open(\'imprime_cupon.php?cod_boleta='.$cod_boleta.'\');"><img src="images/printer.png" /></a>';
							$imprimir = ' ** ';
					}else{
						$sql = "select max(B.COD_BOLETA) COD_BOLETA
								from  BOLETA B
								where B.COD_PLAN_PAGO = $cod_plan_pago
								  and B.CARTERA_CONOCIDA = 'S'
								  AND To_number(To_Char(B.FECHA_BOLETA,'yyyy')) >= 2012";
						
						$result = $db->build_results($sql);
						$cod_boleta = $result[0]['COD_BOLETA'];
						
						if ($cod_boleta==''){
							$imprimir = '';
						}else{
							if ($cod_tipo_deuda == 'CS' || $cod_tipo_deuda == 'CSR' || $cod_tipo_deuda == 'CSN'){
								if ($cod_tipo_deuda == 'CS' and $ano_inicio_cobro >= 2006){
									$imprimir = '<a href="#" onclick="window.open(\'imprime_cupon.php?cod_boleta='.$cod_boleta.'\');"><img src="images/printer.png" /></a>';
								}else if ($cod_tipo_deuda == 'CSR' and $ano_inicio_cobro >= 2004){
									$imprimir = '<a href="#" onclick="window.open(\'imprime_cupon.php?cod_boleta='.$cod_boleta.'\');"><img src="images/printer.png" /></a>';
								}else if ($cod_tipo_deuda == 'CSN' and $ano_inicio_cobro >= 2006){
									$imprimir = '<a href="#" onclick="window.open(\'imprime_cupon.php?cod_boleta='.$cod_boleta.'\');"><img src="images/printer.png" /></a>';
								}else{
									$imprimir = '';
								}
							}else if ($cod_tipo_deuda == 'CRPI' || $cod_tipo_deuda == 'CR12'){
								$imprimir = '<a href="#" onclick="window.open(\'imprime_cupon.php?cod_boleta='.$cod_boleta.'\');"><img src="images/printer.png" /></a>';
							}else{
								$imprimir = '';
							}
						}
					}
				}else{
					$imprimir = '';					
				}
			}
			$this->dws['dw_plan_pago']->set_item($i, 'IMPRIMIR', $imprimir);
		}	

		// borra las cuotas que no tienen boleta para imrimir
		for($i = 0; $i < $this->dws['dw_plan_pago']->row_count(); ){
			$imprimir = $this->dws['dw_plan_pago']->get_item($i, 'IMPRIMIR');
			if ($imprimir!='')
				$i++;
			else
				$this->dws['dw_plan_pago']->delete_row($i);
		}
					
		$this->dws['dw_plan_pago']->habilitar($t, false);
		print $t->toString();
	}
}
$w = new w_cupon_pago();
?>