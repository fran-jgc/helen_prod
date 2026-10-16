create or replace PROCEDURE       spp_usach_carga_otra_ues(ve_cod_ejecucion_proceso        IN  NUMBER  := NULL
                                                     ,ve_cod_carga_otra_ues        IN  NUMBER  := NULL
                                                     ,ve_cod_usuario                IN  NUMBER  := NULL)

AS
BEGIN
  DECLARE
    K_ESTADO_CARGA_EXITOSA          NUMBER:= 2;
    K_ESTADO_SALDO_NEGATIVO          NUMBER:= 15;
    K_COBRANZA_NORMAL                NUMBER:= 2;
    K_DEUDA_CONDONADA				 NUMBER:= 3;
    K_DEUDA_CANCELADA                NUMBER:= 4;
    K_DEUDA_NO_EXIGIBLE                NUMBER:= 1;
    KL_FAMILIA_SOLIDARIO              NUMBER:= 1;
    KL_FAMILIA_INSTITUCIONAL        NUMBER:= 2;
    K_ESTADO_PAGO_VIGENTE            NUMBER:= 1;
    K_ESTADO_PAGO_ANULADO            NUMBER:= 2;
    vl_cod_origen_anterior            NUMBER:= 9999;
    vl_fecha_pago_anterior            DATE;
    vl_cod_estado_deuda                NUMBER;
    vl_cod_deudor                   NUMBER;
    vl_cod_plan_pago                NUMBER;
    vl_count_deudor                    NUMBER;
    vl_cod_abono                    NUMBER;
    vl_excedente_pesos                NUMBER;
    vl_cod_componente_deuda            NUMBER;
    vl_fecha_pago                    DATE;
    vl_cod_moneda                    NUMBER;
    vl_monto_capital_pesos            NUMBER;
    vl_monto_interes_pesos            NUMBER;
    vl_monto_multa_pesos            NUMBER;
    vl_factor_K                     NUMBER;
    vl_factor_IN                    NUMBER;
    vl_factor_MULTA                   NUMBER;
    vl_monto_capital                NUMBER;
      vl_monto_interes                  NUMBER;
      vl_monto_multa                    NUMBER;
      vl_count_origen_pago            NUMBER;
      vl_fecha_carga                    DATE;
    vl_nom_comprobante                VARCHAR(100);
    vl_origen_comprobante            NUMBER;
      vl_cod_comp_solidario            NUMBER:= null;
      vl_count_comprobante            NUMBER;
      vl_cod_cuenta_bco                NUMBER;
      vl_porc_otras_ues                NUMBER;
      vl_monto_descto_moneda             NUMBER;
    vl_monto_dscto_capital          NUMBER;
    vl_monto_dscto_interes          NUMBER;
    vl_monto_dscto_multa            NUMBER;
    vl_calc_total_abono               NUMBER;
    vl_calc_total_descto          NUMBER;
    vl_monto_dscto_otras_ues      NUMBER;
    vl_monto_dscto_otras_ues_utm  NUMBER;
    vl_monto_k_otras_ues              NUMBER;
    vl_monto_k_otras_ues_utm          NUMBER;
    vl_monto_capital_fict             NUMBER;
    vl_monto_interes_fict             NUMBER;
    vl_monto_multa_fict             NUMBER;
    vl_monto_dscto_capital_fict   NUMBER;
    vl_monto_dscto_interes_fict   NUMBER;
    vl_monto_dscto_multa_fict     NUMBER;
    vl_cod_origen                   NUMBER;
    vl_fecha_pago_comp                DATE;
    vl_valor_moneda                 NUMBER;
    vl_total_pesos_abono             NUMBER;
    vl_cod_descuento                  NUMBER;
    vl_cod_cuenta_dscto             NUMBER;
    vl_cod_tipo_deuda               VARCHAR(20);
    vl_count_pp_pendientes            NUMBER;
    vl_count_tot_reg_ant            NUMBER;
    vl_count_tot_exit_ejec            NUMBER;

    vl_count_tot_reg                NUMBER;
    vl_count_tot_exitoso            NUMBER;
    vl_count_tot_pendiente            NUMBER;
    vl_tot_monto_carga                NUMBER;
    vl_tot_monto_exitoso            NUMBER;
    vl_tot_monto_pendiente            NUMBER;
    vl_cod_analitico                  NUMBER;
    vl_cta_excedente                  NUMBER;
    vl_count_sin_analitico            NUMBER;

    K_COMP_CARGA_BCO_OTRAS_UNIV          NUMBER:= 31;
    K_CTA_DSCTO                       NUMBER:= 9;
    K_CONCEPTO_CUENTA_EXCEDENTE     NUMBER:= 19;

    vl_cod_deuda                    NUMBER;
    vl_count_cruzado                NUMBER;
    vl_porc_usach                    NUMBER;
    vl_ano_cuota                     NUMBER;
    vl_nro_cuota                    NUMBER;
    vl_monto_utm_deuda_cancelada NUMBER;

    vl_factor_IP NUMBER;
    vl_monto_multa_pesos_z NUMBER;
    vl_monto_interes_pesos_z NUMBER;
    vl_monto_capital_pesos_z NUMBER;
    vl_estado_plan_pago NUMBER;
    vl_monto_pp NUMBER;
    --PARA VALIDAR SI LA DEUDA PERTENECE AL DEUDOR
    vl_count_deuda_deudor NUMBER;
    --estados plan_pago
    K_ESTADO_PP_EMITIDA     NUMBER:= 1;
    K_ESTADO_PP_ABONADA     NUMBER:= 2;
    K_ESTADO_PP_PAGADA      NUMBER:= 3;
    K_ESTADO_PP_MOROSA      NUMBER:= 4;
    --cursor moroso
    CURSOR     C_ITEM_ORSAN IS
      select cod_item_carga_otra_ues
            ,rut
            ,fecha_pago
            ,ano_cuota
            ,monto_utm
            ,monto_pesos
            ,monto_descto_utm
            ,monto_descto_pesos
            ,cod_origen_pago
            ,fecha_deposito
            ,folio
            ,cod_deuda
            ,nro_cuota
        from item_carga_otra_ues
        where cod_carga_otra_ues = ve_cod_carga_otra_ues
        and cod_estado_carga not in(K_ESTADO_CARGA_EXITOSA,K_ESTADO_SALDO_NEGATIVO)
        ORDER BY cod_origen_pago,fecha_deposito, cod_item_carga_otra_ues;

    BEGIN

        vl_fecha_pago_anterior:= to_date('01/01/1900', 'dd/mm/yyyy');

        select count(*)
        into vl_count_tot_reg_ant
        from item_carga_otra_ues
        where cod_carga_otra_ues    = ve_cod_carga_otra_ues
        and cod_estado_carga <> K_ESTADO_CARGA_EXITOSA;

        select count(*)
        into vl_count_tot_reg
        from item_carga_otra_ues
        where cod_carga_otra_ues = ve_cod_carga_otra_ues;

        select sum(monto_pesos)
        into vl_tot_monto_carga
        from item_carga_otra_ues
        where cod_carga_otra_ues = ve_cod_carga_otra_ues;

        update  carga_otra_ues set  total_reg_carga = vl_count_tot_reg
                                    ,monto_reg_carga = vl_tot_monto_carga               
        where cod_carga_otra_ues = ve_cod_carga_otra_ues;
        FOR a IN C_ITEM_ORSAN LOOP
            --si el origen de un item es modificado y no tiene comprobante se debe crear el comprobante nuevo
            vl_cod_origen := a.cod_origen_pago;
            vl_fecha_pago_comp := a.fecha_deposito;

            select fecha_carga_otra_ues
            into vl_fecha_carga
            from  carga_otra_ues
            where cod_carga_otra_ues= ve_cod_carga_otra_ues;
            --validar que el origen sea un origen orsan
            IF((vl_cod_origen <> vl_cod_origen_anterior)  or (vl_fecha_pago_comp <> vl_fecha_pago_anterior))THEN
                 select count(*)
                 into vl_count_origen_pago
                 from origen_pago
                 where cod_origen_pago = vl_cod_origen;

                 if(vl_count_origen_pago = 0)then
                    -- estado "Origen No ORSAN"
                    update item_carga_otra_ues set cod_estado_carga = 5
                    where cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;
                 end if;
                 
            END IF;--vl_cod_origen <> vl_cod_origen_anterior

            vl_cod_origen_anterior:= a.cod_origen_pago;
            vl_fecha_pago_anterior:= a.fecha_deposito;

            --valida si existe deuda
            BEGIN
                select cod_deuda, cod_estado_deuda, cod_tipo_deuda
                into vl_cod_deuda, vl_cod_estado_deuda,vl_cod_tipo_deuda
                from deuda de
                where cod_deuda = a.cod_deuda;
             EXCEPTION WHEN NO_DATA_FOUND THEN
                -- Maneja el caso cuando no se encuentra ningún dato
                 --estado 8 "Deuda no existe"
                update item_carga_otra_ues set cod_estado_carga = 8
                where cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;
                --continua con el siguiente registro
                GOTO end_loop;
             END; 
             
            --valida que el deudor exista
            BEGIN
                select cod_deudor
                into vl_cod_deudor
                from deudor
                where rut = a.rut;
             EXCEPTION WHEN NO_DATA_FOUND THEN
                -- Maneja el caso cuando no se encuentra ningún dato
                 --estado 7 "Deudor no existe"
                update item_carga_otra_ues set cod_estado_carga = 7
                where cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;
                --continua con el siguiente registro
                GOTO end_loop;
             END; 
            --valida que la deuda pertenezca al deudor
             select count(*)
             into vl_count_deuda_deudor
             from deuda d, deudor de
             where d.cod_deudor = de.cod_deudor
             and de.rut = a.rut
             and d.cod_deuda = a.cod_deuda;

             if(vl_count_deuda_deudor=0)then
                --estado 13 "Deuda no pertenece a deudor"
                update item_carga_otra_ues set cod_estado_carga = 13
                where cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;
                --continua con el siguiente registro
                GOTO end_loop;
            end if;

            --obtiene cod_moneda de la  deuda
            select cod_moneda
            into vl_cod_moneda
            from tipo_deuda
            where cod_tipo_deuda = vl_cod_tipo_deuda;

            --si no viene fecha de pago se toma la de hoy
            if(a.fecha_pago is not null)then
                vl_fecha_pago:= a.fecha_pago;
            else
                vl_fecha_pago:= sysdate;
            end if;
            --obtiene valor moneda
            vl_valor_moneda  := f_mon_get_valor_moneda(vl_cod_moneda, vl_fecha_pago);
            if((vl_valor_moneda is null) or (vl_valor_moneda = 0))then
                --estado 4 "Falta valor moneda para la Fecha pago"
                update item_carga_otra_ues set cod_estado_carga = 4
                where cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;
            end if;
            --verifica es cruzado y calcula el porcentaje para cada universidad
            select count(*)
            into vl_count_cruzado
            from cruzado
            where cod_deuda = vl_cod_deuda
            and cod_universidad = 6;
    
            if(vl_count_cruzado > 0)then
                select porc
                into vl_porc_usach
                from cruzado
                where cod_deuda = vl_cod_deuda
                and cod_universidad = 6
                and rownum = 1;
            else
            --No es cruzado y no registra ABONO_FICTICIO
                vl_porc_usach := 100; 
            end if;
            vl_porc_otras_ues := 100 - vl_porc_usach;
            --deudas cobranza normal
            if(vl_cod_estado_deuda = K_COBRANZA_NORMAL)then
                --buscar cod_plan_pago
                --si el tipo deuda es 'CRPI' se debe buscar por ano_cuota y nro_cuota
                if(vl_cod_tipo_deuda='CRPI') then
                    BEGIN
                        SELECT cod_plan_pago
                        INTO vl_cod_plan_pago
                        FROM plan_pago
                        WHERE ano_cobro = a.ano_cuota
                        AND cod_deuda = a.cod_deuda
                        AND nro_cuota = a.nro_cuota;
                    EXCEPTION
                        WHEN NO_DATA_FOUND THEN
                        --si no encuentra el plan pago cod_plan_pago queda null
                        vl_cod_plan_pago := NULL;  
                        --estado 12 "No existe Plan Pago"
                        UPDATE item_carga_otra_ues SET cod_estado_carga = 13
                        WHERE cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;
                        GOTO end_loop;
                    END;
                else
                    --de lo contrario se busca solo por año (esto es porque las deudas tienen un plan pago por año)
                    -- a excepcion de CRPI
                    BEGIN
                        SELECT cod_plan_pago
                        INTO vl_cod_plan_pago
                        FROM plan_pago
                        WHERE ano_cobro = a.ano_cuota
                        AND cod_deuda = a.cod_deuda;
                    EXCEPTION
                        WHEN NO_DATA_FOUND THEN
                            --si no encuentra el plan pago cod_plan_pago queda null
                            vl_cod_plan_pago := NULL;
                            --estado 12 "No existe Plan Pago"
                            UPDATE item_carga_otra_ues SET cod_estado_carga = 13
                            WHERE cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;
                            GOTO end_loop;
                    END;
                end if;
                --validar que el plan_pago este en estado EMITIDA,ABONADO O MOROSA
                
                select cod_estado_plan_pago, f_pp_saldo_plan_pago(cod_plan_pago,vl_fecha_pago)
                into vl_estado_plan_pago, vl_monto_pp
                from plan_pago
                where cod_plan_pago = vl_cod_plan_pago;
                -- Validar que el plan_pago tenga saldo
                -- y que esté en un estado válido: 1 EMITIDA, 2 ABONADA o 4 MOROSA
                IF vl_monto_pp <= 0 OR vl_estado_plan_pago NOT IN (K_ESTADO_PP_EMITIDA, K_ESTADO_PP_ABONADA, K_ESTADO_PP_MOROSA) THEN
                        -- estado 9 "No tiene saldo"
                        UPDATE item_carga_otra_ues SET cod_estado_carga = 9
                        WHERE cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;

                    GOTO end_loop;
                END IF;
                
                --si se encuentra plan pago
                -- calcula el componente del pago para abono que va a ser un proceso nuevo y descuento
                vl_cod_componente_deuda := f_pp_componente_pago(vl_cod_plan_pago, vl_fecha_pago);
                vl_cod_descuento := null;
                if( (a.monto_descto_pesos > 0) and (vl_valor_moneda > 0))then
                    vl_monto_descto_moneda  := Round(a.monto_descto_pesos / vl_valor_moneda, 4);
                    vl_monto_capital_pesos  := round(f_pp_saldo_capital_caja(vl_cod_plan_pago, vl_fecha_pago) * vl_valor_moneda, 0);
                    vl_monto_interes_pesos  := round(f_pp_saldo_interes_normal_caja(vl_cod_plan_pago, vl_fecha_pago) * vl_valor_moneda, 0);
                    vl_monto_multa_pesos    := round(f_pp_saldo_multa_caja(vl_cod_plan_pago, vl_fecha_pago) * vl_valor_moneda, 0);
                    if((vl_monto_capital_pesos + vl_monto_interes_pesos) <> 0) then
                        -- porporcion K, IN
                        vl_factor_K  := vl_monto_capital_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
                        vl_factor_IN := vl_monto_interes_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
                        vl_factor_MULTA := vl_monto_multa_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);

                        vl_monto_dscto_capital  := Round(vl_monto_descto_moneda * vl_factor_K, 4);
                        vl_monto_dscto_interes  := Round(vl_monto_descto_moneda * vl_factor_IN, 4);
                        vl_monto_dscto_multa    := Round((vl_monto_descto_moneda - vl_monto_dscto_capital - vl_monto_dscto_interes), 4);

                        IF (vl_monto_dscto_interes < 0)THEN
                            vl_monto_dscto_capital := vl_monto_dscto_capital + (vl_monto_dscto_interes * -1);
                            vl_monto_dscto_interes := 0;
                        END IF;
                            
                        INSERT INTO descuento( cod_descuento
                                                ,fecha_registro
                                                  ,fecha_descuento
                                                  ,cod_usuario
                                                  ,cod_componente_deuda
                                                  ,monto_capital
                                                  ,monto_interes
                                                  ,monto_multa
                                                  ,monto_moneda
                                                  ,monto_pesos
                                                  ,cod_plan_pago
                                                  ,cod_deuda
                                                  ,cod_comprobante
                                                  ,cod_estado_pago
                                                  ,cod_origen_pago
                                                  ,cod_abono)
                                         VALUES(descuento_sq.NEXTVAL
                                                ,sysdate
                                                ,vl_fecha_pago
                                                ,ve_cod_usuario
                                                ,vl_cod_componente_deuda
                                                ,vl_monto_dscto_capital
                                                ,vl_monto_dscto_interes
                                                ,vl_monto_dscto_multa
                                                ,vl_monto_descto_moneda
                                                ,a.monto_descto_pesos
                                                ,vl_cod_plan_pago
                                                ,a.cod_deuda
                                                ,NULL
                                                ,K_ESTADO_PAGO_VIGENTE
                                                ,a.cod_origen_pago 
                                                ,null);

                        SELECT descuento_sq.CURRVAL
                        INTO vl_cod_descuento
                        FROM dual;
                        
                         --si es cruzado calcula descuento para otra univ
                        IF(vl_porc_otras_ues > 0)THEN
                          vl_calc_total_descto          := (a.monto_descto_pesos * 100) / vl_porc_usach;
                          vl_monto_dscto_otras_ues      := vl_calc_total_descto * (vl_porc_otras_ues / 100);
                          vl_monto_dscto_otras_ues_utm  := Round(vl_monto_dscto_otras_ues / vl_valor_moneda, 4);
            
                          vl_monto_dscto_capital_fict := Round(vl_monto_dscto_otras_ues_utm * vl_factor_K, 4);
                          vl_monto_dscto_interes_fict := Round(vl_monto_dscto_otras_ues_utm * vl_factor_IN, 4);
                          vl_monto_dscto_multa_fict := Round((vl_monto_dscto_otras_ues_utm - vl_monto_dscto_capital_fict - vl_monto_dscto_interes_fict), 4);
            
                          IF (vl_monto_dscto_interes_fict < 0)THEN
                            vl_monto_dscto_capital_fict := vl_monto_dscto_capital_fict + (vl_monto_dscto_interes_fict * -1);
                            vl_monto_dscto_interes_fict := 0;
                          END IF;


                          INSERT INTO descuento_ficticio(COD_DESCUENTO_FICTICIO
                                                        ,FECHA_REGISTRO
                                                        ,FECHA_DESCUENTO
                                                        ,COD_USUARIO
                                                        ,MONTO_CAPITAL
                                                        ,MONTO_INTERES
                                                        ,MONTO_MULTA
                                                        ,MONTO_MONEDA
                                                        ,MONTO_PESOS
                                                        ,COD_DEUDA
                                                        ,COD_PLAN_PAGO
                                                        ,COD_DESCUENTO)
                          VALUES(descuento_ficticio_sq.NEXTVAL  -- cod_descuento_ficticio
                                ,sysdate                        -- fecha_registro
                                ,vl_fecha_pago                   -- fecha_descuento
                                ,ve_cod_usuario                 -- cod_usuario
                                ,vl_monto_dscto_capital_fict   -- monto_capital
                                ,vl_monto_dscto_interes_fict    -- monto_interes
                                ,vl_monto_dscto_multa_fict      -- monto_multa
                                ,vl_monto_dscto_otras_ues_utm   -- monto_moneda
                                ,vl_monto_dscto_otras_ues       -- monto_pesos
                                ,a.cod_deuda                    -- cod_deuda
                                ,vl_cod_plan_pago               -- cod_plan_pago
                                ,vl_cod_descuento               -- cod_descuento
                                );

                        END IF;
                        
                        sp_pp_actualiza_estado(vl_cod_plan_pago);
                        sp_deuda_actualiza_estado(a.cod_deuda);
                        
                        update item_carga_otra_ues set cod_estado_carga = 2
                        where cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;

                        update  carga_otra_ues set total_reg_exitoso = total_reg_exitoso + 1        
                        where cod_carga_otra_ues = ve_cod_carga_otra_ues;

                        insert into BITACORA_CARGA_OTRA_UES (COD_BITACORA_CARGA_OTRA_UES
                                                            ,FECHA_REGISTRO
                                                             ,COD_CARGA_OTRA_UES
                                                             ,COD_ITEM_CARGA_OTRA_UES
                                                             ,COD_USUARIO
                                                             ,OBSERVACION)
                                                       values(bitacora_carga_otra_ues_sq.NEXTVAL
                                                                ,sysdate
                                                                ,ve_cod_carga_otra_ues
                                                                ,a.cod_item_carga_otra_ues
                                                                ,ve_cod_usuario
                                                                ,'Carga descuento de forma exitosa');
                    else
                        update item_carga_otra_ues set cod_estado_carga = 6
                        where cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;
                    end if;--(vl_monto_capital_pesos + vl_monto_interes_pesos) <> 0
                else
                    update item_carga_otra_ues set cod_estado_carga = 6
                    where cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;
                end if;--b.monto_descto_pesos > 0
                --si el monto pesos del abono y el valor moneda son mayor a 0 hace el calculo de capital, interes y multa
                if ((a.monto_pesos > 0) and (vl_valor_moneda > 0))then

                    vl_monto_capital_pesos  := round(f_pp_saldo_capital_caja(vl_cod_plan_pago, vl_fecha_pago) * vl_valor_moneda, 0);
                    vl_monto_interes_pesos  := round(f_pp_saldo_interes_normal_caja(vl_cod_plan_pago, vl_fecha_pago) * vl_valor_moneda, 0);
                    vl_monto_multa_pesos    := round(f_pp_saldo_multa_caja(vl_cod_plan_pago, vl_fecha_pago) * vl_valor_moneda, 0);
                    vl_total_pesos_abono := a.monto_pesos;
                    if((vl_monto_capital_pesos + vl_monto_interes_pesos) <> 0) then
                         -- porporcion K, IN
                        vl_factor_K  := vl_monto_capital_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
                        vl_factor_IN := vl_monto_interes_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
                        vl_factor_MULTA := vl_monto_multa_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);

                        vl_monto_capital  := Round((vl_total_pesos_abono / vl_valor_moneda) * vl_factor_K, 4);
                        vl_monto_interes  := Round((vl_total_pesos_abono / vl_valor_moneda) * vl_factor_IN, 4);
                        vl_monto_multa    := Round(((vl_total_pesos_abono / vl_valor_moneda) - vl_monto_capital - vl_monto_interes), 4);

                        if(vl_monto_interes < 0)then
                            vl_monto_capital := vl_monto_capital + (vl_monto_interes * -1);
                            vl_monto_interes := 0;
                        end if;
                        -- crea abono directo
                        INSERT INTO abono
                                          (cod_abono
                                          ,fecha_registro
                                          ,fecha_abono
                                          ,cod_usuario
                                          ,cod_componente_deuda
                                          ,monto_capital
                                          ,monto_interes
                                          ,monto_multa
                                          ,monto_moneda
                                          ,monto_pesos
                                          ,diferencia_pesos
                                          ,cod_plan_pago
                                          ,cod_deuda
                                          ,cod_abono_desde
                                          ,cod_estado_pago
                                          ,cod_origen_pago
                                          ,cod_sub_boleta
                                          ,cod_documento_pago
                                          ,cod_comprobante
                                          ,fecha_deposito
                                          ,folio_carga)
                                    VALUES (abono_sq.NEXTVAL
                                          ,SYSDATE
                                          ,vl_fecha_pago
                                          ,ve_cod_usuario
                                          ,vl_cod_componente_deuda
                                          ,vl_monto_capital
                                          ,vl_monto_interes
                                          ,vl_monto_multa
                                          ,vl_monto_capital + vl_monto_interes + vl_monto_multa
                                          ,vl_total_pesos_abono
                                          ,0
                                          ,vl_cod_plan_pago
                                          ,vl_cod_deuda
                                          ,null
                                          ,K_ESTADO_PAGO_VIGENTE
                                          ,a.cod_origen_pago
                                          ,null
                                          ,null
                                          ,vl_cod_comp_solidario
                                          ,a.fecha_deposito
                                          ,a.folio);
                                 
                        SELECT abono_sq.CURRVAL
                        INTO vl_cod_abono
                        FROM dual;
                        --si es cruzado calcula el abono ficticio
                        IF(vl_porc_otras_ues > 0)THEN
                            vl_calc_total_abono       := vl_total_pesos_abono;
                            vl_monto_k_otras_ues      := vl_calc_total_abono * (vl_porc_otras_ues / vl_porc_usach);
                            vl_monto_k_otras_ues_utm  := Round(vl_monto_k_otras_ues / vl_valor_moneda, 4);
    
                            if((vl_monto_capital+vl_monto_interes+vl_monto_multa)<>0)then
                                vl_monto_capital_pesos_z := ROUND(vl_monto_capital * vl_valor_moneda,0);
                                vl_monto_interes_pesos_z := ROUND(vl_monto_interes * vl_valor_moneda,0);
                                vl_monto_multa_pesos_z := ROUND(vl_monto_multa * vl_valor_moneda,0);
    
                                vl_factor_K  := vl_monto_capital_pesos_z / (vl_monto_capital_pesos_z + vl_monto_interes_pesos_z + vl_monto_multa_pesos_z);
                                vl_factor_IN := vl_monto_interes_pesos_z / (vl_monto_capital_pesos_z + vl_monto_interes_pesos_z + vl_monto_multa_pesos_z);
                                vl_factor_IP := vl_monto_multa_pesos_z / (vl_monto_capital_pesos_z + vl_monto_interes_pesos_z + vl_monto_multa_pesos_z);
    
                                vl_monto_capital_fict := Round(vl_monto_k_otras_ues_utm * vl_factor_K, 4);
                                vl_monto_interes_fict := Round(vl_monto_k_otras_ues_utm * vl_factor_IN, 4);
                                vl_monto_multa_fict := Round((vl_monto_k_otras_ues_utm - vl_monto_capital_fict - vl_monto_interes_fict), 4);
                                IF (vl_monto_interes_fict < 0)THEN
                                    vl_monto_capital_fict := vl_monto_capital_fict + (vl_monto_interes_fict * -1);
                                    vl_monto_interes_fict := 0;
                                end if;
                                INSERT INTO ABONO_FICTICIO (cod_abono_ficticio
                                                  ,fecha_registro
                                                  ,fecha_abono
                                                  ,cod_usuario
                                                  ,monto_capital
                                                  ,monto_interes
                                                  ,monto_multa
                                                  ,monto_moneda
                                                  ,monto_pesos
                                                  ,cod_deuda
                                                  ,cod_plan_pago
                                                  ,cod_abono
                                                  ,fecha_deposito
                                                  ,folio_carga )
                                        VALUES(abono_ficticio_sq.NEXTVAL
                                                   ,SYSDATE
                                                   ,vl_fecha_pago
                                                   ,ve_cod_usuario
                                                   ,vl_monto_capital_fict
                                                   ,vl_monto_interes_fict
                                                   ,vl_monto_multa_fict
                                                   ,vl_monto_k_otras_ues_utm
                                                   ,vl_monto_k_otras_ues
                                                   ,vl_cod_deuda
                                                   ,vl_cod_plan_pago
                                                   ,vl_cod_abono
                                                   ,vl_fecha_carga
                                                   ,a.folio);
                            end if;
                        END IF; --vl_porc_otras_ues > 0
    
                        select  f_pp_saldo_plan_pago(cod_plan_pago,vl_fecha_pago)
                        into  vl_monto_pp
                        from plan_pago
                        where cod_plan_pago = vl_cod_plan_pago;
                        
                        if(vl_monto_pp < 0)then
                             --carga exitosa con saldo negativo
                            update item_carga_otra_ues set cod_estado_carga = 15
                            where cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;
                        else
                            --carga exitosa
                            update item_carga_otra_ues set cod_estado_carga = 2
                                ,cod_comprobante = vl_cod_comp_solidario
                            where cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;
                        end if;
                    
                        --actualiza registros exitosos
                        update  carga_otra_ues set total_reg_exitoso = total_reg_exitoso + 1    
                        where cod_carga_otra_ues = ve_cod_carga_otra_ues;
    
                        insert into BITACORA_CARGA_OTRA_UES (COD_BITACORA_CARGA_OTRA_UES
                                                            ,FECHA_REGISTRO
                                                            ,cod_carga_otra_ues
                                                            ,cod_item_carga_otra_ues
                                                            ,COD_USUARIO
                                                            ,OBSERVACION)
                                                    values(bitacora_carga_otra_ues_sq.NEXTVAL
                                                            ,sysdate
                                                            ,ve_cod_carga_otra_ues
                                                            ,a.cod_item_carga_otra_ues
                                                            ,ve_cod_usuario
                                                            ,'Carga abono de forma exitosa');
    
                        spu_bitacora_deudor ('Carga abono OTRAS UES : ' || TO_CHAR(vl_cod_deuda) , 44, vl_cod_deudor, ve_cod_usuario);
    
                    else
                        update item_carga_otra_ues set cod_estado_carga = 6
                                                    ,cod_comprobante = vl_cod_comp_solidario
                        where cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;
                    end if;   --if((vl_monto_capital_pesos + vl_monto_interes_pesos) <> 0) then        
                else
                    update item_carga_otra_ues set cod_estado_carga = 6
                                                    ,cod_comprobante = vl_cod_comp_solidario
                    where cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;
               
                end if;--if ((a.monto_pesos > 0) and (vl_valor_moneda > 0))then
            
                --actualiza el estado del plan_pago
                sp_pp_actualiza_estado(vl_cod_plan_pago);
                sp_deuda_actualiza_estado(a.cod_deuda);
            END IF; -- K_COBRANZA_NORMAL
            IF(vl_cod_estado_deuda = K_DEUDA_CANCELADA) or (vl_cod_estado_deuda = K_DEUDA_CONDONADA) THEN 
                --buscar cod_plan_pago
                --si el tipo deuda es 'CRPI' se debe buscar por ano_cuota y nro_cuota
                if(vl_cod_tipo_deuda='CRPI') then
                    BEGIN
                        SELECT cod_plan_pago
                        INTO vl_cod_plan_pago
                        FROM plan_pago
                        WHERE ano_cobro = a.ano_cuota
                        AND cod_deuda = a.cod_deuda
                        AND nro_cuota = a.nro_cuota;
                    EXCEPTION
                        WHEN NO_DATA_FOUND THEN
                        --si no encuentra el plan pago cod_plan_pago queda null
                        vl_cod_plan_pago := NULL;  
                        --estado 12 "No existe Plan Pago"
                        UPDATE item_carga_otra_ues SET cod_estado_carga = 13
                        WHERE cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;
                        GOTO end_loop;
                    END;
                else
                    --de lo contrario se busca solo por año (esto es porque las deudas tienen un plan pago por año)
                    -- a excepcion de CRPI
                    BEGIN
                        SELECT cod_plan_pago
                        INTO vl_cod_plan_pago
                        FROM plan_pago
                        WHERE ano_cobro = a.ano_cuota
                        AND cod_deuda = a.cod_deuda;
                    EXCEPTION
                        WHEN NO_DATA_FOUND THEN
                            --si no encuentra el plan pago cod_plan_pago queda null
                            vl_cod_plan_pago := NULL;
                            --estado 13 "No existe Plan Pago"
                            UPDATE item_carga_otra_ues SET cod_estado_carga = 13
                            WHERE cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;
                            GOTO end_loop;
                    END;
                end if;
                  
                    -- calcula el componente del pago
                    vl_cod_componente_deuda := f_pp_componente_pago(vl_cod_plan_pago, vl_fecha_pago);

                    --si monto utm viene null se calcula, si viene se toma ese
                    if(a.monto_utm is null)then
                        vl_monto_utm_deuda_cancelada  := a.monto_pesos/vl_valor_moneda;
                    else
                        vl_monto_utm_deuda_cancelada := a.monto_utm;
                    end if;
                    
                    --insert abono anulado y excedente asociado al abono
                    INSERT INTO abono(cod_abono
                                        ,fecha_registro
                                        ,fecha_abono
                                        ,cod_usuario
                                        ,cod_componente_deuda
                                        ,monto_capital
                                        ,monto_interes
                                        ,monto_multa
                                        ,monto_moneda
                                        ,monto_pesos
                                        ,diferencia_pesos
                                        ,cod_plan_pago
                                        ,cod_deuda
                                        ,cod_estado_pago
                                        ,cod_origen_pago
                                        ,cod_comprobante
                                        ,fecha_deposito
                                        ,folio_carga)
                                VALUES (abono_sq.NEXTVAL
                                        ,SYSDATE
                                        ,vl_fecha_pago
                                        ,ve_cod_usuario
                                        ,vl_cod_componente_deuda
                                        ,0
                                        ,0
                                        ,0
                                        ,0
                                        ,a.monto_pesos
                                        ,0
                                        ,vl_cod_plan_pago
                                        ,vl_cod_deuda
                                        ,K_ESTADO_PAGO_ANULADO
                                        ,a.cod_origen_pago
                                        ,vl_cod_comp_solidario
                                        ,a.fecha_deposito
                                        ,a.folio);

                    SELECT abono_sq.CURRVAL
                    INTO vl_cod_abono
                    FROM dual;
                    
                    --se crea el excedente
                    vl_excedente_pesos := a.monto_pesos;
                    spu_excedente('INSERT', null, sysdate, vl_cod_deudor, vl_excedente_pesos , 1, null, ve_cod_usuario, vl_cod_comp_solidario, vl_cod_abono, null, null);
    
                    --item carga en estado --16 carca estado cancelada
                    update item_carga_otra_ues set cod_estado_carga = 16
                    where cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;

                    update  carga_otra_ues set total_reg_exitoso = total_reg_exitoso + 1    
                    where cod_carga_otra_ues = ve_cod_carga_otra_ues;
                    
                    insert into BITACORA_CARGA_OTRA_UES (COD_BITACORA_CARGA_OTRA_UES
                                                        ,FECHA_REGISTRO
                                                        ,cod_carga_otra_ues
                                                        ,cod_item_carga_otra_ues
                                                        ,COD_USUARIO
                                                        ,OBSERVACION)
                                            values(bitacora_carga_otra_ues_sq.NEXTVAL
                                                    ,sysdate
                                                    ,ve_cod_carga_otra_ues
                                                    ,a.cod_item_carga_otra_ues
                                                    ,ve_cod_usuario
                                                    ,'Deuda CANCELADA, carga abono anulado y excedente de forma exitosa');

                    spu_bitacora_deudor ('Carga abono OTRAS UES : ' || TO_CHAR(vl_cod_deuda) , 44, vl_cod_deudor, ve_cod_usuario);

                END IF;    --K_DEUDA_CANCELADA   y K_DEUDA_CONDONADA
            IF(vl_cod_estado_deuda = K_DEUDA_NO_EXIGIBLE) THEN
                vl_cod_componente_deuda :=1;
                vl_cod_descuento := null;
                if( (a.monto_descto_pesos > 0) and (vl_valor_moneda > 0))then
                    vl_monto_descto_moneda  := Round(a.monto_descto_pesos / vl_valor_moneda, 4);
                    vl_monto_capital_pesos  := round(f_pp_saldo_capital_caja(vl_cod_plan_pago, vl_fecha_pago) * vl_valor_moneda, 0);
                    vl_monto_interes_pesos  := round(f_pp_saldo_interes_normal_caja(vl_cod_plan_pago, vl_fecha_pago) * vl_valor_moneda, 0);
                    vl_monto_multa_pesos    := round(f_pp_saldo_multa_caja(vl_cod_plan_pago, vl_fecha_pago) * vl_valor_moneda, 0);
                    if((vl_monto_capital_pesos + vl_monto_interes_pesos) <> 0) then
                        -- porporcion K, IN
                        vl_factor_K  := vl_monto_capital_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
                        vl_factor_IN := vl_monto_interes_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
                        vl_factor_MULTA := vl_monto_multa_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);

                        vl_monto_dscto_capital  := Round(vl_monto_descto_moneda * vl_factor_K, 4);
                        vl_monto_dscto_interes  := Round(vl_monto_descto_moneda * vl_factor_IN, 4);
                        vl_monto_dscto_multa    := Round((vl_monto_descto_moneda - vl_monto_dscto_capital - vl_monto_dscto_interes), 4);

                        IF (vl_monto_dscto_interes < 0)THEN
                            vl_monto_dscto_capital := vl_monto_dscto_capital + (vl_monto_dscto_interes * -1);
                            vl_monto_dscto_interes := 0;
                        END IF;
                            
                        INSERT INTO descuento( cod_descuento
                                                ,fecha_registro
                                                  ,fecha_descuento
                                                  ,cod_usuario
                                                  ,cod_componente_deuda
                                                  ,monto_capital
                                                  ,monto_interes
                                                  ,monto_multa
                                                  ,monto_moneda
                                                  ,monto_pesos
                                                  ,cod_plan_pago
                                                  ,cod_deuda
                                                  ,cod_comprobante
                                                  ,cod_estado_pago
                                                  ,cod_origen_pago
                                                  ,cod_abono)
                                         VALUES(descuento_sq.NEXTVAL
                                                ,sysdate
                                                ,vl_fecha_pago
                                                ,ve_cod_usuario
                                                ,vl_cod_componente_deuda
                                                ,vl_monto_dscto_capital
                                                ,vl_monto_dscto_interes
                                                ,vl_monto_dscto_multa
                                                ,vl_monto_descto_moneda
                                                ,a.monto_descto_pesos
                                                ,vl_cod_plan_pago
                                                ,a.cod_deuda
                                                ,NULL
                                                ,K_ESTADO_PAGO_VIGENTE
                                                ,a.cod_origen_pago 
                                                ,null);

                        SELECT descuento_sq.CURRVAL
                        INTO vl_cod_descuento
                        FROM dual;
                         --si es cruzado calcula descuento para otra univ
                        IF(vl_porc_otras_ues > 0)THEN
                          vl_calc_total_descto          := (a.monto_descto_pesos * 100) / vl_porc_usach;
                          vl_monto_dscto_otras_ues      := vl_calc_total_descto * (vl_porc_otras_ues / 100);
                          vl_monto_dscto_otras_ues_utm  := Round(vl_monto_dscto_otras_ues / vl_valor_moneda, 4);
            
                          vl_monto_dscto_capital_fict := Round(vl_monto_dscto_otras_ues_utm * vl_factor_K, 4);
                          vl_monto_dscto_interes_fict := Round(vl_monto_dscto_otras_ues_utm * vl_factor_IN, 4);
                          vl_monto_dscto_multa_fict := Round((vl_monto_dscto_otras_ues_utm - vl_monto_dscto_capital_fict - vl_monto_dscto_interes_fict), 4);
            
                          IF (vl_monto_dscto_interes_fict < 0)THEN
                            vl_monto_dscto_capital_fict := vl_monto_dscto_capital_fict + (vl_monto_dscto_interes_fict * -1);
                            vl_monto_dscto_interes_fict := 0;
                          END IF;


                          INSERT INTO descuento_ficticio(COD_DESCUENTO_FICTICIO
                                                        ,FECHA_REGISTRO
                                                        ,FECHA_DESCUENTO
                                                        ,COD_USUARIO
                                                        ,MONTO_CAPITAL
                                                        ,MONTO_INTERES
                                                        ,MONTO_MULTA
                                                        ,MONTO_MONEDA
                                                        ,MONTO_PESOS
                                                        ,COD_DEUDA
                                                        ,COD_PLAN_PAGO
                                                        ,COD_DESCUENTO)
                          VALUES(descuento_ficticio_sq.NEXTVAL  -- cod_descuento_ficticio
                                ,sysdate                        -- fecha_registro
                                ,vl_fecha_pago                   -- fecha_descuento
                                ,ve_cod_usuario                 -- cod_usuario
                                ,vl_monto_dscto_capital_fict   -- monto_capital
                                ,vl_monto_dscto_interes_fict    -- monto_interes
                                ,vl_monto_dscto_multa_fict      -- monto_multa
                                ,vl_monto_dscto_otras_ues_utm   -- monto_moneda
                                ,vl_monto_dscto_otras_ues       -- monto_pesos
                                ,a.cod_deuda                    -- cod_deuda
                                ,vl_cod_plan_pago               -- cod_plan_pago
                                ,vl_cod_descuento               -- cod_descuento
                                );

                        END IF;
                        

                        update item_carga_otra_ues set cod_estado_carga = 2
                        where cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;

                        update  carga_otra_ues set total_reg_exitoso = total_reg_exitoso + 1        
                        where cod_carga_otra_ues = ve_cod_carga_otra_ues;

                        insert into BITACORA_CARGA_OTRA_UES (COD_BITACORA_CARGA_OTRA_UES
                                                            ,FECHA_REGISTRO
                                                             ,COD_CARGA_OTRA_UES
                                                             ,COD_ITEM_CARGA_OTRA_UES
                                                             ,COD_USUARIO
                                                             ,OBSERVACION)
                                                       values(bitacora_carga_otra_ues_sq.NEXTVAL
                                                                ,sysdate
                                                                ,ve_cod_carga_otra_ues
                                                                ,a.cod_item_carga_otra_ues
                                                                ,ve_cod_usuario
                                                                ,'Carga descuento de forma exitosa');
                    else
                        update item_carga_otra_ues set cod_estado_carga = 6
                        where cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;
                    end if;--(vl_monto_capital_pesos + vl_monto_interes_pesos) <> 0
                else
                    update item_carga_otra_ues set cod_estado_carga = 6
                    where cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;
                end if;--b.monto_descto_pesos > 0
                --si el monto_pesos es mayor a 0 se hacen los calculos
                if ((a.monto_pesos > 0) and (vl_valor_moneda > 0))then

                    vl_total_pesos_abono := a.monto_pesos;
                    vl_monto_capital  := Round((vl_total_pesos_abono / vl_valor_moneda) , 4);
                    vl_monto_interes  := 0; --La deuda NO_EXIGIBLE no tiene interes
                    vl_monto_multa    := 0; --La deuda NO_EXIGIBLE no tiene multa

                    INSERT INTO abono(cod_abono
                                      ,fecha_registro
                                      ,fecha_abono
                                      ,cod_usuario
                                      ,cod_componente_deuda
                                      ,monto_capital
                                      ,monto_interes
                                      ,monto_multa
                                      ,monto_moneda
                                      ,monto_pesos
                                      ,diferencia_pesos
                                      ,cod_plan_pago
                                      ,cod_deuda
                                      ,cod_abono_desde
                                      ,cod_estado_pago
                                      ,cod_origen_pago
                                      ,cod_sub_boleta
                                      ,cod_documento_pago
                                      ,cod_comprobante
                                      ,fecha_deposito
                                      ,folio_carga)
                                VALUES (abono_sq.NEXTVAL
                                      ,SYSDATE
                                      ,vl_fecha_pago
                                      ,ve_cod_usuario
                                      ,vl_cod_componente_deuda
                                      ,vl_monto_capital
                                      ,vl_monto_interes
                                      ,vl_monto_multa
                                      ,vl_monto_capital + vl_monto_interes + vl_monto_multa
                                      ,vl_total_pesos_abono
                                      ,0
                                      ,vl_cod_plan_pago
                                      ,vl_cod_deuda
                                      ,null
                                      ,K_ESTADO_PAGO_VIGENTE
                                      ,a.cod_origen_pago
                                      ,null
                                      ,null
                                      ,vl_cod_comp_solidario
                                      ,a.fecha_deposito
                                      ,a.folio);
                               
                    SELECT abono_sq.CURRVAL
                    INTO vl_cod_abono
                    FROM dual;

                    ---si el hay porcentaje para la otra universidades se calcula el monto otra ues
                    IF(vl_porc_otras_ues > 0)THEN
                        vl_calc_total_abono       := a.monto_pesos;
                        vl_monto_k_otras_ues      := vl_calc_total_abono * (vl_porc_otras_ues / vl_porc_usach);
                        vl_monto_k_otras_ues_utm  := Round(vl_monto_k_otras_ues / vl_valor_moneda, 4);

                        vl_monto_capital_pesos_z := ROUND(vl_monto_capital * vl_valor_moneda,0);
                        vl_monto_interes_pesos_z := ROUND(vl_monto_interes * vl_valor_moneda,0);
                        vl_monto_multa_pesos_z := ROUND(vl_monto_multa * vl_valor_moneda,0);

                        vl_factor_K  := vl_monto_capital_pesos_z / (vl_monto_capital_pesos_z + vl_monto_interes_pesos_z + vl_monto_multa_pesos_z);
                        vl_factor_IN := vl_monto_interes_pesos_z / (vl_monto_capital_pesos_z + vl_monto_interes_pesos_z + vl_monto_multa_pesos_z);
                        vl_factor_IP := vl_monto_multa_pesos_z / (vl_monto_capital_pesos_z + vl_monto_interes_pesos_z + vl_monto_multa_pesos_z);

                        vl_monto_capital_fict := Round(vl_monto_k_otras_ues_utm * vl_factor_K, 4);
                        vl_monto_interes_fict := Round(vl_monto_k_otras_ues_utm * vl_factor_IN, 4);
                        vl_monto_multa_fict := Round((vl_monto_k_otras_ues_utm - vl_monto_capital_fict - vl_monto_interes_fict), 4);

                        IF (vl_monto_interes_fict < 0)THEN
                            vl_monto_capital_fict := vl_monto_capital_fict + (vl_monto_interes_fict * -1);
                            vl_monto_interes_fict := 0;
                        END IF;

                        INSERT INTO ABONO_FICTICIO (cod_abono_ficticio
                                                    ,fecha_registro
                                                    ,fecha_abono
                                                    ,cod_usuario
                                                    ,monto_capital
                                                    ,monto_interes
                                                    ,monto_multa
                                                    ,monto_moneda
                                                    ,monto_pesos
                                                    ,cod_deuda
                                                    ,cod_plan_pago
                                                    ,cod_abono
                                                    ,fecha_deposito
                                                    ,folio_carga )
                                        VALUES(abono_ficticio_sq.NEXTVAL
                                                    ,SYSDATE
                                                    ,vl_fecha_pago
                                                    ,ve_cod_usuario
                                                    ,vl_monto_capital_fict
                                                    ,vl_monto_interes_fict
                                                    ,vl_monto_multa_fict
                                                    ,vl_monto_k_otras_ues_utm
                                                    ,vl_monto_k_otras_ues
                                                    ,a.cod_deuda
                                                    ,null
                                                    ,vl_cod_abono
                                                    ,vl_fecha_carga
                                                    ,a.folio);
                    END IF; --vl_porc_otras_ues > 0
  
                    update item_carga_otra_ues set cod_estado_carga = 2
                    where cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;

                    update  carga_otra_ues set total_reg_exitoso = total_reg_exitoso + 1    
                    where cod_carga_otra_ues = ve_cod_carga_otra_ues;

                    insert into BITACORA_CARGA_OTRA_UES (COD_BITACORA_CARGA_OTRA_UES
                                                        ,FECHA_REGISTRO
                                                        ,cod_carga_otra_ues
                                                        ,cod_item_carga_otra_ues
                                                        ,COD_USUARIO
                                                        ,OBSERVACION)
                                                values(bitacora_carga_otra_ues_sq.NEXTVAL
                                                    ,sysdate
                                                    ,ve_cod_carga_otra_ues
                                                    ,a.cod_item_carga_otra_ues
                                                    ,ve_cod_usuario
                                                    ,'DEUDA NO_EXIGIBLE, Carga abono directo de forma exitosa');

                    spu_bitacora_deudor ('Carga abono OTRAS UES : ' || TO_CHAR(vl_cod_deuda) , 44, vl_cod_deudor, ve_cod_usuario);

                else
                    update item_carga_otra_ues set cod_estado_carga = 6
                                                    ,cod_comprobante = vl_cod_comp_solidario
                    where cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;
                end if;
                --actualiza el estado del plan_pago
                sp_pp_actualiza_estado(vl_cod_plan_pago);
                sp_deuda_actualiza_estado(a.cod_deuda);
            END IF;    --K_DEUDA_NO_EXIGIBLE
            --si es igual 0 es porque no existe deuda o cuota
            IF((vl_cod_estado_deuda <> K_COBRANZA_NORMAL) and (vl_cod_estado_deuda <> K_DEUDA_CANCELADA) and (vl_cod_estado_deuda <> K_DEUDA_CONDONADA) and (vl_cod_estado_deuda <> K_DEUDA_NO_EXIGIBLE) and (vl_cod_estado_deuda <> 0)) THEN
                update item_carga_otra_ues set cod_estado_carga = 3
                where cod_item_carga_otra_ues = a.cod_item_carga_otra_ues;
            END IF;
            sp_actualiza_item(vl_cod_comp_solidario);
         COMMIT;
        <<end_loop>>
        NULL; -- add NULL statement to avoid error
        END LOOP;

        select count(*)
        into vl_count_tot_reg
        from item_carga_otra_ues
        where cod_carga_otra_ues = ve_cod_carga_otra_ues;

        select count(*)
        into vl_count_tot_exitoso
        from item_carga_otra_ues
        where cod_carga_otra_ues = ve_cod_carga_otra_ues
        and cod_estado_carga in (K_ESTADO_CARGA_EXITOSA,K_ESTADO_SALDO_NEGATIVO);

        select count(*)
        into vl_count_tot_pendiente
        from item_carga_otra_ues
        where cod_carga_otra_ues = ve_cod_carga_otra_ues
        and cod_estado_carga not in (K_ESTADO_CARGA_EXITOSA,K_ESTADO_SALDO_NEGATIVO);

        select sum(monto_pesos)
        into vl_tot_monto_carga
        from item_carga_otra_ues
        where cod_carga_otra_ues = ve_cod_carga_otra_ues;

        select sum(monto_pesos)
        into vl_tot_monto_exitoso
        from item_carga_otra_ues
        where cod_carga_otra_ues = ve_cod_carga_otra_ues
        and cod_estado_carga in (K_ESTADO_CARGA_EXITOSA,K_ESTADO_SALDO_NEGATIVO);

        select sum(monto_pesos)
        into vl_tot_monto_pendiente
        from item_carga_otra_ues
        where cod_carga_otra_ues = ve_cod_carga_otra_ues
        and cod_estado_carga not in (K_ESTADO_CARGA_EXITOSA,K_ESTADO_SALDO_NEGATIVO);


        update  carga_otra_ues set  total_reg_carga = vl_count_tot_reg
                                       ,total_reg_exitoso = vl_count_tot_exitoso
                                       ,total_reg_pdte = vl_count_tot_pendiente
                                       ,monto_reg_carga = vl_tot_monto_carga
                                       ,monto_reg_exitoso = vl_tot_monto_exitoso
                                       ,monto_reg_pdte = vl_tot_monto_pendiente
        where cod_carga_otra_ues = ve_cod_carga_otra_ues;


        vl_count_tot_exit_ejec:= vl_count_tot_reg_ant - vl_count_tot_pendiente;

        update ejecucion_proceso set tot_registro = vl_count_tot_reg_ant
                                   , actual_registro = vl_count_tot_exit_ejec
                                   ,fecha_termino = sysdate
        where cod_ejecucion_proceso= ve_cod_ejecucion_proceso;

        --si no procesa nada se elimina la ejecucion
        if(vl_count_tot_reg_ant = 0)then
            delete ejecucion_proceso where cod_ejecucion_proceso = ve_cod_ejecucion_proceso;
        end if;

        COMMIT;

    END;
END;