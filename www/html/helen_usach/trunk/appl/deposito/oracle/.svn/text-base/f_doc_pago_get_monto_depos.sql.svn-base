PROMPT CREATE OR REPLACE FUNCTION f_doc_pago_get_monto_depos
CREATE OR REPLACE FUNCTION f_doc_pago_get_monto_depos
(
   ve_cod_deposito   NUMBER
)
RETURN NUMERIC

AS        ve_monto     NUMERIC;
/******************************************************************************
   NAME:       f_pp_get_saldo_capital
   PURPOSE:    hace una sumatoria del monto depositado, este sera mostrado en el
              output de deposito

   REVISIONS:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        23/10/2009  Cristian Sánchez     sumatoria del monto depositado
   NOTAS:

******************************************************************************/
BEGIN
       BEGIN
          SELECT  Nvl(sum (MONTO), 0)
          INTO    ve_monto
          FROM    DOCUMENTO_PAGO DP, DEPOSITO_DOCUMENTO DD
          WHERE   DP.COD_DOCUMENTO_PAGO = DD.COD_DOCUMENTO_PAGO
          AND     COD_DEPOSITO=ve_cod_deposito;

          RETURN ve_monto;
       END;
END;
/

