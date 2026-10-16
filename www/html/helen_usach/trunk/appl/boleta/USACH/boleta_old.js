function checked_radio_button(cod_plan){
	var monto_pago_pesos;
	if(cod_plan != null){
		set_record(cod_plan);
		monto_pago_pesos = '';
	}
	else{
		monto_pago_pesos= document.getElementById('D_MONTO_PESOS_0').value;
	}
	
	record 					= document.getElementById('D_RECORD_COD_PLAN_PAGO_H_0').value;
	var cod_deuda 			= document.getElementById('COD_DEUDA_0').innerHTML;
	var cod_plan_pago 		= document.getElementById('COD_PLAN_PAGO_H_' + record).value;
	var fecha_vence_plan	= document.getElementById('FECHA_VENCTO_' + record).innerHTML;
	var ano_cobro			= document.getElementById('ANO_COBRO_' + record).innerHTML;
	var nro_cuota			= document.getElementById('NRO_CUOTA_' + record).innerHTML;
	var vl_cod_tipo_descuento = document.getElementById('D_COD_TIPO_DESCUENTO_0').value;

	document.getElementById('B_COD_PLAN_PAGO_0').value			= cod_plan_pago;
	document.getElementById('D_MONTO_PESOS_0').value 			= monto_pago_pesos;
	document.getElementById('D_FECHA_VENCE_PLAN_H_0').value 	= fecha_vence_plan;
	document.getElementById('D_ANO_COBRO_H_0').value 			= ano_cobro;
	
	document.getElementById('ANO_COBRO_PP_0').innerHTML = ano_cobro;
	document.getElementById('NRO_CUOTA_PP_0').innerHTML = nro_cuota;
	document.getElementById('FECHA_VENCTO_PP_0').innerHTML = fecha_vence_plan;
	
	
	var ajax = nuevoAjax();
	ajax.open("GET", "ajax_obtiene_saldos.php?cod_plan_pago="+cod_plan_pago+"&cod_deuda="+cod_deuda+"&cod_tipo_descuento="+vl_cod_tipo_descuento+"&monto_pago_pesos="+monto_pago_pesos, false);
	ajax.send(null);
	
    var resp = 	ajax.responseText.split('|');
    if (ajax.readyState==4){		
		//SUBCUOTA
		document.getElementById('D_SALDO_CAPITAL_0').innerHTML	= document.getElementById('SALDO_CAPITAL_' + record).innerHTML;
		document.getElementById('D_SALDO_INTERES_0').innerHTML	= document.getElementById('SALDO_INTERES_' + record).innerHTML;
		document.getElementById('D_SALDO_MULTA_0').innerHTML	= document.getElementById('SALDO_MULTA_' + record).innerHTML;
		document.getElementById('D_SALDO_MONEDA_0').innerHTML	= document.getElementById('SALDO_MONEDA_' + record).innerHTML;
		document.getElementById('D_SALDO_PESOS_0').innerHTML	= document.getElementById('SALDO_PESOS_' + record).innerHTML;
		document.getElementById('D_MONTO_CAPITAL_0').innerHTML	= number_format(resp[0], 4, ',', '.');
		document.getElementById('D_MONTO_CAPITAL_H_0').value	= resp[0];
		document.getElementById('D_MONTO_INTERES_0').innerHTML	= number_format(resp[1], 4, ',', '.');
		document.getElementById('D_MONTO_INTERES_H_0').value	= resp[1];
		document.getElementById('D_MONTO_MULTA_0').innerHTML	= number_format(resp[2], 4, ',', '.');
		document.getElementById('D_MONTO_MULTA_H_0').value		= resp[2];
		document.getElementById('D_MONTO_MONEDA_0').innerHTML	= number_format(parseFloat(resp[0]) + parseFloat(resp[1]) + parseFloat(resp[2]), 4, ',', '.');
		document.getElementById('D_MONTO_MONEDA_H_0').value		= parseFloat(resp[0]) + parseFloat(resp[1]) + parseFloat(resp[2]);
		document.getElementById('D_MONTO_PESOS_0').value		= number_format(resp[3], 0, ',','');
		document.getElementById('D_MONTO_PESOS_H_0').value		= resp[3];
		document.getElementById('D_DSCTO_CAPITAL_0').innerHTML	= number_format(resp[4], 4, ',', '.');
		document.getElementById('D_DSCTO_CAPITAL_H_0').value	= resp[4];
		document.getElementById('D_DSCTO_INTERES_0').innerHTML	= number_format(resp[5], 4, ',', '.');
		document.getElementById('D_DSCTO_INTERES_H_0').value	= resp[5];
		document.getElementById('D_DSCTO_MULTA_0').innerHTML	= number_format(resp[6], 4, ',', '.');
		document.getElementById('D_DSCTO_MULTA_H_0').value		= resp[6];
		document.getElementById('D_DSCTO_MONEDA_0').innerHTML	= number_format(parseFloat(resp[4]) + parseFloat(resp[5]) + parseFloat(resp[6]), 4, ',', '.');
		document.getElementById('D_DSCTO_MONEDA_H_0').value		= parseFloat(resp[4]) + parseFloat(resp[5]) + parseFloat(resp[6]);
		document.getElementById('D_DSCTO_PESOS_0').innerHTML	= number_format(resp[7], 0, ',', '.');
		document.getElementById('D_DSCTO_PESOS_H_0').value		= resp[7];
		
		set_sub_cuotas();		
	}
	
	var tipo_deuda = document.getElementById('COD_TIPO_DEUDA_0');
	tipo_deuda = tipo_deuda.options[tipo_deuda.selectedIndex].label;
	
	if(tipo_deuda == 'CS' && ano_cobro == 9999){
		document.getElementById('D_COD_TIPO_DESCUENTO_0').disabled = false;
	}else{
		document.getElementById('D_COD_TIPO_DESCUENTO_0').selectedIndex=0;
		document.getElementById('D_COD_TIPO_DESCUENTO_0').disabled = true;
	}
}

function get_datos_deudor(){
	var rut_deudor = document.getElementById('RUT_0');
	set_null();
	if(rut_deudor.value == ''){
		document.getElementById('D_COD_TIPO_DESCUENTO_0').disabled = false;
		rut_deudor.focus();
	}else{
		document.getElementById('D_COD_TIPO_DESCUENTO_0').selectedIndex=0;
		document.getElementById('D_COD_TIPO_DESCUENTO_0').disabled = true;
		var ajax = nuevoAjax();
		ajax.open("GET", "ajax_get_datos.php?rut_deudor="+rut_deudor.value, false);	
		ajax.send(null);	
		var resp = ajax.responseText.split('|');
		if(resp[0]== 0){
			alert('Rut inexistente, favor ingrese nuevamente.');
			rut_deudor.value = '';
			set_null();
		}else{
			var ajax = nuevoAjax();
			ajax.open("GET", "ajax_posee_deudas.php?rut_deudor="+rut_deudor.value, false);	
			ajax.send(null);	
			var resp = ajax.responseText.split('|');
			
			if(resp[0] == 0){
				alert('El rut ingresado no posee deudas en estado "Exigible" o "Cobranza Normal"');
				rut_deudor.value = '';
				set_null();
			}
			else{
				document.getElementById('DIG_VERIF_0').innerHTML 		= resp[1];
				document.getElementById('DIG_VERIF_0').value 			= resp[1];
				document.getElementById('NOMBRE_0').innerHTML 			= resp[2];
				document.getElementById('NOMBRE_0').value 				= resp[2];
				//document.getElementById('COD_TIPO_DEUDA_0').selectedIndex=1;
				alert('Ahora Seleccione un Tipo de Deuda para Continuar');
				set_null_totales();
			}
		}
	}
}

function get_deuda_morosa(){
	var cod_deuda = document.getElementById('COD_DEUDA_H_0');
	var ajax = nuevoAjax();
	ajax.open("GET", "ajax_get_morosa.php?cod_deuda="+cod_deuda.value, false);	
	ajax.send(null);	
	var resp = ajax.responseText.split('|');
	
	if(resp != ''){
		document.getElementById('TR_MOROSA').style.display = '';
		document.getElementById('CUOTA_MOROSA_0').innerHTML=resp;
	}
}

function get_datos_deuda(){
	var cod_deuda = document.getElementById('COD_DEUDA_0').innerHTML;
	var tipo_deuda = document.getElementById('COD_TIPO_DEUDA_0');
	tipo_deuda = tipo_deuda.options[tipo_deuda.selectedIndex].label;
	if(tipo_deuda == ''){
		document.getElementById('D_COD_TIPO_DESCUENTO_0').selectedIndex=0;
		document.getElementById('D_COD_TIPO_DESCUENTO_0').disabled = true;
		alert('Debe Seleccionar un Tipo Deuda para continuar.');
	}else{
		var tabla = document.getElementById('DETALLE_PLAN_PAGO');
		// borra todos los tr
		while (tabla.firstChild) {
		  tabla.removeChild(tabla.firstChild);
		}
		var ajax = nuevoAjax();
		ajax.open("GET", "ajax_get_deudas.php?cod_deuda="+cod_deuda, false);
		ajax.send(null);
		var resp = ajax.responseText;
		
		// Copia los TR a la tabla correspondiente, 
		// este codigo se copio desde general.js -> add_line
		var table_aux = document.createElement("TABLE"); 
		table_aux.innerHTML = resp;
		var children = table_aux.childNodes;
		for (var i=0; i < children.length; i++) {
			if (children[i].nodeName=='TBODY') {
			  	var children2 = children[i].childNodes;
			  	for (j=0; j < children2.length; j++) {
					if (children2[j].nodeName=='TR') {
						var tr_contenido = children2[j].innerHTML;
							
						var tbody = null; 
						var child_tabla = tabla.childNodes;
						for (k=0; k < child_tabla.length; k++)
							if (child_tabla[k].nodeName=='TBODY') {
								tbody = child_tabla[k];
								break;
							}
						if (! tbody) {
							tbody = document.createElement("TBODY"); 
							tabla.appendChild(tbody);
						}		
						tbody.appendChild(children2[j]);
					}
				}
			}
		}						
	}	
}

function get_cod_deuda(){
	var cod_deuda = document.getElementById('COD_TIPO_DEUDA_0');
	var rut = document.getElementById('RUT_0');
	
	if(cod_deuda.value != ''){
		var ajax = nuevoAjax();
		ajax.open("GET", "ajax_valida_cartera.php?rut_deudor="+rut.value+"&cod_deuda="+cod_deuda.value, false);	
		ajax.send(null);	
		var resp = ajax.responseText.split('|');
		if (resp == 'CARTERA_DIFERENTE'){
			alert('No corresponde a la cartera por defecto. Deudor RUT: ' + rut.value + '.');
			rut_deudor.value = '';
			set_null();
		}else{
			var ajax = nuevoAjax();
			ajax.open("GET", "ajax_get_cod_deuda.php?cod_deuda="+cod_deuda.value, false);		 						   	
			ajax.send(null);
			var resp = ajax.responseText.split('|');
			
			if(resp == 0){
				document.getElementById('COD_DEUDA_0').innerHTML 			= null;
				document.getElementById('SIMBOLO_0').innerHTML 				= null;
				document.getElementById('B_TASA_INTERES_PENAL_H_0').value	= null;
			}
			else{
				document.getElementById('COD_DEUDA_0').innerHTML			= resp[0];
				document.getElementById('COD_DEUDA_H_0').value 				= resp[0];
				document.getElementById('SIMBOLO_0').innerHTML 				= resp[1];
				document.getElementById('B_TASA_INTERES_PENAL_H_0').value	= resp[2];
			}
		}
		
	}
	else{
		set_null();							
	}
}

function set_null(){
	//document.getElementById('DIG_VERIF_0').innerHTML			= null;
	document.getElementById('NOMBRE_0').innerHTML				= null;
	document.getElementById('COD_DEUDA_0').innerHTML			= null;
	document.getElementById('SIMBOLO_0').innerHTML				= null;
	document.getElementById('B_TASA_INTERES_PENAL_H_0').value	= null;
	
	document.getElementById('ANO_COBRO_PP_0').innerHTML			= null;
	document.getElementById('NRO_CUOTA_PP_0').innerHTML			= null;
	document.getElementById('FECHA_VENCTO_PP_0').innerHTML		= null;
	document.getElementById('D_COD_TIPO_DESCUENTO_0').disabled	= false;
	document.getElementById('TR_MOROSA').style.display = 'none';
	
	var tabla = document.getElementById('DETALLE_PLAN_PAGO');
	// borra todos los tr
	while (tabla.firstChild) {
		tabla.removeChild(tabla.firstChild);
	}
	set_null_totales();
}

function set_null_totales(){	
	document.getElementById('D_SALDO_CAPITAL_0').innerHTML	= null;
	document.getElementById('D_SALDO_INTERES_0').innerHTML	= null;
	document.getElementById('D_SALDO_MULTA_0').innerHTML	= null;
	document.getElementById('D_SALDO_MONEDA_0').innerHTML	= null;
	document.getElementById('D_SALDO_PESOS_0').innerHTML	= null;

	document.getElementById('D_MONTO_CAPITAL_0').innerHTML	= null;
	document.getElementById('D_MONTO_CAPITAL_H_0').value	= null;
	document.getElementById('D_MONTO_INTERES_0').innerHTML	= null;
	document.getElementById('D_MONTO_INTERES_H_0').value	= null;
	document.getElementById('D_MONTO_MULTA_0').innerHTML	= null;
	document.getElementById('D_MONTO_MULTA_H_0').value		= null;
	document.getElementById('D_MONTO_MONEDA_0').innerHTML	= null;
	document.getElementById('D_MONTO_MONEDA_H_0').value		= null;
	document.getElementById('D_MONTO_PESOS_0').value		= null;
	document.getElementById('D_MONTO_PESOS_H_0').value		= null;
	document.getElementById('D_DSCTO_CAPITAL_0').innerHTML	= null;
	document.getElementById('D_DSCTO_CAPITAL_H_0').value	= null;
	document.getElementById('D_DSCTO_INTERES_0').innerHTML	= null;
	document.getElementById('D_DSCTO_INTERES_H_0').value	= null;
	document.getElementById('D_DSCTO_MULTA_0').innerHTML	= null;
	document.getElementById('D_DSCTO_MULTA_H_0').value		= null;
	document.getElementById('D_DSCTO_MONEDA_0').innerHTML	= null;
	document.getElementById('D_DSCTO_MONEDA_H_0').value		= null;
	document.getElementById('D_DSCTO_PESOS_0').innerHTML	= null;
	document.getElementById('D_DSCTO_PESOS_H_0').value		= null;
}

function set_sub_cuotas(){
	var total = parseFloat(document.getElementById('D_MONTO_CAPITAL_0').innerHTML) + parseFloat(document.getElementById('D_MONTO_INTERES_0').innerHTML) + parseFloat(document.getElementById('D_MONTO_MULTA_0').innerHTML);

	var aTR = get_TR('TAB_SUBCUOTA_DETALLE'); 
	for (var i=0; i < aTR.length ; i++) {
		var record = get_num_rec_field(aTR[i].id);	
		del_line('TAB_SUBCUOTA_DETALLE_' + record, 'boleta');
	}
	var vl_row = add_line("TAB_SUBCUOTA_DETALLE", 'boleta');
	
	insertar_filas 	= 1;
	fecha_vencto 	= document.getElementById('FECHA_PRIMERA_CUOTA_H_0').value;
	
	document.getElementById('SC_FECHA_VENCTO_' + vl_row).innerHTML 		= fecha_vencto;
	document.getElementById('SC_FECHA_VENCTO_H_' + vl_row).value 		= fecha_vencto;
	document.getElementById('SC_NRO_SUB_BOLETA_' + vl_row).innerHTML	= insertar_filas;
	document.getElementById('SC_NRO_SUB_BOLETA_H_' + vl_row).value		= insertar_filas;
	document.getElementById('SC_MONTO_CAPITAL_'+vl_row).innerHTML		= document.getElementById('D_MONTO_CAPITAL_0').innerHTML;
	document.getElementById('SC_MONTO_CAPITAL_H_'+vl_row).value			= document.getElementById('D_MONTO_CAPITAL_H_0').value;
	document.getElementById('SC_MONTO_INTERES_'+vl_row).innerHTML		= document.getElementById('D_MONTO_INTERES_0').innerHTML;
	document.getElementById('SC_MONTO_INTERES_H_'+vl_row).value			= document.getElementById('D_MONTO_INTERES_H_0').value;
	document.getElementById('SC_MONTO_MULTA_'+vl_row).innerHTML			= document.getElementById('D_MONTO_MULTA_0').innerHTML;
	document.getElementById('SC_MONTO_MULTA_H_'+vl_row).value			= document.getElementById('D_MONTO_MULTA_H_0').value;
	document.getElementById('SC_TOTAL_'+vl_row).innerHTML 				= roundNumber(total, 4);
	setRow();
}

function setRow(){
	var descuento = document.getElementById('D_DSCTO_CAPITAL_H_0').value;
	var cant_sub_cuota = document.getElementById('CANT_0').value
	if(cant_sub_cuota != 1 && descuento > 0){
		alert('No puede modificar la cantidad de cuotas');
		document.getElementById('CANT_0').focus();
		document.getElementById('CANT_0').value = 1;	
		return;
	}
		
	var indiceFilaFormulario=0;
	insertar_filas 	= document.getElementById('CANT_0').value;
	total_filas 	= document.getElementById("TAB_SUBCUOTA_DETALLE").rows.length

	if (insertar_filas <= 0){
		alert("La cantidad de Subcuotas debe ser superior a 0");
		document.getElementById('CANT_0').focus();
		document.getElementById('CANT_0').value = 1;
	}
	
	var aTR = get_TR('TAB_SUBCUOTA_DETALLE');
	for (var i=0; i < aTR.length ; i++) {
		var record = get_num_rec_field(aTR[i].id);	
		del_line('TAB_SUBCUOTA_DETALLE_' + record, 'boleta');
	}
	var fecha_vencto 		= document.getElementById('FECHA_PRIMERA_CUOTA_H_0').value;
	var monto_capital		= document.getElementById('D_MONTO_CAPITAL_H_0').value;
	var monto_interes		= document.getElementById('D_MONTO_INTERES_H_0').value;
	var monto_multa			= document.getElementById('D_MONTO_MULTA_H_0').value;
	var descuento_capital	= document.getElementById('D_DSCTO_CAPITAL_H_0').value;
	var descuento_interes	= document.getElementById('D_DSCTO_INTERES_H_0').value;

	var vl_fecha_vence_plan = document.getElementById('D_FECHA_VENCE_PLAN_H_0').value;
	var vl_fecha_hoy = document.getElementById('FECHA_HOY_H_0').value;
	
	// para comprar las fechas las lleva a formato yyymmdd
	var vl_parte_fecha = vl_fecha_vence_plan.split('/');		
	var vl_fecha_vence_plan2 = vl_parte_fecha[2] + vl_parte_fecha[1] + vl_parte_fecha[0]; 
	vl_parte_fecha = vl_fecha_hoy.split('/');		
	var vl_fecha_hoy2 = vl_parte_fecha[2] + vl_parte_fecha[1] + vl_parte_fecha[0]; 
	if (vl_fecha_vence_plan2 > vl_fecha_hoy2) { 
		// no ha vencido => las boletas se haran con la fecha de vencimiento de la cuota
		
		// para la cuota futura que vence en el año 9999
		// se deja como fecha de vencimiento el 31/12/ del año actual
		vl_parte_fecha = vl_fecha_vence_plan.split('/');		
		if (vl_parte_fecha[2] == 9999) {
			vl_parte_fecha = vl_fecha_hoy.split('/');
			vl_fecha_vence_plan = '31/12/' + vl_parte_fecha[2];
		}
				
		var resp = new Array();
		for (i=0; i < insertar_filas; i++)
			resp[resp.length] = vl_fecha_vence_plan
	}
	else {
		var ajax = nuevoAjax();
		ajax.open("GET", "ajax_get_fechas_sb.php?fecha_vencto="+fecha_vencto+"&cuotas="+insertar_filas, false);		 						   	
		ajax.send(null);	
		var resp = 	ajax.responseText.split('|');
	}
	var nro_fila = 0; 
	
	var vl_fecha_pago;
	var vl_multa;
	var vl_fecha_vence_plan = document.getElementById('D_FECHA_VENCE_PLAN_H_0').value;
	var vl_tasa_int_penal	= document.getElementById('B_TASA_INTERES_PENAL_H_0').value;
	var vl_ano_plan			= document.getElementById('D_ANO_COBRO_H_0').value;
	var vl_fecha_hoy		= document.getElementById('FECHA_HOY_H_0').value;
	
	var vl_k_a_pagar 		= monto_capital;
	var vl_in_a_pagar 		= monto_interes - descuento_interes;
	vl_in_a_pagar 			= roundNumber(vl_in_a_pagar,4);
	var vl_k_sub_boleta 	= (vl_k_a_pagar / insertar_filas);
	vl_k_sub_boleta 		= roundNumber(vl_k_sub_boleta,4);
	var vl_in_sub_boleta 	= (vl_in_a_pagar / insertar_filas);
	vl_in_sub_boleta 		= roundNumber(vl_in_sub_boleta,4);
	
	var vl_suma_capital	= 0;
	var vl_suma_interes	= 0;
	var vl_suma_multa 	= 0;
	var vl_suma_total 	= 0;
	
	for (i=0; i < insertar_filas; i++) {
		nro_fila = nro_fila + 1;
		var vl_row = add_line("TAB_SUBCUOTA_DETALLE", 'boleta');
		vl_fecha_pago = resp[nro_fila-1];
		if (i == (insertar_filas - 1)){
	    	vl_k_sub_boleta    	= vl_k_a_pagar	- ((insertar_filas - 1) * vl_k_sub_boleta )
			vl_in_sub_boleta 	= vl_in_a_pagar - ((insertar_filas - 1) * vl_in_sub_boleta)
		}

		f1 = vl_fecha_vence_plan;
		f2 = vl_fecha_pago;

		aF1 = f1.split("/");
		aF2 = f2.split("/");
		// para MULTA
	   	if (aF1[2] + aF1[1] + aF1[0]  > aF2[2] + aF2[1] + aF2[0]){
			vl_multa = 0;
			numMeses = 0;
		}
	    else {// cuota vencida
	    	//diferencia de meses en fechas
			numMeses = parseFloat(aF2[2])*12 + parseFloat(aF2[1]) - (parseFloat(aF1[2])*12 + parseFloat(aF1[1]));
			if (aF2[0]<aF1[0]){
				numMeses = parseFloat(numMeses);
			}
			vl_dif_meses = numMeses;
			vl_multa = 0;
			vl_multa = roundNumber(0,4);
		}
	   	vl_total = parseFloat(vl_k_sub_boleta) + parseFloat(vl_in_sub_boleta) + parseFloat(vl_multa);
		vl_total = roundNumber(vl_total,4);
		document.getElementById('SC_NRO_SUB_BOLETA_' + vl_row).innerHTML 	= nro_fila;
		document.getElementById('SC_NRO_SUB_BOLETA_H_' + vl_row).value	 	= nro_fila;
		document.getElementById('SC_FECHA_VENCTO_' + vl_row).innerHTML 		= vl_fecha_vence_plan//resp[nro_fila-1];
		document.getElementById('SC_FECHA_VENCTO_H_' + vl_row).value	 	= vl_fecha_vence_plan//resp[nro_fila-1];
		document.getElementById('SC_MONTO_CAPITAL_' + vl_row).innerHTML 	= number_format(vl_k_sub_boleta, 4, ',', '.');
		document.getElementById('SC_MONTO_CAPITAL_H_' + vl_row).value		= vl_k_sub_boleta;
		document.getElementById('MONTO_CAPITAL_C_' + vl_row).value			= vl_k_sub_boleta;
		vl_suma_capital = vl_suma_capital + parseFloat(vl_k_sub_boleta);
		document.getElementById('SC_MONTO_INTERES_' + vl_row).innerHTML 	= number_format(vl_in_sub_boleta, 4, ',', '.');
		document.getElementById('SC_MONTO_INTERES_H_' + vl_row).value	 	= vl_in_sub_boleta;
		document.getElementById('MONTO_INTERES_C_' + vl_row).value	 		= vl_in_sub_boleta;
		vl_suma_interes = vl_suma_interes + parseFloat(vl_in_sub_boleta);
		document.getElementById('SC_MONTO_MULTA_' + vl_row).innerHTML 		= number_format(0, 4, ',', '.');;//number_format(vl_multa, 4, ',', '.');
		document.getElementById('SC_MONTO_MULTA_H_' + vl_row).value 		= 0;
		document.getElementById('MONTO_MULTA_C_' + vl_row).value 			= 0;
		vl_suma_multa = vl_suma_multa + parseFloat(vl_multa);
		
		document.getElementById('SC_TOTAL_' + vl_row).innerHTML 			= number_format(vl_total, 4, ',', '.');
		document.getElementById('TOTAL_C_' + vl_row).innerHTML 				= vl_total;
		vl_suma_total = vl_suma_total + parseFloat(vl_total);
		document.getElementById('SC_FECHA_PAGO_' + vl_row).innerHTML 		= null;
		document.getElementById('SC_FECHA_PAGO_H_' + vl_row).value 			= null;

		document.getElementById('SUM_MONTO_CAPITAL_C_0').innerHTML 			= number_format(vl_suma_capital, 4, ',', '.');
		document.getElementById('SUM_MONTO_CAPITAL_C_H_0').value 			= vl_suma_capital;
		document.getElementById('SUM_MONTO_INTERES_C_0').innerHTML 			= number_format(vl_suma_interes, 4, ',', '.');
		document.getElementById('SUM_MONTO_INTERES_C_H_0').value 			= vl_suma_interes;
		document.getElementById('SUM_MONTO_MULTA_C_0').innerHTML 			= number_format(vl_suma_multa, 4, ',', '.');
		document.getElementById('SUM_MONTO_MULTA_C_H_0').value 				= vl_suma_multa;
		document.getElementById('SUM_TOTAL_C_0').innerHTML 					= number_format(vl_suma_total, 4, ',', '.');
		document.getElementById('SUM_TOTAL_C_H_0').value 					= vl_suma_total;
	}
}
function calcula_total_utm() {	
	var tipo_deuda = document.getElementById('COD_TIPO_DEUDA_0');
	var cod_tipo_deuda = tipo_deuda.options[tipo_deuda.selectedIndex].label;
	var ajax = nuevoAjax();
	ajax.open("GET", "ajax_get_valor_moneda.php?cod_tipo_deuda="+cod_tipo_deuda, false);
	ajax.send(null);
	var resp = 	URLDecode(ajax.responseText);
	return resp;
}
function set_record(cod_plan){
	document.getElementById('D_RECORD_COD_PLAN_PAGO_H_0').value = get_num_rec_field(cod_plan.id);
	
}
function get_max_subcuota(){
	var ajax = nuevoAjax();
	ajax.open("GET", "ajax_get_max_subcuota.php", false);		 						   	
	ajax.send(null);	
	var resp = 	URLDecode(ajax.responseText);
	cantidad_existente = document.getElementById('CANT_0').value;
	
	if (parseFloat(resp) < parseFloat(cantidad_existente)){
		aTR = get_TR('TAB_SUBCUOTA_DETALLE');
		document.getElementById('CANT_0').value = aTR.length;
		alert("Las cuotas no pueden ser mayor a lo permitido. ("+resp+")");
		document.getElementById('CANT_0').focus();
		return false;
	}
	setRow();
}
function division_tipo_otro(){
	var insertar_filas 	= document.getElementById('CANT_0').value;
	var aTR = get_TR('TAB_SUBCUOTA_DETALLE');
	var total_insertado	= 0;
	var vl_suma_total  	= 0;
	for (var i=0; i < aTR.length ; i++) {
		var record = get_num_rec_field(aTR[i].id);
		var vl_monto_capital 	= document.getElementById('SC_MONTO_CAPITAL_H_' + record).value;
		var vl_monto_interes	= document.getElementById('SC_MONTO_INTERES_H_' + record).value;
		var vl_monto_multa 		= document.getElementById('SC_MONTO_MULTA_H_' + record).value;

		var vl_suma_linea = parseFloat(vl_monto_capital) + parseFloat(vl_monto_interes) + parseFloat(vl_monto_multa);
		
		document.getElementById('SC_TOTAL_' + record).innerHTML = number_format(vl_suma_linea, 4, ',', '.');
		document.getElementById('TOTAL_C_' + record).innerHTML = number_format(vl_suma_linea, 4, ',', '.');
		document.getElementById('TOTAL_C_H_' + record).value 	= vl_suma_linea;
		
		vl_suma_total = parseFloat(vl_suma_total) + parseFloat(vl_suma_linea);			
	}
	document.getElementById('SUM_TOTAL_C_0').innerHTML = number_format(vl_suma_total, 4, ',', '.');	
}
function mostrarOcultar_det_pp(ve_estado_deuda) {
	var k_deuda_no_exigible = 1;
	var table_det_pp = document.getElementById('table_det_pp');
	
	if (to_num(ve_estado_deuda.value)==k_deuda_no_exigible){
		table_det_pp.style.display = 'none'; 
	}
	else{
		table_det_pp.style.display = '';
	}
}