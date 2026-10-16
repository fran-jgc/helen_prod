PROMPT CREATE OR REPLACE PROCEDURE spw_pago_transaccion
CREATE OR REPLACE PROCEDURE spw_pago_transaccion(
 ve_operacion                IN VARCHAR2
,ve_cod_wp_pago_transaccion	 IN NUMBER(10):=NULL
,ve_cod_wp_transaccion       IN NUMBER(10):=NULL
,ve_tbk_accion               IN VARCHAR2(3):=NULL
,ve_tbk_orden_compra         IN VARCHAR2(26):=NULL
,ve_tbk_codigo_comercio      IN NUMBER(12):=NULL
,ve_tbk_codigo_comercio_enc  IN NUMBER(12):=NULL
,ve_tbk_tipo_transaccion     IN VARCHAR2(50):=NULL
,ve_tbk_respuesta            IN NUMBER(2):=NULL
,ve_tbk_monto                IN NUMBER(10):=NULL
,ve_tbk_codigo_autorizacion  IN VARCHAR2(8):=NULL
,ve_tbk_final_numero_tarjeta IN NUMBER(4):=NULL
,ve_tbk_fecha_contable       IN NUMBER(4):=NULL
,ve_tbk_fecha_transaccion    IN NUMBER(4):=NULL
,ve_tbk_fecha_expiracion     IN NUMBER(4):=NULL
,ve_tbk_hora_transaccion     IN NUMBER(6):=NULL
,ve_tbk_id_sesion            IN VARCHAR2(61):=NULL
,ve_tbk_id_transaccion       IN NUMBER(20):=NULL
,ve_tbk_tipo_pago            IN VARCHAR2(2):=NULL
,ve_tbk_numero_cuotas        IN NUMBER(2):=NULL
,ve_tbk_vci                  IN VARCHAR2(3):=NULL
,ve_tbk_mac                  IN VARCHAR2(256):=NULL)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO WP_PAGO_TRANSACCION
				(COD_WP_PAGO_TRANSACCION
    			,COD_WP_TRANSACCION
				,TBK_ACCION
				,TBK_ORDEN_COMPRA
				,TBK_CODIGO_COMERCIO
				,TBK_CODIGO_COMERCIO_ENC
				,TBK_TIPO_TRANSACCION
				,TBK_RESPUESTA
				,TBK_MONTO
				,TBK_CODIGO_AUTORIZACION
				,TBK_FINAL_NUMERO_TARJETA
				,TBK_FECHA_CONTABLE
				,TBK_FECHA_TRANSACCION
				,TBK_FECHA_EXPIRACION
				,TBK_HORA_TRANSACCION
				,TBK_ID_SESION
				,TBK_ID_TRANSACCION
				,TBK_TIPO_PAGO
				,TBK_NUMERO_CUOTAS
				,TBK_VCI
				,TBK_MAC)
         VALUES (wp_pago_transaccion_sq.NEXTVAL
				,ve_cod_wp_transaccion
				,ve_tbk_accion
				,ve_tbk_orden_compra
				,ve_tbk_codigo_comercio
				,ve_tbk_codigo_comercio_enc
				,ve_tbk_tipo_transaccion
				,ve_tbk_respuesta
				,ve_tbk_monto
				,ve_tbk_codigo_autorizacion
				,ve_tbk_final_numero_tarjeta
				,ve_tbk_fecha_contable
				,ve_tbk_fecha_transaccion
				,ve_tbk_fecha_expiracion
				,ve_tbk_hora_transaccion
				,ve_tbk_id_sesion
				,ve_tbk_id_transaccion
				,ve_tbk_tipo_pago
				,ve_tbk_numero_cuotas
				,ve_tbk_vci
				,ve_tbk_mac);
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE WP_PAGO_TRANSACCION
         SET COD_WP_TRANSACCION	= ve_cod_wp_transaccion
			,TBK_ACCION			= ve_tbk_accion
			,TBK_ORDEN_COMPRA	= ve_tbk_orden_compra
			,TBK_CODIGO_COMERCIO		= ve_tbk_codigo_comercio
			,TBK_CODIGO_COMERCIO_ENC	= ve_tbk_codigo_comercio_enc
			,TBK_TIPO_TRANSACCION		= ve_tbk_tipo_transaccion
			,TBK_RESPUESTA		= ve_tbk_respuesta
			,TBK_MONTO			= ve_tbk_monto
			,TBK_CODIGO_AUTORIZACION	= ve_tbk_codigo_autorizacion
			,TBK_FINAL_NUMERO_TARJETA	= ve_tbk_final_numero_tarjeta
			,TBK_FECHA_CONTABLE			= ve_tbk_fecha_contable
			,TBK_FECHA_TRANSACCION		= ve_tbk_fecha_transaccion
			,TBK_FECHA_EXPIRACION		= ve_tbk_fecha_expiracion
			,TBK_HORA_TRANSACCION		= ve_tbk_hora_transaccion
			,TBK_ID_SESION		= ve_tbk_id_sesion
			,TBK_ID_TRANSACCION	= ve_tbk_id_transaccion
			,TBK_TIPO_PAGO		= ve_tbk_tipo_pago
			,TBK_NUMERO_CUOTAS	= ve_tbk_numero_cuotas
			,TBK_VCI			= ve_tbk_vci
			,TBK_MAC			= ve_tbk_mac
       WHERE COD_WP_PAGO_TRANSACCION = ve_cod_wp_pago_transaccion;
   END IF;
   COMMIT;
END;
/