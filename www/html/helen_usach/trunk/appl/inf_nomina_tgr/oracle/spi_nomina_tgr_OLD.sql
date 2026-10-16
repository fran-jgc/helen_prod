CREATE OR REPLACE PROCEDURE spi_nomina_tgr  (ve_cod_usuario		NUMBER)
AS
BEGIN
	DECLARE	K_COD_FAMILIA_DEUDA			NUMBER := 1; -- FONDO SOLIDARIO	COD_CUENTA: 31115
			K_ESTADO_PLAN_PAGO_MOROSA	NUMBER := 4; --4	MOROSA
			K_TIPO_NOMINA_TGR			NUMBER := 25; ----ENVIADO A TGR
			vl_saldo					NUMBER := 0;
			vl_rut						VARCHAR2(20);
			vl_fecha_actual				DATE := SYSDATE;
			vl_glosa_bitacora			VARCHAR2(100);
			vl_mora						NUMBER := 0;
			vl_multa					NUMBER := 0;
			vl_tipo_moneda				NUMBER := 0;
			vl_count_cruzado			NUMBER := 0;
			vl_marca_deuda 				NUMBER := 2;
			vl_rut_universidad			NUMBER := 0;
			vl_dv_rut_universidad		VARCHAR2(1)	 := '';
			vl_razon_universidad		VARCHAR2(55) := '';
			vcruzada_rut_universidad	NUMBER;
			vcruzada_dig_verif_universidad	VARCHAR2(1);
			vl_rut_universidad_conjuntas	VARCHAR2(90);
			vl_nro_cta_bancaria_retenedor	VARCHAR2(15);
			vl_banco_cta_retenedor			VARCHAR2(36);
			K_TIPO_MONEDA_PESOS			NUMBER := 1;
			K_TIPO_MONEDA_UF			NUMBER := 2;
			K_TIPO_MONEDA_UTM			NUMBER := 3;
			K_PARAM_RUT_U				NUMBER := 4;
			K_PARAM_NOM_U				NUMBER := 5;
			K_PARAM_NRO_CUENTA_UNIVERSIDAD	NUMBER := 25;

	--------------------- NOMINA_TGR -----------------
	CURSOR C_NOMINA_TGR
         IS	SELECT	DISTINCT	DO.COD_DEUDOR
								,DE.COD_DEUDA
								,To_Number('2')	TIPO_REGISTRO
								,DO.RUT	RUT_DEUDOR
								,DO.DIG_VERIF	DV_RUT_DEUDOR
								,DO.APELLIDO_PATERNO	AP_PATERNO_DEUDOR
								,DO.APELLIDO_MATERNO	AP_MATERNO_DEUDOR
								,DO.NOMBRE	NOMBRES_DEUDOR
								,To_Number(To_Char(SYSDATE, 'YYYYMMDD'))	F_LIQUIDACION_DEUDA
								,TD.COD_MONEDA	TIPO_MONEDA
								,TO_NUMBER(REPLACE(SUBSTR(f_get_parametro(K_PARAM_RUT_U),1,10),'.','')) RUT_RETENEDOR
								,TO_NUMBER(SUBSTR(f_get_parametro(K_PARAM_RUT_U),12,1)) DV_RUT_RETENEDOR
								,f_get_parametro(K_PARAM_NOM_U) AP_PATERNO_RETENEDOR
								,''	AP_MATERNO_RETENEDOR
								,''	NOMBRES_RETENEDOR
								,To_Number('1')	TIPO_CTA_RETENEDOR
						 FROM	DEUDOR DO, DEUDA DE, PLAN_PAGO PP, TIPO_DEUDA TD
						WHERE	DO.COD_DEUDOR = DE.COD_DEUDOR
						  AND	DE.COD_DEUDA = PP.COD_DEUDA
						  AND	DE.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
						  AND	TD.COD_FAMILIA_DEUDA = K_COD_FAMILIA_DEUDA
						  AND	PP.COD_ESTADO_PLAN_PAGO = K_ESTADO_PLAN_PAGO_MOROSA;

	CURSOR C_PP(vc_cod_deuda number)
		IS	SELECT	COD_PLAN_PAGO
			  FROM	PLAN_PAGO
			 WHERE	COD_DEUDA = vc_cod_deuda;
			 
	CURSOR C_UNIV_CRUZADAS(vc2_cod_deuda number)
		IS	SELECT	COD_UNIVERSIDAD
			FROM    CRUZADO
			WHERE   COD_DEUDA = vc2_cod_deuda;

	BEGIN
		FOR A IN C_NOMINA_TGR
		LOOP
			FOR B IN C_PP(A.COD_DEUDA) LOOP
         		vl_mora := f_pp_saldo_mora(B.COD_PLAN_PAGO, vl_fecha_actual);
				vl_multa := f_pp_saldo_multa(B.COD_PLAN_PAGO, vl_fecha_actual);
				vl_saldo := vl_saldo + vl_mora + vl_multa;
			END LOOP;
			
			--TRATAMIENTO DE DATOS.
				IF(A.TIPO_MONEDA = K_TIPO_MONEDA_PESOS) THEN
					vl_tipo_moneda := 0; --Pesos $
				ELSIF(A.TIPO_MONEDA = K_TIPO_MONEDA_UF)  THEN 
					vl_tipo_moneda := 1;	--UF
				ELSIF(A.TIPO_MONEDA = K_TIPO_MONEDA_UTM) THEN 
					vl_tipo_moneda := 2; --UTM
				ELSE
					vl_tipo_moneda := 3; --Dolares US.
				END IF;
			
			
				--1=NO CONJUNTA, 2=CONJUNTA--- MARCA CORRESPONDE A CRUZADOS
				SELECT	COUNT(*)
				INTO	vl_count_cruzado
				FROM    CRUZADO C
				WHERE   COD_DEUDA = A.COD_DEUDA;
				
				IF(vl_count_cruzado <> 0) THEN
					vl_marca_deuda 		:= 2;
					vl_rut_universidad	:= 0;
					vl_dv_rut_universidad	:= '';
					vl_razon_universidad	:= '';
					
					FOR C IN C_UNIV_CRUZADAS(A.COD_DEUDA) LOOP
						
						SELECT	 TO_NUMBER(SUBSTR(TO_CHAR(RUT_UNIVERSIDAD),1,8))
								,DIG_VERIF_UNIVERSIDAD
						INTO	vcruzada_rut_universidad
								,vcruzada_dig_verif_universidad							
						FROM UNIVERSIDAD
						WHERE COD_UNIVERSIDAD = C.COD_UNIVERSIDAD;
											
						vl_rut_universidad_conjuntas := vl_rut_universidad_conjuntas || TO_CHAR(vcruzada_rut_universidad) || vcruzada_dig_verif_universidad;
					END LOOP;
				ELSE
					vl_marca_deuda := 1;
					vl_rut_universidad	:= TO_NUMBER(REPLACE(SUBSTR(f_get_parametro(K_PARAM_RUT_U),1,10),'.',''));
					vl_dv_rut_universidad	:= TO_NUMBER(SUBSTR(f_get_parametro(K_PARAM_RUT_U),12,1));
					vl_razon_universidad	:= f_get_parametro(K_PARAM_NOM_U);
					vl_rut_universidad_conjuntas	:= '';
				
				---NRO_CTA_BANCARIA_RETENEDOR, BANCO_CTA_RETENEDOR
				SELECT	NRO_CUENTA_CORRIENTE
				INTO	vl_nro_cta_bancaria_retenedor
				FROM CUENTA_CORRIENTE 
				WHERE COD_CUENTA_CORRIENTE = f_get_parametro(K_PARAM_NRO_CUENTA_UNIVERSIDAD);
	
				SELECT	B.NOM_BANCO
				INTO	vl_banco_cta_retenedor
				FROM CUENTA_CORRIENTE C, BANCO B 
				WHERE C.COD_BANCO = B.COD_BANCO 
				AND C.COD_CUENTA_CORRIENTE = f_get_parametro(K_PARAM_NRO_CUENTA_UNIVERSIDAD);
			--END TRATAMIENTOS DE DATOS
			
				INSERT INTO TEMP_NOMINA_TGR
							(TIPO_REGISTRO
							,RUT_DEUDOR
							,DV_RUT_DEUDOR
							,AP_PATERNO_DEUDOR
							,AP_MATERNO_DEUDOR
							,NOMBRES_DEUDOR
							,F_LIQUIDACION_DEUDA
							,MONTO_DEUDA
							,TIPO_MONEDA
							,RUT_RETENEDOR
							,DV_RUT_RETENEDOR
							,AP_PATERNO_RETENEDOR
							,AP_MATERNO_RETENEDOR
							,NOMBRES_RETENEDOR
							,MARCA_DEUDA
							,RUT_UNIVERSIDAD
							,DV_RUT_UNIVERSIDAD
							,RAZON_UNIVERSIDAD
							,RUT_UNIVERSIDAD_CONJUNTAS
							,NRO_CTA_BANCARIA_RETENEDOR
							,BANCO_CTA_RETENEDOR
							,TIPO_CTA_RETENEDOR)
						VALUES
							(A.TIPO_REGISTRO
							,A.RUT_DEUDOR
							,A.DV_RUT_DEUDOR
							,A.AP_PATERNO_DEUDOR
							,A.AP_MATERNO_DEUDOR
							,A.NOMBRES_DEUDOR
							,A.F_LIQUIDACION_DEUDA
							,vl_saldo 		--MONTO_DEUDA
							,vl_tipo_moneda --TIPO_MONEDA
							,A.RUT_RETENEDOR
							,A.DV_RUT_RETENEDOR
							,A.AP_PATERNO_RETENEDOR
							,A.AP_MATERNO_RETENEDOR
							,A.NOMBRES_RETENEDOR
							,vl_marca_deuda --MARCA_DEUDA
							,vl_rut_universidad --RUT_UNIVERSIDAD
							,vl_dv_rut_universidad	--DV_RUT_UNIVERSIDAD
							,vl_razon_universidad	--RAZON_UNIVERSIDAD
							,vl_rut_universidad_conjuntas --RUT_UNIVERSIDAD_CONJUNTAS
							,vl_nro_cta_bancaria_retenedor --NRO_CTA_BANCARIA_RETENEDOR
							,vl_banco_cta_retenedor	--BANCO_CTA_RETENEDOR
							,A.TIPO_CTA_RETENEDOR);
				--execute  bitacora deudor
				
				vl_glosa_bitacora := 'Envio A TGR - Fecha: ' || TO_CHAR(vl_fecha_actual, 'DD/MM/YYYY');
				spu_bitacora_deudor (vl_glosa_bitacora, K_TIPO_NOMINA_TGR, A.COD_DEUDOR, ve_cod_usuario);		
			END IF;
		END LOOP;
		/*
		SELECT	TIPO_REGISTRO
				,RUT_DEUDOR
				,DV_RUT_DEUDOR
				,AP_PATERNO_DEUDOR
				,AP_MATERNO_DEUDOR
				,NOMBRES_DEUDOR
				,F_LIQUIDACION_DEUDA
				,MONTO_DEUDA
				,TIPO_MONEDA
				,RUT_RETENEDOR
				,DV_RUT_RETENEDOR
				,AP_PATERNO_RETENEDOR
				,AP_MATERNO_RETENEDOR
				,NOMBRES_RETENEDOR
				,MARCA_DEUDA
				,RUT_UNIVERSIDAD
				,DV_RUT_UNIVERSIDAD
				,RAZON_UNIVERSIDAD
				,RUT_UNIVERSIDAD_CONJUNTAS
				,NRO_CTA_BANCARIA_RETENEDOR
				,BANCO_CTA_RETENEDOR
				,TIPO_CTA_RETENEDOR
		FROM	TEMP_NOMINA_TGR
		*/
      END;
END spi_nomina_tgr;