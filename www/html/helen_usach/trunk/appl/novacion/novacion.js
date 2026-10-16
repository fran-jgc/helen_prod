function validate() {
	var obs = document.input.OBS_0.value;
	if (obs == '') {
		alert('Debe ingresar "Observaciones" antes de grabar');
		return false;
	}
	return true;
}
function set_datos_deudor() {
	set_value('DIG_VERIF_0', '', '');
	set_value('NOMBRE_0', '', '');
	set_value('COD_DEUDA_0', '', '');
	set_value('COD_DEUDA_H_0', '', '');
	set_value('COD_TIPO_DEUDA_0', '', '');
	set_value('SIMBOLO_0', '', '');
	set_value('NOM_CARTERA_0', '', '');
	set_value('NOM_ESTADO_DEUDA_0', '', '');
	set_value('CANT_CUOTA_0', '', '');
	set_value('FECHA_INICIO_COBRO_0', '', '');
	set_value('MONTO_INICIO_COBRO_0', '', '');
	set_value('CAT_0', '', '');
	set_value('MONTO_SVP_0', '', '');
	set_value('MONTO_SVP_H_0', '', '');
	set_value('MONTO_MORA_0', '', '');
	set_value('MONTO_MORA_H_0', '', '');
	set_value('MONTO_CP_0', '', '');
	set_value('MONTO_CP_H_0', '', '');
	set_value('MONTO_LP_0', '', '');
	set_value('MONTO_LP_H_0', '', '');
	set_value('TOTAL_PESOS_0', '', '');
}

function muestra_datos(lista){
		document.getElementById('DIG_VERIF_0').innerHTML = lista[1];
		document.getElementById('NOMBRE_0').innerHTML = lista[2];
		document.getElementById('COD_DEUDA_0').innerHTML = lista[3];
		document.getElementById('COD_DEUDA_H_0').value = lista[3];
		document.getElementById('COD_TIPO_DEUDA_0').innerHTML = lista[4];
		document.getElementById('SIMBOLO_0').innerHTML = lista[5];
		document.getElementById('NOM_CARTERA_0').innerHTML = lista[6];
		document.getElementById('NOM_ESTADO_DEUDA_0').innerHTML = lista[7];
		document.getElementById('CANT_CUOTA_0').innerHTML = lista[8];
		document.getElementById('FECHA_INICIO_COBRO_0').innerHTML = lista[9];
		
		//
		document.getElementById('MONTO_INICIO_COBRO_0').innerHTML = lista[10]; //number_format(lista[9], 4, ',', '.');
		document.getElementById('CAT_0').innerHTML = lista[11]; //number_format(lista[10],4, ',', '.');
		document.getElementById('MONTO_SVP_0').innerHTML = lista[12]; //number_format(to_num(lista[11]), 0, ',', '.');
		document.getElementById('MONTO_SVP_H_0').value = lista[12]; //to_num(lista[11]);
		document.getElementById('MONTO_MORA_0').innerHTML = lista[13]; //number_format(to_num(lista[12]), 0, ',', '.');
		document.getElementById('MONTO_MORA_H_0').value = lista[13]; //to_num(lista[12]);
		document.getElementById('MONTO_CP_0').innerHTML = lista[14]; //number_format(to_num(lista[13]), 0, ',', '.');
		document.getElementById('MONTO_CP_H_0').value = lista[14]; //to_num(lista[13]);
		document.getElementById('MONTO_LP_0').innerHTML = lista[15]; //number_format(to_num(lista[14]), 0, ',', '.');
		document.getElementById('MONTO_LP_H_0').value = lista[15]; //to_num(lista[14]);
		document.getElementById('TOTAL_PESOS_0').innerHTML = lista[16]; //number_format(to_num(lista[15]), 0, ',', '.');
}

function get_datos_deudor() {

	var rut = document.getElementById('RUT_0').value;
	rut = replaceChars(rut);
	if(rut != ''){
		var ajax = nuevoAjax();
		ajax.open("GET", "ajax_obtiene_datos_deuda.php?rut_deudor="+rut, false);
		ajax.send(null);
		var resp = URLDecode(ajax.responseText);
		var lista = resp.split('|');

		if (lista[0] == "NO_EXISTE_DEUDOR"){
			alert("El Deudor No Existe");
		} else if (lista[0] == "SIN_CS") {
			alert("El Deudor no tiene deuda CS");
		} else if (lista[0] == "SIN_NO_EXIGIBLE") {
			alert("El Deudor tiene deuda CS, pero distinta a EXIGIBLE y NO EXIGIBLE");
		} else if (lista[0] == "SIN_DEUDAS_POR_NOVAR") {
			alert("El Deudor no tiene deuda para Novaci\u00f3n");
		} else if (lista[0] == "NOVACION_DEUDA_RECHAZADA") {
			muestra_datos(lista);
			alert("La deuda fue Registrada en Novacion Cod. "+lista[17]+", Estado: "+lista[18]);
		} else if (lista[0] == "CARTERA_DIFERENTE") {
			alert("El Deudor tiene una deuda con otra Cartera distinta a la por defecto");
			set_value('RUT_0', '', '');
			document.getElementById('RUT_0').focus();
		} else if (lista[0] == "NOVAR_DEUDA") {
			muestra_datos(lista);
		} else if (lista[0]== "VARIAS_DEUDAS"){
			var args = "location:no;dialogLeft:250px;dialogTop:300px;dialogWidth:450px;dialogHeight:200px;dialogLocation:0;Toolbar:no;";
			var returnVal = window.showModalDialog("dlg_mas_deudas.php?rut_deudor="+rut, "_blank", args);
			if(returnVal != null){
				muestra_datos(returnVal);
			}else{
				document.getElementById('RUT_0').value = null;
				document.getElementById('RUT_0').focus();
				set_datos_deudor();
			}
		}
	}else{
		alert("Debe ingresar un rut");
		document.getElementById('RUT_0').focus();
		set_datos_deudor();
	}
}

function f_usuario_confirma(ve_nom_usuario, ve_fecha) {

	document.getElementById('NOM_USUARIO_CONFIRMA_0').innerHTML = '';
	document.getElementById('NOM_USUARIO_CONFIRMA_0').value = '';
	document.getElementById('FECHA_CONFIRMA_0').innerHTML = '';
	document.getElementById('FECHA_CONFIRMA_0').value = '';

	var est_novacion = to_num(document.getElementById('COD_ESTADO_NOVACION_0').value);

	if (est_novacion == 2) {
		document.getElementById('NOM_USUARIO_CONFIRMA_0').innerHTML = ve_nom_usuario;
		document.getElementById('NOM_USUARIO_CONFIRMA_0').value = ve_nom_usuario;

		document.getElementById('FECHA_CONFIRMA_0').innerHTML = ve_fecha;
		document.getElementById('FECHA_CONFIRMA_0').value = ve_fecha;
	}
}
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