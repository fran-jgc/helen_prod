CREATE OR REPLACE function f_ingreso_doc_al_dia(ve_cod_ingreso NUMBER)
RETURN NUMBER
AS
BEGIN
	DECLARE	vs_suma		NUMBER;
	
	BEGIN
		SELECT	NVL(sum(monto), 0)
		INTO	vs_suma
		FROM	documento_ingreso di, documento_pago dp
		WHERE	di.cod_ingreso = ve_cod_ingreso
		  AND	dp.cod_documento_pago = di.cod_documento_pago;

		RETURN vs_suma;
	END;
END;
/