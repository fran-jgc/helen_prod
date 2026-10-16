function valida_nom_estado(){
	var nom_estado = document.getElementById('NOM_ESTADO_PAGARE_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nombre_estado.php?nom_estado="+nom_estado,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El nombre ingresado ya existe. Por favor ingrese otro nombre');
		document.getElementById('NOM_ESTADO_PAGARE_0').value 	= null;
		document.getElementById('NOM_ESTADO_PAGARE_0').focus();
	}
}