PROMPT CREATE OR REPLACE PROCEDURE spw_wp_init_transaccion
CREATE OR REPLACE PROCEDURE spw_wp_init_transaccion(
											  ve_cod_wp_transaccion		IN NUMBER
											  ,ve_token		IN VARCHAR2)
AS
BEGIN
   UPDATE WP_TRANSACCION SET TOKEN = ve_token WHERE COD_WP_TRANSACCION = ve_cod_wp_transaccion ;
   COMMIT;
END;
/

