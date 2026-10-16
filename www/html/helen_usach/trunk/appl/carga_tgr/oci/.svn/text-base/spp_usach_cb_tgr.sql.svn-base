PROMPT CREATE OR REPLACE PROCEDURE spp_usach_cb_tgr
CREATE OR REPLACE PROCEDURE spp_usach_cb_tgr(ve_cod_ejecucion_proceso	IN  NUMBER  := NULL
										 	,ve_cod_carga_tgr			IN  NUMBER  := NULL
										 	,ve_cod_usuario				IN  NUMBER  := NULL)
AS
BEGIN

    declare vl_current_date                  DATE:=trunc(sysdate);
            vl_nom_comprobante          VARCHAR2(100);
            KL_FAMILIA_SOLIDARIO        NUMBER:=1;
            /*
			 * los comprobantes se creaban con estado aprobado el cual [fabiola.arriagada@usach.cl] enviado el 21/10/2014
			 * en un correo dijo que siempre se deben crear con estado pendiente
			 * jv 22/10/2014
			 * */
            K_COMP_PENDIENTE        NUMBER:=1;
            --K_COMP_CONTABILIZADO        NUMBER:=2;

            K_COMP_INGRESO                NUMBER:=1;
            K_COMP_CARGA_TGR              NUMBER:=34;
            vl_cod_comp_solidario         NUMBER;
            vl_cod_cuenta_bco             NUMBER;
            vl_cod_abono                   NUMBER;
            vl_cod_usuario                NUMBER;
            vl_count_registros            NUMBER;
            vl_i                          NUMBER;
            vl_mensaje                    VARCHAR2(100);
            vc_rut_deudor                 NUMBER;
            vc_monto_retenido             NUMBER;
            vc_fecha_deposito             DATE;
            vl_count_deudor               NUMBER;
            vl_count_repro                NUMBER;
            vl_cod_deudor_repro           NUMBER;
            vl_count_deuda                NUMBER;
            K_ESTADO_PP_MOROSO            NUMBER:=4;
            vl_count_morosa               NUMBER;
            vl_cod_plan_pago              NUMBER;
            vl_cod_deuda                  NUMBER;
            vl_cod_moneda                 NUMBER;
            vl_monto_retenido             NUMBER;
            K_ORIGEN_TESORERIA            NUMBER:=6;
            K_ORIGEN_PAGO_TESORERIA       NUMBER:=2;
            vl_total_abono                NUMBER;
            vl_valor_moneda               NUMBER;
            vl_monto_capital_pesos        NUMBER;
            vl_monto_interes_pesos        NUMBER;
            vl_monto_multa_pesos          NUMBER;
            vl_factor_K                   NUMBER;
            vl_factor_IN                  NUMBER;
            vl_factor_IP                  NUMBER;
            vl_monto_capital              NUMBER;
            vl_monto_interes              NUMBER;
            vl_monto_multa                NUMBER;
            vl_ano_cobro            	  NUMBER;
            K_ESTADO_PP_EMITIDO           NUMBER:=1;
            K_ESTADO_PP_ABONADO           NUMBER:=2;
            KL_ESTADO_DEUDA_NORMAL        NUMBER:=2;
            KL_ESTADO_NO_EXIGIBLE		  NUMBER:=1;
            vl_count_mineduc        	NUMBER;
            vl_porc_uni             	NUMBER;
            vl_monto_real_retencion 	NUMBER;
            vl_monto_retencion_c    	NUMBER;
            KL_RUT_UNIV             	NUMBER:=60911000;
            KL_UNIV_DEFECTO         	NUMBER:=6;

            vl_monto_capital_ret    	NUMBER;
            vl_monto_interes_ret    	NUMBER;
            vl_monto_multa_ret      	NUMBER;
            vl_nro_cuota            	NUMBER;
            vl_monto_cuota          	NUMBER;
            vl_nom_universidad      	VARCHAR2(100);

            vl_monto_capital_c      	NUMBER;
            vl_monto_interes_c      	NUMBER;
            vl_monto_multa_c        	NUMBER;
            vl_monto_retencion_ret  	NUMBER;
            vl_fecha_deposito       	date;
            vl_count_deuda_cnormal		NUMBER;
            vl_count_no_exigible		NUMBER;
            vl_total_pesos_abono		NUMBER;
            vl_cod_deudor				NUMBER;
            K_ESTADO_PAGO_ANULADO		NUMBER:=2;
            vl_count_cancelada			NUMBER;
            vl_excedente_pesos			NUMBER;
            vl_cta_excedente			NUMBER;
            K_CONCEPTO_CUENTA_EXCEDENTE NUMBER:= 19;
            vl_cod_tipo_deuda   		VARCHAR(20);
            vl_cod_analitico			NUMBER;
            vl_count_pp_pendientes		NUMBER;
            vl_count_tot_reg			NUMBER;
            vl_count_tot_exitoso		NUMBER;
            vl_count_tot_pendiente		NUMBER;
            vl_tot_monto_carga			NUMBER;
            vl_tot_monto_exitoso		NUMBER;
            vl_tot_monto_pendiente		NUMBER;
            vl_count_tot_exit_ejec		NUMBER;
            vl_count_tot_reg_ant		NUMBER;
            K_ESTADO_CARGA_EXITOSA		NUMBER:=2;


  CURSOR c_cursor IS
    select cod_item_carga_tgr 
          ,rut rut_deudor
          ,monto_pesos monto_retencion
          ,fecha_pago fecha_deposito
          ,folio folio_carga
          ,fecha_pago fecha_real_pago
    from  item_carga_tgr
	WHERE cod_estado_carga <> 2
	and cod_carga_tgr = ve_cod_carga_tgr;

  CURSOR c_mineduc (vc_rut number) IS
  SELECT ct_porcen_uni
      ,ct_rut_uni_sol
  FROM usach_cb_mineduc
  WHERE ct_rut = vc_rut
    AND ct_rut_uni_sol <> KL_RUT_UNIV;

  CURSOR c_cruzado (vc_cod_deuda number) IS
  SELECT cod_universidad
      ,porc/100 porc
  FROM cruzado
  WHERE cod_deuda = vc_cod_deuda
    AND cod_universidad <> KL_UNIV_DEFECTO;

  BEGIN

    SELECT    sysdate 
	into    vl_fecha_deposito
	FROM    dual;

	/*
	 * los comprobantes se creaban con estado aprobado el cual [fabiola.arriagada@usach.cl] enviado el 21/10/2014
	 * en un correo dijo que siempre se deben crear con estado pendiente
	 * jv 22/10/2014
	 * */
    --crea comprobante para fondo solidario
  	vl_nom_comprobante := 'Comprobante Carga de Ingreso: TESORERIA GENERAL DE LA REPUBLICA, '|| TO_CHAR(vl_fecha_deposito, 'dd/mm/yyyy');
  	--spu_comprobante ('INSERT', NULL, vl_fecha_deposito, vl_cod_usuario, vl_nom_comprobante, KL_FAMILIA_SOLIDARIO, K_COMP_CONTABILIZADO, K_COMP_INGRESO, K_COMP_CARGA_TGR, vl_cod_usuario, vl_fecha_deposito);
  	spu_comprobante ('INSERT', NULL, vl_fecha_deposito, ve_cod_usuario, vl_nom_comprobante, KL_FAMILIA_SOLIDARIO, K_COMP_PENDIENTE, K_COMP_INGRESO, K_COMP_CARGA_TGR, ve_cod_usuario, vl_fecha_deposito);

  	SELECT  comprobante_sq.CURRVAL
    INTO    vl_cod_comp_solidario
    FROM    dual;
    
  	select count(*)
	into vl_count_tot_reg_ant
	from item_carga_tgr
	where cod_carga_tgr	= ve_cod_carga_tgr
	and cod_estado_carga <> K_ESTADO_CARGA_EXITOSA;

  FOR a IN c_cursor LOOP

        -- valida que exista deudor
        SELECT    count(*)
        INTO    vl_count_deudor
        FROM      deudor
        WHERE   rut = a.rut_deudor;

        IF(vl_count_deudor = 0)THEN  --begin vl_count_deudor
            vl_mensaje := 'El rut: '||to_char(a.rut_deudor)||' No EXISTE';
            spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
            
            UPDATE  item_carga_tgr
	        SET     cod_estado_carga = 7
	        WHERE   cod_item_carga_tgr = a.cod_item_carga_tgr;   
	        commit;
            
        ELSE
                --valida que el deudor tenga deuda en familia solidario
                SELECT    count(*)
                INTO    vl_count_deuda
                FROM      deudor d, deuda da, tipo_deuda td
                WHERE      rut = a.rut_deudor
                AND          d.cod_deudor = da.cod_deudor
                AND          da.cod_tipo_deuda = td.cod_tipo_deuda
                AND          td.cod_familia_deuda = KL_FAMILIA_SOLIDARIO;
                
                SELECT    count(*)
                INTO    vl_count_deuda_cnormal
                FROM      deudor d, deuda da, tipo_deuda td
                WHERE      rut = a.rut_deudor
                AND          d.cod_deudor = da.cod_deudor
                AND          da.cod_tipo_deuda = td.cod_tipo_deuda
                AND          td.cod_familia_deuda = KL_FAMILIA_SOLIDARIO
                AND        da.cod_estado_deuda = KL_ESTADO_DEUDA_NORMAL;
				
                
                
                if(vl_count_deuda = 0)THEN --begin vl_count_deuda
                    vl_mensaje := 'El rut: '||to_char(a.rut_deudor)||' No Posee deuda en la Familia Fondo Solidario';
                    spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
                    
                    UPDATE  item_carga_tgr
			        SET     cod_estado_carga = 10
			        WHERE   cod_item_carga_tgr = a.cod_item_carga_tgr;   
			        commit;
                
			    elsif(vl_count_deuda_cnormal = 0)then 
                	-- deuda sin saldo
			        UPDATE  item_carga_tgr
			        SET     cod_estado_carga = 9
			        WHERE   cod_item_carga_tgr = a.cod_item_carga_tgr;   
			        commit;
			        
			        
			        SELECT    count(*)
	                INTO    vl_count_no_exigible
	                FROM    deudor d, deuda da, tipo_deuda td
	                WHERE   rut = a.rut_deudor
	                AND     d.cod_deudor = da.cod_deudor
	                AND     da.cod_tipo_deuda = td.cod_tipo_deuda
	                AND     td.cod_familia_deuda = KL_FAMILIA_SOLIDARIO
	                AND     da.cod_estado_deuda = KL_ESTADO_NO_EXIGIBLE;
	                
	                --cualquier otro estado se considera CANCELADA
	                SELECT    count(*)
	                INTO    vl_count_cancelada
	                FROM    deudor d, deuda da, tipo_deuda td
	                WHERE   rut = a.rut_deudor
	                AND     d.cod_deudor = da.cod_deudor
	                AND     da.cod_tipo_deuda = td.cod_tipo_deuda
	                AND     td.cod_familia_deuda = KL_FAMILIA_SOLIDARIO
	                AND     da.cod_estado_deuda not in (KL_ESTADO_NO_EXIGIBLE, KL_ESTADO_DEUDA_NORMAL);
			        
			        
			        --abono directo
			        if(vl_count_no_exigible > 0)then
			        
			        	SELECT max(da.cod_deuda)
			        		  ,cod_moneda
                        INTO   vl_cod_deuda
                        	  ,vl_cod_moneda
	                    FROM    deuda da, deudor dr, tipo_deuda td
	                    WHERE   da.cod_deudor = dr.cod_deudor
	                    AND     da.cod_tipo_deuda = td.cod_tipo_deuda
	                    AND     rut = a.rut_deudor
	                    AND     cod_familia_deuda = KL_FAMILIA_SOLIDARIO
	                    AND     da.cod_estado_deuda = KL_ESTADO_NO_EXIGIBLE
	                    GROUP BY cod_moneda;
	                    
	                    
	                    vl_valor_moneda  := f_mon_get_valor_moneda(vl_cod_moneda, a.fecha_real_pago);
					
						if((vl_valor_moneda is null) or (vl_valor_moneda = 0))then
						
							UPDATE  item_carga_tgr set cod_estado_carga = 4
					        WHERE   cod_item_carga_tgr = a.cod_item_carga_tgr; 
					    end if;
				
						if ((a.monto_retencion > 0) and (vl_valor_moneda > 0))then
						
						   vl_total_pesos_abono := a.monto_retencion;
						   vl_monto_capital  := Round((vl_total_pesos_abono / vl_valor_moneda) , 4);
				           vl_monto_interes  := Round((vl_total_pesos_abono / vl_valor_moneda) , 4);
				           vl_monto_multa    := 0; --La deuda NO_EXIGIBLE no tiene multa
						   
						    spu_abono_carga(a.fecha_real_pago          -- fecha_abono
		                                    ,ve_cod_usuario          -- cod_usuario
		                                    ,vl_monto_capital        -- monto_capital
		                                    ,vl_monto_interes        -- monto_interes
		                                    ,vl_monto_multa          -- monto_multa
		                                    ,vl_total_pesos_abono      -- monto_pesos
		                                    ,0                         -- diferencia_pesos
		                                    ,null        			   -- cod_plan_pago
		                                    ,vl_cod_deuda              -- cod_deuda
		                                    ,null                      -- cod_abono_desde
		                                    ,K_ORIGEN_PAGO_TESORERIA    -- cod_origen_pago
		                                    ,null                       -- cod_sub_boleta
		                                    ,null                       -- cod_documento_pago
		                                    ,vl_cod_comp_solidario 		-- cod_comprobante
		                                    ,vl_cod_abono );
							
		                    vl_cod_cuenta_bco := 11102; --BANCO DE CHILE

                            spu_item_comprobante('INSERT', NULL, vl_cod_comp_solidario, vl_cod_cuenta_bco,  vl_total_pesos_abono, 0);
                            spu_analitico('INSERT', NULL, vl_cod_comp_solidario, vl_cod_deuda, vl_cod_cuenta_bco, vl_total_pesos_abono, 0);
                            sp_comp_ing(vl_cod_comp_solidario, vl_cod_abono);                
		                                    
				            UPDATE  item_carga_tgr set cod_estado_carga = 2
						    WHERE   cod_item_carga_tgr = a.cod_item_carga_tgr;  
						    commit;
						  	
						  	
						  	insert into BITACORA_CARGA_TGR (COD_BITACORA_CARGA_TGR
						  									 ,FECHA_REGISTRO
															 ,cod_carga_tgr       
															 ,cod_item_carga_tgr 
															 ,COD_USUARIO                 
														     ,OBSERVACION)
													   values(bitacora_carga_tgr_sq.NEXTVAL
													   		 ,sysdate
													   		 ,ve_cod_carga_tgr
													   		 ,a.cod_item_carga_tgr
													   		 ,ve_cod_usuario
													   		 ,'DEUDA NO_EXIGIBLE, Carga abono directo de forma exitosa');
				            
				            select cod_deudor 
							into vl_cod_deudor
							from deudor 
							where rut = a.rut_deudor;
						
							spu_bitacora_deudor ('Carga abono TGR : ' || TO_CHAR(vl_cod_deuda) , 45, vl_cod_deudor, ve_cod_usuario);
													   		 
				        else
						
							update item_carga_tgr set cod_estado_carga = 6
			              		  ,cod_comprobante = vl_cod_comp_solidario
						  	where cod_item_carga_tgr = a.cod_item_carga_tgr; 
						 
						end if;   
	                end if; --vl_count_no_exigible = 0
			        
			        --abono anulado y excedente
			        if(vl_count_no_exigible = 0 and vl_count_cancelada > 0)then
			        
			        	SELECT  max(da.cod_deuda)
			        	       ,cod_moneda
                        INTO    vl_cod_deuda
                               ,vl_cod_moneda
	                    FROM    deuda da, deudor dr, tipo_deuda td
	                    WHERE   da.cod_deudor = dr.cod_deudor
	                    AND     da.cod_tipo_deuda = td.cod_tipo_deuda
	                    AND     rut = a.rut_deudor
	                    AND     cod_familia_deuda = KL_FAMILIA_SOLIDARIO
	                    AND     da.cod_estado_deuda not in (KL_ESTADO_NO_EXIGIBLE, KL_ESTADO_DEUDA_NORMAL)
	                    GROUP BY cod_moneda;
	                    
	                    SELECT nvl(max(cod_plan_pago),null)
                        INTO  vl_cod_plan_pago
	                    FROM  plan_pago
	                    where cod_deuda= vl_cod_deuda;
	                    
	                    vl_valor_moneda  := f_mon_get_valor_moneda(vl_cod_moneda, a.fecha_real_pago);
	                    
	                    if ((a.monto_retencion > 0) and (vl_valor_moneda > 0))then
							vl_monto_capital  := Round((a.monto_retencion / vl_valor_moneda) , 4);
						else
							vl_monto_capital := 0;
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
									,a.fecha_real_pago
									,ve_cod_usuario
									,5 --sin componente
									,vl_monto_capital
									,0
									,0
									,vl_monto_capital
									,a.monto_retencion 
									,0
									,vl_cod_plan_pago
									,vl_cod_deuda
									,K_ESTADO_PAGO_ANULADO
									,K_ORIGEN_PAGO_TESORERIA
									,vl_cod_comp_solidario
									,null
									,ve_cod_carga_tgr);
				
						SELECT abono_sq.CURRVAL
						INTO vl_cod_abono
						FROM dual;
						
						vl_excedente_pesos := a.monto_retencion;
						
						select count(*) 
						into vl_count_deudor
						from deudor 
						where rut = a.rut_deudor;
					
						if(vl_count_deudor > 0)then
						
							select cod_deudor 
							into vl_cod_deudor
							from deudor 
							where rut = a.rut_deudor;
						
							spu_excedente('INSERT', null, sysdate, vl_cod_deudor, vl_excedente_pesos , 1, null, ve_cod_usuario, vl_cod_comp_solidario, vl_cod_abono, null, null);
							
							--DEBE
							vl_cod_cuenta_bco := 11102; --BANCO DE CHILE
							spu_item_comprobante ('INSERT', NULL, vl_cod_comp_solidario, vl_cod_cuenta_bco,  vl_excedente_pesos, 0);
					        spu_analitico ('INSERT', NULL, vl_cod_comp_solidario, vl_cod_deuda, vl_cod_cuenta_bco, vl_excedente_pesos, 0);
					        
					        select cod_tipo_deuda
					        into vl_cod_tipo_deuda
					        from deuda
					        where cod_deuda = vl_cod_deuda;
							
							--HABER
							vl_cta_excedente:= f_comp_get_cuenta_tipo_deuda(vl_cod_tipo_deuda, K_CONCEPTO_CUENTA_EXCEDENTE);
							spu_item_comprobante ('INSERT', NULL, vl_cod_comp_solidario, vl_cta_excedente,  0, vl_excedente_pesos);
					        spu_analitico ('INSERT', NULL, vl_cod_comp_solidario, vl_cod_deuda, vl_cta_excedente, 0, vl_excedente_pesos);
					
							SELECT  analitico_comprobante_sq.CURRVAL
							INTO vl_cod_analitico
							FROM dual;
					
					        update analitico_comprobante
							SET cod_deudor = vl_cod_deudor
							where cod_analitico_comprobante = vl_cod_analitico;
							
							
							UPDATE  item_carga_tgr set cod_estado_carga = 2
						    WHERE   cod_item_carga_tgr = a.cod_item_carga_tgr;  
						    commit;	
						    
						    insert into BITACORA_CARGA_TGR (COD_BITACORA_CARGA_TGR
						  									 ,FECHA_REGISTRO
															 ,cod_carga_tgr       
															 ,cod_item_carga_tgr 
															 ,COD_USUARIO                 
														     ,OBSERVACION)
													   values(bitacora_carga_tgr_sq.NEXTVAL
													   		 ,sysdate
													   		 ,ve_cod_carga_tgr
													   		 ,a.cod_item_carga_tgr
													   		 ,ve_cod_usuario
													   		 ,'Deuda CANCELADA, carga abono anulado y excedente de forma exitosa');
							
						
							spu_bitacora_deudor ('Carga abono TGR : ' || TO_CHAR(vl_cod_deuda) , 45, vl_cod_deudor, ve_cod_usuario);
							
						end if;
	                end if; --vl_count_no_exigible = 0 and vl_count_cancelada > 0
			        
			        
                ELSE
                
                	
                	SELECT  count(*)
                    INTO    vl_ano_cobro
                    FROM    plan_pago p, deuda da, deudor dr, tipo_deuda td
                    WHERE   p.cod_deuda = da.cod_deuda
                    AND     da.cod_deudor = dr.cod_deudor
                    AND     rut = a.rut_deudor
                    AND     cod_estado_plan_pago in(K_ESTADO_PP_EMITIDO, K_ESTADO_PP_ABONADO ,K_ESTADO_PP_MOROSO)
                    AND     td.cod_tipo_deuda = da.cod_tipo_deuda
                    AND     cod_familia_deuda = KL_FAMILIA_SOLIDARIO
                    AND          f_pp_saldo_plan_pago(cod_plan_pago, sysdate) > 0
                    AND        da.cod_estado_deuda = KL_ESTADO_DEUDA_NORMAL;
                    
                    if(vl_ano_cobro = 0)then 
                    	-- deuda sin saldo
				        UPDATE  item_carga_tgr
				        SET     cod_estado_carga = 9
				        WHERE   cod_item_carga_tgr = a.cod_item_carga_tgr;   
				        commit;
                    end if; 
                
                    SELECT  min(ano_cobro)
                    INTO    vl_ano_cobro
                    FROM    plan_pago p, deuda da, deudor dr, tipo_deuda td
                    WHERE   p.cod_deuda = da.cod_deuda
                    AND     da.cod_deudor = dr.cod_deudor
                    AND     rut = a.rut_deudor
                    AND     cod_estado_plan_pago in(K_ESTADO_PP_EMITIDO, K_ESTADO_PP_ABONADO ,K_ESTADO_PP_MOROSO)
                    AND     td.cod_tipo_deuda = da.cod_tipo_deuda
                    AND     cod_familia_deuda = KL_FAMILIA_SOLIDARIO
                    AND          f_pp_saldo_plan_pago(cod_plan_pago, sysdate) > 0
                    AND        da.cod_estado_deuda = KL_ESTADO_DEUDA_NORMAL;
					
                    --dbms_output.put_line( a.rut_deudor);
                    
                    SELECT  min(cod_plan_pago)
                          ,min (da.cod_deuda)
                          ,cod_moneda
                    INTO    vl_cod_plan_pago
                            ,vl_cod_deuda
                            ,vl_cod_moneda
                    FROM    deuda da, deudor dr, plan_pago p, tipo_deuda td
                    WHERE   da.cod_deudor = dr.cod_deudor
                    AND     da.cod_deuda = p.cod_deuda
                    AND     da.cod_tipo_deuda = td.cod_tipo_deuda
                    AND     rut = a.rut_deudor
                    AND     cod_estado_plan_pago in(K_ESTADO_PP_EMITIDO, K_ESTADO_PP_ABONADO ,K_ESTADO_PP_MOROSO)
                    AND     ano_cobro = vl_ano_cobro
                    AND     cod_familia_deuda = KL_FAMILIA_SOLIDARIO
                    AND        da.cod_estado_deuda = KL_ESTADO_DEUDA_NORMAL
                    GROUP BY cod_moneda;


                    IF(a.monto_retencion > 0)THEN --begin a.monto_retencion
                        SELECT Count(*)
                        INTO vl_count_mineduc
                        FROM USACH_CB_MINEDUC
                        WHERE CT_RUT = a.rut_deudor;

                        IF (vl_count_mineduc > 0) THEN --begin el deudor es cruzado mineduc
                            --obtiene porc correspondiente usach
                            SELECT CT_PORCEN_UNI
                            INTO vl_porc_uni
                            FROM USACH_CB_MINEDUC
                            WHERE CT_RUT_UNI_SOL = KL_RUT_UNIV
                            AND CT_RUT = a.rut_deudor;

                            vl_monto_real_retencion := Round(a.monto_retencion * vl_porc_uni, 0);
                        ELSE
                            vl_monto_real_retencion := a.monto_retencion;
                        END IF; --end el deudor es cruzado mineduc

                        vl_valor_moneda         := f_mon_get_valor_moneda(vl_cod_moneda, a.fecha_real_pago);
                        vl_monto_capital_pesos  := round(f_pp_saldo_capital_caja(vl_cod_plan_pago, a.fecha_real_pago) * vl_valor_moneda, 0);
                        vl_monto_interes_pesos  := round(f_pp_saldo_interes_normal_caja(vl_cod_plan_pago, a.fecha_real_pago) * vl_valor_moneda, 0);
                        vl_monto_multa_pesos    := round(f_pp_saldo_multa_caja(vl_cod_plan_pago, a.fecha_real_pago) * vl_valor_moneda, 0);
                        --vl_monto_moroso :=     vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos;

                        IF ((vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos) <> 0) THEN  --begin vl_monto_capital_pesos
                            -- porporcion K, IN, IP
                            vl_factor_K  := vl_monto_capital_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
                            vl_factor_IN := vl_monto_interes_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
                            vl_factor_IP := vl_monto_multa_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);

                            vl_monto_capital    := Round((vl_monto_real_retencion / vl_valor_moneda) * vl_factor_K, 4);--Round((vl_total_abono / vl_valor_moneda) * vl_factor_K, 4);
                            vl_monto_interes    := Round((vl_monto_real_retencion / vl_valor_moneda) * vl_factor_IN, 4);--Round((vl_total_abono / vl_valor_moneda) * vl_factor_IN, 4);
                            vl_monto_multa        := Round((vl_monto_real_retencion / vl_valor_moneda) * vl_factor_IP, 4);--Round((vl_total_abono / vl_valor_moneda) * vl_factor_IP, 4);

                            -- si no cuadra con el total_moneda que se esta pagando, se agrega la diferencia al abono K
                            IF ((vl_monto_capital + vl_monto_interes + vl_monto_multa) <> round(vl_monto_real_retencion / vl_valor_moneda,4)) THEN
                                vl_monto_capital := vl_monto_capital + (round(vl_monto_real_retencion / vl_valor_moneda,4) - (vl_monto_capital + vl_monto_interes + vl_monto_multa));
                            END IF;
                             
                             spu_abono_carga(a.fecha_real_pago          -- fecha_abono
		                                    ,ve_cod_usuario          -- cod_usuario
		                                    ,vl_monto_capital        -- monto_capital
		                                    ,vl_monto_interes        -- monto_interes
		                                    ,vl_monto_multa          -- monto_multa
		                                    ,vl_monto_real_retencion  -- monto_pesos
		                                    ,0                              -- diferencia_pesos
		                                    ,vl_cod_plan_pago        -- cod_plan_pago
		                                    ,vl_cod_deuda              -- cod_deuda
		                                    ,null                          -- cod_abono_desde
		                                    ,K_ORIGEN_PAGO_TESORERIA    -- cod_origen_pago
		                                    ,null                          -- cod_sub_boleta
		                                    ,null                          -- cod_documento_pago
		                                    ,vl_cod_comp_solidario -- cod_comprobante
		                                    ,vl_cod_abono );
                                    
                                
		                        select count(*)
					            into vl_count_pp_pendientes
					            from plan_pago
					            where cod_deuda = vl_cod_deuda
					            and cod_estado_plan_pago not in(3, 6);
					            
					            if(vl_count_pp_pendientes = 0)then
					            	update deuda set cod_estado_deuda = 4
					            	where cod_deuda = vl_cod_deuda;
					            end if;
				            
	                            -- cuando entra al loop actualiza la tabla, y se marca procesado con 'S' para efecto de historial
						        UPDATE  item_carga_tgr
						        SET     cod_estado_carga = 2
						        WHERE   cod_item_carga_tgr = a.cod_item_carga_tgr;  
						        commit;
						        
						        insert into BITACORA_CARGA_TGR (COD_BITACORA_CARGA_TGR
						  									 ,FECHA_REGISTRO
															 ,cod_carga_tgr       
															 ,cod_item_carga_tgr 
															 ,COD_USUARIO                 
														     ,OBSERVACION)
													   values(bitacora_carga_tgr_sq.NEXTVAL
													   		 ,sysdate
													   		 ,ve_cod_carga_tgr
													   		 ,a.cod_item_carga_tgr
													   		 ,ve_cod_usuario
													   		 ,'DEUDA COBRANZA NORMAL, Carga abono de forma exitosa');
						        

                            ---solo cruzado normal
                            FOR c IN c_cruzado (vl_cod_deuda) LOOP --begin c_cruzado
                                vl_monto_retencion_ret    := Round(vl_monto_real_retencion * c.porc, 0);
                                vl_monto_capital_ret    := Round((vl_monto_retencion_ret / vl_valor_moneda) * vl_factor_K, 4);
                                vl_monto_interes_ret    := Round((vl_monto_retencion_ret / vl_valor_moneda) * vl_factor_IN, 4);
                                vl_monto_multa_ret        := Round((vl_monto_retencion_ret / vl_valor_moneda) * vl_factor_IP, 4);
                                vl_monto_capital_c        := Round((a.monto_retencion / vl_valor_moneda) * vl_factor_K, 4);
                                vl_monto_interes_c        := Round((a.monto_retencion / vl_valor_moneda) * vl_factor_IN, 4);
                                vl_monto_multa_c        := Round((a.monto_retencion / vl_valor_moneda) * vl_factor_IP, 4);

                                SELECT     nro_cuota
                                        ,monto_capital + monto_interes
                                INTO     vl_nro_cuota
                                        ,vl_monto_cuota
                                FROM    plan_pago
                                WHERE    cod_plan_pago = vl_cod_plan_pago;

                                SELECT nom_universidad
                                INTO vl_nom_universidad
                                FROM universidad
                                WHERE cod_universidad = c.cod_universidad;

                                INSERT INTO RENDICION_OTRAS_UES
                                (RUT            ,ANO_COBRO            , NRO_CUOTA            ,MONTO_CUOTA        ,FECHA_PAGO
                                ,CAPITAL_UTM    ,CAPITAL_PESOS        ,INTERES_UTM        ,INTERES_PESOS        ,TOTAL_UTM
                                ,TOTAL_PESOS    ,DSCTO_TOTAL_UTM    ,DSCTO_TOTAL_PESOS    ,NOMBRE_OTRA_UNIV    ,PORC_OTRA_UNIV
                                ,CAPITAL_UTM_OTRA_UNIV        ,CAPITAL_PESOS_OTRA_UNIV    ,INTERES_UTM_OTRA_UNIV    ,INTERES_PESOS_OTRA_UNIV
                                ,DSCTO_UTM_OTRA_UNIV        ,DSCTO_PESOS_OTRA_UNIV        ,VALOR_UTM                ,FORMA_PAGO)
                                        VALUES    (a.rut_deudor
                                                ,vl_ano_cobro
                                                ,vl_nro_cuota
                                                ,vl_monto_cuota
                                                ,a.fecha_real_pago
                                                ,vl_monto_capital_c+vl_monto_interes_c
                                                ,Round((vl_monto_capital_c+vl_monto_interes_c)*vl_valor_moneda, 0)
                                                ,vl_monto_multa_c
                                                ,Round(vl_monto_multa_c*vl_valor_moneda)
                                                ,Round(a.monto_retencion/vl_valor_moneda, 4)
                                                ,a.monto_retencion
                                                ,0
                                                ,0
                                                ,vl_nom_universidad --nombre_otra_univ
                                                ,c.porc
                                                ,(vl_monto_capital_ret+vl_monto_interes_ret)
                                                ,Round((vl_monto_capital_ret+vl_monto_interes_ret)*vl_valor_moneda, 0)
                                                ,vl_monto_multa_ret
                                                ,Round(vl_monto_multa_ret*vl_valor_moneda, 0)
                                                ,0
                                                ,0
                                                ,vl_valor_moneda
                                                ,'TGR');
                            END LOOP; --end c_cruzado

                            FOR b IN c_mineduc (a.rut_deudor) LOOP --begin c_mineduc
                                vl_monto_capital_ret    := Round((a.monto_retencion / vl_valor_moneda) * vl_factor_K, 4);--Round((vl_total_abono / vl_valor_moneda) * vl_factor_K, 4);
                                vl_monto_interes_ret    := Round((a.monto_retencion / vl_valor_moneda) * vl_factor_IN, 4);--Round((vl_total_abono / vl_valor_moneda) * vl_factor_IN, 4);
                                vl_monto_multa_ret        := Round((a.monto_retencion / vl_valor_moneda) * vl_factor_IP, 4);--Round((vl_total_abono / vl_valor_moneda) * vl_factor_IP, 4);

                                SELECT     nro_cuota
                                        ,monto_capital + monto_interes
                                INTO     vl_nro_cuota
                                        ,vl_monto_cuota
                                FROM plan_pago
                                WHERE cod_plan_pago = vl_cod_plan_pago;

                                SELECT nom_universidad
                                INTO vl_nom_universidad
                                FROM universidad
                                WHERE rut_universidad = b.ct_rut_uni_sol;

                                INSERT INTO RENDICION_OTRAS_UES
                                (RUT            ,ANO_COBRO            , NRO_CUOTA            ,MONTO_CUOTA        ,FECHA_PAGO
                                ,CAPITAL_UTM    ,CAPITAL_PESOS        ,INTERES_UTM        ,INTERES_PESOS        ,TOTAL_UTM
                                ,TOTAL_PESOS    ,DSCTO_TOTAL_UTM    ,DSCTO_TOTAL_PESOS    ,NOMBRE_OTRA_UNIV    ,PORC_OTRA_UNIV
                                ,CAPITAL_UTM_OTRA_UNIV        ,CAPITAL_PESOS_OTRA_UNIV    ,INTERES_UTM_OTRA_UNIV    ,INTERES_PESOS_OTRA_UNIV
                                ,DSCTO_UTM_OTRA_UNIV        ,DSCTO_PESOS_OTRA_UNIV        ,VALOR_UTM                ,FORMA_PAGO)
                                    VALUES    (a.rut_deudor
                                            ,vl_ano_cobro
                                            ,vl_nro_cuota
                                            ,vl_monto_cuota
                                            ,a.fecha_real_pago
                                            ,vl_monto_capital_ret+vl_monto_interes_ret
                                            ,Round((vl_monto_capital_ret+vl_monto_interes_ret)*vl_valor_moneda, 0)
                                            ,vl_monto_multa_ret
                                            ,Round(vl_monto_multa_ret*vl_valor_moneda)
                                            ,Round(a.monto_retencion/vl_valor_moneda, 4)
                                            ,a.monto_retencion
                                            ,0
                                            ,0
                                            ,vl_nom_universidad --nombre_otra_univ
                                            ,b.ct_porcen_uni*100
                                            ,(vl_monto_capital_ret+vl_monto_interes_ret) * b.ct_porcen_uni
                                            ,Round((vl_monto_capital_ret+vl_monto_interes_ret)*vl_valor_moneda*b.ct_porcen_uni, 0)
                                            ,vl_monto_multa_ret*b.ct_porcen_uni
                                            ,Round(vl_monto_multa_ret*vl_valor_moneda*b.ct_porcen_uni, 0)
                                            ,0
                                            ,0
                                            ,vl_valor_moneda
                                            ,'TGR');


                            END LOOP; --end c_mineduc

                            -- Se Obtiene la cuenta contable del banco
                            /*SELECT    cc.cod_cuenta_contable
                            INTO    vl_cod_cuenta_bco
                            FROM      origen_carga_banco ocb, cuenta_corriente cc
                            WHERE      ocb.cod_origen_carga_banco = K_ORIGEN_TESORERIA
                            AND      cc.cod_cuenta_corriente = ocb.cod_cuenta_corriente;*/
                            vl_cod_cuenta_bco := 11102; --BANCO DE CHILE

                            spu_item_comprobante('INSERT', NULL, vl_cod_comp_solidario, vl_cod_cuenta_bco,  vl_monto_real_retencion, 0);
                            spu_analitico('INSERT', NULL, vl_cod_comp_solidario, vl_cod_deuda, vl_cod_cuenta_bco, vl_monto_real_retencion, 0);
                            sp_comp_ing(vl_cod_comp_solidario, vl_cod_abono);

                            --se debe hacer update a todos los abonos que se crean
                            UPDATE     abono
                            SET         fecha_deposito = a.fecha_deposito
                                    ,folio_carga = a.folio_carga
                            WHERE    fecha_abono = a.fecha_real_pago
                            AND     cod_origen_pago = K_ORIGEN_PAGO_TESORERIA
                            AND     cod_deuda = vl_cod_deuda
                            AND     cod_deuda IN(SELECT cod_deuda
                                                FROM deuda
                                                WHERE cod_deudor IN (SELECT cod_deudor
                                                                    FROM  deudor
                                                                    WHERE rut = a.rut_deudor));



                           -- cuando entra al loop actualiza la tabla, y se marca procesado con 'S' para efecto de historial
					        UPDATE  item_carga_tgr
					        SET     cod_estado_carga = 2
					        WHERE   cod_item_carga_tgr = a.cod_item_carga_tgr;   
					        
					        commit;
 
                        END IF; --end vl_monto_capital_pesos
                    END IF; --end a.monto_retencion
                END IF; --end vl_count_deuda
            END IF; ----begin vl_count_deudor

     END LOOP;
     
     
     	select count(*)
		into vl_count_tot_reg
		from item_carga_tgr
		where cod_carga_tgr = ve_cod_carga_tgr;
		
		select count(*)
		into vl_count_tot_exitoso
		from item_carga_tgr
		where cod_carga_tgr = ve_cod_carga_tgr
		and cod_estado_carga = 2;
		
		select count(*)
		into vl_count_tot_pendiente
		from item_carga_tgr
		where cod_carga_tgr = ve_cod_carga_tgr
		and cod_estado_carga <> 2;
		
		select sum(monto_pesos) 
		into vl_tot_monto_carga
		from item_carga_tgr
		where cod_carga_tgr = ve_cod_carga_tgr;
		
		select sum(monto_pesos) 
		into vl_tot_monto_exitoso
		from item_carga_tgr
		where cod_carga_tgr = ve_cod_carga_tgr
		and cod_estado_carga = 2;
		
		select sum(monto_pesos) 
		into vl_tot_monto_pendiente
		from item_carga_tgr
		where cod_carga_tgr = ve_cod_carga_tgr
		and cod_estado_carga <> 2;
		
		
		update  carga_tgr set  total_reg_carga = vl_count_tot_reg
							   ,total_reg_exitoso = vl_count_tot_exitoso
							   ,total_reg_pdte = vl_count_tot_pendiente
							   ,monto_reg_carga = vl_tot_monto_carga
							   ,monto_reg_exitoso = vl_tot_monto_exitoso
							   ,monto_reg_pdte = vl_tot_monto_pendiente
		where cod_carga_tgr = ve_cod_carga_tgr;
		
		
		vl_count_tot_exit_ejec:= vl_count_tot_reg_ant - vl_count_tot_pendiente;
		
		update ejecucion_proceso set tot_registro = vl_count_tot_reg_ant
								   , actual_registro = vl_count_tot_exit_ejec
		where cod_ejecucion_proceso= ve_cod_ejecucion_proceso;
     
     
		sp_actualiza_item(vl_cod_comp_solidario);

    END;
END;
/