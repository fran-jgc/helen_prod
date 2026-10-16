function valida_login() {
	var login		= document.getElementById('LOGIN_0').value;
	var largo 		= login.length;
	for(var i=0; i < largo; i++) {
		if (login[i] == ' ') {
			alert('No se puede ingresar espacios en blanco en el Login');
			document.getElementById('LOGIN_0').value 	= null;
			document.getElementById('LOGIN_0').focus();
			return false;
		}
	}
}
function valida_password() {
	var password 	= document.getElementById('PASSWORD_0').value;
	var largo 		= password.length;
	for(var i=0; i < largo; i++) {
		if (password[i] == ' ') {
			alert('No se puede ingresar espacios en blanco en la Password');
			document.getElementById('PASSWORD_0').value 	= null;
			document.getElementById('PASSWORD_0').focus();
			return false;
		}
	}
}
function f_get_nom_usuario(){
	var nom_usuario = document.getElementById('NOM_USUARIO_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nom_usuario.php?nom_usuario="+nom_usuario,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El Nombre de Usuario ingresado ya existe. Por favor ingrese otro nombre');
		document.getElementById('NOM_USUARIO_0').value 	= null;
		document.getElementById('NOM_USUARIO_0').focus();
	}
}
function f_get_nom_login(){
	var nom_login = document.getElementById('LOGIN_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nom_login.php?nom_login="+nom_login,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El Nombre de Login ingresado ya existe. Por favor ingrese otro nombre');
		document.getElementById('LOGIN_0').value 	= null;
		document.getElementById('LOGIN_0').focus();
	}else{
		valida_login();
	}
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