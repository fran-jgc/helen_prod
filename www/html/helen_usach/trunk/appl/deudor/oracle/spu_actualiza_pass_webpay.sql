CREATE OR REPLACE PROCEDURE spu_actualiza_pass_webpay(ve_cod_deudor NUMBER
                                                      ,ve_pass      VARCHAR)
AS
BEGIN
  UPDATE  DEUDOR
  SET     PASSWORD = ve_pass
  WHERE   COD_DEUDOR =  ve_cod_deudor;
  COMMIT;
END;