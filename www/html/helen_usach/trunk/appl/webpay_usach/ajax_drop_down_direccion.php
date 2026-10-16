<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once ("../../appl.ini");
session::set('K_ROOT_URL', K_ROOT_URL);
session::set('K_ROOT_DIR', K_ROOT_DIR);
session::set('K_APPL', K_APPL);

// La commonlib_seven usa estos datos
session::set("COD_USUARIO","2");
session::set("NOM_USUARIO","USUARIO WEB");

$cod_campo = $_REQUEST['cod_campo'];
$value = $_REQUEST['value'];

switch ($cod_campo) {
	case 'COD_PAIS': 
	    $sql = "SELECT 		COD_REGION,
										NOM_REGION
							FROM 		REGION
							WHERE 		COD_PAIS = $value
							AND      COD_REGION <> 99999
							ORDER BY	COD_REGION";
		$nom = 'NOM_REGION';
		break;
	case 'COD_REGION': 
	    $sql = "SELECT 		COD_CIUDAD,
										NOM_CIUDAD
							FROM 		CIUDAD
							WHERE 		COD_REGION = $value
							AND      COD_CIUDAD <> 99999
							ORDER BY	NOM_CIUDAD";
		$nom = 'NOM_CIUDAD';
		break;
	case 'COD_CIUDAD': 
	    $sql = "select 		COD_COMUNA,
										NOM_COMUNA
							from 		COMUNA
							where 		COD_CIUDAD = $value
							order by	NOM_COMUNA";
		$nom = 'NOM_COMUNA';
		break;
}
$sql = base::replace_KEYS($sql, array($value));
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);

print urlencode(json_encode($result));
?>