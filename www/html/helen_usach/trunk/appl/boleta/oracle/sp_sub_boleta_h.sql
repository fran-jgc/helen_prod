CREATE OR REPLACE PROCEDURE sp_sub_boleta_h (ve_cod_boleta	NUMBER
											,ve_origen		VARCHAR2
											,ve_cod_origen	NUMBER)
AS
BEGIN
  INSERT INTO sub_boleta_h
		    (COD_SUB_BOLETA_H
			,ORIGEN
			,COD_ORIGEN
			,COD_SUB_BOLETA
			,COD_BOLETA
			,NRO_SUB_BOLETA
			,FECHA_VENCTO
			,MONTO_CAPITAL
			,MONTO_INTERES
			,MONTO_MULTA
			,FECHA_PAGO
			,COD_COMPONENTE_DEUDA)
  SELECT	sub_boleta_h_sq.NEXTVAL
			,ve_origen
			,ve_cod_origen
			,cod_sub_boleta
			,cod_boleta
			,nro_sub_boleta
			,fecha_vencto
			,monto_capital
			,monto_interes
			,monto_multa
			,fecha_pago
			,cod_componente_deuda
	FROM	sub_boleta
	WHERE	cod_boleta = ve_cod_boleta;
END;
/