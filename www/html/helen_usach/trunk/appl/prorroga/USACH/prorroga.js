function f_get_name_user() 
{
	set_datos_deudor();
	var rut = document.getElementById('RUT_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET", "USACH/ajax_obtiene_nombre.php?rut="+rut, false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	var lista = resp.split('|');
	if ( (lista[0] != "NO") && (lista[0] != "NO_EXIGIBLE") && (lista[0] != "PENDIENTE") && (lista[0] != "VACIO") && (lista[0] != "CARTERA_DIFERENTE") ) {
		var result = eval("(" + resp + ")");
		document.getElementById('DIG_VERIF_0').innerHTML = result[0]['DIG_VERIF'];
		document.getElementById('NOMBRE_0').innerHTML = result[0]['NOMBRE'];
		document.getElementById('COD_DEUDA_0').innerHTML = result[0]['COD_DEUDA'];
		document.getElementById('COD_DEUDA_H_0').value = result[0]['COD_DEUDA'];
		
		var vl_cod_tipo_deuda = document.getElementById('COD_TIPO_DEUDA_0'); 
		for (var i=0; i < result.length; i++) {
			var nuevaOpcion=document.createElement("option"); 
			nuevaOpcion.value = result[i]['COD_DEUDA']; 
			nuevaOpcion.innerHTML = result[i]['COD_TIPO_DEUDA'];
			vl_cod_tipo_deuda.appendChild(nuevaOpcion); 
		}
		
		f_deuda_otra_cartera(rut);
		
	}
	else{
		if (lista[0] == "NO") {
			alert("El Deudor No Existe");
		}
		else if (lista[0] == "NO_EXIGIBLE"){
			alert("El deudor no posee deudas (distintas de CSR) en estado EXIGIBLE O NO EXIGIBLE");
		}
		else if (lista[0] == "PENDIENTE"){
			alert("El deudor tiene una solicitud de Prórroga pendiente");
		}
		else if (lista[0] == "VACIO"){
			set_datos_deudor();
		}
		document.getElementById('RUT_0').value 	= null;
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