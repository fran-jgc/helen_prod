PROMPT CREATE OR REPLACE PROCEDURE spp_comprobante_castigo
CREATE OR REPLACE PROCEDURE spp_comprobante_castigo (
      VE_COD_EJECUCION_PROCESO  NUMBER
      ,VE_FECHA_VENCTO          DATE)
AS
/******************************************************************************
   NAME:       spp_om_obtiene_mora
   DESCRIPTION Este proceso se corre después del vencimiento de las cuotas,
               es decir en enero y en junio (para el caso CS) de cada año

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        21/10/2009   SC              Realiza la obtencion de mora

   NOTES:

******************************************************************************/
BEGIN
  DECLARE
    K_DEUDA_EXIGIBLE            NUMBER := 2;
    K_PLAN_PAGO_EMITIDA         NUMBER := 1;
    K_PLAN_PAGO_ABONADA         NUMBER := 2;
    K_PLAN_PAGO_PAGADA          NUMBER := 3;
    K_PLAN_PAGO_MOROSA          NUMBER := 4;
    K_COMP_PENDIENTE            NUMBER := 1;
    K_COMP_TRASPASO             NUMBER := 3;
    K_COMP_ORIGEN_CASTIGO       NUMBER := 26;
    K_CTA_CASTIGO_VENCIDA       NUMBER := 4;
    K_CTA_CP                    NUMBER := 2;
    K_COMPONENTE_VIGENTE        NUMBER := 3;
    K_ESTADO_PAGO_VIGENTE       NUMBER := 1;
    K_ORIGEN_NO_COBRABLE        NUMBER := 8;
    vl_cod_estado_plan_pago     estado_plan_pago.cod_estado_plan_pago%TYPE;
    vl_cod_usuario              NUMBER;
    vl_count_plan_pago          NUMBER;
    vl_count                    NUMBER;
    i                           NUMBER;
    vl_cta_castigo              cuenta_tipo_deuda.cod_cuenta_tipo_deuda%TYPE;
    vl_cta_cp                   cuenta_tipo_deuda.cod_cuenta_tipo_deuda%TYPE;
    vl_cta_condonaciones        cuenta_tipo_deuda.cod_cuenta_tipo_deuda%TYPE;
    vl_cod_tipo_deuda           tipo_deuda.cod_tipo_deuda%TYPE;
    vl_nom_comprobante          comprobante.nom_comprobante%TYPE;
    vl_cod_comprobante          NUMBER;
    vl_saldo_capital_peso       NUMBER;
    vl_saldo_int_normal_peso    NUMBER;
    vl_cod_deuda_err            NUMBER := 0;
    vl_saldo_capital            NUMBER;
    vl_saldo_int_normal         NUMBER;
    vl_cod_estado_deuda         NUMBER;
    vl_catigo_fecha_moneda		VARCHAR2(100);
    vl_valor_moneda				valor_moneda.monto_valor_moneda%TYPE;

    CURSOR C_PLAN_PAGO IS
      SELECT    pp.cod_plan_pago
                ,pp.cod_deuda
                ,pp.nro_cuota
                ,pp.cod_estado_plan_pago
                ,pp.fecha_vencto
                ,d.cod_cartera
                ,d.cod_tipo_deuda
                ,td.monto_no_cobrable
                ,td.cod_familia_deuda
                ,td.cod_moneda
      FROM      plan_pago pp
                ,deuda d
                ,tipo_deuda td
      WHERE     pp.cod_deuda = d.cod_deuda
                AND d.cod_tipo_deuda = td.cod_tipo_deuda
                AND Trunc(pp.fecha_vencto) = Trunc(VE_FECHA_VENCTO) -- compra sin hrs
      ORDER BY  d.cod_tipo_deuda;
    BEGIN
      -- usuario que ejecuta el proceso
      SELECT cod_usuario
        INTO vl_cod_usuario
        FROM ejecucion_proceso
       WHERE cod_ejecucion_proceso = ve_cod_ejecucion_proceso;

      -- IMPORTANTE: El where de este select debe ser igual al del cursor !!
      -- calcula el total de registros
      SELECT  Count(*)
      INTO    vl_count_plan_pago
      FROM    plan_pago pp
              , deuda d
              , tipo_deuda td
      WHERE   pp.cod_deuda = d.cod_deuda
              AND d.cod_tipo_deuda = td.cod_tipo_deuda
              AND Trunc(pp.fecha_vencto) = Trunc(VE_FECHA_VENCTO); -- compra sin hrs

      vl_count  := vl_count_plan_pago;
      spu_ejecucion_proceso ('SET_TOTAL', ve_cod_ejecucion_proceso, vl_count);

      i                   := 1;
      vl_cod_tipo_deuda   := '__NO_EXISTE__';
      vl_cod_comprobante  := NULL;
------------------------------------------------------------------------------------------
-------------------------------------------MAIN-------------------------------------------
------------------------------------------------------------------------------------------
      FOR A IN C_PLAN_PAGO LOOP
        vl_cod_deuda_err := a.cod_deuda;

        vl_cod_estado_deuda := f_deuda_get_estado(a.cod_deuda, Add_Months(a.fecha_vencto, 3));
        IF (vl_cod_estado_deuda <> K_DEUDA_EXIGIBLE) THEN
            GOTO end_loop;
        END IF;

        -- verifica si el proceso ha sido cancelado
        IF (f_ep_cancela_proceso (ve_cod_ejecucion_proceso) = 'S') THEN
          sp_comp_simplifica(vl_cod_comprobante);
          COMMIT;
          RETURN;
        END IF;
        -- crear comprobante
        vl_nom_comprobante  := 'Castigo cuotas vencidas ' || To_Char(VE_FECHA_VENCTO, 'dd/mm/yyyy') || ': ' || a.cod_tipo_deuda;
         -- crea un nuevo comprobante si corresponde
        IF (vl_cod_tipo_deuda <> a.cod_tipo_deuda) THEN
          sp_comp_simplifica(vl_cod_comprobante);
          spu_comprobante ('INSERT', NULL, Add_Months(VE_FECHA_VENCTO, 3), vl_cod_usuario, vl_nom_comprobante, a.cod_familia_deuda, K_COMP_PENDIENTE, K_COMP_TRASPASO, K_COMP_ORIGEN_CASTIGO, vl_cod_usuario, VE_FECHA_VENCTO);

          SELECT  comprobante_sq.CURRVAL
          INTO    vl_cod_comprobante
          FROM    dual;

          -- obtiene las ctas
          vl_cta_castigo        := f_comp_get_cuenta_tipo_deuda (a.cod_tipo_deuda, K_CTA_CASTIGO_VENCIDA);
          vl_cta_cp             := f_comp_get_cuenta_tipo_deuda (a.cod_tipo_deuda, K_CTA_CP);
          vl_cod_tipo_deuda     := a.cod_tipo_deuda;
        END IF;

        vl_catigo_fecha_moneda := f_get_constante('K_CASTIGO_FECHA_MONEDA');

        IF (vl_catigo_fecha_moneda = 'VENCE_3')THEN
        	vl_valor_moneda := f_mon_get_valor_moneda(a.cod_moneda, Add_Months(a.fecha_vencto, 4));
        	
        	vl_saldo_capital := f_pp_saldo_capital (a.cod_plan_pago, Add_Months(a.fecha_vencto, 3));
        	vl_saldo_int_normal := f_pp_saldo_interes_normal(a.cod_plan_pago, Add_Months(a.fecha_vencto, 3));
        	
        ELSIF (vl_catigo_fecha_moneda = 'ACTUAL') THEN
        	vl_valor_moneda := f_mon_get_valor_moneda(a.cod_moneda, SYSDATE);
        	
    		vl_saldo_capital := f_pp_saldo_capital (a.cod_plan_pago, SYSDATE);
	        vl_saldo_int_normal := f_pp_saldo_interes_normal(a.cod_plan_pago, SYSDATE);

		END IF;

		vl_saldo_capital_peso    := Round(vl_saldo_capital * vl_valor_moneda,0);
		vl_saldo_int_normal_peso := Round(vl_saldo_int_normal * vl_valor_moneda,0);

        IF ((vl_saldo_capital_peso + vl_saldo_int_normal_peso) > 0)THEN
          spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cta_cp, 0, vl_saldo_capital_peso + vl_saldo_int_normal_peso);
          spu_analitico ('INSERT', NULL, vl_cod_comprobante, a.cod_deuda, vl_cta_cp, 0, vl_saldo_capital_peso + vl_saldo_int_normal_peso);

          spu_item_comprobante ('INSERT', NULL , vl_cod_comprobante, vl_cta_castigo, vl_saldo_capital_peso + vl_saldo_int_normal_peso, 0);
          spu_analitico ('INSERT', NULL, vl_cod_comprobante, a.cod_deuda, vl_cta_castigo, vl_saldo_capital_peso + vl_saldo_int_normal_peso, 0);
        END IF;

        COMMIT;

        <<end_loop>>
        NULL; -- add NULL statement to avoid error

        -- actualiza avance
        spu_ejecucion_proceso ('SET_ACTUAL', ve_cod_ejecucion_proceso, i);
        i := i + 1;
------------------------------------------------------------------------------------------
-----------------------------------------END MAIN-----------------------------------------
------------------------------------------------------------------------------------------
      END LOOP;
      sp_comp_simplifica (vl_cod_comprobante);
      spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, i-1);
      COMMIT;

      EXCEPTION
        WHEN OTHERS THEN
        -- Si ocurre un error de la BD al grabar un registro lo registra en el LOG. del sistema
        spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', SQLCODE, SQLERRM, 'Deuda: ' || To_Char(vl_cod_deuda_err));
      	spu_ejecucion_proceso('END', ve_cod_ejecucion_proceso, i-1);
        COMMIT;

   END;
END spp_comprobante_castigo;
/