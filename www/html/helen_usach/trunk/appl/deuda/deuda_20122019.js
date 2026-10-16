function cambio_dicom(check_dicom){
	var dicom = check_dicom.checked;
	var i = get_num_rec_field(check_dicom.id);
	var K_MOROSA = 4;
	var K_PAGADA = 3;
	var cod_estado_pp = document.getElementById('COD_ESTADO_PLAN_PAGO_H_' + i).value;		
	
	if(cod_estado_pp == K_MOROSA){
		// se cambia el valor del campo H para guardar en el save_record 
		if(dicom == true){
			document.getElementById('PUBLICA_DICOM_H_' + i).value = 'S';
		}else{
			document.getElementById('PUBLICA_DICOM_H_' + i).value = 'N';
		}
	}else if((cod_estado_pp == K_PAGADA)&&(dicom == false)){
			document.getElementById('PUBLICA_DICOM_H_' + i).value = 'N';
	}else{
		alert('No se puede Modificar el Plan Pago en el Estado actual.');
		// vuelve al estado anterior si es q no se puede modificar
		if(dicom==true){
			check_dicom.checked=false;
		}
		else{
			check_dicom.checked=true;
		}
	}
}

function validate() {
	var cod_estado_deuda = document.getElementById('COD_ESTADO_DEUDA_0').value;
	var k_estado_deuda = 1; //ESTADO DEUDA NO EXIGIBLE
	if (cod_estado_deuda == k_estado_deuda) {
		var aTR = get_TR('DEUDA_PAGARES');
		var tipo_rebaja = '';
		var monto_moneda_rebaja = '';
		var monto_pesos_rebaja = '';
		for (var i=0; i < aTR.length ; i++) {
			var record = get_num_rec_field(aTR[i].id);
			tipo_rebaja = document.getElementById('COD_TIPO_REBAJA_PAGARE_' + record).value;
			monto_moneda_rebaja = document.getElementById('MONTO_MONEDA_REBAJA_' + record).value;
			monto_pesos_rebaja = document.getElementById('MONTO_PESOS_REBAJA_' + record).value;
			
			if(tipo_rebaja != '' ||  monto_moneda_rebaja != '' || monto_pesos_rebaja != ''){
				if(tipo_rebaja == ''){
					alert('Debe Ingresar Tipo Rebaja antes de Grabar.');
					document.getElementById('COD_TIPO_REBAJA_PAGARE_' + record).focus();
					return false;
				}else if (monto_moneda_rebaja == ''){
					alert('Debe Ingresar Monto UTM Rebaja antes de Grabar.');
					document.getElementById('MONTO_MONEDA_REBAJA_' + record).focus();
					return false;
				}else if(monto_pesos_rebaja == ''){
					alert('Debe Ingresar Monto $ Rebaja antes de Grabar.');
					document.getElementById('MONTO_PESOS_REBAJA_' + record).focus();
					return false;
				}
			}else{
				return true;
			}
		}
	}	
	return true;	
}

function dlg_print_devolucion() {	
	
	cod_deudor = document.getElementById('COD_DEUDOR_0').value
	
	var ajax = nuevoAjax();
	ajax.open("GET", "ajax_valida_devolucion.php?cod_deudor="+cod_deudor, false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	
	var resp 	= URLDecode(ajax.responseText);
	var lista 	= resp.split('|');
	
	if(lista[0]=='TIENE_CHEQUES'){
		alert('El Deudor tiene Cheques por Depositar o Protestado')
		return;
	}
	else{
		var args = "location:no;dialogLeft:600px;dialogTop:500px;dialogWidth:550px;dialogHeight:320px;dialogLocation:0;Toolbar:no;";
		var returnVal = window.showModalDialog("dlg_print_devolucion.php?cod_deudor="+cod_deudor,"_blank", args);
	 	if (returnVal == null)
	 		return false;
		else {
			document.getElementById('wi_hidden').value = returnVal;
			document.input.submit();
	   		return true;
		}
	}
}

//esta función permite seleccionar todos o ninguno si es cod_estado_plan_pago = 4 -> MOROSA 
function seleccion_dicom(){
	var seleccion = document.getElementById('SELECCIONA_DICOM_0').checked;
	var K_MOROSA = 4;
	
	var aTR = get_TR('DEUDA_PLAN_PAGO');
	for (var i=0; i < aTR.length ; i++) {
		var record = get_num_rec_field(aTR[i].id);
		var cod_estado_pp = document.getElementById('COD_ESTADO_PLAN_PAGO_H_' + record).value;
		if(seleccion == true){
			if(cod_estado_pp == K_MOROSA)
				document.getElementById('PUBLICA_DICOM_' + record).checked 	= true;
				document.getElementById('PUBLICA_DICOM_H_' + record).value 	= 'S';
				document.getElementById('VALOR_DICOM_H_0').value 			= 1;
				document.getElementById('SELECCIONA_DICOM_0').checked = true;
		}
		else{
			if(cod_estado_pp == K_MOROSA)
				document.getElementById('PUBLICA_DICOM_' + record).checked = false;
				document.getElementById('PUBLICA_DICOM_H_' + record).value 	= 'N';
				document.getElementById('VALOR_DICOM_H_0').value 			= 0;
				document.getElementById('SELECCIONA_DICOM_0').checked = false;
		}
	}
}

function calcular_porc_cruzado(){
	//se recalcula los porcentajes.
	var porc_item = 0;
	var sum_total_porc = 0;
	var sum_monto_actualizado = document.getElementById('SUMA_MONTO_ACTUALIZADO_0').innerHTML;
	sum_monto_actualizado = sum_monto_actualizado.replace(".","");
	sum_monto_actualizado = sum_monto_actualizado.replace(",",".");
	var sum_porc = document.getElementById('SUMA_MONTO_PORC_0').innerHTML;
	sum_porc = sum_porc.replace(",",".");
	var aTR = get_TR('DEUDA_CRUZADOS');
	for (i=0; i<aTR.length; i++){
		var rec_tr = get_num_rec_field(aTR[i].id);  
		var monto_actualizado = document.getElementById('MONTO_ACTUALIZADO_' + rec_tr).value;
		monto_actualizado = monto_actualizado.replace(",",".");
		if(monto_actualizado == ''){
			monto_actualizado = 0;
		}
		
		//porcentajes item
		porc_item = parseFloat(monto_actualizado * 100) / parseFloat(sum_monto_actualizado);
		
		document.getElementById('PORC_' + rec_tr).innerHTML = number_format(porc_item, 2, ',', '.');
		document.getElementById('PORC_H_' + rec_tr).value = roundNumber(porc_item,2);
		porc_item = roundNumber(porc_item,2);
		sum_total_porc = parseFloat(sum_total_porc) + parseFloat(porc_item);
	}
	
	//el ultimo item porc setea para cuadrar con 100%
	if(parseFloat(sum_total_porc) != 100){
		var vl_porc_ultimo = document.getElementById('PORC_' + (i - 1)).innerHTML;
		vl_porc_ultimo = vl_porc_ultimo.replace(",",".");
		var vl_porc_descudre = roundNumber(parseFloat(vl_porc_ultimo),2) + (100 - roundNumber(parseFloat(sum_total_porc),2));
		
		document.getElementById('PORC_' + (i - 1)).innerHTML = number_format(vl_porc_descudre, 2, ',', '.');
		document.getElementById('PORC_H_' + (i - 1)).value = roundNumber(vl_porc_descudre,2);
	}
	document.getElementById('SUMA_MONTO_PORC_0').innerHTML = number_format(roundNumber(sum_total_porc,0), 2, ',', '.');
}

function sum_total_montos(){
	var sum_montos_actualizados = 0;
	//se suma todos los montos_actualizados
	var aTR = get_TR('DEUDA_CRUZADOS');
	for (i=0; i<aTR.length; i++){
		var rec_tr = get_num_rec_field(aTR[i].id);  
		var monto_actualizado = document.getElementById('MONTO_ACTUALIZADO_' + rec_tr).value;
		if(monto_actualizado == ''){
			monto_actualizado = 0;
		}
		sum_montos_actualizados = parseFloat(sum_montos_actualizados + parseFloat(monto_actualizado.replace(",",".")));
	}
	
	document.getElementById('SUMA_MONTO_ACTUALIZADO_0').innerHTML = number_format(sum_montos_actualizados, 4, ',', '.');
	calcular_porc_cruzado();
}

function del_line_cruzado(ve_tr_id, ve_nom_mantenedor) {
	del_line_standard(ve_tr_id, ve_nom_mantenedor);
	sum_total_montos();
}

function seleccion_emp_cobranza(){
	var cod_empresa_cobranza = document.getElementById('COD_EMPRESA_COBRANZA_0').value;
	var K_MOROSA = 4;
	
	var aTR = get_TR('DEUDA_PLAN_PAGO');
		
		var ajax = nuevoAjax();
		ajax.open("GET", "ajax_get_nom_empresa_cobranza.php?cod_empresa_cobranza="+cod_empresa_cobranza, false);
		ajax.send(null);
		var resp = URLDecode(ajax.responseText);
		
		for (var i=0; i < aTR.length ; i++) {
			var record = get_num_rec_field(aTR[i].id);
			var cod_estado_pp = document.getElementById('COD_ESTADO_PLAN_PAGO_H_' + record).value;
			
			if(cod_estado_pp == K_MOROSA)
				document.getElementById('NOM_EMPRESA_COBRANZA_' + record).innerHTML = resp;
				document.getElementById('VALOR_EM_CO_H_0').value 					= cod_empresa_cobranza;
		}
}

function valida_nuevo_estado(){
	var k_no_exigible = 1; //ESTADO DEUDA NO EXIGIBLE
	var cod_estado_deuda_old = document.getElementById('COD_ESTADO_DEUDA_H_0').value;
	var cod_estado_deuda = document.getElementById('COD_ESTADO_DEUDA_0').value;

	if (cod_estado_deuda_old != k_no_exigible && cod_estado_deuda == k_no_exigible){
		var nom_estado_deuda = document.getElementById('COD_ESTADO_DEUDA_0').options[document.getElementById('COD_ESTADO_DEUDA_0').selectedIndex].label;
		alert('Si desea cambiar el estado a '+nom_estado_deuda+', favor realizar prorroga de la deuda.');
		document.getElementById('COD_ESTADO_DEUDA_0').value = cod_estado_deuda_old;
	}
}