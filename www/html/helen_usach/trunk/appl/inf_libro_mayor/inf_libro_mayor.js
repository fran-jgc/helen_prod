function validate() {
	if (document.getElementById('APROBADO_0').checked==false && document.getElementById('PENDIENTE_0').checked==false) {
		alert("Debe seleccionar uno de los dos estados de comprobante.");
		document.getElementById('APROBADO_0').focus();
		return false;
	}
		 	 
	return true;
}
