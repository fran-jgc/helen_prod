PROMPT CREATE OR REPLACE PROCEDURE spdw_cuponera_pdf
CREATE OR REPLACE PROCEDURE spdw_cuponera_pdf	(ve_cod_ejecucion_proceso	NUMBER
                                            	,ve_cod_tipo_deuda			VARCHAR2
                                            	,ve_cod_usuario				NUMBER
                                            	,ve_fecha_boleta			DATE)
AS
BEGIN
  DECLARE	vl_count_1		NUMBER := 0;
			vl_count_2		NUMBER := 0;
			vl_count_total	NUMBER := 0;
			vl_cod_tipo_deuda	VARCHAR2(10);
			vc_cod_boleta	NUMBER;
			vl_i			NUMBER := 0;
			vl_cod_plan_pago_err	NUMBER;
			vl_concepto_cobro_1		NUMBER := 0;
			vl_concepto_cobro_2		NUMBER := 0;
			vc_cod_tipo_deuda		VARCHAR2(10);
			vc_concepto_cobro_1		NUMBER := 0;
			vc_concepto_cobro_2		NUMBER := 0;
			vl_cod_boleta_err		NUMBER := 0;
			vl_cod_boleta			NUMBER := null;
      		vl_count_temp			 NUMBER := null;

	CURSOR C_COD_BOLETA_A (vc_cod_tipo_deuda VARCHAR2, vc_concepto_cobro_1 NUMBER, vc_concepto_cobro_2 NUMBER) IS
		SELECT	B.COD_BOLETA 
		FROM	BOLETA B, PLAN_PAGO PP, DEUDA DE
		WHERE	To_Char(B.FECHA_BOLETA,'YYYY') = To_Char(SYSDATE,'YYYY') 
		  AND	B.CARTERA_CONOCIDA = 'S'	
		  AND	B.COD_DEUDA = DE.COD_DEUDA
		  AND	DE.COD_TIPO_DEUDA = vc_cod_tipo_deuda
		  AND	B.COD_PLAN_PAGO = PP.COD_PLAN_PAGO 
		  AND	PP.COD_CONCEPTO_COBRO = vc_concepto_cobro_1
		UNION
		SELECT	B.COD_BOLETA 
		FROM	BOLETA B, PLAN_PAGO PP, DEUDA DE
		WHERE	To_Char(B.FECHA_BOLETA,'YYYY') = To_Char(SYSDATE,'YYYY')
		  AND	B.CARTERA_CONOCIDA = 'S'	
		  AND	B.COD_DEUDA = DE.COD_DEUDA
		  AND	DE.COD_TIPO_DEUDA = vc_cod_tipo_deuda
		  AND	B.COD_PLAN_PAGO = PP.COD_PLAN_PAGO 
		  AND	PP.COD_CONCEPTO_COBRO = vc_concepto_cobro_2;
		  
	CURSOR C_COD_BOLETA_B (vc_cod_tipo_deuda VARCHAR2, vc_concepto_cobro_1 NUMBER) IS
		SELECT	B.COD_BOLETA 
		FROM	BOLETA B, PLAN_PAGO PP, DEUDA DE
		WHERE	To_Char(B.FECHA_BOLETA,'YYYY') = To_Char(SYSDATE,'YYYY')
		  AND	B.CARTERA_CONOCIDA = 'S'	
		  AND	B.COD_DEUDA = DE.COD_DEUDA
		  AND	DE.COD_TIPO_DEUDA = vc_cod_tipo_deuda
		  AND	B.COD_PLAN_PAGO = PP.COD_PLAN_PAGO 
		  AND	(vc_concepto_cobro_1 = 0 OR PP.COD_CONCEPTO_COBRO = vc_concepto_cobro_1)
		ORDER BY B.COD_BOLETA ASC;
  			
	BEGIN-- declara tabla temporal
	
		DELETE TEMP_BOLETA_CUPONERA;
		commit;
		
		IF (ve_cod_tipo_deuda = 'CS_5-CS_35') OR (ve_cod_tipo_deuda = 'CSR_5-CSR_35') THEN
			IF (ve_cod_tipo_deuda = 'CS_5-CS_35') THEN
				vl_cod_tipo_deuda := 'CS';
				vl_concepto_cobro_1 := 1;	-- 5%
				vl_concepto_cobro_2 := 3;	-- 3,5-UTM
			ELSIF(ve_cod_tipo_deuda = 'CSR_5-CSR_35') THEN
				vl_cod_tipo_deuda := 'CSR';
				vl_concepto_cobro_1 := 1;	--5%
				vl_concepto_cobro_2 := 3;	--3,5-UTM
			END IF;

			---BEGIN CURSOR
			SELECT	COUNT(*)
			INTO	vl_count_1 
			FROM	BOLETA B, PLAN_PAGO PP, DEUDA DE
			WHERE	To_Char(B.FECHA_BOLETA,'YYYY') = To_Char(SYSDATE,'YYYY')
			  AND	B.CARTERA_CONOCIDA = 'S'	
			  AND	B.COD_DEUDA = DE.COD_DEUDA
			  AND	DE.COD_TIPO_DEUDA = vl_cod_tipo_deuda
			  AND	B.COD_PLAN_PAGO = PP.COD_PLAN_PAGO 
			  AND	PP.COD_CONCEPTO_COBRO = vl_concepto_cobro_1;
			
			SELECT	COUNT(*)
			INTO	vl_count_2
			FROM	BOLETA B, PLAN_PAGO PP, DEUDA DE
			WHERE	To_Char(B.FECHA_BOLETA,'YYYY') = To_Char(SYSDATE,'YYYY')
			  AND	B.CARTERA_CONOCIDA = 'S'	
			  AND	B.COD_DEUDA = DE.COD_DEUDA
			  AND	DE.COD_TIPO_DEUDA = vl_cod_tipo_deuda
			  AND	B.COD_PLAN_PAGO = PP.COD_PLAN_PAGO 
			  AND	PP.COD_CONCEPTO_COBRO = vl_concepto_cobro_2;
			
			vl_count_total := vl_count_1 + vl_count_2;
			
			spu_ejecucion_proceso ('SET_TOTAL', ve_cod_ejecucion_proceso, vl_count_total);
			vl_i := 1;

			FOR b IN C_COD_BOLETA_A (vl_cod_tipo_deuda, vl_concepto_cobro_1, vl_concepto_cobro_2) LOOP
				vl_cod_boleta_err := b.COD_BOLETA;
				
				INSERT INTO	TEMP_BOLETA_CUPONERA
					 SELECT	COD_BOLETA
					   FROM	BOLETA
					  WHERE	COD_BOLETA = b.COD_BOLETA
						AND	CARTERA_CONOCIDA = 'S';
			
				spu_ejecucion_proceso ('SET_ACTUAL', ve_cod_ejecucion_proceso, vl_i);
				vl_i := vl_i + 1;
			END LOOP;

			vl_i := vl_i - 1;
			spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, vl_i);
			COMMIT;
		--IF @ve_cod_tipo_deuda = 'CSR_5-CSR_35' OR @ve_cod_tipo_deuda = 'CSR_5-CSR_35'
		ELSIF (ve_cod_tipo_deuda = 'CS_20') THEN
			vl_cod_tipo_deuda := 'CS';
			vl_concepto_cobro_1 := 4;	-- 20%
		
			---PARA TODAS LAS BOLETAS (@ve_cod_tipo_deuda, @vl_concepto_cobro)
			SELECT	COUNT(*)
			INTO	vl_count_1
			FROM	BOLETA B, PLAN_PAGO PP, DEUDA DE
			WHERE	To_Char(B.FECHA_BOLETA,'YYYY') = To_Char(SYSDATE,'YYYY')
			  AND	B.CARTERA_CONOCIDA = 'S'	
			  AND	B.COD_DEUDA = DE.COD_DEUDA
			  AND	DE.COD_TIPO_DEUDA = vl_cod_tipo_deuda
			  AND	B.COD_PLAN_PAGO = PP.COD_PLAN_PAGO 
			  AND	(vl_concepto_cobro_1 = 0 OR PP.COD_CONCEPTO_COBRO = vl_concepto_cobro_1);
			
			vl_count_total := vl_count_1 + vl_count_2;
			
			spu_ejecucion_proceso ('SET_TOTAL', ve_cod_ejecucion_proceso, vl_count_total);
			vl_i := 1;

			FOR c IN C_COD_BOLETA_B (ve_cod_tipo_deuda, vl_concepto_cobro_1) LOOP
				vl_cod_boleta_err := c.COD_BOLETA;
				
				INSERT INTO	TEMP_BOLETA_CUPONERA
					 SELECT	COD_BOLETA
					   FROM	BOLETA
					  WHERE	COD_BOLETA = c.COD_BOLETA
					    AND	CARTERA_CONOCIDA = 'S';
	
				spu_ejecucion_proceso ('SET_ACTUAL', ve_cod_ejecucion_proceso, vl_i);
				vl_i := vl_i + 1;
			END LOOP;
			vl_i := vl_i - 1;
			spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, vl_i);	
			COMMIT;
			
			--IF (@ve_cod_tipo_deuda = 'CS_20')
		ELSE
		
		
			---PARA TODAS LAS BOLETAS (@ve_cod_tipo_deuda, @vl_concepto_cobro)
			SELECT	COUNT(*)
			INTO	vl_count_1
			FROM	BOLETA B, PLAN_PAGO PP, DEUDA DE
			WHERE	To_Char(B.FECHA_BOLETA,'YYYY') = To_Char(SYSDATE,'YYYY')
			  AND	B.CARTERA_CONOCIDA = 'S'	
			  AND	B.COD_DEUDA = DE.COD_DEUDA
			  AND	DE.COD_TIPO_DEUDA = ve_cod_tipo_deuda
			  AND	B.COD_PLAN_PAGO = PP.COD_PLAN_PAGO 
			  AND	(vl_concepto_cobro_1 = 0 OR PP.COD_CONCEPTO_COBRO = vl_concepto_cobro_1);
			  
		
			vl_count_total := vl_count_1 + vl_count_2;
			
			spu_ejecucion_proceso ('SET_TOTAL', ve_cod_ejecucion_proceso, vl_count_total);
			
			vl_i := 1;

			FOR b IN C_COD_BOLETA_B (ve_cod_tipo_deuda, vl_concepto_cobro_1) LOOP
				vl_cod_boleta_err := b.COD_BOLETA;
				
				SELECT	COD_BOLETA
				INTO	vl_cod_boleta
				FROM	BOLETA
				WHERE	COD_BOLETA = vl_cod_boleta_err
				AND	CARTERA_CONOCIDA = 'S';
				
				INSERT INTO TEMP_BOLETA_CUPONERA VALUES(vl_cod_boleta);
				
        		spu_ejecucion_proceso ('SET_ACTUAL', ve_cod_ejecucion_proceso, vl_i);
				vl_i := vl_i + 1;
			END LOOP;
			vl_i := vl_i - 1;
			spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, vl_i);
			COMMIT;
		END IF;---ELSE 
		COMMIT;
	EXCEPTION
		WHEN OTHERS THEN
			-- Si ocurre un error de la BD al grabar un registro lo registra en el LOG. del sistema
			spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', SQLCODE, SUBSTR(SQLERRM,1,100), 'Boleta: ' || To_Char(vl_cod_boleta_err));
			spu_ejecucion_proceso('END', ve_cod_ejecucion_proceso, vl_i);
		END;
END;
/
