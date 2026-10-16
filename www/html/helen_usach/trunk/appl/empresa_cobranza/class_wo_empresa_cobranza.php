<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_empresa_cobranza extends w_parametrica
{
    function __construct()
   {
      
   	  $sql = sql_empresa_cobranza::get_sql('OUTPUT');
			
   	  parent::__construct('empresa_cobranza', $sql, $_REQUEST['cod_item_menu'], '051556');
      
      // headers
   	  $COD_EMPRESA_COBRANZA = new header_num('COD_EMPRESA_COBRANZA', 'COD_EMPRESA_COBRANZA', 'Código');
   	  $NOM_EMPRESA_COBRANZA = new header_text('NOM_EMPRESA_COBRANZA', 'NOM_EMPRESA_COBRANZA', 'Nombre Empresa Cobranza');
   	  $this->add_header($COD_EMPRESA_COBRANZA);
   	  $this->add_header($NOM_EMPRESA_COBRANZA);
   }
	
    
}
?>