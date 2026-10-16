PROMPT CREATE OR REPLACE PROCEDURE sp_wp_pago_transaccion
CREATE OR REPLACE PROCEDURE sp_wp_pago_transaccion_ws(ve_operacion			IN VARCHAR2
							   ,ve_cod_wp_transaccion	       IN NUMBER
							   ,ve_sesion_id			IN VARCHAR2	:= null
							   ,ve_numero_tarjeta		IN VARCHAR2	:= null
							   ,ve_expiracion_tarjeta		IN VARCHAR2	:= null
							   ,ve_fecha_autorizacion		IN VARCHAR2	:= null
							   ,ve_fecha_hora_autorizacion	IN VARCHAR2	:= null
							   ,ve_resultado_autenticacion	IN VARCHAR2	:= null
							   ,ve_url_redirection		IN VARCHAR2	:= null
							   ,ve_codigo_autorizacion		IN VARCHAR2	:= null
							   ,ve_tipo_pago			IN VARCHAR2	:= null
							   ,ve_codigo_respuesta	 	IN VARCHAR2	:= null
							   ,ve_monto				IN NUMBER	:= null
							   ,ve_valor_cuota			IN NUMBER	:= null
							   ,ve_cantidad_cuota		IN NUMBER	:= null
							   ,ve_codigo_comercio_tienda	IN VARCHAR2	:= null
							   ,ve_orden_compra_tienda		IN VARCHAR2	:= null)
AS
BEGIN


    if (ve_operacion = 'INSERT')THEN

        
        insert into WP_PAGO_TRANSACCION_WS
	 (COD_WP_PAGO_TRANSACCION
        ,COD_WP_TRANSACCION
	 ,SESSION_ID
	 ,NUMERO_TARJETA
	 ,EXPIRACION_TARJETA
	 ,FECHA_AUTORIZACION
	 ,FECHA_HORA_AUTORIZACION
	 ,RESULTADO_AUTENTICACION
	 ,URL_REDIRECTION
	 ,CODIGO_AUTORIZACION
	 ,TIPO_PAGO
	 ,CODIGO_RESPUESTA
	 ,MONTO
	 ,VALOR_CUOTA
	 ,CANTIDAD_CUOTA
	 ,CODIGO_COMERCIO_TIENDA
	 ,ORDEN_COMPRA_TIENDA)
	values (WP_PAGO_TRANSACCION_WS_SQ.nextval
        ,ve_cod_wp_transaccion
	 ,ve_sesion_id
	 ,ve_numero_tarjeta
	 ,ve_expiracion_tarjeta
	 ,ve_fecha_autorizacion
	 ,ve_fecha_hora_autorizacion
	 ,ve_resultado_autenticacion
	 ,ve_url_redirection
	 ,ve_codigo_autorizacion
	 ,ve_tipo_pago
	 ,ve_codigo_respuesta
	 ,ve_monto
	 ,ve_valor_cuota
	 ,ve_cantidad_cuota
	 ,ve_codigo_comercio_tienda
	 ,ve_orden_compra_tienda);
    END IF;
   COMMIT;
   
END;
/

