function valida_nom_carrera(){
	var nom_carrera = document.getElementById('NOM_CARRERA_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nom_carrera.php?nom_carrera="+nom_carrera,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El nombre ingresado ya existe. Por favor ingrese otro nombre');
		document.getElementById('NOM_CARRERA_0').value 	= null;
		document.getElementById('NOM_CARRERA_0').focus();
	}
}

function valida_cod_carrera(){
	var cod_carrera = document.getElementById('COD_CARRERA_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_cod_carrera.php?cod_carrera="+cod_carrera,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El código ingresado ya existe. Por favor ingrese otro código');
		document.getElementById('COD_CARRERA_0').value 	= null;
		document.getElementById('COD_CARRERA_0').focus();
	}
}