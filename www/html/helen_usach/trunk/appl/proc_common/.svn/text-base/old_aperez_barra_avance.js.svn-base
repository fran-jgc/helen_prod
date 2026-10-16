////////////////////////////////////////////////////////////////////////////////////////
//////////////////////// BARRA DE AVANCE //////////////////////////
function barra_set_progress(start) {	
	document.getElementById('barra_avance').style.width = start+"%";
	document.getElementById('porcentaje_barra').innerHTML = number_format(start, 1, ',', '.') + "%";
}

function barra_fill_progress(ve_w_proceso) {
	var ajax = nuevoAjax();
	ajax.open("GET", "../proc_common/ajax_barra_lee_avance.php?w_proceso=" + ve_w_proceso, false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	var aDato = eval("(" + resp + ")");
	
	var vl_avance = aDato['ACTUAL_AVANCE']; 
	var vl_duracion = aDato['ACTUAL_DURACION']; 
	var vl_cant_error = aDato['ACTUAL_CANT_ERROR']; 
	var vl_cant_warning = aDato['ACTUAL_CANT_WARNING']; 
	var vl_arch = aDato['ARCHIVO_RESULTADO']; 
	var vl_actual_registro = aDato['ACTUAL_ACTUAL_REGISTRO']; 

	barra_set_progress(vl_avance);
	
	document.getElementById('ACTUAL_CANT_ERROR_0').innerHTML = vl_cant_error;
	document.getElementById('ACTUAL_CANT_WARNING_0').innerHTML = vl_cant_warning;
	document.getElementById('ACTUAL_DURACION_0').innerHTML = vl_duracion;
	document.getElementById('ACTUAL_ACTUAL_REGISTRO_0').innerHTML = vl_actual_registro;
	
	if (vl_avance < 100)
		setTimeout("barra_fill_progress('"+ve_w_proceso+"')",1000);	// 1 segundo
	else {
		var b_init = document.getElementById('b_init');
		var b_cancel = document.getElementById('b_cancel');
		
		if (b_init.hasAttribute("disabled")) {
			b_init.removeAttribute('disabled');
			b_cancel.setAttribute('disabled', "");
			var msg = 'Proceso teminado.';
			if (vl_cant_error + vl_cant_warning > 0)
				msg = msg + '\nRevise los errores y alertas.';
			alert(msg);
			
			if (vl_arch != '' && vl_arch != null ) {
				var nom_carta = document.getElementById('COD_CARTA_0').options[document.getElementById('COD_CARTA_0').selectedIndex].innerHTML;
				popup_window = window.open('bajar_archivo_resultado.php?arch='+vl_arch+'&nom_carta='+nom_carta)
			}
		}
	}
}

function barra_start(ve_w_proceso) {
	barra_set_progress(0);
	setTimeout("barra_fill_progress('"+ve_w_proceso+"')",2000);	// 2 segundos
}

function confirm_cancel() {
	var agree = confirm("¿ Esta seguro que desea cancelar el proceso ?");
	if (agree) return true ;
	else return false ;
}
