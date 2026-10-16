function cambia_deuda(ve_deuda){
	var tipo_deuda = ve_deuda.value;
	
	if(tipo_deuda == 'CR12'){
		document.getElementById('OPCION').style.display = '';
		document.getElementById('OPERACION').style.display = '';
		document.getElementById('OPCION_ABONOS_H_0').value = 'S';
		document.getElementById('OPCION_DESCTOS_H_0').value = 'N';
		document.getElementById('OPCION_ABONOS_0').checked = true;
	}
	else if(tipo_deuda == 'CRPI'){
		document.getElementById('OPCION').style.display = 'none';
		document.getElementById('OPERACION').style.display = 'none';
		document.getElementById('OPCION_ABONOS_H_0').value = 'S';
		document.getElementById('OPCION_DESCTOS_H_0').value = 'N';
	}
}

function cambia_opcion(ve_check,ve_param){
	borra_todos_abonos();
	if(ve_check.checked == true){
		if(ve_param == 'ABONO'){
			document.getElementById('OPCION_ABONOS_H_0').value = 'S';
			document.getElementById('OPCION_DESCTOS_H_0').value = 'N';
		}
		else if(ve_param == 'DESCTO'){
			document.getElementById('OPCION_ABONOS_H_0').value = 'N';
			document.getElementById('OPCION_DESCTOS_H_0').value = 'S';
		}
	}
}

function validate() {
	/* valida que haya datos en la tabla DETALLE ABONO DESCUENTOS */
	var aTR  = get_TR('DETALLE_ABONO');
	var vl_estado = document.getElementById('COD_ESTADO_RENDICION_0');
	var K_ANULADA = 3;
	
	if(aTR.length == 0) {
		if (!vl_estado || vl_estado.value != K_ANULADA) {
			alert('Debe ingresar un rango de fechas que contenga abonos a distribuir (use el botón "Buscar" para cargar los abonos)');
			return false;
		}
	}
	var vl_cod_estado_rendicion	= document.getElementById('COD_ESTADO_RENDICION_0').value;
	var vl_cod_cuenta_corriente	= document.getElementById('COD_CUENTA_CORRIENTE_0').value;
	var vl_rendicion_aprobada = 2;
	if (vl_cod_estado_rendicion == vl_rendicion_aprobada && vl_cod_cuenta_corriente == ''){
		alert('Debe Seleccionar la Cuenta Corriente');
		return false;
	}
	return true;	
}
function f_get_abonos() {
	var fecha_inicio  	= document.getElementById('FECHA_INICIO_0').value;
	var fecha_termino 	= document.getElementById('FECHA_TERMINO_0').value;
	var tipo_deuda		= document.getElementById('COD_TIPO_DEUDA_0').value;
	var opcion_abonos	= document.getElementById('OPCION_ABONOS_H_0').value;
	var opcion_desctos	= document.getElementById('OPCION_DESCTOS_H_0').value;
	
	/* Valida que existan dos fechas y tipo deuda antes de buscar */
	if(fecha_inicio == '' || fecha_termino == '' || tipo_deuda == '') {
		alert('Debe ingresar Fecha Inicio, Fecha Termino y Tipo Deuda antes de continuar');
		return false;
	}
	else { 
		/* valida que fecha_inicio no sea menor a fecha_termino */
		var fecha1 = fecha_inicio.split('/');
		var fecha2 = fecha_termino.split('/');
		var ano1 = fecha1[2];
		var mes1 = fecha1[1];
		var dia1 = fecha1[0];
		var ano2 = fecha2[2];
		var mes2 = fecha2[1];
		var dia2 = fecha2[0];
		var fecha_ini = new Date(ano1,mes1,dia1);
		var fecha_ter = new Date(ano2,mes2,dia2);
		
		if(fecha_ini > fecha_ter) {
			alert('Fecha Inicio es MENOR que Fecha Termino');
			return false;
		}	
	
		borra_todos_abonos();
		
		var tabla = document.getElementById('DETALLE_ABONO');
		
		fecha_inicio = URLEncode(fecha_inicio);
		fecha_termino = URLEncode(fecha_termino);
		var ajax = nuevoAjax();
		ajax.open("GET", "ajax_obtiene_abonos.php?fecha_inicio="+fecha_inicio+"&fecha_termino="+fecha_termino+"&tipo_deuda="+tipo_deuda+"&opcion_abonos="+opcion_abonos+"&opcion_desctos="+opcion_desctos, false);
		ajax.send(null);
		var resp = URLDecode(ajax.responseText);
		var result = eval("(" + resp + ")");
		if (result.length==0) {
			alert('No se han encontrado datos para el rango de fecha ingresado');
			return false;
		}
		for (var i=0; i< result.length; i++) {
			var vl_row = add_line('DETALLE_ABONO', 'rendicion_ley');
			document.getElementById('TIPO_DATO_' + vl_row).innerHTML 			= result[i]['TIPO_DATO'];
			document.getElementById('CODIGO_' + vl_row).innerHTML 				= result[i]['CODIGO'];
			document.getElementById('FECHA_' + vl_row).innerHTML 				= result[i]['FECHA'];
			document.getElementById('RUT_' + vl_row).innerHTML 					= number_format(result[i]['RUT'],0,',','.');
			document.getElementById('DIG_VERIF_' + vl_row).innerHTML 			= result[i]['DIG_VERIF'];
			document.getElementById('NOM_DEUDOR_' + vl_row).innerHTML 			= URLDecode(result[i]['NOM_DEUDOR']);
			document.getElementById('COD_DEUDA_' + vl_row).innerHTML 			= result[i]['COD_DEUDA'];
			document.getElementById('MONTO_MONEDA_' + vl_row).innerHTML 		= number_format(result[i]['MONTO_MONEDA'], 4, ',', '.');
			document.getElementById('MONTO_PESOS_' + vl_row).innerHTML 			= number_format(result[i]['MONTO_PESOS'], 0, ',', '.');
			document.getElementById('PORC_' + vl_row).innerHTML 				= result[i]['PORC'];
			document.getElementById('NOM_UNIVERSIDAD_' + vl_row).innerHTML 		= result[i]['NOM_UNIVERSIDAD'];
			document.getElementById('MONTO_RENDIDO_MONEDA_' + vl_row).innerHTML = number_format(result[i]['MONTO_RENDIDO_MONEDA'], 4, ',', '.');
			document.getElementById('MONTO_RENDIDO_PESOS_' + vl_row).innerHTML 	= number_format(result[i]['MONTO_RENDIDO_PESOS'], 0, ',', '.');
			
			document.getElementById('TIPO_DATO_H_' + vl_row).value 				= result[i]['TIPO_DATO_H'];
			document.getElementById('CODIGO_H_' + vl_row).value	 				= result[i]['CODIGO_H'];
			document.getElementById('MONTO_MONEDA_H_' + vl_row).value	 		= result[i]['MONTO_MONEDA_H'];
			document.getElementById('MONTO_PESOS_H_' + vl_row).value	 		= result[i]['MONTO_PESOS_H'];
			document.getElementById('PORC_H_' + vl_row).value	 				= result[i]['PORC_H'];
			document.getElementById('COD_UNIVERSIDAD_H_' + vl_row).value		= result[i]['COD_UNIVERSIDAD_H'];
			document.getElementById('MONTO_RENDIDO_MONEDA_H_' + vl_row).value	= result[i]['MONTO_RENDIDO_MONEDA_H'];
			document.getElementById('MONTO_RENDIDO_PESOS_H_' + vl_row).value	= result[i]['MONTO_RENDIDO_PESOS_H'];
			
		}
	}
}
function borra_todos_abonos() {
	
	// Borra todos los registros
	var aTR = get_TR('DETALLE_ABONO');
	
	for (var i=0; i < aTR.length; i++) {
		var rec_tr = get_num_rec_field(aTR[i].id);
		del_line('DETALLE_ABONO_'+rec_tr, 'rendicion');	
	}
}
