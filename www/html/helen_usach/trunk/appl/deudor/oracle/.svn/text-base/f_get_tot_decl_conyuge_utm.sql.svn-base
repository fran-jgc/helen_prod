CREATE OR REPLACE FUNCTION f_get_tot_decl_conyuge_utm (ve_cod_declaracion NUMBER)
   RETURN NUMBER
IS
   vs_tot_utm                 NUMBER;
BEGIN
  SELECT Nvl(Sum(MONTO_CONYUGE_MONEDA), 0)
  INTO vs_tot_utm
  FROM declaracion_mes
  WHERE cod_declaracion = ve_cod_declaracion;

  RETURN vs_tot_utm;
END f_get_tot_decl_conyuge_utm;
/