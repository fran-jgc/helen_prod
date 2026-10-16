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
	document.getElementById('ABONO_SUB_BOLETA_0').innerHTML = 0;
	
	document.getElementById('input_monto_abono').style.display = '';
}
function comprobar_cuota_anterior(i){
	if(parseInt(i) > 0){
		i = parseInt(i)-1;
		var monto_original = parseInt($("#MAXIMO_CUOTA_H_"+i).val());
		var monto_ingresado = parseInt($("#P_MONTO_CUOTA_INGRESABLE_"+i).val());
		
		if (!$("#R_PAGAR_PARCIAL_"+ i).is(':checked')){ 
			alert('Debe cancelar la cuota más antigua primero');
			document.getElementById("P_MONTO_CUOTA_INGRESABLE_"+ i).readOnly = true; 
			$("#P_MONTO_CUOTA_INGRESABLE_"+i).val(monto_original); 
 
			return false;
		}
		
		if(monto_ingresado < monto_original){
			alert('Debe cancelar la totalidad de la cuota anterior si desea pagar la cuota siguiente');
			$("#P_MONTO_CUOTA_INGRESABLE_"+i).val(monto_original); 
			
			return false;
		}else
			return true;
	}else 
		return true;
}
function validate_secuencia(id){
	var id_anterior = Number(id) - 1;
	var monto_parcialidad_anterior = document.getElementById('PARCIALIDAD_MONTO_' + id_anterior).value;
	var monto_a_pagar_anterior = replaceChars(document.getElementById('P_SALDO_PESOS_' + id_anterior).innerHTML);

	
	if(document.getElementById('R_PAGAR_PARCIAL_' + id_anterior).checked == false){
		alert('Debe pagar la cuota anterior.');
		document.getElementById('R_PAGAR_PARCIAL_' + id).checked=false;
		select_color(); 
		return false;
	}
	if(monto_a_pagar_anterior>monto_parcialidad_anterior){
		alert('Debe cancelar la totalidad de la cuota anterior si desea pagar la cuota siguiente.');
		document.getElementById('R_PAGAR_PARCIAL_' + id).checked=false;
		select_color(); 
		return false;
	}
	else{
		document.getElementById('PARCIALIDAD_MONTO_' + id_anterior).disabled=true;
		return true;
	}
}
function calcular_total_pago(){
	var vl_tabla_parcialidad_item = document.getElementById('TABLE_PARCIALIDAD_ITEM');
	var aTR = vl_tabla_parcialidad_item.getElementsByTagName("tr");
	var valor_total = 0;
	for (var i = 0; i < aTR.length; i++){
		if (document.getElementById('R_PAGAR_PARCIAL_' + i).checked == true){
			valor_total = valor_total+ parseInt(document.getElementById('PARCIALIDAD_MONTO_' + i).value);
		}
	}
	document.getElementById('ABONO_SUB_BOLETA_0').innerHTML = number_format(valor_total, 0, ',', '.');
}
function valida_monto_parcialidad(ve_campo){
	var i = get_num_rec_field(ve_campo.id); 
	var monto_parcialidad = Number(document.getElementById('PARCIALIDAD_MONTO_' + i).value);
	var monto_pesos =Number(replaceChars(document.getElementById('P_SALDO_PESOS_' + i).innerHTML));
	var id_posterior = Number(i) + 1;

	var nro_seleccion = document.getElementById('COD_PLAN_PAGO_H_' + i).value;
	var nro_deuda = document.getElementById('COD_DEUDA_H_' + i).value;
	var cod_tipo_deuda = document.getElementById('COD_TIPO_DEUDA_H_' + i).value;
	var vl_saldo_pesos = document.getElementById('ABONO_SUB_BOLETA_0').innerHTML;
	vl_saldo_pesos = vl_saldo_pesos.replaceAll(".", ""); 
	var nro_cuota='';
	nro_cuota = document.getElementById('NRO_CUOTA_' + i).innerHTML;

	if(document.getElementById('R_PAGAR_PARCIAL_' + id_posterior)){
		if((document.getElementById('R_PAGAR_PARCIAL_' + id_posterior).checked == true) &&(monto_parcialidad<monto_pesos)){
			alert('La Parcialidad está permitida solo en la última cuota.');
			document.getElementById('PARCIALIDAD_MONTO_' + i).value = monto_pesos;
			return false;
		}
	}
	if(monto_parcialidad<=0){
		alert('Debe ingresar un Monto superior a 0.');
		document.getElementById('PARCIALIDAD_MONTO_' + i).value = monto_pesos;
		document.getElementById('PARCIALIDAD_MONTO_' + i).focus();
		return false;
	}
	if(monto_parcialidad>monto_pesos){
		alert('El Monto no puede ser mayor al monto de la cuota.');
		document.getElementById('PARCIALIDAD_MONTO_' + i).value = monto_pesos;
		document.getElementById('PARCIALIDAD_MONTO_' + i).focus();
		return false;
	}
	//caso deuda en estado SVP, se setea cod_plan_pago en NULL
	if((nro_cuota == 99)&&(cod_tipo_deuda == 'CS')){
		document.getElementById('tr_descuento').style.display = ''; 
		document.getElementById('tr_total_pagar').style.display = '';
		
		var vl_cod_deuda = nro_deuda;
		var vl_abono_pesos = 0;
		
		if(nro_cuota==99){
			vl_abono_pesos = document.getElementById('PARCIALIDAD_MONTO_' + i).value;
		}else{
			vl_abono_pesos = vl_saldo_pesos;
		}
		//calcular el descuento al seleccionar la cuota para deuda SVP
		var ajax = nuevoAjax();
		ajax.open("GET", "ajax_boleta_calculo.php?cod_deuda="+vl_cod_deuda+"&monto_abono_pesos="+vl_abono_pesos+"&cod_plan_pago="+nro_seleccion, false);
		ajax.send(null);
		var resp = URLDecode(ajax.responseText);
		var result = eval("(" + resp + ")");
		
		if(resp == 'SIN_EXITO'){
			alert('Falla al aplicar descuento');
			return false;
		}else{
			var vl_descuento = result[0]['D_DSCTO_PESOS'];
			var vl_total_pagar = vl_saldo_pesos - vl_descuento;
			
			document.getElementById('DESCTO_SUB_BOLETA_0').innerHTML = number_format(vl_descuento, 0, ',', '.');
			document.getElementById('TOTAL_SUB_BOLETA_0').innerHTML = number_format(vl_total_pagar, 0, ',', '.');
		}
	}else{
		document.getElementById('DESCTO_SUB_BOLETA_0').innerHTML = '';
		document.getElementById('TOTAL_SUB_BOLETA_0').innerHTML = '';
		document.getElementById('tr_descuento').style.display = 'none'; 
		document.getElementById('tr_total_pagar').style.display = 'none';
	}
}
function select_check(ve_campo){
	var vl_nom_campo = get_nom_field(ve_campo.id);
	var i = Number(get_num_rec_field(ve_campo.id)); 
	var nro_seleccion = '';
	var nro_deuda = '';
	var vl_saldo_pesos = 0;
	var valida_secuencia = false;
	var id_posterior = Number(i) + 1;
	var id_anterior = Number(i) - 1;
	var nro_cuota='';
		if (document.getElementById('R_PAGAR_PARCIAL_' + i).checked == true){
			if(i>0){
				if(validate_secuencia(i)==true){
					valida_secuencia=true;
				}else{
					valida_secuencia=false;
				}
			}else{
				valida_secuencia = true;
			}
			if(valida_secuencia){
				document.getElementById('PARCIALIDAD_MONTO_' + i).disabled = false;
				select_color(); 
				nro_seleccion = document.getElementById('COD_PLAN_PAGO_H_' + i).value;
				nro_deuda = document.getElementById('COD_DEUDA_H_' + i).value;
				nro_cuota = document.getElementById('NRO_CUOTA_' + i).innerHTML;
				cod_tipo_deuda = document.getElementById('COD_TIPO_DEUDA_H_' + i).value;
				if(vl_nom_campo=='R_PAGAR_PARCIAL'){
					document.getElementById('PARCIALIDAD_MONTO_' + i).value=replaceChars(document.getElementById('P_SALDO_PESOS_' + i).innerHTML);
				}
			}
		}else{
			if(document.getElementById('R_PAGAR_PARCIAL_' + id_posterior)){
				if(document.getElementById('R_PAGAR_PARCIAL_' + id_posterior).checked == true){
				alert('Debe mantener un correlativo en cuotas seleccionadas.');
				document.getElementById('R_PAGAR_PARCIAL_' + i).checked=true;
				select_color(); 
				calcular_total_pago();
				return false;
				}
			}
			if(document.getElementById('R_PAGAR_PARCIAL_' + id_anterior)){
				if(document.getElementById('R_PAGAR_PARCIAL_' + id_anterior).checked == true){
					document.getElementById('PARCIALIDAD_MONTO_' + id_anterior).disabled = false;
				}
			}
			document.getElementById('PARCIALIDAD_MONTO_' + i).disabled = true;
			select_color(); 
			document.getElementById('PARCIALIDAD_MONTO_' + i).value = '';

		}
	calcular_total_pago();
	vl_saldo_pesos = document.getElementById('ABONO_SUB_BOLETA_0').innerHTML;
	vl_saldo_pesos = vl_saldo_pesos.replaceAll(".", ""); 
	//caso deuda en estado SVP, se setea cod_plan_pago en NULL
	if((nro_cuota == 99)&&(cod_tipo_deuda == 'CS')){
		
		document.getElementById('tr_descuento').style.display = ''; 
		document.getElementById('tr_total_pagar').style.display = '';
		
		var vl_cod_deuda = nro_deuda;
		var vl_abono_pesos = 0;
		
		if(nro_cuota==99){
			vl_abono_pesos = document.getElementById('PARCIALIDAD_MONTO_' + i).value;
		}else{
			vl_abono_pesos = vl_saldo_pesos;
		}
		
		//calcular el descuento al seleccionar la cuota para deuda SVP
		var ajax = nuevoAjax();
		ajax.open("GET", "ajax_boleta_calculo.php?cod_deuda="+vl_cod_deuda+"&monto_abono_pesos="+vl_abono_pesos+"&cod_plan_pago="+nro_seleccion, false);
		ajax.send(null);
		var resp = URLDecode(ajax.responseText);
		var result = eval("(" + resp + ")");
		
		if(resp == 'SIN_EXITO'){
			alert('Falla al aplicar descuento');
			return false;
		}else{
			var vl_descuento = result[0]['D_DSCTO_PESOS'];
			var vl_total_pagar = vl_saldo_pesos - vl_descuento;
			
			document.getElementById('DESCTO_SUB_BOLETA_0').innerHTML = number_format(vl_descuento, 0, ',', '.');
			document.getElementById('TOTAL_SUB_BOLETA_0').innerHTML = number_format(vl_total_pagar, 0, ',', '.');
		}
	}else{
			document.getElementById('DESCTO_SUB_BOLETA_0').innerHTML = '';
			document.getElementById('TOTAL_SUB_BOLETA_0').innerHTML = '';
			document.getElementById('tr_descuento').style.display = 'none'; 
			document.getElementById('tr_total_pagar').style.display = 'none';
	}
}
function valida_monto_abono() {
	
	var vl_tabla_parcialidad_item = document.getElementById('TABLE_PARCIALIDAD_ITEM');
	var aTR = vl_tabla_parcialidad_item.getElementsByTagName("tr");
	var nro_seleccion = '';
	var nro_deuda_seleccion = '';
	var vl_saldo_pesos = '';
	
	if (document.getElementById('R_PAGAR_PARCIAL_0').checked == false){
		alert('Debe seleccionar una cuota.');
		return false;
	}
	
	for (var i = 0; i < aTR.length; i++){
		if (document.getElementById('R_PAGAR_PARCIAL_' + i).checked == true){
			nro_seleccion = nro_seleccion+document.getElementById('COD_PLAN_PAGO_H_' + i).value+'|';
			nro_deuda_seleccion = document.getElementById('COD_DEUDA_H_' + i).value;
			vl_saldo_pesos = vl_saldo_pesos+replaceChars(document.getElementById('PARCIALIDAD_MONTO_' + i).value)+'|';
		}
	}
	document.getElementById('CB_COD_PLAN_PAGO_H_0').value = nro_seleccion;
	document.getElementById('CB_COD_DEUDA_H_0').value = nro_deuda_seleccion;
	document.getElementById('CB_MONTO_PESOS_BOLETA_H_0').value = vl_saldo_pesos;
	return true;
	
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
	
	// if(resp == 'NO_EXISTE_PLAN_PAGO'){
	// 	alert('La Deuda no registra cuotas pendientes de pago. Consulte su situación en Administracion de Fondos del Crédito Universitario de la Universidad.');
	// 	//limpiar_plan_pago();
	// 	return false;
	// }else{
		var result = eval("(" + resp + ")");
		for (var i = 0; i < result.length; i++) {
			var vl_tr = document.createElement("tr");
			vl_tr.id = 'TABLE_PARCIALIDAD_ITEM_' + i;
			vl_tr.setAttribute("height", "23px");				
			if (i%2==0)
				vl_tr.className="claro"; 
			else
				vl_tr.className="oscuro";
				
			var ES_CHECKBOX = result[i]['ES_CHECKBOX'];

			//R_PAGAR_PARCIAL
			var vl_td_r_pagar = document.createElement("td");
			vl_td_r_pagar.width = "10%";
			vl_td_r_pagar.align = "center"; 
			vl_tr.appendChild(vl_td_r_pagar);

			var vl_label_r_pagar = document.createElement("label");
			vl_td_r_pagar.appendChild(vl_label_r_pagar);

			var vl_radio_seleccion = document.createElement("input");
			vl_radio_seleccion.type = "checkbox";
			vl_radio_seleccion.setAttribute('name', "R_PAGAR_PARCIAL");
			vl_radio_seleccion.id = 'R_PAGAR_PARCIAL_' + i;

			if(ES_CHECKBOX == 'S'){
				vl_radio_seleccion.onclick = function(){select_check(this);};
			}else{
				vl_radio_seleccion.disabled = true;
				vl_radio_seleccion.title = "Cuota no habilitada para pago";
			}

			vl_label_r_pagar.appendChild(vl_radio_seleccion);
			
			//NRO_CUOTA
			var vl_td_parcialidad = document.createElement("td");
			vl_td_parcialidad.width = "10%";
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
					
			//TIPO_DEUDA_H HIDDEN
				var vl_cod_tipo_deuda_h = document.createElement("input");
				vl_cod_tipo_deuda_h.type = 'hidden';
				vl_cod_tipo_deuda_h.id = 'COD_TIPO_DEUDA_H_' + i;
				vl_cod_tipo_deuda_h.value = result[i]['COD_TIPO_DEUDA'];
				vl_td_parcialidad.appendChild(vl_cod_tipo_deuda_h);					
					
			//COD_DEUDA_H HIDDEN
			var vl_cod_deuda_h = document.createElement("input");
			vl_cod_deuda_h.type = 'hidden';
			vl_cod_deuda_h.id = 'COD_DEUDA_H_' + i;
			vl_cod_deuda_h.value = result[i]['COD_DEUDA'];
			vl_td_parcialidad.appendChild(vl_cod_deuda_h);
			
			//ANO_COBRO
			var vl_td_conv_utm = document.createElement("td");
			vl_td_conv_utm.width = "10%";
			vl_td_conv_utm.align = "right";
			vl_tr.appendChild(vl_td_conv_utm);
				var vl_label_conv_utm = document.createElement("label");
				vl_label_conv_utm.innerHTML = result[i]['ANO_COBRO'];
				vl_label_conv_utm.id = 'ANO_COBRO_' + i;
				vl_td_conv_utm.appendChild(vl_label_conv_utm);
				
			//FECHA_VENCTO
			var vl_td_f_vencto = document.createElement("td");
			vl_td_f_vencto.width = "15%";
			vl_td_f_vencto.align = "center";
			vl_tr.appendChild(vl_td_f_vencto);
				var vl_label_f_vencto = document.createElement("label");
				vl_label_f_vencto.innerHTML = result[i]['FECHA_VENCTO'];
				vl_label_f_vencto.id = 'FECHA_VENCTO_' + i;
				vl_td_f_vencto.appendChild(vl_label_f_vencto);
			
			//SALDO UTM
			var vl_td_saldo = document.createElement("td");
			vl_td_saldo.width = "15%";
			vl_td_saldo.align = "right";
			vl_tr.appendChild(vl_td_saldo);
				var vl_label_saldo = document.createElement("label");
				vl_label_saldo.innerHTML = number_format(result[i]['SALDO'], 4, ',', '.');
				vl_label_saldo.id = 'SALDO_' + i;
				vl_td_saldo.appendChild(vl_label_saldo);
				
			//SALDO PESOS
			var vl_td_saldo_pesos = document.createElement("td");
			vl_td_saldo_pesos.width = "15%";
			vl_td_saldo_pesos.align = "right";
			vl_tr.appendChild(vl_td_saldo_pesos);
				var vl_label_saldo_pesos = document.createElement("label");
				vl_label_saldo_pesos.innerHTML = number_format(result[i]['SALDO_PESOS'], 0, ',', '.');
				vl_label_saldo_pesos.id = 'P_SALDO_PESOS_' + i;
				vl_td_saldo_pesos.appendChild(vl_label_saldo_pesos);
				
			vl_tbody_parcialidad.appendChild(vl_tr); 
			vl_tabla_parcialidad.appendChild(vl_tbody_parcialidad);

			//MONTO_PARCIALIDAD
			var vl_td_parcialidad_monto = document.createElement("td");
			vl_td_parcialidad_monto.width = "15%";
			vl_td_parcialidad_monto.align = "right";
			vl_tr.appendChild(vl_td_parcialidad_monto);

			var vl_input_parcialidad = document.createElement("INPUT");
			vl_input_parcialidad.setAttribute("type", "text");
			vl_input_parcialidad.setAttribute("onchange", "valida_num(this);valida_monto_parcialidad(this);select_check(this);");
			vl_input_parcialidad.setAttribute("onkeypress", "return event.charCode >= 48 && event.charCode <= 57");
			vl_input_parcialidad.setAttribute("disabled", "");
			vl_input_parcialidad.innerHTML = '';
			vl_input_parcialidad.value = '';
			vl_input_parcialidad.id = 'PARCIALIDAD_MONTO_' + i;

			// si la cuota no está habilitada, el monto también queda bloqueado
			if(ES_CHECKBOX != 'S'){
				vl_input_parcialidad.disabled = true;
				vl_input_parcialidad.title = "Cuota no habilitada para pago";
			}

			vl_td_parcialidad_monto.appendChild(vl_input_parcialidad);
		}
		
		//se deshabilita los botones de parcialidad pagar/limpiar
		document.getElementById("parcialidad_pagar").style.display = '';
		document.getElementById("parcialidad_limpiar").style.display = '';
		document.getElementById('parcialidad_pagar').focus();
	// }

}

function isNumeric(val) {
    return /^-?\d+$/.test(val);
}

function valida_num(ve_campo){
	var id_campo = ve_campo.id;
	var num = document.getElementById(id_campo).value;
	var record = get_num_rec_field(id_campo);
	var monto_pesos =Number(replaceChars(document.getElementById('P_SALDO_PESOS_' + record).innerHTML));
	if (!isNumeric(num)) {
		
		alert("Debe ingresar un n\u00FAmero v\u00E1lido.");
		document.getElementById(id_campo).value = monto_pesos;
		return false;
	}else{
		return true;
	}
}