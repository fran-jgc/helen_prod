function validate() {
	var nom_perfil = document.getElementById('NOM_PERFIL_0').value;
	var largo = nom_perfil.length;
	for(var i=0; i < largo; i++) {
		if (nom_perfil[i] == ' ') {
			alert('No se puede ingresar espacios en blanco');
			return false;
		}
	}
}
function f_get_nom_perfil(){
	var nom_perfil = document.getElementById('NOM_PERFIL_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nom_perfil.php?nom_perfil="+nom_perfil,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El Nombre de Perfil ingresado ya existe. Por favor ingrese otro nombre');
		document.getElementById('NOM_PERFIL_0').value 	= null;
		document.getElementById('NOM_PERFIL_0').focus();
	}	
}

function valida_checkbox_lectura() {
	// La row 3 es PARAMETROS y define el acceso a las parametricas
	var lectura = document.getElementById('LECTURA_3').checked;	
	
	if (lectura == false) {
		for (var i=4; i<=30; i++) {
			document.getElementById('LECTURA_' + i).disabled = true;
			document.getElementById('LECTURA_' + i).checked = false;
		}
	}else {	
		for (var i=4; i<=30; i++) {
			document.getElementById('LECTURA_' + i).disabled = false;
			document.getElementById('LECTURA_' + i).checked = true;
			
		}
	}	
}

function valida_checkbox_escritura() {
	// La row 3 es PARAMETROS y define el acceso a las parametricas
	var escritura = document.getElementById('ESCRITURA_3').checked;
	if (escritura == false) {
		for (var i=4; i<=30; i++) {
			document.getElementById('ESCRITURA_' + i).disabled = true;
			document.getElementById('ESCRITURA_' + i).checked = false;		
		}
	}else {	
		for (var i=4; i<=30; i++) {
			document.getElementById('ESCRITURA_' + i).disabled = false
			document.getElementById('ESCRITURA_' + i).checked = true;
		}
	}
}

// Se llama en html en <body onload="checkbox()" {WI_ONLOAD}>
function checkbox() {
	// La row 3 es PARAMETROS y define el acceso a las parametricas
	var lectura   = document.getElementById('LECTURA_3').checked;
	var escritura = document.getElementById('ESCRITURA_3').checked;
	
	if (lectura == false) {
		for (var i=4; i<=30; i++) {
			document.getElementById('LECTURA_' + i).disabled = true;
		}
	}
	
	if (escritura == false) {
		for (var i=4; i<=30; i++) {
			document.getElementById('ESCRITURA_' + i).disabled = true;
		}
	}	
}