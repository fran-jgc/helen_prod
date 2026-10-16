function valida_nom_origen_carga_banco(){
	var nom_origen_carga_banco = document.getElementById('NOM_ORIGEN_CARGA_BANCO_0').value;
	var cod_cuenta_corriente   = document.getElementById('COD_CUENTA_CORRIENTE_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nom_origen_carga_banco.php?nom_origen_carga_banco="+nom_origen_carga_banco+"&cod_cuenta_corriente="+cod_cuenta_corriente,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('Ya existe un nombre de ORIGEN CARGA BANCO asociado a esa CUENTA CORRIENTE');
		document.getElementById('NOM_ORIGEN_CARGA_BANCO_0').value 	= null;
		document.getElementById('NOM_ORIGEN_CARGA_BANCO_0').focus();
	}
}
function valida_cod_cuenta_corriente(){	
	var nom_origen_carga_banco = document.getElementById('NOM_ORIGEN_CARGA_BANCO_0').value;
	var cod_cuenta_corriente   = document.getElementById('COD_CUENTA_CORRIENTE_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nom_origen_carga_banco.php?nom_origen_carga_banco="+nom_origen_carga_banco+"&cod_cuenta_corriente="+cod_cuenta_corriente,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('Ya existe una CUENTA CORRIENTE asociada a ese nombre de ORIGEN CARGA BANCO');
		document.getElementById('COD_CUENTA_CORRIENTE_0').value 	= null;
		document.getElementById('COD_CUENTA_CORRIENTE_0').focus();
	}
}