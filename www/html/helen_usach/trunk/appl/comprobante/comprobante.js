function dlg_print() {	
	document.getElementById('wi_hidden').value = 1;	
   	return true;
}
// Setea a cero debe si se ingresa algun valor en haber y viceversa cuando textbox estan vacios
function debe_haber(ve_campo){
	 
	var aTR = get_TR('DETALLE_CUENTA');
	var nom_campo = get_nom_field(ve_campo.id);
	var num_fila = get_num_rec_field(ve_campo.id);

	if (nom_campo == "DEBE")
		document.getElementById('HABER_' + num_fila).value = 0;
	else if (nom_campo == "HABER")
		document.getElementById('DEBE_' + num_fila).value = 0;
}
//valida que no se hayan puesto valores en debe y haber en la misma fila
function f_valida_debe_haber(item)
{	
	var num_fila = get_num_rec_field(item.id);
	var nom_item=item.id;
	
	var debe = document.getElementById('DEBE_'+num_fila).value;
	var haber = document.getElementById('HABER_'+num_fila).value; 
	
	
	//1. CONTROLA QUE NO HAYA DEBE Y HABER AL MISMO TIEMPO
	if ((nom_item== 'DEBE_'+num_fila)&& (document.getElementById('DEBE_'+num_fila).value>0))
	{	
		if (haber > 0 || debe == null)
		{
			document.getElementById('HABER_'+num_fila).value=0;
			return false;
		}
	}
	
	if ((nom_item== 'HABER_'+num_fila)&& (document.getElementById('HABER_'+num_fila).value>0))
	{	
		if (debe > 0 || haber == null)
		{
			document.getElementById('DEBE_'+num_fila).value=0;
			return false;
		}
	}
	
	//2. que no sean ambos ceros
	
		if (document.getElementById('DEBE_'+num_fila).value=='0')
		{
			if (document.getElementById('HABER_'+num_fila).value=='0')
			{	
				alert('Debe ingresar  valor para DEBE o HABER en la fila N° '+num_fila);
				document.getElementById('DEBE_'+num_fila).focus();
				return false;
			}
		}
		
		if (document.getElementById('HABER_'+num_fila).value=='0')
		{
			if (document.getElementById('DEBE_'+num_fila).value=='0')
			{	
				alert('Debe ingresar  valor para DEBE o HABER en la fila N° '+num_fila);
				document.getElementById('DEBE_'+num_fila).focus();
				return false;
			}
		}
		
		
}
function f_valida_campos(item, ve_tabla_item){
	var num_fila = get_num_rec_field(item.id);
	var nom_item=item.id;
	
	var debe = document.getElementById('DEBE_'+num_fila).value;
	var haber = document.getElementById('HABER_'+num_fila).value;
	
	if (( debe=='')&& ( haber==''))	{
		alert('Debe poner valores al menos en uno, DEBE o HABER');
		document.getElementById('DEBE_'+num_fila).focus();
		return false;
	}
	else{
		if (debe==''){
			document.getElementById('DEBE_'+num_fila).value=0;
			document.getElementById('DEBE_'+num_fila).innerHTML=0;
		}
		if (haber==''){
			document.getElementById('HABER_'+num_fila).value=0;
			document.getElementById('HABER_'+num_fila).innerHTML=0;
		}
	}
}

//si el estado cambia a aprobado se llena campo de quien aprob� y en qu� fecha.
function f_get_aprobado(cod_usuario){
	var ajax = nuevoAjax();
	ajax.open("GET", "obtiene_usuario.php?cod_usuario="+cod_usuario, false);
	ajax.send(null);
	if (ajax.readyState==4){
		var existe = URLDecode(ajax.responseText);	
		if (existe == 0)
		{ 
			alert('No existe un Usuario con ese Código');
			return false;
		}
		else{		
			var estado_comprobante = document.getElementById('COD_ESTADO_COMPROBANTE_0');
			estado_comprobante = estado_comprobante.options[estado_comprobante.selectedIndex].value;
			
			if (estado_comprobante==2)	//'aprobado'
			{	
				var resp = ajax.responseText.split('|');
				
				document.getElementById('COD_USUARIO_ESTADO_H_0').value=cod_usuario;
				document.getElementById('COD_USUARIO_ESTADO_H_0').innerHTML=cod_usuario;
				
				document.getElementById('NOM_USUARIO_ESTADO_0').value=to_num(resp[0]);
				document.getElementById('NOM_USUARIO_ESTADO_0').innerHTML=to_num(resp[0]);
				
				document.getElementById('FECHA_ESTADO_0').value=to_num(resp[1]);
				document.getElementById('FECHA_ESTADO_0').innerHTML=to_num(resp[1]);
				
				document.getElementById('FECHA_ESTADO_H_0').value=resp[1];
				document.getElementById('FECHA_ESTADO_H_0').innerHTML=resp[1];
			}
			else{
				document.getElementById('COD_USUARIO_ESTADO_H_0').value='';
				document.getElementById('COD_USUARIO_ESTADO_H_0').innerHTML='';
				
				document.getElementById('NOM_USUARIO_ESTADO_0').value='';
				document.getElementById('NOM_USUARIO_ESTADO_0').innerHTML='';
				
				document.getElementById('FECHA_ESTADO_0').value='';
				document.getElementById('FECHA_ESTADO_0').innerHTML='';
				
				document.getElementById('FECHA_ESTADO_H_0').value='';
				document.getElementById('FECHA_ESTADO_H_0').innerHTML='';
			}
		}
	}
}

function validate(){
	var aTR = get_TR('DETALLE_CUENTA');
	var cuentas=aTR.length;
	
	// Comprueba que se agregen registros en detalle cuenta antes de grabar
	if (aTR.length==0) {
		alert('Debe ingresar al menos 2 registros en Detalle Cuentas antes de grabar.');
		return false;
	}
		
	var total_debe=document.getElementById('SUM_DEBE2_H_0').value;
	var total_haber=document.getElementById('SUM_HABER2_H_0').value
	
	if ( total_debe != total_haber ){
		alert('El TOTAL DEBE y el TOTAL HABER deben cuadrar, corrija valores Debe y Haber');
		return false;
	}

	//que todos los campos de las cuentas est�n completos
	for (var i = 0; i < aTR.length; i++){
		
		//debe=0 y haber=0
		if (document.getElementById('DEBE_'+i).value=='0'){
			if (document.getElementById('HABER_'+i).value=='0'){
				alert('Debe ingresar  valor para DEBE o HABER en la fila N° '+i);
				document.getElementById('DEBE_'+i).focus();
				return false;
			}
		}

		//haber=0 Y DEBE=0
		if (document.getElementById('HABER_'+i).value=='0'){
			if (document.getElementById('DEBE_'+i).value=='0'){
				alert('Debe ingresar  valor para DEBE o HABER en la fila N° '+i);
				document.getElementById('DEBE_'+i).focus();
				return false;
			}
		}
	}
}