<?php

    $recibe_parametro = $_GET["archivo"];
    $parametro = '';
    
    if($recibe_parametro == 'Formulario_Ley_19287'){
    	$parametro = "images/Formulario_Ley_19287.pdf";
    }
    if($recibe_parametro == 'Formulario_Ley_19848'){
    	$parametro = "images/Formulario_Ley_19848.pdf";
    }
    if($recibe_parametro == 'Formulario_Ley_20572'){
    	$parametro = "images/Formulario_Ley_20572.pdf";
    }
    if($recibe_parametro == 'adobe_reader'){
    	$parametro = "images/Adobe_Reader_X_(10.1.3).exe";
    }
    
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"$parametro\"\n");
    $fp=fopen("$parametro", "r");
    fpassthru($fp);
?>