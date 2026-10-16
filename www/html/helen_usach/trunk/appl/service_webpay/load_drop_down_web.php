<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$codigo_origen			= $_REQUEST["codigo_origen"];
$nom_tabla_origen		= $_REQUEST["nom_tabla_origen"];
$nom_tabla_dependiente	= $_REQUEST["nom_tabla_dependiente"];
$tipo_direccion			= $_REQUEST["tipo_direccion"];


$sql = "SELECT 	COD_$nom_tabla_dependiente
				,NOM_$nom_tabla_dependiente
		FROM	$nom_tabla_dependiente
		WHERE	COD_$nom_tabla_origen = {KEY1}";
/*????????????????????*/
$record = 0;
/*????????????????????*/

// En empresa al eligir un pasi distinto de chile, se caia porque no existen ciudades
$drop_down = new drop_down_dw("COD_".$nom_tabla_dependiente."_".$tipo_direccion, $sql, 90);
$drop_down->retrieve($codigo_origen);
$drop_down = $drop_down->draw_entrable('', $record);
if ($drop_down->drop_down_dependiente!='')
$drop_down = '/'.$drop_down->drop_down_dependiente.'_'.$record.'/'.$drop_down;
print $drop_down;
?>