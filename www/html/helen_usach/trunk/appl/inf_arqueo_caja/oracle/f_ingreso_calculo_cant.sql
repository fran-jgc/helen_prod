/*Esta funcion devuelve la cantidad de documentos por tipo_documento_pago. USADA EN INFORME ARQUEO CAJA*/
CREATE OR REPLACE function f_ingreso_calculo_cant(ve_cod_tipo_documento_pago  NUMBER
                                                 , ve_cod_usuario             NUMBER
                                                 , ve_fecha_inicio            DATE
                                                 , ve_fecha_termino           DATE
                                                 , ve_cod_familia_deuda       NUMBER)
RETURN NUMBER
as

BEGIN
	declare vs_count					      NUMBER;
			    K_TIPO_OTRO				      NUMBER:=0;	
			    K_TIPO_DOC_CHEQUE			  NUMBER:=1;
			    K_TIPO_DOC_DEPOSITO		  NUMBER:=3;
			    K_TIPO_DOC_CHEQUE_FECHA	NUMBER:=5;
			    K_TIPO_DESCUENTO			  NUMBER:=10;
			    K_TIPO_DOC_EFECTIVO		  NUMBER:=4;
			    K_ESTADO_SIMULACION		NUMBER:=1000;
				K_ESTADO_ANULADO		NUMBER:=2;
	
  BEGIN
	IF(ve_cod_tipo_documento_pago = K_TIPO_OTRO)THEN -- TIPO OTROS
		SELECT  count(*)
    INTO    vs_count 
		from	  ingreso i, documento_ingreso di, documento_pago dp
		where	  i.cod_ingreso = di.cod_ingreso
		and		  dp.cod_documento_pago = di.cod_documento_pago
		and		  cod_tipo_documento_pago NOT in(K_TIPO_DOC_CHEQUE, K_TIPO_DOC_DEPOSITO, K_TIPO_DOC_EFECTIVO, K_TIPO_DOC_CHEQUE_FECHA)
		and		  (0 = ve_cod_usuario or i.cod_usuario = ve_cod_usuario)
		and		  i.fecha_ingreso between ve_fecha_inicio and to_date(To_Char(ve_fecha_termino, 'dd/mm/yyyy') || ' 23:59:59', 'dd/mm/yyyy HH24:MI:SS')
		and		i.cod_estado_ingreso not in (K_ESTADO_SIMULACION, K_ESTADO_ANULADO)
    and		  (0 = ve_cod_familia_deuda or dp.cod_familia_deuda = ve_cod_familia_deuda);

	ELSIF(ve_cod_tipo_documento_pago = K_TIPO_DOC_CHEQUE)THEN -- TIPO CHEQUE
		SELECT	count(*)
    INTO    vs_count
		from	  ingreso i, documento_ingreso di, documento_pago dp
		where	  i.cod_ingreso = di.cod_ingreso
		and		  dp.cod_documento_pago = di.cod_documento_pago
		and		  cod_tipo_documento_pago = K_TIPO_DOC_CHEQUE
		and		  (0 = ve_cod_usuario or i.cod_usuario = ve_cod_usuario)
		and		  i.fecha_ingreso between ve_fecha_inicio and to_date(To_Char(ve_fecha_termino, 'dd/mm/yyyy') || ' 23:59:59', 'dd/mm/yyyy HH24:MI:SS')
		and		i.cod_estado_ingreso not in (K_ESTADO_SIMULACION, K_ESTADO_ANULADO)
    and		  (0 = ve_cod_familia_deuda or dp.cod_familia_deuda = ve_cod_familia_deuda);
	
	ELSIF(ve_cod_tipo_documento_pago = K_TIPO_DOC_CHEQUE_FECHA)THEN-- TIPO CHEQUE FECHA
		SELECT	count(*)
    INTO    vs_count  
		from	ingreso i, documento_ingreso_ch_fecha dch, documento_pago dp
		where	i.cod_ingreso = dch.cod_ingreso
		and		dp.cod_documento_pago = dch.cod_documento_pago
		and		  cod_tipo_documento_pago = K_TIPO_DOC_CHEQUE_FECHA
		and		  (0 = ve_cod_usuario or i.cod_usuario = ve_cod_usuario)
		and		  i.fecha_ingreso between ve_fecha_inicio and to_date(To_Char(ve_fecha_termino, 'dd/mm/yyyy') || ' 23:59:59', 'dd/mm/yyyy HH24:MI:SS')
		and		i.cod_estado_ingreso not in (K_ESTADO_SIMULACION, K_ESTADO_ANULADO)
    and		  (0 = ve_cod_familia_deuda or dp.cod_familia_deuda = ve_cod_familia_deuda);

	ELSIF(ve_cod_tipo_documento_pago = K_TIPO_DOC_DEPOSITO)THEN -- TIPO DEPOSITO
		select	count(*)
    INTO    vs_count 
		from	  ingreso i, documento_ingreso di, documento_pago dp
		where	  i.cod_ingreso = di.cod_ingreso 
		and		  di.cod_documento_pago = dp.cod_documento_pago
		and		  cod_tipo_documento_pago = K_TIPO_DOC_DEPOSITO
		and		  (0 = ve_cod_usuario or i.cod_usuario = ve_cod_usuario)
		and		  i.fecha_ingreso between ve_fecha_inicio and to_date(To_Char(ve_fecha_termino, 'dd/mm/yyyy') || ' 23:59:59', 'dd/mm/yyyy HH24:MI:SS')
		and		i.cod_estado_ingreso not in (K_ESTADO_SIMULACION, K_ESTADO_ANULADO)
    and		  (0 = ve_cod_familia_deuda or dp.cod_familia_deuda = ve_cod_familia_deuda);
	
	ELSIF(ve_cod_tipo_documento_pago = K_TIPO_DESCUENTO)THEN -- MONTO DESCUENTO DE ITEM_INGRESO
		SELECT  count(*)
    INTO    vs_count
		from	ingreso i, descuento_ingreso di
		where	i.cod_ingreso = di.cod_ingreso
		and		  (0 = ve_cod_usuario or i.cod_usuario = ve_cod_usuario)
		and		  i.fecha_ingreso between ve_fecha_inicio and to_date(To_Char(ve_fecha_termino, 'dd/mm/yyyy') || ' 23:59:59', 'dd/mm/yyyy HH24:MI:SS')
		and		i.cod_estado_ingreso not in (K_ESTADO_SIMULACION, K_ESTADO_ANULADO)
    and		  (0 = ve_cod_familia_deuda or i.cod_familia_deuda = ve_cod_familia_deuda);
	END IF; 

	RETURN NVL(vs_count,0);
  END;
END;
/