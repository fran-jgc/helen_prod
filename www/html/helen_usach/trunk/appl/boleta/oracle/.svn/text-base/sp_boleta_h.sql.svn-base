CREATE OR REPLACE PROCEDURE sp_boleta_h (ve_cod_boleta	NUMBER
										,ve_origen		VARCHAR2
										,ve_cod_origen	NUMBER)
AS
BEGIN
  INSERT INTO boleta_h
		    (COD_BOLETA_H
		    ,ORIGEN
		    ,COD_ORIGEN
		    ,COD_BOLETA
		    ,FECHA_BOLETA
		    ,COD_USUARIO
		    ,COD_DEUDA
		    ,COD_PLAN_PAGO
		    ,MONTO_CAPITAL
		    ,MONTO_INTERES
		    ,MONTO_MULTA
		    ,MONTO_MONEDA
		    ,MONTO_PESOS
		    ,DSCTO_CAPITAL
		    ,DSCTO_INTERES
		    ,DSCTO_MULTA
		    ,DSCTO_PESOS
		    ,FECHA_PRIMERA_CUOTA
		    ,CANT
		    ,CARTERA_CONOCIDA
		    ,COD_TIPO_DESCUENTO)
  SELECT 	 boleta_h_sq.NEXTVAL
			,ve_origen
			,ve_cod_origen
			,cod_boleta
			,fecha_boleta
			,cod_usuario
			,cod_deuda
			,cod_plan_pago
			,monto_capital
			,monto_interes
			,monto_multa
			,monto_moneda
			,monto_pesos
			,dscto_capital
			,dscto_interes
			,dscto_multa
			,dscto_pesos
			,fecha_primera_cuota
			,cant
			,cartera_conocida
			,cod_tipo_descuento
	FROM	boleta
	WHERE	cod_boleta = ve_cod_boleta;
END;
/