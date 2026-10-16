function f_get_deuda_user() {
	var existe_deudor = document.getElementById('EXISTE_0').value;
	if (existe_deudor == 'S') {
		var rut = document.getElementById('RUT_0').value;
		var tipo_deuda = document.getElementById('COD_TIPO_DEUDA_0');
		tipo_deuda = tipo_deuda.options[tipo_deuda.selectedIndex].value;
		var ajax = nuevoAjax()

		ajax.open("GET", "ajax_obtiene_deuda_usuario.php?rut=" + rut
				+ "&tipo_deuda=" + tipo_deuda, false);

		ajax.send(null);
		if (ajax.readyState == 4) {
			var existe = URLDecode(ajax.responseText);
			var resp = ajax.responseText;

			if (existe == 0) {
				alert('No existe Deudas para ese tipo de deuda y Deudor.');
				document.getElementById('COD_DEUDA_0').innerHTML = '';
				document.getElementById('COD_DEUDA_0').value = '';
				document.getElementById('COD_DEUDA_H_0').innerHTML = '';
				document.getElementById('COD_DEUDA_H_0').value = '';
			} else {
				document.getElementById('COD_DEUDA_0').innerHTML = resp;
				document.getElementById('COD_DEUDA_0').value = resp;
				document.getElementById('COD_DEUDA_H_0').innerHTML = resp;
				document.getElementById('COD_DEUDA_H_0').value = resp;
			}
		}
	}
}

function muestra_datos(lista){
	document.getElementById('DIG_VERIF_0').innerHTML = lista[1];
	document.getElementById('NOMBRE_0').innerHTML = lista[2];
	document.getElementById('COD_TIPO_DEUDA_0').innerHTML = lista[3];
	document.getElementById('COD_TIPO_DEUDA_H_0').value = lista[3];
	document.getElementById('COD_DEUDA_0').innerHTML = lista[4];
	document.getElementById('COD_DEUDA_H_0').value = lista[4];
	document.getElementById('COD_DEUDOR_0').value = lista[5];
	// marcar que existe
	document.getElementById('EXISTE_0').innerHTML = 'S';
}


function f_get_info_deudor() {
	// RESPUESTAS DEL AJAX:
	// O = EL RUT NO EXISTE EN TABLA DEUDOR (PUEDE INGRESARLO)
	// 1 = EL RUT EXISTE
	// ABONO = deuda posee abonos por lo tanto no se puede
	var rut = document.getElementById('RUT_0').value;
	var ano_h = document.getElementById('ANO_SUSPENSION_H_0').value;
	rut = replaceChars(rut);

	if(rut != ''){
		var ajax = nuevoAjax();
		ajax.open("GET", "ajax_info_deudor.php?rut=" + rut+"&ano="+ano_h, false);
		ajax.send(null);
		var resp = URLDecode(ajax.responseText);
		var lista = resp.split('|');
		if (lista[0] == 'NO_EXISTE_RUT') {
			alert('No existe un Deudor con ese Rut.');
			setea_null_deudor();
			document.getElementById('RUT_0').focus();
		} else if (lista[0] == 'DEUDA_NO_VIG') {
			alert('Deudor no posee deuda(s) Vigente(s).');
			setea_null_deudor();
			document.getElementById('RUT_0').focus();
			
		} else if (lista[0] == 'CARTERA_DIFERENTE') {
			alert('El Deudor tiene una deuda con otra Cartera distinta a la por defecto.');
			setea_null_deudor();
			document.getElementById('RUT_0').focus();
			
		} else if (lista[0] == 'DEUDA_NO_CS_CSR') {
			alert('Deudor no posee CS , CSR , CR12,  CRI o CAME.');
			setea_null_deudor();
			document.getElementById('RUT_0').focus();
		} else if (lista[0] == 'SIN_DEUDAS_PARA_SUSPENSION') {
			alert('Deudor sin Deudas por Suspender del año '+ano_h+'.');
			set_value('ANO_SUSPENSION_0', null, null);
			//set_value('ANO_SUSPENSION_H_0', null, null);
			document.getElementById('ANO_SUSPENSION_0').focus();
			document.getElementById('DIG_VERIF_0').innerHTML = lista[1];
			document.getElementById('NOMBRE_0').innerHTML = lista[2];
		} else if (lista[0] == 'SUSPENSION_DEUDA_RECHAZADA') {
			alert("La deuda fue Registrada en Suspension Cod. "+lista[5]+", Estado: "+lista[6]);
			muestra_datos(lista);
		} else if (lista[0] == 'SUSPENSION_DEUDA_NUEVA') {
			muestra_datos(lista);			
		} else if (lista[0] == 'VARIAS_DEUDAS') {
			//FALTA IMPLEMENTAR CUANDO SON VARIAS DEUDAS A SUSPENDER		
		}
	
	
}else{
		alert("Debe ingresar un rut.");
		document.getElementById('RUT_0').focus();
		setea_null_deudor();
	}
}

function f_valida_ano(ve_ano, ve_ano_actual) {
	if(ve_ano.value != ''){
		var rut = document.getElementById('RUT_0').value;
		rut = replaceChars(rut);
		
		if (rut != '') {
			var ano = ve_ano.value;
			if (ano < 1970) {
				alert('A\u00f1o invalido, debe ser mayor.');
				document.getElementById('COD_DEUDA_0').innerHTML = null;
				document.getElementById('COD_DEUDOR_0').innerHTML = null;
				document.getElementById('COD_TIPO_DEUDA_0').innerHTML = null;
				document.getElementById('COD_TIPO_DEUDA_0').value = null;
				ve_ano.value = '';
				ve_ano.focus();
				return false;
			} else if (ano > ve_ano_actual) {
				alert('El A\u00f1o a suspender no puede ser MAYOR al A\u00f1o actual.');
				document.getElementById('COD_DEUDA_0').innerHTML = null;
				document.getElementById('COD_DEUDOR_0').innerHTML = null;
				document.getElementById('COD_TIPO_DEUDA_0').innerHTML = null;
				document.getElementById('COD_TIPO_DEUDA_0').value = null;
				ve_ano.value = '';
				ve_ano.focus();
				return false;
			}else{
				var ajax = nuevoAjax()
				ajax.open("GET", "ajax_ano_a_suspender.php?rut=" + rut + "&ano=" + ano, false);
				ajax.send(null);
				var resp = URLDecode(ajax.responseText);
			
				if (resp == 'TIENE_ABONO') {
					alert('Deudor posee abonos pendientes, no se puede realizar suspensi\u00f3n.');
					setea_null_deudor();
					document.getElementById('RUT_0').focus();
				} else if (resp == 'SIN_CAPITAL') {
					alert('El Monto Capital del Plan de Pago es Cero.');
					document.getElementById('ANO_SUSPENSION_0').value = '';
					document.getElementById('ANO_SUSPENSION_0').focus();
					return false;
				} else if (resp == 'ANO_OK'){
					//A�O CORRECTO
					document.getElementById('ANO_SUSPENSION_H_0').value = ano;
					document.getElementById('ANO_SUSPENSION_0').value = ano;
					f_get_info_deudor();
					return true;
				}
			}
		}else{
			alert('Ingrese RUT antes de ingresar A\u00f1o.');
			document.getElementById('ANO_SUSPENSION_0').value = '';
			document.getElementById('RUT_0').focus();
			return false;
		}
	}else{
		alert("Debe ingresar el A\u00f1o a Suspender.");
		document.getElementById('ANO_SUSPENSION_0').focus();
		setea_null_deudor();
		return false;
	}
}

function setea_null_deudor() {
	document.getElementById('RUT_0').value = null;
	document.getElementById('DIG_VERIF_0').innerHTML = null;
	document.getElementById('NOMBRE_0').innerHTML = null;
	document.getElementById('COD_DEUDA_0').innerHTML = null;
	document.getElementById('COD_DEUDOR_0').innerHTML = null;
	document.getElementById('COD_TIPO_DEUDA_0').innerHTML = null;
	document.getElementById('COD_TIPO_DEUDA_0').value = null;
	document.getElementById('ANO_SUSPENSION_0').value = null;
	document.getElementById('ANO_SUSPENSION_0').innerHTML = null;
	document.getElementById('COD_DEUDOR_0').innerHTML = null;
	
}
function load_con_ajaxx(nom_tabla, selectDestino, id_destino,
		opcionSeleccionada) {
	// 'suspension',document.getElementById('COD_TIPO_DEUDA_0'),
	// 'COD_TIPO_DEUDA_0', rut deudor
	// Obtengo el elemento del select que debo cargar
	// Creo el nuevo objeto AJAX y envio al servidor el ID del select a cargar y
	// la opcion seleccionada del select origen

	var rut = URLEncode(opcionSeleccionada);
	var ajax = nuevoAjax();

	var fecha_actual = document.getElementById('FECHA_SUSPENSION_H_0').value;
	var fragmentoTexto = fecha_actual.split('/');
	var ano = fragmentoTexto[5];

	ajax.open("GET", "ajax_obtiene_tipo_deuda.php?rut=" + rut + "&nom_tabla="
			+ nom_tabla + "&select=" + id_destino + "&ano=" + ano, false);
	ajax.send(null);

	resp = ajax.responseText;

	if (resp != '') {
		if (ajax.readyState == 1) {
			// Mientras carga elimino la opcion "Selecciona Opcion..." y pongo
			// una que dice "Cargando..."
			selectDestino.length = 0;
			var nuevaOpcion = document.createElement("option");
			nuevaOpcion.value = 0;
			nuevaOpcion.innerHTML = "Cargando...";
			selectDestino.appendChild(nuevaOpcion);
			selectDestino.disabled = true;
		}
		if (ajax.readyState == 4) {
			var existe = URLDecode(ajax.responseText);
			if (existe == 0) {
				alert('No existen deudas con ese Rut.');
				return false;
			} else {
				var resp = ajax.responseText.split('|');
				// cuando el dropdown destino tiene a su vez un dependiente
				// asociado el ajax responde = "/CAMPO_DEPENDIENTE/<select
				// name=...."
				if (resp[1].substr(0, 1) != "/")
					selectDestino.parentNode.innerHTML = resp[1];
				else {
					var i = 1;
					var new_id_destino = "";
					while (resp[1].substr(i, 1) != "/") {
						new_id_destino = new_id_destino + resp[1].substr(i, 1);
						i++;
					}
					resp = resp[1].substr(i + 1, resp[1].length - i - 1)
					selectDestino.parentNode.innerHTML = resp;
				}
			}
		}
	}
}

function validate() {
	// valida que el primer estado que se ponga sea solicitado
	var cod_deuda = document.getElementById('COD_DEUDA_H_0').value;
	var cod_deudor = document.getElementById('COD_DEUDOR_0').value;

	var max_susp_x_tipo = document.getElementById('MAX_SUSP_X_TIPO_0').value
	var ajax = nuevoAjax();
	ajax.open("GET", "ajax_max_susp_x_deuda.php?cod_deuda=" + cod_deuda + "&cod_deudor=" + cod_deudor, false);
	ajax.send(null);

	resp = ajax.responseText;
	if (resp >= max_susp_x_tipo) {
		alert('Esta Deuda super\u00f3 el n\u00famero m\u00E1ximo de Suspensiones Permitidas. No pueden ingresarse m\u00E1s.');
		return false;
	}
	return true;
}
function f_usuario_confirma(ve_nom_usuario, ve_fecha) {
	document.getElementById('NOM_USUARIO_CONFIRMA_0').innerHTML = '';
	document.getElementById('NOM_USUARIO_CONFIRMA_0').value = '';
	document.getElementById('FECHA_CONFIRMA_NO_ING_0').innerHTML = '';
	document.getElementById('FECHA_CONFIRMA_NO_ING_0').value = '';

	var est_declaracion = document.getElementById('COD_ESTADO_SUSPENSION_0').value;

	if (est_declaracion == 2) {
		document.getElementById('NOM_USUARIO_CONFIRMA_0').innerHTML = ve_nom_usuario;
		document.getElementById('NOM_USUARIO_CONFIRMA_0').value = ve_nom_usuario;

		document.getElementById('FECHA_CONFIRMA_NO_ING_0').innerHTML = ve_fecha;
		document.getElementById('FECHA_CONFIRMA_NO_ING_0').value = ve_fecha;
	}
}

function f_get_anos_suspendido(ve_cod_tipo_suspension) {
	var rut = document.getElementById('RUT_0').value;
	var cod_tipo_suspension = ve_cod_tipo_suspension.value;
	rut = replaceChars(rut);

	if (rut == '') {
		alert('Debe ingresar RUT antes de Tipo Suspensi\u00f3n.');
		document.getElementById('COD_TIPO_SUSPENSION_0').value = '';
		document.getElementById('RUT_0').focus();
		return false;
	}else{
		if (cod_tipo_suspension == '') {
			alert('Debe seleccionar Tipo Suspensi\u00f3n para continuar.');
			document.getElementById('COD_TIPO_SUSPENSION_0').focus();
			return false;
		}else{
			var ajax = nuevoAjax()
			ajax.open("GET", "ajax_get_anos_suspension.php?rut="+rut+"&cod_tipo_suspension="+cod_tipo_suspension, false);
			ajax.send(null);
			var resp = URLDecode(ajax.responseText);
			document.getElementById('CANT_SUSPENSIONES_0').innerHTML = resp;
		}
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

function max_ano_suspension(){
	var tipo_suspension = document.getElementById('COD_TIPO_SUSPENSION_0').value;
	if(tipo_suspension == ''){
		document.getElementById('MAX_SUSP_X_TIPO_0').value = '';
	}
	else{
		var ajax = nuevoAjax();
		ajax.open("GET", "ajax_max_susp_x_tipo.php?tipo_suspension=" + tipo_suspension, false);
		ajax.send(null);
		resp = ajax.responseText;
		
		//document.getElementById('OBS_0').value = resp;
		document.getElementById('MAX_SUSP_X_TIPO_0').value = resp;
	}	
}