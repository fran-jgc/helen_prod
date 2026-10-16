<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_perfil extends w_output
{
    function __construct()
    {
        //////////////// CLASS_SQL = OUTPUT ///////////////
        $sql = sql_perfil::get_sql('OUTPUT');
        
        parent::__construct('perfil', $sql, $_REQUEST['cod_item_menu']);
        
        // headers
        
        $cod_perfil = new header_num('COD_PERFIL', 'COD_PERFIL', 'Código');
        $nom_perfil = new header_text('NOM_PERFIL', 'NOM_PERFIL', 'Perfil');
        
        $this->add_header($cod_perfil);
        $this->add_header($nom_perfil);
    }
}
?>
