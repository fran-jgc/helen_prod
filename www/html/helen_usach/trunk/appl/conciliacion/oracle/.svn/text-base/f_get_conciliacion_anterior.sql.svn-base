CREATE OR REPLACE FUNCTION f_get_conciliacion_anterior(
   ve_mes     NUMBER
   ,ve_ano    NUMBER
)
RETURN NUMERIC
AS ve_monto     NUMERIC;

/*****************************************************************************
   NAME:       f_get_conciliacion_anterior

   Retorna el Saldo De la conciliacion del Mes Anterior

   REVISIONS:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        01/04/2010  Sergio Castro    Creacion
   NOTAS:

******************************************************************************/
BEGIN
DECLARE
  vl_mes  NUMBER;
  vl_ano  NUMBER;
  BEGIN
    IF (ve_mes = 1) THEN
      vl_mes := 12;
      vl_ano := ve_ano - 1;
    ELSE
      vl_mes := ve_mes - 1;
      vl_ano := ve_ano;
    END IF;

    SELECT (SALDO_ANTERIOR + INGRESO_MES - EGRESO_MES)
    INTO    ve_monto
    FROM    conciliacion
    WHERE   mes = vl_mes
            AND ano = vl_ano;

    RETURN Nvl(ve_monto,0);
  END;
END;