function f_valida_estado(){
	var dd_estado_excedente = document.getElementById('COD_ESTADO_EXCEDENTE_0');
	var estado = dd_estado_excedente.value;
	
	var K_ESTADO_EMITIDO = 1;
	var K_ESTADO_CON_EGRESO = 2;

	if(estado == K_ESTADO_CON_EGRESO){
		alert('Se le solicita Ingresar el Nro. de Cheque');
		window.document.getElementById('NRO_CHEQUE_0').focus();
	}	
}

function validate(){
	var vl_cod_estado_excedente	= document.getElementById('COD_ESTADO_EXCEDENTE_0').value;
	var vl_cod_cuenta_corriente	= document.getElementById('COD_CUENTA_CORRIENTE_0').value;
	var vl_excedente_con_egreso = 2;
	if (vl_cod_estado_excedente == vl_excedente_con_egreso && vl_cod_cuenta_corriente == ''){
		alert('Debe Seleccionar la Cuenta Corriente');
		return false;
	}
	return true;
}
//si el estado cambia de emitido a con egreso se carga el  campo de quien aprobó y en qué fecha.
function f_get_anulado(ve_cod_usuario){
	var ajax = nuevoAjax();
	ajax.open("GET", "obtiene_usuario.php?cod_usuario="+ve_cod_usuario, false);
	ajax.send(null);
	if (ajax.readyState==4){
		var existe = URLDecode(ajax.responseText);		
			
		var estado_excedente = document.getElementById('COD_ESTADO_EXCEDENTE_0');
		estado_excedente = estado_excedente.options[estado_excedente.selectedIndex].value;
		
		if (estado_excedente==2){	//'anulado
			var resp = ajax.responseText.split('|');
			
			document.getElementById('FECHA_EGRESO_0').value=to_num(resp[0]);
			document.getElementById('FECHA_EGRESO_0').innerHTML=to_num(resp[0]);
			
			document.getElementById('NOM_USUARIO_0').value=to_num(resp[1]);
			document.getElementById('NOM_USUARIO_0').innerHTML=to_num(resp[1]);
		}
		else{
			document.getElementById('FECHA_EGRESO_0').value='';
			document.getElementById('FECHA_EGRESO_0').innerHTML='';
			
			document.getElementById('NOM_USUARIO_0').value='';
			document.getElementById('NOM_USUARIO_0').innerHTML='';
		}
	}
}