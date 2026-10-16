CREATE OR REPLACE FUNCTION f_get_monto_conciliacion(
   ve_cod_conciliacion      NUMBER
   ,ve_tipo_conciliacion    VARCHAR
)
RETURN NUMERIC
AS ve_monto     NUMERIC;

/*****************************************************************************
   NAME:       f_get_monto_conciliacion

   Obtiene la suma de los montos de la conciliacion dependiendo del tipo de esta

   REVISIONS:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        30/03/2010  Sergio Castro    Creacion
   NOTAS:

******************************************************************************/
BEGIN
  BEGIN
    IF (ve_tipo_conciliacion = 'I') THEN
      SELECT  Sum(MONTO_INGRESO)
      INTO    ve_monto
      FROM    conciliacion_ingreso
      WHERE   cod_conciliacion = ve_cod_conciliacion;
    ELSIF (ve_tipo_conciliacion = 'E') THEN
      SELECT  Sum(MONTO_EGRESO)
      INTO    ve_monto
      FROM    conciliacion_egreso
      WHERE   cod_conciliacion = ve_cod_conciliacion;
    ELSIF (ve_tipo_conciliacion = 'C') THEN
      SELECT  Sum(MONTO_CHEQUE)
      INTO    ve_monto
      FROM    conciliacion_cheque
      WHERE   cod_conciliacion = ve_cod_conciliacion;
    END IF;


    RETURN Nvl(ve_monto,0);
  END;
END;