<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");


if (w_output::f_viene_del_menu('tipo_rebaja_pagare')) {
	$wo_tipo_rebaja_pagare = new wo_tipo_rebaja_pagare();
	$wo_tipo_rebaja_pagare->retrieve();
}
else {
    $wo = unserialize(session::get('wo_tipo_rebaja_pagare'));
	$wo->procesa_event();
	
}
?>