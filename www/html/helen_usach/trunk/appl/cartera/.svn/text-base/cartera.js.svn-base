function valida_nom_cartera(){
	var nom_cartera = document.getElementById('NOM_CARTERA_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nom_cartera.php?nom_cartera="+nom_cartera,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El nombre ingresado ya existe. Por favor ingrese otro nombre');
		document.getElementById('NOM_CARTERA_0').value 	= null;
		document.getElementById('NOM_CARTERA_0').focus();
	}
}