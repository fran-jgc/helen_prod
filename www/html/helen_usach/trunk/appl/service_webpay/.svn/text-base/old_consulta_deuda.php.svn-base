<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
include("funciones.php");
class w_consulta_deuda{
	function w_consulta_deuda() {
		valida_inicio();
		
		$t = new Template_appl("consulta_deuda.html");

		$cod_deudor = session::get('cod_deudor');

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

		$sql = "SELECT	D.RUT
					    ,D.DIG_VERIF
					    ,D.NOMBRE||' '||D.APELLIDO_PATERNO||' '||D.APELLIDO_MATERNO NOMBRE
					    ,DIP.DIRECCION  DIRECCION
					    ,DIP.TELEFONO FONO_PARTICULAR
					    ,DIP.EMAIL EMAIL_PARTICULAR
					    ,DIC.DIRECCION DIRECCION_COMERCIAL
					    ,DIC.TELEFONO FONO_COMERCIAL
					    ,DIC.EMAIL EMAIL_COMERCIAL
				FROM 	DEUDOR D
					    ,DIRECCION DIP
					    ,DIRECCION DIC
				WHERE 	D.COD_DEUDOR= ".$cod_deudor."
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
		}
				
		$dw_deudas->habilitar($t, false);
		
		 print $t->toString();
	}
}

$w = new w_consulta_deuda();
?>