CREATE OR REPLACE PROCEDURE spu_documento_nuevo(ve_operacion				VARCHAR2
											, ve_cod_documento_nuevo		NUMBER
											, ve_cod_cambio_documento		NUMBER
											, ve_cod_documento_pago			NUMBER
											, ve_cod_tipo_documento_pago	NUMBER
											, ve_nro_documento_pago			NUMBER
											, ve_cod_banco					NUMBER
											, ve_cod_plaza					NUMBER			
											, ve_fecha_documento_pago		DATE
											, ve_monto						NUMBER
											, ve_cod_deudor					NUMBER
											, ve_cod_familia_deuda			NUMBER
											, ve_cod_usuario				NUMBER
											)
AS
BEGIN
	DECLARE vl_cod_doc_pago			NUMBER;
			K_ESTADO_POR_DEPOSITAR	NUMBER:= 1;
			K_ESTADO_DEPOSITADO		  NUMBER:= 2;
			K_DEPOSITO				      NUMBER:= 3;
			K_TRANSBANK				      NUMBER:= 6;
			K_AJUSTE_SALDO			    NUMBER:= 99;
			vl_cod_estado_documento_pago	NUMBER;
			vl_nro_documento_pago_old		NUMBER;
			vl_cod_banco_old				NUMBER;
			vl_cod_plaza_old				NUMBER;
			vl_fecha_documento_pago_old		DATE;
			vl_log_cambio_sq				NUMBER;
			
	BEGIN
	
		IF(ve_cod_tipo_documento_pago = K_DEPOSITO or ve_cod_tipo_documento_pago = K_TRANSBANK or ve_cod_tipo_documento_pago = K_AJUSTE_SALDO) THEN
			vl_cod_estado_documento_pago := K_ESTADO_DEPOSITADO;
		ELSE
			vl_cod_estado_documento_pago := K_ESTADO_POR_DEPOSITAR;
		END IF;
	
		IF (ve_operacion = 'INSERT') THEN
			spu_documento_pago('INSERT'
									, NULL
									, vl_cod_estado_documento_pago
									, ve_cod_tipo_documento_pago
									, ve_nro_documento_pago
									, ve_cod_banco					
									, ve_cod_plaza					
									, ve_fecha_documento_pago		
									, ve_monto						
									, ve_cod_deudor				
									, ve_cod_familia_deuda);	
	
			SELECT documento_pago_sq.CURRVAL
			INTO vl_cod_doc_pago
			FROM dual;
			
			INSERT INTO documento_nuevo
	                  (cod_documento_nuevo
                    ,cod_cambio_documento
	                  ,cod_documento_pago)
	           VALUES (documento_nuevo_sq.NEXTVAL
	           		 , ve_cod_cambio_documento
	                 , vl_cod_doc_pago);
		ELSIF(ve_operacion = 'UPDATE') THEN
			/*este codigo tenia por default
			 * UPDATE	documento_nuevo
	        SET		cod_documento_pago = ve_cod_documento_pago
	       WHERE	cod_documento_nuevo = ve_cod_documento_nuevo;*/
			---SE REALIZA UPDATE EN documento_pago.
			
			--DATOS CAMBIADOS 
			SELECT	 nro_documento_pago
					,cod_banco
					,cod_plaza
					,fecha_documento_pago
			INTO	 vl_nro_documento_pago_old
					,vl_cod_banco_old
					,vl_cod_plaza_old
					,vl_fecha_documento_pago_old
			FROM DOCUMENTO_PAGO
			WHERE COD_DOCUMENTO_PAGO =  ve_cod_documento_pago;
			
			UPDATE documento_pago
			SET  nro_documento_pago = ve_nro_documento_pago
                ,cod_banco = ve_cod_banco
				,cod_plaza = ve_cod_plaza
				,fecha_documento_pago = ve_fecha_documento_pago
			WHERE COD_DOCUMENTO_PAGO =  ve_cod_documento_pago;
			
			--para registrar log_cambio
			IF (vl_nro_documento_pago_old <> ve_nro_documento_pago) THEN
				sp_log_cambio ('documento_pago', to_char(ve_cod_documento_pago), ve_cod_usuario, 'U');
				--obtengo el ultimo insert 
				SELECT log_cambio_sq.CURRVAL
				INTO vl_log_cambio_sq
				FROM dual;
				
				sp_detalle_cambio_relacionada (vl_log_cambio_sq, 'documento_pago', 'nro_documento_pago'
												, to_char(ve_cod_documento_pago)
												, to_char(vl_nro_documento_pago_old)
												, to_char(ve_nro_documento_pago), 'U');
		
			ELSIF(vl_cod_banco_old <> ve_cod_banco) THEN
				sp_log_cambio ('documento_pago', to_char(ve_cod_documento_pago), ve_cod_usuario, 'U');
				--obtengo el ultimo insert 
				SELECT log_cambio_sq.CURRVAL
				INTO vl_log_cambio_sq
				FROM dual;
				
				sp_detalle_cambio_relacionada (vl_log_cambio_sq, 'documento_pago', 'cod_banco'
												, to_char(ve_cod_documento_pago)
												, to_char(vl_cod_banco_old)
												, to_char(ve_cod_banco), 'U');
			
			ELSIF(vl_cod_plaza_old <> ve_cod_plaza) THEN
				sp_log_cambio ('documento_pago', to_char(ve_cod_documento_pago), ve_cod_usuario, 'U');
				--obtengo el ultimo insert 
				SELECT log_cambio_sq.CURRVAL
				INTO vl_log_cambio_sq
				FROM dual;
				sp_detalle_cambio_relacionada (vl_log_cambio_sq, 'documento_pago', 'cod_plaza'
												, to_char(ve_cod_documento_pago)
												, to_char(vl_cod_plaza_old)
												, to_char(ve_cod_plaza), 'U');
			
			ELSIF(vl_fecha_documento_pago_old <> ve_fecha_documento_pago) THEN
				sp_log_cambio ('documento_pago', to_char(ve_cod_documento_pago), ve_cod_usuario, 'U');
				--obtengo el ultimo insert 
				SELECT log_cambio_sq.CURRVAL
				INTO vl_log_cambio_sq
				FROM dual;
				
				sp_detalle_cambio_relacionada (vl_log_cambio_sq, 'documento_pago', 'fecha_documento_pago'
												, to_char(ve_cod_documento_pago)
												, to_char(vl_fecha_documento_pago_old,'dd/mm/yyyy')
												, to_char(ve_fecha_documento_pago,'dd/mm/yyyy'), 'U');
			END IF;
			
		END IF;
	END;
END;
/