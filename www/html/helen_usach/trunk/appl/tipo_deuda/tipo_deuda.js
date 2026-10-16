function validate() {
	var aTR = get_TR('CONVENIO');
	/*if (aTR.length==0) {
		alert('Debe ingresar al menos 1 item en la tabla CONVENIO BANCO DEL DESARROLLO antes de grabar.');
		return false;
	}*/
	for (i=0; i<aTR.length; i++){	
		var rec_tr = get_num_rec_field(aTR[i].id);
		var ano     = document.getElementById('ANO_'+rec_tr).value;
		var con_dec = document.getElementById('CON_DECLARACION_'+rec_tr).checked;
		
		for (j=i+1; j<aTR.length; j++){	
			var rec_tr = get_num_rec_field(aTR[j].id);
			var ano2     = document.getElementById('ANO_'+rec_tr).value;
			var con_dec2 = document.getElementById('CON_DECLARACION_'+rec_tr).checked;							
			if (ano == ano2 && con_dec==con_dec2) {				
				alert('El año '+ano+' ya posee convenio con y sin declaración, revise antes de guardar ' );			
				return false;
			}	
		}
	}
	return true;
}

function valida_cod_tipo_deuda(){
	var cod_tipo_deuda = document.getElementById('COD_TIPO_DEUDA_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_cod_tipo_deuda.php?cod_tipo_deuda="+cod_tipo_deuda,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El código ingresado ya existe. Por favor ingrese otro código');
		document.getElementById('COD_TIPO_DEUDA_0').value 	= null;
		document.getElementById('COD_TIPO_DEUDA_0').focus();
	}
}

function valida_nom_tipo_deuda(){
	var nom_tipo_deuda = document.getElementById('NOM_TIPO_DEUDA_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nom_tipo_deuda.php?nom_tipo_deuda="+nom_tipo_deuda,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El nombre ingresado ya existe. Por favor ingrese otro nombre');
		document.getElementById('NOM_TIPO_DEUDA_0').value 	= null;
		document.getElementById('NOM_TIPO_DEUDA_0').focus();
	}
}

function valida_ano(ve_ano) {
	var ano   	  = ve_ano.value;
	var largo_ano = ano.length;
	
	if (largo_ano < 4) {
		alert('El formato del año debe ser: "AAAA"');
		document.getElementById('ANO_0').value 	= null;
		document.getElementById('ANO_0').focus();
		return false;
	} else if (ano < 1950) {
		alert('Debe ingresar un año MAYOR a '+ 1950);
		document.getElementById('ANO_0').value 	= null;
		document.getElementById('ANO_0').focus();
	} else if (ano > 2100) {
		alert('Debe ingresar un año MENOR a '+2100);
		document.getElementById('ANO_0').value 	= null;
		document.getElementById('ANO_0').focus();
	}
}
		
	
