function validate() {
	var aTR = get_TR('ABONO');
	var haySeleccionados = false;

	for (var i = 0; i < aTR.length; i++) {
		var rec_tr = get_num_rec_field(aTR[i].id);
		var control = document.getElementById('ANULA_ABONO_' + rec_tr);

		if (control && control.checked) {
			haySeleccionados = true;
			break;
		}
	}

	if (!haySeleccionados) {
		alert('Debe seleccionar al menos un abono');
		return false;
	}

	var obs = document.input.OBS_0.value;

	if (obs.replace(/^\s+|\s+$/g, '') == '') {
		alert('Debe ingresar "Observaciones" antes de grabar');
		document.input.OBS_0.focus();
		return false;
	}

	/*
	 * El estado del comprobante y la configuración del excedente
	 * se validan en class_wi_anula_abono.php para evitar una
	 * petición HTTP bloqueante por cada abono seleccionado.
	 */
	return true;
}

function f_get_name_user() {
	var rut = document.getElementById('RUT_0').value;
	var ajax = nuevoAjax();

	ajax.open(
		'GET',
		'obtiene_nombre.php?rut=' + encodeURIComponent(rut),
		false
	);
	ajax.send(null);

	limpiar_datos_deuda();

	if (ajax.readyState != 4) {
		return;
	}

	var existe = URLDecode(ajax.responseText);

	if (existe == 0) {
		alert('No existe un Deudor con ese Rut');
		limpiar_datos_deudor();
		document.getElementById('RUT_0').focus();
		return;
	}

	if (rut == '') {
		limpiar_datos_deudor();
		document.getElementById('RUT_0').focus();
		return;
	}

	var datos = ajax.responseText.split('|');

	document.getElementById('NOMBRE_0').innerHTML = datos[0];
	document.getElementById('NOMBRE_0').value = datos[0];

	document.getElementById('DIG_VERIF_0').innerHTML = datos[1];
	document.getElementById('DIG_VERIF_0').value = datos[1];

	document.getElementById('COD_DEUDOR_H_0').innerHTML =
		to_num(datos[2]);
	document.getElementById('COD_DEUDOR_H_0').value =
		to_num(datos[2]);
}

function limpiar_datos_deudor() {
	var campos = [
		'RUT_0',
		'NOMBRE_0',
		'DIG_VERIF_0',
		'COD_DEUDOR_H_0'
	];

	for (var i = 0; i < campos.length; i++) {
		var control = document.getElementById(campos[i]);

		if (!control) {
			continue;
		}

		control.innerHTML = '';
		control.value = '';
	}
}

function limpiar_datos_deuda() {
	var campos = [
		'COD_DEUDA_0',
		'SIMBOLO_0',
		'NRO_CUOTA_0',
		'SALDO_CUOTA_0'
	];

	for (var i = 0; i < campos.length; i++) {
		var control = document.getElementById(campos[i]);

		if (!control) {
			continue;
		}

		control.innerHTML = '';
		control.value = '';
	}

	limpiar_tabla_abonos();
}

function limpiar_tabla_abonos() {
	var tabla = document.getElementById('ABONO');

	if (!tabla) {
		return;
	}

	while (tabla.firstChild) {
		tabla.removeChild(tabla.firstChild);
	}
}

function get_tipo_deuda(controlDeuda) {
	limpiar_datos_plan_pago();

	var indice = controlDeuda.selectedIndex;

	if (indice < 0) {
		return;
	}

	var opcion = controlDeuda.options[indice];
	var codDeuda = opcion.value;
	var simbolo = opcion.dataset.dropdown;

	document.getElementById('COD_DEUDA_0').innerHTML = codDeuda;
	document.getElementById('COD_DEUDA_0').value = codDeuda;

	document.getElementById('SIMBOLO_0').innerHTML = simbolo;
	document.getElementById('SIMBOLO_0').value = simbolo;

	select_ano_cobro(true);
}

function limpiar_datos_plan_pago() {
	var campos = ['NRO_CUOTA_0', 'SALDO_CUOTA_0'];

	for (var i = 0; i < campos.length; i++) {
		var control = document.getElementById(campos[i]);

		if (!control) {
			continue;
		}

		control.innerHTML = '';
		control.value = '';
	}

	limpiar_tabla_abonos();
}

function get_ano_cobro(controlPlanPago) {
	limpiar_datos_plan_pago();

	var indice = controlPlanPago.selectedIndex;

	if (indice < 0 || controlPlanPago.options[indice].value == '') {
		return;
	}

	var codPlanPago = controlPlanPago.options[indice].value;

	// Carga los abonos sin esperar el cálculo del saldo.
	select_ano_cobro(false);

	var ajax = nuevoAjax();

	ajax.onreadystatechange = function() {
		if (ajax.readyState != 4) {
			return;
		}

		if (ajax.status != 200 && ajax.status != 0) {
			alert('No fue posible obtener los datos de la cuota.');
			return;
		}

		// Evita mostrar una respuesta anterior si se cambió de año.
		if (document.getElementById('COD_PLAN_PAGO_PP_0').value != codPlanPago) {
			return;
		}

		var datos = ajax.responseText.split('|');
		var nroCuota = datos.length > 0 ? datos[0] : '0';
		var saldoCuota = datos.length > 1 ? datos[1] : '0';

		document.getElementById('NRO_CUOTA_0').innerHTML = nroCuota;
		document.getElementById('NRO_CUOTA_0').value = nroCuota;

		document.getElementById('SALDO_CUOTA_0').innerHTML =
			number_format(saldoCuota, 0, ',', '.');
		document.getElementById('SALDO_CUOTA_0').value = saldoCuota;
	};

	ajax.open(
		'GET',
		'obtiene_datos_plan_pago.php?cod_plan_pago=' +
			encodeURIComponent(codPlanPago),
		true
	);
	ajax.send(null);
}

function select_ano_cobro(noExigible) {
	limpiar_tabla_abonos();

	var rut = document.getElementById('RUT_0').value;
	var codDeuda =
		document.getElementById('COD_DEUDA_DD_0').value;
	var codPlanPago;

	if (noExigible) {
		codPlanPago = 9999;
	}
	else {
		codPlanPago =
			document.getElementById('COD_PLAN_PAGO_PP_0').value;
	}

	var url =
		'load_abono.php?rut=' + encodeURIComponent(rut) +
		'&cod_deuda_dd=' + encodeURIComponent(codDeuda) +
		'&cod_plan_pago_pp=' + encodeURIComponent(codPlanPago);

	var ajax = nuevoAjax();

	ajax.onreadystatechange = function() {
		if (ajax.readyState != 4) {
			return;
		}

		if (ajax.status != 200 && ajax.status != 0) {
			alert('No fue posible cargar los abonos.');
			return;
		}

		cargar_filas_abonos(ajax.responseText);
	};

	/*
	 * Petición asíncrona: evita que la pantalla se congele
	 * mientras el servidor consulta los abonos.
	 */
	ajax.open('GET', url, true);
	ajax.send(null);
}

function cargar_filas_abonos(respuesta) {
	var tabla = document.getElementById('ABONO');
	var tablaAuxiliar = document.createElement('TABLE');

	tablaAuxiliar.innerHTML = respuesta;

	var filas = tablaAuxiliar.getElementsByTagName('TR');
	var tbody = document.createElement('TBODY');

	/*
	 * Las filas cambian de contenedor; se usa siempre la primera
	 * hasta que no queden filas dentro de la tabla auxiliar.
	 */
	while (filas.length > 0) {
		tbody.appendChild(filas[0]);
	}

	tabla.appendChild(tbody);
}