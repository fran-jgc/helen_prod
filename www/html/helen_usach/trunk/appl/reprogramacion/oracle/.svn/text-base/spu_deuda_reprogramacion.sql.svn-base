CREATE OR REPLACE PROCEDURE spu_deuda_reprogramacion(ve_operacion	VARCHAR2
													,ve_cod_deuda_reprogramacion  number	:= NULL     
													,ve_cod_reprogramacion        number	:= NULL           
													,ve_cod_universidad_deuda     number	:= NULL          
													,ve_cod_deuda                 number	:= NULL           
													,ve_cod_tipo_deuda            VARCHAR2	:= NULL          
													,ve_saldo_capital             number 	:= NULL		        
													,ve_saldo_interes_penal       number 	:= NULL)
AS
BEGIN
	
	DECLARE K_EN_ESTADO_REPRO NUMBER;
	
	BEGIN
		
		K_EN_ESTADO_REPRO := 20;
		
		IF (ve_operacion = 'INSERT')THEN
			INSERT INTO DEUDA_REPROGRAMACION(COD_DEUDA_REPROGRAMACION
										,COD_REPROGRAMACION 
										,COD_UNIVERSIDAD_DEUDA
										,COD_DEUDA 
										,COD_TIPO_DEUDA 
										,SALDO_CAPITAL 
										,SALDO_INTERES_PENAL 
										)
										 VALUES(deuda_reprogramacion_sq.NEXTVAL
										,ve_cod_reprogramacion
	 									,ve_cod_universidad_deuda 
										,ve_cod_deuda 
										,ve_cod_tipo_deuda 
										,ve_saldo_capital 
										,ve_saldo_interes_penal 
										);
			--SE ACTUALIZA DEUDA A EN_ESTADO_REPRO							
			UPDATE DEUDA
			SET COD_ESTADO_DEUDA = K_EN_ESTADO_REPRO
			WHERE COD_DEUDA = ve_cod_deuda;
		
		ELSIF (ve_operacion = 'UPDATE')THEN
			UPDATE DEUDA_REPROGRAMACION			
			SET		COD_REPROGRAMACION		= ve_cod_reprogramacion
					,COD_UNIVERSIDAD_DEUDA	= ve_cod_universidad_deuda 
					,COD_DEUDA				= ve_cod_deuda   
					,COD_TIPO_DEUDA			= ve_cod_tipo_deuda  
					,SALDO_CAPITAL			= ve_saldo_capital 
					,SALDO_INTERES_PENAL	= ve_saldo_interes_penal 
			WHERE COD_DEUDA_REPROGRAMACION	= ve_cod_deuda_reprogramacion;				
		
		ELSIF (ve_operacion = 'DELETE')THEN
			DELETE FROM DEUDA_REPROGRAMACION
	        WHERE COD_DEUDA_REPROGRAMACION = ve_cod_deuda_reprogramacion;
	    END IF;
	END;
END;