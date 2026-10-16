function get_tipo_deuda_selector() {
	return document.getElementById('COD_TIPO_DEUDA_DD_0');
}

function tiene_selector_tipo_deuda() {
	return get_tipo_deuda_selector() != null;
}

function get_suspension_url(path) {
	if (typeof K_SUSPENSION_BASE_URL != 'undefined' && K_SUSPENSION_BASE_URL != null && K_SUSPENSION_BASE_URL != '') {
		return K_SUSPENSION_BASE_URL + path;
	}

	return path;
}

function get_cod_deuda_element() {
	return document.getElementById('COD_DEUDA_0');
}

function set_cod_deuda_link_href(enlace, cod_deuda) {
	if (enlace == null) {
		return;
	}

	var href_actual = enlace.getAttribute('href');
	if (href_actual == null || href_actual == '') {
		return;
	}

	var href_template = enlace.getAttribute('data-href-template');
	if (href_template == null || href_template == '') {
		href_template = href_actual.replace(/cod_modulo_destino=[^&]*/i, 'cod_modulo_destino={COD_DEUDA}');
		enlace.setAttribute('data-href-template', href_template);
	}

	if (href_template.indexOf('{COD_DEUDA}') >= 0) {
		enlace.setAttribute('href', href_template.replace('{COD_DEUDA}', encodeURIComponent(cod_deuda)));
	}
}

function set_cod_deuda(cod_deuda) {
	var elemento = get_cod_deuda_element();
	var valor = (cod_deuda == null) ? '' : cod_deuda;

	if (elemento == null) {
		return;
	}

	if (elemento.tagName && elemento.tagName.toUpperCase() == 'A') {
		elemento.innerHTML = valor;
		set_cod_deuda_link_href(elemento, valor);
	}
	else {
		var enlaces = elemento.getElementsByTagName ? elemento.getElementsByTagName('a') : [];
		if (enlaces.length > 0) {
			enlaces[0].innerHTML = valor;
			set_cod_deuda_link_href(enlaces[0], valor);
		}
		else {
			elemento.innerHTML = valor;
		}
	}

	document.getElementById('COD_DEUDA_H_0').value = valor;
}

function bind_tipo_deuda_selector() {
	var selector_tipo = get_tipo_deuda_selector();

	if (selector_tipo != null) {
		selector_tipo.onchange = f_selecciona_deuda_multiple;
	}
}

function set_tipo_deuda_texto(tipo_deuda) {
	document.getElementById('COD_TIPO_DEUDA_0').innerHTML = tipo_deuda;
	document.getElementById('COD_TIPO_DEUDA_H_0').value = tipo_deuda;
}

function decode_respuesta_ajax(resp) {
	var decoded = resp;

	if (typeof URLDecode == 'function') {
		decoded = URLDecode(resp);
	}
	else {
		try {
			decoded = decodeURIComponent(resp.replace(/\+/g, ' '));
		}
		catch (e) {
			decoded = resp;
		}
	}

	if (decoded.indexOf('%7C') >= 0 || decoded.indexOf('%5E') >= 0) {
		try {
			decoded = decodeURIComponent(decoded.replace(/\+/g, ' '));
		}
		catch (e2) {
			return decoded;
		}
	}

	return decoded;
}

function carga_tipos_deuda(lista) {
	var html = '<select id="COD_TIPO_DEUDA_DD_0" name="COD_TIPO_DEUDA_DD_0" onchange="f_selecciona_deuda_multiple();">';
	var primer_tipo = '';
	var primer_cod_deuda = '';
	var i = 0;

	for (i = 4; i < lista.length; i++) {
		if (lista[i] != '') {
			var deuda = lista[i].split('^');
			if (deuda.length == 2) {
				if (primer_tipo == '') {
					primer_tipo = deuda[0];
					primer_cod_deuda = deuda[1];
				}
				html += '<option value="' + deuda[1] + '">' + deuda[0] + '</option>';
			}
		}
	}

	html += '</select>';
	document.getElementById('COD_TIPO_DEUDA_0').innerHTML = html;
	bind_tipo_deuda_selector();
	document.getElementById('COD_TIPO_DEUDA_H_0').value = primer_tipo;
	set_cod_deuda(primer_cod_deuda);
	return true;
}

function muestra_datos(lista, mantener_selector) {
	if (mantener_selector == null) {
		mantener_selector = false;
	}

	document.getElementById('DIG_VERIF_0').innerHTML = lista[1];
	document.getElementById('NOMBRE_0').innerHTML = lista[2];

	if (!mantener_selector || !tiene_selector_tipo_deuda()) {
		set_tipo_deuda_texto(lista[3]);
	}
	else {
		var selector_tipo = get_tipo_deuda_selector();
		var i = 0;
		for (i = 0; i < selector_tipo.options.length; i++) {
			if (selector_tipo.options[i].text == lista[3]) {
				selector_tipo.selectedIndex = i;
				break;
			}
		}
		document.getElementById('COD_TIPO_DEUDA_H_0').value = lista[3];
	}

	set_cod_deuda(lista[4]);
	document.getElementById('COD_DEUDOR_0').value = lista[5];
	document.getElementById('EXISTE_0').value = 'S';
}

function f_selecciona_deuda_multiple() {
	var selector_tipo = get_tipo_deuda_selector();
	var rut = replaceChars(document.getElementById('RUT_0').value);
	var ano_h = document.getElementById('ANO_SUSPENSION_H_0').value;

	if (selector_tipo == null || selector_tipo.value == '') {
		document.getElementById('COD_TIPO_DEUDA_H_0').value = '';
		set_cod_deuda('');
		return false;
	}

	document.getElementById('COD_TIPO_DEUDA_H_0').value = selector_tipo.options[selector_tipo.selectedIndex].text;
	set_cod_deuda(selector_tipo.value);

	var ajax = nuevoAjax();
	ajax.open("GET", get_suspension_url("USACH/ajax_info_deuda_seleccionada.php") + "?rut=" + rut + "&ano=" + ano_h + "&cod_deuda=" + selector_tipo.value + "&ts=" + new Date().getTime(), false);
	ajax.send(null);

	var resp = decode_respuesta_ajax(ajax.responseText);
	var lista = resp.split('|');

	if (lista[0] == 'SUSPENSION_DEUDA_RECHAZADA') {
		alert("La deuda fue Registrada en Suspension Cod. " + lista[6] + ", Estado: " + lista[7]);
		muestra_datos(lista, true);
		f_deuda_otra_cartera(rut);
	}
	else if (lista[0] == 'SUSPENSION_DEUDA_NUEVA') {
		muestra_datos(lista, true);
		f_deuda_otra_cartera(rut);
	}
	else if (lista[0] == 'DEUDA_NO_CS_CSR') {
		alert('Deudor no posee CS, CSR, CSN, CR12, CRI o CAME.');
		setea_null_deudor();
	}
	else if (resp == '') {
		alert('USACH/ajax_info_deuda_seleccionada.php no devolvio respuesta.');
	}
	else {
		alert('Respuesta no reconocida de USACH/ajax_info_deuda_seleccionada.php: ' + resp);
	}

	return true;
}

function validate() {
	var tipo_deuda = document.getElementById('COD_TIPO_DEUDA_H_0').value;
	var K_TIPO_SUS_MAGISTER = 1;
	var K_TIPO_SUS_DOCTORAD = 2;
	var K_TIPO_SUS_PREGRADO = 3;
	var K_TIPO_SUS_CESANTIA = 4;

	if (tipo_deuda != 'CSR') {
		var tipo_suspension = document.getElementById('COD_TIPO_SUSPENSION_0').value;
		if (tipo_suspension == K_TIPO_SUS_PREGRADO) {
			document.getElementById('COD_TIPO_SUSPENSION_0').focus();
			alert('La deuda ' + tipo_deuda + ' no puede tener tipo suspension: PROGRAMA DE PREGRADO');
			return false;
		}
	}

	var cod_deuda = document.getElementById('COD_DEUDA_H_0').value;
	var cod_deudor = document.getElementById('COD_DEUDOR_0').value;
	var tipo_suspension = document.getElementById('COD_TIPO_SUSPENSION_0').value;
	var max_susp_x_tipo = document.getElementById('MAX_SUSP_X_TIPO_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET", get_suspension_url("ajax_max_susp_x_deuda.php") + "?cod_deuda=" + cod_deuda + "&cod_deudor=" + cod_deudor, false);
	ajax.send(null);
	resp = ajax.responseText;

	if (tipo_suspension == K_TIPO_SUS_MAGISTER || tipo_suspension == K_TIPO_SUS_DOCTORAD) {
		if (resp >= max_susp_x_tipo) {
			alert('Esta deuda supero el numero maximo de suspensiones permitidas.');
			return false;
		}
	}
	else if (tipo_suspension == K_TIPO_SUS_CESANTIA) {
		if (tipo_deuda == 'CS' || tipo_deuda == 'CR12' || tipo_deuda == 'CAME') {
			if (resp >= max_susp_x_tipo) {
				alert('Esta deuda supero el numero maximo de suspensiones permitidas.');
				return false;
			}
		}
	}

	return true;
}

function f_get_info_deudor() {
	var rut = document.getElementById('RUT_0').value;
	var ano_h = document.getElementById('ANO_SUSPENSION_H_0').value;
	var val = document.getElementById('val');
	rut = replaceChars(rut);

	if (rut != '') {
		var ajax = nuevoAjax();
		ajax.open("GET", get_suspension_url("USACH/ajax_info_deudor.php") + "?rut=" + rut + "&ano=" + ano_h, false);
		ajax.send(null);

		var resp = decode_respuesta_ajax(ajax.responseText);
		var lista = resp.split('|');

		if (lista[0] == 'NO_EXISTE_RUT') {
			alert('No existe un deudor con ese Rut.');
			setea_null_deudor();
			if (val) val.style.display = 'none';
			document.getElementById('RUT_0').focus();
		}
		else if (lista[0] == 'DEUDA_NO_VIG') {
			alert('Deudor no posee deuda(s) vigente(s).');
			setea_null_deudor();
			if (val) val.style.display = 'none';
			document.getElementById('RUT_0').focus();
		}
		else if (lista[0] == 'NO_TIENE_CUOTA_FIJA') {
			alert('La deuda debe ser recalculada, favor contactar al encargado de informatica.');
			setea_null_deudor();
			if (val) val.style.display = 'none';
			document.getElementById('RUT_0').focus();
		}
		else if (lista[0] == 'DEUDA_NO_CS_CSR') {
			alert('Deudor no posee CS, CSR, CSN, CR12, CRI o CAME.');
			setea_null_deudor();
			if (val) val.style.display = 'none';
			document.getElementById('RUT_0').focus();
		}
		else if (lista[0] == 'SUSPENSION_DEUDA_RECHAZADA') {
			alert("La deuda fue Registrada en Suspension Cod. " + lista[6] + ", Estado: " + lista[7]);
			muestra_datos(lista);
			f_deuda_otra_cartera(rut);
		}
		else if (lista[0] == 'SUSPENSION_DEUDA_NUEVA') {
			muestra_datos(lista);
			f_deuda_otra_cartera(rut);
		}
		else if (lista[0] == 'NO_EXISTE_CUOTA_A_SUSPENDER') {
			document.getElementById('ANO_SUSPENSION_H_0').value = null;
			document.getElementById('ANO_SUSPENSION_0').value = null;
			alert('No existe cuota elegible para suspender en el ano "' + ano_h + '".');
		}
		else if (lista[0] == 'VARIAS_DEUDAS') {
			try {
				document.getElementById('DIG_VERIF_0').innerHTML = lista[1];
				document.getElementById('NOMBRE_0').innerHTML = lista[2];
				document.getElementById('COD_DEUDOR_0').value = lista[3];
				document.getElementById('EXISTE_0').value = 'S';
				carga_tipos_deuda(lista);
				f_selecciona_deuda_multiple();
			}
			catch (e) {
				alert('ERROR VARIAS_DEUDAS: ' + e.message);
			}
		}
		else if (resp == '') {
			alert('USACH/ajax_info_deudor.php no devolvio respuesta.');
		}
		else {
			alert('Respuesta no reconocida de USACH/ajax_info_deudor.php: ' + resp);
		}
	}
	else {
		alert('Debe ingresar un rut.');
		document.getElementById('RUT_0').focus();
		setea_null_deudor();
		if (val) val.style.display = 'none';
	}
}

function f_deuda_otra_cartera(ve_rut) {
	var rut = ve_rut;
	var ajax = nuevoAjax();
	ajax.open("GET", get_suspension_url("USACH/ajax_get_deuda_otra_cartera.php") + "?rut=" + rut, false);
	ajax.send(null);

	var resp = decode_respuesta_ajax(ajax.responseText);
	var lista = resp.split('|');
	var val = document.getElementById('val');

	if (lista[0] == 'DEUDA_OTRA_CARTERA') {
		val.style.display = '';
	}
	else {
		val.style.display = 'none';
	}
}

function f_valida_ano(ve_ano, ve_ano_actual) {
	if (ve_ano.value != '') {
		var rut = document.getElementById('RUT_0').value;
		rut = replaceChars(rut);

		if (rut != '') {
			var ano = ve_ano.value;
			if (ano < 1970) {
				alert('Ano invalido, debe ser mayor.');
				set_cod_deuda('');
				document.getElementById('COD_DEUDOR_0').innerHTML = null;
				document.getElementById('COD_TIPO_DEUDA_0').innerHTML = null;
				document.getElementById('COD_TIPO_DEUDA_H_0').value = null;
				ve_ano.value = '';
				ve_ano.focus();
				return false;
			}
			else if (ano > ve_ano_actual) {
				alert('El ano a suspender no puede ser mayor al ano actual.');
				set_cod_deuda('');
				document.getElementById('COD_DEUDOR_0').innerHTML = null;
				document.getElementById('COD_TIPO_DEUDA_0').innerHTML = null;
				document.getElementById('COD_TIPO_DEUDA_H_0').value = null;
				ve_ano.value = '';
				ve_ano.focus();
				return false;
			}
			
			else if (ano < (ve_ano_actual - 1)) {
				alert('El ano a suspender solo puede ser el actual o un ano anterior.');
				set_cod_deuda('');
				document.getElementById('COD_DEUDOR_0').innerHTML = null;
				document.getElementById('COD_TIPO_DEUDA_0').innerHTML = null;
				document.getElementById('COD_TIPO_DEUDA_H_0').value = null;
				ve_ano.value = '';
				ve_ano.focus();
				return false;
			}

			
			else {
				var ajax = nuevoAjax();
				ajax.open("GET", get_suspension_url("ajax_ano_a_suspender.php") + "?rut=" + rut + "&ano=" + ano, false);
				ajax.send(null);

				var resp = decode_respuesta_ajax(ajax.responseText);

				if (resp == 'TIENE_ABONO') {
					alert('Deudor posee abonos pendientes, no se puede realizar suspension.');
					setea_null_deudor();
					document.getElementById('RUT_0').focus();
				}
				else if (resp == 'SIN_CAPITAL') {
					alert('El monto capital del plan de pago es cero.');
					document.getElementById('ANO_SUSPENSION_0').value = '';
					document.getElementById('ANO_SUSPENSION_0').focus();
					return false;
				}
				else if (resp == 'ANO_OK') {
					document.getElementById('ANO_SUSPENSION_H_0').value = ano;
					document.getElementById('ANO_SUSPENSION_0').value = ano;
					f_get_info_deudor();
					return true;
				}
			}
		}
		else {
			alert('Ingrese Rut antes de ingresar Ano.');
			document.getElementById('ANO_SUSPENSION_0').value = '';
			document.getElementById('RUT_0').focus();
			return false;
		}
	}
	else {
		alert('Debe ingresar el Ano a Suspender.');
		document.getElementById('ANO_SUSPENSION_0').focus();
		setea_null_deudor();
		return false;
	}
}

function setea_null_deudor() {
	var val = document.getElementById('val');
	document.getElementById('RUT_0').value = null;
	document.getElementById('DIG_VERIF_0').innerHTML = null;
	document.getElementById('NOMBRE_0').innerHTML = null;
	set_cod_deuda('');
	document.getElementById('COD_DEUDOR_0').innerHTML = null;
	document.getElementById('COD_DEUDOR_0').value = null;
	document.getElementById('COD_TIPO_DEUDA_0').innerHTML = null;
	document.getElementById('COD_TIPO_DEUDA_H_0').value = null;
	document.getElementById('ANO_SUSPENSION_0').value = null;
	document.getElementById('ANO_SUSPENSION_0').innerHTML = null;
	document.getElementById('EXISTE_0').value = null;
	if (val) val.style.display = 'none';
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
		alert('Debe ingresar Rut antes de Tipo Suspension.');
		document.getElementById('COD_TIPO_SUSPENSION_0').value = '';
		document.getElementById('RUT_0').focus();
		return false;
	}
	else {
		if (cod_tipo_suspension == '') {
			alert('Debe seleccionar Tipo Suspension para continuar.');
			document.getElementById('COD_TIPO_SUSPENSION_0').focus();
			return false;
		}
		else {
			var ajax = nuevoAjax();
			ajax.open("GET", get_suspension_url("ajax_get_anos_suspension.php") + "?rut=" + rut + "&cod_tipo_suspension=" + cod_tipo_suspension, false);
			ajax.send(null);

			var resp = decode_respuesta_ajax(ajax.responseText);
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
		temp = "" + (temp.substring(0, pos) + add + temp.substring((pos + out.length), temp.length));
	}
	return temp;
}

function max_ano_suspension() {
	var tipo_suspension = document.getElementById('COD_TIPO_SUSPENSION_0').value;
	if (tipo_suspension == '') {
		document.getElementById('MAX_SUSP_X_TIPO_0').value = '';
	}
	else {
		var ajax = nuevoAjax();
		ajax.open("GET", get_suspension_url("ajax_max_susp_x_tipo.php") + "?tipo_suspension=" + tipo_suspension, false);
		ajax.send(null);
		resp = ajax.responseText;
		document.getElementById('MAX_SUSP_X_TIPO_0').value = resp;
	}
}
