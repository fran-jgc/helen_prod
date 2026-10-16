function validate_load() {
	var vl_archivo = document.getElementById('FILE_NAME').value;
		
	if (!vl_archivo) {
		alert("Debe seleccionar un archivo");
		return false;
	}		
}
function f_get_estado (ve_control){

	var array_1= ve_control.id.split('_');
	
	var rut =document.getElementById('RUT_ARCHIVO_'+array_1[2]).innerHTML;
	var ano = document.getElementById('ANO_COBRO_'+array_1[2]).value;
	var cod_deuda =document.getElementById('COD_DEUDA_'+array_1[2]).value;
	var nro_cuota = document.getElementById('NRO_CUOTA_'+array_1[2]).value;
	
	if(rut != ''){
		var ajax = nuevoAjax();
		ajax.open("GET", "ajax_obtiene_estado.php?rut="+rut+"&ano="+ano+"&cod_deuda="+cod_deuda+"&nro_cuota="+nro_cuota, false);
		ajax.send(null);
		var resp = URLDecode(ajax.responseText);
		var result = eval("(" + resp + ")");		
		document.getElementById('NOM_ESTADO_CARGA_'+array_1[2]).innerHTML= result[0]['NOM_ESTADO_CARGA'];
		document.getElementById('COD_ESTADO_CARGA_'+array_1[2]).value= result[0]['COD_ESTADO_CARGA'];

	}
}
function set_forma_pago(ve_control){
	document.getElementById('COD_ORIGEN_PAGO_H_0').value= ve_control.value;
}