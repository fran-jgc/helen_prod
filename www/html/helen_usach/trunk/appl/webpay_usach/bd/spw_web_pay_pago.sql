PROMPT CREATE OR REPLACE PROCEDURE spw_web_pay_pago
CREATE OR REPLACE PROCEDURE spw_web_pay_pago (ve_cod_wp_transaccion		IN NUMBER
											 ,ve_resultado_transaccion	IN VARCHAR2 := '1'
                       						 ,ve_pago_directo IN VARCHAR2 := NULL)
AS
BEGIN
	DECLARE
		K_ORIGEN_WEBPAY			NUMBER := 3;
    	K_ORIGEN_WEBPAY_INSTITUCIONAL			NUMBER := 30;
		K_INTENTO_PAGO_WEBPAY	NUMBER := 40;
		K_INTENTO_PAGO_SERVIPAG	NUMBER := 41;
		K_USUARIO_WEB			NUMBER := 2;
		vc_tipo					VARCHAR2(20);
		vc_monto_capital		NUMBER;
		vc_monto_interes		NUMBER;
		vc_monto_multa			NUMBER;
		vc_monto_pesos			NUMBER;
		vc_cod_plan_pago		NUMBER;
		vc_cod_deuda			NUMBER;
		vc_cod_sub_boleta		NUMBER;
		vs_cod_abono			NUMBER;
		vl_fecha_actual			DATE := SYSDATE;
		vl_cod_deudor			NUMBER;
		vl_tipo_pago_web		VARCHAR2(20);
		vl_ano_cobro			NUMBER;
		vl_cod_componente_deuda	NUMBER;
		vl_dscto_capital		NUMBER;
		vl_dscto_pesos			NUMBER;
		K_ESTADO_PAGO_VIGENTE	NUMBER := 1; --VIGENTE
		K_ORIGEN_PAGO_WEBPAY	NUMBER := 3; --WEBPAY
    	vl_origen_pago 			NUMBER;
    	vl_cod_descuento		NUMBER;
    	vl_exito				VARCHAR2(1);
    	vl_descto_pesos			NUMBER;

    


	CURSOR C_SUB_BOLETA (vl_cod_wp_transaccion VARCHAR2) IS
		SELECT wt.TIPO
				,s.MONTO_CAPITAL
				,s.MONTO_INTERES
				,case TIPO
					when 'PAGO_ABONO' then s.MONTO_MULTA
					else f_subboleta_multa(s.COD_SUB_BOLETA, sysdate)
				end MONTO_MULTA
				,case TIPO
					when 'PAGO_ABONO' then b.MONTO_PESOS
					else (s.MONTO_INTERES + f_subboleta_multa(s.COD_SUB_BOLETA, sysdate) + s.MONTO_CAPITAL) * f_mon_get_valor_moneda(3, sysdate)
				end MONTO_PESOS
				,b.cod_plan_pago
				,b.cod_deuda
				,s.cod_sub_boleta
				,b.fecha_boleta
				,b.cod_boleta
		FROM wp_transaccion wt, wp_sub_boleta ws, sub_boleta s, boleta b
		where wt.cod_wp_transaccion = vl_cod_wp_transaccion
		  and ws.cod_wp_transaccion = wt.cod_wp_transaccion
		  and s.cod_sub_boleta = ws.cod_sub_boleta
		  and b.cod_boleta = s.cod_boleta;

	BEGIN

	    IF(ve_pago_directo = 'S') THEN
	      vl_origen_pago := K_ORIGEN_WEBPAY_INSTITUCIONAL;
	    ELSE
	      vl_origen_pago := K_ORIGEN_WEBPAY;
	    END IF;
			
	    select exito 
	    into vl_exito
	    from wp_transaccion
	    where cod_wp_transaccion = ve_cod_wp_transaccion;
	    
	    IF(vl_exito is null)THEN
	    	
			IF (ve_resultado_transaccion = '1') THEN
			
				UPDATE	wp_transaccion
				SET		exito = 'S'
				WHERE	cod_wp_transaccion = ve_cod_wp_transaccion;
				
				FOR a IN C_SUB_BOLETA (ve_cod_wp_transaccion) LOOP
					spu_abono(a.fecha_boleta	--fecha_abono
							 ,K_USUARIO_WEB		--cod_usuario   "USUARIO WEB"
							 ,a.MONTO_CAPITAL	--MONTO_CAPITAL
							 ,a.MONTO_INTERES	--MONTO_INTERES
							 ,a.MONTO_MULTA		--MONTO_MULTA
							 ,a.MONTO_PESOS		--MONTO_PESOS
							 ,0					--diferencia pesos
							 ,a.cod_plan_pago	--COD_PLAN_PAGO
							 ,a.cod_deuda		--COD_DEUDA
							 ,null				--@ve_cod_abono_desde
							 ,vl_origen_pago	--@ve_cod_origen_pago
							 ,a.cod_sub_boleta	--COD_SUB_BOLETA
							 ,null				--@ve_cod_documento_pago
							 ,null				--@ve_cod_comprobante
							 ,vs_cod_abono);	--output
	
					UPDATE SUB_BOLETA
					SET FECHA_PAGO = sysdate
					where COD_SUB_BOLETA = a.cod_sub_boleta;
	
					--se crea descuento siempre que la deuda se futura.
					if(a.cod_plan_pago is null)then
						--caso deudas en estado SVP y que se le aplica descuento
						select DSCTO_PESOS 
						into vl_descto_pesos
						from BOLETA 
						where cod_boleta = a.cod_boleta;
						
						if(vl_descto_pesos > 0)then
							vl_ano_cobro := 9999;
						end if;
						
						vl_cod_componente_deuda := 1; --NO EXIGIBLE
					
				
					else 
						--se crea descuento siempre que la deuda se futura.
						SELECT	ano_cobro
						INTO	vl_ano_cobro
						FROM	plan_pago
						WHERE	cod_plan_pago = a.cod_plan_pago;
						
						vl_cod_componente_deuda := f_pp_componente_pago(a.cod_plan_pago, vl_fecha_actual);
					end if;
	
					IF (vl_ano_cobro = 9999) THEN
	
						SELECT	 b.dscto_capital
								,b.dscto_pesos
						INTO	 vl_dscto_capital
								,vl_dscto_pesos
						FROM	sub_boleta sb, boleta b
						WHERE	sb.cod_sub_boleta = a.cod_sub_boleta
						AND		sb.cod_boleta = b.cod_boleta;
	
						spu_descuento('INSERT'					--@ve_operacion
									 ,null						--@ve_cod_descuento
									 ,a.fecha_boleta			--@ve_fecha_descuento
									 ,K_USUARIO_WEB				--@ve_cod_usuario   "USUARIO WEB"
									 ,vl_cod_componente_deuda	--@ve_cod_componente_deuda
									 ,vl_dscto_capital			--@ve_monto_capital
									 ,0							--@ve_monto_interes
									 ,0							--@ve_monto_multa
									 ,vl_dscto_capital			--@ve_monto_moneda
									 ,vl_dscto_pesos			--@ve_monto_pesos
									 ,a.cod_plan_pago			--@ve_cod_plan_pago
									 ,a.cod_deuda				--@ve_cod_deuda
									 ,null						--@ve_cod_comprobante
									 ,K_ESTADO_PAGO_VIGENTE		--@ve_cod_estado_pago  --1
									 ,vl_origen_pago		--@ve_cod_origen_pago  --3
									 ,a.cod_sub_boleta);		--@ve_cod_sub_boleta
									 
						select descuento_sq.currval
		                INTO vl_cod_descuento
		                from dual;		
		                
		                update descuento set cod_abono = vs_cod_abono
		                where cod_descuento = vl_cod_descuento;
									 
	
					END IF;
	
				END LOOP;
				
	
				COMMIT;
			ELSIF (ve_resultado_transaccion = '0') THEN
				select	cod_deudor
						,tipo_pago_web
				into	vl_cod_deudor
						,vl_tipo_pago_web
				from	wp_transaccion
				where	cod_wp_transaccion = ve_cod_wp_transaccion;
	
				IF ((vl_tipo_pago_web = 'PAGO_TRANSBANK') OR (vl_tipo_pago_web = 'PAGO_SEGIC'))  THEN
					spu_bitacora_deudor ('Intento pago via Webpay', K_INTENTO_PAGO_WEBPAY, vl_cod_deudor, K_USUARIO_WEB);
				ELSIF (vl_tipo_pago_web = 'PAGO_SERVIPAG') THEN
					spu_bitacora_deudor ('Intento pago via Servipag', K_INTENTO_PAGO_SERVIPAG, vl_cod_deudor, K_USUARIO_WEB);
				END IF;
	
				update wp_transaccion
				set exito = 'N'
				where cod_wp_transaccion = ve_cod_wp_transaccion;
	
			END IF;
			
		END IF;
	COMMIT;
	END;
END;
/

