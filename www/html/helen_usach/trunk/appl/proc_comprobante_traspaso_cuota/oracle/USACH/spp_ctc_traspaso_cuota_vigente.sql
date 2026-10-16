PROMPT CREATE OR REPLACE PROCEDURE spp_ctc_traspaso_cuota_vigente
CREATE OR REPLACE PROCEDURE spp_ctc_traspaso_cuota_vigente(
  ve_cod_ejecucion_proceso   NUMBER)
AS
/******************************************************************************
   NAME:       spp_tcv_traspaso_cuota_vigente

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        02/05/2010   SC
   2.0        13/11/2010   IS				se agregan las cuotas CS

   NOTES:

******************************************************************************/
BEGIN
  DECLARE
    K_TIPO_DEUDA_CS           VARCHAR(2) :='CS';
    K_EST_COMPROBANTE_PDTE    NUMBER:= 1;
    K_COMPROB_TRASPASO        NUMBER:= 3;
    K_ORIGEN_TRASPASO		  NUMBER:= 15;
    K_CTA_CP                  NUMBER:= 2;
    K_CTA_LP                  NUMBER:= 3;
    K_COBRANZA_NORMAL         NUMBER:= 2;
    vl_cod_usuario            NUMBER;
    vl_count_tipo_deuda       NUMBER;
    vl_count_registros        NUMBER := 0;
    vl_count_registros_otros  NUMBER := 0;
    vl_count_registros_CS     NUMBER := 0;
    i                         NUMBER;
    vl_cod_comprobante        NUMBER;
    vl_nom_comprobante        VARCHAR(100);
    vl_ano_comprobante        NUMBER := To_Number(To_Char(SYSDATE,'YYYY'));
    vl_count_plan_pago        NUMBER;
    vl_monto_cuota            NUMBER;
    vl_cta_cp                 NUMBER;
    vl_cta_lp                 NUMBER;
    vl_monto_cuota_peso       NUMBER;
    vl_total_cuotas_peso      NUMBER;
    vl_cod_moneda_CS          NUMBER;
    vl_cod_familia_deuda_CS   NUMBER;
	vl_cod_deuda_err	      NUMBER;


    CURSOR c_tipo_deuda IS
      SELECT  DISTINCT td.cod_tipo_deuda
              ,td.cod_familia_deuda
              ,td.cod_moneda
      FROM    tipo_deuda td, deuda d, plan_pago pp
      WHERE   td.cod_tipo_deuda NOT IN ('CS')
              AND td.cod_tipo_deuda = d.cod_tipo_deuda
              AND d.cod_deuda = pp.cod_deuda
              AND pp.ano_cobro = vl_ano_comprobante;


    CURSOR c_deuda (vec_cod_tipo_deuda VARCHAR) IS
      SELECT  d.cod_deuda
              ,pp.cod_plan_pago
      FROM    deuda d
              ,plan_pago pp
      WHERE   cod_tipo_deuda =  vec_cod_tipo_deuda
              AND d.cod_deuda = pp.cod_deuda
              AND ano_cobro = vl_ano_comprobante;

    CURSOR c_deuda_CS IS
      SELECT  cod_deuda
      FROM    deuda
      WHERE   cod_tipo_deuda = 'CS'
              AND cod_estado_deuda = K_COBRANZA_NORMAL;

    BEGIN
      -- usuario que ejecuta el proceso
      SELECT  cod_usuario
      INTO    vl_cod_usuario
      FROM    ejecucion_proceso
      WHERE   cod_ejecucion_proceso = ve_cod_ejecucion_proceso;

      -- calcula el total de registros distintos a deuda CS
      SELECT  Count (*)
      INTO    vl_count_registros_otros
      FROM    tipo_deuda td
              ,deuda d
              ,plan_pago pp
      WHERE   td.cod_tipo_deuda NOT IN (K_TIPO_DEUDA_CS)
              AND td.cod_tipo_deuda = d.cod_tipo_deuda
              AND d.cod_deuda = pp.cod_deuda
              AND pp.ano_cobro = vl_ano_comprobante;

      -- calcula el total de registros iguales a deuda CS
      SELECT  Count(*)
      INTO    vl_count_registros_CS
      FROM    deuda
      WHERE   cod_tipo_deuda = K_TIPO_DEUDA_CS
              AND cod_estado_deuda = K_COBRANZA_NORMAL;

      vl_count_registros := vl_count_registros_otros + vl_count_registros_CS;

      spu_ejecucion_proceso('SET_TOTAL', ve_cod_ejecucion_proceso, vl_count_registros);
      i                     := 1;
      vl_cod_comprobante  := NULL;
      ------------------------------------------------------------------------------------------
      -- MAIN
      ------------------------------------------------------------------------------------------
      FOR a IN c_tipo_deuda LOOP
        sp_comp_simplifica(vl_cod_comprobante);

        vl_nom_comprobante  := 'Traspaso a cuota vigente ' || a.cod_tipo_deuda;

        spu_comprobante('INSERT', NULL, To_Date('01/01/'||To_Char(vl_ano_comprobante), 'dd/mm/yyyy'), vl_cod_usuario, vl_nom_comprobante, a.cod_familia_deuda, K_EST_COMPROBANTE_PDTE, K_COMPROB_TRASPASO, K_ORIGEN_TRASPASO, vl_cod_usuario,To_Date('01/01/'||To_Char(vl_ano_comprobante), 'dd/mm/yyyy'));
        SELECT  comprobante_sq.CURRVAL
        INTO    vl_cod_comprobante
        FROM    DUAL;
        vl_total_cuotas_peso := 0;

        vl_cta_cp := f_comp_get_cuenta_tipo_deuda (a.cod_tipo_deuda, K_CTA_CP);
        vl_cta_lp := f_comp_get_cuenta_tipo_deuda (a.cod_tipo_deuda, K_CTA_LP);

        FOR b IN c_deuda (a.cod_tipo_deuda) LOOP
		      vl_cod_deuda_err := b.cod_deuda;
          vl_monto_cuota := f_pp_saldo_plan_pago(b.cod_plan_pago, sysdate);

          vl_monto_cuota_peso   := vl_monto_cuota * f_mon_get_valor_moneda(a.cod_moneda, To_Date('01/01/'||To_Char(vl_ano_comprobante), 'dd/mm/yyyy'));
          vl_total_cuotas_peso := vl_total_cuotas_peso + vl_monto_cuota_peso;

          spu_analitico ('INSERT', NULL, vl_cod_comprobante, b.cod_deuda, vl_cta_lp, 0, vl_monto_cuota_peso);
          spu_analitico ('INSERT', NULL, vl_cod_comprobante, b.cod_deuda, vl_cta_cp, vl_monto_cuota_peso, 0);

          spu_ejecucion_proceso('SET_ACTUAL', ve_cod_ejecucion_proceso, i);
          i := i + 1;
        END LOOP;

        spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cta_lp, 0, vl_total_cuotas_peso);
        spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cta_cp, vl_total_cuotas_peso, 0);
      END LOOP;
      sp_comp_simplifica(vl_cod_comprobante);

	  ----------------------------------------
      --para deudas CS
      SELECT cod_familia_deuda
            ,cod_moneda
      INTO vl_cod_familia_deuda_CS
            ,vl_cod_moneda_CS
      FROM tipo_deuda
      WHERE cod_tipo_deuda = K_TIPO_DEUDA_CS;


      vl_nom_comprobante  := 'Traspaso a cuota vigente '|| K_TIPO_DEUDA_CS;
      spu_comprobante('INSERT', NULL, SYSDATE, vl_cod_usuario, vl_nom_comprobante, vl_cod_familia_deuda_CS, K_EST_COMPROBANTE_PDTE, K_COMPROB_TRASPASO, K_ORIGEN_TRASPASO, vl_cod_usuario,SYSDATE);
      SELECT  comprobante_sq.CURRVAL
      INTO    vl_cod_comprobante
      FROM    DUAL;
      vl_total_cuotas_peso := 0;

      vl_cta_cp := f_comp_get_cuenta_tipo_deuda (K_TIPO_DEUDA_CS, K_CTA_CP);
      vl_cta_lp := f_comp_get_cuenta_tipo_deuda (K_TIPO_DEUDA_CS, K_CTA_LP);

      FOR c IN c_deuda_CS LOOP
	    vl_cod_deuda_err := c.cod_deuda;
        vl_monto_cuota := f_deuda_cat(c.cod_deuda, vl_ano_comprobante);

        vl_monto_cuota_peso   := vl_monto_cuota * f_mon_get_valor_moneda(vl_cod_moneda_CS, To_Date('01/01/'||To_Char(vl_ano_comprobante), 'dd/mm/yyyy'));
        vl_total_cuotas_peso := vl_total_cuotas_peso + vl_monto_cuota_peso;

        spu_analitico ('INSERT', NULL, vl_cod_comprobante, c.cod_deuda, vl_cta_lp, 0, vl_monto_cuota_peso);
        spu_analitico ('INSERT', NULL, vl_cod_comprobante, c.cod_deuda, vl_cta_cp, vl_monto_cuota_peso, 0);

        spu_ejecucion_proceso('SET_ACTUAL', ve_cod_ejecucion_proceso, i);
        i := i + 1;
      END LOOP;

      spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cta_lp, 0, vl_total_cuotas_peso);
      spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cta_cp, vl_total_cuotas_peso, 0);
      sp_comp_simplifica(vl_cod_comprobante);


      spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, i-1);
    EXCEPTION
      WHEN OTHERS THEN
        -- Si ocurre un error de la BD al grabar un registro lo registra en el LOG. del sistema
        spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', SQLCODE, SQLERRM, 'Deuda :' || to_char(vl_cod_deuda_err));
        COMMIT;
      END;
END spp_ctc_traspaso_cuota_vigente;
/