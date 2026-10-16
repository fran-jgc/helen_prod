document.getElementById('COD_ESTADO_REPRO_RCS_0').onClick('return dlg_print();');
function validate(){
	
	cod_estado_repro	=	document.getElementById('COD_ESTADO_REPRO_RCS_0').value;
	if(cod_estado_repro == 2)
	{
		var recibido_docto	= document.getElementById('RECIBIDO_DOCTO_0').checked;
		
		if(recibido_docto == false){
			alert('Debe recibir Pagaré antes de confirmar');
			return false;
		}

		var estado_cuota_pago_inicial= 	document.getElementById('COD_ESTADO_PP_H_0').value;
		var K_CUOTA_PAGADA = 3;
		
		if(estado_cuota_pago_inicial != K_CUOTA_PAGADA){
			alert('La cuota N° 0 del Pago inicial debe estar PAGADA');
			return false;
		}
		else
		{
			return true;
		}
	}
	else if(cod_estado_repro == 3){
		monto_ingreso_p		=	document.getElementById('MONTO_INGRESO_P_0').innerHTML;
		
		if(monto_ingreso_p != ""){
			alert('No se puede anular ya que contiene un Ingreso de caja');
			return false;
		}
	}
		
	if(!valida_monto())
	{
		return false;
	}
}
function carga_deuda(ve_rut){
	document.getElementById('RUT_H_0').value = ve_rut.value;

	var nom_tabla = 'repro_rcs';
	var aTR = get_TR('DEUDA_REPRO');
	for (i=0; i<aTR.length; i++){
		var rec_tr = get_num_rec_field(aTR[i].id);
		del_line('DEUDA_REPRO_'+rec_tr, nom_tabla);
	}
	
	var vl_ajax = nuevoAjax();	

	vl_ajax.open("GET", "ajax_carga_deuda.php?rut="+ve_rut.value+"&opcion=es_candidato",false);
	vl_ajax.send(null);
	var vl_resp_es_candidato = URLDecode(vl_ajax.responseText);
	var es_candidato = eval("(" + vl_resp_es_candidato + ")");
	if(es_candidato == 'S')
	{
		var vl_ajax = nuevoAjax();	
		vl_ajax.open("GET", "ajax_carga_deuda.php?rut="+ve_rut.value+"&opcion=deudor",false);
		vl_ajax.send(null);
		
		var vl_resp_deudor = URLDecode(vl_ajax.responseText);
		var vl_deudor = eval("(" + vl_resp_deudor + ")");
	
		document.getElementById('NOM_DEUDOR_0').innerHTML 		= URLDecode(vl_deudor[0]['NOMBRE']);
		document.getElementById('COD_DEUDOR_0').value 			= vl_deudor[0]['COD_DEUDOR'];
		
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		var vl_ajax = nuevoAjax();	
		vl_ajax.open("GET", "ajax_carga_deuda.php?rut="+ve_rut.value+"&opcion=deudas",false);
		vl_ajax.send(null);
		
		var vl_resp = URLDecode(vl_ajax.responseText);

		var vl_result = eval("(" + vl_resp + ")");
		
		var vl_saldo_capital 	= 0;
		var vl_saldo_capital_p 	= 0;
		var vl_saldo_multa 		= 0;
		var vl_saldo_multa_p 	= 0;
		var vl_saldo_total 		= 0;
		var vl_saldo_total_p 	= 0; 
		
		
		for(i=0;i<vl_result.length;i++){
			var vl_row = add_line('DEUDA_REPRO', 'repro_rcs');
			
			document.getElementById('RD_COD_DEUDA_' + vl_row).innerHTML 		= vl_result[i]['COD_DEUDA'];
			document.getElementById('RD_TIPO_DEUDA_' + vl_row).innerHTML 		= vl_result[i]['COD_TIPO_DEUDA'];
			document.getElementById('RD_NOM_UNIVERSIDAD_' + vl_row).innerHTML 	= vl_result[i]['NOM_UNIVERSIDAD'];
			document.getElementById('RD_SALDO_CAPITAL_' + vl_row).innerHTML 	= number_format(vl_result[i]['SALDO_CAPITAL'], 4, ',', '.');
			document.getElementById('RD_SALDO_CAPITAL_P_' + vl_row).innerHTML 	= number_format(vl_result[i]['SALDO_CAPITAL_P'], 0, ',', '.');
			document.getElementById('RD_SALDO_MULTA_' + vl_row).innerHTML 		= number_format(vl_result[i]['SALDO_MULTA'], 4, ',', '.');
			document.getElementById('RD_SALDO_MULTA_P_' + vl_row).innerHTML 	= number_format(vl_result[i]['SALDO_MULTA_P'], 0, ',', '.');
			document.getElementById('RD_SALDO_TOTAL_' + vl_row).innerHTML 		= number_format(vl_result[i]['SALDO_TOTAL'], 4, ',', '.');
			document.getElementById('RD_SALDO_TOTAL_P_' + vl_row).innerHTML 	= number_format(vl_result[i]['SALDO_TOTAL_P'], 0, ',', '.');
			
			//////////////// se setean variables hidden ///////////////////////////////////////////////////////////////////
			document.getElementById('D_COD_DEUDA_H_'+ vl_row).value		= number_format(vl_result[i]['COD_DEUDA'], 4, '.', '');
			document.getElementById('D_SALDO_CAPITAL_H_'+ vl_row).value	= number_format(vl_result[i]['SALDO_CAPITAL'], 4, '.', '');
			document.getElementById('D_SALDO_MULTA_H_'+ vl_row).value	= number_format(vl_result[i]['SALDO_MULTA'], 4, '.', '');
			
			
			vl_saldo_capital 	= parseFloat(vl_saldo_capital) 		+ parseFloat(vl_result[i]['SALDO_CAPITAL']);
			vl_saldo_capital_p 	= parseFloat(vl_saldo_capital_p) 	+ parseFloat(vl_result[i]['SALDO_CAPITAL_P']);
			vl_saldo_multa 		= parseFloat(vl_saldo_multa) 		+ parseFloat(vl_result[i]['SALDO_MULTA']);
			vl_saldo_multa_p 	= parseFloat(vl_saldo_multa_p) 		+ parseFloat(vl_result[i]['SALDO_MULTA_P']);
			vl_saldo_total 		= parseFloat(vl_saldo_total) 		+ parseFloat(vl_result[i]['SALDO_TOTAL']);
			vl_saldo_total_p 	= parseFloat(vl_saldo_total_p) 		+ parseFloat(vl_result[i]['SALDO_TOTAL_P']);
			
			 
		}
		
		document.getElementById('SUM_RD_SALDO_CAPITAL_0').innerHTML 	= number_format(vl_saldo_capital, 4, ',', '.');
		document.getElementById('SUM_RD_SALDO_CAPITAL_P_0').innerHTML 	= number_format(vl_saldo_capital_p, 0, ',', '.');
		document.getElementById('SUM_RD_SALDO_MULTA_0').innerHTML 		= number_format(vl_saldo_multa, 4, ',', '.');
		document.getElementById('SUM_RD_SALDO_MULTA_P_0').innerHTML 	= number_format(vl_saldo_multa_p, 0, ',', '.');
		document.getElementById('SUM_RD_SALDO_TOTAL_0').innerHTML 		= number_format(vl_saldo_total, 4, ',', '.');
		document.getElementById('SUM_RD_SALDO_TOTAL_P_0').innerHTML 	= number_format(vl_saldo_total_p, 0, ',', '.');
	}
	else if(es_candidato == 'N')
	{
		alert('El deudor no es candidato');
	}
	else
	{
		document.getElementById('SUM_RD_SALDO_CAPITAL_0').innerHTML 	= '';
		document.getElementById('SUM_RD_SALDO_CAPITAL_P_0').innerHTML 	= '';
		document.getElementById('SUM_RD_SALDO_MULTA_0').innerHTML 		= '';
		document.getElementById('SUM_RD_SALDO_MULTA_P_0').innerHTML 	= '';
		document.getElementById('SUM_RD_SALDO_TOTAL_0').innerHTML 		= '';
		document.getElementById('SUM_RD_SALDO_TOTAL_P_0').innerHTML 	= '';
		alert(es_candidato);
	}
}
function repro_rcs(ve_campo) {
	var vl_fecha_repro 			= document.getElementById('FECHA_REPRO_RCS_0').innerHTML;
	var vl_saldo_capital 		= document.getElementById('SUM_RD_SALDO_CAPITAL_0').innerHTML;
	var vl_saldo_multa 			= document.getElementById('SUM_RD_SALDO_MULTA_0').innerHTML;
	var vl_porc_pago_inicial 	= document.getElementById('PORC_PAGO_INICIAL_0').value;
	var vl_monto_pago_inicial 	= document.getElementById('MONTO_PAGO_INICIAL_0').value;
	var vl_monto_pago_inicial_p	= document.getElementById('MONTO_PAGO_INICIAL_P_0').value;
	var vl_cant_cuota			= document.getElementById('CANT_CUOTA_0').value;
	//se reemplazan los puntos y comas segun corresponda y se convierte las variables en float
	if(vl_saldo_capital != null || vl_saldo_multa != null)
	{
		vl_saldo_capital 		= vl_saldo_capital.replace(',','.', 'g');
		vl_saldo_multa			= vl_saldo_multa.replace(',','.', 'g');
		vl_monto_pago_inicial	= vl_monto_pago_inicial.replace(',','.', 'g');
		vl_monto_pago_inicial_p = vl_monto_pago_inicial_p.replace('.','', 'g');

		vl_saldo_capital  		= parseFloat(vl_saldo_capital);	
		vl_saldo_multa			= parseFloat(vl_saldo_multa);
		vl_monto_pago_inicial	= parseFloat(vl_monto_pago_inicial);
		vl_monto_pago_inicial_p	= parseFloat(vl_monto_pago_inicial_p);
	}
	if(vl_porc_pago_inicial != null)
	{
		vl_porc_pago_inicial	= vl_porc_pago_inicial.replace(',','.', 'g');
	}
	else
	{
		vl_porc_pago_inicial = 10;
	}
	
	var vl_ajax = nuevoAjax();
	
	vl_ajax.open("GET", "ajax_repro_rcs.php?campo="+ve_campo.id+
											"&fecha_repro_rcs="+vl_fecha_repro+
											"&saldo_capital="+vl_saldo_capital+
											"&saldo_multa="+vl_saldo_multa+
											"&porc_pago_inicial="+vl_porc_pago_inicial+
											"&monto_pago_inicial="+vl_monto_pago_inicial+
											"&monto_pago_inicial_p="+vl_monto_pago_inicial_p+
											"&cant_cuota="+vl_cant_cuota
											,false);
	vl_ajax.send(null);

	var vl_resp_deudor = URLDecode(vl_ajax.responseText);

	//elimina las " en la variable vl_resp_deudor
	vl_resp_deudor = vl_resp_deudor.replace(/"/gi, "");

	var vl_lista = vl_resp_deudor.split('|');
	
	var vl_saldo_total	= document.getElementById('SUM_RD_SALDO_TOTAL_0').innerHTML;

	if(vl_lista[14] == '')
	{
		if (vl_saldo_total == 0 || vl_saldo_total == '')
		{
			document.getElementById('MONTO_PAGO_INICIAL_0').value			= '';
			document.getElementById('MONTO_PAGO_INICIAL_P_0').value			= '';
			document.getElementById('PORC_DSCTO_MULTA_0').innerHTML			= '';
			document.getElementById('MONTO_DSCTO_MULTA_0').innerHTML		= '';
			document.getElementById('MONTO_DSCTO_MULTA_P_0').innerHTML		= '';
			document.getElementById('MONTO_A_REPROGRAMAR_0').innerHTML		= '';
			document.getElementById('MONTO_A_REPROGRAMAR_P_0').innerHTML	= '';
			document.getElementById('MONTO_CUOTA_0').innerHTML				= '';
			document.getElementById('MONTO_CUOTA_P_0').innerHTML			= '';
			document.getElementById('PORC_PAGO_INICIAL_0').value			= '';
			
			var vl_cant_cuota = document.getElementById('CANT_CUOTA_0');
			vl_largo_asig = vl_lista[10];
			//se limpia el drop down
			var vl_largo_dd = vl_cant_cuota.options.length;
			for (var i=0; i < vl_largo_dd; i++) {
				vl_cant_cuota.remove(0); 
			}
			vl_option = new Option(0,0); 
			vl_cant_cuota.appendChild(vl_option);
		}
		else
		{
			if (ve_campo.id != 'CANT_CUOTA_0')
			{
				document.getElementById('MONTO_PAGO_INICIAL_0').value			= vl_lista[0];
				document.getElementById('MONTO_PAGO_INICIAL_P_0').value			= vl_lista[1];
				document.getElementById('PORC_DSCTO_MULTA_0').innerHTML			= vl_lista[2];
				document.getElementById('MONTO_DSCTO_MULTA_0').innerHTML		= vl_lista[3];
				document.getElementById('MONTO_DSCTO_MULTA_P_0').innerHTML		= vl_lista[4];
				document.getElementById('MONTO_A_REPROGRAMAR_0').innerHTML		= vl_lista[5];
				document.getElementById('MONTO_A_REPROGRAMAR_P_0').innerHTML	= vl_lista[6];
				document.getElementById('MONTO_CUOTA_0').innerHTML				= vl_lista[7];
				document.getElementById('MONTO_CUOTA_P_0').innerHTML			= vl_lista[8];
				document.getElementById('PORC_PAGO_INICIAL_0').value			= vl_lista[9];
				
				//////////////// se setean variables hidden ////////////////////////////////////////////////////////////////
				document.getElementById('PORC_DSCTO_MULTA_H_0').value		= vl_lista[2];
				document.getElementById('MONTO_DSCTO_MULTA_H_0').value		= vl_lista[3];
				document.getElementById('MONTO_A_REPROGRAMAR_H_0').value	= vl_lista[5];
				document.getElementById('MONTO_CUOTA_H_0').value			= vl_lista[7];
				
				/////////////// se muestran los mensajes segun la regla de negocio
				if(vl_lista[12] != '')
					alert(vl_lista[12]);
				if(vl_lista[13] != '')
					alert(vl_lista[13]);
			}
			else
			{
				document.getElementById('MONTO_CUOTA_0').innerHTML				= vl_lista[7];
				document.getElementById('MONTO_CUOTA_P_0').innerHTML			= vl_lista[8];
				
				document.getElementById('PORC_DSCTO_MULTA_H_0').value		= vl_lista[2];
				document.getElementById('MONTO_DSCTO_MULTA_H_0').value		= vl_lista[3];
				document.getElementById('MONTO_A_REPROGRAMAR_H_0').value	= vl_lista[5];
				document.getElementById('MONTO_CUOTA_H_0').value			= vl_lista[7];
				
			}
			
			/////////////////////////////// SE CARGA EL DROP DOWN CANT_CUOTA ///////////////////////////////////////////
			//se asigna a una variable el drop down
			var vl_cant_cuota = document.getElementById('CANT_CUOTA_0');
			vl_largo_asig = vl_lista[10];
			//se limpia el drop down
			var vl_largo_dd = vl_cant_cuota.options.length;
			for (var i=0; i < vl_largo_dd; i++) {
				vl_cant_cuota.remove(0); 
			}
			//se llena el drop down
			for (var i=0; i < vl_largo_asig; i++) {
				vl_option = new Option(vl_largo_asig-i,vl_largo_asig-i); 
				vl_cant_cuota.appendChild(vl_option); 
			}
			
			
			if(vl_lista[11] == 0)
				document.getElementById('CANT_CUOTA_0').value = vl_largo_asig;
			else
				document.getElementById('CANT_CUOTA_0').value = vl_lista[11];
				
			if(ve_campo.id != 'CANT_CUOTA_0')
				document.getElementById('CANT_CUOTA_0').value = vl_largo_asig;
			/////////////////////////////// FIN DE CARGA DROP DOWN CANT_CUOTA //////////////////////////////////////////
			//set_var_hidden();
		}
	}
	else
	{
		alert(vl_lista[14]);
		document.getElementById('MONTO_PAGO_INICIAL_0').value			= '';
		document.getElementById('MONTO_PAGO_INICIAL_P_0').value			= '';
		document.getElementById('PORC_DSCTO_MULTA_0').innerHTML			= '';
		document.getElementById('MONTO_DSCTO_MULTA_0').innerHTML		= '';
		document.getElementById('MONTO_DSCTO_MULTA_P_0').innerHTML		= '';
		document.getElementById('MONTO_A_REPROGRAMAR_0').innerHTML		= '';
		document.getElementById('MONTO_A_REPROGRAMAR_P_0').innerHTML	= '';
		document.getElementById('MONTO_CUOTA_0').innerHTML				= '';
		document.getElementById('MONTO_CUOTA_P_0').innerHTML			= '';
		document.getElementById('PORC_PAGO_INICIAL_0').value			= '';
		
		var vl_cant_cuota = document.getElementById('CANT_CUOTA_0');
		vl_largo_asig = vl_lista[10];
		//se limpia el drop down
		var vl_largo_dd = vl_cant_cuota.options.length;
		for (var i=0; i < vl_largo_dd; i++) {
			vl_cant_cuota.remove(0); 
		}
		vl_option = new Option(0,0); 
		vl_cant_cuota.appendChild(vl_option);
	}
}
function valida_monto()
{
	var vl_rut = document.getElementById('RUT_H_0').value; 
	var monto = document.getElementById('MONTO_PAGO_INICIAL_0').value;
	
	var vl_ajax = nuevoAjax();	
	vl_ajax.open("GET", "ajax_valida_monto.php?rut="+vl_rut+"&monto="+monto,false);
	vl_ajax.send(null);
	
	var vl_resp_deudor = URLDecode(vl_ajax.responseText);
	var vl_valida = eval("(" + vl_resp_deudor + ")");
	
	if(vl_valida == 'N')
	{
		alert('Caso especial, favor contáctese con integrasystem');
		return false;
	}
	return true;
}
function dlg_print() {
	
	var url = "dlg_print.php";
	$.showModalDialog({
		 url: url,
		 dialogArguments: '',
		 height: 240,
		 width: 370,
		 scrollable: false,
		 onClose: function(){ 
		 	var returnVal = this.returnValue;
		 	if (returnVal == null){		
				return false;
			}			
			else {
				var input = document.createElement("input");
				input.setAttribute("type", "hidden");
				input.setAttribute("name", "b_print_x");
				input.setAttribute("id", "b_print_x");
				document.getElementById("input").appendChild(input);
				
				document.getElementById('wi_hidden').value = returnVal;
				document.input.submit();
		   		return true;
			}
		}
	});
	return false;
}
function borra_matricula(){
	// se implementa en function carga_deuda para UTEM    
	
}