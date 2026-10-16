<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_comuna extends w_parametrica
{
    function __construct()
   {   	
      $sql = sql_comuna::get_sql('OUTPUT');
			
      parent::__construct('comuna', $sql, $_REQUEST['cod_item_menu'], '0515');
      
      // headers
      $COD_COMUNA = new header_num('COD_COMUNA', 'COD_COMUNA', 'Código');
      $NOM_COMUNA = new header_text('NOM_COMUNA', 'NOM_COMUNA', 'COMUNA');
      
      $this->add_header($COD_COMUNA);
      $this->add_header($NOM_COMUNA);
      
   }
}
?>