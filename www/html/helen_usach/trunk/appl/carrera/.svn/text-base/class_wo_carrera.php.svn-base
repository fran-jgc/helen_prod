<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_carrera extends w_parametrica
{
    function __construct()
   {
      $sql = sql_carrera::get_sql('OUTPUT');
			
      parent::__construct('carrera', $sql, $_REQUEST['cod_item_menu'], '0515');
      
      // headers
      $COD_CARRERA = new header_num('COD_CARRERA', 'COD_CARRERA', 'Código');
      $NOM_CARRERA = new header_text('NOM_CARRERA', 'NOM_CARRERA', 'Nombre Carrera');
      $sql_facultad = sql_carrera::get_sql('DD_FACULTAD_OUTPUT');
      $NOM_FACULTAD = new header_drop_down_string('NOM_FACULTAD', 'F.COD_FACULTAD', 'Facultad', $sql_facultad);
      
      $this->add_header($COD_CARRERA);
      $this->add_header($NOM_CARRERA);
      $this->add_header($NOM_FACULTAD);
   }
	
    
}
?>