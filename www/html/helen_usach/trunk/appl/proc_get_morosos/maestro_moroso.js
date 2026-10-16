function load(){
	document.getElementById('fecha').style.border = '1px solid #FF0000';
	document.getElementById('fecha').focus();	
}

function valida_fecha(){
	var fecha = document.getElementById('fecha').value;
	var array_fecha = fecha.split("/");
    var anio_ingresado = parseInt(array_fecha[2]);
    var anio_actual = parseInt(document.getElementById('anio_actual').value);
    
	if(fecha == ''){
		alert('Debe ingresar Fecha antes de Importar la información.');
		load();
		return false;
	}
	else if(anio_ingresado < anio_actual){
		alert('No puede ingresar un año menor al año de Fecha actual.');
		load();
		return false;
	}
	
	return true;
}