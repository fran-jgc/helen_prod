function valida_nom_pais(){
	var nom_pais = document.getElementById('NOM_PAIS_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nom_pais.php?nom_pais="+nom_pais,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El nombre ingresado ya existe. Por favor ingrese otro nombre');
		document.getElementById('NOM_PAIS_0').value = null;
		document.getElementById('NOM_PAIS_0').focus();
	}
}

function valida_cod_pais(){
	var cod_pais = document.getElementById('COD_PAIS_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_cod_pais.php?cod_pais="+cod_pais,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El código ingresado ya existe. Por favor ingrese otro código');
		document.getElementById('COD_CARRERA_0').value 	= null;
		document.getElementById('COD_CARRERA_0').focus();
	}
}