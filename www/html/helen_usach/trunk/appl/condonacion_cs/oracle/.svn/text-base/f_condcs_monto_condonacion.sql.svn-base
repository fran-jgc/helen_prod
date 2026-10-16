CREATE OR REPLACE FUNCTION f_condcs_monto_condonacion (
   ve_cod_deuda   IN   NUMBER
)
   RETURN VARCHAR
AS
/******************************************************************************
   NAME:
   PURPOSE:

   REVISIONS:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
    1.0       29/06/2010  FM                TRAE MONTO_CAPITAL DE LA DEUDA PARA 
                                            CONDONACION CS
              
   NOTES:
******************************************************************************/
   vs_monto           NUMBER;
BEGIN
   BEGIN      
      SELECT f_pp_saldo_plan_pago(cod_plan_pago)
      INTO vs_monto
      FROM plan_pago
      WHERE cod_deuda = ve_cod_deuda
        AND ano_cobro = 9999;

      RETURN vs_monto;
   END;
END;
/
