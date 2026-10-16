CREATE OR REPLACE FUNCTION f_subboleta_multa(ve_cod_sub_boleta NUMBER
											,ve_fecha DATE)
	RETURN NUMBER
AS
-- Calcula la multa de K+IN de una subboleta
BEGIN
	DECLARE
		vl_cod_plan_pago	NUMBER;
		vl_meses			NUMBER;
		vl_tasa				NUMBER;
		vl_multa_total		NUMBER;
		vl_capital			NUMBER;
	BEGIN
		SELECT   b.cod_plan_pago
				,s.monto_capital + s.monto_interes
		INTO	 vl_cod_plan_pago
				,vl_capital
		FROM	sub_boleta s, boleta b
		WHERE	s.cod_sub_boleta = ve_cod_sub_boleta
		  AND	b.cod_boleta = s.cod_boleta;
	
		IF (vl_cod_plan_pago is null) THEN
			return 0;
		END IF;
	
		--SELECT	Ceil(months_between(ve_fecha, FECHA_VENCTO)) --MULLOA
		SELECT	Ceil(months_between(ve_fecha, FECHA_VENCTO)) --JMINO => revisado por LF. OK
		INTO	vl_meses
		FROM	PLAN_PAGO  
		WHERE	COD_PLAN_PAGO = vl_cod_plan_pago;
	
		IF (vl_meses <= 0)THEN
			return 0;
		END IF;
		
		-- obtiene la tasa de interes
		SELECT	tasa_interes_penal/100.0000
		INTO	vl_tasa
		FROM	tipo_deuda td
				,plan_pago pp
				,deuda d
		WHERE	pp.cod_plan_pago = vl_cod_plan_pago
		  AND	d.cod_deuda = pp.cod_deuda
		  AND	td.cod_tipo_deuda = d.cod_tipo_deuda;
	
		vl_multa_total := vl_capital * vl_tasa * vl_meses;
		
		RETURN vl_multa_total;
	END;
END;
/