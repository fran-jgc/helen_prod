<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");
require_once("class_print_devolucion_pagare_dos.php");
ini_set('memory_limit', '250M');

class dw_pagare extends datawindow{
	
	function __construct() {

		$sql = "SELECT  p.COD_PAGARE 
						,p.FOLIO_PAGARE 
						,TO_CHAR(p.FECHA_PAGARE ,'dd/mm/yyyy') FECHA_PAGARE 
						,p.COD_ESTADO_PAGARE 
						,p.MONTO_MONEDA MONTO
						,ep.NOM_ESTADO_PAGARE 
				FROM PAGARE p 
					, ESTADO_PAGARE ep 
				WHERE p.COD_ESTADO_PAGARE = ep.COD_ESTADO_PAGARE 
				AND p.COD_DEUDA = {KEY1}
				and p.COD_UNIVERSIDAD=6
				order by COD_PAGARE";

		parent::__construct($sql, 'PAGARE', true, false);

		$this->add_control(new edit_text('COD_PAGARE_H', 10, 10, 'hidden'));

	}
	
}
class dw_detalle_deuda extends datawindow{
	
	function __construct() {

		$sql = "SELECT  d.COD_DEUDA
						,d.COD_TIPO_DEUDA 
						,ed.NOM_ESTADO_DEUDA
						,de.RUT 
						,de.DIG_VERIF 
						,de.NOMBRE ||' '|| de.APELLIDO_PATERNO ||' '|| de.APELLIDO_MATERNO NOMBRE
						,d.COD_DEUDOR COD_DEUDOR_H
				FROM deudor de 
					, deuda d
					, estado_deuda ed 
				WHERE de.cod_deudor = d.cod_deudor
				and   d.cod_estado_deuda = ed.cod_estado_deuda
				and   d.cod_deuda = {KEY1}
				order by d.COD_DEUDA";

		parent::__construct($sql);

		$this->add_control(new edit_text('COD_DEUDOR_H',10,10, 'hidden'));
		$this->add_control(new static_num('RUT')); 
		$this->add_control(new static_text('NOMBRE'));
		$this->add_control(new static_text('COD_TIPO_DEUDA'));
		$this->add_control(new static_text('NOM_ESTADO_DEUDA'));
		$this->add_control(new static_text('COD_DEUDA'));
	}
	
}
class wi_devolucion_pagare extends w_input{
	function __construct($cod_item_menu){
		parent::__construct('devolucion_pagare', $cod_item_menu);

		$sql = "SELECT  dp.COD_DEVOLUCION_PAGARE 
						,dp.COD_DEVOLUCION_PAGARE COD_DEVOLUCION_PAGARE_H
						,NRO_FOLIO
						,dp.COD_DEUDA COD_DEUDA_H
						,dp.ORIGEN
						,dp.RETIRADO_POR RETIRADO_POR_D
						,dp.RETIRADO_POR RETIRADO_POR_PS 
						,dp.RUT_RETIRA 
						,dp.NOM_RETIRA 
						,dp.COD_ESTADO_ENVIO_DOMICILIO 
						,dp.DIRECCION_ENVIO_DOMICILIO 
						,TO_CHAR(dp.FECHA_EMISION_ORDINARIO ,'dd/mm/yyyy') FECHA_EMISION_ORDINARIO 
						,dp.NRO_ORDEN_TRABAJO 
						,TO_CHAR(dp.FECHA_RECEPCION ,'dd/mm/yyyy') FECHA_RECEPCION 
						,TO_CHAR(dp.FECHA_ENVIO_MAIL ,'dd/mm/yyyy') FECHA_ENVIO_MAIL 
						,edp.NOM_ESTADO_DEVOLUCION_PAGARE 
						,edp.COD_ESTADO_DEVOLUCION_PAGARE
						,edp.COD_ESTADO_DEVOLUCION_PAGARE COD_ESTADO_DEV_PAGARE_H
						,TO_CHAR(dp.FECHA_ESTADO_DEV_PAGARE ,'dd/mm/yyyy') FECHA_ESTADO_DEV_PAGARE 
						,'none' DISPLAY_RETIRO
						,'none' DISPLAY_ENVIO_DOMICILIO
						,'none' DISPLAY_ENVIO_EMAIL
						,'none' DISPLAY_ENVIADO_EXITOSO
						,'none' DISPLAY_ENVIADO_FALLIDO
						,dp.EMAIL
						,dp.COD_USUARIO
						,u.NOM_USUARIO 
						,dp.MOTIVO
						,dp.COD_ESTADO_RETIRO_PRESENCIAL
				FROM DEVOLUCION_PAGARE dp left outer join ESTADO_DEVOLUCION_PAGARE edp on dp.COD_ESTADO_DEVOLUCION_PAGARE = edp.COD_ESTADO_DEVOLUCION_PAGARE 
										left outer join USUARIO u on  dp.COD_USUARIO =u.COD_USUARIO 
				WHERE  dp.COD_DEVOLUCION_PAGARE={KEY1}";

		$this->dws['dw_devolucion_pagare'] = new datawindow($sql);
		$this->dws['dw_devolucion_pagare']->add_control(new edit_text('COD_DEVOLUCION_PAGARE', 10,10, 'hidden'));
		$this->dws['dw_devolucion_pagare']->add_control(new static_text('COD_DEVOLUCION_PAGARE_H'));
		$this->dws['dw_devolucion_pagare']->add_control(new static_text('COD_ESTADO_DEV_PAGARE_H'));
		
		$this->dws['dw_devolucion_pagare']->add_control(new static_text('ORIGEN'));
		$this->dws['dw_devolucion_pagare']->add_control(new static_text('NRO_FOLIO'));
		$sql_estado = "SELECT COD_ESTADO_DEVOLUCION_PAGARE  
							, NOM_ESTADO_DEVOLUCION_PAGARE  
						FROM ESTADO_DEVOLUCION_PAGARE edp 
						ORDER BY COD_ESTADO_DEVOLUCION_PAGARE DESC";
		$this->dws['dw_devolucion_pagare']->add_control($control =new drop_down_dw('COD_ESTADO_DEVOLUCION_PAGARE', $sql_estado, 160, '', false));		
		$control->set_onChange("cambio_estado();");
		$this->dws['dw_devolucion_pagare']->add_control(new static_text('FECHA_ESTADO_DEV_PAGARE'));

		$this->dws['dw_devolucion_pagare']->add_control($control_d = new edit_radio_button('RETIRADO_POR_D', 'deudor', '', 'Deudor','RETIRADO_POR'));
		$control_d->set_onChange("habilita_input_presencial();");
		$this->dws['dw_devolucion_pagare']->add_control($control_ps = new edit_radio_button('RETIRADO_POR_PS', 'poder_simple', '', 'Poder Simple','RETIRADO_POR'));
		$control_ps->set_onChange("habilita_input_presencial();");
		$this->dws['dw_devolucion_pagare']->add_control(new edit_text('RUT_RETIRA',15,15));
		$this->dws['dw_devolucion_pagare']->add_control(new edit_text('NOM_RETIRA',80,100));

		// FIX: se cambia de combo visible a campo oculto. La pantalla del
		// usuario no tiene ni debe tener este combo; lo único que se
		// necesita es que el valor (1=en proceso, 2=devuelto) viaje al
		// guardar. El JS (devolucion_pagare.js) lo fija automáticamente
		// al elegir "Deudor"/"Poder Simple".
		$this->dws['dw_devolucion_pagare']->add_control(new edit_text('COD_ESTADO_RETIRO_PRESENCIAL', 10, 10, 'hidden'));

		
		$this->dws['dw_devolucion_pagare']->add_control(new edit_text('DIRECCION_ENVIO_DOMICILIO',80,100));
		$this->dws['dw_devolucion_pagare']->add_control(new edit_text('EMAIL',30,100));
		$sql_estado_envio_domicilio = "SELECT COD_ESTADO_ENVIO_DOMICILIO  
										, NOM_ESTADO_ENVIO_DOMICILIO  
									FROM ESTADO_ENVIO_DOMICILIO edp 
									ORDER BY COD_ESTADO_ENVIO_DOMICILIO DESC";
		$this->dws['dw_devolucion_pagare']->add_control($control =new drop_down_dw('COD_ESTADO_ENVIO_DOMICILIO', $sql_estado_envio_domicilio, 160, '', false));		
		$control->set_onChange("cambio_estado_envio_domicilio();");
		$this->dws['dw_devolucion_pagare']->add_control(new static_text('FECHA_EMISION_ORDINARIO'));

		$this->dws['dw_devolucion_pagare']->add_control(new edit_text('NRO_ORDEN_TRABAJO',30,100));
		$this->dws['dw_devolucion_pagare']->add_control(new	edit_date('FECHA_RECEPCION'));
		$this->dws['dw_devolucion_pagare']->add_control(new edit_text_multiline('MOTIVO',54,5));

		$this->dws['dw_devolucion_pagare']->add_control(new static_text('FECHA_ENVIO_MAIL'));
		$this->dws['dw_devolucion_pagare']->add_control(new static_text('NOM_USUARIO'));
		$this->dws['dw_devolucion_pagare']->add_control(new edit_text('COD_DEUDA_H', 10,10, 'hidden'));

		$this->dws['dw_pagare'] = new dw_pagare();
		$this->dws['dw_detalle_deuda'] = new dw_detalle_deuda();
		
	}
	function new_record(){
		$this->dws['dw_devolucion_pagare']->insert_row();
		$this->dws['dw_devolucion_pagare']->set_item(0, 'ORIGEN', 'Manual');
		$cod_deuda =  session::get("CREA_DESDE_COD_DEUDA");
		$this->dws['dw_detalle_deuda']->retrieve($cod_deuda);
		$this->dws['dw_pagare']->retrieve($cod_deuda);

		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

		$sql = "SELECT  NOM_USUARIO  FROM USUARIO u  WHERE COD_USUARIO =$this->cod_usuario";
		$result = $db->build_results($sql);
		$this->dws['dw_devolucion_pagare']->set_item(0, 'NOM_USUARIO', $result[0]['NOM_USUARIO']);
	}
	function load_record(){
		$ESTADO_RETIRO_PRESENCIAL =2; //DEVUELTO
		$ENVIADO_EXITOSO= 2;

		$COD_DEVOLUCION_PAGARE = $this->get_item_wo($this->current_record, 'COD_DEVOLUCION_PAGARE');
		$this->dws['dw_devolucion_pagare']->retrieve($COD_DEVOLUCION_PAGARE);
		$cod_deuda = $this->dws['dw_devolucion_pagare']->get_item(0, 'COD_DEUDA_H');
		$this->dws['dw_detalle_deuda']->retrieve($cod_deuda);
		$this->dws['dw_pagare']->retrieve($cod_deuda);
		
		$this->b_no_save_visible	= true;
		$this->b_save_visible 	 	= true;
		$this->b_modify_visible	 	= true;
		$this->b_print_visible 		= true;
		

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

		$sql = "SELECT  NOM_USUARIO  FROM USUARIO u  WHERE COD_USUARIO =$this->cod_usuario";
		$result = $db->build_results($sql);
		$this->dws['dw_devolucion_pagare']->set_item(0, 'NOM_USUARIO', $result[0]['NOM_USUARIO']);

		$sql = "SELECT  COD_ESTADO_DEVOLUCION_PAGARE , 
						COD_ESTADO_ENVIO_DOMICILIO  ,
						COD_ESTADO_RETIRO_PRESENCIAL
				FROM DEVOLUCION_PAGARE
				WHERE COD_DEVOLUCION_PAGARE =$COD_DEVOLUCION_PAGARE";
		$result = $db->build_results($sql);

		$COD_EST_DEV_PAGARE=$result[0]['COD_ESTADO_DEVOLUCION_PAGARE'];
		$COD_EST_ENVIO_DOM=$result[0]['COD_ESTADO_ENVIO_DOMICILIO'];
		$COD_ESTADO_RETIRO_PRESENCIAL=$result[0]['COD_ESTADO_RETIRO_PRESENCIAL'];

		if ($COD_ESTADO_RETIRO_PRESENCIAL==$ESTADO_RETIRO_PRESENCIAL || $COD_EST_ENVIO_DOM==$ENVIADO_EXITOSO ) {
			$this->b_modify_visible	 	= false;
		}

	}
	function get_key(){
		return $this->dws['dw_devolucion_pagare']->get_item(0, 'COD_DEVOLUCION_PAGARE');
	}

	function save_record($db){
		$COD_DEVOLUCION_PAGARE			= $this->dws['dw_devolucion_pagare']->get_item(0, 'COD_DEVOLUCION_PAGARE_H');
		$COD_DEUDA						= $this->dws['dw_detalle_deuda']->get_item(0, 'COD_DEUDA');
		$COD_DEUDOR						= $this->dws['dw_detalle_deuda']->get_item(0, 'COD_DEUDOR_H');	
		$ORIGEN							= $this->dws['dw_devolucion_pagare']->get_item(0, 'ORIGEN');
		$COD_ESTADO_DEVOLUCION_PAGARE	= $this->dws['dw_devolucion_pagare']->get_item(0, 'COD_ESTADO_DEVOLUCION_PAGARE');
		$RETIRADO_POR					= $this->dws['dw_devolucion_pagare']->get_item(0, 'RETIRADO_POR_D');
		$RUT_RETIRA						= $this->dws['dw_devolucion_pagare']->get_item(0, 'RUT_RETIRA');
		$NOM_RETIRA						= $this->dws['dw_devolucion_pagare']->get_item(0, 'NOM_RETIRA');
		$COD_ESTADO_ENVIO_DOMICILIO		= $this->dws['dw_devolucion_pagare']->get_item(0, 'COD_ESTADO_ENVIO_DOMICILIO');
		$COD_ESTADO_RETIRO_PRESENCIAL	= $this->dws['dw_devolucion_pagare']->get_item(0, 'COD_ESTADO_RETIRO_PRESENCIAL');
		$DIRECCION_ENVIO_DOMICILIO		= $this->dws['dw_devolucion_pagare']->get_item(0, 'DIRECCION_ENVIO_DOMICILIO');
		$NRO_ORDEN_TRABAJO				= $this->dws['dw_devolucion_pagare']->get_item(0, 'NRO_ORDEN_TRABAJO');
		$FECHA_RECEPCION				= $this->dws['dw_devolucion_pagare']->get_item(0, 'FECHA_RECEPCION');
		$MOTIVO							= $this->dws['dw_devolucion_pagare']->get_item(0, 'MOTIVO');	
		$COD_USUARIO					= $this->cod_usuario;
		$EMAIL							= $this->dws['dw_devolucion_pagare']->get_item(0, 'EMAIL');
		
		$FECHA_RECEPCION = $this->str2date($FECHA_RECEPCION);

		$COD_DEVOLUCION_PAGARE 			= ($COD_DEVOLUCION_PAGARE == '') ? "null" : $COD_DEVOLUCION_PAGARE;
		$COD_DEUDA 						= ($COD_DEUDA == '') ? "null" : $COD_DEUDA;
		$ORIGEN 						= ($ORIGEN == '') ? "null" : "'$ORIGEN'";
		$COD_ESTADO_DEVOLUCION_PAGARE 	= ($COD_ESTADO_DEVOLUCION_PAGARE == '') ? "null" : $COD_ESTADO_DEVOLUCION_PAGARE;
		$RETIRADO_POR 					= ($RETIRADO_POR == '') ? "null" :"'$RETIRADO_POR'";
		$RUT_RETIRA		 				= ($RUT_RETIRA == '') ? "null" : "'$RUT_RETIRA'";
		$NOM_RETIRA 					= ($NOM_RETIRA == '') ? "null" : "'$NOM_RETIRA'";
		$COD_ESTADO_ENVIO_DOMICILIO 	= ($COD_ESTADO_ENVIO_DOMICILIO == '') ? "null" : $COD_ESTADO_ENVIO_DOMICILIO;
		$COD_ESTADO_RETIRO_PRESENCIAL 	= ($COD_ESTADO_RETIRO_PRESENCIAL == '') ? "null" : $COD_ESTADO_RETIRO_PRESENCIAL;
		$DIRECCION_ENVIO_DOMICILIO		= ($DIRECCION_ENVIO_DOMICILIO == '') ? "null" : "'$DIRECCION_ENVIO_DOMICILIO'";
		$NRO_ORDEN_TRABAJO 				= ($NRO_ORDEN_TRABAJO == '') ? "null" : "'$NRO_ORDEN_TRABAJO'";
		$FECHA_RECEPCION 				= ($FECHA_RECEPCION == '') ? "null" : $FECHA_RECEPCION;
		$MOTIVO 						= ($MOTIVO == '') ? "null" : "'$MOTIVO'";
		$COD_USUARIO 					= ($COD_USUARIO == '') ? "null" : $COD_USUARIO;
		$EMAIL			 				= ($EMAIL == '') ? "null" : "'$EMAIL'";

		
		$sp = 'spu_devolucion_pagare';
		if ($this->is_new_record()){
			$operacion = 'INSERT';
			// El alta de retiro presencial comienza en estado "en proceso".
			if ($COD_ESTADO_DEVOLUCION_PAGARE == 1) {
				$COD_ESTADO_RETIRO_PRESENCIAL = 1;
			}
		}
		else{
			$operacion = 'UPDATE';
		}
		$param	= "'$operacion'
					,$COD_DEVOLUCION_PAGARE 			
					,$COD_DEUDA 						
					,$ORIGEN 							
					,$COD_ESTADO_DEVOLUCION_PAGARE 	
					,$RETIRADO_POR 					
					,$RUT_RETIRA 						
					,$NOM_RETIRA 						
					,$COD_ESTADO_ENVIO_DOMICILIO 		
					,$DIRECCION_ENVIO_DOMICILIO 		
					,$NRO_ORDEN_TRABAJO 				
					,$FECHA_RECEPCION 				
					,$MOTIVO 							
					,$COD_USUARIO 					
					,$EMAIL 
					,$COD_DEUDOR
					,$COD_ESTADO_RETIRO_PRESENCIAL";
			// echo	$sp.' '.$param;
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$cod_devolucion_pagare = $db->GET_IDENTITY('devolucion_pagare');
				$this->dws['dw_devolucion_pagare']->set_item(0, 'COD_DEVOLUCION_PAGARE', $cod_devolucion_pagare);
			}
			return true;
		}
		return false;
	}
	function print_record() {
		$cod_deuda						= $this->dws['dw_detalle_deuda']->get_item(0, 'COD_DEUDA');
		$COD_DEVOLUCION_PAGARE			= $this->dws['dw_devolucion_pagare']->get_item(0, 'COD_DEVOLUCION_PAGARE_H');
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql_val = "SELECT NRO_FOLIO FROM DEVOLUCION_PAGARE WHERE COD_DEUDA=$cod_deuda";
		$result = $db->build_results($sql_val);
		if($result[0]['NRO_FOLIO']!=''){
			$sql = "SELECT  DEUDA.COD_DEUDA
							, DEUDOR.RUT
							, DEUDOR.DIG_VERIF
							, DEUDOR.NOMBRE||' '||DEUDOR.APELLIDO_PATERNO||' '||DEUDOR.APELLIDO_MATERNO NOMBRE
							, TD.COD_TIPO_DEUDA
							, ED.NOM_ESTADO_DEUDA
							, P.FOLIO_PAGARE
							, P.MONTO_PESOS
							, P.MONTO_MONEDA MONTO_MONEDA
							, TO_CHAR(P.FECHA_PAGARE, 'dd/mm/yyyy') FECHA_PAGARE
							, EP.NOM_ESTADO_PAGARE
							, M.SIMBOLO
							,to_char (SYSDATE, 'DD/MM/YYYY') FECHA_IMPRESO
							,to_char (SYSDATE, 'HH24:MI:SS') HORA_IMPRESO 
							, f_get_parametro(5) NOM_UNIVERSIDAD -- NOMBRE_UNIVERSIDAD	
							, P.DEVOLUCION_A
							, P.MOTIVO_DEVOLUCION
							, P.RUT_DEVOLUCION
					FROM    DEUDA DEUDA, DEUDOR DEUDOR, ESTADO_DEUDA ED, TIPO_DEUDA TD, PAGARE P,
							ESTADO_PAGARE EP, MONEDA M
					WHERE   DEUDA.COD_DEUDA = $cod_deuda
					AND
							DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR AND
								DEUDA.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA AND
								TD.COD_TIPO_DEUDA = DEUDA.COD_TIPO_DEUDA AND
							DEUDA.COD_DEUDA = P.COD_DEUDA (+) AND
							P.COD_ESTADO_PAGARE = EP.COD_ESTADO_PAGARE (+) AND
								TD.COD_MONEDA = M.COD_MONEDA";
			$labels = array();
			$labels['strCOD_DEUDA'] = $cod_deuda;
			$labels['strUSUARIO'] = $this->nom_usuario;
			$rpt = new reporte_helen($sql, $this->root_dir.'appl/devolucion_pagare/devolucion_pagare.xml', $labels, utf8_decode("Devolución de pagarés").$cod_deuda.'.pdf',1);

			$K_NOM_ADMIN=19;
			$sql_2 = "SELECT DISTINCT To_Char(SYSDATE, 'dd')  DIA
									, f_get_nom_mes(to_number(To_Char(SYSDATE, 'mm'))) MES
									, To_Char(SYSDATE, 'yyyy') ANO_SYS
							, P.COD_DEUDA
							, DEUDOR.RUT
							, DEUDOR.DIG_VERIF
							, DEUDOR.NOMBRE||' '||DEUDOR.APELLIDO_PATERNO||' '||DEUDOR.APELLIDO_MATERNO NOMBRE
							, DIR.DIRECCION||', '||CO.NOM_COMUNA    DIRECCION
							, DEUDOR.SEXO
							, TD.NOM_TIPO_DEUDA
							, f_get_parametro($K_NOM_ADMIN) NOM_ADMIN
							, (SELECT COUNT(PA.COD_DEUDA) FROM PAGARE PA WHERE PA.COD_DEUDA = DEUDA.COD_DEUDA GROUP BY PA.COD_DEUDA) CANT_PAGARES
							, DEV.NRO_FOLIO
					FROM    PAGARE P,DEUDA DEUDA, DEUDOR DEUDOR, TIPO_DEUDA TD,  DIRECCION DIR , COMUNA CO,DEVOLUCION_PAGARE DEV
									
					WHERE   DEUDA.COD_DEUDA = $cod_deuda
					AND  DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR 
					AND  TD.COD_TIPO_DEUDA = DEUDA.COD_TIPO_DEUDA 
					AND	DEUDA.COD_DEUDA = P.COD_DEUDA (+)
					AND	DIR.COD_COMUNA (+) = CO.COD_COMUNA
					AND	DIR.COD_DEUDOR  =  DEUDOR.COD_DEUDOR
					AND DEUDA.COD_DEUDA = DEV.COD_DEUDA 
					AND	DIR.ES_VALIDA   =   'S'
					AND COD_DEVOLUCION_PAGARE=$COD_DEVOLUCION_PAGARE" ;

			// reporte
			$labels = array();
			$file_name = $this->find_file('devolucion_pagare', 'devolucion_pagare_dos.xml');
			$rpt = new print_devolucion_pagare_dos($sql_2, $file_name, $labels, utf8_decode("Devolución de pagar 2 ").$cod_deuda.'.pdf', 1);
			
		}else{
			$this->alert('Se debe crear el N° de folio');
		}
		$this->redraw();
	}
}
?>