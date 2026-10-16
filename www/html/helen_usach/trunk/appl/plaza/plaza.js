function valida_nom_plaza(){
	var nom_plaza = document.getElementById('NOM_PLAZA_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nom_plaza.php?nom_plaza="+nom_plaza,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El nombre ingresado ya existe. Por favor ingrese otro nombre');
		document.getElementById('NOM_PLAZA_0').value 	= null;
		document.getElementById('NOM_PLAZA_0').focus();
	}
}

function valida_cod_plaza(){
	var cod_plaza = document.getElementById('COD_PLAZA_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_cod_plaza.php?cod_plaza="+cod_plaza,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El código ingresado ya existe. Por favor ingrese otro código');
		document.getElementById('COD_PLAZA_0').value 	= null;
		document.getElementById('COD_PLAZA_0').focus();
	}
}