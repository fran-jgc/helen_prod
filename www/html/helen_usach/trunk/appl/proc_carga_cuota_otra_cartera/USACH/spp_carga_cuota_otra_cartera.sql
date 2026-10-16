PROMPT CREATE OR REPLACE PROCEDURE spp_carga_cuota_otra_cartera
CREATE OR REPLACE PROCEDURE spp_carga_cuota_otra_cartera (ve_cod_ejecucion_proceso NUMBER)
AS
BEGIN
	DECLARE	
			vl_cod_deuda			NUMBER;
			vl_max_cuota			NUMBER;
			vl_nro_cuota			NUMBER;
			K_COBRANZA_NORMAL		NUMBER := 2;
			K_TIPO_DEUDA_CS			VARCHAR2(2) := 'CS'; 
			K_ESTADO_PP_EMITIDA		NUMBER := 1;
			i						NUMBER;
			vl_cod_usuario			NUMBER;
			vl_count				NUMBER;
			vl_rut_error			NUMBER;
			vl_mensaje_error		VARCHAR2(100);
			vl_count_deuda_cs_exigible	NUMBER;
			vl_count_deuda_cartera_defecto	NUMBER;
			vl_count_deuda_cruzada			NUMBER;
			vl_count_pp				NUMBER;
			vl_count_deudor			NUMBER;
			KL_CARTERA_DEFECTO		NUMBER := 2;

		CURSOR C_CARGA_CUOTA_CARTERA   IS
			SELECT COD_CARGA_CUOTA_OTRA_CARTERA
			      ,RUT
			      ,DIG_VERIF
			      ,MONTO_CAPITAL
			      ,MONTO_INTERES
			      ,FECHA_VENCTO
			      ,COD_CONCEPTO_COBRO
			      ,ANO_COBRO
			      ,FECHA_CARGA
			FROM CARGA_CUOTA_OTRA_CARTERA
			ORDER BY  COD_CARGA_CUOTA_OTRA_CARTERA ASC;

	BEGIN
		-- usuario que ejecuta el proceso
		SELECT	cod_usuario
		INTO	vl_cod_usuario
	    FROM	ejecucion_proceso
	    WHERE	cod_ejecucion_proceso = ve_cod_ejecucion_proceso;
	    
	    SELECT  Count(*)
	    INTO	vl_count
	    FROM	CARGA_CUOTA_OTRA_CARTERA;
	    
		spu_ejecucion_proceso ('SET_TOTAL', ve_cod_ejecucion_proceso, vl_count);
		i	:= 1;
		
		FOR A IN C_CARGA_CUOTA_CARTERA LOOP
			vl_rut_error := A.RUT;
			
			-- verifica si el proceso ha sido cancelado
			IF (f_ep_cancela_proceso (ve_cod_ejecucion_proceso) = 'S') THEN
				RETURN;
			END IF;

			--valida que exista deudor
			SELECT	count(*)
			INTO	vl_count_deudor
			FROM	deudor
			WHERE	rut = A.RUT;

			IF(vl_count_deudor = 0) THEN
				--Variable para mensaje de error.
				vl_mensaje_error := 'RUT: ' || to_char(A.RUT) || '-' || A.DIG_VERIF || ' No existe en Deudor.';
          		spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje_error);
          		
          		GOTO end_loop;
        	END IF;
        	
			--valida que tenga deuda CS y este exigible
			SELECT	count(*)
			INTO	vl_count_deuda_cs_exigible
			FROM	deuda de, deudor do
			WHERE	do.rut = A.RUT
			  AND	do.cod_deudor = de.cod_deudor
			  AND	de.cod_tipo_deuda = 'CS'
			  AND	de.cod_estado_deuda = 2;
			
			IF(vl_count_deuda_cs_exigible = 0) THEN
				--Variable para mensaje de error.
				vl_mensaje_error := 'RUT: ' || to_char(A.RUT) || '-' || A.DIG_VERIF || ' No cumple con deuda tipo CS y Exegible.';
				spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje_error);
				
				GOTO end_loop;
			END IF;
        	
			--valida que deuda se cobra otra universidad
			SELECT	count(*)
			INTO	vl_count_deuda_cartera_defecto
			FROM	deuda de, deudor do
			WHERE	do.rut = A.RUT
			  AND	do.cod_deudor = de.cod_deudor
			  AND	de.cod_tipo_deuda = 'CS'
			  AND	de.cod_estado_deuda = 2
			  AND	de.cod_cartera <> f_get_parametro(KL_CARTERA_DEFECTO);
			
			IF(vl_count_deuda_cartera_defecto = 0) THEN
				--Variable para mensaje de error.
				vl_mensaje_error := 'RUT: ' || to_char(A.RUT) || '-' || A.DIG_VERIF || ', Deuda cobra la misma Universidad.';
				spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje_error);
				
			  	GOTO end_loop;
			END IF;
			
			--valida que deuda CS y sea cruzada
			IF(vl_count_deuda_cartera_defecto > 1) THEN
				--de aqui sale la deuda mas actual. por pagar que ademas sea cruzada 
				SELECT	de.cod_deuda
				INTO	vl_cod_deuda
				FROM	deuda de, deudor do
				WHERE	do.rut = A.RUT
				  AND	do.cod_deudor = de.cod_deudor
				  AND	de.cod_tipo_deuda = 'CS'
				  AND	de.cod_estado_deuda = 2
				  AND	de.cod_cartera <> f_get_parametro(KL_CARTERA_DEFECTO)
				  AND	ROWNUM = 1
				ORDER BY de.fecha_inicio_cobro	desc;
			ELSE
					--obtiene la deuda
        			SELECT	de.cod_deuda
        			INTO	vl_cod_deuda 
		        	FROM	deuda de, deudor do
		        	WHERE	do.rut = A.RUT
		        	  AND	do.cod_deudor = de.cod_deudor
		        	  AND	de.cod_tipo_deuda = 'CS'
		        	  AND	de.cod_estado_deuda = 2
		        	  AND	de.cod_cartera <> f_get_parametro(KL_CARTERA_DEFECTO);
			END IF;

			--valida que la deuda sea cruzada
			SELECT	count(*)
			INTO	vl_count_deuda_cruzada
			FROM	cruzado
			WHERE	cod_deuda = vl_cod_deuda;
			
			IF(vl_count_deuda_cruzada = 0) THEN
				--Variable para mensaje de error.
				vl_mensaje_error := 'RUT: ' || to_char(A.RUT) || '-' || A.DIG_VERIF || ' No tiene Deuda Cruzada.';
				spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje_error);
				
			  	GOTO end_loop;
			END IF;
			
			--verifica si existe PP asociado para el año a cargar
			SELECT  Count(*)
			INTO	vl_count_pp
			FROM    plan_pago
			WHERE   cod_deuda = vl_cod_deuda
			AND		ano_cobro = A.ANO_COBRO;
			
			IF(vl_count_pp > 0) THEN
				--Variable para mensaje de error.
				vl_mensaje_error := 'RUT: ' || to_char(A.RUT) || '-' || A.DIG_VERIF || ' tiene Deuda con cuota ya Creada.';
				spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje_error);
				
				GOTO end_loop;
			END IF;
        	
			--se obtiene la cuota a crear
			SELECT  NVL(max(nro_cuota),0)
			INTO	vl_nro_cuota 
			FROM    plan_pago
			WHERE   cod_deuda = vl_cod_deuda
			AND     nro_cuota <> 99;					
			
			vl_nro_cuota := vl_nro_cuota + 1;
			
			-- llamar crea la cuota para la carga otra cartera
			spu_plan_pago('INSERT'
						 ,NULL
						 ,vl_cod_deuda
						 ,vl_nro_cuota
						 ,A.ANO_COBRO
						 ,A.MONTO_CAPITAL
						 ,A.MONTO_INTERES
						 ,A.FECHA_VENCTO
						 ,NULL
						 ,K_ESTADO_PP_EMITIDA
						 ,NULL
						 ,NULL);

			<<end_loop>>
			NULL; -- add NULL statement to avoid error
			-- actualiza avance
			spu_ejecucion_proceso ('SET_ACTUAL', ve_cod_ejecucion_proceso, i);
			i := i + 1;
		END LOOP;
		
		i := i - 1;
		spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, i);
		
	EXCEPTION
        WHEN OTHERS THEN
			-- Si ocurre un error de la BD al grabar un registro lo registra en el LOG. del sistema
			spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', SQLCODE, SQLERRM, 'Rut Deudor : ' || To_Char(vl_rut_error));
			spu_ejecucion_proceso('END', ve_cod_ejecucion_proceso, i-1);
        COMMIT;      
	END;
END spp_carga_cuota_otra_cartera;
/