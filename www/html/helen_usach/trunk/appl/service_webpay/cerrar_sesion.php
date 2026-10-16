<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
//base::spd_lock_table_user();
session::destroy();

header("Location:inicio_sesion.php");
?>