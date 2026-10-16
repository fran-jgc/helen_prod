function valida_nom_tipo_suspension(){
	var nom_tipo_suspension = document.getElementById('NOM_TIPO_SUSPENSION_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nom_tipo_suspension.php?nom_tipo_suspension="+nom_tipo_suspension,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El nombre ingresado ya existe. Por favor ingrese otro nombre');
		document.getElementById('NOM_TIPO_SUSPENSION_0').value 	= null;
		document.getElementById('NOM_TIPO_SUSPENSION_0').focus();
	}
}