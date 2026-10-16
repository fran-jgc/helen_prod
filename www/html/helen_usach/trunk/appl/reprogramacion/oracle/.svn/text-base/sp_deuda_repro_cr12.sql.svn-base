CREATE OR REPLACE PROCEDURE sp_deuda_repro_cr12(ve_cod_reprogramacion	  IN NUMBER
)
AS
BEGIN
  	DECLARE K_PARAM_UNIVERSIDAD			number;
			K_PARAM_CARTERA				number;
			K_COD_TIPO_DEUDA			varchar2(10);
			K_DEUDA_EXIGIBLE			number;
			K_LEY_REPRO					number;
			vl_cod_universidad			number;
			vl_cod_cartera				number;
			vl_cod_deuda				number;
			vl_cod_usuario				number;
			vl_monto_pesos				number;
			vl_cant_cotas_12			number;
			vl_monto					number(14,4);
			vl_ano						number;
			vl_cod_deudor				number;
			vl_monto_total				number(14,4);
			vl_fecha_pago_inicial		date;
			vl_cod_plan_pago			number;
			vl_cod_boleta				number;
			vl_indice					number;
			vl_monto_intertes			number(14,4);
			vl_estado_pagare_vig		number;
			KL_DEUDA_PAGADA				number;
			vl_estado_pp				number;

	
	 CURSOR C_PAGARE IS			
		SELECT COD_PAGARE_REPROGRAMACION 
		FROM PAGARE_REPROGRAMACION
		WHERE COD_REPROGRAMACION = ve_cod_reprogramacion;			
	
	 CURSOR C_PP_CR12(vl_cod_pr number)IS	
		select COD_CUOTA_ANUAL
			  ,NRO
			  ,To_Char(FECHA_VCTO,'YYYY') ANO
			  ,CAPITAL + INTERES_PENAL MONTO_CAPITAL
			  ,INTERES + INTERES_NORMAL MONTO_INTERES
			  ,FECHA_VCTO
			  ,COD_PLAN_PAGO
		from CUOTA_ANUAL
		WHERE COD_PAGARE_REPROGRAMACION = vl_cod_pr
		ORDER BY NRO ASC;
	 
	 CURSOR C_DEUDA_REPRO IS	
		 SELECT COD_DEUDA
		 FROM DEUDA_REPROGRAMACION
		 WHERE COD_REPROGRAMACION = ve_cod_reprogramacion;
		 
	BEGIN
		
	
	K_PARAM_UNIVERSIDAD		:= 3;
	K_PARAM_CARTERA			:= 2;
	K_DEUDA_EXIGIBLE			:= 2;
	vl_cod_universidad			:= f_get_parametro(K_PARAM_UNIVERSIDAD);
	vl_cod_cartera				:= f_get_parametro(K_PARAM_CARTERA);
	K_COD_TIPO_DEUDA			:= 'CR12';
	K_LEY_REPRO				:= 21;
	KL_DEUDA_PAGADA			:= 4;
	
	
	
	SELECT PR.NRO_CUOTAS_REPROGRAMADO
			,PR.MONTO_REPROGRAMADO
			,RE.COD_USUARIO
	INTO	vl_cant_cotas_12
			,vl_monto_total
			,vl_cod_usuario
	FROM	 PAGARE_REPROGRAMACION PR
			,REPROGRAMACION RE
	WHERE PR.COD_REPROGRAMACION = ve_cod_reprogramacion
		AND PR.COD_REPROGRAMACION = RE.COD_REPROGRAMACION
		AND ROWNUM = 1;
		
	SELECT VALOR
	INTO vl_estado_pagare_vig
	FROM CONSTANTE
	WHERE COD_CONSTANTE = 'K_EST_PAGARE_VIG';
	
	SELECT COD_DEUDOR
	INTO vl_cod_deudor
	FROM DEUDOR DR
		,DEUDOR_REPROGRAMACION DOR
	WHERE  DOR.COD_REPROGRAMACION = ve_cod_reprogramacion
	   AND DR.RUT = DOR.RUT;
	
	------------------------------------------------------------------------------------------
	-- MAIN
	------------------------------------------------------------------------------------------
	
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
			values(DEUDA_SQ.NEXTVAL
					,vl_cod_deudor
					,K_COD_TIPO_DEUDA
					,vl_cod_universidad
					,vl_cod_cartera
					,K_DEUDA_EXIGIBLE
					,vl_cant_cotas_12
					,To_Date('01/01/' || To_Char(To_Char(SYSDATE,'yyyy') + 1), 'dd/mm/yyyy') --SYSDATE
					,vl_monto_total);
							
	
	SELECT  deuda_sq.CURRVAL
    INTO    vl_cod_deuda
    FROM    DUAL;
    
    /*
    si DEUDA.MONTO_INICIO_COBRO es cero el estado de la deuda = PAGADA
    */
    IF (vl_monto_total = 0) THEN
      UPDATE DEUDA
      SET COD_ESTADO_DEUDA = KL_DEUDA_PAGADA
      WHERE cod_deuda = vl_cod_deuda;
    END IF;
	
	--SE CREA REGISTRO EN PAGARE
	FOR a IN C_PAGARE LOOP
	
		INSERT INTO PAGARE(COD_PAGARE
						  ,FOLIO_PAGARE
						  ,FECHA_PAGARE
						  ,COD_DEUDA
						  ,MONTO_MONEDA
						  ,MONTO_PESOS
						  ,COD_ESTADO_PAGARE
						  ,COD_USUARIO_DEVOLUCION
						  ,FECHA_DEVOLUCION
						  ,DEVOLUCION_A
						  ,FECHA_PROTESTO
						  ,COD_USUARIO_PROTESTO
						  ,COD_TIPO_REBAJA_PAGARE
						  ,MONTO_MONEDA_REBAJA
						  ,MONTO_PESOS_REBAJA
						  ,COD_UNIVERSIDAD
						  ,MOTIVO_DEVOLUCION
						  ,RUT_DEVOLUCION)
						SELECT	PAGARE_SQ.NEXTVAL 
								  ,ID_PAGARE
								  ,To_Date('01/01/' || To_Char(To_Char(SYSDATE,'yyyy') + 1), 'dd/mm/yyyy') --SYSDATE
								  ,vl_cod_deuda
								  ,(INTERESES * (100 - PORC_CONDONACION_INTERES_PENAL)/100 + SALDO_CAPITAL)
								  ,(INTERESES * (100 - PORC_CONDONACION_INTERES_PENAL)/100 + SALDO_CAPITAL) * f_mon_get_valor_moneda(3,FECHA_GENERACION_PAGARE) MONTO_PAGARE_PESOS
								  ,vl_estado_pagare_vig
								  ,null
								  ,null
								  ,null
								  ,null
								  ,null
								  ,null
								  ,null
								  ,null
								  ,COD_UNIVERSIDAD
								  ,NULL
								  ,NULL
							FROM PAGARE_REPROGRAMACION
							WHERE COD_PAGARE_REPROGRAMACION = a.cod_pagare_reprogramacion;
	
	END LOOP;
	
	vl_indice := 0;
	/*
	si DEUDA.MONTO_INICIO_COBRO es cero el estado de la cuota = PAGADA
	*/
	IF (vl_monto_total = 0) THEN
		vl_estado_pp := 3; --PAGADA
	ELSE
		vl_estado_pp := 1;  --EMITIDA 
	END IF;

	FOR b IN C_PAGARE LOOP
		
		vl_indice := vl_indice + 1;
		
		FOR a IN C_PP_CR12(b.cod_pagare_reprogramacion) LOOP
			--SE CREAN LOS PLANES PAGO
			
		
			if(vl_indice = 1)then
				--SE CREAN LOS PLANES PAGO
				INSERT INTO PLAN_PAGO(COD_PLAN_PAGO
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
							VALUES(PLAN_PAGO_SQ.NEXTVAL
								  ,vl_cod_deuda
								  ,a.nro
								  ,a.ano
								  ,a.monto_capital
								  ,a.monto_interes
								  ,a.fecha_vcto
								  ,null
								  ,vl_estado_pp
								  ,null
								  ,null
								  ,null);
				
				SELECT  plan_pago_sq.CURRVAL
	    		INTO    vl_cod_plan_pago
	    		FROM    DUAL;
				
				update CUOTA_ANUAL
				set COD_PLAN_PAGO = vl_cod_plan_pago
				where COD_CUOTA_ANUAL = a.cod_cuota_anual;
	
			else 
				
				/*
				 para que esta idea funcione deben ser las cuotas del pagare 1 igual que el pagare 2
				 respecto al NRO_CUOTA y ANO_CUOTA
				 */
				select cod_plan_pago
				into vl_cod_plan_pago
				from plan_pago
				where cod_deuda = vl_cod_deuda
					and nro_cuota = a.nro
					and ano_cobro = a.ano
					and rownum = 1;
				
				UPDATE PLAN_PAGO
				SET  MONTO_CAPITAL 	= MONTO_CAPITAL + a.monto_capital
					,MONTO_INTERES	= MONTO_INTERES + a.monto_interes
				WHERE COD_PLAN_PAGO = vl_cod_plan_pago;
					
				update CUOTA_ANUAL
				set COD_PLAN_PAGO = vl_cod_plan_pago
				where COD_CUOTA_ANUAL = a.cod_cuota_anual;
			end IF;
			
			--SE HACE UPDATE A TABLA CUOTA ANUAL CON EL COD_PLAN_PAGO
			UPDATE CUOTA_ANUAL
			SET COD_PLAN_PAGO = vl_cod_plan_pago
			WHERE COD_CUOTA_ANUAL = a.cod_cuota_anual;
			
		END LOOP;
	END LOOP;
	
	
	--SE ACTUALIZA EL CAMPO DEUDA_CR12 CON LA DEUDA DEL PAGO INICIAL								  
	UPDATE REPROGRAMACION
	SET COD_DEUDA_CR12 = vl_cod_deuda
	WHERE COD_REPROGRAMACION = ve_cod_reprogramacion;
	
	--CREA REGISTROS EN CRUZADO, SI CORRESPONDE
	sp_cruzado_repro('CR12',ve_cod_reprogramacion);
	
	--CURSOR DEUDAS REPROGRAMADAS
	FOR a IN C_DEUDA_REPRO LOOP
		--SE CAMBIAN LOS ESTADOS DE LAS DEUDAS ORIGINALES
		UPDATE DEUDA
		SET COD_ESTADO_DEUDA = K_LEY_REPRO
		WHERE COD_DEUDA = a.cod_deuda;
	END LOOP;

	------------------------------------------------------------------------------------------
	-- END MAIN
	------------------------------------------------------------------------------------------
	END; 
END;