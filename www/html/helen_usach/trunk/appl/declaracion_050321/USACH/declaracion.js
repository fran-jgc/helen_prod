function existe_rut() {
	var ajax = nuevoAjax();
	var rut_deudor = document.getElementById('RUT_0').value;
	var ano_declaracion = document.getElementById('ANO_DECLARACION_0').value;
	ajax.open("GET", "USACH/ajax_load_deudor.php?rut_deudor=" + rut_deudor + "&ano_declaracion=" + ano_declaracion, false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	var lista = resp.split('|');
	
	if (lista[0] == "VACIO") {
		// set_datos_deudor();
	} else if (lista[0] == "NO") {
		alert("El Deudor No Existe");
		set_datos_deudor();
		document.getElementById('val').style.display = 'none';	
	} 
	else if(lista[0] == "NO_POSEE_DEUDA_EXIGIBLE"){
		document.getElementById('val').style.display = 'none';
		alert("El Deudor ingresado no posee una deuda en estado Exigible.");
		set_datos_deudor();
	}
	else if(lista[0] == "NO_POSEE_DEUDA_CS_CSR"){
		document.getElementById('val').style.display = 'none';
		alert("El Deudor ingresado no posee una deuda tipo CS o CSR.");
		set_datos_deudor();
	}
	else if (lista[0] == "CONTIENE_DECLARACION") {
		document.getElementById('val').style.display = 'none';	
		set_datos_deudor();
		alert("El Deudor posee una declaracion Aprobada para el año " + ano_declaracion);
	}
	else if (lista[0] == "CARTERA_DIFERENTE") {
		
		alert('El Deudor tiene una deuda con otra Cartera distinta a la por defecto');
		set_datos_deudor();		
	} else {
		document.getElementById('RUT_NO_ING_0').innerHTML = rut_deudor;
		document.getElementById('COD_DEUDOR_0').value = lista[0];
		document.getElementById('DIG_VERIF_0').innerHTML = lista[1];
		document.getElementById('DIG_VERIF_NO_ING_0').innerHTML = lista[1];
		document.getElementById('NOMBRE_0').innerHTML = lista[2];
		document.getElementById('NOMBRE_NO_ING_0').innerHTML = lista[2];
		document.getElementById('DIRECCION_PARTICULAR_0').value = lista[3];
		document.getElementById('FECHA_DIRECCION_PARTICULAR_0').innerHTML = lista[4];

		set_value_dropdown('COD_PAIS_PARTICULAR_0', lista[7]);
		load_drop_down('declaracion', 'COD_PAIS_PARTICULAR_0', 'COD_REGION_PARTICULAR');
		set_value_dropdown('COD_REGION_PARTICULAR_0', lista[19]);
		load_drop_down('declaracion', 'COD_REGION_PARTICULAR_0', 'COD_CIUDAD_PARTICULAR');
		set_value_dropdown('COD_CIUDAD_PARTICULAR_0', lista[6]);
		load_drop_down('declaracion', 'COD_CIUDAD_PARTICULAR_0', 'COD_COMUNA_PARTICULAR');
		set_value_dropdown('COD_COMUNA_PARTICULAR_0', lista[5]);

		document.getElementById('EMAIL_PARTICULAR_0').value = lista[8];
		document.getElementById('TELEFONO_PARTICULAR_0').value = lista[9];
		document.getElementById('CELULAR_PARTICULAR_0').value = lista[10];
		document.getElementById('DIRECCION_COMERCIAL_0').value = lista[11];
		document.getElementById('FECHA_DIRECCION_COMERCIAL_0').innerHTML = lista[12];
		document.getElementById('EMAIL_COMERCIAL_0').value = lista[16];
		document.getElementById('TELEFONO_COMERCIAL_0').value = lista[17];
		document.getElementById('CELULAR_COMERCIAL_0').value = lista[18];
		document.getElementById('COD_REGION_COMERCIAL_0').value = lista[20];

		set_value_dropdown('COD_PAIS_COMERCIAL_0', lista[15]);
		load_drop_down('declaracion', 'COD_PAIS_COMERCIAL_0', 'COD_REGION_COMERCIAL');
		set_value_dropdown('COD_REGION_COMERCIAL_0', lista[20]);
		load_drop_down('declaracion', 'COD_REGION_COMERCIAL_0', 'COD_CIUDAD_COMERCIAL');
		set_value_dropdown('COD_CIUDAD_COMERCIAL_0', lista[14]);
		load_drop_down('declaracion', 'COD_CIUDAD_COMERCIAL_0', 'COD_COMUNA_COMERCIAL');
		set_value_dropdown('COD_COMUNA_COMERCIAL_0', lista[13]);
		
		CSR_menor_10_cuotas(rut_deudor);
	}
}

function mostrarOcultar_estado(ve_cod_estado_civil) {
	if(ve_cod_estado_civil.value==1){	// soltero
		document.getElementById('RUT_CONYUGE_0').readOnly = true;
		document.getElementById('NOM_CONYUGE_0').readOnly = true;
		document.getElementById('CONYUGE_ES_DEUDOR_0').disabled = true;
		document.getElementById('CONYUGE_DECLARA_0').disabled = true;
		document.getElementById('DIG_VERIF_CONYUGE_0').readOnly = true;
		document.getElementById('ENTREGA_F22_CONYUGE_0').disabled = true;
		document.getElementById('COD_AFP_CONYUGE_0').setAttribute('disabled', "");
		

		document.getElementById('RUT_CONYUGE_0').value = '';
		document.getElementById('DIG_VERIF_CONYUGE_0').value = '';
		document.getElementById('NOM_CONYUGE_0').value = '';
		document.getElementById('CONYUGE_ES_DEUDOR_0').checked = false;
		document.getElementById('CONYUGE_DECLARA_0').checked = false;
		document.getElementById('ENTREGA_F22_CONYUGE_0').checked = false;
		document.getElementById('COD_AFP_CONYUGE_0').value = '';
		
		for (i=0; i<6; i++) {		
			document.getElementById('MONTO_DEUDOR_PESOS1_' + i).readOnly = false;
			document.getElementById('MONTO_DEUDOR_PESOS2_' + i).readOnly = false;

			document.getElementById('MONTO_CONYUGE_PESOS1_' + i).readOnly = true;
			document.getElementById('MONTO_CONYUGE_PESOS2_' + i).readOnly = true;

			document.getElementById('MONTO_CONYUGE_PESOS1_' + i).value = '';
			document.getElementById('MONTO_CONYUGE_MONEDA1_' + i).innerHTML = '';
			document.getElementById('MONTO_CONYUGE_MONEDA1_H_' + i).value = 0;
	
			document.getElementById('MONTO_CONYUGE_PESOS2_' + i).value = '';
			document.getElementById('MONTO_CONYUGE_MONEDA2_' + i).innerHTML = '';
			document.getElementById('MONTO_CONYUGE_MONEDA2_H_' + i).value = 0;
	
			document.getElementById('MONTO_CONYUGE_PESOS_' + i).value = 0;
			document.getElementById('MONTO_CONYUGE_PESOS_H_' + i).value = 0;
			document.getElementById('MONTO_CONYUGE_MONEDA_' + i).value = 0;
			document.getElementById('MONTO_CONYUGE_MONEDA_H_' + i).value = 0;
		}
		document.getElementById('SUM_MONTO_CONYUGE_PESOS_0').innerHTML = null;
		document.getElementById('SUM_MONTO_CONYUGE_PESOS_H_0').value = null;
		document.getElementById('SUM_MONTO_CONYUGE_MONEDA_0').innerHTML = null;
		document.getElementById('SUM_MONTO_CONYUGE_MONEDA_H_0').value = null;
	}
	else if(ve_cod_estado_civil.value==2){	// casado
		document.getElementById('RUT_CONYUGE_0').readOnly = false;
		document.getElementById('NOM_CONYUGE_0').readOnly = false;
		document.getElementById('CONYUGE_ES_DEUDOR_0').disabled = false;
		document.getElementById('CONYUGE_DECLARA_0').disabled = false;
		document.getElementById('DIG_VERIF_CONYUGE_0').readOnly = false;
		document.getElementById('ENTREGA_F22_CONYUGE_0').disabled = false;
		document.getElementById('COD_AFP_CONYUGE_0').removeAttribute('disabled');

		for (i=0; i<6; i++) {
			document.getElementById('MONTO_DEUDOR_PESOS1_' + i).readOnly = false;
			document.getElementById('MONTO_DEUDOR_PESOS2_' + i).readOnly = false;

			document.getElementById('MONTO_CONYUGE_PESOS1_' + i).readOnly = true;
			document.getElementById('MONTO_CONYUGE_PESOS2_' + i).readOnly = true;

			document.getElementById('MONTO_CONYUGE_PESOS1_' + i).value = '';
			document.getElementById('MONTO_CONYUGE_MONEDA1_' + i).innerHTML = '';
			document.getElementById('MONTO_CONYUGE_MONEDA1_H_' + i).value = 0;
	
			document.getElementById('MONTO_CONYUGE_PESOS2_' + i).value = '';
			document.getElementById('MONTO_CONYUGE_MONEDA2_' + i).innerHTML = '';
			document.getElementById('MONTO_CONYUGE_MONEDA2_H_' + i).value = 0;
	
			document.getElementById('MONTO_CONYUGE_PESOS_' + i).value = 0;
			document.getElementById('MONTO_CONYUGE_PESOS_H_' + i).value = 0;
			document.getElementById('MONTO_CONYUGE_MONEDA_' + i).value = 0;
			document.getElementById('MONTO_CONYUGE_MONEDA_H_' + i).value = 0;
		}
	}
}
