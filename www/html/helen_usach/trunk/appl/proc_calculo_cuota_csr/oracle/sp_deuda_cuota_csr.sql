CREATE OR REPLACE PROCEDURE sp_deuda_cuota_csr(ve_cod_deuda              NUMBER
											   ,ve_ano                    NUMBER
											   ,ve_cod_usuario            NUMBER
											   ,vs_cod_comprobante_new    OUT NUMBER
											   ,ve_recalculo              VARCHAR2
                         )
AS
BEGIN
    DECLARE K_CONCEPTO_COBRO_5PORC          NUMBER := 1;
          	K_CONCEPTO_COBRO_0              NUMBER := 2;
          	K_CONCEPTO_COBRO_35             NUMBER := 3;
          	K_CONCEPTO_COBRO_20             NUMBER := 4;
          	K_CONCEPTO_COBRO_DPA            NUMBER := 5;
            K_CONCEPTO_COBRO_CR             NUMBER := 6;
          	K_ESTADO_DECL_APRO              NUMBER := 2;
          	K_TIPO_COMP_TRASPASO            NUMBER := 3;
          	K_FONDO_SOLIDARIO               NUMBER := 1;
          	K_ESTADO_COMP_PENDIENTE         NUMBER := 1;
          	K_ORIGEN_CALCULO_CUOTA_CSR      NUMBER := 20;
          	K_COMP_DEUDA_VIGENTE            NUMBER := 3;
          	K_ESTADO_PAGO_VIGENTE           NUMBER := 1;
          	K_TIPO_DEUDA_CSR                VARCHAR2(10) := 'CSR';
          	K_ORIGEN_PAGO_CSR               NUMBER := 9;
          	K_CTA_CP                        NUMBER := 2;
			K_CTA_CASTIGO					number:=4;
          	K_CTA_CONDONACION_DJ            NUMBER := 15;
          	vl_cod_concepto_cobro           NUMBER;
          	vl_valor_cuota                  NUMBER;
          	vl_monto_anual_deudor_moneda    NUMBER;
          	vl_monto_cuota_actual           NUMBER;
          	vl_cod_plan_pago                NUMBER;
          	vl_monto_capital                NUMBER;
          	vl_monto_interes                NUMBER;
          	vl_nom_comprobante              VARCHAR2(100);
          	vl_diferencia                   NUMBER;
          	vl_factor_capital               NUMBER;
          	vl_factor_interes               NUMBER;
          	vl_monto_capital_descuento      NUMBER;
          	vl_monto_interes_descuento      NUMBER;
          	vl_cod_moneda                   NUMBER;
          	vl_cta_cp                       NUMBER;
          	vl_cta_condonacion_dj           NUMBER;
          	vl_valor_moneda                 NUMBER;
          	vl_monto_diferencia_pesos       NUMBER;
          	vl_fecha_actual                 DATE;
          	vl_count                        NUMBER;
          	vl_cod_comprobante_dscto        NUMBER;
            vl_fecha_vencto                 DATE;
            vl_fecha_declaracion			DATE;
  BEGIN

    --SETAMOS LA VARIABLE FECHA
    SELECT SYSDATE
    INTO  vl_fecha_actual
    FROM DUAL;

    -- obtiene las ctas
    vl_cta_cp             := f_comp_get_cuenta_tipo_deuda (K_TIPO_DEUDA_CSR, K_CTA_CP);
    vl_cta_condonacion_dj := f_comp_get_cuenta_tipo_deuda (K_TIPO_DEUDA_CSR, K_CTA_CONDONACION_DJ);

    SELECT 	 cod_plan_pago
            ,monto_capital
            ,monto_interes
			,fecha_vencto
    INTO     vl_cod_plan_pago
           	,vl_monto_capital
           	,vl_monto_interes
			,vl_fecha_vencto
	FROM    plan_pago
    WHERE   cod_deuda = ve_cod_deuda
      AND   ano_cobro = ve_ano;

    IF (vs_cod_comprobante_new IS NULL) THEN
            -- crear comprobante
            IF (ve_recalculo = 'S') THEN
              vl_nom_comprobante := 'Re-Calculo Cuota CSR, deuda: ' || TO_CHAR(ve_cod_deuda);
            ELSE
                vl_nom_comprobante := 'Calculo Cuota CSR: ' || TO_CHAR(SYSDATE,'dd/mm/yyyy');
     		END IF;
          	spu_comprobante ('INSERT', NULL, vl_fecha_actual, ve_cod_usuario, vl_nom_comprobante, K_FONDO_SOLIDARIO, K_ESTADO_COMP_PENDIENTE, K_TIPO_COMP_TRASPASO, K_ORIGEN_CALCULO_CUOTA_CSR, ve_cod_usuario, vl_fecha_actual);
            
			SELECT  comprobante_sq.CURRVAL
		    INTO    vs_cod_comprobante_new
		    FROM    dual;
    END IF;

    IF (ve_recalculo = 'S') THEN
            SELECT	count(*)
      		INTO	vl_count
            FROM	descuento
            WHERE	cod_plan_pago = vl_cod_plan_pago
              AND	cod_origen_pago = K_ORIGEN_PAGO_CSR
              AND	cod_estado_pago = 1;    -- vigente

            IF (vl_count > 0) THEN
                vl_nom_comprobante := 'Anula descuento anterior Re-Calculo Cuota CSR, deuda: ' || TO_CHAR(ve_cod_deuda);
                spu_comprobante ('INSERT', NULL, vl_fecha_actual, ve_cod_usuario, vl_nom_comprobante, K_FONDO_SOLIDARIO, K_ESTADO_COMP_PENDIENTE, K_TIPO_COMP_TRASPASO, K_ORIGEN_CALCULO_CUOTA_CSR, ve_cod_usuario, vl_fecha_actual);

                SELECT  comprobante_sq.CURRVAL
			    INTO    vl_cod_comprobante_dscto
			    FROM    dual;

                SELECT	monto_pesos
        		INTO	vl_monto_diferencia_pesos
                FROM	descuento
                WHERE	cod_plan_pago = vl_cod_plan_pago
                  AND	cod_origen_pago = K_ORIGEN_PAGO_CSR
                  AND	cod_estado_pago = 1;    -- vigente

				/*-- si han pasado mas de 3 meses del vencto => se debe usar la cta de castigo
				-- sino se usa la cta CP
				select	FECHA_DECLARACION
				  into	vl_fecha_declaracion
				  from	DECLARACION DE, DEUDA DA
				 where	DA.COD_DEUDA = ve_cod_deuda
				   and	DA.COD_DEUDOR = DE.COD_DEUDOR
				   and	DE.ANO = ve_ano - 1
				   and	DE.COD_ESTADO_DECLARACION = K_ESTADO_DECL_APRO;
		
				IF (vl_fecha_declaracion IS NOT NULL) THEN
					IF (MONTHS_BETWEEN(vl_fecha_declaracion, vl_fecha_vencto) > 3) THEN
						vl_cta_cp := f_comp_get_cuenta_tipo_deuda ('CSR', K_CTA_CASTIGO);
			    	END IF;
				END IF;*/

                spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante_dscto, vl_cta_cp, vl_monto_diferencia_pesos, 0);
                spu_analitico ('INSERT', NULL, vl_cod_comprobante_dscto, ve_cod_deuda, vl_cta_cp, vl_monto_diferencia_pesos, 0);
                
				spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante_dscto, vl_cta_condonacion_dj, 0, vl_monto_diferencia_pesos);
                spu_analitico ('INSERT', NULL, vl_cod_comprobante_dscto, ve_cod_deuda, vl_cta_condonacion_dj, 0, vl_monto_diferencia_pesos);

                UPDATE	descuento
                   SET	cod_estado_pago = 2 -- anulado
                WHERE	cod_plan_pago = vl_cod_plan_pago
                  AND	cod_origen_pago = K_ORIGEN_PAGO_CSR;

                sp_pp_actualiza_estado (vl_cod_plan_pago);
            END IF;
        END IF;

        vl_cod_concepto_cobro := f_deuda_concepto_cs(ve_cod_deuda, ve_ano);

		-- si no presenta declaracion => no hace nada
		IF (vl_cod_concepto_cobro = K_CONCEPTO_COBRO_20
			OR vl_cod_concepto_cobro = K_CONCEPTO_COBRO_DPA) THEN
			vl_cod_concepto_cobro := K_CONCEPTO_COBRO_CR;
		END IF;

        UPDATE	plan_pago
           SET	cod_concepto_cobro = vl_cod_concepto_cobro
        WHERE	cod_deuda = ve_cod_deuda
           AND	ano_cobro = ve_ano;

		sp_log_cambio ('PLAN_PAGO', TO_CHAR(vl_cod_plan_pago), ve_cod_usuario, 'R');	-- Calculo o Re-Calculo cuota

      	IF (vl_cod_concepto_cobro = K_CONCEPTO_COBRO_CR) THEN
        	RETURN;
        END IF;

		IF(vl_cod_concepto_cobro = K_CONCEPTO_COBRO_0) THEN
			vl_valor_cuota := 0;
		ELSIF(vl_cod_concepto_cobro = K_CONCEPTO_COBRO_35) THEN
			vl_valor_cuota := 3.5;  --- CACULAR QUE REALIZE BIEN EL CACULO;
		ELSIF(vl_cod_concepto_cobro = K_CONCEPTO_COBRO_5PORC) THEN
			SELECT  NVL(Sum(monto_deudor_moneda),0)
			INTO	vl_monto_anual_deudor_moneda
			FROM    deuda da, declaracion d, declaracion_mes dm
			WHERE   da.cod_deuda = ve_cod_deuda
			AND		d.cod_deudor = da.cod_deudor
			AND		d.ano = ve_ano - 1
			AND		d.cod_estado_declaracion = K_ESTADO_DECL_APRO
			AND		dm.cod_declaracion = d.cod_declaracion;

			vl_valor_cuota := ROUND(vl_monto_anual_deudor_moneda * 0.0500, 4);
		END IF;

		-- cuota CSR
		vl_monto_cuota_actual := vl_monto_capital + vl_monto_interes;

		-- si la cuota calculada CS es menor que cuotaCSR se crea un descuento y se contabiliza
		IF(vl_valor_cuota < vl_monto_cuota_actual) THEN
			
			--anula los descuentos anteriores
            UPDATE	descuento
            SET	cod_estado_pago = 2 -- anulado
            WHERE	cod_plan_pago = vl_cod_plan_pago
            	AND	cod_origen_pago = K_ORIGEN_PAGO_CSR;
                   
			vl_diferencia := vl_monto_cuota_actual - vl_valor_cuota;

			vl_factor_capital := ROUND(vl_monto_capital / vl_monto_cuota_actual, 4);
			vl_factor_interes := ROUND(vl_monto_interes / vl_monto_cuota_actual, 4);

			vl_monto_capital_descuento  := ROUND(vl_diferencia * vl_factor_capital, 4);
			vl_monto_interes_descuento  := ROUND(vl_diferencia - vl_monto_capital_descuento, 4);

			SELECT  cod_moneda
			INTO	vl_cod_moneda
			FROM    tipo_deuda
			WHERE   cod_tipo_deuda = K_TIPO_DEUDA_CSR;

			vl_valor_moneda := f_mon_get_valor_moneda(vl_cod_moneda, SYSDATE);

			vl_monto_diferencia_pesos := ROUND(vl_diferencia * vl_valor_moneda, 0);

			spu_descuento  ('INSERT'
							,NULL
							,vl_fecha_actual
							,ve_cod_usuario
							,K_COMP_DEUDA_VIGENTE
							,vl_monto_capital_descuento
							,vl_monto_interes_descuento
							,0
							,vl_diferencia
							,vl_monto_diferencia_pesos
							,vl_cod_plan_pago
							,ve_cod_deuda
							,vs_cod_comprobante_new
							,K_ESTADO_PAGO_VIGENTE
							,K_ORIGEN_PAGO_CSR);

			sp_pp_actualiza_estado (vl_cod_plan_pago);

			/*-- si han pasado mas de 3 meses del vencto => se debe usar la cta de castigo
			-- sino se usa la cta CP 
			select	FECHA_DECLARACION
			  into	vl_fecha_declaracion
			  from	DECLARACION DE, DEUDA DA
			 where	DA.COD_DEUDA = ve_cod_deuda
			   and	DA.COD_DEUDOR = DE.COD_DEUDOR
			   and	DE.ANO = ve_ano - 1
			   and	DE.COD_ESTADO_DECLARACION = K_ESTADO_DECL_APRO;
	
			IF (vl_fecha_declaracion IS NOT NULL) THEN
				IF (MONTHS_BETWEEN(vl_fecha_declaracion, vl_fecha_vencto) > 3) THEN
					vl_cta_cp := f_comp_get_cuenta_tipo_deuda ('CSR', K_CTA_CASTIGO);
		    	END IF;
			END IF;*/

			spu_item_comprobante ('INSERT', NULL, vs_cod_comprobante_new, vl_cta_cp, 0, vl_monto_diferencia_pesos);
			spu_analitico ('INSERT', NULL, vs_cod_comprobante_new, ve_cod_deuda, vl_cta_cp, 0, vl_monto_diferencia_pesos);

			spu_item_comprobante ('INSERT', NULL, vs_cod_comprobante_new, vl_cta_condonacion_dj, vl_monto_diferencia_pesos, 0);
			spu_analitico ('INSERT', NULL, vs_cod_comprobante_new, ve_cod_deuda, vl_cta_condonacion_dj, vl_monto_diferencia_pesos, 0);
		END IF;
	END;
END sp_deuda_cuota_csr;
/

