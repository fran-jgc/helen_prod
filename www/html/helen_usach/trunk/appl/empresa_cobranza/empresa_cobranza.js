function valida_nom_empresa_cobranza(){
	var nom_empresa_cobranza = document.getElementById('NOM_EMPRESA_COBRANZA_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_get_nom_empresa_cobranza.php?nom_empresa_cobranza="+nom_empresa_cobranza,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El nombre ingresado ya existe. Por favor ingrese otro nombre');
		document.getElementById('NOM_EMPRESA_COBRANZA_0').value 	= null;
		document.getElementById('NOM_EMPRESA_COBRANZA_0').focus();
	}
}

function valida_cod_empresa_cobranza(){
	var cod_empresa_cobranza = document.getElementById('COD_EMPRESA_COBRANZA_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_get_cod_empresa_cobranza.php?cod_empresa_cobranza="+cod_empresa_cobranza,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El código ingresado ya existe. Por favor ingrese otro código');
		document.getElementById('COD_EMPRESA_COBRANZA_0').value 	= null;
		document.getElementById('COD_EMPRESA_COBRANZA_0').focus();
	}
}