function validate(){
	var fecha = new Date();
	var obs = document.input.OBS_0.value;
	if (obs == ''){
		alert('Debe ingresar "Observaciones" antes de grabar');
		return false;
	}
	var fecha_ini = document.getElementById('FECHA_INICIO_COBRO_0').value;
	var fecha_ini2 = fecha_ini.slice(fecha_ini.length-4);
	var ano = fecha.getFullYear() + 1;
	//if (fecha_ini2 < ano){
	//	alert('"Fecha Inicio Cobro" no puede ser menor a 01/01/' + ano);
	//	return false;
	//}
		return true;
}
function set_datos_deudor() {
	set_value('DIG_VERIF_0','','');
	set_value('NOMBRE_0', '', '');
	set_value('COD_DEUDA_DD_0','','');
	set_value('COD_DEUDA_0','','');
	document.getElementById('COD_TIPO_DEUDA_0').options.length = 0;//borra todos los options
}

function f_valida_fecha(ve_fecha, ve_ano_actual) {
	var fecha_prorroga	= ve_fecha.value.split('/');
	fecha_prorroga = fecha_prorroga[2];
		
	var fecha_maxima_prorroga = ve_ano_actual + 3 

	//if(fecha_prorroga < ve_ano_actual){
	//	alert('La Fecha Inicio Cobro no puede ser Menor a la Fecha Actual');
	//	ve_fecha.value = '';
	//	ve_fecha.focus();
	//	return false;
	//}else 
	if(fecha_prorroga > fecha_maxima_prorroga){
		alert('La Fecha Inicio Cobro no puede ser Mayor a 3 años de la Fecha Actual');
		ve_fecha.value = '';
		ve_fecha.focus();
		return false;
	}
	return true;
}

function f_get_name_user() 
{
	set_datos_deudor();
	var rut = document.getElementById('RUT_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET", "ajax_obtiene_nombre.php?rut="+rut, false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	var lista = resp.split('|');
	if ( (lista[0] != "NO") && (lista[0] != "NO_EXIGIBLE") && (lista[0] != "PENDIENTE") && (lista[0] != "VACIO") && (lista[0] != "CARTERA_DIFERENTE") ) {
		var result = eval("(" + resp + ")");
		document.getElementById('DIG_VERIF_0').innerHTML = result[0]['DIG_VERIF'];
		document.getElementById('NOMBRE_0').innerHTML = result[0]['NOMBRE'];
		document.getElementById('COD_DEUDA_0').innerHTML = result[0]['COD_DEUDA'];
		document.getElementById('COD_DEUDA_H_0').value = result[0]['COD_DEUDA'];
		
		var vl_cod_tipo_deuda = document.getElementById('COD_TIPO_DEUDA_0'); 
		for (var i=0; i < result.length; i++) {
			var nuevaOpcion=document.createElement("option"); 
			nuevaOpcion.value = result[i]['COD_DEUDA']; 
			nuevaOpcion.innerHTML = result[i]['COD_TIPO_DEUDA'];
			vl_cod_tipo_deuda.appendChild(nuevaOpcion); 
		}
		
		
	}
	else{
		if (lista[0] == "NO") {
			alert("El Deudor No Existe");
		}
		else if (lista[0] == "NO_EXIGIBLE"){
			alert("El deudor no posee deudas (distintas de CSR) en estado EXIGIBLE O NO EXIGIBLE");
		}
		else if (lista[0] == "PENDIENTE"){
			alert("El deudor tiene una solicitud de Prórroga pendiente");
		}
		else if (lista[0] == "CARTERA_DIFERENTE"){
			alert("El Deudor tiene una deuda con otra Cartera distinta a la por defecto.");
		}
		else if (lista[0] == "VACIO"){
			set_datos_deudor();
		}
		document.getElementById('RUT_0').value 	= null;
		document.getElementById('RUT_0').focus();
		set_datos_deudor();
	}
}
function f_usuario_confirma(ve_nom_usuario,ve_fecha){
	document.getElementById('NOM_USUARIO_CONFIRMA_0').innerHTML 	= '';
	document.getElementById('NOM_USUARIO_CONFIRMA_0').value 		= '';	
	document.getElementById('FECHA_CONFIRMA_0').innerHTML 	= '';
	document.getElementById('FECHA_CONFIRMA_0').value 		= '';
	
	var est_prorroga = document.getElementById('COD_ESTADO_PRORROGA_0').value;
	
	if(est_prorroga == 2){
		document.getElementById('NOM_USUARIO_CONFIRMA_0').innerHTML     = ve_nom_usuario;
		document.getElementById('NOM_USUARIO_CONFIRMA_0').value 		= ve_nom_usuario;
	
		document.getElementById('FECHA_CONFIRMA_0').innerHTML 	= ve_fecha;
		document.getElementById('FECHA_CONFIRMA_0').value 		= ve_fecha;
	} 
}
function change_tipo_deuda(ve_cod_tipo_deuda) {
	document.getElementById('COD_DEUDA_0').innerHTML = ve_cod_tipo_deuda.value;
	document.getElementById('COD_DEUDA_H_0').value = ve_cod_tipo_deuda.value;
}