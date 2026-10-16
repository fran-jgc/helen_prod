function valida_nom_tipo_bitacora(){
	var nom_tipo_bitacora = document.getElementById('NOM_TIPO_BITACORA_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nom_tipo_bitacora.php?nom_tipo_bitacora="+nom_tipo_bitacora,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El nombre ingresado ya existe. Por favor ingrese otro nombre');
		document.getElementById('NOM_TIPO_BITACORA_0').value 	= null;
		document.getElementById('NOM_TIPO_BITACORA_0').focus();
	}
}