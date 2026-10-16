function valida_cod_tipo_descuento(){
	var cod_tipo_descuento = document.getElementById('COD_TIPO_DESCUENTO_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_get_cod_tipo_descuento.php?cod_tipo_descuento="+cod_tipo_descuento,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El código ingresado ya existe. Por favor ingrese otro código');
		document.getElementById('COD_TIPO_DESCUENTO_0').value 	= null;
		document.getElementById('COD_TIPO_DESCUENTO_0').focus();
	}
}