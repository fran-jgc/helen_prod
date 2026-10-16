/*esta funcion devuelve la suma total de documentos(monto) dependiendo del tipo_documento_pago. USADA EN INFORME ARQUEO DE CAJA*/
CREATE OR REPLACE function f_ingreso_calculo(ve_cod_tipo_documento_pago   NUMBER
                                            , ve_cod_usuario              NUMBER
                                            , ve_fecha_inicio             DATE
                                            , ve_fecha_termino            DATE
                                            , ve_cod_familia_deuda        NUMBER)
RETURN NUMBER 
AS
BEGIN
	DECLARE 	vs_monto					NUMBER;
			    K_TIPO_OTRO					NUMBER:=0;
				K_TIPO_DOC_CHEQUE			NUMBER:=1;
			    K_TIPO_DOC_DEPOSITO			NUMBER:=3;
			    K_TIPO_DOC_EFECTIVO			NUMBER:=4;
			    K_TIPO_DOC_CHEQUE_FECHA		NUMBER:=5;
			    K_TIPO_DESCUENTO			NUMBER:=10;
			    K_ESTADO_SIMULACION			NUMBER:=1000;
				K_ESTADO_ANULADO			NUMBER:=2;

	BEGIN 
		IF(ve_cod_tipo_documento_pago = K_TIPO_OTRO)THEN-- TIPO OTROS
			SELECT	sum(monto)
			INTO	vs_monto 
			from	ingreso i, documento_ingreso di, documento_pago dp
			where	i.cod_ingreso = di.cod_ingreso
			and		dp.cod_documento_pago = di.cod_documento_pago
			and		cod_tipo_documento_pago NOT in(K_TIPO_DOC_CHEQUE, K_TIPO_DOC_DEPOSITO, K_TIPO_DOC_EFECTIVO, K_TIPO_DOC_CHEQUE_FECHA)
			and		(0 = ve_cod_usuario or i.cod_usuario = ve_cod_usuario)
			and		i.fecha_ingreso between ve_fecha_inicio and to_date(To_Char(ve_fecha_termino, 'dd/mm/yyyy') || ' 23:59:59', 'dd/mm/yyyy HH24:MI:SS')
			and		i.cod_estado_ingreso not in (K_ESTADO_SIMULACION, K_ESTADO_ANULADO)
      AND   (0 = ve_cod_familia_deuda or dp.cod_familia_deuda = ve_cod_familia_deuda);
	
		ELSIF(ve_cod_tipo_documento_pago = K_TIPO_DOC_CHEQUE)THEN -- TIPO CHEQUE
			SELECT	sum(monto)
			INTO	vs_monto  
			from	ingreso i, documento_ingreso di, documento_pago dp
			where	i.cod_ingreso = di.cod_ingreso
			and		dp.cod_documento_pago = di.cod_documento_pago
			and		cod_tipo_documento_pago = K_TIPO_DOC_CHEQUE
			and		(0 = ve_cod_usuario or i.cod_usuario = ve_cod_usuario)
			and		i.fecha_ingreso between ve_fecha_inicio and to_date(To_Char(ve_fecha_termino, 'dd/mm/yyyy') || ' 23:59:59', 'dd/mm/yyyy HH24:MI:SS')
			and		i.cod_estado_ingreso not in (K_ESTADO_SIMULACION, K_ESTADO_ANULADO)
      AND   (0 = ve_cod_familia_deuda or dp.cod_familia_deuda = ve_cod_familia_deuda);
			
		ELSIF(ve_cod_tipo_documento_pago = K_TIPO_DOC_EFECTIVO)THEN -- TIPO EFECTIVO
			SELECT	sum(monto) 
			INTO	vs_monto
			from	ingreso i, documento_ingreso di, documento_pago dp
			where	i.cod_ingreso = di.cod_ingreso
			and		dp.cod_documento_pago = di.cod_documento_pago
			and		cod_tipo_documento_pago = K_TIPO_DOC_EFECTIVO
			and		(0 = ve_cod_usuario or i.cod_usuario = ve_cod_usuario)
			and		i.fecha_ingreso between ve_fecha_inicio and to_date(To_Char(ve_fecha_termino, 'dd/mm/yyyy') || ' 23:59:59', 'dd/mm/yyyy HH24:MI:SS')
			and		i.cod_estado_ingreso not in (K_ESTADO_SIMULACION, K_ESTADO_ANULADO)
      AND   (0 = ve_cod_familia_deuda or dp.cod_familia_deuda = ve_cod_familia_deuda);
			
		ELSIF(ve_cod_tipo_documento_pago = K_TIPO_DOC_CHEQUE_FECHA)THEN -- TIPO CHEQUE FECHA
			SELECT	sum(monto)
			INTO	vs_monto  
			from	ingreso i, documento_ingreso_ch_fecha dch, documento_pago dp
			where	i.cod_ingreso = dch.cod_ingreso
			and		dp.cod_documento_pago = dch.cod_documento_pago
			and		cod_tipo_documento_pago = K_TIPO_DOC_CHEQUE_FECHA
			and		(0 = ve_cod_usuario or i.cod_usuario = ve_cod_usuario)
			and		i.fecha_ingreso between ve_fecha_inicio and to_date(To_Char(ve_fecha_termino, 'dd/mm/yyyy') || ' 23:59:59', 'dd/mm/yyyy HH24:MI:SS')
			and		i.cod_estado_ingreso not in (K_ESTADO_SIMULACION, K_ESTADO_ANULADO)
      AND   (0 = ve_cod_familia_deuda or dp.cod_familia_deuda = ve_cod_familia_deuda);
			
		ELSIF(ve_cod_tipo_documento_pago = K_TIPO_DOC_DEPOSITO)THEN -- TIPO DEPOSITO
			SELECT	sum(monto)
			INTO	vs_monto   
			from	ingreso i, documento_ingreso di, documento_pago dp
			where	i.cod_ingreso = di.cod_ingreso 
			and		di.cod_documento_pago = dp.cod_documento_pago
			and		cod_tipo_documento_pago = K_TIPO_DOC_DEPOSITO
			and		(0 = ve_cod_usuario or i.cod_usuario = ve_cod_usuario)
			and		i.fecha_ingreso between ve_fecha_inicio and to_date(To_Char(ve_fecha_termino, 'dd/mm/yyyy') || ' 23:59:59', 'dd/mm/yyyy HH24:MI:SS')
			and		i.cod_estado_ingreso not in (K_ESTADO_SIMULACION, K_ESTADO_ANULADO)
      AND   (0 = ve_cod_familia_deuda or dp.cod_familia_deuda = ve_cod_familia_deuda);
			
		ELSIF(ve_cod_tipo_documento_pago = K_TIPO_DESCUENTO)THEN  -- MONTO DESCUENTO DE ITEM_INGRESO
			SELECT	round(sum (monto_descuento * f_mon_get_valor_moneda(3, i.fecha_ingreso)),0)
			INTO	vs_monto 
			from	ingreso i, item_ingreso ii, descuento_ingreso di
			where	i.cod_ingreso = ii.cod_ingreso
			and		i.cod_ingreso = di.cod_ingreso
			and		(0 = ve_cod_usuario or i.cod_usuario = ve_cod_usuario)
			and		i.fecha_ingreso between ve_fecha_inicio and to_date(To_Char(ve_fecha_termino, 'dd/mm/yyyy') || ' 23:59:59', 'dd/mm/yyyy HH24:MI:SS')
			and		i.cod_estado_ingreso not in (K_ESTADO_SIMULACION, K_ESTADO_ANULADO)
      AND   (0 = ve_cod_familia_deuda or i.cod_familia_deuda = ve_cod_familia_deuda);
			
		END IF;

		RETURN Nvl(vs_monto,0);
	END;
END;
/