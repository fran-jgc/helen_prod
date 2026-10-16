function valida_nom_region(){
	var nom_region = document.getElementById('NOM_REGION_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nom_region.php?nom_region="+nom_region,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El nombre ingresado ya existe. Por favor ingrese otro nombre');
		document.getElementById('NOM_REGION_0').value = null;
		document.getElementById('NOM_REGION_0').focus();
	}
}

function valida_cod_region(){
	var cod_region = document.getElementById('COD_REGION_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_cod_region.php?cod_region="+cod_region,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El código ingresado ya existe. Por favor ingrese otro código');
		document.getElementById('COD_REGION_0').value 	= null;
		document.getElementById('COD_REGION_0').focus();
	}
}