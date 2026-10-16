function change_recibido(){
	// valida que seleccione al menos una cuota
	var vl_tabla = document.getElementById('R_PAGARE');
	var aTR = vl_tabla.getElementsByTagName("tr");
	var vl_una_checked = false;
	var vl_otra = true;
	var aTR = get_TR('R_PAGARE');
		for (var i=2; i < aTR.length-1; i++)
		{
			var record = get_num_rec_field(aTR[i].id);
			var vl_seleccion = document.getElementById('RECIBIDO_' + record);
			vl_una_checked = vl_seleccion.checked;
			if(vl_una_checked==false)
			{
				vl_otra=false;
			}
		}
		
		if(vl_otra==true)
		{
			var vl_seleccion_pagare = document.getElementById('DOC_PAGARES_0');
			var vl_seleccion_pagare_h = document.getElementById('DOC_PAGARES_H_0');
			vl_seleccion_pagare.checked=true;
			vl_seleccion_pagare.value='S';
			vl_seleccion_pagare_h.value='S';
		}else{
			var vl_seleccion_pagare = document.getElementById('DOC_PAGARES_0');
			var vl_seleccion_pagare_h = document.getElementById('DOC_PAGARES_H_0');
			vl_seleccion_pagare.checked=false;
			vl_seleccion_pagare.value='N';
			vl_seleccion_pagare_h.value='N';
		}
}

function checked_manifiesto() {
	var vl_manifiesto = document.getElementById('DOC_MANIFIESTO_0').checked;
	var vl_f_recep_h = document.getElementById('FECHA_RECEP_MANIFIESTO_H_0').value;
	var vl_f_recep = document.getElementById('FECHA_RECEP_MANIFIESTO_0').value;

	if(vl_manifiesto == true){
		document.getElementById('F_RECEP_TD').style.display = '';
		if (vl_f_recep == '')
		document.getElementById('FECHA_RECEP_MANIFIESTO_0').value = vl_f_recep_h;
	}else if (vl_manifiesto == false){
		document.getElementById('F_RECEP_TD').style.display = 'none';
		document.getElementById('FECHA_RECEP_MANIFIESTO_0').value = null;
	}
}