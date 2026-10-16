function replaceChars(entry) {
	var out = ".";
	var add = "";
	var temp = "" + entry;
	while (temp.indexOf(out) > -1) {
		pos = temp.indexOf(out);
		temp = ""
				+ (temp.substring(0, pos) + add + temp.substring(
						(pos + out.length), temp.length));
	}
	return temp;
}

function style(ve_table){
	var aTR = get_TR(ve_table);
	//alert(aTR);
	for (var i=0; i < aTR.length ; i++) {
		var record = get_num_rec_field(aTR[i].id);
		if (i%2==0)
			document.getElementById(ve_table+'_' + record).className="claro";
		else
			document.getElementById(ve_table+'_' + record).className="oscuro";
	}
}

function del_table(vl_table) {
	//TABLE_TOTAL_PAGO
	var vl_tabla_delete = document.getElementById(vl_table);
	// borra todos los tr
	while (vl_tabla_delete.firstChild) {
	  vl_tabla_delete.removeChild(vl_tabla_delete.firstChild);
	}
}

function select_parcialidad_color(ve_table,ve_control){
	style(ve_table);
	var vl_tabla_selected = document.getElementById(ve_table);
	var aTR = vl_tabla_selected.getElementsByTagName("tr");
	
	for (var i = 0; i < aTR.length; i++){
		if (document.getElementById(ve_control+'_' + i).checked == true){
			document.getElementById(ve_table+'_'+i).className="selectitem";
		}
	}
}

function limpiar_total_pago() {
	document.getElementById('TABLE_PARCIALIDAD_TITULOS').style.display = 'none';
	document.getElementById('TABLE_PARCIALIDAD_ITEM').style.display = 'none';
	document.getElementById('td_titulo_parcial').style.display = 'none';
	del_table('TABLE_PARCIALIDAD_ITEM');
	
	//se deshabilita los botones de parcialidad pagar/limpiar
	var vl_boton_pagar = document.getElementById("parcialidad_pagar");
	var vl_boton_limpiar = document.getElementById("parcialidad_limpiar");
	var vl_input_monto_abono = document.getElementById("input_monto_abono");
	vl_boton_pagar.style.display = 'none';
	vl_boton_limpiar.style.display = 'none';
	vl_input_monto_abono.style.display = 'none';
}

function select_color(){
	style('TABLE_PARCIALIDAD_ITEM');
	select_parcialidad_color('TABLE_PARCIALIDAD_ITEM','R_PAGAR_PARCIAL');
	document.getElementById('ABONO_SUB_BOLETA_0').value = 0;
	
	document.getElementById('input_monto_abono').style.display = '';
}

function select_radio(){
	var vl_tabla_parcialidad_item = document.getElementById('TABLE_PARCIALIDAD_ITEM');
	var aTR = vl_tabla_parcialidad_item.getElementsByTagName("tr");
	var nro_seleccion = '';
	var vl_saldo_pesos = 0;
	for (var i = 0; i < aTR.length; i++){
		if (document.getElementById('R_PAGAR_PARCIAL_' + i).checked == true){
			nro_seleccion = document.getElementById('COD_PLAN_PAGO_H_' + i).value;
			vl_saldo_pesos = replaceChars(document.getElementById('P_SALDO_PESOS_' + i).innerHTML);
		}
	}
	
	document.getElementById('ABONO_SUB_BOLETA_0').value = vl_saldo_pesos;
}

function valida_monto_abono() {
	var vl_monto_abono_pesos = document.getElementById('ABONO_SUB_BOLETA_0').value;
	
	var vl_tabla_parcialidad_item = document.getElementById('TABLE_PARCIALIDAD_ITEM');
	var aTR = vl_tabla_parcialidad_item.getElementsByTagName("tr");
	var nro_seleccion = '';
	var vl_saldo_pesos = 0;
	for (var i = 0; i < aTR.length; i++){
		if (document.getElementById('R_PAGAR_PARCIAL_' + i).checked == true){
			nro_seleccion = document.getElementById('COD_PLAN_PAGO_H_' + i).value;
			vl_saldo_pesos = replaceChars(document.getElementById('P_SALDO_PESOS_' + i).innerHTML);
		}
	}
	
	if(parseInt(vl_monto_abono_pesos) == 0 || vl_monto_abono_pesos == ''){
		alert('Debe ingresar un Monto superior a 0.');
		document.getElementById('ABONO_SUB_BOLETA_0').focus();
		return false;

	/*}else if(parseInt(vl_monto_abono_pesos) > 999999 ){
		alert('El monto ingresado no puede ser mayor  a 1.000.000.');
		document.getElementById('ABONO_SUB_BOLETA_0').focus();
		return false;*/

	}else if(parseInt(vl_monto_abono_pesos) > parseInt(vl_saldo_pesos) ){
		alert('El monto ingresado no puede ser mayor al monto de la parcialidad.');
		document.getElementById('ABONO_SUB_BOLETA_0').focus();
		return false;
	}else{
		document.getElementById('CB_COD_PLAN_PAGO_H_0').value = nro_seleccion;
		document.getElementById('CB_MONTO_PESOS_BOLETA_H_0').value = document.getElementById('ABONO_SUB_BOLETA_0').value;
		return true;
	}
}

function crea_sub_boletas_pago(ve_nro_seleccion, ve_abono_monto_pesos){
	var vl_cod_plan_pago = ve_nro_seleccion;
	var vl_abono_pesos = ve_abono_monto_pesos;
	//crea boletas y sub_boletas
	var ajax = nuevoAjax();
	ajax.open("GET", "ajax_crea_sub_boleta.php?cod_plan_pago="+vl_cod_plan_pago+"&monto_abono_pesos="+vl_abono_pesos, false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	var result = eval("(" + resp + ")");
	
	if(resp == 'SIN_EXITO'){
		alert('no creo boleta');
		return false;
	}else{
		var vl_cod_boleta = result[0]['COD_BOLETA'];
		var vl_cod_sub_boleta = result[0]['COD_SUB_BOLETA'];
		var vl_monto_boleta = result[0]['MONTO_PESOS'];
		
		document.getElementById('COD_SUB_BOLETA_H_0').value = vl_cod_sub_boleta;
		document.getElementById('MONTO_PESOS_BOLETA_H_0').value = vl_monto_boleta;
		
		return true;
	}
}

function display_plan_pago(ve_cod_deuda) {
	document.getElementById('td_titulo_parcial').style.display = '';
	document.getElementById('TABLE_PARCIALIDAD_TITULOS').style.display = '';
	document.getElementById('TABLE_PARCIALIDAD_ITEM').style.display = '';
	//borra - TABLE_PARCIALIDAD
	del_table('TABLE_PARCIALIDAD_ITEM');
	document.getElementById('input_monto_abono').style.display = 'none';
	
	var vl_tabla_parcialidad = document.getElementById('TABLE_PARCIALIDAD_ITEM');
	var vl_tbody_parcialidad = document.createElement('tbody');
	
	//se obtiene el cod_plan_pago de la deuda y se obtiene las boletas y sub boletas 
	//que corresponde a sus parcialidades
	var ajax = nuevoAjax();
	ajax.open("GET", "ajax_get_plan_pago.php?cod_deuda="+ve_cod_deuda, false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	
	if(resp == 'NO_EXISTE_PLAN_PAGO'){
		alert('La Deuda no registra cuotas pendientes de pago. Consulte su situación en Administracion de Fondos del Crédito Universitario de la Universidad.');
		//limpiar_plan_pago();
		return false;
	}else{
		var result = eval("(" + resp + ")");
		for (var i = 0; i < result.length; i++) {
			var vl_tr = document.createElement("tr");
			vl_tr.id = 'TABLE_PARCIALIDAD_ITEM_' + i;
			vl_tr.setAttribute("height", "23px");				
			if (i%2==0)
				vl_tr.className="claro";
			else
				vl_tr.className="oscuro";
				
			
			var es_radio_button = result[i]['ES_RADIO_BUTTON'];
			if(es_radio_button == 'S'){
				//R_PAGAR_PARCIAL
				var vl_td_r_pagar = document.createElement("td");
				vl_td_r_pagar.width = "10%";
				vl_td_r_pagar.align = "center";
				vl_tr.appendChild(vl_td_r_pagar);
					var vl_label_r_pagar = document.createElement("label");
					vl_td_r_pagar.appendChild(vl_label_r_pagar);
					var vl_radio_seleccion = document.createElement("input");
						vl_radio_seleccion.type = "radio";
						//vl_radio_seleccion.name = "R_ANUALIDAD";
						vl_radio_seleccion.setAttribute('name', "R_PAGAR_PARCIAL")
						vl_radio_seleccion.id = 'R_PAGAR_PARCIAL_' + i;
						//vl_radio_seleccion.onclick = function(){select_color();hidden_anualidad();}
						vl_radio_seleccion.onclick = function(){select_color();select_radio();};
						//vl_radio_seleccion.onclick = function(){select_parcialidad_color('TABLE_PARCIALIDAD_ITEM','PAGAR_CHECK');};				
						vl_label_r_pagar.appendChild(vl_radio_seleccion);
			}else{
				//R_PAGAR_PARCIAL
				var vl_td_r_pagar = document.createElement("td");
				vl_td_r_pagar.width = "10%";
				vl_td_r_pagar.align = "center";
				vl_tr.appendChild(vl_td_r_pagar);
					var vl_label_r_pagar = document.createElement("label");
					vl_label_r_pagar.innerHTML = 'X';
					vl_label_r_pagar.id = 'R_PAGAR_PARCIAL_' + i;
					vl_td_r_pagar.appendChild(vl_label_r_pagar);
			}
			
			//NRO_CUOTA
			var vl_td_parcialidad = document.createElement("td");
			vl_td_parcialidad.width = "15%";
			vl_td_parcialidad.align = "right";
			vl_tr.appendChild(vl_td_parcialidad);
				var vl_label_parcialidad = document.createElement("label");
				vl_label_parcialidad.innerHTML = result[i]['NRO_CUOTA'];
				vl_label_parcialidad.id = 'NRO_CUOTA_' + i;
				vl_td_parcialidad.appendChild(vl_label_parcialidad);
				//COD_PLAN_PAGO_H HIDDEN
					var vl_cod_plan_pago_h = document.createElement("input");
					vl_cod_plan_pago_h.type = 'hidden';
					vl_cod_plan_pago_h.id = 'COD_PLAN_PAGO_H_' + i;
					vl_cod_plan_pago_h.value = result[i]['COD_PLAN_PAGO'];
					vl_td_parcialidad.appendChild(vl_cod_plan_pago_h);
			
			//ANO_COBRO
			var vl_td_conv_utm = document.createElement("td");
			vl_td_conv_utm.width = "20%";
			vl_td_conv_utm.align = "right";
			vl_tr.appendChild(vl_td_conv_utm);
				var vl_label_conv_utm = document.createElement("label");
				vl_label_conv_utm.innerHTML = result[i]['ANO_COBRO'];
				vl_label_conv_utm.id = 'ANO_COBRO_' + i;
				vl_td_conv_utm.appendChild(vl_label_conv_utm);
				
			//FECHA_VENCTO
			var vl_td_f_vencto = document.createElement("td");
			vl_td_f_vencto.width = "20%";
			vl_td_f_vencto.align = "center";
			vl_tr.appendChild(vl_td_f_vencto);
				var vl_label_f_vencto = document.createElement("label");
				vl_label_f_vencto.innerHTML = result[i]['FECHA_VENCTO'];
				vl_label_f_vencto.id = 'FECHA_VENCTO_' + i;
				vl_td_f_vencto.appendChild(vl_label_f_vencto);
			
			//SALDO UTM
			var vl_td_saldo = document.createElement("td");
			vl_td_saldo.width = "20%";
			vl_td_saldo.align = "right";
			vl_tr.appendChild(vl_td_saldo);
				var vl_label_saldo = document.createElement("label");
				vl_label_saldo.innerHTML = number_format(result[i]['SALDO'], 4, ',', '.');
				vl_label_saldo.id = 'SALDO_' + i;
				vl_td_saldo.appendChild(vl_label_saldo);
				
			//SALDO PESOS
			var vl_td_saldo_pesos = document.createElement("td");
			vl_td_saldo_pesos.width = "20%";
			vl_td_saldo_pesos.align = "right";
			vl_tr.appendChild(vl_td_saldo_pesos);
				var vl_label_saldo_pesos = document.createElement("label");
				vl_label_saldo_pesos.innerHTML = number_format(result[i]['SALDO_PESOS'], 0, ',', '.');
				vl_label_saldo_pesos.id = 'P_SALDO_PESOS_' + i;
				vl_td_saldo_pesos.appendChild(vl_label_saldo_pesos);
				
			vl_tbody_parcialidad.appendChild(vl_tr); 
			vl_tabla_parcialidad.appendChild(vl_tbody_parcialidad);
		}
		
		//se deshabilita los botones de parcialidad pagar/limpiar
		document.getElementById("parcialidad_pagar").style.display = '';
		document.getElementById("parcialidad_limpiar").style.display = '';
		document.getElementById('parcialidad_pagar').focus();
	}

}