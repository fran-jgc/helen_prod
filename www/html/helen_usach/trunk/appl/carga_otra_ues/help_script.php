<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once ("../../appl.ini");

$temp = new Template_appl('help_scipt.htm');
	
$temp->setVar("GEST_HELP_SCRIPT", '<table width="450"  name="GEST_HELP_SCRIPT_0" >
<b>Formato Carga Otras Ues</b>
<tr>
	<td colspan="3" class="titulo_tabla">
	Separado por tabulación:
	</td>
</tr>
<tr>
	<td class="oscuro">rut</td>
	<td class="oscuro">=>NUMBER(10)</td>
	<td class="oscuro">Obligatorio</td>
</tr>
<tr>
	<td class="claro">folio</td>
	<td class="claro">=> VARCHAR(100)</td>
	<td class="claro"></td>
</tr>
<tr>
	<td class="oscuro">ano_cuota</td>
	<td class="oscuro">=> NUMBER(4)</td>
	<td class="oscuro">Obligatorio(DEUDAS COBRANZA NORMAL)</td>
</tr>
<tr>
	<td class="claro">cod_deuda</td>
	<td class="claro">=> NUMBER(10)</td>
	<td class="claro">Obligatorio</td>
</tr>
<tr>
	<td class="oscuro">monto_utm</td>
	<td class="oscuro">=> NUMBER(14,4)	</td>
	<td class="oscuro"></td>
</tr>	
<tr>
	<td class="claro">monto_pesos</td>
	<td class="claro">=> NUMBER(14)</td>
	<td class="claro">Obligatorio</td>
</tr>
<tr>
	<td class="oscuro">fecha_pago</td>
	<td class="oscuro">=> Date(dd-mm-yyyy)</td>
	<td class="oscuro"></td>
</tr>
<tr>
	<td class="claro">cod_origen_pago</td>
	<td class="claro">=> NUMBER(14,4)</td>
	<td class="claro">Obligatorio</td>
</tr>
<tr>
	<td class="oscuro">fecha_deposito</td>
	<td class="oscuro">=> Date(dd-mm-yyyy)</td>
	<td class="oscuro">Obligatorio</td>
</tr>
<tr>
	<td class="claro">nro_cuota</td>
	<td class="claro">=> NUMBER(10)</td>
	<td class="claro"></td>
</tr>
<tr>
	<td class="oscuro">monto_descto_utm</td>
	<td class="oscuro">=> NUMBER(14,4)</td>
	<td class="oscuro"></td>
</tr>
<tr>
	<td class="claro">monto_descto_pesos</td>
	<td class="claro">=> NUMBER(14)</td>
	<td class="claro">Obligatorio</td>
</tr>
</table>');
		
print $temp->toString();
?>