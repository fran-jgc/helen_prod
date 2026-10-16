<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
 
class wo_anula_abono extends w_output
{
   function __construct()
   {   	
      $sql = sql_anula_abono::get_sql('OUTPUT');
							
      parent::__construct('anula_abono', $sql, $_REQUEST['cod_item_menu']);
      
      //formato numeros
	  $this->dw->add_control(new static_num('RUT'));
	  $this->dw->add_control(new static_num('MONTO_PESOS'));
	  
      // headers
	  $COD_ANULA_ABONO = new header_num('COD_ANULA_ABONO', 'COD_ANULA_ABONO', 'N° Anulación');
	  $FECHA_ANULA_ABONO = new header_date('FECHA_ANULA_ABONO', 'FECHA_ANULA_ABONO', 'Fecha Anulación');
	  $RUT = new header_rut('RUT', 'DO', 'Rut');
	  $NOMBRE = new header_text('NOMBRE', 'NOMBRE', 'Nombre');
	  $APELLIDO_PATERNO = new header_text('APELLIDO_PATERNO', 'APELLIDO_PATERNO', 'Apellido Paterno');
	  $APELLIDO_MATERNO = new header_text('APELLIDO_MATERNO', 'APELLIDO_MATERNO', 'Apellido Materno');
	  $COD_DEUDA = new header_num('COD_DEUDA', 'DA.COD_DEUDA', 'N° Deuda');
	  $ANO_COBRO = new header_num('ANO_COBRO', 'ANO_COBRO', 'Año Cuota');
	  $MONTO_PESOS = new header_num('MONTO_PESOS', 'MONTO_PESOS', 'Monto Abono');
	  
	  $this->add_header($COD_ANULA_ABONO);
	  $this->add_header($FECHA_ANULA_ABONO);
	  $this->add_header($RUT);
	  $this->add_header($NOMBRE);
	  $this->add_header($APELLIDO_PATERNO);
	  $this->add_header($APELLIDO_MATERNO);
	  $this->add_header($COD_DEUDA);
	  $this->add_header($ANO_COBRO);
	  $this->add_header($MONTO_PESOS);
   }
}
?>