/*esta funcion es parecida a f_ingreso_calculo, se diferencia en que entra como parametro COD_INGRESO, en general hace lo mismo. USADA EN INFORME ARQUEO CAJA*/
CREATE OR REPLACE function f_ingreso_get_detalle(ve_cod_ingreso NUMBER, ve_cod_tipo_documento_pago NUMBER)
RETURN NUMBER
AS
BEGIN
	DECLARE vs_monto					        NUMBER;
			    K_OTRO					          NUMBER:= 0;
			    K_TIPO_DOC_CHEQUE_AL_DIA	NUMBER:= 1;
			    K_TIPO_DOC_DEPOSITO		  NUMBER:= 3;
			    K_TIPO_DOC_EFECTIVO		  NUMBER:= 4;
			    K_TIPO_DOC_CHEQUE_FECHA	NUMBER:= 5;
			    K_TIPO_DESCUENTO			    NUMBER:= 10; 

	BEGIN 
		IF(ve_cod_tipo_documento_pago = K_OTRO)THEN
			SELECT	sum(monto)
			INTO	vs_monto 
			from	documento_pago dp, documento_ingreso di
			where	dp.cod_documento_pago = di.cod_documento_pago
			and		cod_ingreso = ve_cod_ingreso
			and		cod_tipo_documento_pago NOT IN (K_TIPO_DOC_CHEQUE_AL_DIA, K_TIPO_DOC_DEPOSITO, K_TIPO_DOC_EFECTIVO, K_TIPO_DOC_CHEQUE_FECHA);
		
		ELSIF(ve_cod_tipo_documento_pago = K_TIPO_DOC_CHEQUE_AL_DIA)THEN 
			select	sum(monto)
			INTO	vs_monto
			from	documento_pago dp, documento_ingreso di
			where	dp.cod_documento_pago = di.cod_documento_pago
			and		dp.cod_tipo_documento_pago = K_TIPO_DOC_CHEQUE_AL_DIA
			and		di.cod_ingreso = ve_cod_ingreso;
		
		ELSIF(ve_cod_tipo_documento_pago = K_TIPO_DOC_DEPOSITO)THEN 
			SELECT	sum(monto)
			INTO	vs_monto
			from	documento_pago dp, documento_ingreso di
			where	dp.cod_documento_pago = di.cod_documento_pago
			and		dp.cod_tipo_documento_pago = K_TIPO_DOC_DEPOSITO
			and		di.cod_ingreso = ve_cod_ingreso;
		  
		ELSIF(ve_cod_tipo_documento_pago = K_TIPO_DOC_EFECTIVO)THEN
			SELECT	sum(monto)
			INTO    vs_monto
			from	documento_pago dp, documento_ingreso di
			where	dp.cod_documento_pago = di.cod_documento_pago
			and		dp.cod_tipo_documento_pago = K_TIPO_DOC_EFECTIVO
			and		di.cod_ingreso = ve_cod_ingreso;
		  
		ELSIF(ve_cod_tipo_documento_pago = K_TIPO_DOC_CHEQUE_FECHA)THEN
			SELECT	sum(monto)
			INTO	vs_monto
			from	documento_pago dp, documento_ingreso_ch_fecha dch
			where	dch.cod_ingreso = ve_cod_ingreso
			and		dp.cod_documento_pago = dch.cod_documento_pago
			and		dp.cod_tipo_documento_pago = K_TIPO_DOC_CHEQUE_FECHA;
		  
		ELSIF(ve_cod_tipo_documento_pago = K_TIPO_DESCUENTO)THEN
			SELECT  round(sum (monto_descuento * f_mon_get_valor_moneda(3, i.fecha_ingreso)),0)
			INTO    vs_monto
			from	INGRESO I, ITEM_INGRESO II
			where	I.COD_INGRESO = ve_cod_ingreso
			and		I.COD_INGRESO = II.COD_INGRESO; 
		END IF;

		return nvl(vs_monto,0);
	END;
END;
/