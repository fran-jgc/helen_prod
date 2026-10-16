<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wi_dep_plazo_fijo extends w_input{
    
    const K_DEP_PLAZO_FIJO_EMITIDA			= 1;
    const K_DEP_PLAZO_FIJO_CONFIRMADA		= 2;
    const K_DEP_PLAZO_FIJO_ANULADA			= 3;
    
    function __construct($cod_item_menu){
	    
	    parent::__construct('dep_plazo_fijo', $cod_item_menu);
		
	    $this->b_print_visible = FALSE;
	    
	    $sql = "SELECT COD_DEP_PLAZO_FIJO
                  ,U.NOM_USUARIO
                  ,U.COD_USUARIO COD_USUARIO_H
                  ,EPF.COD_ESTADO_DEP_PLAZO_FIJO  
                  ,M.COD_MONEDA                 
                  ,TO_CHAR(FECHA_DEP_PLAZO_FIJO, 'DD/MM/YYYY') FECHA_DEP_PLAZO_FIJO       
                  ,TO_CHAR(FECHA_EMISION, 'DD/MM/YYYY')    FECHA_EMISION          
                  ,DIAS
                  ,DIAS DIAS_H                       
                  ,TO_CHAR(FECHA_VENCTO, 'DD/MM/YYYY')  FECHA_VENCTO             
                  ,TASA_INTERES_BASE          
                  ,TASA_INTERES_PERIODO
                  ,TASA_INTERES_PERIODO TASA_INTERES_PERIODO_H      
                  ,VALOR_INICIAL              
                  ,INTERESES        
                  ,INTERESES INTERESES_H          
                  ,VALOR_FINAL    
                  ,VALOR_FINAL VALOR_FINAL_H            
                  ,DESCRIPCION
                  ,COD_CUENTA_CORRIENTE
                FROM DEP_PLAZO_FIJO PF, USUARIO U,ESTADO_DEP_PLAZO_FIJO EPF, MONEDA M
                WHERE PF.COD_DEP_PLAZO_FIJO = {KEY1}
                AND PF.COD_USUARIO = U.COD_USUARIO
                AND PF.COD_ESTADO_DEP_PLAZO_FIJO = EPF.COD_ESTADO_DEP_PLAZO_FIJO
                AND PF.COD_MONEDA = M.COD_MONEDA";

		$this->dws['dw_dep_plazo_fijo'] = new datawindow($sql);
		//CONTROLES
		$this->dws['dw_dep_plazo_fijo']->add_control(new static_text('NOM_USUARIO'));
		$this->dws['dw_dep_plazo_fijo']->add_control(new edit_text('COD_USUARIO_H',10,10,'hidden'));
		$this->dws['dw_dep_plazo_fijo']->add_control(new static_text('FECHA_DEP_PLAZO_FIJO'));
		
		$sql_est = "SELECT COD_MONEDA, NOM_MONEDA FROM MONEDA";
		$this->dws['dw_dep_plazo_fijo']->add_control(new drop_down_dw('COD_MONEDA', $sql_est, 100, '', false));
		
		$sql_cc = " SELECT COD_CUENTA_CORRIENTE
                          , NOM_CUENTA_CORRIENTE   
                     FROM CUENTA_CORRIENTE";
		$this->dws['dw_dep_plazo_fijo']->add_control(new drop_down_dw('COD_CUENTA_CORRIENTE', $sql_cc, 200, '', false));
		
		$this->dws['dw_dep_plazo_fijo']->add_control(new static_text('FECHA_DEP_PLAZO_FIJO'));
		$this->dws['dw_dep_plazo_fijo']->add_control($control = new edit_date('FECHA_EMISION'));
		$control->set_onChange("calcula_dia();");
		
		$this->dws['dw_dep_plazo_fijo']->add_control(new static_text('DIAS'));
		$this->dws['dw_dep_plazo_fijo']->add_control(new edit_text('DIAS_H',10,10,'hidden'));
		$this->dws['dw_dep_plazo_fijo']->add_control($control = new edit_date('FECHA_VENCTO'));
		$control->set_onChange("calcula_dia();");
		$this->dws['dw_dep_plazo_fijo']->add_control($control = new edit_num('TASA_INTERES_BASE',10, 10, 5));
		$control->set_onChange("calcula_interes_periodo();");
		$this->dws['dw_dep_plazo_fijo']->add_control(new static_num('TASA_INTERES_PERIODO', 5));
		$this->dws['dw_dep_plazo_fijo']->add_control(new edit_text('TASA_INTERES_PERIODO_H',10,10,'hidden'));
		$this->dws['dw_dep_plazo_fijo']->add_control($control = new edit_num('VALOR_INICIAL',18, 18,0));
		$control->set_onChange("calcula_intereses();");
		$this->dws['dw_dep_plazo_fijo']->add_control(new static_num('INTERESES', 0));
		$this->dws['dw_dep_plazo_fijo']->add_control(new edit_text('INTERESES_H',10,10,'hidden'));
		$this->dws['dw_dep_plazo_fijo']->add_control(new static_num('VALOR_FINAL', 0));
		$this->dws['dw_dep_plazo_fijo']->add_control(new edit_text('VALOR_FINAL_H',10,10,'hidden'));
		$this->dws['dw_dep_plazo_fijo']->add_control(new edit_text_multiline('DESCRIPCION',100, 5));
		
		$sql_est = "SELECT COD_ESTADO_DEP_PLAZO_FIJO, NOM_ESTADO_DEP_PLAZO_FIJO FROM ESTADO_DEP_PLAZO_FIJO WHERE COD_ESTADO_DEP_PLAZO_FIJO = 1";
		$this->dws['dw_dep_plazo_fijo']->add_control(new drop_down_dw('COD_ESTADO_DEP_PLAZO_FIJO', $sql_est, 150, '', false));

		$this->dws['dw_dep_plazo_fijo']->set_mandatory('COD_ESTADO_DEP_PLAZO_FIJO', 'Estado Deposito');
		$this->dws['dw_dep_plazo_fijo']->set_mandatory('COD_CUENTA_CORRIENTE', 'Cuenta Corriente');
		$this->dws['dw_dep_plazo_fijo']->set_mandatory('COD_MONEDA', 'Tipo Moneda');
		$this->dws['dw_dep_plazo_fijo']->set_mandatory('TASA_INTERES_BASE', 'Tasa Interes');
		$this->dws['dw_dep_plazo_fijo']->set_mandatory('FECHA_VENCTO', 'Fecha Vencto');
		$this->dws['dw_dep_plazo_fijo']->set_mandatory('FECHA_EMISION', 'Fecha Emision');
		$this->dws['dw_dep_plazo_fijo']->set_mandatory('VALOR_INICIAL', 'Valor Inicial');

	}
	function load_record(){
		$cod_dep_plazo_fijo	= $this->get_item_wo($this->current_record, 'COD_DEP_PLAZO_FIJO');
		$this->dws['dw_dep_plazo_fijo']->retrieve($cod_dep_plazo_fijo);
		
		$cod_estado_dep_plazo_fijo = $this->dws['dw_dep_plazo_fijo']->get_item(0, 'COD_ESTADO_DEP_PLAZO_FIJO');
		
		if ($cod_estado_dep_plazo_fijo==self::K_DEP_PLAZO_FIJO_CONFIRMADA){
		 
		    $this->dws['dw_dep_plazo_fijo']->set_entrable('FECHA_EMISION', false);
		    $this->dws['dw_dep_plazo_fijo']->set_entrable('FECHA_VENCTO', false);
		    $this->dws['dw_dep_plazo_fijo']->set_entrable('TASA_INTERES_BASE', false);
		    $this->dws['dw_dep_plazo_fijo']->set_entrable('COD_MONEDA', false);
		    $this->dws['dw_dep_plazo_fijo']->set_entrable('VALOR_INICIAL', false);
		    $this->dws['dw_dep_plazo_fijo']->set_entrable('COD_CUENTA_CORRIENTE', false);
		    
		    $sql_est = "SELECT COD_ESTADO_DEP_PLAZO_FIJO, NOM_ESTADO_DEP_PLAZO_FIJO 
                        FROM ESTADO_DEP_PLAZO_FIJO
                        WHERE COD_ESTADO_DEP_PLAZO_FIJO in (2,3)";
		    $this->dws['dw_dep_plazo_fijo']->add_control(new drop_down_dw('COD_ESTADO_DEP_PLAZO_FIJO', $sql_est, 150, '', false));
		
		}else if ($cod_estado_dep_plazo_fijo==self::K_DEP_PLAZO_FIJO_ANULADA){
		    
		    $this->b_modify_visible	 	= false;
		    $this->b_save_visible	 	= false;
		    $this->b_no_save_visible	= false;
		    
		}
		else if ($cod_estado_dep_plazo_fijo==1){
			$sql_est = "SELECT COD_ESTADO_DEP_PLAZO_FIJO, NOM_ESTADO_DEP_PLAZO_FIJO 
                        FROM ESTADO_DEP_PLAZO_FIJO
                        WHERE COD_ESTADO_DEP_PLAZO_FIJO in (1,2,3)";
		    $this->dws['dw_dep_plazo_fijo']->add_control(new drop_down_dw('COD_ESTADO_DEP_PLAZO_FIJO', $sql_est, 150, '', false));
		}	
	}
	function get_key(){
		return $this->dws['dw_dep_plazo_fijo']->get_item(0, 'COD_DEP_PLAZO_FIJO');
	}
	
	function new_record() {
	    $this->dws['dw_dep_plazo_fijo']->insert_row();
	    
	    $sql = "select to_char(SYSDATE, 'DD/MM/YYYY') FECHA FROM DUAL";
	    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	    $result = $db->build_results($sql);
	    
	    $this->dws['dw_dep_plazo_fijo']->set_item(0, 'NOM_USUARIO', $this->nom_usuario);
	    $this->dws['dw_dep_plazo_fijo']->set_item(0, 'COD_USUARIO_H', $this->cod_usuario);
	    $this->dws['dw_dep_plazo_fijo']->set_item(0, 'FECHA_DEP_PLAZO_FIJO', $result[0]['FECHA']);
	    $this->dws['dw_dep_plazo_fijo']->set_item(0, 'COD_CUENTA_CORRIENTE', 4); // BANCO SANTANDER
	    
	}
	
	function save_record($db){
		$COD_DEP_PLAZO_FIJO = $this->dws['dw_dep_plazo_fijo']->get_item(0, 'COD_DEP_PLAZO_FIJO');
		$COD_USUARIO = $this->cod_usuario;
		$COD_ESTADO_DEP_PLAZO_FIJO = $this->dws['dw_dep_plazo_fijo']->get_item(0, 'COD_ESTADO_DEP_PLAZO_FIJO');
		$COD_MONEDA = $this->dws['dw_dep_plazo_fijo']->get_item(0, 'COD_MONEDA');
		$FECHA_EMISION = $this->dws['dw_dep_plazo_fijo']->get_item(0, 'FECHA_EMISION');
		$DIAS = $this->dws['dw_dep_plazo_fijo']->get_item(0, 'DIAS_H');
		$FECHA_VENCTO = $this->dws['dw_dep_plazo_fijo']->get_item(0, 'FECHA_VENCTO');
		$TASA_INTERES_BASE = $this->dws['dw_dep_plazo_fijo']->get_item(0, 'TASA_INTERES_BASE');
		$TASA_INTERES_PERIODO = $this->dws['dw_dep_plazo_fijo']->get_item(0, 'TASA_INTERES_PERIODO_H');
		$VALOR_INICIAL = $this->dws['dw_dep_plazo_fijo']->get_item(0, 'VALOR_INICIAL');
		$INTERESES = $this->dws['dw_dep_plazo_fijo']->get_item(0, 'INTERESES_H');
		$VALOR_FINAL = $this->dws['dw_dep_plazo_fijo']->get_item(0, 'VALOR_FINAL_H');
		$DESCRIPCION = $this->dws['dw_dep_plazo_fijo']->get_item(0, 'DESCRIPCION');
		$COD_CUENTA_CORRIENTE = $this->dws['dw_dep_plazo_fijo']->get_item(0, 'COD_CUENTA_CORRIENTE');
		

		$COD_DEP_PLAZO_FIJO = ($COD_DEP_PLAZO_FIJO =='') ? "null" : $COD_DEP_PLAZO_FIJO;
		$DESCRIPCION = ($DESCRIPCION =='') ? "null" : "'$DESCRIPCION'";
		$FECHA_EMISION = $this->str2date($FECHA_EMISION);
		$FECHA_VENCTO = $this->str2date($FECHA_VENCTO);
		
		$sp = 'spu_deposito_plazo_fijo';
		if ($this->is_new_record()){
		    $operacion = 'INSERT';
		}
		else{
		    $operacion = 'UPDATE';
		}
		
		$param	= "'$operacion'
					,$COD_DEP_PLAZO_FIJO
					,$COD_USUARIO
					,$COD_ESTADO_DEP_PLAZO_FIJO
					,$COD_MONEDA
					,$FECHA_EMISION
					,$DIAS
					,$FECHA_VENCTO
					,$TASA_INTERES_BASE
					,$TASA_INTERES_PERIODO
					,$VALOR_INICIAL
					,$INTERESES
					,$VALOR_FINAL
					,$DESCRIPCION
                    ,$COD_CUENTA_CORRIENTE";

		if ($db->EXECUTE_SP($sp, $param)){
		    if ($this->is_new_record()) {
		        $COD_DEP_PLAZO_FIJO = $db->GET_IDENTITY("dep_plazo_fijo");
		        $this->dws['dw_dep_plazo_fijo']->set_item(0, 'COD_DEP_PLAZO_FIJO', $COD_DEP_PLAZO_FIJO);
		        
		    }
		    
		    return true;
		}
		return false;
	}
}
?>