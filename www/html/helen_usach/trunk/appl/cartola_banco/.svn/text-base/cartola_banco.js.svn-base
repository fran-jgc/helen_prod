function validate_load() {
	var vl_archivo = document.getElementById('FILE_NAME').value;
	if (!vl_archivo) {
		alert("Debe seleccionar un archivo");
		return false;
	}
	else{
    	var vl_extension = (vl_archivo.substring(vl_archivo.lastIndexOf("."))).toLowerCase();
    	if (vl_extension != '.txt') { 
			alert("Debe seleccionar un archivo con extensión '.txt'");
			return false;
		}
		return true;
	}
}

function dlg_cta_corriente(){
	
	var args = "location:no;dialogLeft:300px;dialogTop:300px;dialogWidth:400px;dialogHeight:250px;dialogLocation:0;Toolbar:no;";
		var returnVal = window.showModalDialog("dlg_cta_corriente.php","_blank", args);
	 	if (returnVal == null)
	 		return false;
		else {
			document.getElementById('wo_hidden').value = returnVal;
			document.output.submit();
	   		return true;
		}
}