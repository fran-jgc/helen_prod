CREATE OR REPLACE PROCEDURE spu_reprogramacion( ve_operacion	VARCHAR2
											,ve_cod_reprogramacion NUMBER := NULL
											,ve_fecha_reprogramacion DATE := NULL
											,ve_cod_usuario NUMBER := NULL
											,ve_cod_estado_reprogramacion NUMBER := NULL
											,ve_doc_pago_inicial VARCHAR2 := NULL
											,ve_doc_pagares VARCHAR2 := NULL
											,ve_doc_manifiesto VARCHAR2 := NULL
											,ve_cod_deuda_crpi NUMBER := NULL
											,ve_cod_deuda_cr12 NUMBER := NULL
											,ve_k_cliente IN VARCHAR := NULL)
as 
BEGIN
	DECLARE vl_cod_estado_old NUMBER;
			KL_ESTADO_EMITIDO NUMBER;
			KL_ESTADO_CONFIRMADO NUMBER;
			KL_ESTADO_ANULADO NUMBER;
			vl_doc_pagares VARCHAR2(1);
			vl_doc_manifiesto VARCHAR2(1);
			vl_pago_inicial VARCHAR2(1);
	
	BEGIN
		KL_ESTADO_EMITIDO		:=1; 
		KL_ESTADO_CONFIRMADO	:=2;
		KL_ESTADO_ANULADO		:=3;
		
		IF (ve_operacion = 'INSERT')THEN
		INSERT INTO REPROGRAMACION(	 COD_REPROGRAMACION
									,FECHA_REPROGRAMACION
									,COD_USUARIO
									,COD_ESTADO_REPROGRAMACION
									,DOC_PAGO_INICIAL
									,DOC_PAGARES
									,DOC_MANIFIESTO
									,COD_DEUDA_CRPI
									,COD_DEUDA_CR12
									,K_CLIENTE)
							VALUES(  reprogramacion_sq.NEXTVAL
									,SYSDATE
									,ve_cod_usuario
									,ve_cod_estado_reprogramacion 
									,ve_doc_pago_inicial
									,ve_doc_pagares
									,ve_doc_manifiesto
									,ve_cod_deuda_crpi
									,ve_cod_deuda_cr12
									,ve_k_cliente);
		ELSIF (ve_operacion = 'UPDATE')THEN
			SELECT	COD_ESTADO_REPROGRAMACION
			INTO	vl_cod_estado_old
			FROM	REPROGRAMACION
			WHERE	COD_REPROGRAMACION = ve_cod_reprogramacion;
			
			UPDATE	REPROGRAMACION
			SET		 DOC_PAGARES			= ve_doc_pagares
					,DOC_MANIFIESTO			= ve_doc_manifiesto
					,K_CLIENTE				= ve_k_cliente
			WHERE	COD_REPROGRAMACION = ve_cod_reprogramacion;
			
			IF (vl_cod_estado_old = KL_ESTADO_EMITIDO and ve_cod_estado_reprogramacion = KL_ESTADO_CONFIRMADO)THEN
				UPDATE	REPROGRAMACION
				SET		 COD_ESTADO_REPROGRAMACION	= ve_cod_estado_reprogramacion
						 ,K_CLIENTE			= ve_k_cliente
				WHERE	COD_REPROGRAMACION = ve_cod_reprogramacion;
			
				--consulta si esta todo OK. (recepcion manifiesto y pagares)
				SELECT	 DOC_PAGARES
						,DOC_MANIFIESTO
						,DOC_PAGO_INICIAL
				INTO	vl_doc_pagares
						,vl_doc_manifiesto
						,vl_pago_inicial
				FROM	REPROGRAMACION
				WHERE	COD_REPROGRAMACION = ve_cod_reprogramacion;
				
				IF(vl_doc_pagares = 'S' and vl_doc_manifiesto = 'S' and vl_pago_inicial = 'S') THEN
					spu_reprogramacion ('CONFIRMA_REPROGRAMACION', ve_cod_reprogramacion);
				END IF;
			ELSIF (vl_cod_estado_old = KL_ESTADO_EMITIDO and ve_cod_estado_reprogramacion = KL_ESTADO_ANULADO) THEN
				UPDATE REPROGRAMACION
				SET COD_ESTADO_REPROGRAMACION = KL_ESTADO_ANULADO
					,K_CLIENTE			= ve_k_cliente
				WHERE COD_REPROGRAMACION = ve_cod_reprogramacion;
				
				sp_deuda_repro_crpi ('ANULA', ve_cod_reprogramacion);
				
				/**crea bitacora de anulacion Repro.****/
				spu_log_reprogramacion (ve_cod_reprogramacion, ve_cod_usuario, 'REPROGRAMACION','cod_reprogramacion','Anulacion de Reprogramacion. Via Sistema Helen');					                               
			END IF;
		ELSIF (ve_operacion = 'CONFIRMA_PAGO_INICIAL')THEN
			UPDATE	REPROGRAMACION
			SET		DOC_PAGO_INICIAL	= 'S'
					,FECHA_PAGO_INICIAL = SYSDATE
					,K_CLIENTE			= ve_k_cliente
			WHERE COD_REPROGRAMACION = ve_cod_reprogramacion;
			
			--consulta si esta todo OK. (recepcion manifiesto y pagares)
			SELECT	 DOC_PAGARES
					,DOC_MANIFIESTO
			INTO	 vl_doc_pagares
					,vl_doc_manifiesto
			FROM	REPROGRAMACION
			WHERE	COD_REPROGRAMACION = ve_cod_reprogramacion;
			
			IF(vl_doc_pagares = 'S' and vl_doc_manifiesto = 'S') THEN
					spu_reprogramacion ('CONFIRMA_REPROGRAMACION', ve_cod_reprogramacion);
			END IF;
		ELSIF (ve_operacion = 'CANCELA_ABONO_PAGO_INICIAL')THEN
			UPDATE	REPROGRAMACION
			SET		COD_ESTADO_REPROGRAMACION	= KL_ESTADO_EMITIDO
					,DOC_PAGO_INICIAL	= 'N'
					,FECHA_PAGO_INICIAL = NULL
					,K_CLIENTE			= ve_k_cliente
			WHERE COD_REPROGRAMACION = ve_cod_reprogramacion;
		
	    ELSIF (ve_operacion = 'CONFIRMA_REPROGRAMACION')THEN
				    UPDATE	REPROGRAMACION
				    SET		COD_ESTADO_REPROGRAMACION	= KL_ESTADO_CONFIRMADO --CONFIRMADA
						    ,FECHA_CONFIRMA = SYSDATE
						    ,INFORMADO_MINEDUC = 'S'
						    ,K_CLIENTE			= ve_k_cliente
				    WHERE	COD_REPROGRAMACION = ve_cod_reprogramacion;
						
				    sp_deuda_repro_cr12(ve_cod_reprogramacion);
		ELSIF (ve_operacion = 'DELETE')THEN
				DELETE FROM REPROGRAMACION
				WHERE COD_REPROGRAMACION = ve_cod_reprogramacion;
		END IF;	
	END;
END;