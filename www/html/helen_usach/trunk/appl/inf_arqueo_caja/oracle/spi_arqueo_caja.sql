CREATE OR REPLACE PROCEDURE spi_arqueo_caja(ve_cod_usuario		IN  NUMBER
										                      , ve_fecha_inicio	  IN  DATE
										                      , ve_fecha_termino	IN  DATE
										                      , ve_formato		    IN  VARCHAR2
                                          , ve_cod_familia_deuda IN NUMBER)
AS
BEGIN
	DECLARE K_TIPO_DOC_CHEQUE			NUMBER:= 1;
		    K_TIPO_OTRO				    NUMBER:= 0;
		    K_TIPO_DOC_DEPOSITO		    NUMBER:= 3;
		    K_TIPO_DOC_EFECTIVO		    NUMBER:= 4;
		    K_TIPO_DOC_CHEQUE_A_FECHA	NUMBER:= 5;
		    K_TIPO_DESCUENTO			NUMBER:= 10;
		    K_ESTADO_SIMULACION			NUMBER:= 1000;
			K_ESTADO_ANULADO			NUMBER:= 2;
			vl_sum_efectivo				NUMBER:= 0;
			vl_sum_ch_al_dia			NUMBER:= 0;
			vl_sum_ch_fecha				NUMBER:= 0;
			vl_sum_otros				NUMBER:= 0;
			vl_sum_deposito				NUMBER:= 0;
			vl_sum_total				NUMBER:= 0;
			vl_sum_descuento			NUMBER:= 0;

	CURSOR C_DETALLE_SUM
		IS	SELECT	 I.COD_ESTADO_INGRESO
					,f_ingreso_get_detalle(I.COD_INGRESO, K_TIPO_DOC_EFECTIVO) EFECTIVO		--sum_efectivo
					,f_ingreso_get_detalle(I.COD_INGRESO, K_TIPO_DOC_CHEQUE) CHEQUE_AL_DIA	--sum_ch_al_dia		
					,f_ingreso_get_detalle(I.COD_INGRESO, K_TIPO_DOC_CHEQUE_A_FECHA) CHEQUE_FECHA --sum_ch_fecha
					,f_ingreso_get_detalle(I.COD_INGRESO, K_TIPO_OTRO) OTROS				--sum_otros
					,f_ingreso_get_detalle(I.COD_INGRESO, K_TIPO_DOC_DEPOSITO) DEPOSITO		--sum_deposito
					,(f_ingreso_get_detalle(I.COD_INGRESO, K_TIPO_DOC_EFECTIVO) +
					 f_ingreso_get_detalle(I.COD_INGRESO, K_TIPO_DOC_CHEQUE) +
					 f_ingreso_get_detalle(I.COD_INGRESO, K_TIPO_DOC_CHEQUE_A_FECHA) +
					 f_ingreso_get_detalle(I.COD_INGRESO, K_TIPO_OTRO) +
					 f_ingreso_get_detalle(I.COD_INGRESO, K_TIPO_DOC_DEPOSITO))  TOTAL		--sum_total
					,f_ingreso_get_detalle(I.COD_INGRESO, K_TIPO_DESCUENTO) DESCUENTO		--sum_descuento
			FROM	INGRESO I, ESTADO_INGRESO  IE
			WHERE	I.COD_ESTADO_INGRESO = IE.COD_ESTADO_INGRESO
			  AND	(0 = ve_cod_usuario or i.cod_usuario = ve_cod_usuario)
			  AND	i.fecha_ingreso between ve_fecha_inicio and to_date(To_Char(ve_fecha_termino, 'dd/mm/yyyy') || ' 23:59:59', 'dd/mm/yyyy HH24:MI:SS')
			  AND	I.COD_ESTADO_INGRESO <> K_ESTADO_SIMULACION
        AND (0 = ve_cod_familia_deuda or i.cod_familia_deuda = ve_cod_familia_deuda);

  BEGIN
	  --insert into @TEMPO 
	  IF(ve_formato = 'RESUMEN')THEN
		  INSERT INTO	TEMP_ARQUEO_CAJA
						(EFECTIVO
						,CHEQUE_AL_DIA
						,CHEQUE_FECHA
						,OTROS
						,DESCUENTO
						,DEPOSITO
						,SUBTOTAL1
						,SUBTOTAL2
						,TOTAL
						,COUNT_CH_AL_DIA
						,COUNT_CH_A_FECHA
						,COUNT_OTROS
						,COUNT_DESCUENTO
						,COUNT_DEPOSITO
						,TOTAL_COUNT1
						,TOTAL_COUNT2
						,NOM_ESTADO_INGRESO
						,FECHA_INGRESO
						,COD_INGRESO
						,SUM_EFECTIVO
						,SUM_CH_AL_DIA
						,SUM_CH_FECHA
						,SUM_OTROS
						,SUM_DEPOSITOS
						,SUM_TOTAL
						,SUM_DESCUENTO)  
			SELECT		f_ingreso_calculo(K_TIPO_DOC_EFECTIVO		, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda) EFECTIVO
					    ,f_ingreso_calculo(K_TIPO_DOC_CHEQUE		, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda)CHEQUE_AL_DIA
					    ,f_ingreso_calculo(K_TIPO_DOC_CHEQUE_A_FECHA, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda)CHEQUE_FECHA
					    ,f_ingreso_calculo(K_TIPO_OTRO	, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda)OTROS
					    ,f_ingreso_calculo(K_TIPO_DESCUENTO		, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda)DESCUENTO
						,f_ingreso_calculo(K_TIPO_DOC_DEPOSITO		, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda)DEPOSITO
						
						,(f_ingreso_calculo(K_TIPO_DOC_EFECTIVO		, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda) +
						f_ingreso_calculo(K_TIPO_DOC_CHEQUE		, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda) +
						f_ingreso_calculo(K_TIPO_DOC_CHEQUE_A_FECHA, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda)	+
						f_ingreso_calculo(K_TIPO_OTRO	, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda))SUBTOTAL1
						
						,(f_ingreso_calculo(K_TIPO_DESCUENTO	, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda) +
						f_ingreso_calculo(K_TIPO_DOC_DEPOSITO		, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda))SUBTOTAL2
						
						,(f_ingreso_calculo(K_TIPO_DOC_EFECTIVO		, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda) +
						f_ingreso_calculo(K_TIPO_DOC_CHEQUE		, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda) +
						f_ingreso_calculo(K_TIPO_DOC_CHEQUE_A_FECHA, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda)	+
						f_ingreso_calculo(K_TIPO_OTRO	, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda) +
						f_ingreso_calculo(K_TIPO_DESCUENTO	, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda) +
						f_ingreso_calculo(K_TIPO_DOC_DEPOSITO		, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda)) TOTAL
						
						,f_ingreso_calculo_cant(K_TIPO_DOC_CHEQUE		, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda) COUNT_CH_AL_DIA
						,f_ingreso_calculo_cant(K_TIPO_DOC_CHEQUE_A_FECHA	, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda) COUNT_CH_A_FECHA
						,f_ingreso_calculo_cant(K_TIPO_OTRO	, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda) COUNT_OTROS
						,f_ingreso_calculo_cant(K_TIPO_DESCUENTO	, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda) COUNT_DESCUENTO
						,f_ingreso_calculo_cant(K_TIPO_DOC_DEPOSITO	, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda) COUNT_DEPOSITO
						,(f_ingreso_calculo_cant(K_TIPO_DOC_CHEQUE		, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda) +
						f_ingreso_calculo_cant(K_TIPO_DOC_CHEQUE_A_FECHA	, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda) +
						f_ingreso_calculo_cant(K_TIPO_OTRO	, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda))TOTAL_COUNT1
						,(f_ingreso_calculo_cant(K_TIPO_DESCUENTO	, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda) +
						f_ingreso_calculo_cant(K_TIPO_DOC_DEPOSITO	, ve_cod_usuario, ve_fecha_inicio, ve_fecha_termino, ve_cod_familia_deuda)) TOTAL_COUNT2
						,NULL -- NOM_ESTADO_INGRESO
						,NULL -- FECHA_INGRESO
						,NULL -- COD_INGRESO
						,NULL -- SUM_EFECTIVO
						,NULL -- SUM_CH_AL_DIA
						,NULL -- SUM_CH_FECHA
						,NULL -- SUM_OTROS
						,NULL -- SUM_DEPOSITOS
						,NULL -- SUM_TOTAL
						,NULL -- SUM_DESCUENTO
				FROM    DUAL;
	  ELSIF (ve_formato = 'DETALLE') THEN
		FOR A IN C_DETALLE_SUM	LOOP
			IF(A.COD_ESTADO_INGRESO <> K_ESTADO_ANULADO ) THEN
				vl_sum_efectivo		:= vl_sum_efectivo	+ A.EFECTIVO;
				vl_sum_ch_al_dia	:= vl_sum_ch_al_dia	+ A.CHEQUE_AL_DIA;
				vl_sum_ch_fecha		:= vl_sum_ch_fecha	+ A.CHEQUE_FECHA;
				vl_sum_otros		:= vl_sum_otros		+ A.OTROS;
				vl_sum_deposito		:= vl_sum_deposito	+ A.DEPOSITO;
				vl_sum_total		:= vl_sum_total		+ A.TOTAL;
				vl_sum_descuento	:= vl_sum_descuento	+ A.DESCUENTO;
			END IF;
		END LOOP;

			INSERT INTO	TEMP_ARQUEO_CAJA
						(EFECTIVO
						,CHEQUE_AL_DIA
						,CHEQUE_FECHA
						,OTROS
						,DESCUENTO
						,DEPOSITO
						,SUBTOTAL1
						,SUBTOTAL2
						,TOTAL
						,COUNT_CH_AL_DIA
						,COUNT_CH_A_FECHA
						,COUNT_OTROS
						,COUNT_DESCUENTO
						,COUNT_DEPOSITO
						,TOTAL_COUNT1
						,TOTAL_COUNT2
						,NOM_ESTADO_INGRESO
						,FECHA_INGRESO
						,COD_INGRESO
						,SUM_EFECTIVO
						,SUM_CH_AL_DIA
						,SUM_CH_FECHA
						,SUM_OTROS
						,SUM_DEPOSITOS
						,SUM_TOTAL
						,SUM_DESCUENTO)
			  SELECT	f_ingreso_get_detalle(I.COD_INGRESO, K_TIPO_DOC_EFECTIVO) EFECTIVO
						,f_ingreso_get_detalle(I.COD_INGRESO, K_TIPO_DOC_CHEQUE) CHEQUE_AL_DIA			
						,f_ingreso_get_detalle(I.COD_INGRESO, K_TIPO_DOC_CHEQUE_A_FECHA) CHEQUE_FECHA
						,f_ingreso_get_detalle(I.COD_INGRESO, K_TIPO_OTRO) OTROS
						,f_ingreso_get_detalle(I.COD_INGRESO, K_TIPO_DESCUENTO) DESCUENTO
						,f_ingreso_get_detalle(I.COD_INGRESO, K_TIPO_DOC_DEPOSITO) DEPOSITO
						,NULL -- SUBTOTAL1
						,NULL -- SUBTOTAL2	
						,(f_ingreso_get_detalle(I.COD_INGRESO, K_TIPO_DOC_EFECTIVO) +
						 f_ingreso_get_detalle(I.COD_INGRESO, K_TIPO_DOC_CHEQUE) +
						 f_ingreso_get_detalle(I.COD_INGRESO, K_TIPO_DOC_CHEQUE_A_FECHA) +
						 f_ingreso_get_detalle(I.COD_INGRESO, K_TIPO_OTRO) +
						 f_ingreso_get_detalle(I.COD_INGRESO, K_TIPO_DOC_DEPOSITO))  TOTAL
						,NULL -- COUNT_CH_AL_DIA
						,NULL -- COUNT_CH_A_FECHA
						,NULL -- COUNT_OTROS
						,NULL -- COUNT_DESCUENTO
						,NULL -- COUNT_DEPOSITO
						,NULL -- TOTAL_COUNT1
						,NULL -- TOTAL_COUNT2
						,NOM_ESTADO_INGRESO
						,TO_char(FECHA_INGRESO, 'dd/mm/yyyy') FECHA_INGRESO
						,COD_INGRESO
						,vl_sum_efectivo	-- SUM_EFECTIVO
						,vl_sum_ch_al_dia	-- SUM_CH_AL_DIA
						,vl_sum_ch_fecha	-- SUM_CH_FECHA
						,vl_sum_otros		-- SUM_OTROS
						,vl_sum_deposito	-- SUM_DEPOSITOS
						,vl_sum_total		-- SUM_TOTAL
						,vl_sum_descuento	-- SUM_DESCUENTO
				FROM	INGRESO I, ESTADO_INGRESO  IE
				WHERE	I.COD_ESTADO_INGRESO = IE.COD_ESTADO_INGRESO
				  AND	(0 = ve_cod_usuario or i.cod_usuario = ve_cod_usuario)
				  AND	i.fecha_ingreso between ve_fecha_inicio and to_date(To_Char(ve_fecha_termino, 'dd/mm/yyyy') || ' 23:59:59', 'dd/mm/yyyy HH24:MI:SS')
				  AND	i.COD_ESTADO_INGRESO <> K_ESTADO_SIMULACION
          AND (0 = ve_cod_familia_deuda or i.cod_familia_deuda = ve_cod_familia_deuda); 
	  END IF;
  END;
END;
/
