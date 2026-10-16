<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

ini_set('memory_limit', '480M');
ini_set('max_execution_time', 900); //900 seconds = 15 minutes

class wi_inf_dato_contacto extends w_param_informe_helen {
    function __construct($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_deudores_cobranza/inf_dato_contacto.xml';
		$sql_informe = "SELECT    RUT	
                                  ,DIG_VERIF					
                                  ,COD_DEUDA              
                                  ,NOMBRES				  
                                  ,PATERNO			      
                                  ,MATERNO			      
                                  ,TIPO_DEUDA		      
                                  ,CARTERA				  
                                  ,UNIVERSIDAD    		  
                                  ,ESTADO_DEUDA  
                                  ,MONTO_INICIO_COBRO  		  
                                  ,AIC 	            
                                  ,CANT_CUOTA       
                                  ,SALDO_DEUDA SALDO_DEUDA_ACTUALIZADA     
                                  ,TELEFONO         
                                  ,CELULAR	
                                  ,DIRECCION
                                  ,COMUNA 	
                                  ,CIUDAD	
                                  ,REGION	
                                  ,MAIL	
					    FROM 	  TEMP_DATOS_CONTACTO
					    ORDER BY  RUT";
		parent::__construct('inf_dato_contacto', $cod_item_menu, 'Dato Contacto.pdf', $xml, $sql_informe, 'spi_dato_contacto');
		
		$sql = "select  '' COD_TIPO_DEUDA
								,To_Char(SYSDATE,'yyyy') ANO_INICIAL
								,To_Char(SYSDATE,'yyyy') ANO_FINAL
						from 	DUAL";
		
		$this->dws['dw_param'] = new datawindow($sql);
		
		$sql = "select COD_TIPO_DEUDA
									, COD_TIPO_DEUDA COD_TIPO_DEUDA_COD
							from 	 TIPO_DEUDA
							order by COD_TIPO_DEUDA";
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_TIPO_DEUDA', $sql));
		$this->dws['dw_param']->add_control(new edit_num('ANO_INICIAL', 4, 4));
		$this->dws['dw_param']->add_control(new edit_num('ANO_FINAL', 4, 4));

		// mandatorys		
		$this->dws['dw_param']->set_mandatory('COD_TIPO_DEUDA', 'Tipo Deuda');	
		$this->dws['dw_param']->set_mandatory('ANO_INICIAL', 'año Inicial');
		$this->dws['dw_param']->set_mandatory('ANO_FINAL', 'año Final');
	}
	function make_filtro() {
	 
		$cod_tipo_deuda = $this->dws['dw_param']->get_item(0, 'COD_TIPO_DEUDA');
		$ano_inicial    = $this->dws['dw_param']->get_item(0, 'ANO_INICIAL');
		$ano_final      = $this->dws['dw_param']->get_item(0, 'ANO_FINAL');
		
		// Filtro
		$nom_tipo_deuda = $this->dws['dw_param']->controls['COD_TIPO_DEUDA']->get_label_from_value($cod_tipo_deuda);
		$this->filtro = "Tipo Deuda = $nom_tipo_deuda\n";
		$this->filtro .= "año Inicial = $ano_inicial; año Final = $ano_final\n";
		//$ano_inicial = $this->str2date($ano_inicial);	
		//$ano_final = $this->str2date($ano_final);
		// Arma los parametros para el SP
		$this->param = "'$cod_tipo_deuda',$ano_inicial,$ano_final";			
	}	
}
?>