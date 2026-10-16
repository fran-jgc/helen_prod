

function forzar_focus(){
	var rut = document.getElementById('RUT_0').value;
	var dato = document.getElementById('NOMBRE_0').innerHTML;
	if(dato == '' && !document.getElementById('RUT_0').focus())
		document.getElementById('RUT_0').focus();
	else
		document.getElementById('COD_TIPO_CONDONACION_0').style.border='1px solid #FF0000';
}
function valida_rut(rut){	
	if(rut == ' ' || rut == '')	
		return false;
	else
		return true;
}

function f_get_name_user() {
	// RESPUESTAS DEL AJAX:
	// O = EL RUT NO EXISTE EN TABLA DEUDOR (PUEDE INGRESARLO)
	// 1 = EL RUT EXISTE 	
	var rut = document.getElementById('RUT_0').value;
	
	if(valida_rut(rut) == false){
		alert('Debe ingresar Rut para continuar');
		document.getElementById('RUT_0').focus();
	}
	else {	
		var ajax = nuevoAjax();
		ajax.open("GET", "ajax_obtiene_nombre.php?rut="+rut, false);
		ajax.send(null);
		if (ajax.readyState==4) {
		
			var existe  = URLDecode(ajax.responseText);
			var resp	= existe.split('|');
				
			if (resp[0] == 0) {
				alert('No existe Deudor con ese Rut');
				f_set_null();
			}
			else if (resp[0] == 1) {
				alert('Deudor ya Condonado');
				f_set_null();
			}
			else if (resp[0] == "NO_DEUDAS") {
				alert('Deudor sin deudas para condonar');
				f_set_null();
			}
			else if (resp[0] == "CONDONA_SOLICITADA") {
				alert('Deudor ya posee condonación solicitada');
				f_set_null();
			}
			else if (resp[0] == "CARTERA_DIFERENTE") {
				alert('El Deudor tiene una deuda con otra Cartera distinta a la por defecto');
				f_set_null();
			}
			else {
				var resp = ajax.responseText.split('|');
				document.getElementById('NOMBRE_0').innerHTML = to_num(resp[0]);
				document.getElementById('NOMBRE_0').value = to_num(resp[0]);
				document.getElementById('DIG_VERIF_0').innerHTML = to_num(resp[1]);
				document.getElementById('DIG_VERIF_0').value = to_num(resp[1]);
				document.getElementById('COD_DEUDOR_0').innerHTML = to_num(resp[2]);
				document.getElementById('COD_DEUDOR_0').value = to_num(resp[2]);
			}	
		}
	}		
}
function f_set_null(){
	document.getElementById('RUT_0').innerHTML = '';
	document.getElementById('RUT_0').value = ''				
	document.getElementById('NOMBRE_0').innerHTML = '';
	document.getElementById('NOMBRE_0').value = '';
	document.getElementById('DIG_VERIF_0').innerHTML = '';
	document.getElementById('DIG_VERIF_0').value = '';
	document.getElementById('COD_DEUDOR_0').innerHTML = '';
	document.getElementById('COD_DEUDOR_0').value = '';
}

function f_get_deudas(){	
	var rut = document.getElementById('RUT_0').value;
	var tabla = document.getElementById('DETALLE_DEUDA');	
	// borra todos los tr
	while (tabla.firstChild) {
	  tabla.removeChild(tabla.firstChild);
	}

	var ajax = nuevoAjax();
	ajax.open("GET", "ajax_obtiene_deuda.php?rut="+rut, false);
	ajax.send(null);
					
	var resp = ajax.responseText;	
	
	// Copia los TR a la tabla correspondiente, 
	// este codigo se copio desde general.js -> add_line
	var table_aux = document.createElement("TABLE"); 
	table_aux.innerHTML = resp;
	var children = table_aux.childNodes;
	for (var i=0; i < children.length; i++) {
		if (children[i].nodeName=='TBODY') {
		  	var children2 = children[i].childNodes;
		  	for (j=0; j < children2.length; j++) {
				if (children2[j].nodeName=='TR') {
					var tr_contenido = children2[j].innerHTML;
						
					var tbody = null; 
					var child_tabla = tabla.childNodes;
					for (k=0; k < child_tabla.length; k++)
						if (child_tabla[k].nodeName=='TBODY') {
							tbody = child_tabla[k];
							break;
						}
					if (! tbody) {
						tbody = document.createElement("TBODY"); 
						tabla.appendChild(tbody);
					}		
					tbody.appendChild(children2[j]);
				}
			}
		}
	}
}
function f_usuario_confirma(ve_nom_usuario,ve_fecha){
	document.getElementById('NOM_USUARIO_CONFIRMA_0').innerHTML 	= '';
	document.getElementById('NOM_USUARIO_CONFIRMA_0').value 		= '';	
	document.getElementById('FECHA_CONFIRMA_NO_ING_0').innerHTML 	= '';
	document.getElementById('FECHA_CONFIRMA_NO_ING_0').value 		= '';
	
	var est_declaracion = document.getElementById('COD_ESTADO_CONDONACION_0').value;
	
	if(est_declaracion == 2)
	{
		document.getElementById('NOM_USUARIO_CONFIRMA_0').innerHTML     = ve_nom_usuario;
		document.getElementById('NOM_USUARIO_CONFIRMA_0').value 		= ve_nom_usuario;
	
		document.getElementById('FECHA_CONFIRMA_NO_ING_0').innerHTML 	= ve_fecha;
		document.getElementById('FECHA_CONFIRMA_NO_ING_0').value 		= ve_fecha;
	} 
}