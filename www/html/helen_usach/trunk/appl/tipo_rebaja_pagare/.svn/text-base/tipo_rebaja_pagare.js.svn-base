function valida_nom_tipo_rebaja_pagare(){
	var nom_tipo_rebaja_pagare = document.getElementById('NOM_TIPO_REBAJA_PAGARE_0').value;
	var ajax = nuevoAjax();
	ajax.open("GET","ajax_obtiene_nombre_tipo_rebaja_pagare.php?nom_tipo_rebaja_pagare="+nom_tipo_rebaja_pagare,false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	if (resp == 1) {
		alert('El nombre ingresado ya existe. Por favor ingrese otro nombre');
		document.getElementById('NOM_TIPO_REBAJA_PAGARE_0').value 	= null;
		document.getElementById('NOM_TIPO_REBAJA_PAGARE_0').focus();
	}
}