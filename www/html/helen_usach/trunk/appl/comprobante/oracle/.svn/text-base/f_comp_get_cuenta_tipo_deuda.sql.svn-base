CREATE OR REPLACE FUNCTION f_comp_get_cuenta_tipo_deuda (
   ve_tipo_deuda    VARCHAR2
 , ve_tipo_cuenta   NUMERIC
)
   RETURN NUMBER
AS
/******************************************************************************
   NAME:       f_comp_get_cuenta_tipo_deuda

   REVISIONS:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        13/10/2009  Paula Cea         Obtiene el CODIGO de la cuenta contable
                                           entrando el tipo de la deuda y el tipo cuenta
   NOTAS:

******************************************************************************/
BEGIN
   DECLARE
      vs_cod_cuenta_contable                  NUMERIC;
   BEGIN
      SELECT cc.cod_cuenta_contable
        INTO vs_cod_cuenta_contable
        FROM concepto_cuenta cncta
           , cuenta_contable cc
           , tipo_deuda td
           , cuenta_tipo_deuda ctd
       WHERE ctd.cod_tipo_deuda = td.cod_tipo_deuda
         AND ctd.cod_concepto_cuenta = cncta.cod_concepto_cuenta
         AND ctd.cod_cuenta_contable = cc.cod_cuenta_contable
         AND ctd.cod_tipo_deuda = ve_tipo_deuda
         AND cncta.cod_concepto_cuenta = ve_tipo_cuenta;

      RETURN vs_cod_cuenta_contable;
   END;
END;
/