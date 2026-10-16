function validate(){
	var cod_estado_deposito_value = get_value('COD_ESTADO_DEPOSITO_0'); 
	if (to_num(cod_estado_deposito_value) == 3){	
		var motivo_anula = document.getElementById('MOTIVO_ANULA_0');
		if (motivo_anula.value == '') {
			alert('Debe ingresar el Motivo de Anulaci\u00f3n antes de grabar.');
			motivo_anula.focus();
			return false;
		}
	}
	
	var aTR = get_TR('DOCUMENTO_PAGO');
	for (i=0; i<aTR.length; i++){
		var rec_tr =get_num_rec_field(aTR[i].id);
		var seleccion = document.getElementById('SELECCION_'+rec_tr).checked;
		if (seleccion == true){
			break;
		}		
	}
	
	if (seleccion == false){ 
			alert("Debe seleccionar al menos un documento");
			return false;
		}
	return true;
}
function get_banco_nro_cta(){
	var cod_cuenta_corriente = document.getElementById('COD_CUENTA_CORRIENTE_0');
	
	var cod_cc = to_num(cod_cuenta_corriente.options[cod_cuenta_corriente.selectedIndex].value);
	ajax = nuevoAjax();
	ajax.open("GET", "get_banco_cta_cte.php?cod_cuenta_corriente="+cod_cuenta_corriente.options[cod_cuenta_corriente.selectedIndex].value,false);
    ajax.send(null);
    var resp = ajax.responseText.split('|');
    if (cod_cc == 0) {
		document.getElementById('NOM_BANCO_0').innerHTML 			= '';	
		document.getElementById('NRO_CUENTA_CORRIENTE_0').innerHTML = '';
		document.getElementById('COD_FAMILIA_DEUDA_0').value 		= '';
		document.getElementById('NOM_FAMILIA_DEUDA_0').innerHTML	= '';	 
		return;
	}
	cod_cuenta_corriente.value = resp[0];
	document.getElementById('NOM_BANCO_0').innerHTML 			= resp[1];
	document.getElementById('NRO_CUENTA_CORRIENTE_0').innerHTML = resp[2];
	document.getElementById('COD_FAMILIA_DEUDA_0').value 		= resp[3];
	document.getElementById('NOM_FAMILIA_DEUDA_0').innerHTML 	= resp[4];
}

function load_doc_x_depositar() {
	var cod_cuenta_corriente = document.getElementById('COD_CUENTA_CORRIENTE_0').value;
	var vl_fecha_deposito = document.getElementById('FECHA_DEPOSITO_0').value;
	
	var aTR = get_TR('DOCUMENTO_PAGO');
	for (i=0; i<aTR.length; i++){
		var rec_tr = get_num_rec_field(aTR[i].id);
		del_line('DOCUMENTO_PAGO_'+rec_tr, 'deposito');
	}
	
	var ajax=nuevoAjax();
	ajax.open("GET", "load_doc_x_depositar.php?cod_cuenta_corriente="+cod_cuenta_corriente+"&fecha_deposito="+vl_fecha_deposito, false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	var result = eval("(" + resp + ")");

	for (var i = 0; i < result.length; i++) {
		var row = add_line('DOCUMENTO_PAGO', 'deposito');
		
		document.getElementById('NRO_DOCUMENTO_PAGO_' + row).innerHTML = result[i]['NRO_DOCUMENTO_PAGO'];
		
		if (result[i]['SELECCION']=='S')
			document.getElementById('SELECCION_'+ row).checked = true;
		else
			document.getElementById('SELECCION_'+ row).checked = false;
		
		document.getElementById('SELECCION_V_' + row).value 				= result[i]['SELECCION_V'];
		document.getElementById('COD_DOCUMENTO_PAGO_' + row).value 			= result[i]['COD_DOCUMENTO_PAGO'];
		document.getElementById('COD_BANCO_DP_' + row).value	 			= result[i]['COD_BANCO_DP'];
		document.getElementById('NOM_BANCO_DP_' + row).innerHTML 			= URLDecode(result[i]['NOM_BANCO_DP']);
		document.getElementById('NOM_PLAZA_' + row).innerHTML 				= URLDecode(result[i]['NOM_PLAZA']);
		document.getElementById('FECHA_DOCUMENTO_PAGO_' + row).innerHTML 	= result[i]['FECHA_DOCUMENTO_PAGO'];
		document.getElementById('MONTO_' + row).innerHTML 					= number_format(result[i]['MONTO'], 0, ',', '.'); 
		document.getElementById('MONTO_SELECCIONADO_H_' + row).value 		= result[i]['MONTO']*result[i]['SELECCION_V'];
		document.getElementById('RUT_' + row).innerHTML 					= number_format(result[i]['RUT'], 0, ',', '.'); 
		document.getElementById('DIG_VERIF_' + row).innerHTML 				= result[i]['DIG_VERIF'];
		document.getElementById('COD_DEPOSITO_DOCUMENTO_' + row).innerHTML 	= result[i]['COD_DEPOSITO_DOCUMENTO'];
		document.getElementById('NOM_TIPO_DOCUMENTO_PAGO_' + row).innerHTML = URLDecode(result[i]['NOM_TIPO_DOCUMENTO_PAGO']);
		document.getElementById('NRO_INGRESO_' + row).innerHTML 			= result[i]['NRO_INGRESO'];
	}
	
	var aTR = get_TR('DOCUMENTO_PAGO'); 
	var suma = 0;
	for (var i=0; i < aTR.length ; i++) {
		var record = get_num_rec_field(aTR[i].id);
		var seleccion = document.getElementById('SELECCION_' + record).checked;
		if (seleccion == true){
			suma = suma + parseFloat(document.getElementById('MONTO_SELECCIONADO_H_' + record).value);
		}
	}
	document.getElementById('SUM_MONTO_SELECCIONADO_0').innerHTML = number_format(suma, 0, ',', '.');
	document.getElementById('SUM_MONTO_SELECCIONADO_H_0').value = suma;
	
}
function change_fecha_deposito(ve_fecha_deposito) {
	if (ve_fecha_deposito.value.length == 8 || ve_fecha_deposito.value.length == 10)
		load_doc_x_depositar();
}

function mostrarOcultar_Anula(ve_campo) {
	var tr_anula = document.getElementById('tr_anula');
	
	if (to_num(ve_campo.value)==3) {
		tr_anula.style.display = ''; 
		
		document.getElementById('MOTIVO_ANULA_0').type='text';
		document.getElementById('MOTIVO_ANULA_0').setAttribute('onblur', "this.style.border=''");				
		document.getElementById('MOTIVO_ANULA_0').setAttribute('onfocus', "this.style.border='1px solid #FF0000'");	
		document.getElementById('MOTIVO_ANULA_0').focus();
	}
	else{
		document.getElementById('MOTIVO_ANULA_0').value = '';
		tr_anula.style.display = 'none'; 
	}
}
function seleccion_deposito(ve_seleccion){ 
	var seleccion = ve_seleccion.checked;
	var record = get_num_rec_field(ve_seleccion.id);
	
	if (seleccion == true){
		document.getElementById('SELECCION_V_' + record).value = '1';
	}
	else if(seleccion == false){
		document.getElementById('SELECCION_V_' + record).value = '0';
	}
	computed(record, 'MONTO_SELECCIONADO');
}
/*******************FILTRO EN EL INPUT     ******/
function dlg_filtro_date(ve_field, ve_nom_header, ve_valor_filtro1, ve_valor_filtro2) {
	var args = "location:no;dialogLeft:400px;dialogTop:300px;dialogWidth:470px;dialogHeight:170px;dialogLocation:0;Toolbar:no;";
	var returnVal = window.showModalDialog("../../../../commonlib_seven/trunk/php/dlg_find_date.php?nom_header="+URLEncode(ve_nom_header)+"&valor_filtro1="+URLEncode(ve_valor_filtro1)+"&valor_filtro2="+URLEncode(ve_valor_filtro2), "_blank", args);

	var vl_filtro;
 	if (returnVal == null)
		vl_filtro = '__BORRAR_FILTRO__';
	else
		vl_filtro = returnVal;
		
	filtro_input(ve_field, vl_filtro);
}
function filtro_date(ve_field_id, ve_value_filtro) {
	var vl_filtrado = false;
	var vl_fecha = get_value(ve_field_id); 
	var aFechas = ve_value_filtro.split('|');
	if (aFechas.length==1) {
		if (vl_fecha!=aFechas[0])
			vl_filtrado = true;
	}
	else {
		var vl_fecha_ini = aFechas[0];
		var vl_ficha_fin = aFechas[1];
		
		// pasa las fechas a formato yyyymmdd para poder compararlas, vienen en formato dd/mm/yyyy 
		vl_fecha_ini = vl_fecha_ini.substr(6, 4) + vl_fecha_ini.substr(3, 2) + vl_fecha_ini.substr(0, 2); 
		vl_ficha_fin = vl_ficha_fin.substr(6, 4) + vl_ficha_fin.substr(3, 2) + vl_ficha_fin.substr(0, 2); 
		vl_fecha = vl_fecha.substr(6, 4) + vl_fecha.substr(3, 2) + vl_fecha.substr(0, 2); 
		
		if (!(parseInt(vl_fecha) >= parseInt(vl_fecha_ini) && parseInt(vl_fecha) <= parseInt(vl_ficha_fin))) 
			vl_filtrado = true;
	}
	return vl_filtrado;
}


function dlg_filtro_text(ve_field, ve_nom_header, ve_valor_filtro) {
	var args = "location:no;dialogLeft:400px;dialogTop:300px;dialogWidth:470px;dialogHeight:170px;dialogLocation:0;Toolbar:no;";
	var returnVal = window.showModalDialog("../../../../commonlib_seven/trunk/php/dlg_find_text.php?nom_header="+URLEncode(ve_nom_header)+"&valor_filtro="+URLEncode(ve_valor_filtro), "_blank", args);

	var vl_filtro;
 	if (returnVal == null)
		vl_filtro = '__BORRAR_FILTRO__';
	else
		vl_filtro = returnVal.trim();
	filtro_input(ve_field, vl_filtro);
}
function filtro_text(ve_field_id, ve_value_filtro) {
	var vl_text = get_value(ve_field_id); 
	vl_text = vl_text.toUpperCase();
	ve_value_filtro = ve_value_filtro.toUpperCase();
	
	var vl_res = vl_text.search(ve_value_filtro);
	if (vl_res == -1)
		return true;	// se debe filtrar
		
	return false;
}


function dlg_filtro_num(ve_field, ve_nom_header, ve_valor_filtro1, ve_valor_filtro2) {
	var args = "location:no;dialogLeft:400px;dialogTop:300px;dialogWidth:470px;dialogHeight:170px;dialogLocation:0;Toolbar:no;";
	var returnVal = window.showModalDialog("../../../../commonlib_seven/trunk/php/dlg_find_num.php?nom_header="+URLEncode(ve_nom_header)+"&valor_filtro1="+URLEncode(ve_valor_filtro1)+"&valor_filtro2="+URLEncode(ve_valor_filtro2)+"&cant_decimal=0&solo_positivos=1", "_blank", args);

	var vl_filtro;
 	if (returnVal == null)
		vl_filtro = '__BORRAR_FILTRO__';
	else
		vl_filtro = returnVal;
		
	filtro_input(ve_field, vl_filtro);
}
function filtro_num(ve_field_id, ve_value_filtro) {
	var vl_filtrado = false;
	var vl_num = get_value(ve_field_id);
	vl_num = to_num(vl_num); 
	var aNums = ve_value_filtro.split('|');
	if (aNums.length==1) {
		if (vl_num!=aNums[0])
			vl_filtrado = true;
	}
	else {
		var vl_num_ini = aNums[0];
		var vl_num_fin = aNums[1];
		
		if (!(parseInt(vl_num) >= parseInt(vl_num_ini) && parseInt(vl_num) <= parseInt(vl_num_fin))) 
			vl_filtrado = true;
	}
	return vl_filtrado;
}

function dlg_filtro_drop_down(ve_field, ve_nom_header, ve_valor_filtro, ve_sql) {
	var args = "location:no;dialogLeft:400px;dialogTop:300px;dialogWidth:470px;dialogHeight:170px;dialogLocation:0;Toolbar:no;";
	var returnVal = window.showModalDialog("../../../../commonlib_seven/trunk/php/dlg_find_drop_down.php?nom_header="+URLEncode(ve_nom_header)+"&valor_filtro="+URLEncode(ve_valor_filtro)+"&sql="+URLEncode(ve_sql), "_blank", args);

	var vl_filtro;
 	if (returnVal == null)
		vl_filtro = '__BORRAR_FILTRO__';
	else
		vl_filtro = returnVal;
		
	filtro_input(ve_field, vl_filtro);
}
function filtro_drop_down(ve_field_id, ve_value_filtro) {
	var vl_filtrado = false;
	var vl_num = get_value(ve_field_id);
	if (vl_num != ve_value_filtro)
		vl_filtrado = true;

	return vl_filtrado;
}
/////////////////

function filtro_input(ve_field, ve_filtro_new) {		
	ajax = nuevoAjax();
	ajax.open("GET", "ajax_filtro.php?field="+ve_field+"&filtro="+ve_filtro_new,false);
    ajax.send(null);
    
    var resp = URLDecode(ajax.responseText);
	var result = eval("(" + resp + ")");
	
    //var resp = ajax.responseText.split('|');

	var vl_ind = 0;		// para definir el class
	var aTR = get_TR('DOCUMENTO_PAGO');
	for (i=0; i<aTR.length; i++){
		var rec_tr = get_num_rec_field(aTR[i].id);

		// valida los filtros
		var vl_filtrado = false;
		if (!vl_filtrado && result['RUT']!='')
			vl_filtrado = filtro_text('RUT_' + rec_tr, result['RUT']);
		
		if (!vl_filtrado && result['NOM_TIPO_DOCUMENTO_PAGO']!='')
			vl_filtrado = filtro_text('NOM_TIPO_DOCUMENTO_PAGO_' + rec_tr, result['NOM_TIPO_DOCUMENTO_PAGO']);
		
		if (!vl_filtrado && result['NOM_BANCO_DP']!='')
			vl_filtrado = filtro_drop_down('COD_BANCO_DP_' + rec_tr, result['NOM_BANCO_DP']);
		
		if (!vl_filtrado && result['NOM_PLAZA']!='')
			vl_filtrado = filtro_text('NOM_PLAZA_' + rec_tr, result['NOM_PLAZA']);
		
		if (!vl_filtrado && result['NRO_DOCUMENTO_PAGO']!='')
			vl_filtrado = filtro_num('NRO_DOCUMENTO_PAGO_' + rec_tr, result['NRO_DOCUMENTO_PAGO']);
		
		if (!vl_filtrado && result['FECHA_DOCUMENTO_PAGO']!='')
			vl_filtrado = filtro_date('FECHA_DOCUMENTO_PAGO_' + rec_tr, result['FECHA_DOCUMENTO_PAGO']);
		
		if (!vl_filtrado && result['MONTO']!='')
			vl_filtrado = filtro_num('MONTO_' + rec_tr, result['MONTO']);
		
		// filtra o desfiltra
		var vl_seleccion = document.getElementById('SELECCION_' + rec_tr);
		if (vl_filtrado) {
			aTR[i].style.display = 'none';
			vl_seleccion.checked = false;
		}
		else {
			aTR[i].style.display = '';
			vl_seleccion.checked = true;
			if (vl_ind % 2 == 0)
				aTR[i].setAttribute('class', "claro");
			else	
				aTR[i].setAttribute('class', "oscuro");
			vl_ind++
		}
		seleccion_deposito(vl_seleccion); 	// click de SLEECCION
	}
}
/*******************FIN FILTRO EN EL INPUT     ******/

function marcar_todo() {
	var vl_marcar_todo = document.getElementById('MARCAR_TODO').value;
	if (vl_marcar_todo==0) {
		document.getElementById('MARCAR_TODO').value = 1;
		vl_check = true;
	}
	else {
		document.getElementById('MARCAR_TODO').value = 0;
		vl_check = false;
	}
	
	var aTR = get_TR('DOCUMENTO_PAGO');
	for (var i=0; i < aTR.length; i++)	{
		var vl_record = get_num_rec_field(aTR[i].id);
		
		if(aTR[i].style.display != 'none'){
			var vl_seleccion = document.getElementById('SELECCION_' + vl_record);
			vl_seleccion.checked = vl_check;
			seleccion_deposito(vl_seleccion);		
		}
	}
}
