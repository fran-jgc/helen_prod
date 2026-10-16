function obtiene_direccion(cod_deudor){
    var ajax = nuevoAjax();
	
    // console.log(cod_dev_pagare);

	ajax.open("GET", "ajax_obtiene_direccion.php?COD_DEUDOR=" + cod_deudor , false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
    return resp;
}
function cambio_estado() {
    var ESTADO_RETIRO_PRESENCIAL    = 1;
    var ESTADO_ENVIO_DOMICILIO      = 2;
    var ESTADO_ENVIO_EMAIL          = 3;

    var cod_estado_dev_pagare = document.getElementById("COD_ESTADO_DEVOLUCION_PAGARE_0").value;
    var cod_deudor = document.getElementById("COD_DEUDOR_H_0").value;
    document.getElementById("COD_ESTADO_DEV_PAGARE_H_0").innerHTML = cod_estado_dev_pagare;

    if (cod_estado_dev_pagare == ESTADO_RETIRO_PRESENCIAL) {
        
        habilita_input_presencial();
        var estado_retiro = document.getElementById("COD_ESTADO_RETIRO_PRESENCIAL_0");
        if (estado_retiro) {
            estado_retiro.value = 1;
        }
        document.getElementById("tab_retiro").style.display="";
        document.getElementById("tab_envio_domicilio").style.display="none";
        document.getElementById("tab_envio_email").style.display="none";
        document.getElementById("DIRECCION_ENVIO_DOMICILIO_0").value="";
    }
    if (cod_estado_dev_pagare == ESTADO_ENVIO_DOMICILIO) {
        document.getElementById("tab_retiro").style.display="none";
        document.getElementById("tab_envio_domicilio").style.display="";
        document.getElementById("tab_envio_email").style.display="none";
        document.getElementById("DIRECCION_ENVIO_DOMICILIO_0").value=obtiene_direccion(cod_deudor);
    }
    if (cod_estado_dev_pagare == ESTADO_ENVIO_EMAIL) {
        document.getElementById("tab_retiro").style.display="none";
        document.getElementById("tab_envio_domicilio").style.display="none";
        document.getElementById("tab_envio_email").style.display="";
        document.getElementById("DIRECCION_ENVIO_DOMICILIO_0").value="";
    }
    if (cod_estado_dev_pagare == '') {
        document.getElementById("tab_retiro").style.display="none";
        document.getElementById("tab_envio_domicilio").style.display="none";
        document.getElementById("tab_envio_email").style.display="none";
        document.getElementById("DIRECCION_ENVIO_DOMICILIO_0").value="";
    }

}

function cambio_estado_envio_domicilio() {

    var EN_PREPARACION         = 1;
    var ENVIADO_EXITOSO         = 2;
    var ENVIADO_FALLIDO         = 3;
    var cod_estado_envio_dom = document.getElementById("COD_ESTADO_ENVIO_DOMICILIO_0").value;

    if (cod_estado_envio_dom == ENVIADO_EXITOSO) {
        document.getElementById("tab_enviado_exitoso_1").style.display="";
        document.getElementById("tab_enviado_exitoso_2").style.display="";
        document.getElementById("tab_enviado_fallido_1").style.display="none";
        document.getElementById("tab_enviado_fallido_2").style.display="none";
    }
    if (cod_estado_envio_dom == ENVIADO_FALLIDO) {
        document.getElementById("tab_enviado_exitoso_1").style.display="none";
        document.getElementById("tab_enviado_exitoso_2").style.display="none";
        document.getElementById("tab_enviado_fallido_1").style.display="";
        document.getElementById("tab_enviado_fallido_2").style.display="";
    }
    if (cod_estado_envio_dom == EN_PREPARACION || cod_estado_envio_dom=='') {
        document.getElementById("tab_enviado_exitoso_1").style.display="none";
        document.getElementById("tab_enviado_exitoso_2").style.display="none";
        document.getElementById("tab_enviado_fallido_1").style.display="none";
        document.getElementById("tab_enviado_fallido_2").style.display="none";
    }

}
function load_estados() {
    
    var ENVIO_POR_EMAIL = 3;
    var ENVIO_DOMICILIO	= 2;
    var RETIRO_PRESENCIAL = 1;

   var DOMICILIO_EN_PREPARACION   =1;
   var DOMICILIO_ENVIADO_EXITOSO  =2;
   var DOMICILIO_ENVIADO_FALLIDO  =3;

    var ajax = nuevoAjax();
    cod_dev_pagare = document.getElementById("COD_DEVOLUCION_PAGARE_H_0").innerHTML ;
	
    // console.log(cod_dev_pagare);

	ajax.open("GET", "ajax_consulta_estado.php?COD_DEV_PAGARE=" + cod_dev_pagare , false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
	var lista = resp.split('|');
    var COD_ESTADO_DEV_PAGARE=lista[0];
    var COD_ESTADO_ENVIO_DOM=lista[1];
    
    if (COD_ESTADO_DEV_PAGARE == RETIRO_PRESENCIAL) {
        document.getElementById("tab_retiro").style.display="";
        document.getElementById("tab_envio_domicilio").style.display="none";
        document.getElementById("tab_envio_email").style.display="none";

    }
    else if (COD_ESTADO_DEV_PAGARE == ENVIO_DOMICILIO) {
        
        document.getElementById("tab_retiro").style.display="none";
        document.getElementById("tab_envio_domicilio").style.display="";
        document.getElementById("tab_envio_email").style.display="none";

        if (COD_ESTADO_ENVIO_DOM == DOMICILIO_ENVIADO_EXITOSO) {
        
            document.getElementById("tab_enviado_exitoso_1").style.display="";
            document.getElementById("tab_enviado_exitoso_2").style.display="";
            document.getElementById("tab_enviado_fallido_1").style.display="none";
            document.getElementById("tab_enviado_fallido_2").style.display="none";

        }
        if (COD_ESTADO_ENVIO_DOM == DOMICILIO_ENVIADO_FALLIDO) {
        
            document.getElementById("tab_enviado_exitoso_1").style.display="none";
            document.getElementById("tab_enviado_exitoso_2").style.display="none";
            document.getElementById("tab_enviado_fallido_1").style.display="";
            document.getElementById("tab_enviado_fallido_2").style.display="";
            
        }
    } 
    else if (COD_ESTADO_DEV_PAGARE == ENVIO_POR_EMAIL) {

        document.getElementById("tab_retiro").style.display="none";
        document.getElementById("tab_envio_domicilio").style.display="none";
        document.getElementById("tab_envio_email").style.display="";
    }else{
        document.getElementById("tab_retiro").style.display="none";
        document.getElementById("tab_envio_domicilio").style.display="none";
        document.getElementById("tab_envio_email").style.display="none";
    }

    // FIX: si el registro ya viene con "Deudor" o "Poder Simple" guardado
    // desde antes (registro existente), el radio carga ya marcado y el
    // onChange nunca se dispara al solo presionar "Guardar". Sincronizamos
    // aquí también (sin borrar RUT/Nombre ya guardados) para que
    // "Estado Retiro" quede en 2 (Devuelto) sin depender de que el usuario
    // vuelva a clickear el radio.
    habilita_input_presencial(false);
}
function habilita_input_presencial(b_reset){
    if (typeof b_reset === 'undefined') { b_reset = true; }
    var ESTADO_RETIRO_DEVUELTO = 2; // KE_ESTADO_RETIRO_PRE_DEVUELTO en spu_devolucion_pagare

    var seleccion = document.getElementsByName('RETIRADO_POR');
    var estado_retiro = document.getElementById("COD_ESTADO_RETIRO_PRESENCIAL_0");

    if (estado_retiro) {
        estado_retiro.value = '';
    }

    for (let i = 0; i < seleccion.length; i++) {
       if(seleccion[i].checked==true){
        document.getElementById("RUT_RETIRA_0").disabled = false;
        document.getElementById("NOM_RETIRA_0").disabled = false;
        if (b_reset) {
            document.getElementById("RUT_RETIRA_0").value='';
            document.getElementById("NOM_RETIRA_0").value='';
        }

        // FIX: al elegir quién retira (Deudor / Poder Simple) el retiro
        // presencial debe quedar confirmado con subestado "devuelto".
        // Si se deja vacío, el SP recibe NULL y no ejecuta la bitácora.
        if (estado_retiro) {
            estado_retiro.value = ESTADO_RETIRO_DEVUELTO;
        }
       }else{
        document.getElementById("RUT_RETIRA_0").disabled = true;
        document.getElementById("NOM_RETIRA_0").disabled = true;
       }
    }
}
function cargar_deudas(){

    //elimina body de la tabla DEUDAS_CANCELADAS
    if(document.getElementById('DEUDAS_CANCELADAS_BODY')){
        var element = document.getElementById('DEUDAS_CANCELADAS_BODY');
        element.parentNode.removeChild(element);
    }
	
	var vl_tabla_deudas_canceladas = document.getElementById('DEUDAS_CANCELADAS');
	var vl_tbody_deudas_c = document.createElement('tbody');
    vl_tbody_deudas_c.id = 'DEUDAS_CANCELADAS_BODY';

    var ve_rut = document.getElementById("RUT_DEUDOR_0").value;
    var ajax = nuevoAjax();
	ajax.open("GET", "ajax_cargar_deudas_canceladas.php?rut="+ve_rut, false);
	ajax.send(null);
	var resp = URLDecode(ajax.responseText);
    
    if(resp=='DEUDAS_SIN_ESTADO_DEV_PAG'){
        alert('El deudor posee deudas sin estado de devolución registradas en el módulo.')
    }else if(resp=='SIN_DEUDAS_CANCELADAS'){
        alert('El Deudor no posee deudas canceladas con pagares para devolución');
    }else{
        var result = eval("(" + resp + ")");
        for (var i = 0; i < (result.length); i++) {
            var vl_tr = document.createElement("tr");
            vl_tr.id = 'DEUDAS_CANCELADAS_' + i;
            vl_tr.setAttribute("height", "23px");				
            if (i%2==0)
                vl_tr.className="claro";
            else
                vl_tr.className="oscuro";
                
                
            var vl_td_cod_deuda = document.createElement("td");
            vl_td_cod_deuda.width = "15%";
            vl_td_cod_deuda.align = "center";
            vl_tr.appendChild(vl_td_cod_deuda);
                var vl_button_deuda = document.createElement("button");
                vl_button_deuda.value = result[i]['COD_DEUDA'];
                vl_button_deuda.innerHTML = 'Seleccionar';
                vl_button_deuda.id = 'ITEM_COD_DEUDA_B_' + i;
                vl_button_deuda.onclick = function(){okMe(this,ve_rut);};
                vl_td_cod_deuda.appendChild(vl_button_deuda);

            var vl_td_cod_deuda = document.createElement("td");
            vl_td_cod_deuda.width = "15%";
            vl_td_cod_deuda.align = "center";
            vl_tr.appendChild(vl_td_cod_deuda);
                var vl_label_cod_deuda = document.createElement("label");
                vl_label_cod_deuda.innerHTML = result[i]['COD_DEUDA'];
                vl_label_cod_deuda.id = 'ITEM_COD_DEUDA_' + i;
                vl_td_cod_deuda.appendChild(vl_label_cod_deuda);

            var vl_td_estado = document.createElement("td");
            vl_td_estado.width = "15%";
            vl_td_estado.align = "center";
            vl_tr.appendChild(vl_td_estado);
                var vl_label_estado = document.createElement("label");
                vl_label_estado.innerHTML = result[i]['NOM_ESTADO_DEUDA'];
                vl_label_estado.id = 'ITEM_ESTADO_DEUDA_' + i;
                vl_td_estado.appendChild(vl_label_estado);

            var vl_td_tipo_deuda = document.createElement("td");
            vl_td_tipo_deuda.width = "15%";
            vl_td_tipo_deuda.align = "center";
            vl_tr.appendChild(vl_td_tipo_deuda);
                var vl_label_tipo_deuda = document.createElement("label");
                vl_label_tipo_deuda.innerHTML = result[i]['COD_TIPO_DEUDA'];
                vl_label_tipo_deuda.id = 'ITEM_TIPO_DEUDA_' + i;
                vl_td_tipo_deuda.appendChild(vl_label_tipo_deuda);

            vl_tbody_deudas_c.appendChild(vl_tr); 
            vl_tabla_deudas_canceladas.appendChild(vl_tbody_deudas_c);
        }
    }
   
}
function del_table(vl_table) {
	//TABLE_TOTAL_PAGO
	var vl_tabla_delete = document.getElementById(vl_table);
	// borra todos los tr
	while (vl_tabla_delete.firstChild) {
	  vl_tabla_delete.removeChild(vl_tabla_delete.firstChild);
	}
}