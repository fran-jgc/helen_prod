function validate() {
	if (document.getElementById('APROBADO_0').checked==false && document.getElementById('PENDIENTE_0').checked==false) {
		alert("Debe seleccionar comprobantes 'Aprobados' y/o 'Pendientes'");
		document.getElementById('APROBADO_0').focus();
		return false;
	}
		 	 
	return true;
}
