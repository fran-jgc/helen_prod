function actualiza_utm(){
	var fecha_vencto = document.getElementById('FECHA_VENCTO_0');
	if(fecha_vencto.value.length == 10){
		var ajax = nuevoAjax();
		ajax.open("GET", "ajax_actualiza_utm.php?fecha_vencto="+fecha_vencto.value, false);
		ajax.send(null);
	    var resp = URLDecode(ajax.responseText);
	    if(resp == 'no_utm'){
	    	alert('No existe Valor UTM para la fecha tres meses despues de la Ingresada');
	    	document.getElementById('VALOR_UTM_MENSUAL_0').innerHTML = '';
    		document.getElementById('FECHA_VENCTO_0').value = '';
    		document.getElementById('FECHA_VENCTO_0').focus;
	    }
	    else{
	    	document.getElementById('VALOR_UTM_MENSUAL_0').innerHTML = resp;
	    }
    }
    else{
    	if(fecha_vencto.value.length < 10){
    		alert('Ingrese una Fecha Válida');
    		document.getElementById('VALOR_UTM_MENSUAL_0').innerHTML = '';
    		document.getElementById('FECHA_VENCTO_0').value = '';
    		document.getElementById('FECHA_VENCTO_0').focus;
    	}
    }   
}