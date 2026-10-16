function is_cruzado(){
	var tipo_condonacion = document.getElementById('COD_TIPO_CONDONACION_0').value;
	var aTR = get_TR('DETALLE_DEUDA');
	var count = 0;
	for (var i=0; i < aTR.length ; i++) {
		var record = get_num_rec_field(aTR[i].id);
		var seleccion = document.getElementById('SELECCION_'+record).checked;
		var cod_deuda_h = document.getElementById('COD_DEUDA_H_'+record).value;
		if(seleccion==true){
			var ajax = nuevoAjax();
			ajax.open("GET", "USACH/ajax_cruzado.php?cod_deuda="+cod_deuda_h, false);
			ajax.send(null);
								
			var resp = ajax.responseText;	
			if(resp=='SI'){
				count=count+1;
			}
		}
	}
	if((count==0)&&(tipo_condonacion==7)){
		alert('La/s deuda/s seleccionadas no son deuda cruzada.');
		document.getElementById('COD_TIPO_CONDONACION_0').value = '';
	}
	if((count>0)&&(tipo_condonacion==1)){
		alert('El rut es CRUZADO, debe escoger el tipo condonacion (CRUZADO)');
		document.getElementById('COD_TIPO_CONDONACION_0').value = '';
	}
}

function validate(){
	var aTR = get_TR('DETALLE_DEUDA');
	var seleccion_deuda = false;
	for (var i=0; i < aTR.length ; i++) {
		var record = get_num_rec_field(aTR[i].id);
		var seleccion = document.getElementById('SELECCION_'+record).checked;
		var cod_tipo_deuda = document.getElementById('COD_TIPO_DEUDA_'+record).innerHTML;
		var cod_tipo_condonacion = document.getElementById('COD_TIPO_CONDONACION_0').value;
		var cod_deuda_h = document.getElementById('COD_DEUDA_H_'+record).value;
		
		if(cod_tipo_deuda == 'CSR'){
			if(seleccion == true){
				seleccion_deuda = true;
				if(cod_tipo_condonacion == 1){
					alert('No puede condonar la deuda CSR con el tipo de condonación "CONDONACIÓN LEY 19287".');
					return false;
				}
			}
		}
		else if(cod_tipo_deuda == 'CU' || cod_tipo_deuda == 'CFCU' || cod_tipo_deuda == 'CF'|| cod_tipo_deuda == 'CFR'|| cod_tipo_deuda == 'CSN' || cod_tipo_deuda == 'CRI' || cod_tipo_deuda == 'CR12' || cod_tipo_deuda == 'CSR'|| cod_tipo_deuda == 'RD'){
			if(seleccion == true){
				seleccion_deuda = true;
				if(cod_tipo_condonacion == 1){
					alert('La deuda que se desea condonar solo puede ser tipo condonación "DEFUNCIÓN"');
					return false;
				} 
			}
		}else if((cod_tipo_deuda == 'CS') && (seleccion == true)){
			seleccion_deuda = true;
			if(cod_tipo_condonacion == 1){
				//POR EL SOLO MINISTERIO DE LA LEY 1928
				var ajax = nuevoAjax();
				ajax.open("GET", "USACH/ajax_get_deuda_cs.php?cod_deuda="+cod_deuda_h, false);
				ajax.send(null);
				var resp  = URLDecode(ajax.responseText);
				
				if(resp == "CUOTA_12_15"){
					alert('La deuda N° '+cod_deuda_h+', Tipo Deuda CS no puede ser condonada por "POR EL SOLO MINISTERIO DE LA LEY 19287".\nLa Cuota del año no Corresponde Nro. Cuota 12 / 15.');
					return false;
				}else if(resp == "DEUDA_MOROSA"){
					alert('La deuda N° '+cod_deuda_h+', Tipo Deuda CS no puede ser condonada por "POR EL SOLO MINISTERIO DE LA LEY 19287".\nLa Deuda Tiene Cuotas Morosas.');
					return false;
				}else if(resp == "true"){
					return true;
				}else{
					alert('La deuda N° '+cod_deuda_h+', Tipo Deuda CS no puede ser condonada por "POR EL SOLO MINISTERIO DE LA LEY 19287".');
					return false;
				}
			}
		}
	}
	if (seleccion_deuda == false){ 
		alert("Debe seleccionar al menos una deuda");
		return false;
	}
	return true;
}

function f_get_name_user() {
	// RESPUESTAS DEL AJAX:
	// O = EL RUT NO EXISTE EN TABLA DEUDOR (PUEDE INGRESARLO)
	// 1 = EL RUT EXISTE 	
	var rut = document.getElementById('RUT_0').value;
	
	if(valida_rut(rut) == false){
		alert('Debe ingresar Rut para continuar');
		document.getElementById('RUT_0').focus();
	}
	else {	
		var ajax = nuevoAjax();
		ajax.open("GET", "USACH/ajax_obtiene_nombre.php?rut="+rut, false);
		ajax.send(null);
		if (ajax.readyState==4) {
		
			var existe  = URLDecode(ajax.responseText);
			var resp	= existe.split('|');
				
			if (resp[0] == 0) {
				alert('No existe Deudor con ese Rut');
				f_set_null();
				val.style.display = 'none';
			}
			else if (resp[0] == 1) {
				alert('Deudor ya Condonado');
				f_set_null();
				val.style.display = 'none';
			}
			else if (resp[0] == "NO_DEUDAS") {
				alert('Deudor sin deudas para condonar');
				f_set_null();
				val.style.display = 'none';
			}
			else if (resp[0] == "CONDONA_SOLICITADA") {
				alert('Deudor ya posee condonación solicitada');
				f_set_null();
				val.style.display = 'none';
			}
			else {
				var resp = ajax.responseText.split('|');
				document.getElementById('NOMBRE_0').innerHTML = to_num(resp[0]);
				document.getElementById('NOMBRE_0').value = to_num(resp[0]);
				document.getElementById('DIG_VERIF_0').innerHTML = to_num(resp[1]);
				document.getElementById('DIG_VERIF_0').value = to_num(resp[1]);
				document.getElementById('COD_DEUDOR_0').innerHTML = to_num(resp[2]);
				document.getElementById('COD_DEUDOR_0').value = to_num(resp[2]);
				
				f_deuda_otra_cartera(rut);
			}	
		}
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