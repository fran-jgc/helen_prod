function load_detalle_declaracion(ve_ano) {
	var record = get_num_rec_field(ve_ano.id);
	var cod_declaracion_value = document.getElementById('COD_DECLARACION_' + record).value;

	// Obtengo el elemento del select que debo cargar
	// Creo el nuevo objeto AJAX y envio al servidor el ID del select a cargar y la opcion seleccionada del select origen
	var ajax = nuevoAjax();
	cod_declaracion_value = URLEncode(cod_declaracion_value);
	ajax.open("GET", "detalle_declaracion.php?cod_declaracion="+cod_declaracion_value, false);
	ajax.send(null);		

	var resp = URLDecode(ajax.responseText);
	var lista = resp.split('|');
 	
	for (var i=0; i <= 47;i++) {
		for (var j=0; j <= 11;j++) {
			document.getElementById('MONTO_DEUDOR_MONEDA_'+j).innerHTML		= number_format(lista[i], 4,',', '.');
			document.getElementById('MONTO_DEUDOR_PESOS_'+j).innerHTML		= number_format(lista[i+1], 0,',', '.');
			document.getElementById('MONTO_CONYUGE_MONEDA_'+j).innerHTML	= number_format(lista[i+2], 4,',', '.');
			document.getElementById('MONTO_CONYUGE_PESOS_'+j).innerHTML		= number_format(lista[i+3], 0,',', '.');
			i=i+4;
		}
	}
	document.getElementById('ANO_DECLARACION_SELECCIONADA_0').innerHTML		=ve_ano.innerHTML;
	
	document.getElementById('SUM_MONTO_DEUDOR_MONEDA_0').innerHTML		=number_format(lista[48], 4,',', '.');
	document.getElementById('SUM_MONTO_DEUDOR_PESOS_0').innerHTML		=number_format(lista[49], 0,',', '.');
	document.getElementById('SUM_MONTO_CONYUGE_MONEDA_0').innerHTML		=number_format(lista[50], 4,',', '.');
	document.getElementById('SUM_MONTO_CONYUGE_PESOS_0').innerHTML		=number_format(lista[51], 0,',', '.');
}
function envio_clave_internet(){
	var vl_mail = document.getElementById('EMAIL_PARTICULAR_H_0').value;
	if (vl_mail == ''){
		TabbedPanels1.showPanel(1);
		alert('Deudor sin e-mail registrado.\n\nIngrese este dato e intente nuevamente.');
		return false;
	}
	var vl_resp = confirm('Desea enviar e-mail a: '+vl_mail+'?');
	
	if(vl_resp == '')
		return vl_resp;	
}

function validate(){
	var direccion_comercial = document.getElementById('DIRECCION_COMERCIAL_0').value;
	var pais_comercial = document.getElementById('COD_PAIS_COMERCIAL_0').value;
	
	if(direccion_comercial != '' && pais_comercial == ''){
		alert('debe ingresar Pa\u00EDs Comercial');
		return false;
	}
	var cod_pais_chile = document.getElementById('K_CHILE_H_0').value;
	var cod_pais = document.getElementById('COD_PAIS_PARTICULAR_0').value;
	if(cod_pais == cod_pais_chile){
		var cod_region = document.getElementById('COD_REGION_PARTICULAR_0').value;
		var cod_ciudad = document.getElementById('COD_CIUDAD_PARTICULAR_0').value;
		
		if(cod_region == ''){
			alert('Debe ingresar "Regi\u00f3n Particular" antes de grabar');
			document.getElementById('COD_REGION_PARTICULAR_0').focus();
			return false;
		}else{
			if(cod_ciudad == ''){
				alert('Debe ingresar "Ciudad Particular" antes de grabar.');
				document.getElementById('COD_CIUDAD_PARTICULAR_0').focus();
				return false;
			}
		}
	}else{
		var ext_region = document.getElementById('EXT_REGION_PARTICULAR_0').value.trim();
		var ext_ciudad = document.getElementById('EXT_CIUDAD_PARTICULAR_0').value.trim();
		
		if(ext_region == ''){
			alert('Debe ingresar "Regi\u00f3n Particular Extranjero" antes de grabar');
			document.getElementById('EXT_REGION_PARTICULAR_0').focus();
			return false;
		}else{
			if(ext_ciudad == ''){
				alert('Debe ingresar "Ciudad Particular Extranjero" antes de grabar.');
				document.getElementById('EXT_CIUDAD_PARTICULAR_0').focus();
				return false;
			}
		}
	}	
	
	return true;
}
function add_line_simulacion() {
	var vl_cod_deudor = document.getElementById('COD_DEUDOR_0').value;
	var args = "location:no;dialogLeft:500px;dialogTop:350px;dialogWidth:900px;dialogHeight:500px;dialogLocation:0;Toolbar:no;";
	var returnVal = window.showModalDialog("UCM/dlg_simulacion.php?cod_simulacion=-1&cod_deudor="+vl_cod_deudor, "_blank", args);
	if (returnVal == null){
 		return false;
 	}
	else {
		document.getElementById('wi_hidden').value = returnVal;
		document.input.submit();
   		return true;
	}
}

function change_semestre(ve_semestre) {
	var vl_record = get_num_rec_field(ve_semestre.id);
	var vl_semestre = ve_semestre.value;
	
	if (vl_semestre == 0 || vl_semestre > 2){
		alert('El tipo Semestre no aceptado. Opcion: 1 ó 2');
		document.getElementById('SEMESTRE_' + vl_record).value = "";
		document.getElementById('SEMESTRE_' + vl_record).focus();
		return false;
	}else{
		return true;
	}
}

function aux_load_drop_down(ve_tabla, ve_campo, ve_nom_tipo_direccion){
	var cod_pais_chile = document.getElementById('K_CHILE_H_0').value;
	var cod_pais = ve_campo.value;
	
	if(ve_nom_tipo_direccion == 'PARTICULAR'){
		if(cod_pais == cod_pais_chile){
			load_drop_down(ve_tabla, ve_campo.id, 'COD_REGION_'+ve_nom_tipo_direccion);
			//limpiar los input de extranjero
			document.getElementById('EXT_REGION_PARTICULAR_0').value = "";
			document.getElementById('EXT_CIUDAD_PARTICULAR_0').value = "";
			document.getElementById('EXT_COMUNA_PARTICULAR_0').value = "";
			
			//particular chile 
			document.getElementById('td_cod_region_p').style.display = '';
			document.getElementById('td_cod_ciudad_p').style.display = '';
			document.getElementById('td_cod_comuna_p').style.display = '';
			
			//particular extranjero
			document.getElementById('td_ext_region_p').style.display = 'none';
			document.getElementById('td_ext_ciudad_p').style.display = 'none';
			document.getElementById('td_ext_comuna_p').style.display = 'none';
		}else{
			//drop down region, ciudad y comuna null
			document.getElementById('COD_REGION_PARTICULAR_0').options.length = 1;
			document.getElementById('COD_CIUDAD_PARTICULAR_0').options.length = 1;
			document.getElementById('COD_COMUNA_PARTICULAR_0').options.length = 1;
			
			//particular chile 
			document.getElementById('td_cod_region_p').style.display = 'none';
			document.getElementById('td_cod_ciudad_p').style.display = 'none';
			document.getElementById('td_cod_comuna_p').style.display = 'none';
			
			//particular extranjero
			document.getElementById('td_ext_region_p').style.display = '';
			document.getElementById('td_ext_ciudad_p').style.display = '';
			document.getElementById('td_ext_comuna_p').style.display = '';
		}
	}else if(ve_nom_tipo_direccion == 'COMERCIAL'){
		if(cod_pais == cod_pais_chile){
			load_drop_down(ve_tabla, ve_campo.id, 'COD_REGION_'+ve_nom_tipo_direccion);
			//limpiar los input de extranjero
			document.getElementById('EXT_REGION_COMERCIAL_0').value = "";
			document.getElementById('EXT_CIUDAD_COMERCIAL_0').value = "";
			document.getElementById('EXT_COMUNA_COMERCIAL_0').value = "";
			
			//comercial chile 
			document.getElementById('td_cod_region_c').style.display = '';
			document.getElementById('td_cod_ciudad_c').style.display = '';
			document.getElementById('td_cod_comuna_c').style.display = '';
			
			//comercial extranjero
			document.getElementById('td_ext_region_c').style.display = 'none';
			document.getElementById('td_ext_ciudad_c').style.display = 'none';
			document.getElementById('td_ext_comuna_c').style.display = 'none';
		}else{
			//region, ciudad y comuna null
			document.getElementById('COD_REGION_COMERCIAL_0').options.length = 1;
			document.getElementById('COD_CIUDAD_COMERCIAL_0').options.length = 1;
			document.getElementById('COD_COMUNA_COMERCIAL_0').options.length = 1;
			
			//comercial chile 
			document.getElementById('td_cod_region_c').style.display = 'none';
			document.getElementById('td_cod_ciudad_c').style.display = 'none';
			document.getElementById('td_cod_comuna_c').style.display = 'none';
			
			//comercial extranjero
			document.getElementById('td_ext_region_c').style.display = '';
			document.getElementById('td_ext_ciudad_c').style.display = '';
			document.getElementById('td_ext_comuna_c').style.display = '';
		}
	}
}

function valida_tipo_bitacora(ve_cod_tipo_bitacora){
	var record = get_num_rec_field(ve_cod_tipo_bitacora.id);
	var permite_manual = ve_cod_tipo_bitacora.options[ve_cod_tipo_bitacora.selectedIndex].dataset.dropdown;
	if (permite_manual == 'N'){
		alert('El tipo evento seleccionado no puede ser ingresado de forma manual');
		document.getElementById('COD_TIPO_BITACORA_' + record).value=1; //OTRO	
	}
}