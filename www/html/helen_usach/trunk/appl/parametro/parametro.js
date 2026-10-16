function valida_acceso(ve_cod_item_menu) {
   	ajax = nuevoAjax();
	ajax.open("GET", "ajax_tiene_acceso.php?cod_item_menu=" + ve_cod_item_menu, false);
    ajax.send(null);
     
    var resp = URLDecode(ajax.responseText);   
	if (resp=='N') {
		alert('Ud. no esta autorizado para ingresar a esta opción.');
		return false;
	}
	return true;
}

function validate(){
	var mail = document.getElementById('EMAIL_0').value;
	var filter=/^[A-Za-z][A-Za-z0-9_]*@[A-Za-z0-9_]+\.[A-Za-z0-9_.]+[A-za-z]$/;
	if (mail.length == 0 ) 
		return true;
	if (filter.test(mail))
		return true;
	else{
		alert("Ingrese una dirección de correo válida");
		document.getElementById('EMAIL_0').focus();
		return false;
	}
	return true;	
}
function borrar_todos() {
	var aTR = get_TR('LOCK_TABLE');
	for (var i=0; i < aTR.length; i++) {
		var vl_record = get_num_rec_field(aTR[i].id);
		del_line('LOCK_TABLE_' + vl_record, 'parametro');
	}
}

function comp_descuadrado(){
	var ajax = nuevoAjax();
	var php = "ajax_com_descuadrado.php";
	ajax.open("GET", php, false);
	ajax.send(null);	
	var resp = ajax.responseText;
	
	if (resp == '0')
		alert('¡Sin comprobantes descuadrados!');
	else
		alert(resp);
		
	return true;
}