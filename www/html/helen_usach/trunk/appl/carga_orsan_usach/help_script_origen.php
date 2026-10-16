<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once ("../../appl.ini");

$temp = new Template_appl('help_scipt_origen.htm');
	
$temp->setVar("GEST_HELP_SCRIPT_ORIGEN", '<table width="440">
<tr class="titulo_tabla"><td>Nombre</td><td>Valor</td></tr>
<tr>
	<td class="oscuro">ORIGEN ORSAN_CD</td>
	<td class="oscuro">10</td>
</tr>
<tr>
	<td class="claro">ORIGEN ORSAN_CDE</td>
	<td class="claro">11</td>
</tr>	
<tr>
	<td class="oscuro">ORIGEN ORSAN_CRUZ_19287</td>
	<td class="oscuro">12</td>
</tr>	
<tr>
	<td class="claro">ORIGEN ORSAN_CRUZ_19848</td>
	<td class="claro">13</td>
</tr>	
<tr>
	<td class="oscuro">ORIGEN ORSAN_DEU_PROP</td>
	<td class="oscuro">14</td>
</tr>	
<tr>
	<td class="claro">ORIGEN ORSAN_MOROSO</td>
	<td class="claro">15</td>
</tr>	
<tr>
	<td class="oscuro">ORIGEN ORSAN_REZAGA</td>
	<td class="oscuro">16</td>
</tr>	
<tr>
	<td class="claro">ORIGEN ORSAN_TGR</td>
	<td class="claro">17</td>
</tr>	
</table>
');
		
print $temp->toString();
?>