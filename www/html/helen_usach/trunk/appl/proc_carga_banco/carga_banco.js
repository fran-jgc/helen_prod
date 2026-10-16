function validate_save(){
	return true;
}
function validate_load() {
	var vl_archivo = document.getElementById('FILE_NAME').value;
	var vl_cod_origen_carga = document.getElementById('COD_ORIGEN_CARGA_BANCO_0').value;

	if (vl_cod_origen_carga == "") {
		alert("Debe seleccionar un Tipo de Carga.");
		document.getElementById('COD_ORIGEN_CARGA_BANCO_0').focus();
		return false;
	}else{
		if(vl_cod_origen_carga == 6){
			var vl_fecha_deposito = document.getElementById('FECHA_DEPOSITO_0').value;
			if (vl_fecha_deposito == "") {
				alert("Debe Ingresar Fecha Deposito.");
				document.getElementById('FECHA_DEPOSITO_0').focus();
				return false;
			}
		}
		
		if (!vl_archivo) {
			alert("Debe seleccionar un archivo");
			return false;
		}
		
		return true;
	}
}