PROMPT CREATE OR REPLACE PROCEDURE spu_abono
CREATE OR REPLACE PROCEDURE spu_abono (ve_fecha_abono			IN	DATE
									  ,ve_cod_usuario			IN	NUMBER
									  ,ve_monto_capital			IN	NUMBER
									  ,ve_monto_interes			IN	NUMBER
									  ,ve_monto_multa			IN	NUMBER
									  ,ve_monto_pesos			IN	NUMBER
									  ,ve_diferencia_pesos		IN	NUMBER
									  ,ve_cod_plan_pago			IN	NUMBER
									  ,ve_cod_deuda				IN	NUMBER
									  ,ve_cod_abono_desde		IN	NUMBER
									  ,ve_cod_origen_pago		IN	NUMBER
									  ,ve_cod_sub_boleta		IN	NUMBER
									  ,ve_cod_documento_pago	IN	NUMBER
									  ,ve_cod_comprobante		IN	NUMBER
									  ,vs_cod_abono				IN	OUT	NUMBER)
AS
/****************************************************************************************************
   NAME:       spu_abono

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        09/11/2009   Sergio Castro   Crea abonos

   NOTAS:


*****************************************************************************************************/
BEGIN
	DECLARE K_ESTADO_PAGO_VIGENTE		NUMBER:=1;
			vl_monto_capital			NUMBER;
			vl_monto_interes			NUMBER;
			vl_monto_multa				NUMBER;
			vl_monto_pesos				NUMBER;
			vl_excedente_moneda		NUMBER;
			vl_cod_componente_deuda	NUMBER;
      vl_cod_deudor NUMBER;
      vl_excedente_pesos NUMBER;
      vl_cod_moneda NUMBER;
      KL_DEUDA_NO_EXIGIBLE NUMBER:=1;
      vl_factor_K					NUMBER;
	    vl_factor_IN				NUMBER;
	    vl_factor_IP				NUMBER;
      vl_monto_capital_pesos NUMBER;
      vl_monto_interes_pesos NUMBER;
      vl_monto_multa_pesos NUMBER;
      vl_valor_moneda NUMBER;
      vl_monto_abono NUMBER;
      vl_nom_comprobante VARCHAR2(100);
      KL_EST_COMP_APROBADO  NUMBER:=2;
      KL_TIPO_COMP_INGRESO  NUMBER:=1;
      KL_ORIGEN_COMP_ING_PAGO NUMBER:=11;
      vl_cod_comprobante  NUMBER;
      vl_cod_excedente  NUMBER;
			vl_cod_familia_deuda  NUMBER;
      K_CONCEPTO_CUENTA_EXCEDENTE NUMBER:=19;
      vl_cod_tipo_deuda VARCHAR2(20);
      vl_cod_analitico  NUMBER;
      vl_cod_abono NUMBER;
      vl_cta_excedente  NUMBER;
      K_COMPONENTE_DEUDA_NO_EXIGIBLE	NUMBER:=1;
      KL_ESTADO_NO_EXIGIBLE NUMBER:=1;
      vl_cod_estado_deuda NUMBER;
      KL_ESTADO_EXIGIBLE  NUMBER:=2;
      K_ORIGEN_CAJA NUMBER:=1;
      K_PLAN_PAGO_PAGADA NUMBER:=3; --PAGADA
	  vl_nro_cuota	NUMBER;
	  vl_cod_estado_plan_pago	NUMBER;
	  vl_cod_reprogramacion		NUMBER;

      CURSOR C_PLAN_PAGO (vc_cod_deuda number) IS
      SELECT cod_plan_pago
            ,ano_cobro
      FROM plan_pago
      WHERE f_pp_saldo_plan_pago (cod_plan_pago, ve_fecha_abono) > 0
        AND cod_deuda = vc_cod_deuda
      ORDER BY nro_cuota ASC;

      CURSOR C_DEUDA (vc_cod_deudor NUMBER, vc_cod_familia_deuda NUMBER) IS
      SELECT cod_deuda
      FROM deuda d, tipo_deuda td
      WHERE cod_deudor = vc_cod_deudor
          AND d.cod_tipo_deuda = td.cod_tipo_deuda
          AND td.cod_familia_deuda = vc_cod_familia_deuda
          AND	d.cod_estado_deuda = KL_ESTADO_EXIGIBLE
      ORDER BY Abs(cod_deuda -  ve_cod_deuda);

	BEGIN

		--se llena el cod_tipo_deuda
		SELECT  cod_tipo_deuda
    	INTO   vl_cod_tipo_deuda
    	FROM deuda
    	WHERE COD_DEUDA = ve_cod_deuda;

		-- calcula el componente del pago
		vl_cod_componente_deuda := f_pp_componente_pago(ve_cod_plan_pago, ve_fecha_abono);

    vs_cod_abono:=NULL; --para el caso cuando donde no cree el abono uno, porque los saldos son cero

		vl_monto_capital := ve_monto_capital;
		vl_monto_interes := ve_monto_interes;
		vl_monto_multa := ve_monto_multa;

		sp_ingreso_distribuye(ve_cod_plan_pago
							 ,ve_fecha_abono
							 ,vl_cod_componente_deuda
							 ,vl_monto_capital
							 ,vl_monto_interes
							 ,vl_monto_multa
							 ,vl_excedente_moneda);

    --deudas nop exigible
    IF(vl_cod_componente_deuda = K_COMPONENTE_DEUDA_NO_EXIGIBLE) THEN
      vl_excedente_moneda := vl_monto_capital - f_deuda_saldo_componente(ve_cod_deuda, K_COMPONENTE_DEUDA_NO_EXIGIBLE, ve_fecha_abono);
    END IF;

    IF (vl_excedente_moneda > 0)THEN
    	/* IS, 18/01/12
     	para los ingresos de caja no crear??xcedente por montos inferiores al indicado
    	ya que son por casos donde el redondeo crea m??abono que el real.
    	por ejemplo: en la uach, ingreso de caja 1200103 que pago completo el saldo de la cuota 2010
    	pero por redondeo el sistema creo mas abonos por $4 en cuota que continuaba = 2011.
    	Esto ocasionaba adem??que los comprobantes quedaran descuadrados
    	*/
	    IF (ve_cod_origen_pago <> K_ORIGEN_CAJA or vl_excedente_moneda > 0.01) THEN

    	  SELECT Round(f_mon_get_valor_moneda (cod_moneda, ve_fecha_abono) * vl_excedente_moneda, 0)
        	  ,cod_deudor
          		,cod_familia_deuda
      	INTO vl_excedente_pesos
        	,vl_cod_deudor
        	,vl_cod_familia_deuda
      	FROM deuda de, tipo_deuda td
      	WHERE cod_deuda = ve_cod_deuda
      	AND de.cod_tipo_deuda = td.cod_tipo_deuda;

      	vl_monto_pesos := ve_monto_pesos - vl_excedente_pesos;
      ELSE
        vl_monto_pesos := ve_monto_pesos;
			  vl_excedente_moneda := 0;
      END IF;
    ELSE
      vl_monto_pesos := ve_monto_pesos;
    END IF;

      INSERT INTO abono
					(cod_abono
					,fecha_registro
					,fecha_abono
					,cod_usuario
					,cod_componente_deuda
					,monto_capital
					,monto_interes
					,monto_multa
					,monto_moneda
					,monto_pesos
					,diferencia_pesos
					,cod_plan_pago
					,cod_deuda
					,cod_abono_desde
					,cod_estado_pago
					,cod_origen_pago
					,cod_sub_boleta
					,cod_documento_pago
					,cod_comprobante)
			VALUES (abono_sq.NEXTVAL
					,SYSDATE
					,ve_fecha_abono
					,ve_cod_usuario
					,vl_cod_componente_deuda
					,vl_monto_capital
					,vl_monto_interes
					,vl_monto_multa
					,vl_monto_capital + vl_monto_interes + vl_monto_multa
					,vl_monto_pesos
					,ve_diferencia_pesos
					,ve_cod_plan_pago
					,ve_cod_deuda
					,ve_cod_abono_desde
					,K_ESTADO_PAGO_VIGENTE
					,ve_cod_origen_pago
					,ve_cod_sub_boleta
					,ve_cod_documento_pago
					,ve_cod_comprobante);

		SELECT abono_sq.CURRVAL
		INTO vs_cod_abono
		FROM dual;

	sp_pp_actualiza_estado(ve_cod_plan_pago);
	/* aqui van los cambios para REPRO marca como DOC_PAGO_INICIAL	= 'S'*/
	IF (vl_cod_tipo_deuda = 'CRPI') THEN
		SELECT	 NRO_CUOTA
				,COD_ESTADO_PLAN_PAGO
		INTO	 vl_nro_cuota
				,vl_cod_estado_plan_pago
		FROM	plan_pago
		WHERE	cod_plan_pago = ve_cod_plan_pago;

		--codigo de reprogramacion
		SELECT	COD_REPROGRAMACION
		INTO	vl_cod_reprogramacion
		FROM	REPROGRAMACION
		WHERE	COD_DEUDA_CRPI = ve_cod_deuda;

		IF (vl_nro_cuota = 1 and vl_cod_estado_plan_pago = K_PLAN_PAGO_PAGADA) THEN
			--spu_reprogramacion ('CONFIRMA_PAGO_INICIAL', vl_cod_reprogramacion);
			UPDATE	REPROGRAMACION
			SET		DOC_PAGO_INICIAL	= 'S'
					,FECHA_PAGO_INICIAL = SYSDATE
			WHERE COD_REPROGRAMACION = vl_cod_reprogramacion;
		END IF;
	END IF;
	/* fin los cambios para REPRO marca como DOC_PAGO_INICIAL	= 'S'*/
	sp_deuda_actualiza_estado(ve_cod_deuda);

    IF (vl_excedente_moneda > 0) THEN

      --recorre otras deudas y asigna excedente
      FOR b IN C_DEUDA(vl_cod_deudor, vl_cod_familia_deuda) LOOP
        SELECT cod_moneda
              ,cod_familia_deuda
              ,de.cod_tipo_deuda
              ,cod_estado_deuda
        INTO vl_cod_moneda
            ,vl_cod_familia_deuda
            ,vl_cod_tipo_deuda
            ,vl_cod_estado_deuda
        FROM deuda de, tipo_deuda td
        WHERE cod_deuda = b.cod_deuda
          AND de.cod_tipo_deuda = td.cod_tipo_deuda;

        IF (vl_cod_estado_deuda <> KL_ESTADO_NO_EXIGIBLE) THEN
          FOR a IN C_PLAN_PAGO (b.cod_deuda) LOOP
            vl_cod_componente_deuda := f_pp_componente_pago(a.cod_plan_pago, ve_fecha_abono);
            vl_monto_capital:= vl_excedente_moneda;
			      vl_monto_interes:= 0;
			      vl_monto_multa:= 0;
            vl_monto_pesos := vl_excedente_pesos;

            sp_ingreso_distribuye(a.cod_plan_pago
							    ,ve_fecha_abono
							    ,vl_cod_componente_deuda
							    ,vl_monto_capital
							    ,vl_monto_interes
							    ,vl_monto_multa
							    ,vl_excedente_moneda);

            -- el nuevo abono tendr??xcedente
            IF (vl_excedente_moneda > 0)THEN
              SELECT Round(f_mon_get_valor_moneda (vl_cod_moneda, ve_fecha_abono) * vl_excedente_moneda, 0)
              INTO vl_excedente_pesos
              FROM deuda de, tipo_deuda td
              WHERE cod_deuda = ve_cod_deuda
                AND de.cod_tipo_deuda = td.cod_tipo_deuda;

              vl_monto_pesos := vl_monto_pesos - vl_excedente_pesos;
            ELSE
              vl_excedente_pesos:=0;
            END IF;
            IF (vl_monto_pesos > 0) THEN

            --calcular distribucion de los montos
            vl_valor_moneda := f_mon_get_valor_moneda(vl_cod_moneda, ve_fecha_abono);
            vl_monto_abono := round(vl_monto_pesos / vl_valor_moneda, 4);

            /*falta considerar la deudas no exigibles*/

            vl_monto_capital_pesos := ROUND(f_pp_saldo_capital(a.cod_plan_pago,ve_fecha_abono)* vl_valor_moneda,0);
            vl_monto_interes_pesos := ROUND(f_pp_saldo_interes_normal(a.cod_plan_pago,ve_fecha_abono) * vl_valor_moneda,0);
            vl_monto_multa_pesos := ROUND(f_pp_saldo_multa(a.cod_plan_pago,ve_fecha_abono) * vl_valor_moneda,0);


            IF ((vl_monto_capital_pesos
	            + vl_monto_interes_pesos
	            + vl_monto_multa_pesos) <> 0) THEN	---INI SUMAS_TO

	            -- porporcion K, IN, IP
	            vl_factor_K  := vl_monto_capital_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
	            vl_factor_IN := vl_monto_interes_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
	            vl_factor_IP := vl_monto_multa_pesos / (vl_monto_capital_pesos + vl_monto_interes_pesos + vl_monto_multa_pesos);
            ELSE
              vl_factor_K  := 1;
              vl_factor_IN := 0;
              vl_factor_IP := 0;
            END IF;

	            vl_monto_capital := Round(vl_monto_abono * vl_factor_K, 4);
	            vl_monto_interes := Round(vl_monto_abono * vl_factor_IN, 4);
	            vl_monto_multa := Round(vl_monto_abono * vl_factor_IP, 4);

	            -- si no cuadra con el total_moneda que se esta pagando, se agrega la diferencia al abono K
	            IF ((vl_monto_capital + vl_monto_interes + vl_monto_multa) <> vl_monto_abono) THEN
		            vl_monto_capital := vl_monto_capital + (vl_monto_abono - (vl_monto_capital + vl_monto_interes + vl_monto_multa));
	            END IF;

              INSERT INTO abono
					      (cod_abono
					      ,fecha_registro
					      ,fecha_abono
					      ,cod_usuario
					      ,cod_componente_deuda
					      ,monto_capital
					      ,monto_interes
					      ,monto_multa
					      ,monto_moneda
					      ,monto_pesos
					      ,diferencia_pesos
					      ,cod_plan_pago
					      ,cod_deuda
					      ,cod_abono_desde
					      ,cod_estado_pago
					      ,cod_origen_pago
					      ,cod_sub_boleta
					      ,cod_documento_pago
					      ,cod_comprobante)
			        VALUES (abono_sq.NEXTVAL
					      ,SYSDATE
					      ,ve_fecha_abono
					      ,ve_cod_usuario
					      ,vl_cod_componente_deuda
					      ,vl_monto_capital
					      ,vl_monto_interes
					      ,vl_monto_multa
					      ,vl_monto_capital + vl_monto_interes + vl_monto_multa
					      ,vl_monto_pesos
					      ,ve_diferencia_pesos
					      ,a.cod_plan_pago
					      ,b.cod_deuda
					      ,ve_cod_abono_desde
					      ,K_ESTADO_PAGO_VIGENTE
					      ,ve_cod_origen_pago
					      ,ve_cod_sub_boleta
					      ,ve_cod_documento_pago
					      ,ve_cod_comprobante);

		          SELECT abono_sq.CURRVAL
		          INTO vl_cod_abono
		          FROM dual;

              sp_comp_ing (ve_cod_comprobante, vl_cod_abono);

	            sp_pp_actualiza_estado(a.cod_plan_pago);
    	        sp_deuda_actualiza_estado(ve_cod_deuda);

            END IF;
          END LOOP;
         END IF; --IF (vl_cod_estado_deuda <> KL_ESTADO_NO_EXIGIBLE)
      END LOOP;
      -- crea excedente
      IF (vl_excedente_moneda > 0) THEN
          spu_excedente('INSERT', NULL, ve_fecha_abono, vl_cod_deudor, vl_excedente_pesos, 1, NULL, 1, NULL, vs_cod_abono, NULL);
        --se utilizar??a cuenta de la ultima deuda para crear excedente
        vl_cta_excedente:= f_comp_get_cuenta_tipo_deuda(vl_cod_tipo_deuda, K_CONCEPTO_CUENTA_EXCEDENTE);

        spu_item_comprobante ('INSERT', NULL, ve_cod_comprobante, vl_cta_excedente,  0, vl_excedente_pesos);
        --se graba el nro de la ultima deuda
        spu_analitico ('INSERT', NULL, ve_cod_comprobante, ve_cod_deuda, vl_cta_excedente, 0, vl_excedente_pesos);

				SELECT  analitico_comprobante_sq.CURRVAL
				INTO vl_cod_analitico
				FROM dual;

        update analitico_comprobante
				SET cod_deudor = vl_cod_deudor
				where cod_analitico_comprobante = vl_cod_analitico;

      END IF;

		END IF;
	END;
END;
/

