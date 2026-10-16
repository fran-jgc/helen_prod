<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once ("../../appl.ini");
include("funciones.php");

class w_cupon_pago extends w_base {
	var $dws = array();
	var $cod_deudor;
	
	function __construct() {
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
					,D.COD_ESTADO_DEUDA
					,D.COD_CARTERA
					,PP.COD_ESTADO_PLAN_PAGO
				
				FROM DEUDOR DE
					,PLAN_PAGO PP
		        	,DEUDA D
		      		,TIPO_DEUDA TD
		      		,ESTADO_PLAN_PAGO EPP
				
				WHERE DE.COD_DEUDOR = $this->cod_deudor
				  AND D.COD_DEUDOR = DE.COD_DEUDOR 
 		          AND PP.COD_DEUDA = D.COD_DEUDA
				  AND PP.COD_ESTADO_PLAN_PAGO NOT IN (3,4)
		          AND D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
		          AND EPP.COD_ESTADO_PLAN_PAGO = PP.COD_ESTADO_PLAN_PAGO
				  AND D.COD_ESTADO_DEUDA = 2
				UNION
				
				SELECT  D.COD_DEUDA
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
					,D.COD_ESTADO_DEUDA
					,D.COD_CARTERA
					,PP.COD_ESTADO_PLAN_PAGO
				FROM DEUDOR DE
					,PLAN_PAGO PP
		        	,DEUDA D
		      		,TIPO_DEUDA TD
		      		,ESTADO_PLAN_PAGO EPP
				
				WHERE DE.COD_DEUDOR = $this->cod_deudor
				  AND D.COD_DEUDOR = DE.COD_DEUDOR 
		          AND PP.COD_DEUDA = D.COD_DEUDA
				  AND PP.COD_ESTADO_PLAN_PAGO= 4 -- MOROSA 
		          AND D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
		          AND EPP.COD_ESTADO_PLAN_PAGO = PP.COD_ESTADO_PLAN_PAGO
				  AND D.COD_ESTADO_DEUDA = 2
				ORDER BY 3 ASC";

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
					,D.COD_ESTADO_DEUDA
					,D.COD_CARTERA
					,PP.COD_ESTADO_PLAN_PAGO
				FROM    DEUDOR DE
					,PLAN_PAGO PP
		        		,DEUDA    D
		      			,TIPO_DEUDA TD
		      			,ESTADO_PLAN_PAGO EPP
				WHERE DE.COD_DEUDOR = $this->cod_deudor
				  AND D.COD_DEUDOR = DE.COD_DEUDOR
 	        		  AND PP.COD_DEUDA = D.COD_DEUDA
	        		  AND D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
	        		  AND EPP.COD_ESTADO_PLAN_PAGO = PP.COD_ESTADO_PLAN_PAGO
			        ORDER BY NRO_CUOTA";

		$this->dws['dw_plan_pago'] = new datawindow($sql, 'detalle_cuotas');
		$this->dws['dw_plan_pago']->add_control(new static_num('SALDO', 4));
		$this->dws['dw_plan_pago']->add_control(new static_num('SALDO_PESOS'));
		$this->dws['dw_plan_pago']->retrieve();

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$imprimir = '';
		for($i = 0; $i < $this->dws['dw_plan_pago']->row_count(); $i++){
			$saldo_pesos        = $this->dws['dw_plan_pago']->get_item($i, 'SALDO_PESOS');
			$cod_plan_pago      = $this->dws['dw_plan_pago']->get_item($i, 'COD_PLAN_PAGO');
			$cod_familia_deuda  = $this->dws['dw_plan_pago']->get_item($i, 'COD_FAMILIA_DEUDA');
			$num_cuota          = $this->dws['dw_plan_pago']->get_item($i, 'NRO_CUOTA');
			$cod_tipo_deuda     = $this->dws['dw_plan_pago']->get_item($i, 'COD_TIPO_DEUDA');
			$ano_inicio_cobro   = $this->dws['dw_plan_pago']->get_item($i, 'ANO_INICIO_COBRO');
			/////nuevas validaciones
			$cod_estado_deuda   = $this->dws['dw_plan_pago']->get_item($i, 'COD_ESTADO_DEUDA');
			$cod_cartera        = $this->dws['dw_plan_pago']->get_item($i, 'COD_CARTERA');
			$cod_estado_planpago= $this->dws['dw_plan_pago']->get_item($i, 'COD_ESTADO_PLAN_PAGO');

			if ($saldo_pesos <= 0 or $cod_cartera != 6 or $cod_estado_deuda == 4){ 
				//$cod_tipo_deuda = $this->dws['dw_plan_pago']->get_item($i, 'COD_TIPO_DEUDA');
				if ($cod_tipo_deuda == 'CRPI' OR $cod_tipo_deuda == 'CR12' OR $cod_tipo_deuda == 'CRI')
					$imprimir = ' ';
				else
					$imprimir = '';
			}else {
				if ($cod_familia_deuda == 1){ //1 DEUDA = FONDO SOLIDARIO
					//valida  CS no cruzados con a�o inicio de cobro >= 2006 y los CSR no cruzados con a�o inicio cobro >=2004
					//$cod_tipo_deuda = $this->dws['dw_plan_pago']->get_item($i, 'COD_TIPO_DEUDA');
					//$ano_inicio_cobro = $this->dws['dw_plan_pago']->get_item($i, 'ANO_INICIO_COBRO');
					
					if ($cod_tipo_deuda == 'CU'){
						$imprimir = '';
					}
					/*else if ($cod_tipo_deuda == 'CRPI' OR $cod_tipo_deuda == 'CR12'){
						$sql = "select max(B.COD_BOLETA) COD_BOLETA
								from  BOLETA B
								where B.COD_PLAN_PAGO = $cod_plan_pago
								  and B.CARTERA_CONOCIDA = 'S'";
						$result = $db->build_results($sql);
						$cod_boleta = $result[0]['COD_BOLETA'];
						
						if ($cod_boleta=='')
							$imprimir = 'Plan Pago Sin Boleta';
						else
							$imprimir = '<a href="#" onclick="window.open(\'imprime_cupon.php?cod_boleta='.$cod_boleta.'\');"><img src="images/printer.png" /></a>';
							if ($cod_tipo_deuda == 'CR12') {  
							    $imprimir = '&nbsp;';}
					}*/
					else{
						$sql = "select max(B.COD_BOLETA) COD_BOLETA
								from  BOLETA B
								where B.COD_PLAN_PAGO = $cod_plan_pago
								  and B.CARTERA_CONOCIDA = 'S'
								  AND To_number(To_Char(B.FECHA_BOLETA,'yyyy')) >= 2012";
						
						$sql = "select max(B.COD_BOLETA) COD_BOLETA
								from  BOLETA B
								where B.COD_PLAN_PAGO = $cod_plan_pago
								  and B.CARTERA_CONOCIDA = 'S'
								  AND To_Char(B.FECHA_BOLETA,'yyyy') = To_Char(SYSDATE,'yyyy')";

						$result = $db->build_results($sql);
						$cod_boleta = $result[0]['COD_BOLETA'];
						
						if ($cod_boleta==''){
							$imprimir = 'Sin Cupon de pago';
						}else{
							if ($cod_tipo_deuda == 'CS' || $cod_tipo_deuda == 'CSR' || $cod_tipo_deuda == 'CSN'|| $cod_tipo_deuda == 'CR12'|| $cod_tipo_deuda == 'CRPI'|| $cod_tipo_deuda == 'CRI'){
								if ($cod_tipo_deuda == 'CS' and $ano_inicio_cobro >= 2006){
									$imprimir = '<a href="#" onclick="window.open(\'imprime_cupon.php?cod_boleta='.$cod_boleta.'\');"><img src="imagen/printer.png" /></a>';
								}else if ($cod_tipo_deuda == 'CSR' and $ano_inicio_cobro >= 2004){
									$imprimir = '<a href="#" onclick="window.open(\'imprime_cupon.php?cod_boleta='.$cod_boleta.'\');"><img src="imagen/printer.png" /></a>';
								}else if ($cod_tipo_deuda == 'CSN' and $ano_inicio_cobro >= 2006){
									$imprimir = '<a href="#" onclick="window.open(\'imprime_cupon.php?cod_boleta='.$cod_boleta.'\');"><img src="imagen/printer.png" /></a>';
								}else if ($cod_tipo_deuda == 'CR12' ){
									$imprimir = '<a href="#" onclick="window.open(\'imprime_cupon.php?cod_boleta='.$cod_boleta.'\');"><img src="imagen/printer.png" /></a>';
								}else if ($cod_tipo_deuda == 'CRI' ){
									$imprimir = '<a href="#" onclick="window.open(\'imprime_cupon.php?cod_boleta='.$cod_boleta.'\');"><img src="imagen/printer.png" /></a>';
								}else{
									$imprimir = '';
								}
							}

							/*elseif ($cod_tipo_deuda == 'CRI' ){
									$imprimir = '<a href="#" onclick="window.open(\'imprime_cupon.php?cod_boleta='.$cod_boleta.'\');"><img src="images/printer.png" /></a>';
								}
							
							else if ($cod_tipo_deuda == 'CRPI' || $cod_tipo_deuda == 'CR12'){
								$imprimir = '<a href="#" onclick="window.open(\'imprime_cupon.php?cod_boleta='.$cod_boleta.'\');"><img src="images/printer.png" /></a>';
							}else{
								$imprimir = '';
							}*/
							
						}
					}
					if ($num_cuota == '99' or $cod_estado_planpago == 4) { 
					    $imprimir = '&nbsp;';}
				}else{
					$imprimir = '';					
				}
			}
			$this->dws['dw_plan_pago']->set_item($i, 'IMPRIMIR', $imprimir);
		}	

		// borra las cuotas que no tienen boleta para imrimir
//		for($i = 0; $i < $this->dws['dw_plan_pago']->row_count(); ){
			//$imprimir = $this->dws['dw_plan_pago']->get_item($i, 'IMPRIMIR');
			//if ($imprimir!='')
//				$i++;
			//else
			//	$this->dws['dw_plan_pago']->delete_row($i);
//		}
					
		$this->dws['dw_plan_pago']->habilitar($t, false);
		print $t->toString();
	}
}
$w = new w_cupon_pago();
?>