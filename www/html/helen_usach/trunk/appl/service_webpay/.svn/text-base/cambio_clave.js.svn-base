function validate1() {
	var vl_actual_clave = URLEncode(document.getElementById('ACTUAL_CLAVE_0').value);
	var vl_nueva_clave = URLEncode(document.getElementById('NUEVA_CLAVE_0').value);
	var vl_confirma_clave = URLEncode(document.getElementById('CONFIRMA_CLAVE_0').value);

	var ajax=nuevoAjax();
	ajax.open("GET", "ajax_valida_cambio_clave.php?actual_clave="+vl_actual_clave+"&nueva_clave="+vl_nueva_clave+"&confirma_clave="+vl_confirma_clave, false);
	ajax.send(null);
	
	var vl_resp = URLDecode(ajax.responseText);
	if (vl_resp=='PASSWORD_INVALIDA') {
		alert('La clave ingresada como actual no es correcta.');
		return false;
	}		
	else if (vl_resp=='PASSWORD_VACIA') {
		alert('La nueva clave no puede ser vacía.');
		return false;
	}		
	else if (vl_resp=='CONFIRMA_ERRONEA') {
		alert('La confirmación de clave no es igual a la clave ingresada.');
		return false;
	}		
	else // OK
		return true;
}