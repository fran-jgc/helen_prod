PROMPT CREATE OR REPLACE PROCEDURE spu_usach_cb_desarrollo
CREATE OR REPLACE PROCEDURE spu_usach_cb_desarrollo(ve_operacion                  VARCHAR2
                                                    ,ve_cod_registro			        IN NUMBER   := NULL
													                          ,ve_cod_convenio		          IN NUMBER   := NULL
			                		                          ,ve_cta_abono			            IN VARCHAR2 := NULL
			                		                          ,ve_identif_deudor		        IN VARCHAR2 := NULL
			                		                          ,ve_fecha_vencto		          IN DATE     := NULL
													                          ,ve_fecha_real_pago		        IN DATE     := NULL
													                          ,ve_valor_cuota			          IN NUMBER   := NULL
													                          ,ve_monto_recargo		          IN NUMBER   := NULL
													                          ,ve_valor_recaudado_efectivo	IN NUMBER   := NULL
													                          ,ve_valor_recaudado_doc			  IN NUMBER   := NULL
													                          ,ve_sucursal_recaudador			  IN NUMBER   := NULL
													                          ,ve_identif_cajero		        IN VARCHAR2 := NULL
													                          ,ve_marca_manual		          IN VARCHAR2 := NULL
													                          ,ve_curso				              IN VARCHAR2 := NULL
													                          ,ve_nro_cuota			            IN NUMBER   := NULL
                                                    ,ve_fecha_deposito			      IN DATE     := NULL
													                          ,ve_filler				            IN VARCHAR2 := NULL)
AS
BEGIN
  IF(ve_operacion = 'INSERT')THEN
    INSERT INTO USACH_CB_DESARROLLO
			  (COD_USACH_CB_DESARROLLO
			  ,COD_REGISTRO
			  ,COD_CONVENIO
			  ,CTA_ABONO
			  ,IDENTIFICADOR_DEUDOR
			  ,FECHA_VENCIMIENTO
			  ,FECHA_REAL_PAGO
			  ,VALOR_CUOTA
			  ,MONTO_RECARGO
			  ,VALOR_RECAUDADO_EFECTIVO
			  ,VALOR_RECAUDADO_DOC
			  ,SUCURSAL_RECAUDADOR
			  ,IDENTIFICADOR_CAJERO
			  ,MARCA_MANUAL
			  ,CURSO
			  ,NRO_CUOTA
        ,FECHA_DEPOSITO
			  ,FILLER
        ,PROCESADO
        ,CARGADO)
	  VALUES	(usach_cb_desarrollo_sq.NEXTVAL
			  ,ve_cod_registro
			  ,ve_cod_convenio
			  ,ve_cta_abono
			  ,ve_identif_deudor
			  ,ve_fecha_vencto
			  ,ve_fecha_real_pago
			  ,ve_valor_cuota
			  ,ve_monto_recargo
			  ,ve_valor_recaudado_efectivo
			  ,ve_valor_recaudado_doc
			  ,ve_sucursal_recaudador
			  ,ve_identif_cajero
			  ,ve_marca_manual
			  ,ve_curso
			  ,ve_nro_cuota
        ,ve_fecha_deposito
			  ,ve_filler
        ,'N'
        ,'N');
  ELSIF(ve_operacion = 'DELETE')THEN
    DELETE  usach_cb_desarrollo
    WHERE   cargado = 'N'
    AND     procesado = 'N'; 
  END IF; 
END spu_usach_cb_desarrollo;
/