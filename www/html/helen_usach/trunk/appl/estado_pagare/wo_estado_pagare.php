<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");


if (w_output::f_viene_del_menu('estado_pagare')) {
	$wo_estado_pagare = new wo_estado_pagare();	
	$wo_estado_pagare->retrieve();
}
else {
    $wo = unserialize(session::get('wo_estado_pagare'));
	$wo->procesa_event();
}
?>