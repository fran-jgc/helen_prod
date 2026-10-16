function load(){
	document.getElementById('ano').style.border = '1px solid #FF0000';
	document.getElementById('ano').focus();	
}

function valida_ano(){
	var ano = document.getElementById('ano').value;
	if(ano == ''){
		alert('Debe ingresar Año antes de Importar la información.');
		load();
		return false;
	}
	else{
		var ajax = nuevoAjax();	
	    ajax.open("GET", "existen_datos.php?ano="+ano, false);    
	    ajax.send(null);    
		var resp = ajax.responseText;				
		if(resp[0] == 'N'){
			alert('El Año solicitado no registra información.');
			document.getElementById('ano').value = '';
			load();
			return false;
		}
	}	
	return true;
}