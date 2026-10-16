CREATE OR REPLACE FUNCTION f_comp_get_monto_debe
(
   ve_cod_comprobante   NUMBER
)
RETURN NUMERIC

AS        ve_monto     NUMERIC;




/******************************************************************************
   NAME:       f_pp_get_saldo_capital

   REVISIONS:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        23/10/2009  Cristian Sánchez     sumatoria del monto depositado
   NOTAS:

******************************************************************************/
BEGIN
       BEGIN
          SELECT  Nvl(sum (MONTO_DEBE), 0) INTO ve_monto FROM ITEM_COMPROBANTE  WHERE COD_COMPROBANTE=ve_cod_comprobante;

          RETURN ve_monto;
       END;
END;
/