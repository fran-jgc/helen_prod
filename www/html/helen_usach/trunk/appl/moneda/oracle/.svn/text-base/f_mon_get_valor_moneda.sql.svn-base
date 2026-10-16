CREATE OR REPLACE FUNCTION f_mon_get_valor_moneda (
   ve_cod_moneda           NUMBER
 , ve_fecha_valor_moneda   DATE := sysdate
)
   RETURN valor_moneda.monto_valor_moneda%TYPE
AS
/******************************************************************************
   NAME:       f_mon_get_valor_moneda
   PURPOSE:    Entregar valor de un tipo de moneda para una fecha determinada.

   REVISIONS:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        13/10/2009  Paula Cea
   2.0        21/11/2009  Sergio Castro     Revision y Cambios
   NOTES:

******************************************************************************/
BEGIN
   DECLARE
      K_PESOS                                 NUMBER:=1;
      vl_monto_valor_moneda                   valor_moneda.monto_valor_moneda%TYPE;
      vl_count                                NUMBER;
   BEGIN
      IF (ve_cod_moneda = K_PESOS) THEN
        RETURN 1;
      END IF;

      SELECT COUNT (*)
        INTO vl_count
        FROM valor_moneda
       WHERE cod_moneda = ve_cod_moneda
         AND fecha_valor_moneda = TRUNC (ve_fecha_valor_moneda);

      IF (vl_count <> 0)
      THEN
         SELECT NVL (monto_valor_moneda, 0)
           INTO vl_monto_valor_moneda
           FROM valor_moneda
          WHERE cod_moneda = ve_cod_moneda
            AND fecha_valor_moneda = TRUNC (ve_fecha_valor_moneda);
      ELSE
         vl_monto_valor_moneda                := 0;
      END IF;

      RETURN vl_monto_valor_moneda;
   END;
END;
/