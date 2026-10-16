<?php
function auto_load($class_name) {
	$ROOT = dirname(__FILE__);
	$file_name = $ROOT.'/appl/proc_common/class_w_proceso.php';
	require_once($file_name);
	if (substr($class_name, 0, 3)=='wp_') {
		$folder = substr($class_name, 3);
		$file_name = $ROOT.'/appl/proc_'.$folder.'/class_'.$class_name.'.php';
		if (file_exists($file_name)) {
			require_once($file_name);
    		return;
		}
	}
	else if ($class_name=='reporte_helen') {
		require_once($ROOT."/appl/common_appl/class_reporte_helen.php");
	}
    else if ($class_name=='print_plan_pago') {
        require_once($ROOT."/appl/common_appl/class_print_plan_pago.php");
	}
	else if ($class_name=='print_boleta') {
	    require_once($ROOT."/appl/boleta/USACH/class_print_boleta.php");
	}   
	else if ($class_name=='print_suspension') {
	    require_once($ROOT."/appl/suspension/USACH/class_print_suspension.php");
	}else if ($class_name=='print_prorroga') {
	    require_once($ROOT."/appl/prorroga/USACH/class_print_prorroga.php");
	}else if ($class_name=='certificado_ingreso') {
	    require_once($ROOT."/appl/common_appl/class_certificado_ingreso.php");
	}else if ($class_name=='print_ingreso') {
	    require_once($ROOT."/appl/common_appl/class_print_ingreso.php");
	}else if ($class_name=='print_ingreso_usach') {
	    require_once($ROOT."/appl/ingreso/USACH/class_print_ingreso_usach.php");
	}else if ($class_name=='print_reprogramacion') {
	    require_once($ROOT."/appl/reprogramacion/class_print_reprogramacion.php");
	}else if ($class_name=='print_condonacion') {
	    require_once($ROOT."/appl/condonacion/USACH/class_print_condonacion.php");
	}else if ($class_name=='print_declaracion_usach') {
	    require_once($ROOT."/appl/declaracion/USACH/class_print_declaracion.php");
	}else if ($class_name=='print_devolucion_pagare_2') {
	    require_once($ROOT."/appl/deuda/class_print_devolucion_pagare_2.php");
	}else if ($class_name=='print_devolucion_pagare_dos') {
	    require_once($ROOT."/appl/devolucion_pagare/class_print_devolucion_pagare_dos.php");
	}
}//certificado_ingreso
?>