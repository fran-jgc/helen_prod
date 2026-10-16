function valida_nom_ciudad(){
	var nom_ciudad = document.getElementById('NOM_CIUDAD_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nom_ciudad.php?nom_ciudad="+nom_ciudad,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El nombre ingresado ya existe. Por favor ingrese otro nombre');
		document.getElementById('NOM_CIUDAD_0').value = null;
		document.getElementById('NOM_CIUDAD_0').focus();
	}
}

function valida_cod_ciudad(){
	var cod_ciudad = document.getElementById('COD_CIUDAD_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_cod_ciudad.php?cod_ciudad="+cod_ciudad,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El código ingresado ya existe. Por favor ingrese otro código');
		document.getElementById('COD_CIUDAD_0').value 	= null;
		document.getElementById('COD_CIUDAD_0').focus();
	}
}