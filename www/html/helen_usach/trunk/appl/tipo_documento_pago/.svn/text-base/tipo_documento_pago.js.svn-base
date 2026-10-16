function validate(){
	//valida que si marca 'N' debe ingresar la cuenta
	var aTR = get_TR('CUENTA_TIPO_FAMILIA_DEUDA');
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

function valida_cod_tipo_documento_pago(){

	var cod_tipo_documento_pago = document.getElementById('COD_TIPO_DOCUMENTO_PAGO_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_get_cod_tipo_documento_pago.php?cod_tipo_documento_pago="+cod_tipo_documento_pago,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El código ingresado ya existe. Por favor ingrese otro código');
		document.getElementById('COD_TIPO_DOCUMENTO_PAGO_0').value 	= null;
		document.getElementById('COD_TIPO_DOCUMENTO_PAGO_0').focus();
	}
}

