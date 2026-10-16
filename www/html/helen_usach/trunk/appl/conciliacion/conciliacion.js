function set_montos_resumen(){
	document.getElementById('CARTOLA_INGRESO_MES_0').innerHTML	= document.getElementById('SUM_MONTO_INGRESO_C_0').innerHTML;
	document.getElementById('CARTOLA_INGRESO_MES_H_0').value	= document.getElementById('SUM_MONTO_INGRESO_C_H_0').value;
	document.getElementById('CARTOLA_EGRESO_MES_0').innerHTML 	= document.getElementById('SUM_MONTO_EGRESO_C_0').innerHTML;
	document.getElementById('CARTOLA_EGRESO_MES_H_0').value 	= document.getElementById('SUM_MONTO_EGRESO_C_H_0').value;
	document.getElementById('CARTOLA_CHEQUE_MES_0').innerHTML 	= document.getElementById('SUM_MONTO_CHEQUE_C_0').innerHTML;
	document.getElementById('CARTOLA_CHEQUE_MES_H_0').value 	= document.getElementById('SUM_MONTO_CHEQUE_C_H_0').value;
	
	var monto_anterior 		= document.getElementById('MONTO_ANTERIOR_RESUMEN_H_0').value;
	var monto_cartola_ing	= document.getElementById('CARTOLA_INGRESO_MES_H_0').value;
	var monto_cartola_egr	= document.getElementById('CARTOLA_EGRESO_MES_H_0').value;
	var monto_cartola_che	= document.getElementById('CARTOLA_CHEQUE_MES_H_0').value;
	document.getElementById('MONTO_MES_ACTUAL_0').innerHTML	=  number_format(parseFloat(monto_anterior) + parseFloat(monto_cartola_ing) - parseFloat(monto_cartola_egr), 0,',', '.');	
	document.getElementById('MONTO_MES_ACTUAL_H_0').value 	=	parseFloat(monto_anterior) + parseFloat(monto_cartola_ing) - parseFloat(monto_cartola_egr);
	var monto_actual		= document.getElementById('MONTO_MES_ACTUAL_H_0').value;
	document.getElementById('SALDO_SEGUN_CARTOLA_0').innerHTML	=  number_format(parseFloat(monto_actual) + parseFloat(monto_cartola_che), 0,',', '.');
}
function set_valores(){
	var mes 	= document.getElementById('MES_0').value;
	var ano 	= document.getElementById('ANO_0').value;
	var cuenta	= document.getElementById('COD_CUENTA_CORRIENTE_0');
	cuenta 		= cuenta.options[cuenta.selectedIndex].value;
	
	if (mes != '' && ano != '' && cuenta != ''){
		var ajax = nuevoAjax();
		ajax.open("GET", "ajax_set_valores.php?mes="+mes+"&ano="+ano+"&cta_cte="+cuenta, false);
		ajax.send(null);
		var resp = ajax.responseText.split('|');
		document.getElementById('MES_ACTUAL_0').innerHTML				= mes;
		document.getElementById('ANO_ACTUAL_0').innerHTML				= ano;
		if(mes == 1){
			document.getElementById('MES_ANTERIOR_0').innerHTML				= 12;
			document.getElementById('ANO_ANTERIOR_0').innerHTML				= ano - 1;
		}
		else{
			document.getElementById('MES_ANTERIOR_0').innerHTML				= mes - 1;
			document.getElementById('ANO_ANTERIOR_0').innerHTML				= ano;
		}
		document.getElementById('INGRESO_MES_0').innerHTML 				= number_format(resp[0], 0,',', '.');
		document.getElementById('INGRESO_MES_H_0').value				= resp[0];
		document.getElementById('INGRESO_MES_RESUMEN_0').innerHTML		= number_format(resp[0], 0,',', '.');
		
		document.getElementById('EGRESO_MES_0').innerHTML 				= number_format(resp[1], 0,',', '.');
		document.getElementById('EGRESO_MES_H_0').value					= resp[1];
		document.getElementById('EGRESO_MES_RESUMEN_0').innerHTML		= number_format(resp[1], 0,',', '.');
		
		document.getElementById('MONTO_ANTERIOR_RESUMEN_0').innerHTML	= number_format(resp[2], 0,',', '.');
		document.getElementById('MONTO_ANTERIOR_RESUMEN_H_0').value		= resp[2];
	}
	else{
		document.getElementById('INGRESO_MES_0').innerHTML 			= null;
		document.getElementById('INGRESO_MES_H_0').value			= null;
		document.getElementById('INGRESO_MES_RESUMEN_0').innerHTML	= null;
		document.getElementById('EGRESO_MES_0').innerHTML 			= null;
		document.getElementById('EGRESO_MES_H_0').value				= null;
		document.getElementById('EGRESO_MES_RESUMEN_0').innerHTML	= null;
	}
}
function validate(){
	var mes 	= document.getElementById('MES_0').value;
	var ano 	= document.getElementById('ANO_0').value;

	var cuenta = document.getElementById('COD_CUENTA_CORRIENTE_0');
	cuenta = cuenta.options[cuenta.selectedIndex].value;

	var ajax = nuevoAjax();
	ajax.open("GET", "ajax_existe_conciliacion.php?mes="+mes+"&ano="+ano+"&cta_cte="+cuenta, false);
	ajax.send(null);
	var resp = ajax.responseText.split('|');

	if(resp == 'EXISTE_CONCILIACION'){
		alert('Ya existe una conciliación para este Mes y año');
		return false;
	}
}
function set_cabecera(){
	var cuenta = document.getElementById('COD_CUENTA_CORRIENTE_0');
	cuenta = cuenta.options[cuenta.selectedIndex].value;
}