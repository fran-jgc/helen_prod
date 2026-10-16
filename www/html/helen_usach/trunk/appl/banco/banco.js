function valida_nom_banco(){
	var nom_banco = document.getElementById('NOM_BANCO_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nombre_banco.php?nom_banco="+nom_banco,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El nombre ingresado ya existe. Por favor ingrese otro nombre');
		document.getElementById('NOM_BANCO_0').value 	= null;
		document.getElementById('NOM_BANCO_0').focus();
	}
}

function valida_cod_banco(){
	var cod_banco = document.getElementById('COD_BANCO_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_cod_banco.php?cod_banco="+cod_banco,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El código ingresado ya existe. Por favor ingrese otro código');
		document.getElementById('COD_BANCO_0').value 	= null;
		document.getElementById('COD_BANCO_0').focus();
	}
}