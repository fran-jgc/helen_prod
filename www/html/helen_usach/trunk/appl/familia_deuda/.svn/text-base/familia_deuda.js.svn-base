function validate(){
	//valida que si marca 'N' debe ingresar la cuenta
	var aTR = get_TR('CUENTA_TIPO_DOCUMENTO_PAGO');
	for (var i=0; i < aTR.length ; i++) {
		var record 		= get_num_rec_field(aTR[i].id);
		var usar_ctacte = document.getElementById('USAR_CUENTA_CTACTE_'+record).checked;	
		var cuenta_contable = document.getElementById('COD_CUENTA_CONTABLE_'+record);
		cuenta_contable = cuenta_contable.options[cuenta_contable.selectedIndex].value;

		if (usar_ctacte == true && (!cuenta_contable == '')){
			alert('No se debe definir "cuenta contable", ya que se está seleccionando "cuenta corriente".');
			document.getElementById('COD_CUENTA_CONTABLE_'+record).focus();
			return false;
		}
	
		if (usar_ctacte == false && (cuenta_contable == '')){
			alert('Debe definir "cuenta contable" -> seleccionar "cuenta corriente".');
			document.getElementById('COD_CUENTA_CONTABLE_'+record).focus();
			return false;
		}
	}
	return true;
}

function valida_nom_familia_deuda(){
	var nom_familia_deuda = document.getElementById('NOM_FAMILIA_DEUDA_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nom_familia_deuda.php?nom_familia_deuda="+nom_familia_deuda,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El nombre ingresado ya existe. Por favor ingrese otro nombre');
		document.getElementById('NOM_FAMILIA_DEUDA_0').value 	= null;
		document.getElementById('NOM_FAMILIA_DEUDA_0').focus();
	}
}
