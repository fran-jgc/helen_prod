CREATE OR REPLACE FUNCTION f_ingreso_get_desde_simulacion (ve_cod_simulacion NUMBER)
RETURN NUMBER
AS
BEGIN
	DECLARE 
			kl_estado_ingreso_anulado NUMBER := 2;
			vl_cod_ingreso NUMBER;
	BEGIN
	
		select	cod_ingreso
		INTO	vl_cod_ingreso
		from	ingreso
		where	cod_ingreso_desde = ve_cod_simulacion
		  and	cod_estado_ingreso <> kl_estado_ingreso_anulado; 

	    RETURN vl_cod_ingreso;
    END;
END;
/