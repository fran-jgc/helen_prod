<?php

    $recibe_parametro = $_GET["archivo"];
    $parametro = '';
    
    if($recibe_parametro == 'Formulario_Ley_19287'){
    	$parametro = "imagen/Instructivo_Ley_19287.pdf";
    }
    if($recibe_parametro == 'Formulario_Ley_19848'){
    	$parametro = "imagen/Instructivo_Ley_19848.pdf";
    }
    if($recibe_parametro == 'Formulario_Ley_20572'){
    	$parametro = "imagen/Instructivo_Ley_20572.pdf";
    }
	if($recibe_parametro == 'INSTRUCTIVO_DECLARACION_JURADA_DE_INGRESOS'){
    	$parametro = "imagen/INSTRUCTIVO_DECLARACION_JURADA_DE_INGRESOS.pdf";
	}
	if($recibe_parametro == 'Inicio de Cobranza'){
    	$parametro = "imagen/Inicio de Cobranza.pdf";
		}
	if($recibe_parametro == 'Ley19287'){
    	$parametro = "imagen/Ley19287.pdf";
		}
	if($recibe_parametro == 'FONDO SOLIDARIO'){
    	$parametro = "imagen/FONDO SOLIDARIO.pdf";
		
			}
	if($recibe_parametro == 'TGR'){
    	$parametro = "imagen/TGR.pdf";
    
    }
    if($recibe_parametro == 'adobe_reader'){
    	$parametro = "imagen/Adobe_Reader_X_(10.1.3).exe";
    }
    
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"$parametro\"\n");
    $fp=fopen("$parametro", "r");
    fpassthru($fp);
?>