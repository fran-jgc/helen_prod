function onlyNumbers_dj(ve_obj, ve_evt, ve_max_num_dec, ve_solo_positivo){
	var keynum;
	
	if(window.event) // IE
		keynum = ve_evt.keyCode;
	else if(ve_evt.which) // Netscape/Firefox/Opera
		keynum = ve_evt.which;
	else // Para FF los carcaters tab, end, etc
		keynum = ve_evt.keyCode;
		
	var vl_reg_coma = /\,/;
		
	if (keynum==8	// backspace
		|| keynum==9	// tab
		|| keynum==35	// end
		|| keynum==36	// home
		|| keynum==37	// left
		|| keynum==39	// right
		)
		return true;
	else if (keynum==46 && ve_evt.charCode==0)	// sumpirmir
		return true;
	if ((keynum==99	// ctrl C
		|| keynum==118	// ctrl V
		|| keynum==120)	// ctrl X
		&& ve_evt.ctrlKey)
		return true;
	else if((keynum >= 48 && keynum <= 57)) { // numeros
		if (ve_max_num_dec > 0 && vl_reg_coma.test(ve_obj.value)) { // permite decimales y ya tiene la coma
			var vl_pos = ve_obj.value.lastIndexOf(',');
			var vl_pos_cursor = posicionCursor(ve_obj);
			if (vl_pos_cursor <= vl_pos)	// se esta antes de la coma
				return true;
			var vl_cant_dec = ve_obj.value.length - vl_pos - 1;
			if (vl_cant_dec >= ve_max_num_dec)
				return false;
		}
		return true;
	}
	else if (keynum==45) { // minus
		if (ve_solo_positivo)	// no permite negativos
			return false;
		else if (ve_obj.value.substr(0,1)=="-") // ya esta el caracter menos
			return false
		else if (posicionCursor(ve_obj)==0)
			return true;
	}
	else if (keynum==44) { // coma
		if (ve_max_num_dec ==0)	// no permite decimales
			return false;
		
		if (vl_reg_coma.test(ve_obj.value))	// ya tiene la coma
			return false;
		else
			return true;
	}
	else if (keynum==13){ //enter
		var i;
		for (i = 0; i < ve_obj.form.elements.length; i++)
			if (ve_obj == ve_obj.form.elements[i])
			break;
		
		i = ve_obj.form.elements[i].tabIndex + 1;
		for( j = 0 ; j < ve_obj.form.elements.length; j++)
		{
			if( ve_obj.form.elements[j].tabIndex == i)
				break;
		}
		
		if(ve_obj.form.elements[j]) {
		    ve_obj.form.elements[j].focus();
		    return false;
		} 
		else {
		    return true;
		}
	}
	else {
		return false;
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

function validate() {
	var direccion_comercial = document.getElementById('DIRECCION_COMERCIAL_0').value;
	var pais_comercial = document.getElementById('COD_PAIS_COMERCIAL_0').value;

	if (direccion_comercial != '' && pais_comercial == '') {
		alert('debe ingresar Pa\u00EDs Comercial');
		return false;
	}
	/********************************************************************************/
	/**************************** DECLARACION MES CONYUGE ***************************/
	/********************************************************************************/
	var conyuge_declara = document.getElementById('CONYUGE_DECLARA_0').checked;
	if(conyuge_declara == true){
		var monto_pesos_conyuge_enero = document.getElementById('MONTO_CONYUGE_PESOS1_0').value;
		if(monto_pesos_conyuge_enero == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS1_0').focus();
			return false;
		}
		var monto_pesos_conyuge_febrero = document.getElementById('MONTO_CONYUGE_PESOS1_1').value;
		if(monto_pesos_conyuge_febrero == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS1_1').focus();
			return false;
		}
		var monto_pesos_conyuge_marzo = document.getElementById('MONTO_CONYUGE_PESOS1_2').value;
		if(monto_pesos_conyuge_marzo == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS1_2').focus();
			return false;
		}
		var monto_pesos_conyuge_abril = document.getElementById('MONTO_CONYUGE_PESOS1_3').value;
		if(monto_pesos_conyuge_abril == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS1_3').focus();
			return false;
		}
		var monto_pesos_conyuge_mayo = document.getElementById('MONTO_CONYUGE_PESOS1_4').value;
		if(monto_pesos_conyuge_mayo == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS1_4').focus();
			return false;
		}
		var monto_pesos_conyuge_junio = document.getElementById('MONTO_CONYUGE_PESOS1_5').value;
		if(monto_pesos_conyuge_junio == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS1_5').focus();
			return false;
		}
		var monto_pesos_conyuge_julio = document.getElementById('MONTO_CONYUGE_PESOS2_0').value;
		if(monto_pesos_conyuge_julio == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS2_0').focus();
			return false;
		}
		var monto_pesos_conyuge_agosto = document.getElementById('MONTO_CONYUGE_PESOS2_1').value;
		if(monto_pesos_conyuge_agosto == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS2_1').focus();
			return false;
		}
		var monto_pesos_conyuge_septiembre = document.getElementById('MONTO_CONYUGE_PESOS2_2').value;
		if(monto_pesos_conyuge_septiembre == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS2_2').focus();
			return false;
		}
		var monto_pesos_conyuge_octubre = document.getElementById('MONTO_CONYUGE_PESOS2_3').value;
		if(monto_pesos_conyuge_octubre == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS2_3').focus();
			return false;
		}
		var monto_pesos_conyuge_noviembre = document.getElementById('MONTO_CONYUGE_PESOS2_4').value;
		if(monto_pesos_conyuge_noviembre == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS2_4').focus();
			return false;
		}
		var monto_pesos_conyuge_diciembre = document.getElementById('MONTO_CONYUGE_PESOS2_5').value;
		if(monto_pesos_conyuge_diciembre == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS2_5').focus();
			return false;
		}
	}
	return true;
}

function load_declaracion() {
	var vl_cod_estado_civil = document.getElementById('COD_ESTADO_CIVIL_0');
	if (vl_cod_estado_civil) {
		if (vl_cod_estado_civil.value == '') {
			document.getElementById('MONTO_DEUDOR_PESOS1_0').readOnly = true;
			document.getElementById('MONTO_DEUDOR_PESOS1_1').readOnly = true;
			document.getElementById('MONTO_DEUDOR_PESOS1_2').readOnly = true;
			document.getElementById('MONTO_DEUDOR_PESOS1_3').readOnly = true;
			document.getElementById('MONTO_DEUDOR_PESOS1_4').readOnly = true;
			document.getElementById('MONTO_DEUDOR_PESOS1_5').readOnly = true;
			document.getElementById('MONTO_DEUDOR_PESOS2_0').readOnly = true;
			document.getElementById('MONTO_DEUDOR_PESOS2_1').readOnly = true;
			document.getElementById('MONTO_DEUDOR_PESOS2_2').readOnly = true;
			document.getElementById('MONTO_DEUDOR_PESOS2_3').readOnly = true;
			document.getElementById('MONTO_DEUDOR_PESOS2_4').readOnly = true;
			document.getElementById('MONTO_DEUDOR_PESOS2_5').readOnly = true;
	 	}
	}		
		
	var conyuge = document.getElementById('CONYUGE_DECLARA_0');
	if (conyuge) { 
		if (conyuge.checked == false) {
			document.getElementById('CONYUGE_DECLARA_0').checked = false;
			document.getElementById('MONTO_CONYUGE_PESOS1_0').readOnly = true;
			document.getElementById('MONTO_CONYUGE_PESOS1_1').readOnly = true;
			document.getElementById('MONTO_CONYUGE_PESOS1_2').readOnly = true;
			document.getElementById('MONTO_CONYUGE_PESOS1_3').readOnly = true;
			document.getElementById('MONTO_CONYUGE_PESOS1_4').readOnly = true;
			document.getElementById('MONTO_CONYUGE_PESOS1_5').readOnly = true;
			document.getElementById('MONTO_CONYUGE_PESOS2_0').readOnly = true;
			document.getElementById('MONTO_CONYUGE_PESOS2_1').readOnly = true;
			document.getElementById('MONTO_CONYUGE_PESOS2_2').readOnly = true;
			document.getElementById('MONTO_CONYUGE_PESOS2_3').readOnly = true;
			document.getElementById('MONTO_CONYUGE_PESOS2_4').readOnly = true;
			document.getElementById('MONTO_CONYUGE_PESOS2_5').readOnly = true;
		}
	}
	asigna_tabindex();
}

function selecciona_dropdown(ve_id_dropdown, ve_value) {
	var vl_dropdown = document.getElementById(ve_id_dropdown);
	for ( var i = 0; i < vl_dropdown.options.length; i++) {
		if (vl_dropdown.options[i].value == ve_value) {
			vl_dropdown.selectedIndex = i;
			break;
		}
	}
}

function mostrarOcultar_estado(ve_cod_estado_civil) {
	if(ve_cod_estado_civil.value==1){	// soltero
		document.getElementById('RUT_CONYUGE_0').readOnly = true;
		document.getElementById('NOM_CONYUGE_0').readOnly = true;
		document.getElementById('CONYUGE_ES_DEUDOR_0').disabled = true;
		document.getElementById('CONYUGE_DECLARA_0').disabled = true;
		document.getElementById('DIG_VERIF_CONYUGE_0').readOnly = true;

		document.getElementById('RUT_CONYUGE_0').value = '';
		document.getElementById('DIG_VERIF_CONYUGE_0').value = '';
		document.getElementById('NOM_CONYUGE_0').value = '';
		document.getElementById('CONYUGE_ES_DEUDOR_0').checked = false;
		document.getElementById('CONYUGE_DECLARA_0').checked = false;
		
		for (i=0; i<6; i++) {		
			document.getElementById('MONTO_DEUDOR_PESOS1_' + i).readOnly = false;
			document.getElementById('MONTO_DEUDOR_PESOS2_' + i).readOnly = false;

			document.getElementById('MONTO_CONYUGE_PESOS1_' + i).readOnly = true;
			document.getElementById('MONTO_CONYUGE_PESOS2_' + i).readOnly = true;

			document.getElementById('MONTO_CONYUGE_PESOS1_' + i).value = '';
			document.getElementById('MONTO_CONYUGE_MONEDA1_' + i).innerHTML = '';
			document.getElementById('MONTO_CONYUGE_MONEDA1_H_' + i).value = 0;
	
			document.getElementById('MONTO_CONYUGE_PESOS2_' + i).value = '';
			document.getElementById('MONTO_CONYUGE_MONEDA2_' + i).innerHTML = '';
			document.getElementById('MONTO_CONYUGE_MONEDA2_H_' + i).value = 0;
	
			document.getElementById('MONTO_CONYUGE_PESOS_' + i).value = 0;
			document.getElementById('MONTO_CONYUGE_PESOS_H_' + i).value = 0;
			document.getElementById('MONTO_CONYUGE_MONEDA_' + i).value = 0;
			document.getElementById('MONTO_CONYUGE_MONEDA_H_' + i).value = 0;
		}
		document.getElementById('SUM_MONTO_CONYUGE_PESOS_0').innerHTML = "";
		document.getElementById('SUM_MONTO_CONYUGE_PESOS_H_0').value = "";
		document.getElementById('SUM_MONTO_CONYUGE_MONEDA_0').innerHTML = "";
		document.getElementById('SUM_MONTO_CONYUGE_MONEDA_H_0').value = "";
	}
	else if(ve_cod_estado_civil.value==2){	// casado
		document.getElementById('RUT_CONYUGE_0').readOnly = false;
		document.getElementById('NOM_CONYUGE_0').readOnly = false;
		document.getElementById('CONYUGE_ES_DEUDOR_0').disabled = false;
		document.getElementById('CONYUGE_DECLARA_0').disabled = false;
		document.getElementById('DIG_VERIF_CONYUGE_0').readOnly = false;

		for (i=0; i<6; i++) {
			document.getElementById('MONTO_DEUDOR_PESOS1_' + i).readOnly = false;
			document.getElementById('MONTO_DEUDOR_PESOS2_' + i).readOnly = false;

			document.getElementById('MONTO_CONYUGE_PESOS1_' + i).readOnly = true;
			document.getElementById('MONTO_CONYUGE_PESOS2_' + i).readOnly = true;

			document.getElementById('MONTO_CONYUGE_PESOS1_' + i).value = '';
			document.getElementById('MONTO_CONYUGE_MONEDA1_' + i).innerHTML = '';
			document.getElementById('MONTO_CONYUGE_MONEDA1_H_' + i).value = 0;
	
			document.getElementById('MONTO_CONYUGE_PESOS2_' + i).value = '';
			document.getElementById('MONTO_CONYUGE_MONEDA2_' + i).innerHTML = '';
			document.getElementById('MONTO_CONYUGE_MONEDA2_H_' + i).value = 0;
	
			document.getElementById('MONTO_CONYUGE_PESOS_' + i).value = 0;
			document.getElementById('MONTO_CONYUGE_PESOS_H_' + i).value = 0;
			document.getElementById('MONTO_CONYUGE_MONEDA_' + i).value = 0;
			document.getElementById('MONTO_CONYUGE_MONEDA_H_' + i).value = 0;
		}
	}
}

function existe_rut() {
	var ajax = nuevoAjax();
	var rut_deudor = document.getElementById('RUT_0').value;
	var ano_declaracion = document.getElementById('ANO_DECLARACION_0').value;
	ajax.open("GET", "ajax_load_deudor.php?rut_deudor=" + rut_deudor + "&ano_declaracion=" + ano_declaracion, false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	var lista = resp.split('|');

	if (lista[0] == "VACIO") {
		// set_datos_deudor();
	} else if (lista[0] == "NO") {
		alert("El Deudor No Existe");
		set_datos_deudor();
		document.getElementById('val').style.display = 'none';	
	} 
	else if(lista[0] == "NO_POSEE_DEUDA_EXIGIBLE"){
		document.getElementById('val').style.display = 'none';
		alert("El Deudor ingresado no posee una deuda en estado Exigible.");
		set_datos_deudor();
	}
	else if(lista[0] == "NO_POSEE_DEUDA_CS_CSR"){
		document.getElementById('val').style.display = 'none';
		alert("El Deudor ingresado no posee una deuda tipo CS o CSR.");
		set_datos_deudor();
	}
	else if (lista[0] == "CONTIENE_DECLARACION") {
		document.getElementById('val').style.display = 'none';	
		set_datos_deudor();
		alert("El Deudor posee una declaracion Aprobada para el año " + ano_declaracion);
	}
	else if (lista[0] == "CARTERA_DIFERENTE") {
		
		alert('El Deudor tiene una deuda con otra Cartera distinta a la por defecto');
		set_datos_deudor();		
	} else {
		document.getElementById('RUT_NO_ING_0').innerHTML = rut_deudor;
		document.getElementById('COD_DEUDOR_0').value = lista[0];
		document.getElementById('DIG_VERIF_0').innerHTML = lista[1];
		document.getElementById('DIG_VERIF_NO_ING_0').innerHTML = lista[1];
		document.getElementById('NOMBRE_0').innerHTML = lista[2];
		document.getElementById('NOMBRE_NO_ING_0').innerHTML = lista[2];
		document.getElementById('DIRECCION_PARTICULAR_0').value = lista[3];
		document.getElementById('FECHA_DIRECCION_PARTICULAR_0').innerHTML = lista[4];

		set_value_dropdown('COD_PAIS_PARTICULAR_0', lista[7]);
		load_drop_down('declaracion', 'COD_PAIS_PARTICULAR_0', 'COD_REGION_PARTICULAR');
		set_value_dropdown('COD_REGION_PARTICULAR_0', lista[19]);
		load_drop_down('declaracion', 'COD_REGION_PARTICULAR_0', 'COD_CIUDAD_PARTICULAR');
		set_value_dropdown('COD_CIUDAD_PARTICULAR_0', lista[6]);
		load_drop_down('declaracion', 'COD_CIUDAD_PARTICULAR_0', 'COD_COMUNA_PARTICULAR');
		set_value_dropdown('COD_COMUNA_PARTICULAR_0', lista[5]);

		document.getElementById('EMAIL_PARTICULAR_0').value = lista[8];
		document.getElementById('TELEFONO_PARTICULAR_0').value = lista[9];
		document.getElementById('CELULAR_PARTICULAR_0').value = lista[10];
		document.getElementById('DIRECCION_COMERCIAL_0').value = lista[11];
		document.getElementById('FECHA_DIRECCION_COMERCIAL_0').innerHTML = lista[12];
		document.getElementById('EMAIL_COMERCIAL_0').value = lista[16];
		document.getElementById('TELEFONO_COMERCIAL_0').value = lista[17];
		document.getElementById('CELULAR_COMERCIAL_0').value = lista[18];
		document.getElementById('COD_REGION_COMERCIAL_0').value = lista[20];

		set_value_dropdown('COD_PAIS_COMERCIAL_0', lista[15]);
		load_drop_down('declaracion', 'COD_PAIS_COMERCIAL_0', 'COD_REGION_COMERCIAL');
		set_value_dropdown('COD_REGION_COMERCIAL_0', lista[20]);
		load_drop_down('declaracion', 'COD_REGION_COMERCIAL_0', 'COD_CIUDAD_COMERCIAL');
		set_value_dropdown('COD_CIUDAD_COMERCIAL_0', lista[14]);
		load_drop_down('declaracion', 'COD_CIUDAD_COMERCIAL_0', 'COD_COMUNA_COMERCIAL');
		set_value_dropdown('COD_COMUNA_COMERCIAL_0', lista[13]);
		
		CSR_menor_10_cuotas(rut_deudor);
	}
}
function valida_ano(ve_ano, ve_ano_actual) {
	var ano = ve_ano.value;
	var largo = ano.length;
	if (largo < 4) {
		alert('Debe ingresar año con formato "AAAA"');
		document.getElementById('ANO_DECLARACION_0').value = '';
		document.getElementById('ANO_DECLARACION_0').focus();
		return false;
	} else if (ano < ve_ano_actual-50 || ano >= ve_ano_actual) {
		alert('Año invalido para declarar');
		document.getElementById('ANO_DECLARACION_0').value = '';
		document.getElementById('ANO_COBRO_0').innerHTML = '';
		document.getElementById('ANO_DECLARACION_0').focus();
		return false;
	}else if(largo == 4 && ano > ve_ano_actual-50) {
		var ajax = nuevoAjax();
		var rut_deudor = document.getElementById('RUT_0').value;
		ajax.open("GET", "ajax_valida_ano_rut.php?rut=" + rut_deudor + "&ano=" + ano, false);
		ajax.send(null);
		var resp = URLDecode(ajax.responseText);
		if (resp == '"TIENE"'){
			alert('El deudor ya posee una Declaración en estado "Solicitada" o "Aprobada" para el Año ingresado.');
			ve_ano.value = '';
			ve_ano.focus();
			
			document.getElementById('VALOR_UTM1_0').value = 0;
			document.getElementById('VALOR_UTM1_1').value = 0;
			document.getElementById('VALOR_UTM1_2').value = 0;
			document.getElementById('VALOR_UTM1_3').value = 0;
			document.getElementById('VALOR_UTM1_4').value = 0;
			document.getElementById('VALOR_UTM1_5').value = 0;
			
			document.getElementById('VALOR_UTM2_0').value = 0;
			document.getElementById('VALOR_UTM2_1').value = 0;
			document.getElementById('VALOR_UTM2_2').value = 0;
			document.getElementById('VALOR_UTM2_3').value = 0;
			document.getElementById('VALOR_UTM2_4').value = 0;
			document.getElementById('VALOR_UTM2_5').value = 0;
			
			for (var i=0; i < 6; i++) {
				computed(i, 'MONTO_DEUDOR_MONEDA1');
				computed(i, 'MONTO_DEUDOR_MONEDA2');
				computed(i, 'MONTO_CONYUGE_MONEDA1');
				computed(i, 'MONTO_CONYUGE_MONEDA2');
			}
		}else if (resp == '"NO_TIENE"') {
			valor_utm_x_ano(ve_ano);
			return true;
		}
	}
}
function asigna_tabindex() {
	document.getElementById('ENTREGA_F22_0').setAttribute('tabindex', "1");
	document.getElementById('CON_HIJO_0').setAttribute('tabindex', "2");
	document.getElementById('COD_AFP_0').setAttribute('tabindex', "3");
	document.getElementById('COD_ESTADO_CIVIL_0').setAttribute('tabindex', "4");
	document.getElementById('RUT_CONYUGE_0').setAttribute('tabindex', "5");
	document.getElementById('DIG_VERIF_CONYUGE_0').setAttribute('tabindex', "6");
	document.getElementById('NOM_CONYUGE_0').setAttribute('tabindex', "7");
	document.getElementById('CONYUGE_ES_DEUDOR_0').setAttribute('tabindex', "8");
	document.getElementById('CONYUGE_DECLARA_0').setAttribute('tabindex', "0");

	document.getElementById('MONTO_DEUDOR_PESOS1_0').setAttribute('tabindex', "10");
	document.getElementById('MONTO_DEUDOR_PESOS1_1').setAttribute('tabindex', "11");
	document.getElementById('MONTO_DEUDOR_PESOS1_2').setAttribute('tabindex', "12");
	document.getElementById('MONTO_DEUDOR_PESOS1_3').setAttribute('tabindex', "13");
	document.getElementById('MONTO_DEUDOR_PESOS1_4').setAttribute('tabindex', "14");
	document.getElementById('MONTO_DEUDOR_PESOS1_5').setAttribute('tabindex', "15");

	document.getElementById('MONTO_DEUDOR_PESOS2_0').setAttribute('tabindex', "16");
	document.getElementById('MONTO_DEUDOR_PESOS2_1').setAttribute('tabindex', "17");
	document.getElementById('MONTO_DEUDOR_PESOS2_2').setAttribute('tabindex', "18");
	document.getElementById('MONTO_DEUDOR_PESOS2_3').setAttribute('tabindex', "19");
	document.getElementById('MONTO_DEUDOR_PESOS2_4').setAttribute('tabindex', "20");
	document.getElementById('MONTO_DEUDOR_PESOS2_5').setAttribute('tabindex', "21");

	document.getElementById('MONTO_CONYUGE_PESOS1_0').setAttribute('tabindex', "22");
	document.getElementById('MONTO_CONYUGE_PESOS1_1').setAttribute('tabindex', "23");
	document.getElementById('MONTO_CONYUGE_PESOS1_2').setAttribute('tabindex', "24");
	document.getElementById('MONTO_CONYUGE_PESOS1_3').setAttribute('tabindex', "25");
	document.getElementById('MONTO_CONYUGE_PESOS1_4').setAttribute('tabindex', "26");
	document.getElementById('MONTO_CONYUGE_PESOS1_5').setAttribute('tabindex', "27");

	document.getElementById('MONTO_CONYUGE_PESOS2_0').setAttribute('tabindex', "28");
	document.getElementById('MONTO_CONYUGE_PESOS2_1').setAttribute('tabindex', "29");
	document.getElementById('MONTO_CONYUGE_PESOS2_2').setAttribute('tabindex', "30");
	document.getElementById('MONTO_CONYUGE_PESOS2_3').setAttribute('tabindex', "31");
	document.getElementById('MONTO_CONYUGE_PESOS2_4').setAttribute('tabindex', "32");
	document.getElementById('MONTO_CONYUGE_PESOS2_5').setAttribute('tabindex', "33");
}
function f_usuario_confirma(ve_nom_usuario, ve_fecha) {
	document.getElementById('NOM_USUARIO_CONFIRMA_0').innerHTML = '';
	document.getElementById('NOM_USUARIO_CONFIRMA_0').value = '';
	document.getElementById('FECHA_CONFIRMA_NO_ING_0').innerHTML = '';
	document.getElementById('FECHA_CONFIRMA_NO_ING_0').value = '';

	var est_declaracion = document.getElementById('COD_ESTADO_DECLARACION_0').value;

	if (est_declaracion == 2) {
		document.getElementById('NOM_USUARIO_CONFIRMA_0').innerHTML = ve_nom_usuario;
		document.getElementById('NOM_USUARIO_CONFIRMA_0').value = ve_nom_usuario;

		document.getElementById('FECHA_CONFIRMA_NO_ING_0').innerHTML = ve_fecha;
		document.getElementById('FECHA_CONFIRMA_NO_ING_0').value = ve_fecha;
	}
}
function set_datos_deudor() {
	document.getElementById('RUT_0').value = null;
	document.getElementById('DIG_VERIF_0').innerHTML = null;
	document.getElementById('RUT_NO_ING_0').innerHTML = null;
	document.getElementById('DIG_VERIF_NO_ING_0').innerHTML = null;
	document.getElementById('NOMBRE_0').innerHTML = null;
	document.getElementById('NOMBRE_NO_ING_0').innerHTML = null;
	document.getElementById('DIRECCION_PARTICULAR_0').value = null;
	document.getElementById('FECHA_DIRECCION_PARTICULAR_0').innerHTML = null;
	document.getElementById('COD_COMUNA_PARTICULAR_0').value = null;
	document.getElementById('COD_CIUDAD_PARTICULAR_0').value = null;
	document.getElementById('COD_REGION_PARTICULAR_0').value = null;
	document.getElementById('COD_PAIS_PARTICULAR_0').value = null;
	document.getElementById('EMAIL_PARTICULAR_0').value = null;
	document.getElementById('TELEFONO_PARTICULAR_0').value = null;
	document.getElementById('CELULAR_PARTICULAR_0').value = null;
	document.getElementById('DIRECCION_COMERCIAL_0').value = null;
	document.getElementById('FECHA_DIRECCION_COMERCIAL_0').innerHTML = null;
	document.getElementById('COD_COMUNA_COMERCIAL_0').value = null;
	document.getElementById('COD_CIUDAD_COMERCIAL_0').value = null;
	document.getElementById('COD_REGION_COMERCIAL_0').value = null;
	document.getElementById('COD_PAIS_COMERCIAL_0').value = null;
	document.getElementById('EMAIL_COMERCIAL_0').value = null;
	document.getElementById('TELEFONO_COMERCIAL_0').value = null;
	document.getElementById('CELULAR_COMERCIAL_0').value = null;
	document.getElementById('RUT_CONYUGE_0').value = null;
	document.getElementById('FECHA_DECLARACION_0').innerHTML = null;
	document.getElementById('ENTREGA_F22_0').checked = false;
	document.getElementById('CON_HIJO_0').checked = false;
	document.getElementById('COD_AFP_0').value = null;
	document.getElementById('COD_ESTADO_CIVIL_0').value = null;
	document.getElementById('RUT_CONYUGE_0').value = null;
	document.getElementById('DIG_VERIF_CONYUGE_0').innerHTML = null;
	document.getElementById('NOM_CONYUGE_0').value = null;
	document.getElementById('CONYUGE_ES_DEUDOR_0').checked = false;
	document.getElementById('CONYUGE_DECLARA_0').checked = false;
	document.getElementById('val').style.display = 'none';	
}

function mostrarOcultar() {
	var conyuge = document.getElementById('CONYUGE_DECLARA_0').checked;

	for (i=0; i<6; i++) {	
		document.getElementById('MONTO_CONYUGE_PESOS1_' + i).readOnly = !conyuge;
		document.getElementById('MONTO_CONYUGE_PESOS1_' + i).value = '';
		document.getElementById('MONTO_CONYUGE_MONEDA1_' + i).innerHTML = '';
		document.getElementById('MONTO_CONYUGE_MONEDA1_H_' + i).value = 0;

		document.getElementById('MONTO_CONYUGE_PESOS2_' + i).readOnly = !conyuge;
		document.getElementById('MONTO_CONYUGE_PESOS2_' + i).value = '';
		document.getElementById('MONTO_CONYUGE_MONEDA2_' + i).innerHTML = '';
		document.getElementById('MONTO_CONYUGE_MONEDA2_H_' + i).value = 0;

		document.getElementById('MONTO_CONYUGE_PESOS_' + i).value = 0;
		document.getElementById('MONTO_CONYUGE_MONEDA_' + i).value = 0;
	}

	document.getElementById('SUM_MONTO_CONYUGE_PESOS_0').innerHTML = "";
	document.getElementById('SUM_MONTO_CONYUGE_PESOS_H_0').value = "";
	document.getElementById('SUM_MONTO_CONYUGE_MONEDA_0').innerHTML = "";
	document.getElementById('SUM_MONTO_CONYUGE_MONEDA_H_0').value = "";
	document.getElementById('MONTO_CONYUGE_PESOS1_0').focus();
}

function valor_utm_x_ano(ve_ano){
	var ajax = nuevoAjax();
	ajax.open("GET", "ajax_valor_utm.php?ano="+ve_ano.value, false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	var aDato = eval("(" + resp + ")");
	
	document.getElementById('VALOR_UTM1_0').value = aDato[0]['VALOR_UTM1'];
	document.getElementById('VALOR_UTM1_1').value = aDato[0]['VALOR_UTM2'];
	document.getElementById('VALOR_UTM1_2').value = aDato[0]['VALOR_UTM3'];
	document.getElementById('VALOR_UTM1_3').value = aDato[0]['VALOR_UTM4'];
	document.getElementById('VALOR_UTM1_4').value = aDato[0]['VALOR_UTM5'];
	document.getElementById('VALOR_UTM1_5').value = aDato[0]['VALOR_UTM6'];
	
	document.getElementById('VALOR_UTM2_0').value = aDato[0]['VALOR_UTM7'];
	document.getElementById('VALOR_UTM2_1').value = aDato[0]['VALOR_UTM8'];
	document.getElementById('VALOR_UTM2_2').value = aDato[0]['VALOR_UTM9'];
	document.getElementById('VALOR_UTM2_3').value = aDato[0]['VALOR_UTM10'];
	document.getElementById('VALOR_UTM2_4').value = aDato[0]['VALOR_UTM11'];
	document.getElementById('VALOR_UTM2_5').value = aDato[0]['VALOR_UTM12'];
	
	for (var i=0; i < 6; i++) {
		computed(i, 'MONTO_DEUDOR_MONEDA1');
		computed(i, 'MONTO_DEUDOR_MONEDA2');
		computed(i, 'MONTO_CONYUGE_MONEDA1');
		computed(i, 'MONTO_CONYUGE_MONEDA2');
	}
} 

function aviso_deuda_cruzada(){
	var rut = document.getElementById('RUT_0').value;
	if(rut == ''){
		document.getElementById('val').style.display = 'none';
		return false;
	}

	var ajax = nuevoAjax();
	ajax.open("GET", "ajax_aviso_deuda_cruzada.php?rut="+rut, false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);

	if(resp == 0){
		document.getElementById('val').style.display = 'none';
	}
	else{
		document.getElementById('val').style.display = '';		
	}
}	

function calc_ano_cobro(){
	var ano_declaracion = document.getElementById('ANO_DECLARACION_0').value;
	var ano_cobro = document.getElementById('ANO_COBRO_H_0').value;
	var cal = parseInt(ano_declaracion) - parseInt(ano_cobro);
	if (cal < -1){
		document.getElementById('ANO_COBRO_0').innerHTML = parseInt(document.getElementById('ANO_DECLARACION_0').value) + parseInt(1);
		document.getElementById('ANO_COBRO_H_0').value = parseInt(document.getElementById('ANO_DECLARACION_0').value) + parseInt(1);
	}
	else if(cal >= 0){
		document.getElementById('ANO_COBRO_0').innerHTML = parseInt(document.getElementById('ANO_DECLARACION_0').value) + parseInt(1);
		document.getElementById('ANO_COBRO_H_0').value = parseInt(document.getElementById('ANO_DECLARACION_0').value) + parseInt(1);
	}
}

function CSR_menor_10_cuotas(ve_rut){
	var rut = ve_rut;
	var ajax = nuevoAjax();
	ajax.open("GET", "ajax_CSR_menor_10_cuotas.php?rut="+rut, false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	
	if(resp == 'CANT_CUOTAS_MENOR'){
		set_datos_deudor();
		document.getElementById('val').style.display = 'none';
		alert('El Deudor ingresado posee menor cantidad de cuotas que las establecidas para el tipo deuda "CSR".');
	}
}