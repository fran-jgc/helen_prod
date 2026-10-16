CREATE OR REPLACE FUNCTION f_rend_tot_pesos_ley(ve_cod_rendicion number)
   RETURN NUMBER
IS
   vs_tot_pesos                 NUMBER;
/******************************************************************************
   NAME:       f_rend_tot_pesos_abono_dscto
   PURPOSE:

   REVISIONS:
   Ver        Date        Author            Description
   ---------  ----------  ---------------   ------------------------------------
   1.0        09/03/2010  Fernando Mercado  Suma monto_pesos de abonos y descuentos
   											                    relacionados a una rendicion
   NOTAS:

******************************************************************************/
BEGIN
  DECLARE
    vl_tot_pesos        NUMBER;
  BEGIN
    SELECT  Nvl(Sum(ra.monto_rendido_pesos),0) monto_pesos
    INTO    vl_tot_pesos
    FROM    rendicion_ley_abono ra, rendicion_ley r
    WHERE   R.cod_rendicion_ley = ra.cod_rendicion_ley
    AND     r.cod_rendicion_ley = ve_cod_rendicion;
                                                           

    vs_tot_pesos := vl_tot_pesos;
  END;
  RETURN vs_tot_pesos;
END f_rend_tot_pesos_ley;
/