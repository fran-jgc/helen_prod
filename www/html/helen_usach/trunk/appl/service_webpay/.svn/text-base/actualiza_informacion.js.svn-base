function validate_direccion(){
	//valida que ingrese estado ES_EGRESADO
	var total = document.getElementsByName('SELECCION_OPCION').length;
	var valor;
	var sum_ctrl = 0;
	for (var i=0;i<total;i++){
		valor = document.getElementsByName('SELECCION_OPCION').item(i).checked;
		 
		if (!valor){
			sum_ctrl = sum_ctrl + 1;
		}
	}
	
	if(direccion_p == ''){ 
		alert('Debe ingresar "Dirección Particular" antes de grabar.');
		document.getElementById('DIRECCION_PARTICULAR_0').focus();
		return false;
	}
	
	var direccion_p = document.getElementById('DIRECCION_PARTICULAR_0').value;
	
	if(direccion_p == ''){ 
		alert('Debe ingresar "Dirección Particular" antes de grabar.');
		document.getElementById('DIRECCION_PARTICULAR_0').focus();
		return false;
	}
	
	var pais_p = document.getElementById('COD_PAIS_PARTICULAR_0').value;
	
	if(pais_p == ''){ 
		alert('Debe ingresar "Pais Particular" antes de grabar.');
		document.getElementById('COD_PAIS_PARTICULAR_0').focus();
		return false;
	}
	
	var region_p = document.getElementById('COD_REGION_PARTICULAR_0').value;
	
	if(region_p == ''){ 
		alert('Debe ingresar "Región Particular" antes de grabar.');
		document.getElementById('COD_REGION_PARTICULAR_0').focus();
		return false;
	}
	
	var ciudad_p = document.getElementById('COD_CIUDAD_PARTICULAR_0').value;
	
	if(ciudad_p == ''){
		alert('Debe ingresar "Ciudad Particular" antes de grabar.');
		document.getElementById('COD_CIUDAD_PARTICULAR_0').focus();
		return false;
	}
	
	var email_p = document.getElementById('EMAIL_PARTICULAR_0').value;
	
	if(email_p == ''){ 
		alert('Debe ingresar "Email" antes de grabar.');
		document.getElementById('EMAIL_PARTICULAR_0').focus();
		return false;
	}
	
	var celular_p = document.getElementById('CELULAR_PARTICULAR_0').value;
	
	if(celular_p == ''){ 
		alert('Debe ingresar "N° Celular " antes de grabar.');
		document.getElementById('CELULAR_PARTICULAR_0').focus();
			return false;
	}

	/*es obligatorio ingresar comuna*/
	var cod_comuna_particular = document.getElementById('COD_COMUNA_PARTICULAR_0').value;
	if(cod_comuna_particular == ''){ 
		alert('Debe ingresar "Comuna Particular" antes de grabar.');
		document.getElementById('COD_COMUNA_PARTICULAR_0').focus();
		return false;
	}
	
	var telefono_p = document.getElementById('TELEFONO_PARTICULAR_0').value;
	
	//COMERCIAL
	
	var dir_comercial = document.getElementById('DIRECCION_COMERCIAL_0').value;
	if(dir_comercial != ''){ 
		var cod_pais = document.getElementById('COD_PAIS_COMERCIAL_0').value;
	
		if(cod_pais == ''){
			alert('Debe ingresar "Pais comercial" antes de grabar.');
			document.getElementById('COD_PAIS_COMERCIAL_0').focus();
			return false;
		}
		
		var region_p = document.getElementById('COD_REGION_COMERCIAL_0').value;
	
		if(region_p == ''){ 
			alert('Debe ingresar "Región Comercial" antes de grabar.');
			document.getElementById('COD_REGION_COMERCIAL_0').focus();
			return false;
		}	
		
		var ciudad_p = document.getElementById('COD_CIUDAD_COMERCIAL_0').value;
	
		if(ciudad_p == ''){
			alert('Debe ingresar "Ciudad Comercial" antes de grabar.');
			document.getElementById('COD_CIUDAD_COMERCIAL_0').focus();
			return false;
		}
		
		var cod_comuna_particular = document.getElementById('COD_COMUNA_COMERCIAL_0').value;
		if(cod_comuna_particular == ''){ 
			alert('Debe ingresar "Comuna Comercial" antes de grabar.');
			document.getElementById('COD_COMUNA_COMERCIAL_0').focus();
			return false;
		}
	
	}
	
	return true;
}

function load_egresado(ve_cod_deudor) {
	var ajax=nuevoAjax();
	ajax.open("GET", "ajax_option_egresado.php?cod_deudor="+ve_cod_deudor, false);
	ajax.send(null);
	
	var vl_resp = URLDecode(ajax.responseText);		
	var result = eval("(" + vl_resp + ")");
	var vl_es_egresado = result[0]['ES_EGRESADO'];
	
	if(vl_es_egresado == 'S'){
		document.getElementById('EGRESADO_SI_0').checked = true;
		document.getElementById('EGRESADO_NO_0').checked = false;
	}else if(vl_es_egresado == 'N'){
		document.getElementById('EGRESADO_SI_0').checked = false;
		document.getElementById('EGRESADO_NO_0').checked = true;
	}else if(vl_es_egresado == 'SIN_DATO'){
		document.getElementById('div_egresado').style.border = "double blue";
		document.getElementById('EGRESADO_SI_0').focus();
		document.getElementById('EGRESADO_SI_0').checked = false;
		document.getElementById('EGRESADO_NO_0').checked = false;
	}	
}

function colocaAyuda(ve_campo)
{
	cAyuda=document.getElementById("mensajesAyuda");
	cNombre=document.getElementById("ayudaTitulo");
	cTex=document.getElementById("ayudaTexto");
	
	cNombre.innerHTML=ve_campo;
	cTex.innerHTML = "Ingresa Si ó No.<br>Es OBLIGATORIO";
	cAyuda.style.display="block";
	
	cAyuda.style.top="50px";
	cAyuda.style.left="470px";
}

/*////////////////////////////////*/
function clear_options(ve_id) {
	// borra todo las options del actual dropdown
	var vl_destino = document.getElementById(ve_id);
	while (vl_destino.firstChild)
	  vl_destino.removeChild(vl_destino.firstChild);
} 

function load_drop_down_web(ve_campo) {
	var vl_nom_campo = get_nom_field(ve_campo.id);
	var vl_cod_campo = get_nom_field(vl_nom_campo);		// COD_PAIS_PARTICULAR lo deja en COD_PAIS
	var vl_tipo = get_num_rec_field(vl_nom_campo); 		// COD_PAIS_PARTICULAR lo deja en PARTICULAR
	

	var ajax=nuevoAjax();
	ajax.open("GET", "ajax_drop_down_direccion.php?cod_campo="+vl_cod_campo+"&value="+ve_campo.value, false);
	ajax.send(null);
	
	var vl_resp = ajax.responseText;		
	var result = eval("(" + vl_resp + ")");
	
	// dropdown destino
	var vl_id_destino = '';
	var vl_cod = '';
	var vl_nom = ''
	switch (vl_cod_campo) {
		case 'COD_PAIS': 
			vl_id_destino = 'COD_REGION_' + vl_tipo + '_0';
			clear_options(vl_id_destino);
			clear_options('COD_CIUDAD_' + vl_tipo + '_0');
			clear_options('COD_COMUNA_' + vl_tipo + '_0');
			vl_cod = 'COD_REGION';
			vl_nom = 'NOM_REGION';
			
			var vl_destino = document.getElementById(vl_id_destino);
			// agrega vacio al comienzo
			var vl_option = document.createElement("option");
			vl_option.value = '';
			vl_option.innerHTML = ' '; 
			vl_destino.appendChild(vl_option); 
			break;
		case 'COD_REGION': 
			vl_id_destino = 'COD_CIUDAD_' + vl_tipo + '_0';
			clear_options(vl_id_destino);
			clear_options('COD_COMUNA_' + vl_tipo + '_0');
			vl_cod = 'COD_CIUDAD';
			vl_nom = 'NOM_CIUDAD';
			
			var vl_destino = document.getElementById(vl_id_destino);
			// agrega vacio al comienzo
			var vl_option = document.createElement("option");
			vl_option.value = '';
			vl_option.innerHTML = ' '; 
			vl_destino.appendChild(vl_option); 
			break;
		case 'COD_CIUDAD': 
			vl_id_destino = 'COD_COMUNA_' + vl_tipo + '_0';
			clear_options(vl_id_destino);
			vl_cod = 'COD_COMUNA';
			vl_nom = 'NOM_COMUNA';
			
			var vl_destino = document.getElementById(vl_id_destino);
			// agrega vacio al comienzo
			var vl_option = document.createElement("option");
			vl_option.value = '';
			vl_option.innerHTML = ' '; 
			vl_destino.appendChild(vl_option); 
			break;
	}
	
	// arma el dropdown a partir del resultado
	for (var i=0; i < result.length; i++) {
		vl_option = document.createElement("option");
		vl_option.value = result[i][vl_cod];
		vl_option.innerHTML = URLDecode(result[i][vl_nom]); 
		vl_destino.appendChild(vl_option); 
	}
}