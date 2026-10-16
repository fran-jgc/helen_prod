<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('devolucion_pagare')) {
	$wo_devolucion_pagare = new wo_devolucion_pagare();
	$wo_devolucion_pagare->retrieve();
}
else {
    $wo = unserialize(session::get('wo_devolucion_pagare'));
	$wo->procesa_event();
	
}
?>
