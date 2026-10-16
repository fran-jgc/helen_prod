CREATE OR REPLACE PROCEDURE spu_cuota_pago_inicial(ve_operacion	VARCHAR2
													,ve_cod_cuota_pago_inicial		NUMBER	:= NULL
													,ve_cod_reprogramacion	NUMBER	:= NULL
													,ve_nro_cuota		NUMBER	:= NULL
													,ve_fecha_vencimiento DATE	:= NULL
													,ve_monto_cuota	NUMBER	:= NULL)											
AS
BEGIN
	IF (ve_operacion = 'INSERT')THEN
	INSERT INTO CUOTA_PAGO_INICIAL(COD_CUOTA_PAGO_INICIAL
								  ,COD_REPROGRAMACION 
            					  ,NRO 
            					  ,FECHA_VCTO
            					  ,MONTO
								  )
								  VALUES(cuota_pago_inicial_sq.NEXTVAL
								  ,ve_cod_reprogramacion
            					  ,ve_nro_cuota 
            					  ,ve_fecha_vencimiento
            					  ,ve_monto_cuota
								  );
	ELSIF (ve_operacion = 'UPDATE')THEN
		UPDATE CUOTA_PAGO_INICIAL
		SET		COD_REPROGRAMACION	= ve_cod_reprogramacion
            	,NRO				= ve_nro_cuota 
            	,FECHA_VCTO			= ve_fecha_vencimiento
            	,MONTO				= ve_monto_cuota
		WHERE COD_CUOTA_PAGO_INICIAL = ve_cod_cuota_pago_inicial;	
	
	ELSIF (ve_operacion = 'DELETE') THEN
		DELETE FROM CUOTA_PAGO_INICIAL
        WHERE COD_CUOTA_PAGO_INICIAL = ve_cod_cuota_pago_inicial;
    END IF;
END; 