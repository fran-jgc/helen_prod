<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once ("../../appl.ini");

session::set('K_ROOT_URL', K_ROOT_URL);
session::set('K_ROOT_DIR', K_ROOT_DIR);
session::set('K_APPL', K_APPL);

// La commonlib usa estos datos
session::set("COD_USUARIO","2");
session::set("NOM_USUARIO","USUARIO WEB");

// Eliminar cookie
setcookie('FR','',time() - 1);

include("funciones.php");
class dw_index{
    function __construct() {
        $t = new Template_appl("index_template.html"); // No reemplazar el template_appl ni el archivo index_template.html, ya que muestran los indicadores economicos
        $sql_indicador = "  SELECT vm.monto_valor_moneda,'(' || m.simbolo || ')' as simbolo, m.NOM_MONEDA 

                            from moneda m, valor_moneda vm
                            
                            where vm.cod_moneda = m.cod_moneda 
                            and m.cod_moneda in(2,3,4,5)
                            and TO_DATE(vm.fecha_valor_moneda,'DD/MM/YY') = TO_DATE(SYSDATE,'DD/MM/YY') 
                            
                            order by m.cod_moneda asc";

                        
        
        $this->dws['dw_indicador_economico'] = new datawindow($sql_indicador,'indicador');
        
        $this->dws['dw_indicador_economico']->add_control(new static_text('NOM_MONEDA'));
        $this->dws['dw_indicador_economico']->add_control(new static_text('SIMBOLO'));
        $this->dws['dw_indicador_economico']->add_control(new static_num('MONTO_VALOR_MONEDA',2));
        $this->dws['dw_indicador_economico']->retrieve();
        $this->dws['dw_indicador_economico']->habilitar($t, true);
        print $t->toString();
    }
}

$w = new dw_index();