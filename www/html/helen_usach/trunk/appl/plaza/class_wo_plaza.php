<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
class wo_plaza extends w_parametrica
{
    function __construct()
	{
		//////////////// CLASS_SQL = OUTPUT ///////////////	
		$sql = sql_plaza::get_sql('OUTPUT');
		
		parent::__construct('plaza', $sql, $_REQUEST['cod_item_menu'], '0515');
		// headers
		$COD_PLAZA = new header_num('COD_PLAZA', 'COD_PLAZA', 'Código');
		$NOM_PLAZA = new header_text('NOM_PLAZA', 'NOM_PLAZA', 'Plaza');
		
		$this->add_header($COD_PLAZA);
		$this->add_header($NOM_PLAZA);
	}
}
?>