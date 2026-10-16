function valida_nom_cta_corriente(){
	var nom_cta_corriente = document.getElementById('NOM_CUENTA_CORRIENTE_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nom_cta_corriente.php?nom_cta_corriente="+nom_cta_corriente,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El nombre ingresado ya existe. Por favor ingrese otro nombre');
		document.getElementById('NOM_CUENTA_CORRIENTE_0').value 	= null;
		document.getElementById('NOM_CUENTA_CORRIENTE_0').focus();
	}
}