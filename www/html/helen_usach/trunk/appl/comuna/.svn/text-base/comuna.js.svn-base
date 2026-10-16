function valida_nom_comuna(){
	var nom_comuna = document.getElementById('NOM_COMUNA_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nom_comuna.php?nom_comuna="+nom_comuna,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El nombre ingresado ya existe. Por favor ingrese otro nombre');
		document.getElementById('NOM_COMUNA_0').value = null;
		document.getElementById('NOM_COMUNA_0').focus();
	}
}

function valida_cod_comuna(){
	var cod_comuna = document.getElementById('COD_COMUNA_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_cod_comuna.php?cod_comuna="+cod_comuna,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El código ingresado ya existe. Por favor ingrese otro código');
		document.getElementById('COD_COMUNA_0').value 	= null;
		document.getElementById('COD_COMUNA_0').focus();
	}
}