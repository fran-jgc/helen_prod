function valida_calculo(){

	var ajax = nuevoAjax();
	ajax.open("GET", "USACH/ajax_valida_calculo.php", false);
	ajax.send(null);
	var resp = ajax.responseText;

	if(resp == 1){
		alert('Existen Declaraciones en Estado Solicitada. No se puede correr el Proceso.');
		return false;
	}
	return true;	
}