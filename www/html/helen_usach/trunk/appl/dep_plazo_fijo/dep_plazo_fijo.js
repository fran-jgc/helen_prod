function calcula_intereses(){
	var monto_incial = document.getElementById('VALOR_INICIAL_0').value;
	var tasa_interes_periodo = document.getElementById('TASA_INTERES_PERIODO_H_0').value;
	
	var ajax = nuevoAjax();
	ajax.open("GET", "ajax_calcula_intereses.php?ve_monto_incial="+monto_incial+"&ve_tasa_interes_periodo="+tasa_interes_periodo, false);
	ajax.send(null);
	
	var resp = ajax.responseText.split('|');
	
	var intereses = resp[0];
	var monto_total = resp[1];
		
	document.getElementById('INTERESES_0').innerHTML = number_format(intereses, 0, ',', '.');
	document.getElementById('INTERESES_H_0').value = intereses
	
	document.getElementById('VALOR_FINAL_0').innerHTML = number_format(monto_total, 0, ',', '.');
	document.getElementById('VALOR_FINAL_H_0').value = monto_total
}

function calcula_interes_periodo(){
	var tasa_interes_base = document.getElementById('TASA_INTERES_BASE_0').value;
	var dias = document.getElementById('DIAS_H_0').value;
	
	if((tasa_interes_base != '')&&(dias != '')){
		
		var ajax = nuevoAjax();
		ajax.open("GET", "ajax_calcula_tasa_periodo.php?ve_tasa_interes_base="+tasa_interes_base+"&ve_dias="+dias, false);
		ajax.send(null);
		
		var resp = ajax.responseText.split('|');
		
		var tasa_interes_periodo = resp[0];
		
		document.getElementById('TASA_INTERES_PERIODO_0').innerHTML = number_format(tasa_interes_periodo, 5, ',', '.');
		document.getElementById('TASA_INTERES_PERIODO_H_0').value = tasa_interes_periodo
	
	}
	var monto_incial = document.getElementById('VALOR_INICIAL_0').value;
	if(monto_incial!=''){
		 calcula_intereses()
	}	
}

function calcula_dia(){
	
	var fecha_emision = document.getElementById('FECHA_EMISION_0').value;
	var fecha_vencto = document.getElementById('FECHA_VENCTO_0').value;
	
	if((fecha_emision != '')&&(fecha_vencto != '')){
		
		var ajax = nuevoAjax();
		ajax.open("GET", "ajax_calcula_dia.php?ve_fecha_emision="+fecha_emision+"&ve_fecha_vencto="+fecha_vencto, false);
		ajax.send(null);
		
		var resp = ajax.responseText.split('|');
		
		var dias = resp[0];
		
		document.getElementById('DIAS_0').innerHTML = dias;
		document.getElementById('DIAS_H_0').value = dias;
	
	var tasa_interes_base = document.getElementById('TASA_INTERES_BASE_0').value;
	if(tasa_interes_base!=''){
		 calcula_interes_periodo()
	}
	
	}	
}