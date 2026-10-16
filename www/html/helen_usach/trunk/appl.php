<?php
ini_set('display_errors','OFF');
define("K_ROOT_URL", "https://".$_SERVER['SERVER_NAME']."/helen_usach/trunk/");
define("K_ROOT_DIR", "/var/www/html/helen_usach/trunk/");
define("K_APPL", "helen_usach");
define("K_CLIENTE", "USACH");

/*ORACLE */
define("K_TIPO_BD", "oci");

/*define("K_SERVER", "(DESCRIPTION =(ADDRESS_LIST =(ADDRESS = (PROTOCOL = TCP)(HOST =158.170.10.209)(PORT = 1521)))(CONNECT_DATA = (SERVER = DEDICATED)(SERVICE_NAME = HELEN)(SRVR = DEDICATED)))");
define("K_BD", "helen");
define("K_USER", "HELEN");
define("K_PASS", "H3L3n2016");*/

//define("K_SERVER", "(DESCRIPTION =(ADDRESS_LIST =(ADDRESS = (PROTOCOL = TCP)(HOST =158.170.10.209)(PORT = 1521)))(CONNECT_DATA = (SERVER = DEDICATED)(SERVICE_NAME = HELEN)(SRVR = DEDICATED)))");
define("K_SERVER", "(DESCRIPTION =(ADDRESS_LIST =(ADDRESS = (PROTOCOL = TCP)(HOST =bdprdlegacy.usach.cl)(PORT = 1521)))(CONNECT_DATA = (SERVER = DEDICATED)(SERVICE_NAME = HELEN)(SRVR = DEDICATED)))");
define("K_BD", "helen");
define("K_USER", "HELEN");
define("K_PASS", "H3L3n2020");

/***Variables necesarias para la ejecucion de proceso***/
define("K_SQLPLUS","/usr/lib/oracle/11.2/client/bin/sqlplus");
define("K_SERVER_BD","dbprdlegacy.usach.cl:1521/helen");


?>