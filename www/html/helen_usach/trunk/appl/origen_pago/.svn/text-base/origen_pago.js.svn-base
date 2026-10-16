function valida_nom_origen_pago(){
	var nom_origen_pago = document.getElementById('NOM_ORIGEN_PAGO_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nom_origen_pago.php?nom_origen_pago="+nom_origen_pago,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El nombre ingresado ya existe. Por favor ingrese otro nombre');
		document.getElementById('NOM_ORIGEN_PAGO_0').value 	= null;
		document.getElementById('NOM_ORIGEN_PAGO_0').focus();
	}
}

function valida_cod_origen_pago(){
	var cod_origen_pago = document.getElementById('COD_ORIGEN_PAGO_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_cod_origen_pago.php?cod_origen_pago="+cod_origen_pago,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El código ingresado ya existe. Por favor ingrese otro código');
		document.getElementById('COD_ORIGEN_PAGO_0').value 	= null;
		document.getElementById('COD_ORIGEN_PAGO_0').focus();
	}
}