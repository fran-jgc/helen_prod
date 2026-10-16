function add_line_fecha(ve_tabla_item, nomTabla) 
{	
	var aTR = get_TR(ve_tabla_item);
	var num_fila=aTR.length;
	var cod_usuario_actual=document.getElementById('USUARIO_ACTUAL_H_0').value;
	
	if (num_fila==0)
	{
		var fecha = document.getElementById('FECHA_DOCUMENTO_PAGO_H_0').value;
		var cod_doc = document.getElementById('COD_DOCUMENTO_PAGO_PP_0').value;
		
		add_line(ve_tabla_item,nomTabla);
		document.getElementById('FECHA_ANTERIOR_0').value=fecha;
		document.getElementById('FECHA_ANTERIOR_0').innerHTML=fecha;
		document.getElementById('FECHA_ANTERIOR_H_0').value=fecha;
		document.getElementById('FECHA_ANTERIOR_H_0').innerHTML=fecha;
		
		document.getElementById('COD_DOCUMENTO_PAGO_0').value=cod_doc;
		document.getElementById('COD_DOCUMENTO_PAGO_0').innerHTML=cod_doc;
		
		document.getElementById('COD_USUARIO_0').value=cod_usuario_actual;
		document.getElementById('COD_USUARIO_0').innerHTML=cod_usuario_actual;
	}
	else
	{	
		 
		
		var maximo_filas = document.getElementById('CANT_H_0').value;
		var maximo_pror_x_doc = document.getElementById('CANT_MAX_PRORROGA_H_0').value;
		
		if (maximo_filas=='') maximo_filas=0;
		var maximo=parseInt(maximo_filas) + parseInt(1);
		
		if (maximo_filas>=maximo_pror_x_doc)
		{
			alert('¡Este Documento ha superado el número de prorrogas permitido, no pueden agregarse más!');
			return false;
			}
		else
		{
	
			if (num_fila >= maximo)
			{
				alert('!No se pueden agregar más ítems, se ha llegado al máximo permitido!');
				return false;
				}
			else
			{
				add_line(ve_tabla_item,nomTabla);
				document.getElementById('COD_USUARIO_'+num_fila).value=cod_usuario_actual;
				document.getElementById('COD_USUARIO_'+num_fila).innerHTML=cod_usuario_actual;
			}
		}
	}			
}
/*
function f_valida_max_fecha(ve_fecha_nueva) {
	var fecha_nueva    		= ve_fecha_nueva.value;
	var largo_fecha	   		= fecha_nueva.length;
	var fecha_doc_original  = document.getElementById('FECHA_ANTERIOR_0').innerHTML;
	
	var aTR = get_TR('PRORROGA_DOCUMENTO_PAGO');
	for (var i=0; i<aTR.length; i++){	
		var rec_tr       = get_num_rec_field(aTR[i].id);
		var fecha_anterior  = document.getElementById('FECHA_ANTERIOR_'+rec_tr).innerHTML;
		
	}
	if (largo_fecha < 10) {
		alert('El formato de la fecha deber ser: "DD/MM/AAAA"');
		document.getElementById('FECHA_NUEVA_'+rec_tr).value = '';
		document.getElementById('FECHA_NUEVA_'+rec_tr).focus();			
		return false;
	}
	
	var fecha1 = fecha_anterior.split('/');
	var fecha2 = fecha_nueva.split('/');
	var fecha3 = fecha_doc_original.split('/');
	
	var ano1 = fecha1[2];
	var mes1 = fecha1[1];
	var dia1 = fecha1[0];
	var ano2 = fecha2[2];
	var mes2 = fecha2[1];
	var dia2 = fecha2[0];
	var ano3 = fecha3[2];
	var mes3 = fecha3[1];
	var dia3 = fecha3[0];
		
	var fecha_ant = new Date(ano1,mes1,dia1); // fecha antes = fecha anterior
	var fecha_des = new Date(ano2,mes2,dia2); // fecha despues = fecha entrada
	var fecha_ori = new Date(ano3,mes3,dia3); // fecha original = fecha doc pago
	
	var diferencia = fecha_des - fecha_ant; 	// compara fecha anterior con fecha de entrada
	var diferencia_2 = fecha_des - fecha_ori;	// compara la fecha original del documento y la fecha de entrada
	var mseg_dia = parseInt(24*60*60*1000);	
	var diferencia_dias = diferencia / mseg_dia;
	var diferencia_dias_2 = diferencia_2 / mseg_dia;
	
	if (diferencia_dias <= 0) { // si la fecha es menor a la actual...
		alert('No puede ingresar una fecha menor o igual a la FECHA ANTERIOR del documento.');
		document.getElementById('FECHA_NUEVA_'+rec_tr).value = '';
		document.getElementById('FECHA_NUEVA_'+rec_tr).focus();		
		return false;
	}
	if (diferencia_dias_2 > 60) { // si la fecha sobre pasa los 60 dias maximo de prorroga
		alert('La fecha ingresada excede el limite de 60 d�as, a partir de la fecha orginal del documento.');
		document.getElementById('FECHA_NUEVA_'+rec_tr).value = '';
		document.getElementById('FECHA_NUEVA_'+rec_tr).focus();		
		return false;
	}
}
*/

