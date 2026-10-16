function validate_save(){
	return true;
}
function validate_load() {
	var vl_archivo = document.getElementById('FILE_NAME').value;
	if (!vl_archivo) {
		alert("Debe seleccionar un archivo");
		document.getElementById('FILE_NAME').focus();
		return false;
	}
	else
		return true;
}