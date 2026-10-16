<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_tipo_rebaja_pagare extends w_parametrica
{
    function __construct()
   {
      $sql = sql_tipo_rebaja_pagare::get_sql('OUTPUT');
			
      parent::__construct('tipo_rebaja_pagare', $sql, $_REQUEST['cod_item_menu'], '51560');
      
      // headers
      $COD_TIPO_REBAJA_PAGARE = new header_num('COD_TIPO_REBAJA_PAGARE', 'COD_TIPO_REBAJA_PAGARE', 'Código');
      $NOM_TIPO_REBAJA_PAGARE = new header_text('NOM_TIPO_REBAJA_PAGARE', 'NOM_TIPO_REBAJA_PAGARE', 'Nombre Tipo Rebaja Pagaré');
      $this->add_header($COD_TIPO_REBAJA_PAGARE);
      $this->add_header($NOM_TIPO_REBAJA_PAGARE);
   }
	

}
?>