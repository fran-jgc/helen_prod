------------------  f_ingreso_get -----------------------------
CREATE OR REPLACE FUNCTION f_ingreso_get(ve_cod_ingreso		NUMBER
							                          ,ve_operacion			varchar2)
/*
@ve_cod_ingreso : Key dwel registro INGRESO
@ve_operacion : Indica que dato es el que se quiere que retorne la funcion
	'CANT_CHEQUE'	: cantidad de cheques en Ingreso
	'MONTO_CHEQUE'	: Suma de los cheques en Ingreso
	'MONTO_EFECTIVO': Suma de los efectivos en Ingreso
	'MONTO_OTRO'	: Suma de los otros tipo documento (distinto a cheque y efectivo) en Ingreso
	'MONTO_TOTAL'	: Suma del pago total en Ingreso
*/
RETURN NUMBER
AS
		vl_resultado		  NUMBER;
		vl_cant_ch		    NUMBER;
		vl_cant_ch_fecha	NUMBER;
		vl_monto_ch		    NUMBER;
		vl_monto_ch_fecha	NUMBER;
		vl_monto_efectivo	NUMBER;
		vl_monto_otro		  NUMBER;
		
BEGIN
	if (ve_operacion = 'CANT_CHEQUE') then
		select count(*)
    INTO vl_cant_ch 
		from DOCUMENTO_INGRESO di, documento_pago dp
		where di.cod_ingreso = ve_cod_ingreso
		  and dp.cod_documento_pago = di.cod_documento_pago
		  and dp.cod_tipo_documento_pago = 1;	-- cheque
		  
		select count(*)
    INTO vl_cant_ch_fecha
		from DOCUMENTO_INGRESO_CH_FECHA di
		where di.cod_ingreso = ve_cod_ingreso;
		  
		vl_resultado := vl_cant_ch + vl_cant_ch_fecha;
	elsif (ve_operacion = 'MONTO_CHEQUE') then
		select nvl(sum(dp.monto), 0)
    into vl_monto_ch
		from DOCUMENTO_INGRESO di, documento_pago dp
		where di.cod_ingreso = ve_cod_ingreso
		  and dp.cod_documento_pago = di.cod_documento_pago
		  and dp.cod_tipo_documento_pago = 1;	-- cheque
		  
		select nvl(sum(dp.monto), 0)
    INTO vl_monto_ch_fecha
		from DOCUMENTO_INGRESO_CH_FECHA di, documento_pago dp
		where di.cod_ingreso = ve_cod_ingreso
		  and dp.cod_documento_pago = di.cod_documento_pago
		  and dp.cod_tipo_documento_pago = 5;	-- cheque a fecha
		  
		vl_resultado := vl_monto_ch + vl_monto_ch_fecha;
	elsif (ve_operacion = 'MONTO_EFECTIVO') then
		select nvl(sum(dp.monto), 0)
    INTO vl_monto_efectivo
		from DOCUMENTO_INGRESO di, documento_pago dp
		where di.cod_ingreso = ve_cod_ingreso
		  and dp.cod_documento_pago = di.cod_documento_pago
		  and dp.cod_tipo_documento_pago = 4;	-- efectivo
		  
		vl_resultado := vl_monto_efectivo;
	elsif (ve_operacion = 'MONTO_OTRO') then
		select nvl(sum(dp.monto), 0)
    INTO vl_monto_otro
		from DOCUMENTO_INGRESO di, documento_pago dp
		where di.cod_ingreso = ve_cod_ingreso
		  and dp.cod_documento_pago = di.cod_documento_pago
		  and dp.cod_tipo_documento_pago not in (1, 4, 5);	-- ch; efectivo; ch fecha
		  
		vl_resultado := vl_monto_otro;
	elsif (ve_operacion = 'MONTO_TOTAL') then
		select nvl(sum(dp.monto), 0)
    INTO vl_monto_ch
		from DOCUMENTO_INGRESO di, documento_pago dp
		where di.cod_ingreso = ve_cod_ingreso
		  and dp.cod_documento_pago = di.cod_documento_pago;
		  
		select nvl(sum(dp.monto), 0)
    INTO vl_monto_ch_fecha
		from DOCUMENTO_INGRESO_CH_FECHA di, documento_pago dp
		where di.cod_ingreso = ve_cod_ingreso
		  and dp.cod_documento_pago = di.cod_documento_pago
		  and dp.cod_tipo_documento_pago = 5;	-- cheque a fecha
		  
		vl_resultado := vl_monto_ch + vl_monto_ch_fecha;
	END IF;		
		

    RETURN vl_resultado;
END;
/
