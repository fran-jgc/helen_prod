function validate() {

	var rut = document.getElementById('RUT_0').value;
	var email = document.getElementById('EMAIL_0').value;
	var ajax=nuevoAjax();
	
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
	else{
		ajax.open("GET", "ajax_recupera_contrasena.php?rut="+rut+"&email="+email, false);
		ajax.send(null);
		
		var vl_resp = URLDecode(ajax.responseText);
		
		if (vl_resp=='RUT_INVALIDO') {
			document.getElementById('RUT_0').value = null;
			document.getElementById('RUT_0').focus();
			alert('El rut ingresado no está registrado en el Fondo de Crédito.');
			return false;
		}
		else if (vl_resp=='EMAIL_INVALIDO') {
			document.getElementById('EMAIL_0').value = null;
			document.getElementById('EMAIL_0').focus();
			alert('El E-mail ingresado no está registrado en el Fondo de Crédito.');
			return false;
		}
		else if (vl_resp=='ERROR_ENVIA_CORREO') {
			alert('Error, no se ha podido enviar el E-mail.');
			return false;
		}
		else if (vl_resp=='ENVIADO_CORRECTAMENTE') {
			alert('E-mail enviado correctamente.');
			return true;
		}
	}
}