function f_get_info_boleta() {
	var cod_boleta	= document.getElementById('COD_BOLETA_0').value;
	if(cod_boleta == ''){
		set_null();
		return;
	}
	ajax			= nuevoAjax();
	cod_boleta		= URLEncode(cod_boleta);
	ajax.open("GET", "ajax_info_boleta.php?cod_boleta="+cod_boleta,false);
    ajax.send(null);
    
    var resp 	= URLDecode(ajax.responseText);
    var lista 	= resp.split('|');
    
    if (lista[0] == "NO") {
    	alert("No Existe Boleta Nro. "+cod_boleta);
    	set_null();
    	return;
    }
    else if (lista[0] == "NO_SB") {
    	alert('La boleta ingresada no posee "Sub Boletas" activas');
    	set_null();
    	return false;
    }
    else if (lista[0] == 0) {
    	alert('La boleta posee no posee deuda "Exigible" o "Cobranza Normal"');
		set_null();
		return;
	}
	document.getElementById('RUT_0').innerHTML 				= number_format(lista[0], 0,',', '.');	
	document.getElementById('DIG_VERIF_0').innerHTML 		= lista[1];	
	document.getElementById('NOMBRE_0').innerHTML 			= lista[2];
	document.getElementById('COD_DEUDA_0').innerHTML		= lista[3];
	document.getElementById('COD_TIPO_DEUDA_0').innerHTML 	= lista[4];
	document.getElementById('ANO_COBRO_0').innerHTML 		= lista[5];
}
function f_get_info_sub_boleta(){
	var cod_sub_boleta	= document.getElementById('COD_SUB_BOLETA_0').value;
	ajax				= nuevoAjax();
	cod_sub_boleta		= URLEncode(cod_sub_boleta);
	ajax.open("GET", "ajax_info_sub_boleta.php?cod_sub_boleta="+cod_sub_boleta,false);
    ajax.send(null);
    
    var resp 	= URLDecode(ajax.responseText);
    var lista 	= resp.split('|');

    if (lista[0] == 'X') {
    	alert("No Existe Sub Boleta Nro. "+cod_sub_boleta);
		set_null();
		return;
	}
		document.getElementById('MONTO_CAPITAL_0').innerHTML	= number_format(lista[0], 4,',', '.');	
		document.getElementById('MONTO_INTERES_0').innerHTML	= number_format(lista[1], 4,',', '.');
		document.getElementById('MONTO_MULTA_0').innerHTML 		= number_format(lista[2], 4,',', '.');
		document.getElementById('TOTAL_MONTO_0').innerHTML 		= number_format(lista[3], 4,',', '.');
		document.getElementById('TOTAL_MONTO_PESO_0').innerHTML	= number_format(lista[4], 0,',', '.');
		document.getElementById('TOTAL_MONTO_PESO_H_0').value = lista[4];		
		document.getElementById('DSCTO_CAPITAL_0').innerHTML	= number_format(lista[5], 4,',', '.');
		document.getElementById('DSCTO_INTERES_0').innerHTML	= number_format(lista[6], 4,',', '.');
		document.getElementById('DSCTO_MULTA_0').innerHTML		= number_format(lista[7], 4,',', '.');
		document.getElementById('TOTAL_DSCTO_0').innerHTML		= number_format(lista[8], 4,',', '.');
		document.getElementById('TOTAL_DSCTO_PESO_0').innerHTML	= number_format(lista[9], 0,',', '.');
		
		document.getElementById('TOTAL_0').innerHTML			= number_format(lista[10], 4,',', '.');
		document.getElementById('TOTAL_PESO_0').innerHTML		= number_format(lista[11], 0,',', '.');
		document.getElementById('TOTAL_PESO_H_0').value 		= number_format(lista[11], 0,',', '');
		
		/*document.getElementById('TOTAL_MONTO_0').innerHTML 		= number_format(lista[4], 4,',', '.');
		document.getElementById('TOTAL_MONTO_PESO_0').innerHTML	= number_format(lista[5], 0,',', '.');
		document.getElementById('TOTAL_MONTO_PESO_H_0').value = lista[5];		
		document.getElementById('DSCTO_CAPITAL_0').innerHTML	= number_format(lista[6], 4,',', '.');
		document.getElementById('DSCTO_INTERES_0').innerHTML	= number_format(lista[7], 4,',', '.');
		document.getElementById('DSCTO_MULTA_0').innerHTML		= number_format(lista[8], 4,',', '.');
		document.getElementById('TOTAL_DSCTO_0').innerHTML		= number_format(lista[9], 4,',', '.');
		document.getElementById('TOTAL_DSCTO_PESO_0').innerHTML	= number_format(lista[10], 0,',', '.');
		
		document.getElementById('TOTAL_0').innerHTML			= number_format(lista[11], 4,',', '.');
		document.getElementById('TOTAL_PESO_0').innerHTML		= number_format(lista[12], 0,',', '.');
		document.getElementById('TOTAL_PESO_H_0').value 		= number_format(lista[12], 0,',', '');*/
}

function validate(){
	var vl_total_peso = to_num(document.getElementById('TOTAL_MONTO_PESO_H_0').value);
	var vl_monto = to_num(document.getElementById('MONTO_0').value);
	if  (vl_monto != vl_total_peso){
		document.getElementById('MONTO_0').focus();
		alert('El monto del pago debe ser igual al monto de la boleta.');
		return false;
	}
	return true;
}
function set_null() {
	document.getElementById('COD_BOLETA_0').value 			= null;
	document.getElementById('RUT_0').innerHTML 				= null;
	document.getElementById('NOMBRE_0').innerHTML 			= null;
	document.getElementById('COD_DEUDA_0').innerHTML 		= null;
	
	document.getElementById('COD_TIPO_DEUDA_0').innerHTML 	= null;
	document.getElementById('ANO_COBRO_0').innerHTML 		= null;
	
	document.getElementById('MONTO_CAPITAL_0').innerHTML 	= null;
	document.getElementById('MONTO_INTERES_0').innerHTML 	= null;
	document.getElementById('MONTO_MULTA_0').innerHTML 		= null;
	document.getElementById('TOTAL_MONTO_0').innerHTML 		= null;
	document.getElementById('TOTAL_MONTO_PESO_0').innerHTML = null;
	document.getElementById('TOTAL_MONTO_PESO_H_0').value = null;
	document.getElementById('TOTAL_PESO_H_0').innerHTML 	= null;
		
	document.getElementById('DSCTO_CAPITAL_0').innerHTML	= null;
	document.getElementById('DSCTO_INTERES_0').innerHTML	= null;
	document.getElementById('DSCTO_MULTA_0').innerHTML		= null;
	document.getElementById('TOTAL_DSCTO_0').innerHTML		= null;
	document.getElementById('DSCTO_PESOS_0').innerHTML		= null;
		
	document.getElementById('TOTAL_0').innerHTML			= null;
	document.getElementById('TOTAL_PESO_0').innerHTML		= null;
}