function validate(){
	/*DECLARACION JURADA*/
	var cod_afp = document.getElementById('COD_AFP_0').value;
	if(cod_afp == ''){ 
		alert('Debe ingresar "AFP" antes de grabar.');
		document.getElementById('COD_AFP_0').focus();
		return false;
	}
	
	var cod_estado_civil = document.getElementById('COD_ESTADO_CIVIL_0').value;
	if(cod_estado_civil == ''){ 
		alert('Debe ingresar "Estado Civil" antes de grabar.');
		document.getElementById('COD_ESTADO_CIVIL_0').focus();
		return false;
	}
	
	/********************************************************************************/
	/**************************** DECLARACION MES DEUDOR ****************************/
	/********************************************************************************/
	
	var monto_pesos_deudor_enero = document.getElementById('MONTO_DEUDOR_PESOS1_0').value;
	if(monto_pesos_deudor_enero == ''){ 
		alert('Debe ingresar "Monto" antes de grabar.');
		document.getElementById('MONTO_DEUDOR_PESOS1_0').focus();
		return false;
	}
		
	var monto_pesos_deudor_febrero = document.getElementById('MONTO_DEUDOR_PESOS1_1').value;
	if(monto_pesos_deudor_febrero == ''){ 
		alert('Debe ingresar "Monto" antes de grabar.');
		document.getElementById('MONTO_DEUDOR_PESOS1_1').focus();
		return false;
	}
	
	var monto_pesos_deudor_marzo = document.getElementById('MONTO_DEUDOR_PESOS1_2').value;
	if(monto_pesos_deudor_marzo == ''){ 
		alert('Debe ingresar "Monto" antes de grabar.');
		document.getElementById('MONTO_DEUDOR_PESOS1_2').focus();
		return false;
	}
	
	var monto_pesos_deudor_abril = document.getElementById('MONTO_DEUDOR_PESOS1_3').value;
	if(monto_pesos_deudor_abril == ''){ 
		alert('Debe ingresar "Monto" antes de grabar.');
		document.getElementById('MONTO_DEUDOR_PESOS1_3').focus();
		return false;
	}
	var monto_pesos_deudor_mayo = document.getElementById('MONTO_DEUDOR_PESOS1_4').value;
	if(monto_pesos_deudor_mayo == ''){ 
		alert('Debe ingresar "Monto" antes de grabar.');
		document.getElementById('MONTO_DEUDOR_PESOS1_4').focus();
		return false;
	}
	var monto_pesos_deudor_junio = document.getElementById('MONTO_DEUDOR_PESOS1_5').value;
	if(monto_pesos_deudor_junio == ''){ 
		alert('Debe ingresar "Monto" antes de grabar.');
		document.getElementById('MONTO_DEUDOR_PESOS1_5').focus();
		return false;
	}
	var monto_pesos_deudor_julio = document.getElementById('MONTO_DEUDOR_PESOS2_0').value;
	if(monto_pesos_deudor_julio == ''){ 
		alert('Debe ingresar "Monto" antes de grabar.');
		document.getElementById('MONTO_DEUDOR_PESOS2_0').focus();
		return false;
	}
	
	var monto_pesos_deudor_agosto = document.getElementById('MONTO_DEUDOR_PESOS2_1').value;
	if(monto_pesos_deudor_agosto == ''){ 
		alert('Debe ingresar "Monto" antes de grabar.');
		document.getElementById('MONTO_DEUDOR_PESOS2_1').focus();
		return false;
	}
	
	var monto_pesos_deudor_septiembre = document.getElementById('MONTO_DEUDOR_PESOS2_2').value;
	if(monto_pesos_deudor_septiembre == ''){ 
		alert('Debe ingresar "Monto" antes de grabar.');
		document.getElementById('MONTO_DEUDOR_PESOS2_2').focus();
		return false;
	}
	
	var monto_pesos_deudor_octubre = document.getElementById('MONTO_DEUDOR_PESOS2_3').value;
	if(monto_pesos_deudor_octubre == ''){ 
		alert('Debe ingresar "Monto" antes de grabar.');
		document.getElementById('MONTO_DEUDOR_PESOS2_3').focus();
		return false;
	}
	
	var monto_pesos_deudor_noviembre = document.getElementById('MONTO_DEUDOR_PESOS2_4').value;
	if(monto_pesos_deudor_noviembre == ''){ 
		alert('Debe ingresar "Monto" antes de grabar.');
		document.getElementById('MONTO_DEUDOR_PESOS2_4').focus();
		return false;
	}
	
	var monto_pesos_deudor_diciembre = document.getElementById('MONTO_DEUDOR_PESOS2_5').value; 
	if(monto_pesos_deudor_diciembre == ''){ 
		alert('Debe ingresar "Monto" antes de grabar.');
		document.getElementById('MONTO_DEUDOR_PESOS2_5').focus();
		return false;
	}
	
	/********************************************************************************/
	/**************************** DECLARACION MES CONYUGE ****************************/
	/********************************************************************************/
	var conyuge_declara = document.getElementById('CONYUGE_DECLARA_0').checked;
	if(conyuge_declara == true){
		var monto_pesos_conyuge_enero = document.getElementById('MONTO_CONYUGE_PESOS1_0').value;
		if(monto_pesos_conyuge_enero == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS1_0').focus();
			return false;
		}
		var monto_pesos_conyuge_febrero = document.getElementById('MONTO_CONYUGE_PESOS1_1').value;
		if(monto_pesos_conyuge_febrero == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS1_1').focus();
			return false;
		}
		var monto_pesos_conyuge_marzo = document.getElementById('MONTO_CONYUGE_PESOS1_2').value;
		if(monto_pesos_conyuge_marzo == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS1_2').focus();
			return false;
		}
		var monto_pesos_conyuge_abril = document.getElementById('MONTO_CONYUGE_PESOS1_3').value;
		if(monto_pesos_conyuge_abril == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS1_3').focus();
			return false;
		}
		var monto_pesos_conyuge_mayo = document.getElementById('MONTO_CONYUGE_PESOS1_4').value;
		if(monto_pesos_conyuge_mayo == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS1_4').focus();
			return false;
		}
		var monto_pesos_conyuge_junio = document.getElementById('MONTO_CONYUGE_PESOS1_5').value;
		if(monto_pesos_conyuge_junio == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS1_5').focus();
			return false;
		}
		var monto_pesos_conyuge_julio = document.getElementById('MONTO_CONYUGE_PESOS2_0').value;
		if(monto_pesos_conyuge_julio == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS2_0').focus();
			return false;
		}
		var monto_pesos_conyuge_agosto = document.getElementById('MONTO_CONYUGE_PESOS2_1').value;
		if(monto_pesos_conyuge_agosto == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS2_1').focus();
			return false;
		}
		var monto_pesos_conyuge_septiembre = document.getElementById('MONTO_CONYUGE_PESOS2_2').value;
		if(monto_pesos_conyuge_septiembre == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS2_2').focus();
			return false;
		}
		var monto_pesos_conyuge_octubre = document.getElementById('MONTO_CONYUGE_PESOS2_3').value;
		if(monto_pesos_conyuge_octubre == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS2_3').focus();
			return false;
		}
		var monto_pesos_conyuge_noviembre = document.getElementById('MONTO_CONYUGE_PESOS2_4').value;
		if(monto_pesos_conyuge_noviembre == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS2_4').focus();
			return false;
		}
		var monto_pesos_conyuge_diciembre = document.getElementById('MONTO_CONYUGE_PESOS2_5').value;
		if(monto_pesos_conyuge_diciembre == ''){ 
			alert('Debe ingresar "Monto" antes de grabar.');
			document.getElementById('MONTO_CONYUGE_PESOS2_5').focus();
			return false;
		}
	}
	return true;
}