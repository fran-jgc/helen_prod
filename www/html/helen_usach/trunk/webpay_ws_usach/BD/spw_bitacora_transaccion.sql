CREATE OR REPLACE PROCEDURE spw_bitacora_transaccion(ve_cod_wp_transaccion	IN NUMBER
							   ,ve_fase			IN VARCHAR2
							   ,ve_mensaje		IN VARCHAR2)
AS
BEGIN

    insert into BITACORA_TRANSACCION
  	(COD_BITACORA_TRANSACCION
  	,COD_WP_TRANSACCION
  	,FASE
  	,MENSAJE)values
  			(bitacora_transaccion_sq.NEXTVAL
  			,ve_cod_wp_transaccion
  			,ve_fase
  			,ve_mensaje);

    COMMIT;
        
END spw_bitacora_transaccion;