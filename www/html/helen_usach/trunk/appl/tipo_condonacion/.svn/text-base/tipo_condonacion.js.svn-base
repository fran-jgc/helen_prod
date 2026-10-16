function valida_nom_tipo_condonacion(){
	var nom_tipo_condonacion = document.getElementById('NOM_TIPO_CONDONACION_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nom_tipo_condonacion.php?nom_tipo_condonacion="+nom_tipo_condonacion,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El nombre ingresado ya existe. Por favor ingrese otro nombre');
		document.getElementById('NOM_TIPO_CONDONACION_0').value 	= null;
		document.getElementById('NOM_TIPO_CONDONACION_0').focus();
	}
}