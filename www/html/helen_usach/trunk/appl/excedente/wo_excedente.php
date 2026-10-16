<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('excedente')) {
	$wo_excedente = new wo_excedente();
	$wo_excedente->retrieve();
}
else {
    $wo = unserialize(session::get('wo_excedente'));
	$wo->procesa_event();

}
?>