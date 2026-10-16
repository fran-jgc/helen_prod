function validate() {
	var rut = document.getElementById('RUT_0').value;
	var email = document.getElementById('EMAIL_0').value;
	var fecha_nacimiento = document.getElementById('FECHA_NACIMIENTO_0').value;
	var ajax = nuevoAjax();

	if (rut == '') {
		alert('Debe ingresar un Rut.');
		document.getElementById('RUT_0').focus();
		return false;
	}
	if (email == '') {
		alert('Debe ingresar un E-mail.');
		document.getElementById('EMAIL_0').focus();
		return false;
	}

	try {
		ajax.open("GET", "ajax_recupera_contrasena.php?rut=" + encodeURIComponent(rut) + "&email=" + encodeURIComponent(email) + "&fecha_nacimiento=" + encodeURIComponent(fecha_nacimiento), false);
		ajax.send(null);
	}
	catch (e) {
		alert('Error, no se ha podido procesar la solicitud.');
		return false;
	}

	var vl_resp = URLDecode(ajax.responseText);
	vl_resp = (vl_resp || '').replace(/^\s+|\s+$/g, '');

	if (vl_resp == 'RUT_INVALIDO') {
		document.getElementById('RUT_0').value = null;
		document.getElementById('RUT_0').focus();
		alert('El rut ingresado no esta registrado en el Fondo de Credito.');
		return false;
	}
	else if (vl_resp == 'EMAIL_INVALIDO') {
		document.getElementById('EMAIL_0').value = null;
		document.getElementById('EMAIL_0').focus();
		alert('El E-mail ingresado no esta registrado en el Fondo de Credito.');
		return false;
	}
	else if (vl_resp == 'FECHA_INVALIDA') {
		document.getElementById('FECHA_NACIMIENTO_0').value = null;
		document.getElementById('FECHA_NACIMIENTO_0').focus();
		alert('La fecha de nacimiento ingresada no coincide con la registrada en el Fondo de Credito.');
		return false;
	}
	else if (vl_resp == 'SIN_FECHA_NACIMIENTO') {
		alert('No existe una fecha de nacimiento registrada para este RUT. Contacte a la administracion del Fondo de Credito.');
		return false;
	}
	else if (vl_resp == 'ERROR_ENVIA_CORREO') {
		alert('Error, no se ha podido enviar el E-mail.');
		return false;
	}
	else if (vl_resp == 'ENVIADO_CORRECTAMENTE') {
		alert('E-mail enviado correctamente.');
		window.location = 'inicio_sesion.php';
		return false;
	}

	if (vl_resp == '') {
		alert('Error, el servidor no entrego respuesta al recuperar la contrasena.');
		return false;
	}

	alert('Error, no se obtuvo una respuesta valida del servidor.');
	return false;
}
