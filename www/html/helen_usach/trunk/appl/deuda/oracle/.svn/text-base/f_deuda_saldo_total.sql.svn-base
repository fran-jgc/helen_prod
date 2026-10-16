CREATE OR REPLACE FUNCTION f_deuda_saldo_total (
   ve_cod_deuda                        NUMERIC
 , ve_fecha                            DATE := SYSDATE
)
   RETURN NUMBER
AS
/******************************************************************************
   NAME:       f_deuda_saldo_total
   PURPOSE:     Obtiene el saldo de la deuda para todos sus componentes

   REVISIONS:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        24/10/2009   Victor Melo

   NOTES:
******************************************************************************/
BEGIN
    DECLARE
          KL_COMP_NO_EXIGIBLE       NUMBER:=1;
          KL_COMP_VENCIDA           NUMBER:=2;
          KL_COMP_VIGENTE           NUMBER:=3;
          KL_COMP_FUTURA            NUMBER:=4;
			    KL_DEUDA_NO_EXIGIBLE      NUMBER:=1;
			    KL_DEUDA_EXIGIBLE         NUMBER:=2;

		  	  vl_cod_estado_deuda       NUMBER;
          vl_saldo                  NUMBER;
      BEGIN
        vl_cod_estado_deuda := f_deuda_get_estado(ve_cod_deuda, ve_fecha);
        IF (vl_cod_estado_deuda <> KL_DEUDA_NO_EXIGIBLE AND vl_cod_estado_deuda <> KL_DEUDA_EXIGIBLE) THEN
          RETURN 0;
        END IF;

        IF (vl_cod_estado_deuda = KL_DEUDA_NO_EXIGIBLE) THEN
          RETURN f_deuda_saldo_componente(ve_cod_deuda, KL_COMP_NO_EXIGIBLE, ve_fecha);
        ELSE
          SELECT Nvl(Sum(f_pp_saldo_capital(cod_plan_pago, ve_fecha) + f_pp_saldo_interes_normal(cod_plan_pago, ve_fecha) + f_pp_saldo_multa(cod_plan_pago, ve_fecha)), 0)
          INTO vl_saldo
          FROM plan_pago
          WHERE cod_deuda = ve_cod_deuda;

		      RETURN  vl_saldo;
        END IF;
      END;
END;