<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_estado_pagare extends w_parametrica
{
   function __construct()
   {   	
      $sql = sql_estado_pagare::get_sql('OUTPUT');
			
      parent::__construct('estado_pagare', $sql, $_REQUEST['cod_item_menu'], '0515');
      
      // headers
      $COD_ESTADO_PAGARE = new header_num('COD_ESTADO_PAGARE', 'COD_ESTADO_PAGARE', 'Código');
      $NOM_ESTADO_PAGARE = new header_text('NOM_ESTADO_PAGARE', 'NOM_ESTADO_PAGARE', 'Estado Pagaré');
      
      $this->add_header($COD_ESTADO_PAGARE);
      $this->add_header($NOM_ESTADO_PAGARE);      
   }	
}
?>