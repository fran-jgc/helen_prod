function valida_cod_cta_contable(){
	var cod_cta_contable = document.getElementById('COD_CUENTA_CONTABLE_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_cod_cta_contable.php?cod_cta_contable="+cod_cta_contable,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El código ingresado ya existe. Por favor ingrese otro código');
		document.getElementById('COD_CUENTA_CONTABLE_0').value 	= null;
		document.getElementById('COD_CUENTA_CONTABLE_0').focus();
	}
}

function valida_nom_cta_contable(){
	var nom_cta_contable = document.getElementById('NOM_CUENTA_CONTABLE_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nom_cta_contable.php?nom_cta_contable="+nom_cta_contable,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El nombre ingresado ya existe. Por favor ingrese otro nombre');
		document.getElementById('NOM_CUENTA_CONTABLE_0').value 	= null;
		document.getElementById('NOM_CUENTA_CONTABLE_0').focus();
	}
}