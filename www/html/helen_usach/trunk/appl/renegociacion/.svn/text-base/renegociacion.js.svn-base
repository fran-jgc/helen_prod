function valida_saldo(){
	var dd_estado_convenio = document.getElementById('COD_ESTADO_RENEGOCIACION_0');
	var pago_inicial_sugerido = document.getElementById('PAGO_INICIAL_SUGERIDO_0').innerHTML;
	var saldo = document.getElementById('SALDO_RENEGOCIACION_H_0').value;
	var pago_inicial = document.getElementById('PAGO_INICIAL_0').value;
	var cod_usuario = document.getElementById('COD_USUARIO_H_0').value;
		
	//elimino los puntos
	pago_inicial_sugerido = pago_inicial_sugerido.replace(".", "");
	
	//aqui le resto 1 al estado, ya que el primer estado comienza en 0 para el JS
	var estado_convenio = document.getElementById('COD_ESTADO_ACTUAL_0').value-1;
	var estado_convenio_actual = dd_estado_convenio.value;
	var k_estado_aprobada = 2;
	
	if((saldo > 0)&&(estado_convenio_actual == k_estado_aprobada)){
		alert('No se puede Confirmar la Renegociación. Antes se debe realizar el pago inicial por: $'+number_format(saldo, 0, ',', '.'));
		dd_estado_convenio.selectedIndex = estado_convenio;
	}
}

function validate() {
	// valida que seleccione una deuda
	var vl_cod_tipo_deuda = document.getElementById('COD_TIPO_DEUDA_0')
	if (vl_cod_tipo_deuda) {
		if (vl_cod_tipo_deuda.value==0) {
			alert('Debe seleccionar una deuda antes de continuar');
			vl_cod_tipo_deuda.focus();
			return false;
		}
	}

	// valida que seleccione al menos una cuota
	var vl_tabla = document.getElementById('PP_RENEGOCIACION');
	var aTR = vl_tabla.getElementsByTagName("tr");
	var vl_una_checked = false;
	for (var i=0; i<aTR.length; i++) {
		var vl_row = get_num_rec_field(aTR[i].id);
		var vl_seleccion = document.getElementById('SELECCION_' + vl_row);
		vl_una_checked = vl_una_checked || vl_seleccion.checked;
	}
	if (!vl_una_checked) {
		alert('Debe seleccionar al menos una cuota a renegociar');
		return false;
	}

	return true;
}
function limpia_datos_deuda() {
	document.getElementById('COD_DEUDA_0').innerHTML = '';
	document.getElementById('COD_DEUDA_H_0').value = '';
	document.getElementById('NOM_CARTERA_0').innerHTML = '';
	document.getElementById('NOM_ESTADO_DEUDA_0').innerHTML = '';
	document.getElementById('CANT_CUOTA_DEUDA_0').innerHTML = '';
	
	// borra todos los tr
	var tabla = document.getElementById('PP_RENEGOCIACION');
	while (tabla.firstChild) {
	  tabla.removeChild(tabla.firstChild);
	}
}
function change_rut(ve_rut) {
	// deudor
	document.getElementById('NOM_DEUDOR_0').innerHTML = '';
	document.getElementById('DIG_VERIF_0').innerHTML = '';
	
	// deuda
	var vl_cod_tipo_deuda = document.getElementById('COD_TIPO_DEUDA_0');
	vl_cod_tipo_deuda.length = 0;
	limpia_datos_deuda();

	var ajax = nuevoAjax();
	ajax.open("GET", "ajax_change_rut.php?rut="+ve_rut.value, false);
	ajax.send(null);

	var resp = URLDecode(ajax.responseText);	
	var result = eval("(" + resp + ")");
	if (result.length==0) {
		alert('No inexistente o no tiene deudas para renegociar');
		ve_rut.value = '';
		ve_rut.focus();
		return false;
	}
	document.getElementById('NOM_DEUDOR_0').innerHTML = URLDecode(result[0]['NOM_DEUDOR']);
	document.getElementById('DIG_VERIF_0').innerHTML = result[0]['DIG_VERIF'];
	
	// completa el DD de deudas
	var vl_option = new Option('', 0); 
	vl_cod_tipo_deuda.appendChild(vl_option); 
	for (var i=0; i< result.length; i++) {
		vl_option = new Option(result[i]['COD_TIPO_DEUDA'], result[i]['COD_DEUDA']); 
		vl_cod_tipo_deuda.appendChild(vl_option); 
	}
}
function change_tipo_deuda(ve_cod_tipo_deuda) {
	limpia_datos_deuda();
	if (ve_cod_tipo_deuda.value==0)
		return;
	 
	var ajax = nuevoAjax();
	ajax.open("GET", "ajax_change_tipo_deuda.php?cod_deuda="+ve_cod_tipo_deuda.value, false);
	ajax.send(null);

	var resp = URLDecode(ajax.responseText);
	var result = eval("(" + resp + ")");

	document.getElementById('COD_DEUDA_0').innerHTML = result[0]['COD_DEUDA'];
	document.getElementById('COD_DEUDA_H_0').value = result[0]['COD_DEUDA'];
	document.getElementById('NOM_CARTERA_0').innerHTML = result[0]['NOM_CARTERA'];
	document.getElementById('NOM_ESTADO_DEUDA_0').innerHTML = result[0]['NOM_ESTADO_DEUDA'];
	document.getElementById('CANT_CUOTA_DEUDA_0').innerHTML = result[0]['CANT_CUOTA'];
	document.getElementById('SIMBOLO_0').innerHTML = result[0]['SIMBOLO'];

	var vl_tabla = document.getElementById('PP_RENEGOCIACION');
	for (var i=0; i< result.length; i++) {
		var vl_row = add_line('PP_RENEGOCIACION', 'renegociacion');
		document.getElementById('SELECCION_' + vl_row).checked = (result[i]['SELECCION']=='S');
		document.getElementById('ANO_COBRO_' + vl_row).innerHTML = result[i]['ANO_COBRO'];
		document.getElementById('FECHA_VENCTO_' + vl_row).innerHTML = result[i]['FECHA_VENCTO'];
		document.getElementById('SALDO_CUOTA_' + vl_row).innerHTML = number_format(result[i]['SALDO_CUOTA'], 4, ',', '.');
		document.getElementById('SALDO_MULTA_' + vl_row).innerHTML = number_format(result[i]['SALDO_MULTA'], 4, ',', '.');
		document.getElementById('TOTAL_SALDO_MONEDA_' + vl_row).innerHTML = number_format(result[i]['TOTAL_SALDO_MONEDA'], 4, ',', '.');
		document.getElementById('TOTAL_SALDO_PESOS_' + vl_row).innerHTML = number_format(result[i]['TOTAL_SALDO_PESOS'], 0, ',', '.');
		
		document.getElementById('COD_PLAN_PAGO_H_' + vl_row).value = result[i]['COD_PLAN_PAGO'];
		document.getElementById('SALDO_CUOTA_H_' + vl_row).value = result[i]['SALDO_CUOTA'];
		document.getElementById('SALDO_MULTA_H_' + vl_row).value = result[i]['SALDO_MULTA'];
		document.getElementById('TOTAL_SALDO_MONEDA_H_' + vl_row).value = result[i]['TOTAL_SALDO_MONEDA'];
		document.getElementById('TOTAL_SALDO_PESOS_H_' + vl_row).value = result[i]['TOTAL_SALDO_PESOS'];
	}
}
function change_seleccion(ve_seleccion) {
	var vl_tabla = document.getElementById('PP_RENEGOCIACION');
	var aTR = vl_tabla.getElementsByTagName("tr");
	var vl_suma_cuota = 0;
	var vl_suma_multa = 0;
	var vl_suma_total_saldo_moneda = 0;
	var vl_suma_total_saldo_pesos = 0;
	for (var i=0; i<aTR.length; i++) {
		var vl_row = get_num_rec_field(aTR[i].id);
		var vl_seleccion = document.getElementById('SELECCION_' + vl_row);
		if (vl_seleccion.checked) {
			var vl_cuota = to_num(get_value('SALDO_CUOTA_' + vl_row));
			var vl_multa = to_num(get_value('SALDO_MULTA_' + vl_row));
			var vl_total_saldo_moneda = to_num(get_value('TOTAL_SALDO_MONEDA_' + vl_row));
			var vl_total_saldo_pesos = to_num(get_value('TOTAL_SALDO_PESOS_' + vl_row));
		
			vl_suma_cuota += parseFloat(vl_cuota);
			vl_suma_multa += parseFloat(vl_multa);
			vl_suma_total_saldo_moneda += parseFloat(vl_total_saldo_moneda);
			vl_suma_total_saldo_pesos += parseFloat(vl_total_saldo_pesos);
		}
	}
	document.getElementById('SUMA_SALDO_CUOTA_0').innerHTML = number_format(vl_suma_cuota, 4, ',', '.');
	document.getElementById('SUMA_SALDO_MULTA_0').innerHTML = number_format(vl_suma_multa, 4, ',', '.');
	document.getElementById('SUMA_TOTAL_SALDO_MONEDA_0').innerHTML = number_format(vl_suma_total_saldo_moneda, 4, ',', '.');
	document.getElementById('SUMA_TOTAL_SALDO_PESOS_0').innerHTML = number_format(vl_suma_total_saldo_pesos, 0, ',', '.');
	
	// Actualiza montos a renegociar
	var vl_porc = document.getElementById('PORC_PAGO_SUGERIDO_H_0').value;
	var vl_pago_inicial = roundNumber(vl_suma_total_saldo_pesos * vl_porc/100, 0); 
	document.getElementById('PAGO_INICIAL_SUGERIDO_0').innerHTML = number_format(vl_pago_inicial, 0, ',', '.');
	document.getElementById('PAGO_INICIAL_SUGERIDO_H_0').value = vl_pago_inicial;
	document.getElementById('PAGO_INICIAL_0').value = vl_pago_inicial;
	change_pago_inicial(document.getElementById('PAGO_INICIAL_0'));
}
function change_pago_inicial(ve_pago_inicial) {
	var vl_suma_total_saldo_pesos = to_num(get_value('SUMA_TOTAL_SALDO_PESOS_0'));
	var vl_monto_renegociar = vl_suma_total_saldo_pesos - ve_pago_inicial.value;
	document.getElementById('MONTO_RENEGOCIAR_0').innerHTML = number_format(vl_monto_renegociar, 0, ',', '.');
	document.getElementById('MONTO_RENEGOCIAR_H_0').value = vl_monto_renegociar;
	var vl_valor_moneda = document.getElementById('VALOR_MONEDA_0').value;
	var vl_monto_renegociar_moneda = roundNumber(vl_monto_renegociar / vl_valor_moneda, 4); 
	document.getElementById('MONTO_RENEGOCIAR_MONEDA_0').innerHTML = number_format(vl_monto_renegociar_moneda, 4, ',', '.');
	document.getElementById('MONTO_RENEGOCIAR_MONEDA_H_0').value = vl_monto_renegociar_moneda;
	change_cant_cuota(document.getElementById('CANT_CUOTA_0'));
}
function change_cant_cuota(ve_cant_cuota) {
	var vl_cant = ve_cant_cuota.value;
	var vl_monto_renegociar_moneda = get_value('MONTO_RENEGOCIAR_MONEDA_H_0');
	var	vl_fecha_reneg = get_value('FECHA_RENEGOCIACION_0');
	var vl_ano_inicio = get_value('ANO_INICIO_COBRO_0');
	
	var ajax = nuevoAjax();
	ajax.open("GET", "ajax_calculo_cuota.php?monto_renegociar="+vl_monto_renegociar_moneda+"&cant_cuota="+vl_cant+"&fecha_reneg="+vl_fecha_reneg+"&ano_inicio="+vl_ano_inicio, false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);	
	var result = eval("(" + resp + ")");
	var vl_monto_renegociar_act = result[0]['MONTO_ACT'];
	var vl_cuota_anual_moneda = result[0]['MONTO_CUOTA'];
	
	var vl_valor_moneda = document.getElementById('VALOR_MONEDA_0').value;
	var vl_cuota_anual = roundNumber(vl_cuota_anual_moneda * vl_valor_moneda, 0); 

	document.getElementById('MONTO_RENEGOCIAR_ACT_0').innerHTML = number_format(vl_monto_renegociar_act, 4, ',', '.');
	document.getElementById('MONTO_RENEGOCIAR_ACT_H_0').value = vl_monto_renegociar_act;
	document.getElementById('CUOTA_ANUAL_MONEDA_0').innerHTML = number_format(vl_cuota_anual_moneda, 4, ',', '.');
	document.getElementById('CUOTA_ANUAL_MONEDA_H_0').value = vl_cuota_anual_moneda;
	document.getElementById('CUOTA_ANUAL_0').innerHTML = number_format(vl_cuota_anual, 0, ',', '.');
}