CREATE OR REPLACE FUNCTION f_get_anos_suspension(ve_rut IN NUMBER, ve_cod_tipo_suspension IN NUMBER)
RETURN VARCHAR2
AS
   vs_anos_suspendido NUMBER;
BEGIN
   BEGIN
      SELECT Count(*)
      INTO vs_anos_suspendido
      FROM suspension s, tipo_suspension ts, deuda da, deudor dr
      WHERE s.cod_tipo_suspension = ts.cod_tipo_suspension
      AND da.cod_deuda = s.cod_deuda
      AND da.cod_deudor = dr.cod_deudor
      AND dr.rut = ve_rut
      AND s.cod_tipo_suspension = ve_cod_tipo_suspension
      AND s.cod_estado_suspension = 2;
    RETURN  vs_anos_suspendido;
   END;
END;
/