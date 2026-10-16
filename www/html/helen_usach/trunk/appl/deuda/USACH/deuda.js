function validate() {
	var cod_estado_deuda = document.getElementById('COD_ESTADO_DEUDA_0').value;
	var k_estado_deuda = 1; //ESTADO DEUDA NO EXIGIBLE
	var k_estado_deuda_exigible = 2; //ESTADO DEUDA EXIGIBLE
	if (cod_estado_deuda == k_estado_deuda || cod_estado_deuda == k_estado_deuda_exigible) {
		var aTR = get_TR('DEUDA_PAGARES');
		var tipo_rebaja = '';
		var monto_moneda_rebaja = '';
		var monto_pesos_rebaja = '';
		for (var i=0; i < aTR.length ; i++) {
			var record = get_num_rec_field(aTR[i].id);
			tipo_rebaja = document.getElementById('COD_TIPO_REBAJA_PAGARE_' + record).value;
			monto_moneda_rebaja = document.getElementById('MONTO_MONEDA_REBAJA_' + record).value;
			monto_pesos_rebaja = document.getElementById('MONTO_PESOS_REBAJA_' + record).value;
			
			if(tipo_rebaja != '' ||  monto_moneda_rebaja != '' || monto_pesos_rebaja != ''){
				if(tipo_rebaja == ''){
					alert('Debe Ingresar Tipo Rebaja antes de Grabar.');
					document.getElementById('COD_TIPO_REBAJA_PAGARE_' + record).focus();
					return false;
				}else if (monto_moneda_rebaja == ''){
					alert('Debe Ingresar Monto UTM Rebaja antes de Grabar.');
					document.getElementById('MONTO_MONEDA_REBAJA_' + record).focus();
					return false;
				}else if(monto_pesos_rebaja == ''){
					alert('Debe Ingresar Monto $ Rebaja antes de Grabar.');
					document.getElementById('MONTO_PESOS_REBAJA_' + record).focus();
					return false;
				}
			}else{
				return true;
			}
		}
	}	
	return true;	
}

function dlg_print(ve_campo) {
	var id_campo = ve_campo.id;
	var cod_estado_deuda = document.getElementById('COD_ESTADO_DEUDA_0').value;
	var url = "USACH/dlg_print.php?cod_estado_deuda="+cod_estado_deuda;
	$.showModalDialog({
	url: url,
		 dialogArguments: '',
		 height: 225,
		 width: 470,
		 scrollable: false,
		 onClose: 
		 function(){
		 	var returnVal = this.returnValue; 
		 	
		 	if (returnVal == null){
		 		return false;
		 	}
			else {
				var input = document.createElement("input");
				input.setAttribute("type", "hidden");
				input.setAttribute("name", id_campo+'_x');
				input.setAttribute("id", id_campo+'_x');
				document.getElementById("input").appendChild(input);
				
				document.getElementById('wi_hidden').value = returnVal;
				document.input.submit();
				
				var node=document.getElementById(id_campo+'_x');
				if (node) {
				  document.getElementById("input").removeChild(node);
				}
				
		   		return true;
			}
		}
	});
}