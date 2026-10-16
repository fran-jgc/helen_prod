PROMPT CREATE OR REPLACE PROCEDURE spp_cb_abono_descto
CREATE OR REPLACE PROCEDURE spp_cb_abono_descto(ve_cod_ejecucion_proceso number)
AS
BEGIN

	declare vl_cod_usuario				  NUMBER;
          vl_count_registros      NUMBER;
          vl_i                    NUMBER;
          vl_count_deudor         NUMBER;
          vl_mensaje              VARCHAR2(100);
          vl_cod_deudor           NUMBER;
          vl_cant_deuda           NUMBER;
          vl_cod_deuda            NUMBER;
          vl_cant_plan_pago       NUMBER;
          vl_cod_plan_pago        NUMBER;

	CURSOR c_cursor IS
		select	COD_CB_ABONO_DESCTOS
				,TIPO_PAGO           
			    ,RUT                   
			    ,ANO_COBRO             
			    ,NRO_CUOTA             
			    ,FECHA_PAGO            
			    ,PORC_CRUZADO          
			    ,CAPITAL_UTM_REND      
			    ,INTERES_PENAL_REND    
			    ,TOTAL_UTM_REND        
			    ,TOTAL_PESOS_REND
			    ,COD_TIPO_DEUDA
		from CB_ABONO_DESCTOS
		WHERE   CARGADO = 'N'
		AND     PROCESADO = 'N';

  BEGIN
	-- usuario que ejecuta el proceso
	SELECT	cod_usuario
  	INTO    vl_cod_usuario
	FROM	  ejecucion_proceso
	WHERE	  cod_ejecucion_proceso = ve_cod_ejecucion_proceso;

	-- calcula el total de registros
	SELECT  Count(*)
  	INTO    vl_count_registros
	FROM	  CB_ABONO_DESCTOS;

	spu_ejecucion_proceso ('SET_TOTAL', ve_cod_ejecucion_proceso, vl_count_registros);
  	vl_i := 1;


  FOR a IN c_cursor LOOP
 	  -- verifica si el proceso ha sido cancelado
		IF (f_ep_cancela_proceso(ve_cod_ejecucion_proceso) = 'S')THEN
			RETURN;
    	END IF;

		-- valida que exista deudor
		SELECT	count(*)
    	INTO    vl_count_deudor
		FROM	  deudor
		WHERE   rut = a.rut;

		IF(vl_count_deudor = 0)THEN
			vl_mensaje := 'El rut: '||to_char(a.rut)||' No EXISTE';
			spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
		ELSE
			SELECT  cod_deudor
			INTO vl_cod_deudor
	        FROM    deudor
	        WHERE   rut = a.RUT;
	        
	        	--valida que el deudor tenga deuda en familia solidario
			SELECT count(*)
			INTO vl_cant_deuda
	        FROM deuda
	        WHERE cod_deudor = vl_cod_deudor
	        AND cod_tipo_deuda = a.cod_tipo_deuda;

			IF(vl_cant_deuda = 0)THEN
				vl_mensaje := 'El rut: '||to_char(a.rut)||' No Posee deudas ' ||to_char(a.COD_TIPO_DEUDA);
				spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
			ELSE
				SELECT COD_DEUDA
				INTO vl_cod_deuda
		        FROM deuda
		        WHERE cod_deudor = vl_cod_deudor
		        AND cod_tipo_deuda = a.cod_tipo_deuda;
		        
		        --valida que exista el plan pago que esta pagando y que no este estado pagado
		        SELECT count(*)
		        INTO vl_cant_plan_pago
		        FROM PLAN_PAGO
		        WHERE COD_DEUDA = vl_cod_deuda
		        AND NRO_CUOTA = a.nro_cuota
		        AND ANO_COBRO = a.ano_cobro;
		        
		        IF(vl_cant_plan_pago = 0)then
			       	vl_mensaje := 'La cuota Nº'||to_char(a.nro_cuota)||' de la deuda '|| to_char(vl_cod_deuda) ||' no existe';
            		spu_error_proceso ('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, vl_mensaje);
		        else
		        	IF(a.tipo_pago = 'ABONO')then
				        INSERT INTO ABONO (COD_ABONO
				        				  ,FECHA_REGISTRO
										  ,FECHA_ABONO
										  ,COD_USUARIO
										  ,COD_COMPONENTE_DEUDA
										  ,MONTO_CAPITAL
										  ,MONTO_INTERES
										  ,MONTO_MULTA
										  ,MONTO_MONEDA
										  ,MONTO_PESOS
										  ,DIFERENCIA_PESOS
										  ,COD_PLAN_PAGO
										  ,COD_DEUDA
										  ,COD_ESTADO_PAGO
										  ,COD_ORIGEN_PAGO) 
									VALUES(abono_sq.nextval
										  ,sysdate
										  ,a.fecha_pago
										  ,vl_cod_usuario
										  ,3 --VIGENTE
										  ,a.capital_utm_rend
										  ,a.interes_penal_rend
										  ,0
										  ,a.total_utm_rend
										  ,a.total_pesos_rend
										  ,0
										  ,vl_cod_plan_pago
										  ,vl_cod_deuda
										  ,1
										  ,22);
										  
						sp_pp_actualiza_estado(vl_cod_plan_pago);
										  
		       		ELSIF(a.tipo_pago = 'DSCTO')THEN
			       		INSERT INTO DESCUENTO (COD_DESCUENTO
			       							  ,FECHA_REGISTRO
											  ,FECHA_DESCUENTO
											  ,COD_USUARIO
											  ,COD_COMPONENTE_DEUDA
											  ,MONTO_CAPITAL
											  ,MONTO_INTERES
											  ,MONTO_MULTA
											  ,MONTO_MONEDA
											  ,MONTO_PESOS
											  ,COD_PLAN_PAGO
											  ,COD_DEUDA
											  ,COD_ESTADO_PAGO
											  ,COD_ORIGEN_PAGO)
										VALUES(descuento_sq.nextval
											  ,sysdate
											  ,a.fecha_pago
											  ,vl_cod_usuario
											  ,3 --VIGENTE
											  ,a.capital_utm_rend
											  ,a.interes_penal_rend
											  ,0
											  ,a.total_utm_rend
											  ,a.total_pesos_rend
											  ,vl_cod_plan_pago
											  ,vl_cod_deuda
											  ,1
											  ,22);
		       		END IF;	
		        end if;
				
          	END IF;
       
		
    	END IF;
  		-- actualiza avance
		spu_ejecucion_proceso ('SET_ACTUAL', ve_cod_ejecucion_proceso, vl_i);
		vl_i := vl_i + 1;
 	END LOOP;

  vl_i := vl_i -1;
  spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, vl_i);
 
  EXCEPTION
      WHEN OTHERS THEN
	    -- Si ocurre un error de la BD al grabar un registro lo registra en el LOG. del sistema
			spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', SQLCODE, SUBSTR(SQLERRM,1,100), 'spp_ucm_cb_tgr: plan_pago: ' || To_Char(vl_cod_plan_pago));
			spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, vl_i);
  
  END;
END;
/

