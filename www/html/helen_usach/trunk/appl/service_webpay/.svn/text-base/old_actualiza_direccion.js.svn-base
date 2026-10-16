function validate(){
	
	var direccion_p = document.getElementById('DIRECCION_PARTICULAR_0').value;
	if(direccion_p == ''){ 
		alert('Debe ingresar "Dirección Particular" antes de grabar.');
		document.getElementById('DIRECCION_PARTICULAR_0').focus();
		return false;
	}
	
	var pais_p = document.getElementById('COD_PAIS_PARTICULAR_0').value;
	if(pais_p == ''){ 
		alert('Debe ingresar "Pais Particular" antes de grabar.');
		document.getElementById('COD_PAIS_PARTICULAR_0').focus();
		return false;
	}
	
	var region_p = document.getElementById('COD_REGION_PARTICULAR_0').value;
	if(region_p == ''){ 
		alert('Debe ingresar "Región Particular" antes de grabar.');
		document.getElementById('COD_REGION_PARTICULAR_0').focus();
		return false;
	}
	
	var region_p = document.getElementById('COD_CIUDAD_PARTICULAR_0').value;
	if(region_p == ''){ 
		alert('Debe ingresar "Ciudad Particular" antes de grabar.');
		document.getElementById('COD_CIUDAD_PARTICULAR_0').focus();
		return false;
	}
	return true;
}
function clear_options(ve_id) {
	// borra todo las options del actual dropdown
	var vl_destino = document.getElementById(ve_id);
	while (vl_destino.firstChild)
	  vl_destino.removeChild(vl_destino.firstChild);
} 

function load_drop_down_web(ve_campo) {
	var vl_nom_campo = get_nom_field(ve_campo.id);
	var vl_cod_campo = get_nom_field(vl_nom_campo);		// COD_PAIS_PARTICULAR lo deja en COD_PAIS
	var vl_tipo = get_num_rec_field(vl_nom_campo); 		// COD_PAIS_PARTICULAR lo deja en PARTICULAR
	

	var ajax=nuevoAjax();
	ajax.open("GET", "ajax_drop_down_direccion.php?cod_campo="+vl_cod_campo+"&value="+ve_campo.value, false);
	ajax.send(null);
	
	var vl_resp = URLDecode(ajax.responseText);		
	var result = eval("(" + vl_resp + ")");
	
	// dropdown destino
	var vl_id_destino = '';
	var vl_cod = '';
	var vl_nom = ''
	switch (vl_cod_campo) {
		case 'COD_PAIS': 
			vl_id_destino = 'COD_REGION_' + vl_tipo + '_0';
			clear_options(vl_id_destino);
			clear_options('COD_CIUDAD_' + vl_tipo + '_0');
			clear_options('COD_COMUNA_' + vl_tipo + '_0');
			vl_cod = 'COD_REGION';
			vl_nom = 'NOM_REGION';
			break;
		case 'COD_REGION': 
			vl_id_destino = 'COD_CIUDAD_' + vl_tipo + '_0';
			clear_options(vl_id_destino);
			clear_options('COD_COMUNA_' + vl_tipo + '_0');
			vl_cod = 'COD_CIUDAD';
			vl_nom = 'NOM_CIUDAD';
			break;
		case 'COD_CIUDAD': 
			vl_id_destino = 'COD_COMUNA_' + vl_tipo + '_0';
			clear_options(vl_id_destino);
			vl_cod = 'COD_COMUNA';
			vl_nom = 'NOM_COMUNA';
			break;
	}
	var vl_destino = document.getElementById(vl_id_destino);
	// agrega vacio al comienzo
	var vl_option = document.createElement("option");
	vl_option.value = '';
	vl_option.innerHTML = ' '; 
	vl_destino.appendChild(vl_option); 
	
	// arma el dropdown a partir del resultado
	for (var i=0; i < result.length; i++) {
		vl_option = document.createElement("option");
		vl_option.value = result[i][vl_cod];
		vl_option.innerHTML = URLDecode(result[i][vl_nom]); 
		vl_destino.appendChild(vl_option); 
	}
}