function validate(){
	var aTR = get_TR('DOCUMENTO_ORIGINAL');
	var count = 0;
	for (i=0; i<aTR.length; i++){
		var rec_tr =get_num_rec_field(aTR[i].id);
		var seleccion = document.getElementById('SELECCION_' + rec_tr).checked;
		if (seleccion == true){
			count = count + 1;
		}
	}
	if (count == 0){
		alert('Debe seleccionar al menos un documento antes de grabar.');
		document.getElementById('SELECCION_0').focus();
		return false;
	}

	var aTR_doc_nuevo = get_TR('DOCUMENTO_PAGO');
	if(count >= 0 && aTR_doc_nuevo.length == 0){
		alert('Debe ingresar al menos un documento nuevo antes de grabar.');
		return false;
	}
	
	var monto_total;
	var filas_tabla 		= document.getElementById("DOCUMENTO_PAGO").rows.length;
	var pago_efectivo		= 4;
	var sum_monto_doc		= document.getElementById('SUM_MONTO_C_H_0').value;
	var sum_monto			= number_format(parseFloat(sum_monto_doc), 0,'', '');
	var cont				= 0;
	var aTR = get_TR('DOCUMENTO_PAGO');
	ajax			= nuevoAjax();
	ajax.open("GET", "ajax_fecha_actual.php",false);
    ajax.send(null);
    var resp 	= URLDecode(ajax.responseText);
    var fecha_actual	= resp;
	for (var i=0; i < aTR.length ; i++) {
		var record 					= get_num_rec_field(aTR[i].id);
		var cod_tipo_documento_pago = document.getElementById('COD_TIPO_DOCUMENTO_PAGO_'+i);
		cod_tipo_documento_pago = cod_tipo_documento_pago.options[cod_tipo_documento_pago.selectedIndex].value;
		var nro_documento_pago		= document.getElementById('NRO_DOCUMENTO_PAGO_'+i).value;
		var cod_banco				= document.getElementById('COD_BANCO_'+i).value;
		var cod_plaza				= document.getElementById('COD_PLAZA_'+i).value;
		var fecha_documento_pago	= document.getElementById('FECHA_DOCUMENTO_PAGO_'+i).value;
		var monto					= document.getElementById('MONTO_'+i).value;
		var tipo_documento_pago		= document.getElementById('COD_TIPO_DOCUMENTO_PAGO_'+i).value;

		var k_efectivo				= 4;
		
		if(tipo_documento_pago == k_efectivo) {
			cont++;
		}
		if(cont > 1){
			TabbedPanels1.showPanel(0);
			document.getElementById('COD_TIPO_DOCUMENTO_PAGO_'+i).focus();
			alert('Solo se puede agregar una vez "Efectivo"');
			return false;
		}
		if(monto == 0){
			TabbedPanels1.showPanel(0);
			document.getElementById('MONTO_'+i).focus();
			alert('Debe Ingresar Un Monto mayor a 0');
			return false;
		}
		if(cod_tipo_documento_pago != pago_efectivo){						
			if(nro_documento_pago == '' || nro_documento_pago == null){
				TabbedPanels1.showPanel(0);
				document.getElementById('NRO_DOCUMENTO_PAGO_'+i).focus();
				alert('Debe Ingresar el Nro del documento');
				return false;
			}
			if(cod_banco == '' || cod_banco == null){
				TabbedPanels1.showPanel(0);
				document.getElementById('COD_BANCO_'+i).focus();
				alert('Debe Ingresar el Banco');
				return false;
			}
			if(cod_plaza == '' || cod_plaza == null){
				TabbedPanels1.showPanel(0);
				document.getElementById('COD_PLAZA_'+i).focus();
				alert('Debe Ingresar La Plaza');
				return false;
			}
			if(fecha_documento_pago == '' || fecha_documento_pago == null){
				TabbedPanels1.showPanel(0);
				document.getElementById('FECHA_DOCUMENTO_PAGO_'+i).focus();
				alert('Debe Ingresar Fecha de Pago del Documento');
				return false;
			}
		}
	}

	//verifica que las sumas de Doc. Original y Doc. Nuevo sean iguales, de lo contrario no permitira grabar
	var sum_doc_original = document.getElementById('SUM_MONTO_SELECCIONADO_0').value;
	var sum_doc_nuevo = document.getElementById('SUM_MONTO_C_H_0').value;

	if(sum_doc_original > sum_doc_nuevo){
		alert('El total de Documento Original no puede ser mayor que el total de los Documentos Nuevos.\nFavor verifique antes de grabar.\n\n Total Documento Original: '+ number_format(sum_doc_original, 0, ',', '.') + '\n Total Documento Nuevo: '+ number_format(sum_doc_nuevo, 0, ',', '.'));
		return false;
	}
	else if(sum_doc_original < sum_doc_nuevo){
		alert('El total de Documento Original no puede ser menor que el total de los Documentos Nuevos.\nFavor verifique antes de grabar.\n\n Total Documento Original: '+ number_format(sum_doc_original, 0, ',', '.') + '\n Total Documento Nuevo: '+ number_format(sum_doc_nuevo, 0, ',', '.'));
		return false;
	}
	
	
	var total_doc_ori 	= document.getElementById('SUM_MONTO_SELECCIONADO_H_0').value;
	var recargo 		= document.getElementById('RECARGO_0').value;
	var descuento 		= document.getElementById('DESCUENTO_0').value;
	
	var resta 		= parseInt(recargo) - parseInt(descuento);
	var sumatoria 	= parseInt(total_doc_ori) + resta;
	
	if(sum_doc_nuevo != sumatoria){
		alert('Los montos asignados no cuadran. Favor verifique antes de grabar.\n\n Total: '+ number_format(sumatoria, 0, ',', '.') + '\n Total Documentos: '+ number_format(sum_doc_nuevo, 0, ',', '.'));
		return false;
	}
	
	return true;
}

function obtiene_datos(){

	var rut = document.getElementById('RUT_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET", "ajax_datos_deudor.php?rut=" + rut, false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);

	if (resp == 'VACIO') {
		setea_null_deudor();
		document.getElementById('RUT_0').focus();
	} else if (resp == 'NO_RUT') {
		alert('No existe un Deudor con ese Rut');
		setea_null_deudor();
		document.getElementById('RUT_0').focus();
	}	
	else {
		var result = eval("(" + resp + ")");	
		document.getElementById('DIG_VERIF_0').innerHTML 		= result[0]['DIG_VERIF'];
		document.getElementById('NOMBRE_0').innerHTML 			= result[0]['NOMBRE'];
		document.getElementById('COD_DEUDOR_0').value 			= result[0]['COD_DEUDOR'];
		document.getElementById('COD_DEUDA_0').value 			= result[0]['COD_DEUDA'];
	}
}

function setea_null_deudor() {
	document.getElementById('RUT_0').value = null;
	document.getElementById('DIG_VERIF_0').innerHTML = null;
	document.getElementById('NOMBRE_0').innerHTML = null;
	document.getElementById('COD_DEUDOR_0').value = null;
	document.getElementById('COD_DEUDA_0').value = null;
}

function del_line(ve_tr_id, ve_nom_mantenedor) {
/* 
ve_tr_id: id del tr a eliminar ej: ITEM_COTIZACION_1
ve_nom_mantenedor: Nombre del mantenedor donde esta inserto la dw, ej: 'cotizacion'

Esta funcion se llama al eliminar una linea en una dw

En los modulos donde es usada y si se desea agregar un c�digo adiconal se debe 
reimplementar esta funcion
ver ejemplo en sabbagh.item_registro_hora.js
*/
	del_line_standard(ve_tr_id, ve_nom_mantenedor);
}
function del_line_standard(tr_id, nom_tabla) {
	var tr = document.getElementById(tr_id); 
	var label_record = get_nom_field(tr_id);
	var record = get_num_rec_field(tr_id);
	var ajax = nuevoAjax();
	ajax.open("GET", "../../../../commonlib_seven/trunk/php/del_line.php?nom_tabla="+nom_tabla+"&label_record="+label_record+"&record="+record, false);
	ajax.send(null);	
	var resp = ajax.responseText;	// no se espera respuesta
	tr.parentNode.removeChild(tr);
}

function obtiene_docs_original(){
	var aTR = get_TR('DOCUMENTO_ORIGINAL');	
	for (var i=0; i<aTR.length; i++) {
		var record = get_num_rec_field(aTR[i].id);
		del_line('DOCUMENTO_ORIGINAL_'+record, 'cambio_documento');
	}
	
	var cod_deudor = document.getElementById('COD_DEUDOR_0').value;
	if (cod_deudor != ''){
		var tipo_doc = 'original';
		var ajax = nuevoAjax();
		ajax.open("GET", "ajax_obtiene_docs.php?cod_deudor="+ cod_deudor+"&tipo_doc="+tipo_doc, false);
		ajax.send(null);
		
		var resp = URLDecode(ajax.responseText);
		
		if (resp == 'NO_EXISTEN_DOCS')
			alert('No existen documentos asociados al rut ingresado.');
		else{
			var result = eval("(" + resp + ")");
			for (var i=0; i< result.length; i++) {
				var vl_row = add_line('DOCUMENTO_ORIGINAL', 'cambio_documento');
				document.getElementById('SELECCION_' + vl_row).value 						= result[i]['SELECCION'];
				document.getElementById('COD_DOCUMENTO_PAGO_DO_H_' + vl_row).value 			= result[i]['COD_DOCUMENTO_PAGO_H'];
				document.getElementById('NOM_ESTADO_DOCUMENTO_PAGO_' + vl_row).innerHTML	= result[i]['NOM_ESTADO_DOCUMENTO_PAGO'];
				document.getElementById('NOM_TIPO_DOCUMENTO_PAGO_' + vl_row).innerHTML 		= result[i]['NOM_TIPO_DOCUMENTO_PAGO'];
				document.getElementById('NRO_DOCUMENTO_PAGO_DO_' + vl_row).innerHTML 		= number_format(result[i]['NRO_DOCUMENTO_PAGO'], 0, ',', '.');
				document.getElementById('NOM_BANCO_' + vl_row).innerHTML 					= result[i]['NOM_BANCO'];
				document.getElementById('NOM_PLAZA_' + vl_row).innerHTML 					= result[i]['NOM_PLAZA'];
				document.getElementById('FECHA_DOCUMENTO_PAGO_DO_' + vl_row).innerHTML 		= result[i]['FECHA_DOCUMENTO_PAGO'];
				document.getElementById('MONTO_DO_' + vl_row).innerHTML 					= number_format(result[i]['MONTO'],0, ',', '.');
				document.getElementById('COD_FAMILIA_DEUDA_H_' + vl_row).value 				= result[i]['COD_FAMILIA_DEUDA_H'];
				document.getElementById('COD_DEUDOR_DOC_ORI_' + vl_row).value 						= result[i]['COD_DEUDOR_DOC_ORI'];
		
				document.getElementById('DOCUMENTO_ORIGINAL_' + vl_row).style.display 		= result[i]['DISPLAY_TR'];  
	
			}
		} 
		
		var tipo_doc = 'nuevo';
		var ajax = nuevoAjax();
		ajax.open("GET", "ajax_obtiene_docs.php?cod_deudor="+ cod_deudor+"&tipo_doc="+tipo_doc, false);
		ajax.send(null);
		
		var resp = URLDecode(ajax.responseText);
		
		var result = eval("(" + resp + ")");
		for (var j=0; j< result.length; j++) {
			var vl_row = add_line('DOCUMENTO_ORIGINAL', 'cambio_documento');
			document.getElementById('SELECCION_' + vl_row).value 						= result[j]['SELECCION'];
			document.getElementById('COD_DOCUMENTO_PAGO_DO_H_' + vl_row).value 			= result[j]['COD_DOCUMENTO_PAGO_H'];
			document.getElementById('NOM_TIPO_DOCUMENTO_PAGO_' + vl_row).innerHTML 		= result[j]['NOM_TIPO_DOCUMENTO_PAGO'];
			document.getElementById('NRO_DOCUMENTO_PAGO_DO_' + vl_row).innerHTML 		= number_format(result[j]['NRO_DOCUMENTO_PAGO'], 0, ',', '.');
			document.getElementById('NOM_BANCO_' + vl_row).innerHTML 					= result[j]['NOM_BANCO'];
			document.getElementById('NOM_PLAZA_' + vl_row).innerHTML 					= result[j]['NOM_PLAZA'];
			document.getElementById('FECHA_DOCUMENTO_PAGO_DO_' + vl_row).innerHTML 		= result[j]['FECHA_DOCUMENTO_PAGO'];
			document.getElementById('MONTO_DO_' + vl_row).innerHTML 					= number_format(result[j]['MONTO'],0, ',', '.');  
			document.getElementById('COD_FAMILIA_DEUDA_H_' + vl_row).value 				= result[i]['COD_FAMILIA_DEUDA_H'];
			document.getElementById('COD_DEUDOR_DOC_ORI_' + vl_row).value 						= result[i]['COD_DEUDOR_DOC_ORI'];
		
			document.getElementById('DOCUMENTO_ORIGINAL_' + vl_row).style.display 		= result[i]['DISPLAY_TR'];
	
		}
	}//if (cod_deudor != null)	
}

function seleccion_doc(ve_seleccion){ 
	var seleccion = ve_seleccion.checked;
	var record = get_num_rec_field(ve_seleccion.id);
	
	if (seleccion == true){
		document.getElementById('SELECCION_V_' + record).value = '1';
	}
	else if(seleccion == false){
		document.getElementById('SELECCION_V_' + record).value = '0';
	}
	computed(get_num_rec_field(record), 'MONTO_SELECCIONADO');
}

function recargo_descuento(ve_campo){
	
	var nom_campo = get_nom_field(ve_campo.id);

	if (nom_campo == "RECARGO")
		document.getElementById('DESCUENTO_0').value = 0;
	else if (nom_campo == "DESCUENTO")
		document.getElementById('RECARGO_0').value = 0;
}

function change_familia_deuda(ve_cod_familia_deuda) {
	var vl_ind = 0;
	var aTR = get_TR('DOCUMENTO_ORIGINAL');
	for (i=0; i<aTR.length; i++){
		var rec_tr = get_num_rec_field(aTR[i].id);
		var vl_seleccion = document.getElementById('SELECCION_' + rec_tr);
		vl_seleccion.checked = false;
		var vl_cod_familia_deuda = document.getElementById('COD_FAMILIA_DEUDA_H_' + rec_tr).value;
		if (vl_cod_familia_deuda == ve_cod_familia_deuda.value) {
			aTR[i].style.display = '';
			if (vl_ind % 2 == 0)
				aTR[i].setAttribute('class', "claro");
			else	
				aTR[i].setAttribute('class', "oscuro");
			vl_ind++
		}
		else {
			aTR[i].style.display = 'none';
		}
	}
}

function static_result(){
	var aTR = get_TR('DOCUMENTO_ORIGINAL');
	var sum = 0;
	var recargo = document.getElementById('RECARGO_0').value;
	if(recargo == '')
		recargo = 0;
	var descuento = document.getElementById('DESCUENTO_0').value;
	if(descuento =='')
		descuento = 0;
	for (i=0; i<aTR.length; i++){
		var rec_tr = get_num_rec_field(aTR[i].id);
		var seleccion = document.getElementById('SELECCION_' + rec_tr).checked;
		if(seleccion == true){
			sum = parseInt(sum)+ parseInt(document.getElementById('MONTO_SELECCIONADO_H_'+ rec_tr).value);
		}
	}
	var aux = parseInt(recargo) - parseInt(descuento);	
	var sumatoria 	= sum + aux;
	document.getElementById('STATIC_RESULT_0').innerHTML = number_format(sumatoria, 0, ',', '.');
	
}
function f_banco_deposito(ve_tipo_doc_pago){
	var vl_cod_deuda = document.getElementById('COD_DEUDA_0').value;
	var aTR = get_TR('DOCUMENTO_PAGO');
	for (var tr=0; tr < aTR.length ; tr++) {
		var cod_tipo_doc_pago	= document.getElementById('COD_TIPO_DOCUMENTO_PAGO_'+tr).value;
		var seleccionado = document.getElementById('COD_BANCO_'+tr).selectedIndex; // TOMA EL VALOR DEL ITEM SELECCIONADO PARA VOLVER A CARGARLO AL FINAL DEL LOOP
		document.getElementById('COD_BANCO_'+tr).length = 0; //DEJA EL DD SIN OPTIONS
		
		ajax = nuevoAjax();
		ajax.open("GET", "ajax_dd_banco.php?cod_tipo_doc_pago="+cod_tipo_doc_pago+"&cod_deuda="+vl_cod_deuda,false);
		ajax.send(null);
		
		var resp	= URLDecode(ajax.responseText);
		var lista1	= resp.split('*');
	    
		var selOpcion = new Option('', 0); //CREA UN OBJETO OPTION
		eval(document.getElementById('COD_BANCO_'+tr).options[0]=selOpcion); //LE ASIGNA UN VALOR DE 0 Y '' AL OBJETO OPTION
		
		var j = 1; //PARA MANEJAR LOS INDICES DE LOS OPTIONS CREADOS
		for(var i=0;i<lista1.length-1;i++){
	    	var lista2 = lista1[i].split('|');
	    	var selOpcion = new Option(lista2[1], lista2[0]);// SE CREA UN OPTION CON LOS VALORES DEL AJAX
			eval(document.getElementById('COD_BANCO_'+tr).options[j]=selOpcion);// SE LE ASIGNAN LOS VALORES DE TEXT Y VALUE AL OPTION CREADO
			j++;  
	    }
	    
	    var largo = document.getElementById('COD_BANCO_'+tr).length;
	    if(seleccionado > largo){
	   		document.getElementById('COD_BANCO_'+tr).options[0].selected = true; // ASIGNA EL VALOR '' SI ES QUE EL VALUE DEL OPTION TENIA VALOR Y LUEGO NO
		}
		else{
			document.getElementById('COD_BANCO_'+tr).options[seleccionado].selected = true; // ASIGNA EL VALOR SELECCIONADO ORIGINALMENTE
		}
	}
}

function cambia_banco(ve_campo){
	var record = get_num_rec_field(ve_campo.id);
	var aTR = get_TR('DOCUMENTO_PAGO');
	for (i=0; i<aTR.length; i++){
		if (record < i){
			document.getElementById('COD_BANCO_'+i).value = document.getElementById('COD_BANCO_'+record).value;
		}
	}
}

function cambia_plaza(ve_campo){
	var record = get_num_rec_field(ve_campo.id);
	var aTR = get_TR('DOCUMENTO_PAGO');
	for (i=0; i<aTR.length; i++){
		if (record < i){
			document.getElementById('COD_PLAZA_'+i).value = document.getElementById('COD_PLAZA_'+record).value;
		}
	}
}

function cambia_nro_documento(ve_campo){
	var record = get_num_rec_field(ve_campo.id);
	var aTR = get_TR('DOCUMENTO_PAGO');
	var nro_documento = document.getElementById('NRO_DOCUMENTO_PAGO_'+record).value;
	
	//si se borra el nro documento, copia este cambio a los dem�s registros
	if (nro_documento == ''){
		for (i=0; i<aTR.length; i++){
			if (record < i)
				document.getElementById('NRO_DOCUMENTO_PAGO_'+i).value = '';
		}
		return;
	}
	
	var nro_documento = parseInt(nro_documento);
	for (i=0; i<aTR.length; i++){
		if (record < i){
			var nro_documento = nro_documento + 1;
			document.getElementById('NRO_DOCUMENTO_PAGO_'+i).value =  nro_documento;
		}
	}
}

function cambia_fecha_docto (ve_campo){
	var record = get_num_rec_field(ve_campo.id);
	var aTR = get_TR('DOCUMENTO_PAGO');
	
	for (i=record; i<aTR.length; i++){
		var record = get_num_rec_field(aTR[i].id);
		var aux = get_num_rec_field(aTR[i].id) + 1;
		var fecha = document.getElementById('FECHA_DOCUMENTO_PAGO_'+record).value;
		var fecha = fecha.split('/');
		var ano = fecha[2];
				
		if ((ano.length )== 2){ //se ingreso el a�o en dos digitos, ejemplo = 17/01/11
			var ano_final = parseInt(ano) + 2000; //deja el a�o en cuatro digitos, ejemplo = 17/01/2011
		}	
		else{
			ano_final = fecha[2];
		}
		
		var  fecha = fecha[0]+'/'+fecha[1]+'/'+ano_final;	
		ajax			= nuevoAjax();
		ajax.open("GET", "ajax_get_fecha_posterior.php?fecha=" + fecha, false);
		ajax.send(null);
		var resp 	= URLDecode(ajax.responseText);
		var resp = ajax.responseText.split('|');	
	
		for (j=parseInt(record)+parseInt(1); j<aTR.length; j++){
			var record2 = get_num_rec_field(aTR[j].id);
			document.getElementById('FECHA_DOCUMENTO_PAGO_'+record2).value = resp;
		} 
	}
}

function add_line(ve_tabla_id, ve_nom_tabla) {
	if (ve_tabla_id == 'DOCUMENTO_PAGO') {
		var vl_row = add_line_standard(ve_tabla_id, ve_nom_tabla);
		if (vl_row>0){
			var vl_row_ant = vl_row - 1;
			var cod_banco = document.getElementById('COD_BANCO_'+vl_row_ant).value;
			var cod_plaza = document.getElementById('COD_PLAZA_'+vl_row_ant).value;
			var fecha_documento_pago = document.getElementById('FECHA_DOCUMENTO_PAGO_'+vl_row_ant).value;
			
			cambia_nro_documento (document.getElementById('NRO_DOCUMENTO_PAGO_'+vl_row_ant));
			document.getElementById('COD_BANCO_'+vl_row).value = cod_banco;
			document.getElementById('COD_PLAZA_'+vl_row).value = cod_plaza;
			cambia_fecha_docto (document.getElementById('FECHA_DOCUMENTO_PAGO_'+vl_row_ant));
		}
		return vl_row;
	}
	else
		return add_line_standard(ve_tabla_id, ve_nom_tabla);
}