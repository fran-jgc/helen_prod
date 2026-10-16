
function get_nom_field(field) {
	// dado un nombre tipo NOM_SUCURSAL_2 retorna el nombre del campo => "NOM_SUCURSAL
		var pos = field.lastIndexOf('_');
		return field.substr(0, pos);
}
function get_num_rec_field(field) {
	// dado un nombre tipo NOM_SUCURSAL_2 retorna el num del campo => "2"
		var pos = field.lastIndexOf('_');
		return field.substr(pos + 1, field.length - pos - 1);
}
function onlyNumbers(ve_obj, ve_evt, ve_max_num_dec, ve_solo_positivo){
    	var keynum;
    	
    	ve_obj.onpaste = function(e) {
    	    e.preventDefault();
    	    return false;
    	 }
    	ve_obj.oncopy = function(e) {
    	    e.preventDefault();
    	    return false;
    	}
    	if(window.event) // IE
    		keynum = ve_evt.keyCode;
    	else if(ve_evt.which) // Netscape/Firefox/Opera
    		keynum = ve_evt.which;
    	else // Para FF los carcaters tab, end, etc
    		keynum = ve_evt.keyCode;
    		
    	var vl_reg_coma = /\,/;
    	if (keynum==8	// backspace
    		|| keynum==9	// tab
    		|| keynum==35	// end
    		|| keynum==36	// home
    		|| keynum==37	// left
    		|| keynum==39	// right
    		|| keynum==13	// enter
    		)
    		return true;
    	else if (keynum==46 && ve_evt.charCode==0)	// sumpirmir
    		return true;
    	if ((keynum==99	// ctrl C
    		|| keynum==118	// ctrl V
    		|| keynum==120)	// ctrl X
    		&& ve_evt.ctrlKey)
    		return true;
    	else if((keynum >= 48 && keynum <= 57)) { // numeros
    		if (ve_max_num_dec > 0 && vl_reg_coma.test(ve_obj.value)) { // permite decimales y ya tiene la coma
    			var vl_pos = ve_obj.value.lastIndexOf(',');
    			var vl_pos_cursor = posicionCursor(ve_obj);
    			if (vl_pos_cursor <= vl_pos)	// se esta antes de la coma
    				return true;
    			var vl_cant_dec = ve_obj.value.length - vl_pos - 1;
    			if (vl_cant_dec >= ve_max_num_dec)
    				return false;
    		}
    		return true;
    	}
    	else if (keynum==45) { // minus
    		if (ve_solo_positivo)	// no permite negativos
    			return false;
    		else if (ve_obj.value.substr(0,1)=="-") // ya esta el caracter menos
    			return false
    		else if (posicionCursor(ve_obj)==0)
    			return true;
    	}
    	else if (keynum==44) { // coma
    		if (ve_max_num_dec ==0)	// no permite decimales
    			return false;
    		
    		if (vl_reg_coma.test(ve_obj.value))	// ya tiene la coma
    			return false;
    		else
    			return true;
    	}
    	else {
    		return false;
    	}
}
function URLDecode( encoded) {
	   // Replace + with ' '
	   // Replace %xx with equivalent character
	   // Put [ERROR] in output if %xx is invalid.
	   var HEXCHARS = "0123456789ABCDEFabcdef"; 
	   var plaintext = "";
	   var i = 0;
	   while (i < encoded.length) {
	       var ch = encoded.charAt(i);
		   if (ch == "+") {
		       plaintext += " ";
			   i++;
		   } else if (ch == "%") {
				if (i < (encoded.length-2) 
						&& HEXCHARS.indexOf(encoded.charAt(i+1)) != -1 
						&& HEXCHARS.indexOf(encoded.charAt(i+2)) != -1 ) {
					plaintext += unescape( encoded.substr(i,3) );
					i += 3;
				} else {
					alert( 'Bad escape combination near ...' + encoded.substr(i) );
					plaintext += "%[ERROR]";
					i++;
				}
			} else {
			   plaintext += ch;
			   i++;
			}
		} // while
		return plaintext;
}
//var computeds = new Array();
function campo_computed(field, formula, num_dec) {
	   this.field = field;
	   this.formula = formula;
	   this.num_dec = num_dec;
}
function computed(record, field_computed) {
	for (var i=0; i < computeds.length; i++)
		if (computeds[i].field==field_computed) {
			formula = computeds[i].formula;
			num_dec = computeds[i].num_dec;
			break;
		}

	var computed_field = document.getElementById(field_computed + '_' + record); 
	var expresion = /(\[[A-Z_0-9]+\])/g;
  	var res = formula.match(expresion);

	// calcula el computed		
	for (i=0; i < res.length; i++) {
		var field_formula = document.getElementById(res[i].substring(1, res[i].length - 1) + '_' + record);
		/* 
		Se implementa IF para solucionar error en IE, sobre el calculo con o sin IVA
		dentro de los Totales, Sabbagh modulo factura de honorarios
		*/
		if (navigator.appName=='Microsoft Internet Explorer'){
			valor = findAndReplace(get_value(field_formula.id), '.', '');
		}else{
			valor = get_value(field_formula.id);}
		
		valor = to_num(valor);
		formula = findAndReplace(formula, res[i], valor);
	}
	valor = eval(formula);
	valor = roundNumber(valor, num_dec);

	set_value(computed_field.id, valor, number_format(valor, num_dec, ',', '.'));
	var computed_field_h_id = field_computed + '_H_' + record;
	var computed_field_h = document.getElementById(computed_field_h_id); 
	var valor_old_computed = get_value(computed_field_h_id);
	set_value(computed_field_h_id, valor, number_format(valor, num_dec, ',', '.'));


	recalc_computed_relacionados(record, field_computed);

	// verifica si tiene accumulate y si tiene actualiza el total
	var acum = document.getElementById('SUM_' + field_computed + '_0'); 
	var acum_h = document.getElementById('SUM_' + field_computed + '_H_0'); 
	if (acum) {
		var valor_sum = acum_h.value;
		if (valor_sum=='') valor_sum = 0;
		if (valor_old_computed=='') valor_old_computed = 0;
		if (valor=='') valor = 0;
		valor_sum = parseFloat(valor_sum) - parseFloat(valor_old_computed) + parseFloat(valor);
		
		set_value(acum.id, valor_sum, number_format(valor_sum, num_dec, ',', '.'));
		set_value(acum_h.id, valor_sum, number_format(valor_sum, num_dec, ',', '.'));

		// Verifica si el campo SUM es usado en otro computed
		recalc_computed_relacionados(0, 'SUM_'+ field_computed);

		if (acum_h.onchange) {
			var f = acum_h.onchange;
			f();
		}
		
	}
}
function get_value(field_id) {
	var field = document.getElementById(field_id); 
	var valor = 0;
	if (field) {
		if (field.value!=null)
			valor = field.value;
		else if (field.options!=null)
			valor = field.options[field.selectedIndex].value;
		else
			valor = field.innerHTML;
	}
	return valor;
}
function to_num(num) {
	num = findAndReplace(num, '.', '');  // borra los puntos en los miles
	num = findAndReplace(num, ',', '.'); // cambia coma decimal por punto
	
	// Borra los ceros de la izquierda, para evitar que se interprete como un nro octal
	while (num.length > 0) {
		if (num.substr(0,1)=='0')
			num = num.substr(1, num.length  -  1);
		else
			break;
	}
	if (num=='') num = 0;
	return num;
}
function findAndReplace(ve_string, ve_valor_a_reemplazar, ve_valor_x_reemplazar){
    var i = 0, length = ve_string.length;

    for (i; i < length; i++) {
          ve_string = ve_string.replace(ve_valor_a_reemplazar, ve_valor_x_reemplazar);
    }
    return ve_string;
}
function roundNumber(rnum, rlength) { // Arguments: number to round, number of decimal places
	  var newnumber = Math.round(rnum*Math.pow(10,rlength))/Math.pow(10,rlength);
	  return newnumber;
}
function set_value(field_id, valor, value_fmt) {
	var field = document.getElementById(field_id); 
	if (field) {
		if (field.value!=null)
			field.value = valor;
		else if (field.options!=null)
			field.options[field.selectedIndex].value = valor;
		else
			field.innerHTML = value_fmt;
	}
}
function set_value_dropdown(ve_drop_down_id, ve_value) {
// Busca ve_value en los options del dropdown y deja selectedIndex apuntando al que corresponde
	var dropdown = document.getElementById(ve_drop_down_id);
	for(var i=0; i < dropdown.options.length; i++)
		if (dropdown.options[i].value ==  ve_value) {
			dropdown.selectedIndex = i;
			break;
		}
}
function number_format( number, decimals, dec_point, thousands_sep ) {
    // http://kevin.vanzonneveld.net
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +     bugfix by: Michael White (http://getsprink.com)
    // +     bugfix by: Benjamin Lupton
    // +     bugfix by: Allan Jensen (http://www.winternet.no)
    // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +     bugfix by: Howard Yeend
    // +    revised by: Luke Smith (http://lucassmith.name)
    // +     bugfix by: Diogo Resende
    // +     bugfix by: Rival
    // %        note 1: For 1000.55 result with precision 1 in FF/Opera is 1,000.5, but in IE is 1,000.6
    // *     example 1: number_format(1234.56);
    // *     returns 1: '1,235'
    // *     example 2: number_format(1234.56, 2, ',', ' ');
    // *     returns 2: '1 234,56'
    // *     example 3: number_format(1234.5678, 2, '.', '');
    // *     returns 3: '1234.57'
    // *     example 4: number_format(67, 2, ',', '.');
    // *     returns 4: '67,00'
    // *     example 5: number_format(1000);
    // *     returns 5: '1,000'
    // *     example 6: number_format(67.311, 2);
    // *     returns 6: '67.31'
 
    var n = number, prec = decimals;
    n = !isFinite(+n) ? 0 : +n;
    prec = !isFinite(+prec) ? 0 : Math.abs(prec);
    var sep = (typeof thousands_sep == "undefined") ? ',' : thousands_sep;
    var dec = (typeof dec_point == "undefined") ? '.' : dec_point;
 
    var s = (prec > 0) ? n.toFixed(prec) : Math.round(n).toFixed(prec); //fix for IE parseFloat(0.55).toFixed(0) = 0;
 
    var abs = Math.abs(n).toFixed(prec);
    var _, i;
 
    if (abs >= 1000) {
        _ = abs.split(/\D/);
        i = _[0].length % 3 || 3;
 
        _[0] = s.slice(0,i + (n < 0)) +
              _[0].slice(i).replace(/(\d{3})/g, sep+'$1');
 
        s = _.join(dec);
    } else {
        s = findAndReplace(s, '.', dec);
    }
 
    return s;
}
function recalc_computed_relacionados(record, field_computed) {
	var expresion = /(\[[A-Z_0-9]+\])/g;
	// Verifica si el campo es usado en otro computed
	for (var i=0; i < computeds.length; i++) {
		if (computeds[i].field!=field_computed) {
		  var res = computeds[i].formula.match(expresion);
			for (var j=0; j < res.length; j++)
				if (res[j]=='['+field_computed+']') {
					computed(record, computeds[i].field);
				}
		}
	}
}
function findAndReplace(ve_string, ve_valor_a_reemplazar, ve_valor_x_reemplazar){
    var i = 0, length = ve_string.length;

    for (i; i < length; i++) {
          ve_string = ve_string.replace(ve_valor_a_reemplazar, ve_valor_x_reemplazar);
    }
    return ve_string;
}