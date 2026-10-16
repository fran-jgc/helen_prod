CREATE OR REPLACE PROCEDURE sp_deuda_repro_crpi( ve_operacion             IN VARCHAR2
                                                ,ve_cod_reprogramacion	  IN NUMBER
)
AS
BEGIN
	DECLARE K_PARAM_UNIVERSIDAD			number;
			K_PARAM_CARTERA				number;
			K_COD_TIPO_DEUDA			varchar2(10);
			K_EST_EMITIDA				number;
			vl_cod_universidad			number;
			vl_cod_cartera				number;
			vl_cod_deuda				number;
			vl_cod_usuario				number;
			vl_monto_pesos				number;
			vl_cant_cotas_PI			number;
			vl_cod_deudor				number;
			vl_monto_total				number(14,4);
			vl_cod_plan_pago			number;
			vl_cod_boleta				number;
			vl_cod_deuda_crpi			number;
			K_EST_ANULA					number;
			K_DEUDA_EXIGIBLE			number;
			vl_ano_cuota				number;
			vl_k_cliente				varchar2(100);
			vl_rut_uni					varchar2(20);
			vl_cod_empresa_cobranza	number;
			
			CURSOR C_PP_CRPI IS
			select NRO
				  ,FECHA_VCTO
				  ,MONTO
			from CUOTA_PAGO_INICIAL CA
			WHERE CA.COD_REPROGRAMACION = ve_cod_reprogramacion;
			
			CURSOR C_DEUDA IS
			select COD_DEUDA
			from DEUDA_REPROGRAMACION
			WHERE COD_REPROGRAMACION = ve_cod_reprogramacion;
			
			CURSOR C_BOLETA(vc_cod_deuda_crpi number)IS
			select COD_BOLETA
			from BOLETA
			 WHERE COD_DEUDA = vc_cod_deuda_crpi;
			
			
	BEGIN
		
		K_PARAM_UNIVERSIDAD			:= 3;			
		K_PARAM_CARTERA				:= 2;
		vl_cod_universidad			:= f_get_parametro(K_PARAM_UNIVERSIDAD);
		vl_cod_cartera				:= f_get_parametro(K_PARAM_CARTERA);
		K_COD_TIPO_DEUDA			:= 'CRPI';
		K_EST_EMITIDA				:= 1;
		K_EST_ANULA					:= 3;
		K_DEUDA_EXIGIBLE			:= 2;
		
		SELECT PR.NRO_CUOTAS_PAGO_INICIAL
				  ,PR.MONTO_PAGO_INICIAL
				  ,RE.COD_USUARIO
		INTO vl_cant_cotas_PI
			,vl_monto_total
			,vl_cod_usuario
		FROM PAGARE_REPROGRAMACION PR
			 ,REPROGRAMACION RE
		WHERE PR.COD_REPROGRAMACION = ve_cod_reprogramacion
			AND PR.COD_REPROGRAMACION = RE.COD_REPROGRAMACION
    	GROUP BY  PR.NRO_CUOTAS_PAGO_INICIAL,PR.MONTO_PAGO_INICIAL ,PR.FECHA_PAGO_INICIAL,RE.COD_USUARIO;
		
		SELECT DO.COD_DEUDOR
		INTO vl_cod_deudor
		FROM DEUDOR DO
			,DEUDOR_REPROGRAMACION DOR
		WHERE  DOR.COD_REPROGRAMACION = ve_cod_reprogramacion
		   AND DOR.RUT = DO.RUT;
		   
		------------------------------------------------------------------------------------------
		-- MAIN
		------------------------------------------------------------------------------------------
		IF (ve_operacion = 'INSERT')THEN   
				--INSERT A DEUDA DEL PAGO INICIAL 
				insert into DEUDA(COD_DEUDA
								 ,COD_DEUDOR
								 ,COD_TIPO_DEUDA
								 ,COD_UNIVERSIDAD
								 ,COD_CARTERA
								 ,COD_ESTADO_DEUDA
								 ,CANT_CUOTA
								 ,FECHA_INICIO_COBRO
								 ,MONTO_INICIO_COBRO) 
						values(	deuda_sq.NEXTVAL
								,vl_cod_deudor
								,K_COD_TIPO_DEUDA
								,vl_cod_universidad
								,vl_cod_cartera
								,K_DEUDA_EXIGIBLE
								,vl_cant_cotas_PI
								,SYSDATE
								,vl_monto_total);
										
				SELECT  deuda_sq.CURRVAL
    			INTO    vl_cod_deuda
    			FROM    DUAL;
				
    			SELECT to_number(To_Char(FECHA_PAGO_INICIAL,'yyyy'))
    			INTO vl_ano_cuota
				FROM PAGARE_REPROGRAMACION
				WHERE COD_REPROGRAMACION = ve_cod_reprogramacion
					AND rownum = 1;

			  -- Los PP deben quedar sociados a la empresa de cobranza si corresponde
			SELECT	REPLACE(valor,'.', '')
			INTO	vl_rut_uni
			FROM	PARAMETRO
			WHERE	COD_PARAMETRO = 4; --parametro rut
				
			vl_cod_empresa_cobranza := NULL;
			IF (vl_rut_uni = '81380500-6') THEN --UACH
				vl_cod_empresa_cobranza := 1;
			ELSE
				SELECT k_cliente
		        INTO vl_k_cliente
				FROM REPROGRAMACION
				WHERE COD_REPROGRAMACION = ve_cod_reprogramacion;
					
				IF (vl_k_cliente = 'MASCOB_UCM') THEN 
					vl_cod_empresa_cobranza := 1;
				ELSIF (vl_k_cliente = 'SURCOB_UCM') THEN      
					vl_cod_empresa_cobranza := 2;
		        END IF;
			END IF;

        
				--CURSOR PLAN DE PAGOS
				FOR a IN C_PP_CRPI LOOP
					
					--SE CREAN LOS PLANES PAGO
					INSERT INTO plan_pago(COD_PLAN_PAGO
										 ,COD_DEUDA
										 ,NRO_CUOTA
										 ,ANO_COBRO
										 ,MONTO_CAPITAL
										 ,MONTO_INTERES
										 ,FECHA_VENCTO
										 ,COD_CONCEPTO_COBRO
										 ,COD_ESTADO_PLAN_PAGO
										 ,COD_EMPRESA_COBRANZA
										 ,PUBLICA_DICOM
										 ,COD_ANALITICO_COMPROBANTE)
								VALUES(plan_pago_sq.NEXTVAL
									  ,vl_cod_deuda
									  ,a.nro
            			  			  ,To_Number(To_Char(a.fecha_vcto,'YYYY')) 
									  ,a.monto
									  ,0
									  ,a.fecha_vcto
									  ,null
									  ,1
									  ,vl_cod_empresa_cobranza
									  ,null
									  ,null);
					
					SELECT  plan_pago_sq.CURRVAL
	    			INTO    vl_cod_plan_pago
	    			FROM    DUAL;
	    			
					vl_monto_pesos := a.monto * f_mon_get_valor_moneda(3, sysdate);
							  
					--SE CREAN LAS BOLETAS
					INSERT INTO BOLETA(COD_BOLETA
									  ,FECHA_BOLETA
									  ,COD_USUARIO
									  ,COD_DEUDA
									  ,COD_PLAN_PAGO
									  ,MONTO_CAPITAL
									  ,MONTO_INTERES
									  ,MONTO_MULTA
									  ,MONTO_MONEDA
									  ,MONTO_PESOS
									  ,DSCTO_CAPITAL
									  ,DSCTO_INTERES
									  ,DSCTO_MULTA
									  ,DSCTO_PESOS
									  ,FECHA_PRIMERA_CUOTA
									  ,CANT
									  ,CARTERA_CONOCIDA
									  ,COD_TIPO_DESCUENTO
									  ,COD_EJECUCION_PROCESO) 
								VALUES(boleta_sq.NEXTVAL
									  ,sysdate
									  ,vl_cod_usuario
									  ,vl_cod_deuda
									  ,vl_cod_plan_pago
									  ,a.monto
									  ,0
									  ,0
									  ,a.monto
									  ,vl_monto_pesos
									  ,0
									  ,0
									  ,0
									  ,0
									  ,a.fecha_vcto
									  ,1
									  ,'S'
									  ,null
									  ,null);
					
					SELECT  boleta_sq.CURRVAL
	    			INTO    vl_cod_boleta
	    			FROM    DUAL;
											  
					INSERT INTO SUB_BOLETA(COD_SUB_BOLETA
										  ,COD_BOLETA
										  ,NRO_SUB_BOLETA
										  ,FECHA_VENCTO
										  ,MONTO_CAPITAL
										  ,MONTO_INTERES
										  ,MONTO_MULTA
										  ,FECHA_PAGO
										  ,COD_COMPONENTE_DEUDA)
									values(sub_boleta_sq.NEXTVAL
										  ,vl_cod_boleta
										  ,a.nro
										  ,a.fecha_vcto
										  ,a.monto
										  ,0
										  ,0
										  ,null
										  ,3);
					
					
				END LOOP;
				
				--SE ACTUALIZA EL CAMPO DEUDA_CRPI CON LA DEUDA DEL PAGO INICIAL								  
				UPDATE REPROGRAMACION
				SET COD_DEUDA_CRPI = vl_cod_deuda
				WHERE COD_REPROGRAMACION = ve_cod_reprogramacion;
				
				--CREA REGISTROS EN CRUZADO, SI CORRESPONDE
				sp_cruzado_repro('CRPI',ve_cod_reprogramacion);

		ELSIF(ve_operacion = 'ANULA')THEN
				UPDATE REPROGRAMACION
				SET COD_ESTADO_REPROGRAMACION = K_EST_ANULA
				WHERE COD_REPROGRAMACION = ve_cod_reprogramacion;
				
			 --CURSOR DEUDA_REPROGRAMACION
				FOR a IN C_DEUDA LOOP
					UPDATE DEUDA
					SET COD_ESTADO_DEUDA = K_DEUDA_EXIGIBLE
					WHERE COD_DEUDA = a.cod_deuda;
				END LOOP;	
				
			----ELIMINACION----
			SELECT COD_DEUDA_CRPI
			INTO vl_cod_deuda_crpi
			FROM REPROGRAMACION
			WHERE COD_REPROGRAMACION = ve_cod_reprogramacion;
			
			--CURSOR BOLETAS
				FOR a IN C_BOLETA(vl_cod_deuda_crpi) LOOP
					
					DELETE SUB_BOLETA
					WHERE COD_BOLETA = a.cod_boleta;
					
					DELETE BOLETA
					WHERE COD_BOLETA = a.cod_boleta;
					
				END LOOP;
				
			--ELIMINA PLAN_PAGO_CRPI
			DELETE PLAN_PAGO
			WHERE COD_DEUDA = vl_cod_deuda_crpi;
			
			--ELIMINA CRUZADO
			DELETE CRUZADO
			WHERE COD_DEUDA = vl_cod_deuda_crpi;
				
			--ELIMINA DEUDA_CRPI
			DELETE DEUDA
			WHERE COD_DEUDA = vl_cod_deuda_crpi;
		
		END IF;
		------------------------------------------------------------------------------------------
		-- END MAIN
		------------------------------------------------------------------------------------------
	END; 
END;