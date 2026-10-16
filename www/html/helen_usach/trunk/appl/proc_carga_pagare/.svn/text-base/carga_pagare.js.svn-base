function validate_save(){
	return true;
}
function validate_load() {
	var vl_archivo = document.getElementById('FILE_NAME').value;
	if (!vl_archivo) {
		document.getElementById('FILE_NAME').focus();
		alert("Debe seleccionar un archivo");
		return false;
	}
	else{
    	var vl_extension = (vl_archivo.substring(vl_archivo.lastIndexOf("."))).toLowerCase();
    	if (vl_extension != '.txt') { 
    		document.getElementById('FILE_NAME').focus();
			alert("Debe seleccionar un archivo con extensión '.txt'");
			return false;
		}
		return true;
	}
}