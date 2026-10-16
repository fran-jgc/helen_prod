<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_devolucion_pagare extends w_output {
    function __construct() {

		$sql = "SELECT  DP.COD_DEVOLUCION_PAGARE 
						,DP.NRO_FOLIO
						,DP.COD_DEUDA 
						,D.COD_TIPO_DEUDA 
						,ED.COD_ESTADO_DEUDA
						,ED.NOM_ESTADO_DEUDA
						,DE.RUT 
						,DE.DIG_VERIF 
						,DE.NOMBRE ||' '|| DE.APELLIDO_PATERNO ||' '|| DE.APELLIDO_MATERNO NOMBRE
						,EDP.NOM_ESTADO_DEVOLUCION_PAGARE 
						,EDP.COD_ESTADO_DEVOLUCION_PAGARE
						,TO_CHAR(DP.FECHA_ESTADO_DEV_PAGARE ,'DD/MM/YYYY') FECHA_ESTADO_DEV_PAGARE 
						,(SELECT EP.NOM_ESTADO_PAGARE FROM PAGARE P , ESTADO_PAGARE EP 
							WHERE COD_DEUDA = DP.COD_DEUDA
							AND P.COD_ESTADO_PAGARE = EP.COD_ESTADO_PAGARE
							AND  ROWNUM = 1) NOM_ESTADO_PAGARE
				FROM DEVOLUCION_PAGARE DP LEFT OUTER JOIN ESTADO_DEVOLUCION_PAGARE EDP ON DP.COD_ESTADO_DEVOLUCION_PAGARE = EDP.COD_ESTADO_DEVOLUCION_PAGARE 
				, DEUDA D , DEUDOR DE ,ESTADO_DEUDA ED   
				WHERE  DP.COD_DEUDA= D.COD_DEUDA
				AND  D.COD_DEUDOR  =DE.COD_DEUDOR 
				AND  D.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA 
				ORDER BY DP.NRO_FOLIO DESC";

		parent::__construct('devolucion_pagare', $sql, $_REQUEST['cod_item_menu']);
		//formato numeros
        $rut = new static_num('RUT');
        $this->dw->add_control($rut);

		// headers
		$cod_devolucion_pagare	    = new header_num('COD_DEVOLUCION_PAGARE', 'COD_DEVOLUCION_PAGARE', 'COD_DEVOLUCION_PAGARE');
		$nro_folio			        = new header_num('NRO_FOLIO', 'NRO_FOLIO', 'Folio');
		$cod_deuda                  = new header_num('COD_DEUDA', 'DP.COD_DEUDA', 'Deuda');
		$sql_estado_deuda 			= "SELECT  COD_ESTADO_DEUDA , NOM_ESTADO_DEUDA  FROM ESTADO_DEUDA ed ORDER BY COD_ESTADO_DEUDA desc";
		$nom_estado_deuda           = new header_drop_down('NOM_ESTADO_DEUDA', 'ED.COD_ESTADO_DEUDA', 'Estado Deuda',$sql_estado_deuda );
		$rut                        = new header_rut('RUT', 'DE ', 'Rut');
		$nom_deudor	    	        = new header_text('NOMBRE', 'NOMBRE', 'Nombre');
		// $sql_estado_dev_pagare 		= "SELECT COD_ESTADO_DEVOLUCION_PAGARE  
		// 									, NOM_ESTADO_DEVOLUCION_PAGARE  
		// 								FROM ESTADO_DEVOLUCION_PAGARE edp 
		// 								ORDER BY COD_ESTADO_DEVOLUCION_PAGARE DESC";
		// $nom_estado_dev_pagare      = new header_drop_down( 'NOM_ESTADO_DEVOLUCION_PAGARE', 'edp.COD_ESTADO_DEVOLUCION_PAGARE', 'Estado Pagarés',$sql_estado_dev_pagare);
		$fecha_estado_pagare        = new header_date('FECHA_ESTADO_DEV_PAGARE', 'FECHA_ESTADO_DEV_PAGARE', 'Fecha Estado Pagaré');
	/*	$sql_estado_pagare 		= "SELECT COD_ESTADO_PAGARE  
											, NOM_ESTADO_PAGARE 
										FROM ESTADO_PAGARE  
										ORDER BY COD_ESTADO_PAGARE DESC";
		$nom_estado_pagare      = new header_drop_down( 'NOM_ESTADO_PAGARE', 'COD_ESTADO_PAGARE', 'Estado Pagares',$sql_estado_pagare);
*/
		$nom_estado_pagare	    	  = new header_text('NOM_ESTADO_PAGARE', '(SELECT EP.NOM_ESTADO_PAGARE FROM PAGARE P , ESTADO_PAGARE EP 
																				WHERE COD_DEUDA = DP.COD_DEUDA
																				AND P.COD_ESTADO_PAGARE = EP.COD_ESTADO_PAGARE
																				AND  ROWNUM = 1)', 'Estado Pagarés');
		
		$this->add_header($cod_devolucion_pagare);
		$this->add_header($nro_folio);
		$this->add_header($cod_deuda);
		$this->add_header($nom_estado_deuda);
		$this->add_header($rut);
		$this->add_header($nom_deudor);
		$this->add_header($nom_estado_pagare);
		$this->add_header($fecha_estado_pagare);
		
	}
	function habilita_boton(&$temp, $boton, $habilita) {
        if ($boton=='add' && $habilita)
            $temp->setVar("WO_ADD_CREAR", '<input name="b_'.$boton.'" id="b_'.$boton.'" src="../../../../commonlib_seven/trunk/images/b_'.$boton.'.jpg" type="image" '.
                'onMouseDown="MM_swapImage(\'b_'.$boton.'\',\'\',\'../../../../commonlib_seven/trunk/images/b_'.$boton.'_click.jpg\',1)" '.
                'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
                'onMouseOver="MM_swapImage(\'b_'.$boton.'\',\'\',\'../../../../commonlib_seven/trunk/images/b_'.$boton.'_over.jpg\',1)" '.
                'onClick="request_crear_desde();  return false;" />');
            else
                parent::habilita_boton($temp, $boton, $habilita);
    }
    
    function crear_desde($request) {
		$request = explode("|", $request);
		$rut = $request[0];
		$deuda = $request[1]; 		
		session::set('CREA_DESDE_RUT', $rut);
		session::set('CREA_DESDE_COD_DEUDA', $deuda);
		$this->add();
	}
 	
    
    function procesa_event() {
		// print_r($_POST);
        if(strlen($_POST['res_hidden'])>0){
            $this->crear_desde($_POST['res_hidden']);
		}else
             parent::procesa_event();
    }
}
?>