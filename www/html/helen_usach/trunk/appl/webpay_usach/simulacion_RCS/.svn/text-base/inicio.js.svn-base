function valida_datos()
{
	var retorna						= true;
	var mensaje						= '';
	var COD_UNIVERSIDAD_DEUDOR		= document.getElementById('COD_UNIVERSIDAD_DEUDOR_0').value;
	var RUT							= document.getElementById('RUT_0').value;
	var DIG_VERIF					= document.getElementById('DIG_VERIF_0').value;
	var NOMBRE						= document.getElementById('NOMBRE_0').value;
	var APELLIDO_PATERNO			= document.getElementById('APELLIDO_PATERNO_0').value;
	var DIRECCION					= document.getElementById('DIRECCION_0').value;
	var NOM_COMUNA					= document.getElementById('NOM_COMUNA_0').value;
	var NOM_CIUDAD					= document.getElementById('NOM_CIUDAD_0').value;
	var COD_DEUDA					= document.getElementById('COD_DEUDA_0');

	if(COD_UNIVERSIDAD_DEUDOR == 0)
		mensaje += '- UNIVERSIDAD \n';
	if(RUT == 0)
		mensaje += '- RUT \n';
	if(DIG_VERIF == ' ')
		mensaje += '- DIGITO VERIF \n';
	if(NOMBRE == ' ')
		mensaje += '- NOMBRE \n';
	if(APELLIDO_PATERNO == ' ')
		mensaje += '- APELLIDO PATERNO \n'
	if(DIRECCION == ' ')
		mensaje += '- DIRECCION \n';
	if(NOM_COMUNA == '')
		mensaje += '- COMUNA \n';
	if(NOM_CIUDAD == '')
		mensaje += '- CIUDAD \n';
		
	if(mensaje != '')
	{
		//mensaje += '\n Favor ingrese el o los datos faltantes en  \'Actualizaci\xf3n de informaci\xf3n \'';
		//alert('No Registra \n\n'+mensaje);
		
		alert('No es posible realizar la transacción, favor intente mas tarde');
		
		retorna =  false;
	}
	if(COD_DEUDA == null){
		//alert(' No registra deudas');
		alert('No es posible realizar la transacción, favor intente mas tarde');
		
		retorna =  false;
	}
		
	return retorna;
}
