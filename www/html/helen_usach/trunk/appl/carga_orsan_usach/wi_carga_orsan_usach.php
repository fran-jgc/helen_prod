<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_input::f_viene_del_output('carga_orsan_usach')) {

    $wi = new wi_carga_orsan_usach($_REQUEST['cod_item_menu']);    
    $rec_no = $_REQUEST['rec_no'];
    $wi->goto_record($rec_no);
}
else {

    $wi = unserialize(session::get('wi_carga_orsan_usach'));
    $wi->procesa_event();
}
?>