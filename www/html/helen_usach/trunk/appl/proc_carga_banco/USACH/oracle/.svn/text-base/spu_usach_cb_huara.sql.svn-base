PROMPT CREATE OR REPLACE PROCEDURE spu_usach_cb_huara
CREATE OR REPLACE PROCEDURE spu_usach_cb_huara	(ve_operacion                 IN VARCHAR2
											  	,ve_rut						IN NUMBER   :=NULL
												,ve_cod_deuda				IN NUMBER   :=NULL
												,ve_fecha_abono				IN DATE     :=NULL
												,ve_fecha_deposito			IN DATE     :=NULL
												,ve_folio					IN VARCHAR2 :=NULL
												,ve_monto_capital_utm		IN NUMBER   :=NULL
												,ve_monto_int_utm			IN NUMBER   :=NULL
												,ve_monto_int_normal_utm	IN NUMBER   :=NULL
												,ve_monto_pesos				IN NUMBER   :=NULL
												,ve_monto_int_normal_pesos		IN NUMBER   :=NULL
												,ve_monto_int_moratorio_pesos	IN NUMBER   :=NULL
												,ve_monto_descuento_utm		IN NUMBER   :=NULL
												,ve_monto_descuento_pesos	IN NUMBER   :=NULL
												,ve_cod_componente_deuda	IN NUMBER   :=NULL
												,ve_cod_entidad_origen_pago	IN VARCHAR2 :=NULL
												,ve_fecha_ingreso_abono		IN DATE     :=NULL
												,ve_ano_pago				IN NUMBER   :=NULL)
AS
BEGIN
	IF (ve_operacion = 'INSERT')THEN
	    INSERT INTO usach_cb_huara
				    (COD_USACH_CB_HUARA
					,RUT	
					,COD_DEUDA	
					,FECHA_ABONO	
					,FECHA_DEPOSITO	
					,FOLIO	
					,MONTO_CAPITAL_UTM	
					,MONTO_INTERES_UTM	
					,MONTO_INTERES_NORMAL_UTM	
					,MONTO_CAPITAL_PESOS	
					,MONTO_INTERES_NORMAL_PESOS	
					,MONTO_INTERES_MORATORIO_PESOS	
					,MONTO_DESCUENTO_UTM	
					,MONTO_DESCUENTO_PESOS	
					,COD_COMPONENTE_DEUDA	
					,COD_ENTIDAD_ORIGEN_PAGO	
					,FECHA_INGRESO_ABONO	
					,ANO_PAGO
					,PROCESADO
					,CARGADO)
		  	VALUES	(usach_cb_huara_sq.NEXTVAL
					,ve_rut	
					,ve_cod_deuda	
					,ve_fecha_abono	
					,ve_fecha_deposito	
					,ve_folio	
					,ve_monto_capital_utm	
					,ve_monto_int_utm	
					,ve_monto_int_normal_utm	
					,ve_monto_pesos	
					,ve_monto_int_normal_pesos	
					,ve_monto_int_moratorio_pesos	
					,ve_monto_descuento_utm	
					,ve_monto_descuento_pesos	
					,ve_cod_componente_deuda	
					,ve_cod_entidad_origen_pago	
					,ve_fecha_ingreso_abono	
					,ve_ano_pago
					,'N'
					,'N');
	ELSIF (ve_operacion = 'DELETE')THEN 
		DELETE usach_cb_huara
		WHERE cargado = 'N'
		AND   procesado = 'N';
	END IF;
END spu_usach_cb_huara;
/