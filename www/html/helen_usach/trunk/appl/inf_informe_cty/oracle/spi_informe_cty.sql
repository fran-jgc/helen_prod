CREATE OR REPLACE PROCEDURE spi_informe_cty (ve_cod_tipo_deuda	VARCHAR2
											,ve_cod_cartera		NUMBER
											,ve_ano				NUMBER)                                           
AS
BEGIN
  DECLARE	vl_monto_ingreso_deudor		NUMBER;
			vl_monto_ingreso_conyuge	NUMBER;
			vc_cod_deudor				NUMBER;
			vc_cod_deuda				NUMBER;
			vc_rut						VARCHAR2(10);
			vc_fecha_vencto				VARCHAR2(20);
			vc_nom_concepto_cobro		VARCHAR2(100);
			vc_monto_cty				NUMBER;
			vc_monto_cat				NUMBER;
			vc_monto_pec				NUMBER;
			vc_cod_plan_pago			NUMBER;
			vl_count					NUMBER;
			vl_porc1					NUMBER;
			vl_porc2					NUMBER;
			vl_monto_dscto_CSR			NUMBER;
			vl_fecha_fin				DATE;

    CURSOR c_plan(vl_fecha_fin DATE) IS
    SELECT de.cod_deudor
          ,REPLACE(To_Char(de.rut, '99,999,999'), ',', '.') || '-' || de.dig_verif rut
          ,d.cod_deuda
          ,To_char(p.fecha_vencto, 'dd/mm/yyyy') fecha_vencto
          ,c.nom_concepto_cobro
          ,p.monto_capital + p.monto_interes monto_cty
          ,f_deuda_cat(d.cod_deuda, p.ano_cobro) monto_cat
          ,f_pp_monto_abono (p.cod_plan_pago, 'MONEDA', vl_fecha_fin) monto_pec
          ,p.cod_plan_pago
    FROM	plan_pago p	left outer join concepto_cobro c on p.cod_concepto_cobro = c.cod_concepto_cobro,
    		deuda d,	deudor de    
    WHERE p.ano_cobro = ve_ano
      AND d.cod_deuda = p.cod_deuda
      AND d.cod_tipo_deuda = ve_cod_tipo_deuda
      AND (ve_cod_cartera = 0 OR d.cod_cartera = ve_cod_cartera)
      AND de.cod_deudor = d.cod_deudor
      AND d.cod_estado_deuda <> 1;	-- no exigible
  BEGIN

	vl_fecha_fin := To_Date('31/12/' || To_Char(ve_ano), 'dd/mm/yyyy') + 86399/(24*60*3600);

    FOR a IN c_plan(vl_fecha_fin) LOOP
    	vc_monto_cty	:= a.monto_cty;
		vc_monto_cat	:= a.monto_cat;
		vc_monto_pec	:= a.monto_pec;
		
		IF (ve_cod_tipo_deuda='CSR')THEN
			vl_monto_dscto_CSR := 0;

			SELECT	nvl(SUM(d.MONTO_MONEDA), 0)
			INTO	vl_monto_dscto_CSR
			FROM	DESCUENTO d
			WHERE	d.COD_PLAN_PAGO = a.cod_plan_pago
			  AND	d.COD_ESTADO_PAGO = 1	-- vigente
			  AND	d.COD_ORIGEN_PAGO = 9;	-- origen DJ CSR

			-- para CSR la CAT es simplemente la cuota K + IN,
			-- y la CTY es considerando los dsctos por presentar la DJ
			vc_monto_cat := a.monto_cty;
			vc_monto_cty := a.monto_cty - vl_monto_dscto_CSR;
		END IF;

		SELECT	 Sum(dm.monto_deudor_moneda)
				,Sum(dm.monto_conyuge_moneda)
		INTO	 vl_monto_ingreso_deudor
				,vl_monto_ingreso_conyuge
		FROM declaracion d, declaracion_mes dm
		WHERE d.cod_deudor = a.cod_deudor
		AND d.ano = ve_ano - 1
		AND dm.cod_declaracion = d.cod_declaracion;

    	-- cruzado
		SELECT	COUNT(*)
		INTO	vl_count
		FROM	cruzado
		WHERE	COD_DEUDA = a.cod_deuda;

		IF(vl_count = 0) THEN
			vl_porc1 := 100;
		ELSE
			SELECT	porc
			INTO	vl_porc1
			FROM	CRUZADO
			WHERE	COD_DEUDA = a.cod_deuda
			  AND	COD_UNIVERSIDAD = f_get_parametro(3)
              AND ROWNUM = 1;
		END IF;

		-- cruzado interno
		SELECT	COUNT(*)
		INTO	vl_count
		FROM	CRUZADO_INTERNO
		WHERE	COD_DEUDA = a.cod_deuda;

		IF (vl_count = 0) THEN
			vl_porc2 := 100;
		ELSE
			SELECT	porc
			INTO	vl_porc2
			FROM	CRUZADO_INTERNO
			WHERE	COD_DEUDA = a.cod_deuda
			  AND	COD_ACREEDOR_INTERNO = 2;	-- FONDO acreedor interno
		END IF;

		vc_monto_cty	:= vc_monto_cty * vl_porc1 /100.0000 * vl_porc2 /100.0000;
		vc_monto_cat	:= vc_monto_cat * vl_porc1 /100.0000 * vl_porc2 /100.0000;
		vc_monto_pec	:= vc_monto_pec * vl_porc1 /100.0000 * vl_porc2 /100.0000;

		INSERT INTO TEMP_INFORME_CTY
					(RUT
					,ANO
					,FECHA_VENCTO
					,MONTO_INGRESO_DEUDOR
					,MONTO_INGRESO_CONYUGE
					,NOM_CONCEPTO_COBRO
					,MONTO_CTY
					,MONTO_CAT
					,MONTO_PEC)
				  VALUES
					(a.rut
					,ve_ano
					,a.fecha_vencto
					,NVL(vl_monto_ingreso_deudor,0)
					,NVL(vl_monto_ingreso_conyuge,0)
					,a.nom_concepto_cobro
					,NVL(vc_monto_cty,0)
					,NVL(vc_monto_cat,0)
					,NVL(vc_monto_pec,0));
    END LOOP;
  END;
END spi_informe_cty;
/