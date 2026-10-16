PROMPT CREATE OR REPLACE PROCEDURE spp_cuota_cs
CREATE OR REPLACE PROCEDURE spp_cuota_cs (
   ve_cod_ejecucion_proceso   NUMBER
)
AS
/******************************************************************************
   NAME:       spp_cuota_cs
   DESCRIPTION Se corre en junio o cuando se hayan digitado todas las declaraciones


   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        17/02/2010   SC              Creación

   NOTES:

******************************************************************************/
BEGIN
	DECLARE	K_TIPO_DEUDA_CS				VARCHAR2(10) := 'CS';
			K_ESTADO_DEUDA_EXIGIBLE		NUMBER := 2;
			K_CTA_CONDONACIONES_VARIAS	NUMBER := 7;
			K_CALCULO_CUOTA_CS			NUMBER := 19;
			vl_cod_usuario				NUMBER;
			vl_count_deudas				NUMBER;
			vl_count					NUMBER;
			vl_i						NUMBER;
			vl_count_plan_pago			NUMBER;
			vl_cod_comprobante1_new		NUMBER;
			vl_cod_comprobante2_new		NUMBER;
			vl_cod_plan_pago			NUMBER;
			vl_cod_deuda_err			NUMBER := 0;
			vl_cod_deudor				NUMBER;
			vl_ano_cobro				NUMBER;


		CURSOR C_DEUDA_CS IS	SELECT  d.cod_deuda
								       ,d.cod_tipo_deuda
								FROM    deuda d
							            ,tipo_deuda td
							            ,PLAN_PAGO p
							    WHERE   d.cod_tipo_deuda = K_TIPO_DEUDA_CS
							    AND		d.cod_estado_deuda = K_ESTADO_DEUDA_EXIGIBLE
							    
							    AND		d.cod_tipo_deuda = td.cod_tipo_deuda
							    and		p.COD_DEUDA = d.COD_DEUDA
							    and		p.ANO_COBRO = 9999
							    and		f_pp_saldo_plan_pago(p.cod_plan_pago) > 0;
							    


	BEGIN
    --seteo de año cobro
    SELECT To_Char(SYSDATE,'yyyy')
      INTO vl_ano_cobro
    FROM   dual;


		 -- usuario que ejecuta el proceso
		SELECT	cod_usuario
		INTO	vl_cod_usuario
	    FROM	ejecucion_proceso
	    WHERE	cod_ejecucion_proceso = ve_cod_ejecucion_proceso;

		SELECT  Count(*)
		INTO	vl_count_deudas
	    FROM    deuda d
						,tipo_deuda td
						,PLAN_PAGO p
		WHERE   d.cod_tipo_deuda = K_TIPO_DEUDA_CS
		AND		d.cod_estado_deuda = K_ESTADO_DEUDA_EXIGIBLE
							    
		AND		d.cod_tipo_deuda = td.cod_tipo_deuda
		and		p.COD_DEUDA = d.COD_DEUDA
		and		p.ANO_COBRO = 9999
		and		f_pp_saldo_plan_pago(p.cod_plan_pago) > 0;


	    vl_count  := vl_count_deudas;
		spu_ejecucion_proceso ('SET_TOTAL', ve_cod_ejecucion_proceso, vl_count);

		vl_i := 1;
		vl_count_plan_pago := 0;
		vl_cod_comprobante1_new := NULL;
		vl_cod_comprobante2_new := NULL;

		------------------------------------------------------------------------------------------
		-- MAIN
		------------------------------------------------------------------------------------------
		FOR A IN C_DEUDA_CS LOOP
			vl_cod_deuda_err := a.cod_deuda;
			-- verifica si el proceso ha sido cancelado
			IF (f_ep_cancela_proceso (ve_cod_ejecucion_proceso) = 'S') THEN
				sp_comp_simplifica (vl_cod_comprobante1_new);
				sp_comp_simplifica (vl_cod_comprobante2_new);
				RETURN;
			END IF;

			-- Saber si existe plan de pago para el año actual
			SELECT  Count(*)
			INTO	vl_count_plan_pago
			FROM    plan_pago
			WHERE   ano_cobro = vl_ano_cobro
			AND		cod_deuda = a.cod_deuda;
      --Dbms_Output.put_line('aa  '||a.cod_deuda||' '||vl_ano_cobro||' '||vl_count_plan_pago);
			IF (vl_count_plan_pago = 0) THEN
				sp_deuda_cuota_cs (a.cod_deuda, vl_ano_cobro ,'N', ve_cod_ejecucion_proceso, vl_cod_comprobante1_new, vl_cod_comprobante2_new, vl_cod_usuario);
			END IF;

			spu_ejecucion_proceso ('SET_ACTUAL', ve_cod_ejecucion_proceso, vl_i);

			SELECT  cod_deudor
			INTO	vl_cod_deudor
			FROM    deuda
			WHERE   cod_deuda = a.cod_deuda;

			spu_bitacora_deudor ('Calculo cuota CS, deuda : ' || TO_CHAR(a.cod_deuda) , K_CALCULO_CUOTA_CS, vl_cod_deudor, vl_cod_usuario);

			vl_i := vl_i + 1;
		END LOOP;
		------------------------------------------------------------------------------------------
		-- END MAIN
		------------------------------------------------------------------------------------------
		sp_comp_simplifica (vl_cod_comprobante1_new);
		sp_comp_simplifica (vl_cod_comprobante2_new);
		vl_i := vl_i - 1;
		spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, vl_i);

  EXCEPTION
        WHEN OTHERS THEN
        -- Si ocurre un error de la BD al grabar un registro lo registra en el LOG. del sistema
        spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', SQLCODE, SUBSTR(SQLERRM,1,100), 'Deuda: ' || To_Char(vl_cod_deuda_err));
    	sp_comp_simplifica (vl_cod_comprobante1_new);
		sp_comp_simplifica (vl_cod_comprobante2_new);
  		spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, vl_i);
   END;
END spp_cuota_cs;
/
