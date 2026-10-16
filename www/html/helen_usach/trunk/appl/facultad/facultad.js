function valida_nom_facultad(ve_nom_facu){
	var nom_facultad = ve_nom_facu.value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nom_facultad.php?nom_facultad="+nom_facultad,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El nombre de FACULTAD ingresado, ya existe');
		document.getElementById('NOM_FACULTAD_0').value 	= null;
		document.getElementById('NOM_FACULTAD_0').focus();
	}
}