function validate(){
	var valor_mensual = document.getElementById('VALOR_MENSUAL_0');
	var monto_valor_mensual = document.getElementById('MONTO_VALOR_MENSUAL_0');
	if(valor_mensual.checked == true && monto_valor_mensual.value == '') {		
			alert('Debe ingresar un Valor Mensual a la Moneda');
			monto_valor_mensual.focus();
			return false;
	}
	
	//////// que no se envien campos en blanco	
	for (i=1; i <= 31 ;i++) {
		var valor_campo = 	document.getElementById('VALOR'+i+'_0'); 
		var dia 		=	document.getElementById('DIA'+i+'_0').innerHTML;		
		if(valor_campo.value == ''){						
			alert('El día '+dia+' no debe quedar vacio,\n si no existe valor favor dejar en 0');
			valor_campo.focus();
			return false;
		}					
	}	
	return true;	
}	
function muestra_campo(){
	var valor_mensual = document.getElementById('VALOR_MENSUAL_0');
	var monto_valor_mensual = document.getElementById('DIV_MONTO_VALOR_MENSUAL');
	var tabla_valores = document.getElementById('TABLA_VALORES');	
	var cod_moneda = document.getElementById('COD_MONEDA').innerHTML;
		
	if(valor_mensual.checked == true){		
		tabla_valores.style.display = 'none';
		monto_valor_mensual.style.display = '';
	}
	else{		
		tabla_valores.style.display = '';
		monto_valor_mensual.style.display = 'none';
		for(i=1; i<=31;i++){
			document.getElementById('VALOR'+i+'_0').value = number_format(0, 2, ',', '.');
		}
	}
}
function cambia_mes_y_ano(){
	var mes_actual = document.getElementById('MES_0').value;
	var ano_actual = document.getElementById('ANO_0').value;
	var cod_moneda = document.getElementById('COD_MONEDA').innerHTML;
	
	var ajax = nuevoAjax();	
    ajax.open("GET", "get_valor_moneda.php?mes_actual="+mes_actual+"&ano_actual="+ano_actual+"&cod_moneda="+cod_moneda, false);        
    ajax.send(null);    
	var resp = ajax.responseText.split('|');			
    
    var vl_valor_mensual = document.getElementById('VALOR_MENSUAL_0').checked;
    if (vl_valor_mensual) {
		set_value('MONTO_VALOR_MENSUAL_0', resp[1], number_format(resp[1], 2, ',', '.'));
    }
    else {
		for (i=0, dia= 1; i < resp.length - 1; i += 2) {
			document.getElementById('DIA'+dia+'_0').innerHTML = resp[i];
			
			try{			
				var res = document.getElementById('VALOR'+dia+'_0').value;
				document.getElementById('VALOR'+dia+'_0').value = resp[i+1].replace(".","", 'g');
			}
			catch(err){
				document.getElementById(dia).innerHTML = resp[i+1];
				if(mes_actual == 2){			
					///// calculo bisiesto OUTPUT///////
					var a = ano_actual % 4;
					var b = ano_actual % 100;
					var c = ano_actual % 400;
					
					if (a == 0 && b != 0 || c == 0){					
						document.getElementById(29).innerHTML = resp[57];															
					}
												
					else{					
						document.getElementById(29).innerHTML = "";
					}
					//////////////////////////////
					document.getElementById(30).innerHTML = "";
					document.getElementById(31).innerHTML = "";
																				
				}			
				else if(mes_actual == 4 || mes_actual == 6 || mes_actual == 9 || mes_actual == 11){
					document.getElementById(31).innerHTML = "";						
				}						
			}
			dia++;
		}	
			
		if(mes_actual != 2){		
			document.getElementById('VALOR29_0').type = 'text';
			document.getElementById('VALOR30_0').type = 'text';
			document.getElementById('VALOR31_0').type = 'text';
			if(mes_actual == 4 || mes_actual == 6 || mes_actual == 9 || mes_actual == 11){
				document.getElementById('VALOR31_0').type = 'hidden';						
			}		
		}
		else{
			document.getElementById('VALOR29_0').type = 'hidden';
			document.getElementById('VALOR30_0').type = 'hidden';
			document.getElementById('VALOR31_0').type = 'hidden';				
		}	
		
		/////////////////// CALCULAR A�O BISIESTO INPUT///////////////////
		var a = ano_actual % 4;
		var b = ano_actual % 100;
		var c = ano_actual % 400;
			
		if (a == 0 && b != 0 || c == 0){		
			document.getElementById('VALOR29_0').type = 'text';
		}		
		/////////////////////////////////////////////////////////////
	}
}