function clear_options(ve_id) {
	// borra todo las options del actual dropdown
	var vl_destino = document.getElementById(ve_id);
	while (vl_destino.firstChild)
	  vl_destino.removeChild(vl_destino.firstChild);
} 


function load_drop_down(ve_campo){
	var vl_nom_campo = get_nom_field(ve_campo.id);
	var vl_cod_campo = get_nom_field(vl_nom_campo);		// COD_PAIS_PARTICULAR lo deja en COD_PAIS
	var vl_tipo = get_num_rec_field(vl_nom_campo); 		// COD_PAIS_PARTICULAR lo deja en PARTICULAR
	
	var ajax=nuevoAjax();
	ajax.open("GET", "ajax_drop_down.php?cod_campo="+vl_cod_campo+"&value="+ve_campo.value, false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	var result = eval("(" + resp + ")");
	var vl_destino = document.getElementById('COD_TIPO_DEUDA_0');
	
	//borra los options 
	clear_options('COD_TIPO_DEUDA_0');
	var vl_option = document.createElement("option");
	vl_option.value = '';
	vl_option.innerHTML = ' '; 
	vl_destino.appendChild(vl_option); 
	// arma el dropdown a partir del resultado
	for (var i=0; i < result.length; i++) {
		vl_option = document.createElement("option");
		vl_option.value = result[i]['COD_TIPO_DEUDA'];
		vl_option.innerHTML = result[i]['NOM_TIPO_DEUDA']; 
		vl_destino.appendChild(vl_option); 
	}
}