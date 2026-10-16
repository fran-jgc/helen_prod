<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once ("../../appl.ini");

$cod_item_carga = $_GET['cod_item_carga'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql= 	"SELECT TO_CHAR(BT.FECHA_REGISTRO,'dd/mm/yyyy') FECHA_REGISTRO
			   ,U.NOM_USUARIO
			   ,BT.OBSERVACION
		 FROM BITACORA_CARGA_ORSAN BT left outer join USUARIO U on BT.COD_USUARIO = U.COD_USUARIO
		 where COD_ITEM_CARGA_ORSAN_USACH = $cod_item_carga
		 order by FECHA_REGISTRO";
$result = $db->build_results($sql);

$items = "";
for($i=0; $i < count($result); $i++){

	if (($i % 2) == 0){
		$style= "class='oscuro'";
	}
	else{
		$style= "class='claro'";
	}
	
	$fecha_reg = $result[$i]['FECHA_REGISTRO'];
	$usuario = $result[$i]['NOM_USUARIO'];
	$obs = $result[$i]['OBSERVACION'];
	
	$items = $items."<tr>
					 <td width='100' $style>$fecha_reg</td>
					 <td width='100' $style>$usuario</td>
					 <td width='250' $style>$obs</td>
				   </tr>";
}

$temp = new Template_appl('help_scipt_bitacora.htm');
$temp->setVar("ITEM_BITACORA", $items);
print $temp->toString();
?>