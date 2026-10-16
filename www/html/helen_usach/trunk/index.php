<?php
include("appl.ini");
require_once(dirname(__FILE__)."/../../commonlib_seven/trunk/php/auto_load.php");

//////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/menu/".K_CLIENTE."/menu.php";
if (file_exists($file_name))
    require_once($file_name);
    else {
        // Menu normal
        $archivo = array( new item_menu('Usuarios', '0505', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=usuario&cod_item_menu=0505"),
            new item_menu('Perfiles', '0510', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=perfil&cod_item_menu=0510"),
            new item_menu('Parámetros', '0515', "../appl/parametro/wi_parametro.php?cod_item_menu=0515"),
            
            new item_menu('Cambio password', '0520', "../../../commonlib_seven/trunk/php/change_password.php"),
            new item_menu('Salir', '0525', "../../../commonlib_seven/trunk/php/cerrar_sesion.php"));
        
        $maestros = array( new item_menu('Deudor', '1005', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=deudor&cod_item_menu=1005"),
            new item_menu('Deuda', '1010', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=deuda&cod_item_menu=1010"),
            new item_menu('Pagaré', '1015', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=pagare&cod_item_menu=1015"),
            new item_menu('Plan de pagos', '1020', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=plan_pago&cod_item_menu=1020"),
            new item_menu('-'),
            new item_menu('Abonos', '1025', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=abono&cod_item_menu=1025"),
            new item_menu('Descuentos', '1030', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=descuento&cod_item_menu=1030"),
            new item_menu('-'),
            new item_menu('Declaración de ingreso', '1035', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=declaracion&cod_item_menu=1035"),
            new item_menu('-'),
            new item_menu('Suspensión', '1040', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=suspension&cod_item_menu=1040"),
            new item_menu('Condonación', '1045', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=condonacion&cod_item_menu=1045"),
            new item_menu('Prorroga', '1050', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=prorroga&cod_item_menu=1050"),
            new item_menu('Novación', '1055', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=novacion&cod_item_menu=1055"),
            new item_menu('-'),
            new item_menu('Candidatos condonación CS', '1060', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=condonacion_cs&cod_item_menu=1060")
        );
        
        $pagos = array( new item_menu('Simulación', '1502', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=simulacion&cod_item_menu=1502"),
            new item_menu('Boleta', '1505', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=boleta&cod_item_menu=1505"),
            new item_menu('-'),
            new item_menu('Carga cupones banco', '1510', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=carga_banco&cod_item_menu=1510"),
            new item_menu('-'),
            new item_menu('Ingreso Caja', '1515', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=ingreso&cod_item_menu=1515"),
            new item_menu('Arqueo Caja', '1516', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_arqueo_caja&cod_item_menu=1516"),
            new item_menu('Documento', '1520', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=documento&cod_item_menu=1520"),
            new item_menu('Depósitos', '1525', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=deposito&cod_item_menu=1525"),
            new item_menu('-'),
            new item_menu('Anular abono', '1530', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=anula_abono&cod_item_menu=1530"),
            new item_menu('-'),
            new item_menu('Excedentes', '1535', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=excedente&cod_item_menu=1535"),
        );
        
        $contabilidad = array( new item_menu('Comprobante', '2005', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=comprobante&cod_item_menu=2005"),
            new item_menu('-'),
            new item_menu('Flujo deudas SVP', '2010', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_flujo_svp&cod_item_menu=2010"),
            new item_menu('Flujo deudas Exigibles', '2015', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_flujo_exigible&cod_item_menu=2015"),
            new item_menu('-'),
            new item_menu('Libro diario', '2020', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_libro_diario&cod_item_menu=2020"),
            new item_menu('Libro mayor', '2025', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_libro_mayor&cod_item_menu=2025"),
            new item_menu('Balance ocho columnas', '2030', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_balance_ocho&cod_item_menu=2030"),
            new item_menu('-'),
            new item_menu('Conciliación bancaria', '2035', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=conciliacion&cod_item_menu=2035"),
            new item_menu('-'),
            new item_menu('Cierre Contable', '2040', "../appl/proc_common/proceso.php?modulo=cierre_contable&cod_item_menu=2040")
        );
        
        $procesos=array(new item_menu('Carga Matrícula', '2510', "../appl/proc_common/proceso.php?modulo=carga_matricula&cod_item_menu=2510"),
            new item_menu('Carga Pagarés', '2515', "../appl/proc_common/proceso.php?modulo=carga_pagare&cod_item_menu=2515"),
            new item_menu('-'),
            new item_menu('Obtiene mora', '2520', "../appl/proc_common/proceso.php?modulo=obtiene_mora&cod_item_menu=2520"),
            new item_menu('Nomina a TGR', '2521', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_nomina_TGR&cod_item_menu=2521"),
            new item_menu('Comprobante castigo', '2523', "../appl/proc_common/proceso.php?modulo=comprobante_castigo&cod_item_menu=2523"),
            new item_menu('Generación plan de pagos', '2525', "../appl/proc_common/proceso.php?modulo=genera_plan&cod_item_menu=2525"),
            new item_menu('-'),
            new item_menu('Comprobante traspaso a cuota vigente', '2530', "../appl/proc_common/proceso.php?modulo=comprobante_traspaso_cuota&cod_item_menu=2530"),
            new item_menu('Comprobante genera interés del año', '2535', "../appl/proc_common/proceso.php?modulo=comprobante_genera_interes_ano&cod_item_menu=2535"),
            new item_menu('Comprobante corrección monetaria', '2540', "../appl/proc_common/proceso.php?modulo=comprobante_correccion_monetaria&cod_item_menu=2540"),
            new item_menu('-'),
            new item_menu('Cálculo cuota CS', '2545', "../appl/proc_common/proceso.php?modulo=calculo_cuota_cs&cod_item_menu=2545"),
            new item_menu('Cálculo cuota CSR', '2550', "../appl/proc_common/proceso.php?modulo=calculo_cuota_csr&cod_item_menu=2550"),
            new item_menu('-'),
            new item_menu('Cartas', '2560', "../appl/proc_common/proceso.php?modulo=carta&cod_item_menu=2560"),
            new item_menu('-'),
            new item_menu('Auditoría procesos', '2570', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=auditoria_proceso&cod_item_menu=2570"),
        );
        
        $cruzados = array( new item_menu('Maestro matrícula Project', '3005', "../appl/maestro_matricula/wo_maestro_matricula.php?cod_item_menu=3005"),
            new item_menu('Maestro pagarés Project', '3010', "../appl/maestro_pagare/wo_maestro_pagare.php?cod_item_menu=3010"),
            new item_menu('Cargar consolidado Project', '3010', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=carga_consolidado_project&cod_item_menu=3010"),
            new item_menu('-'),
            new item_menu('Cargar cuotas otra cartera', '3020', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=carga_cuota_otra_cartera&cod_item_menu=3020"),
            new item_menu('-'),
            new item_menu('Rendición para otras universidades', '3025', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=rendicion&cod_item_menu=3025"),
        );
        
        $convenio = array( new item_menu('Convenio', '3505', "../../../commonlib_seven/trunk/php/mantenedor.php?modulo=renegociacion&cod_item_menu=3505"),
            new item_menu('-'),
            new item_menu('Cartas Aviso Incumplimiento', '3510', "../appl/proc_common/proceso.php?modulo=carta_aviso&cod_item_menu=3510"),
        );
        
        
        $informes = array( new item_menu('Recuperación por carrera', '4005', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_recup_carrera&cod_item_menu=4005"),
            new item_menu('Recuperación por tipo deuda', '4010', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_recup_tipo_deuda&cod_item_menu=4010"),
            new item_menu('Recuperación por años', '4015', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_recup_anos&cod_item_menu=4015"),
            new item_menu('Pronto Pago', '4020', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_pronto_pago&cod_item_menu=4020"),
            new item_menu('Informe CTY', '4025', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_informe_cty&cod_item_menu=4025"),
            new item_menu('Morosidad por años', '4030', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_morosidad_ano&cod_item_menu=4030"),
            new item_menu('Crédito asignado', '4035', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_credito_asig&cod_item_menu=4035"),
            new item_menu('Deudores en cobranzas', '4040', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_deudores_cobranza&cod_item_menu=4040"),
            new item_menu('Deudas canceladas', '4045', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_deudas_canceladas&cod_item_menu=4045"),
            new item_menu('Deudores declaración vía web', '4050', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_deudores_via_web&cod_item_menu=4050"),
            new item_menu('Deudores sin declaración', '4055', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_deudores_sin_declaracion&cod_item_menu=4055"),
            new item_menu('Deudores con excedentes', '4060', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_deudores_con_excedentes&cod_item_menu=4060"),
            new item_menu('Deudores morosos', '4070', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_deudores_morosos&cod_item_menu=4070"),
            new item_menu('Pagarés protestados', '4073', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_pagares_protestados&cod_item_menu=4073"),
            new item_menu('Deudores condonados', '4075', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_deudores_condonados&cod_item_menu=4075"),
            new item_menu('Deudores prorrogados', '4080', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_deudores_prorrogados&cod_item_menu=4080"),
            new item_menu('Deudores suspendidos', '4085', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_deudores_suspendidos&cod_item_menu=4085"),
            new item_menu('Deudores novados', '4090', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_deudores_novados&cod_item_menu=4090"),
            new item_menu('Recaudación por entidad', '4095', "../../../commonlib_seven/trunk/php/informe.php?informe=inf_recaudacion_entidad&cod_item_menu=4095"));
        
        $menu = new menu(array(new item_menu('Archivos', '05', '', $archivo),
            new item_menu('Maestros', '10', '', $maestros),
            new item_menu('Pagos', '15', '', $pagos),
            new item_menu('Contabilidad', '20', '', $contabilidad),
            new item_menu('Procesos', '25', '', $procesos),
            new item_menu('Cruzados', '30', '', $cruzados),
            new item_menu('Convenios', '35', '', $convenio),
            new item_menu('Informes', '40', '', $informes))
            ,57);
    }
    
    $menu = serialize($menu);
    session::set('menu_appl', $menu);
    //////////////////////
    
    session::set('K_ROOT_URL', K_ROOT_URL);
    session::set('K_ROOT_DIR', K_ROOT_DIR);
    session::set('K_APPL', K_APPL);
    session::set('K_CLIENTE', K_CLIENTE);
    
    $t = new Template_appl("index.htm");
    $t->setVar('K_ROOT_URL', K_ROOT_URL);
    
    print $t->toString();
    ?>