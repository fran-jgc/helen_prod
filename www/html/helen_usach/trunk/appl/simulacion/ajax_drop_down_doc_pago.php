<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$varios_documento_pagos = $_REQUEST['varios_documento_pagos'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = "SELECT		COD_TIPO_DOCUMENTO_PAGO
					, NOM_TIPO_DOCUMENTO_PAGO
		FROM 		TIPO_DOCUMENTO_PAGO
		WHERE		COD_TIPO_DOCUMENTO_PAGO IN ($varios_documento_pagos)
		ORDER BY 	COD_TIPO_DOCUMENTO_PAGO";

$result = $db->build_results($sql);
print urlencode(json_encode($result));
?>