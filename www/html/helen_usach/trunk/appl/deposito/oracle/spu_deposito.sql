PROMPT CREATE OR REPLACE PROCEDURE spu_deposito
CREATE OR REPLACE PROCEDURE spu_deposito (ve_operacion					VARCHAR2
				                          , ve_cod_deposito				NUMBER
				                          , ve_nro_deposito				NUMBER:= NULL
				                          , ve_fecha_deposito			DATE:= NULL
				                          , ve_cod_usuario				NUMBER:= NULL
				                          , ve_cod_cuenta_corriente		NUMBER:= NULL
				                          , ve_cod_estado_deposito		NUMBER:= NULL
				                          , ve_cod_comprobante			NUMBER:= NULL
				                          , ve_cod_comprobante_anula	NUMBER:= NULL
				                          , ve_cod_familia_deuda		NUMBER:= NULL
				                          , ve_cod_usuario_confirma		NUMBER:= NULL
				                          , ve_fecha_confirma			DATE:= NULL
				                          , ve_cod_usuario_anula		NUMBER:= NULL
				                          , ve_fecha_anula				DATE:= NULL
				                          , ve_motivo_anula				VARCHAR2:= NULL)


AS
/******************************************************************************
   NAME:       spu_deposito
   PURPOSE:    permite crear, actualizar depositos, ademas dependiendo del estado
                se podra crear un comprobante, item_comprobante, analitico, como tambien
                se puede anular un deposito.

   REVISIONS:
   Ver        Date        Author              Description
   ---------  ----------  ---------------     ------------------------------------
   1.0        24/10/2009   Cristian Sánchez

   NOTES:
******************************************************************************/

BEGIN
  DECLARE K_ABONO_VIGENTE			        NUMBER:= 1;
			    K_POR_DEPOSITAR             NUMBER:= 1;
			    K_DEPOSITADO                NUMBER:= 2;

			    vl_fecha_registro           DATE;
			    ve_fecha_confirma2          DATE;
			    vl_cod_estado_deposito      NUMBER;
			    kl_ingresado                NUMBER:=1;
			    kl_confirmado               NUMBER:=2;
			    kl_anulado                  NUMBER:=3;
			    vl_cod_comprobante          NUMBER;
			    vl_cta_cheque_por_cobrar    NUMBER;
			    vl_cod_comprobante_anula    NUMBER;
			    vl_cod_usuario_anula        NUMBER;
			    KL_EST_COMP_APROBADO        NUMBER:=1;
			    KL_TIPO_COMP_TRASPASO       NUMBER:=3;
			    KL_ORIGEN_COMP_DEPO         NUMBER:=7;
			    vl_cod_documento_pago       NUMBER;
			    vl_cod_comprobante_abono    NUMBER;
			    vl_cod_abono                NUMBER;
			    vl_count                    NUMBER;
			    vl_cod_cuenta_caja          NUMBER;
			    vl_nom_comprobante			VARCHAR2(100);
			    vl_cod_cuenta_contable		NUMBER;
			    vc_cod_documento_pago		NUMBER;
			    vc_monto					NUMBER;
			    vl_cuenta_ingreso				NUMBER;
			    vl_cod_analitico_comprobante	NUMBER;
          		K_PARAM_TIPO_UNIVERSIDAD	NUMBER:=27;
          		vl_tipo_universidad         VARCHAR2(100);
          		vl_fecha_abono				DATE;
          		vl_valor_moneda				NUMBER;
          		vl_monto_abono				NUMBER;
          		vl_monto_capital_pesos		NUMBER;
	        	vl_monto_interes_pesos		NUMBER;
	        	vl_monto_multa_pesos		NUMBER;
	        	vl_factor_K					NUMBER;
	        	vl_factor_IN				NUMBER;
	        	vl_factor_IP				NUMBER;
          		vl_monto_capital			NUMBER;
		      	vl_monto_interes			NUMBER;
		      	vl_monto_multa				NUMBER;
	          	K_ORIGEN_PAGO_CAJA			NUMBER:= 1;
	          	vl_cod_cuenta_ingreso		NUMBER;
	         	vl_cod_analitico			NUMBER;
	          	vl_cod_comprobante_ch_fecha NUMBER;
	          	KL_TIPO_CH_FECHA            NUMBER:=5;
	          	vl_cod_comp_resp_ch_fecha 	NUMBER;
	          	vl_cod_cuenta_resp_ch_fecha NUMBER;
	          	KL_TIPO_EFECTIVO            NUMBER:=4;
	          	vl_cod_cuenta_contable_caja NUMBER;
	          	vl_cod_usuario              NUMBER;
			  	vl_cod_familia_deuda        NUMBER;
					
		  		vl_finaliza					VARCHAR2(1):= 'N';
		  		K_CHEQUE_FECHA				NUMBER:=5;
		  		vl_monto_pesos				NUMBER;
		  		vl_tipo_documento_pago		NUMBER;
		  		vl_max_cod_abono			NUMBER;
		  		vl_cod_usuario_confirma		NUMBER;
		  		vl_count_item				NUMBER;
		  		vl_cod_item_ingreso			NUMBER;


	CURSOR C_DOC_PAGO IS
  SELECT	DP.COD_DOCUMENTO_PAGO
			    ,MONTO
			    ,COD_DEUDOR
          ,COD_TIPO_DOCUMENTO_PAGO
	FROM	  DEPOSITO_DOCUMENTO DD, DOCUMENTO_PAGO DP
	WHERE	  DD.COD_DOCUMENTO_PAGO = DP.COD_DOCUMENTO_PAGO
	AND		  DD.COD_DEPOSITO = ve_cod_deposito;

  CURSOR C_DOC_ASIGNADO (vc_cod_documento_pago number) IS
	SELECT  cod_monto_doc_asignado
				,mda.cod_item_ingreso
				,cod_documento_pago
				,monto_asignado
        ,cod_moneda
        ,cod_plan_pago
        ,d.cod_deuda
        ,saldo_total_pesos
        ,cod_ingreso
	FROM    monto_doc_asignado mda, item_ingreso it, deuda d, tipo_deuda td
	WHERE   cod_documento_pago = vc_cod_documento_pago
        AND it.cod_item_ingreso = mda.cod_item_ingreso
        AND d.cod_deuda = it.cod_deuda
        AND td.cod_tipo_deuda = d.cod_tipo_deuda
	ORDER BY   cod_monto_doc_asignado ASC;
	
	
	CURSOR C_ABONO  (vc_cod_deuda number, vc_max_cod_abono number, vc_cod_abono number) IS 
		select cod_abono
			  ,cod_plan_pago
			  ,monto_pesos
		from abono
		where cod_deuda = vc_cod_deuda
		and cod_origen_pago = K_ORIGEN_PAGO_CAJA
		and cod_abono > vc_max_cod_abono;

	BEGIN
		IF (ve_operacion = 'INSERT')THEN
			vl_fecha_registro := SYSDATE;


			--INSERTAR EN deposito
			INSERT INTO deposito
						(cod_deposito
            ,fecha_registro
            ,nro_deposito
						,fecha_deposito
						,cod_usuario
						,cod_cuenta_corriente
						,cod_estado_deposito
						,cod_comprobante
						,cod_comprobante_anula
						,cod_familia_deuda
						)
			VALUES(deposito_sq.NEXTVAL
            ,vl_fecha_registro
            ,ve_nro_deposito
						,ve_fecha_deposito
						,ve_cod_usuario
						,ve_cod_cuenta_corriente
						,ve_cod_estado_deposito
						,ve_cod_comprobante
						,ve_cod_comprobante_anula
						,ve_cod_familia_deuda
						);
		ELSIF (ve_operacion = 'UPDATE')THEN

			SELECT  cod_estado_deposito
      		INTO    vl_cod_estado_deposito
			FROM    deposito
			WHERE   cod_deposito = ve_cod_deposito;

			UPDATE	deposito
		   	SET		nro_deposito			      = ve_nro_deposito
					    ,fecha_deposito         = ve_fecha_deposito
					    ,cod_usuario            = ve_cod_usuario
					    ,cod_cuenta_corriente   = ve_cod_cuenta_corriente
					    ,cod_estado_deposito    = ve_cod_estado_deposito
					    ,cod_comprobante        = ve_cod_comprobante
					    ,cod_comprobante_anula  = ve_cod_comprobante_anula
					  ,cod_familia_deuda      = ve_cod_familia_deuda
			WHERE	cod_deposito = ve_cod_deposito;

      IF (vl_cod_estado_deposito = kl_ingresado AND ve_cod_estado_deposito = kl_anulado) THEN
				UPDATE	deposito
				SET		  fecha_anula           = sysdate,
    						cod_usuario_anula     = ve_cod_usuario_anula,
		    				motivo_anula          = ve_motivo_anula
				WHERE	  cod_deposito = ve_cod_deposito;
      END IF;

		ELSIF (ve_operacion = 'CONFIRMA')THEN	
		
			vl_cod_usuario_confirma:= ve_nro_deposito;
       			
            SELECT COD_USUARIO
					      ,COD_FAMILIA_DEUDA
				    into vl_cod_usuario
					      ,vl_cod_familia_deuda
			      from deposito
			      WHERE	cod_deposito = ve_cod_deposito;
				
       		  -- se marca depositado todos los documentos
			      UPDATE  documento_pago
			      SET     cod_estado_documento_pago = K_DEPOSITADO
			      WHERE   cod_documento_pago in (SELECT cod_documento_pago
                                       		 FROM   deposito_documento
         									                WHERE cod_deposito = ve_cod_deposito);
       
            ve_fecha_confirma2 := SYSDATE;

				    vl_nom_comprobante := 'Comprobante Déposito: '||to_char(ve_cod_deposito);

				    spu_comprobante ('INSERT'
									            , NULL
									            , ve_fecha_confirma2
									            , vl_cod_usuario
									            , vl_nom_comprobante
									            , vl_cod_familia_deuda
									            , KL_EST_COMP_APROBADO		-- estado_coprobante = aprobado
									            , KL_TIPO_COMP_TRASPASO	-- tipo_comprobante = traspaso
									            , KL_ORIGEN_COMP_DEPO		-- origen_comprobante = deposito documento
									            , vl_cod_usuario
									            , ve_fecha_confirma2);

            SELECT  comprobante_sq.CURRVAL
            INTO    vl_cod_comprobante
            FROM    dual;
            
            UPDATE	DEPOSITO
			SET	COD_USUARIO_CONFIRMA = ve_nro_deposito --aca viene el usuario que confirma
				,FECHA_CONFIRMA      = sysdate
				,COD_COMPROBANTE 	 = vl_cod_comprobante
            WHERE	cod_deposito = ve_cod_deposito;


            --comprobante para los ch a fecha
            vl_nom_comprobante := 'Comprobante Déposito CH Fecha: '||to_char(ve_cod_deposito);

				    spu_comprobante ('INSERT'
									            , NULL
									            , ve_fecha_confirma2
									            , vl_cod_usuario
									            , vl_nom_comprobante
									            , vl_cod_familia_deuda
									            , KL_EST_COMP_APROBADO		-- estado_coprobante = aprobado
									            , KL_TIPO_COMP_TRASPASO	-- tipo_comprobante = traspaso
									            , KL_ORIGEN_COMP_DEPO		-- origen_comprobante = deposito documento
									            , vl_cod_usuario
									            , ve_fecha_confirma2);

            SELECT  comprobante_sq.CURRVAL
            INTO    vl_cod_comprobante_ch_fecha
            FROM    dual;

            --comprobante responsabilidad ch a fecha
            vl_nom_comprobante := 'Comprobante Responsabilidad CH Fecha: '||to_char(ve_cod_deposito);

				    spu_comprobante ('INSERT'
									            , NULL
									            , ve_fecha_confirma2
									            , vl_cod_usuario
									            , vl_nom_comprobante
									            , vl_cod_familia_deuda
									            , KL_EST_COMP_APROBADO		-- estado_coprobante = aprobado
									            , KL_TIPO_COMP_TRASPASO	-- tipo_comprobante = traspaso
									            , KL_ORIGEN_COMP_DEPO		-- origen_comprobante = deposito documento
									            , vl_cod_usuario
									            , ve_fecha_confirma2);

            SELECT  comprobante_sq.CURRVAL
            INTO    vl_cod_comp_resp_ch_fecha
            FROM    dual;


            SELECT	COD_CUENTA_CONTABLE -- DEBE
            INTO    vl_cod_cuenta_contable
				    FROM	  DEPOSITO D, CUENTA_CORRIENTE CC
				    WHERE	  D.COD_CUENTA_CORRIENTE = CC.COD_CUENTA_CORRIENTE
				       AND		  D.COD_DEPOSITO = ve_cod_deposito;

            SELECT f_get_parametro (K_PARAM_TIPO_UNIVERSIDAD)
            INTO vl_tipo_universidad
            FROM dual;


            FOR a IN C_DOC_PAGO LOOP

            IF (vl_tipo_universidad = 'ESTATAL' AND a.cod_tipo_documento_pago = KL_TIPO_CH_FECHA) THEN
                FOR b IN C_DOC_ASIGNADO(a.cod_documento_pago) LOOP
                
                	select cod_tipo_documento_pago
                	into vl_tipo_documento_pago
                	from documento_pago 
                	where cod_documento_pago = a.cod_documento_pago;

				      ------------------------------
                      -- crea el abono
                      ------------------------------
				      vl_fecha_abono := sysdate;
          			  vl_valor_moneda := f_mon_get_valor_moneda(b.cod_moneda, vl_fecha_abono);
				      --vl_monto_abono := round(b.monto_asignado /  vl_valor_moneda, 4);

						      -- componente_deuda y porporcion K, IN, IP
		              IF (b.cod_plan_pago IS NULL) THEN
							      vl_monto_capital_pesos := b.saldo_total_pesos;
							      vl_monto_interes_pesos := 0;
							      vl_monto_multa_pesos := 0;
		              ELSE
							      vl_monto_capital_pesos := ROUND(f_pp_saldo_capital(b.cod_plan_pago) * vl_valor_moneda, 0);
							      vl_monto_interes_pesos := ROUND(f_pp_saldo_interes_normal(b.cod_plan_pago) * vl_valor_moneda,0);
							      vl_monto_multa_pesos := ROUND(f_pp_saldo_multa(b.cod_plan_pago) * vl_valor_moneda, 0);
						      END IF;

						      if ((vl_monto_capital_pesos
							      + vl_monto_interes_pesos
							      + vl_monto_multa_pesos) <> 0) THEN	---INI SUMAS_TO = 2
							      -- porporcion K, IN, IP
							      vl_factor_K  := vl_monto_capital_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
							      vl_factor_IN := vl_monto_interes_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
							      vl_factor_IP := vl_monto_multa_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
							      
							      
							       if(vl_tipo_documento_pago = K_CHEQUE_FECHA)then
				            			--Se crea 1 abono distribuido por el monto total del Documento_pago, ya que el monto_asignado queda mal para cheque a fecha
				            			  vl_monto_abono := round(a.monto /  vl_valor_moneda, 4);
				            			  vl_monto_pesos := a.monto;
									else
									      vl_monto_abono := round(b.monto_asignado /  vl_valor_moneda, 4);
									      vl_monto_pesos := b.monto_asignado;
									end if;

							      vl_monto_capital := Round(vl_monto_abono * vl_factor_K, 4);
							      vl_monto_interes := Round(vl_monto_abono * vl_factor_IN, 4);
							      vl_monto_multa := Round(vl_monto_abono * vl_factor_IP, 4);

							      -- si no cuadra con el total_moneda que se esta pagando, se agrega la diferencia al abono K
							      IF ((vl_monto_capital + vl_monto_interes + vl_monto_multa) <> vl_monto_abono) THEN
								      vl_monto_capital := vl_monto_capital + (vl_monto_abono - (vl_monto_capital + vl_monto_interes + vl_monto_multa));
							      END IF;
								  
							      if(vl_finaliza = 'N')then
							      
							      	  select nvl(max(cod_abono),0)
							      	  into vl_max_cod_abono
									  from abono
									  where cod_deuda = b.cod_deuda;
							      
								      spu_abono_carga(vl_fecha_abono                  --ve_fecha_abono
					                              ,vl_cod_usuario_confirma             --ve_cod_usuario
					                              ,vl_monto_capital
					                              ,vl_monto_interes
					                              ,vl_monto_multa
					                              ,vl_monto_pesos
					                              ,0
					                              ,b.cod_plan_pago
					                              ,b.cod_deuda
					                              ,NULL
					                              ,K_ORIGEN_PAGO_CAJA
					                              ,NULL
					                              ,a.cod_documento_pago
					                              ,vl_cod_comprobante_ch_fecha
					                              ,vl_cod_abono);
					                              
					                              
					                    FOR c IN C_ABONO(b.cod_deuda, vl_max_cod_abono, vl_cod_abono) LOOP
					                    
					                    	SELECT count(*) 
					                    	into vl_count_item
					                    	FROM item_ingreso 
					                    	WHERE cod_ingreso = b.cod_ingreso
					                    	and cod_plan_pago = c.cod_plan_pago;
					                    
					                    	if(vl_count_item > 0)then
					                    
					                    		SELECT cod_item_ingreso 
						                    	into vl_cod_item_ingreso
						                    	FROM item_ingreso 
						                    	WHERE cod_ingreso = b.cod_ingreso
						                    	and cod_plan_pago = c.cod_plan_pago
						                    	and rownum = 1;

	  											update monto_doc_asignado set monto_asignado = c.monto_pesos
						                    	WHERE cod_documento_pago = a.cod_documento_pago
	  											and cod_item_ingreso = vl_cod_item_ingreso;
					                    	end if;
					                    	
					                    	--El spu_abono_carga realiza tambien algunos analiticos por ello la condicion
					                    	if( c.cod_abono <= vl_cod_abono)then
							                    /*
							                    ITEM_COMPROBANTE Y ANALITICO_COMPROBANTE CH FECHA
							                    */
							                    sp_comp_ing (vl_cod_comprobante_ch_fecha, c.cod_abono);
											end if;
											
										 -- graba los abonos relacionados al ingreso
									        INSERT INTO ABONO_INGRESO
												      (COD_ABONO_INGRESO
												      ,COD_INGRESO
												      ,COD_ABONO )
										      VALUES(abono_ingreso_sq.NEXTVAL
												      ,b.cod_ingreso
												      ,c.cod_abono);
											      
										END LOOP;--C_ABONO
										      
									end if;--vl_finaliza = 'N'
									
									if(vl_tipo_documento_pago = K_CHEQUE_FECHA)then
								 		vl_finaliza := 'S';
								 	end if;
										      										      
						      END IF;	---FIN SUMAS_TO = 2



                END LOOP; --C_DOC_ASIGNADO
                
                
                /*
                ITEM_COMPROBANTE Y ANALITICO_COMPROBANTE CH FECHA
                */
                -- obtiene la cuenta de ingreso
                SELECT cod_cuenta_contable
                INTO vl_cod_cuenta_contable_caja
                FROM cuenta_tipo_documento_pago
                WHERE cod_tipo_documento_pago = KL_TIPO_EFECTIVO --efectivo o caja
                  AND cod_familia_deuda =  vl_cod_familia_deuda;


				        spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante_ch_fecha, vl_cod_cuenta_contable_caja,  a.monto, 0);
				        spu_analitico ('INSERT', NULL, vl_cod_comprobante_ch_fecha, null, vl_cod_cuenta_contable_caja, a.monto, 0);

				        SELECT  analitico_comprobante_sq.CURRVAL
				        INTO vl_cod_analitico
				        FROM dual;

                update analitico_comprobante
						    SET cod_deudor = a.cod_deudor
						    where cod_analitico_comprobante = vl_cod_analitico;

                /*
                ITEM_COMPROBANTE Y ANALITICO_COMPROBANTE RESPONSABILIDAD CH FECHA
                */
                --cuenta ch fecha
                vl_cod_cuenta_ingreso := f_ingreso_get_cuenta(a.cod_documento_pago);

				        spu_item_comprobante ('INSERT', NULL, vl_cod_comp_resp_ch_fecha, vl_cod_cuenta_ingreso,  0, a.monto);
				        spu_analitico ('INSERT', NULL, vl_cod_comp_resp_ch_fecha, null, vl_cod_cuenta_ingreso, 0, a.monto);

				        SELECT  analitico_comprobante_sq.CURRVAL
				        INTO vl_cod_analitico
				        FROM dual;

                update analitico_comprobante
						    SET cod_deudor = a.cod_deudor
						    where cod_analitico_comprobante = vl_cod_analitico;

                --cuenta recuperacion ch fecha
                select	cod_cuenta_resp_ch_fecha
			          INTO	vl_cod_cuenta_resp_ch_fecha
					      from	familia_deuda
					      where	cod_familia_deuda = vl_cod_familia_deuda;

                spu_item_comprobante ('INSERT', NULL, vl_cod_comp_resp_ch_fecha, vl_cod_cuenta_resp_ch_fecha, a.monto, 0);
				        spu_analitico ('INSERT', NULL, vl_cod_comp_resp_ch_fecha, null, vl_cod_cuenta_resp_ch_fecha, a.monto, 0);

                SELECT  analitico_comprobante_sq.CURRVAL
				        INTO vl_cod_analitico
				        FROM dual;

                update analitico_comprobante
						    SET cod_deudor = a.cod_deudor
						    where cod_analitico_comprobante = vl_cod_analitico;

                -- a vl_cuenta_ingreso, fuerza la cuenta caja
                SELECT cod_cuenta_contable
                INTO vl_cuenta_ingreso
                FROM cuenta_tipo_documento_pago
                WHERE cod_tipo_documento_pago = KL_TIPO_EFECTIVO --efectivo o caja
                  AND cod_familia_deuda =  vl_cod_familia_deuda;

              ELSE
                vl_cuenta_ingreso := f_ingreso_get_cuenta(a.cod_documento_pago); -- HABER

              END IF;-- (vl_tipo_universidad <> 'ESTATAL')


		          spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cuenta_ingreso, 0, a.monto); -- debe
              spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cod_cuenta_contable, a.monto, 0);      -- haber

              spu_analitico ('INSERT', NULL, vl_cod_comprobante, null, vl_cuenta_ingreso, 0, a.monto); -- debe

              SELECT  analitico_comprobante_sq.CURRVAL
              INTO vl_cod_analitico_comprobante
              FROM dual;

			        update analitico_comprobante
			        set cod_deudor = a.cod_deudor
			        where cod_analitico_comprobante = vl_cod_analitico_comprobante;

              spu_analitico ('INSERT', NULL, vl_cod_comprobante, null, vl_cod_cuenta_contable, a.monto, 0);      -- haber
              SELECT  analitico_comprobante_sq.CURRVAL
              INTO vl_cod_analitico_comprobante
              FROM dual;

			        update analitico_comprobante
			        set cod_deudor = a.cod_deudor
			        where cod_analitico_comprobante = vl_cod_analitico_comprobante;

				  END LOOP;
				-- borra las lineas duplicadas
				sp_comp_simplifica (vl_cod_comprobante);
        sp_comp_simplifica (vl_cod_comprobante_ch_fecha);
        sp_comp_simplifica (vl_cod_comp_resp_ch_fecha);
		END IF;
    COMMIT;
END;
END;
/