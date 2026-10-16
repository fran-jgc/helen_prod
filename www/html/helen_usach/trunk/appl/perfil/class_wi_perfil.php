<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/*
 Clase : DW_OPCIONES_ESPECIALES
 */
class dw_opciones_especiales extends datawindow {
    function __construct() {
        
        //////////////// CLASS_SQL = INPUT_DW_OPCION_ESPECIAL ///////////////
        $sql = sql_perfil::get_sql('INPUT_DW_OPCION_ESPECIAL');
        
        parent::__construct($sql, 'OPCIONES_ESPECIALES', true, true);
        
        $this->add_control(new edit_check_box('AUTORIZA','S', 'N'));
    }
    
    function fill_record(&$temp, $record) {
        parent::fill_record($temp, $record);
        $cod_item_menu = $this->get_item($record, 'COD_ITEM_MENU');
        $indent = strlen($cod_item_menu)- 4;
        $nom_item_menu = $this->get_item($record, 'NOM_ITEM_MENU');
        $nom_item_menu = str_repeat("&nbsp;", $indent).$nom_item_menu;
        $hijos = $this->get_item($record, 'HIJOS');
        if ($hijos != 0) {
            $nom_item_menu = '<strong>'.$nom_item_menu.'</strong>';
            $temp->setVar($this->label_record.'.DW_TR_CSS', 'titulo_detalle');
            $temp->setVar($this->label_record.'.AUTORIZA', '');
        }
        $temp->setVar($this->label_record.'.NOM_ITEM_MENU', $nom_item_menu);
    }
    function new_record() {
        $sql = $this->sql;
        $sql = str_replace("{KEY1}", '2', $sql);
        $this->retrieve_bd($sql);
    }
    function validate() {
        
    }
    function update($db) {
        for ($i=0; $i < $this->row_count(); $i++) {
            $cod_perfil = $this->get_item($i, 'COD_PERFIL');
            $cod_item_menu = $this->get_item($i, 'COD_ITEM_MENU');
            $autoriza = $this->get_item($i, 'AUTORIZA');
            $sp = 'spu_opciones_especiales';
            $param = "$cod_perfil,'$cod_item_menu','$autoriza'";
            if (!$db->EXECUTE_SP($sp,$param))
                return false;
        }
        return true;
    }
}


/*
 Clase : DW_AUTORIZA_MENU
 */
class dw_autoriza_menu extends datawindow {
    function __construct() {
        
        //////////////// CLASS_SQL = INPUT_DW_AUTORIZA_MENU ///////////////
        $sql = sql_perfil::get_sql('INPUT_DW_AUTORIZA_MENU');
        
        parent::__construct($sql, 'AUTORIZA_MENU', true, true);
        
        $this->add_control($control = new edit_check_box('LECTURA','S', 'N'));
        $this->add_control($control = new edit_check_box('ESCRITURA','S', 'N'));
        $this->add_control(new edit_check_box('IMPRESION','S', 'N'));
        
    }
    function fill_record(&$temp, $record) {
        $cod_item_menu = $this->get_item($record, 'COD_ITEM_MENU');
        
        // La opcion parametros define el acceso a las tablas parametricas
        $control = $this->controls['LECTURA'];
        if ($cod_item_menu=='0515')
            $control->set_onClick("valida_checkbox_lectura();");
            else
                $control->set_onClick("");
                $control = $this->controls['ESCRITURA'];
                if ($cod_item_menu=='0515')
                    $control->set_onClick("valida_checkbox_escritura();");
                    else
                        $control->set_onClick("");
                        
                        parent::fill_record($temp, $record);
                        $indent = strlen($cod_item_menu) - 2;
                        $nom_item_menu = $this->get_item($record, 'NOM_ITEM_MENU');
                        $nom_item_menu = str_repeat("&nbsp;", $indent).$nom_item_menu;
                        $hijos = $this->get_item($record, 'HIJOS');
                        if ($hijos != 0) {
                            $nom_item_menu = '<strong>'.$nom_item_menu.'</strong>';
                            $temp->setVar($this->label_record.'.DW_TR_CSS', 'titulo_detalle');
                            $temp->setVar($this->label_record.'.LECTURA', '');
                            $temp->setVar($this->label_record.'.ESCRITURA', '');
                            $temp->setVar($this->label_record.'.IMPRESION', '');
                        }
                        
                        // para las parametricas
                        $tipo_item_menu = $this->get_item($record, 'TIPO_ITEM_MENU');
                        $string = strlen($cod_item_menu);
                        if ($tipo_item_menu=='P') {		// parametricas
                            $control->set_onChange("");
                            $indent += 2;
                            $nom_item_menu = str_repeat("&nbsp;", 2).$nom_item_menu;
                        }
                        elseif ($tipo_item_menu=='TP') {	// titulo parametrica
                            $nom_item_menu = '<strong>'.$nom_item_menu.'</strong>';
                            
                            if ($record % 2 == 0)
                                $temp->setVar($this->label_record.'.DW_TR_CSS', 'claro');
                                else
                                    $temp->setVar($this->label_record.'.DW_TR_CSS', 'oscuro');
                        }
                        $temp->setVar($this->label_record.'.NOM_ITEM_MENU', $nom_item_menu);
                        
                        // agregar checkbox de impresion solo si tiene impresion
                        $tiene_impresion = $this->get_item($record, 'TIENE_IMPRESION');
                        if ($tiene_impresion=='N')
                            $temp->setVar($this->label_record.'.IMPRESION', '');
                            
                            $tabs = $this->get_item($record, 'TAB');
                            if ($tabs=='')
                                $temp->setVar($this->label_record.'.TAB', '');
                                else{
                                    if ($tabs!=' ') {
                                        $tab = '<BR>'.str_repeat("&nbsp;", $indent + 10);
                                        $res = explode('|', $tabs);
                                        $tope = count($res);
                                        for ($i = 0; $i < $tope; $i=$i+3){
                                            for ($j=$i; $j<$i+3; $j++){
                                                if ($j==$i)  //codigo
                                                    $cod_item = $res[$j];
                                                    else if ($j==$i+1) // label
                                                        $label = $res[$j];
                                                        else if ($j==$i+2){  //valor
                                                            $valor = $res[$j];
                                                            if ($this->entrable)
                                                                $disable = '';
                                                                else
                                                                    $disable =  'disabled="disabled"';
                                                        } // end if ($j=$i+2)
                                            }	// end for ($j)
                                            if ($valor=='N')
                                                $tab .= '<label><input type="checkbox" id="TAB_'.$cod_item.'"'.$disable.' value="N" name="TAB_'.$cod_item.'"'.$disable.'>'.$label.'</label>  ';
                                                else
                                                    $tab .= '<label><input type="checkbox" id="TAB_'.$cod_item.'"'.$disable.' value="S" name="TAB_'.$cod_item.'" CHECKED '.$disable.'>'.$label.'</label>  ';
                                        }	// end for ($i)
                                        $temp->setVar($this->label_record.'.TAB', $tab);
                                        
                                    }
                                }
    }
    function new_record() {
        $sql = $this->sql;
        $sql = str_replace("{KEY1}", '2', $sql);
        $this->retrieve_bd($sql);
    }
    function validate() {
        for ($i=0; $i < $this->row_count(); $i++) {
            $lectura = $this->get_item($i, 'LECTURA');
            $escritura = $this->get_item($i, 'ESCRITURA');
            $impresion = $this->get_item($i, 'IMPRESION');
            if ($lectura=='N' && ($escritura=='S' || $impresion=='S'))
                $this->set_item($i, 'LECTURA', 'S');
        }
        return parent::validate();
    }
    function get_values_from_POST() {
        if (!$this->entrable)
            return ;
            
            parent::get_values_from_POST();
            
            // Para los TAB
            for ($i=0; $i < $this->row_count(); $i++) {
                $tabs = $this->get_item($i, 'TAB');
                $tabs_new = '';
                
                if ($tabs!=' '){
                    $res = explode('|', $tabs);
                    $tope = count($res);
                    $tabs_new = '';
                    for ($j = 0; $j < $tope; $j=$j+3){
                        for ($k=$j; $k<$j+3; $k++){
                            if ($k==$j)  //codigo
                                $cod_item = $res[$k];
                                else if ($k==$j+2){  //valor
                                    $id_tab = 'TAB_'.$cod_item;
                                    if (isset($_POST[$id_tab]))
                                        $valor = 'E';
                                        else
                                            $valor = 'N';
                                            $tabs_new = $tabs_new."$cod_item|$valor|";
                                }
                        }
                    }	// end for $j
                }
                $this->set_item($i, 'TAB', $tabs_new);
            }
    }
    function update($db) {
        for ($i=0; $i < $this->row_count(); $i++) {
            $cod_perfil = $this->get_item($i, 'COD_PERFIL');
            $cod_item_menu = $this->get_item($i, 'COD_ITEM_MENU');
            $lectura = $this->get_item($i, 'LECTURA');
            $escritura = $this->get_item($i, 'ESCRITURA');
            $impresion = $this->get_item($i, 'IMPRESION');
            $tabs = $this->get_item($i, 'TAB');
            $tabs = str_replace($tabs, '|', '');
            $sp = 'spu_autoriza_menu';
            $param = $cod_perfil.",'".$cod_item_menu."','".$lectura."','".$escritura."','".$impresion."','".$tabs."'";
            if (!$db->EXECUTE_SP($sp,$param))
                return false;
        }
        return true;
    }
}


/*
 Clase : WI_PERFIL
 */
class wi_perfil extends w_input
{
    function __construct($cod_item_menu)
    {
        parent::__construct('perfil', $cod_item_menu);
        $this->add_FK_delete_cascada('AUTORIZA_MENU');
        
        //////////////// CLASS_SQL = INPUT_WI_PERFIL ///////////////
        $sql = sql_perfil::get_sql('INPUT_WI_PERFIL');
        
        $this->dws['dw_perfil'] = new datawindow($sql);
        
        // asigna los formatos
        $this->dws['dw_perfil']->add_control($control = new edit_text_upper('NOM_PERFIL', 80, 100));
        $control->set_onChange("validate(); f_get_nom_perfil();");
        
        
        // asigna los mandatorys
        $this->dws['dw_perfil']->set_mandatory('NOM_PERFIL', 'Perfil');
        
        $this->dws['dw_autoriza_menu'] = new dw_autoriza_menu();
        $this->dws['dw_opciones_especiales'] = new dw_opciones_especiales();
        
    }
    
    function new_record()
    {
        $this->dws['dw_perfil']->insert_row();
        $this->dws['dw_autoriza_menu']->new_record();
        $this->dws['dw_opciones_especiales']->new_record();
        
    }
    function load_record()
    {
        $cod_perfil = $this->get_item_wo($this->current_record, 'COD_PERFIL');
        $this->dws['dw_perfil']->retrieve($cod_perfil);
        $this->dws['dw_autoriza_menu']->retrieve($cod_perfil);
        $this->dws['dw_opciones_especiales']->retrieve($cod_perfil);
        if($cod_perfil == 1 or $cod_perfil == 2){
            $this->b_delete_visible = false;
            $this->b_save_visible = false;
            $this->b_no_save_visible = false;
            $this->b_modify_visible = false;
        }
        else {
            $this->b_delete_visible = true;
            $this->b_save_visible = true;
            $this->b_no_save_visible = true;
            $this->b_modify_visible = true;
        }
        
    }
    function get_key()
    {
        return $this->dws['dw_perfil']->get_item(0, 'COD_PERFIL');
    }
    
    function save_record($db){
        $COD_PERFIL = $this->get_key();
        $NOM_PERFIL = $this->dws['dw_perfil']->get_item(0, 'NOM_PERFIL');
        
        $COD_PERFIL = ($COD_PERFIL=='') ? "null" : $COD_PERFIL;
        
        $sp = 'spu_perfil';
        if ($this->is_new_record()){
            $operacion = 'INSERT';
        }
        else{
            $operacion = 'UPDATE';
        }
        
        $param	= "'$operacion', $COD_PERFIL, '$NOM_PERFIL'";
        $COD_PERFIL = $this->get_key();
        
        if ($db->EXECUTE_SP($sp, $param)){
            if ($this->is_new_record()){
                $cod_perfil = $db->GET_IDENTITY('PERFIL');
                $this->dws['dw_perfil']->set_item(0, 'COD_PERFIL', $cod_perfil);
                
                for ($i=0; $i<$this->dws['dw_autoriza_menu']->row_count(); $i++){
                    $this->dws['dw_autoriza_menu']->set_item($i, 'COD_PERFIL', $cod_perfil, 'primary', false);
                }
                for ($i=0; $i<$this->dws['dw_opciones_especiales']->row_count(); $i++){
                    $this->dws['dw_opciones_especiales']->set_item($i, 'COD_PERFIL', $cod_perfil, 'primary', false);
                }
            }
        }
        else{
            return false;
        }
        if (!$this->dws['dw_autoriza_menu']->update($db)){
            return false;
        }
        if (!$this->dws['dw_opciones_especiales']->update($db)){
            return false;
        }
        return true;
    }
}
?>