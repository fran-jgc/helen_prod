<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_usuario extends w_output {
    function __construct(){
        $sql = sql_usuario::get_sql('OUTPUT');
        w_output::__construct('usuario', $sql, $_REQUEST['cod_item_menu']);
        
        // headers
        $cod_usuario = new header_num('COD_USUARIO', 'COD_USUARIO', 'Código');
        $nom_usuario = new header_text('NOM_USUARIO', 'U.NOM_USUARIO', 'Usuario');
        $login = new header_text('LOGIN', 'U.LOGIN', 'Login');
        
        $sql_perfil = sql_usuario::get_sql('DD_COD_PERFIL');
        $perfil = new header_drop_down('NOM_PERFIL', 'P.COD_PERFIL', 'Perfil', $sql_perfil);
        
        $this->add_header($cod_usuario);
        $this->add_header($nom_usuario);
        $this->add_header($login);
        $this->add_header($perfil);
    }
}
?>