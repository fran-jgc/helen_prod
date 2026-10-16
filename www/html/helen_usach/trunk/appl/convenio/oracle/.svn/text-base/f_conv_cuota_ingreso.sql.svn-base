CREATE OR REPLACE FUNCTION f_conv_cuota_ingreso(ve_cod_cuota_convenio		NUMBER)
RETURN VARCHAR2
AS
BEGIN
	DECLARE
		vl_ingresos		VARCHAR2(100);
		vc_cod_ingreso	NUMBER;
		
		CURSOR C_INGRESO IS
			select i.cod_ingreso
			from pago_cuota_convenio p, ingreso i
			where p.cod_cuota_convenio = ve_cod_cuota_convenio
			and i.cod_ingreso = p.cod_ingreso
			and i.cod_estado_ingreso in (1, 3);	-- emitido, cerrado

	BEGIN
		FOR i IN C_INGRESO LOOP
			vl_ingresos := vl_ingresos || TO_CHAR(i.cod_ingreso) || '-';
		
		END LOOP;
		
		IF (vl_ingresos <> '') THEN
			vl_ingresos := substr(vl_ingresos, 1, length(vl_ingresos) - 1);
		END IF;
		
		return vl_ingresos;
	END;
END;
/