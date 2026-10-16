<?php
$arch = $_REQUEST['arch'];
$nom_carta = $_REQUEST['nom_carta'];

header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"$nom_carta.txt\"n");    	    	
print file_get_contents($arch);
?>