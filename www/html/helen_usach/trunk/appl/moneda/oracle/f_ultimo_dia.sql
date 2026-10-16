CREATE OR REPLACE FUNCTION f_ultimo_dia (ve_mes NUMBER, ve_ano NUMBER)
RETURN number
AS
BEGIN
   DECLARE
      vl_fecha_ultimo_dia DATE;
   BEGIN
      vl_fecha_ultimo_dia := To_Date('01/' || To_Char(ve_mes, '00') || '/' || To_Char(ve_ano, '0000'), 'dd/mm/yyyy');
      vl_fecha_ultimo_dia := vl_fecha_ultimo_dia + 31;
      vl_fecha_ultimo_dia := vl_fecha_ultimo_dia - To_Number(To_Char(vl_fecha_ultimo_dia, 'dd'));
      RETURN To_Number(To_Char(vl_fecha_ultimo_dia, 'dd'));
   END;
END;
/