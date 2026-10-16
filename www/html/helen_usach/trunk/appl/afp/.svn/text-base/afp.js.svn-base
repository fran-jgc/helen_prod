function valida_nom_afp(){
	var nom_afp = document.getElementById('NOM_AFP_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nombre_afp.php?nom_afp="+nom_afp,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El nombre ingresado ya existe. Por favor ingrese otro nombre');
		document.getElementById('NOM_AFP_0').value 	= null;
		document.getElementById('NOM_AFP_0').focus();
	}
}