function get_datos_deudor() {
	var rut = document.getElementById('RUT_0').value;
	rut = replaceChars(rut);
	if(rut != ''){
		var ajax = nuevoAjax();
		ajax.open("GET", "USACH/ajax_obtiene_datos_deuda.php?rut_deudor="+rut, false);
		ajax.send(null);
		var resp = URLDecode(ajax.responseText);
		var lista = resp.split('|');
		if (lista[0] == "NO_EXISTE_DEUDOR"){
			alert("El Deudor No Existe");
			val.style.display = 'none';
		} else if (lista[0] == "SIN_CS") {
			alert("El Deudor no tiene deuda CS");
			val.style.display = 'none';
		} else if (lista[0] == "SIN_NO_EXIGIBLE") {
			alert("El Deudor tiene deuda CS, pero distinta a NO EXIGIBLE");
			val.style.display = 'none';
		} else if (lista[0] == "SIN_DEUDAS_POR_NOVAR") {
			alert("El Deudor no tiene deuda para Novaci\u00f3n");
			val.style.display = 'none';
		} else if (lista[0] == "NOVACION_DEUDA_RECHAZADA") {
			muestra_datos(lista);
			f_deuda_otra_cartera(rut);
			alert("La deuda fue Registrada en Novacion Cod. "+lista[17]+", Estado: "+lista[18]);
		} else if (lista[0] == "NOVAR_DEUDA") {
			muestra_datos(lista);
			f_deuda_otra_cartera(rut);
			f_deuda_dentro_periodo_gracia();
		} else if (lista[0]== "VARIAS_DEUDAS"){
			var args = "location:no;dialogLeft:250px;dialogTop:300px;dialogWidth:450px;dialogHeight:200px;dialogLocation:0;Toolbar:no;";
			var returnVal = window.showModalDialog("dlg_mas_deudas.php?rut_deudor="+rut, "_blank", args);
			if(returnVal != null){
				muestra_datos(returnVal);
				f_deuda_otra_cartera(rut);
				f_deuda_dentro_periodo_gracia();
			}else{
				document.getElementById('RUT_0').value = null;
				document.getElementById('RUT_0').focus();
				set_datos_deudor();
				val.style.display = 'none';
			}
		}
	}else{
		alert("Debe ingresar un rut");
		document.getElementById('RUT_0').focus();
		set_datos_deudor();
		val.style.display = 'none';
	}
}

/*Esta funcion:
al momento de ingresar el rut, verifica que de cartera proviene
si es cartera defecto: no muestra leyenda
si es otra cartera: despliega leyenda de que pertenece a otra cartera*/
function f_deuda_otra_cartera(ve_rut){
	var rut = ve_rut;
	var ajax = nuevoAjax();
	ajax.open("GET", "USACH/ajax_get_deuda_otra_cartera.php?rut="+rut, false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	var lista = resp.split('|');
	var val = document.getElementById('val');
	if(lista[0] == 'DEUDA_OTRA_CARTERA'){
		val.style.display = '';	
	}
	else{
		val.style.display = 'none';
	}
}

/*Esta funcion:
permite novar solo 1 mes antes de la fecha inicio cobro.
ej: si inicia cobro el 01/01/2013
solo podra novar a partir del 01/12/2012, de lo contrario no podra hacerlo*/
function f_deuda_dentro_periodo_gracia(){
	var rut = document.getElementById('RUT_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET", "USACH/ajax_novacion_periodo_gracia.php?rut="+rut, false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	var lista = resp.split('|');
	if(lista[0] == 'NO_PUEDE_HACER_NOVACION'){
		alert('El deudor se puede novar a partir de mes de Diciembre del segundo año de gracia.');
		set_value('RUT_0', '', '');
		set_datos_deudor();
	}
}