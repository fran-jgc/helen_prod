function validate(){
	var aTR = get_TR('ABONO');
	for (i=0; i<aTR.length; i++){
		var rec_tr =get_num_rec_field(aTR[i].id);
		var anula_abono = document.getElementById('ANULA_ABONO_'+rec_tr).checked;
		if (anula_abono == true){
			var cod_abono = document.getElementById('COD_ABONO_'+rec_tr).innerHTML;
			var ajax = nuevoAjax();
			ajax.open("GET", "ajax_comp_aprobado.php?cod_abono="+cod_abono, false);
			ajax.send(null);
			var resp = ajax.responseText;
			
			if(resp == 0){
				alert('Para poder anular el abono el comprobante original de pago debe estar en estado "APROBADO".');
				return false;
			}
			else
				break;
		}		
	}
	if (anula_abono == false){ 
			alert("Debe seleccionar un abono");
			return false;
		}		
	var obs = document.input.OBS_0.value;
	if (obs == ''){
		alert('Debe ingresar "Observaciones" antes de grabar');
		return false;
	}
	return true;
}

function f_get_name_user(){	 
	// RESPUESTAS DEL AJAX:
	// O = EL RUT NO EXISTE EN TABLA DEUDOR (PUEDE INGRESARLO)
	// 1 = EL RUT EXISTE 
	var rut = document.getElementById('RUT_0').value;
		
	var ajax = nuevoAjax();
	ajax.open("GET", "obtiene_nombre.php?rut="+rut, false);
	ajax.send(null);
	
	// siempre se borran los datos de la deuda
	document.getElementById('COD_DEUDA_0').innerHTML 	= '';
	document.getElementById('COD_DEUDA_0').value 	= '';
	document.getElementById('SIMBOLO_0').innerHTML 		= '';
	document.getElementById('SIMBOLO_0').value 		= '';
	document.getElementById('NRO_CUOTA_0').innerHTML 	= '';
	document.getElementById('NRO_CUOTA_0').value 	= '';
	document.getElementById('SALDO_CUOTA_0').innerHTML 		= '';
	document.getElementById('SALDO_CUOTA_0').value 		= '';
	
	var tabla = document.getElementById('ABONO');
	// borra todos los tr
	while (tabla.firstChild) {
	  tabla.removeChild(tabla.firstChild);
	}
	
	if (ajax.readyState==4) 
	{
		var existe = URLDecode(ajax.responseText);	
		if (existe == 0) {
			alert('No existe un Deudor con ese Rut');
			document.getElementById('RUT_0').innerHTML = '';
			document.getElementById('RUT_0').value = ''
			document.getElementById('RUT_0').focus();
			
			document.getElementById('NOMBRE_0').innerHTML = '';
			document.getElementById('NOMBRE_0').value = ''
			document.getElementById('DIG_VERIF_0').innerHTML = '';
			document.getElementById('DIG_VERIF_0').value = '';
			document.getElementById('COD_DEUDOR_H_0').innerHTML = '';
			document.getElementById('COD_DEUDOR_H_0').value = '';
		}
		else if (rut == ''){
			document.getElementById('RUT_0').innerHTML = '';
			document.getElementById('RUT_0').value = ''
			document.getElementById('RUT_0').focus();
			
			document.getElementById('NOMBRE_0').innerHTML = '';
			document.getElementById('NOMBRE_0').value = ''
			document.getElementById('DIG_VERIF_0').innerHTML = '';
			document.getElementById('DIG_VERIF_0').value = '';
			document.getElementById('COD_DEUDOR_H_0').innerHTML = '';
			document.getElementById('COD_DEUDOR_H_0').value = '';
			
		}
		else
		{
			var resp = ajax.responseText.split('|');
			document.getElementById('NOMBRE_0').innerHTML 		= resp[0];
			document.getElementById('NOMBRE_0').value 			= resp[0];
			document.getElementById('DIG_VERIF_0').innerHTML	= resp[1];
			document.getElementById('DIG_VERIF_0').value		= resp[1];
			document.getElementById('COD_DEUDOR_H_0').innerHTML = to_num(resp[2]);
			document.getElementById('COD_DEUDOR_H_0').value 	= to_num(resp[2]);
		}	
	}
}

function get_tipo_deuda(ve_cod_deuda_dd){
	document.getElementById('NRO_CUOTA_0').innerHTML    = '';
	document.getElementById('NRO_CUOTA_0').value 	    = '';
	document.getElementById('SALDO_CUOTA_0').innerHTML  = '';
	document.getElementById('SALDO_CUOTA_0').value 	    = '';
	
	var tabla = document.getElementById('ABONO');
	// borra todos los tr
	while (tabla.firstChild) {
	  tabla.removeChild(tabla.firstChild);
	}	
		
	var cod_deuda = ve_cod_deuda_dd.options[ve_cod_deuda_dd.selectedIndex].value;
	var simbolo = ve_cod_deuda_dd.options[ve_cod_deuda_dd.selectedIndex].dataset.dropdown;
	
	document.getElementById('COD_DEUDA_0').innerHTML 	= cod_deuda;
	document.getElementById('SIMBOLO_0').innerHTML 		= simbolo;
	
	/*la siguiente función se llama dos veces, una en "get_tipo_deuda" y la otra en "get_ano_cobro"
	la idea de los parametros es que indiquen si se desea buscar deuda exigible o no exigible (sin plan pago),
	ya que la función "select_ano_cobro" llama a un ajax que determina que select ejecutar
	*/
	select_ano_cobro (true);
	
}

function get_ano_cobro(ve_cod_plan_pago_pp){
	document.getElementById('NRO_CUOTA_0').innerHTML 	= '';
	document.getElementById('NRO_CUOTA_0').value 		= '';
	document.getElementById('SALDO_CUOTA_0').innerHTML 	= '';
	document.getElementById('SALDO_CUOTA_0').value 		= '';
	
	var nro_saldo	= ve_cod_plan_pago_pp.options[ve_cod_plan_pago_pp.selectedIndex].dataset.dropdown.split('|');	
			
	document.getElementById('NRO_CUOTA_0').innerHTML 	= nro_saldo[0];
	document.getElementById('SALDO_CUOTA_0').innerHTML 	= number_format(nro_saldo[1], 0, ',', '.');
	
	select_ano_cobro(false)
}

function select_ano_cobro(ve_no_exigible) {
	var tabla = document.getElementById('ABONO');
	
	// borra todos los tr
	while (tabla.firstChild) {
	  tabla.removeChild(tabla.firstChild);
	}

	var rut       		 = document.getElementById('RUT_0').value;
	var cod_deuda_dd     = document.getElementById('COD_DEUDA_DD_0').value;
	if (ve_no_exigible == true)
		var cod_plan_pago_pp = 9999;
	else
		var cod_plan_pago_pp = document.getElementById('COD_PLAN_PAGO_PP_0').value;

	ajax = nuevoAjax();
	ajax.open("GET", "load_abono.php?rut="+rut+"&cod_deuda_dd="+cod_deuda_dd+"&cod_plan_pago_pp="+cod_plan_pago_pp,false);
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
