PROMPT CREATE OR REPLACE PROCEDURE spp_cgi_comp_genera_int_ano
CREATE OR REPLACE PROCEDURE spp_cgi_comp_genera_int_ano(
  ve_cod_ejecucion_proceso   NUMBER)
AS
/******************************************************************************
   NAME:       spp_cgi_comp_genera_int_ano

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        17/11/2010   IS

   NOTES:

******************************************************************************/
BEGIN
  DECLARE
    K_EST_COMPROBANTE_PDTE    NUMBER:= 1;
    K_COMPROB_TRASPASO        NUMBER:= 3;
    K_ORIGEN_REAJUSTE         NUMBER:= 16;
    K_CTA_SVP                 NUMBER:= 1;
    K_CTA_CP                  NUMBER:= 2;
    K_CTA_LP                  NUMBER:= 3;
    K_CTA_INTERES_NORMAL      NUMBER := 6;
    K_DEUDA_NO_EXIGIBLE       NUMBER:= 1;
    K_DEUDA_CS				        VARCHAR(2) := 'CS';
    K_DEUDA_CSR				        VARCHAR(3) := 'CSR';
	  K_COMPONENTE_NO_EXIGIBLE  NUMBER := 1;
	  K_COMPONENTE_VIGENTE	    NUMBER := 3;
	  K_COMPONENTE_FUTURA		    NUMBER := 4;
    K_NO_EXIGIBLE			        NUMBER := 1;
    K_COBRANZA_NORMAL		      NUMBER := 2;
    vl_cod_usuario            NUMBER;
    vl_count_tipo_deuda       NUMBER;
    vl_count_registros        NUMBER := 0;
    i                         NUMBER;
    vl_nom_comprobante        VARCHAR(100);
    vl_count_plan_pago        NUMBER;
    vl_cta_cp                 NUMBER;
    vl_cta_lp                 NUMBER;
    vl_cta_svp                NUMBER;
    vl_cta_interes_normal     NUMBER;
    vl_monto_cuota_peso       NUMBER;
    vl_total_cuotas_peso      NUMBER;
    vl_fecha_svp              DATE;
    vl_interes_svp            NUMBER;
    vl_count_svp              NUMBER;
    vl_total_interes_svp      NUMBER;
    vl_interes_svp_peso       NUMBER;
    vl_total_cp               NUMBER;
    vl_total_lp               NUMBER;
    vl_existe_comp_svp        NUMBER;
    vl_existe_comp_cp         NUMBER;
    vl_existe_comp_lp         NUMBER;
    vl_cod_comprobante_svp    NUMBER;
    vl_cod_comprobante_cp     NUMBER;
    vl_cod_comprobante_lp     NUMBER;
    vl_interes_cp             NUMBER;
    vl_interes_cp_peso        NUMBER;
    vl_interes_lp             NUMBER;
    vl_interes_lp_peso        NUMBER;
	  vl_monto_interes_actual	  NUMBER;
	  vl_count_pp				        NUMBER;
	  vl_monto_interes_anterior NUMBER;
	  vl_interes 				        NUMBER;
    vl_monto_int_actual       NUMBER;
    vl_monto_int_ant          NUMBER;
    vl_monto_int_cp           NUMBER;
    vl_cuota_restante         NUMBER;
    vl_monto_int_lp           NUMBER;
    vl_monto_int              NUMBER;
    vl_monto_int_peso         NUMBER;
    vl_count_int_actual       NUMBER;
    vl_count_int_ant          NUMBER;
    vl_count                  NUMBER;
    vl_valor_cat_CP           NUMBER;
    vl_monto_int_peso_CP      NUMBER;
    vl_valor_cat_LP           NUMBER;
    vl_monto_int_peso_LP      NUMBER;
    vl_cant_cuota_pp          NUMBER;
    vl_cant_cuota             NUMBER;
    vl_dif_cant_cuota         NUMBER;
    vl_K_por_pagar            NUMBER; -- usada solo para USACH



    CURSOR c_tipo_deuda IS
      SELECT  cod_tipo_deuda
              ,cod_familia_deuda
              ,cod_moneda
              ,tasa_interes_credito
      FROM    tipo_deuda
      WHERE   cod_tipo_deuda <> K_DEUDA_CSR
      ORDER BY cod_tipo_deuda DESC;

    CURSOR c_deuda (vec_cod_tipo_deuda VARCHAR) IS
      SELECT  d.cod_deuda
              ,d.cod_estado_deuda
              ,d.cod_tipo_deuda
              ,To_Number(To_Char(d.fecha_inicio_cobro, 'yyyy')) ano_inicio_cobro
      FROM    deuda d
      WHERE   cod_tipo_deuda = vec_cod_tipo_deuda
              AND cod_estado_deuda IN (K_NO_EXIGIBLE,K_COBRANZA_NORMAL)
      ORDER BY d.cod_deuda ASC;

  BEGIN
    -- usuario que ejecuta el proceso
    SELECT  cod_usuario
    INTO    vl_cod_usuario
    FROM    ejecucion_proceso
    WHERE   cod_ejecucion_proceso = ve_cod_ejecucion_proceso;

    -- calcula el total de registros
    SELECT  Count(*)
    INTO    vl_count_registros
    FROM    deuda d
    WHERE   cod_tipo_deuda <> K_DEUDA_CSR
        AND cod_estado_deuda IN (K_NO_EXIGIBLE,K_COBRANZA_NORMAL);

    spu_ejecucion_proceso('SET_TOTAL', ve_cod_ejecucion_proceso, vl_count_registros);
    i                       := 1;
    vl_cod_comprobante_svp  := NULL;
    vl_cod_comprobante_cp   := NULL;
    vl_cod_comprobante_lp   := NULL;
    ------------------------------------------------------------------------------------------
    -- MAIN
    ------------------------------------------------------------------------------------------
    FOR a IN c_tipo_deuda LOOP
      vl_cta_svp            := f_comp_get_cuenta_tipo_deuda (a.cod_tipo_deuda, K_CTA_SVP);
      vl_cta_interes_normal := f_comp_get_cuenta_tipo_deuda (a.cod_tipo_deuda, K_CTA_INTERES_NORMAL);
      vl_cta_cp             := f_comp_get_cuenta_tipo_deuda (a.cod_tipo_deuda, K_CTA_CP);
      vl_cta_lp             := f_comp_get_cuenta_tipo_deuda (a.cod_tipo_deuda, K_CTA_LP);

      vl_nom_comprobante  := 'Interes del Periodo SVP ' || a.cod_tipo_deuda;
      spu_comprobante('INSERT', NULL, SYSDATE, vl_cod_usuario, vl_nom_comprobante, a.cod_familia_deuda, K_EST_COMPROBANTE_PDTE, K_COMPROB_TRASPASO, K_ORIGEN_REAJUSTE, vl_cod_usuario,SYSDATE);

      SELECT  comprobante_sq.CURRVAL
      INTO    vl_cod_comprobante_svp
      FROM    DUAL;


      vl_nom_comprobante  := 'Interes del Periodo CP ' || a.cod_tipo_deuda;
      spu_comprobante('INSERT', NULL, SYSDATE, vl_cod_usuario, vl_nom_comprobante, a.cod_familia_deuda, K_EST_COMPROBANTE_PDTE, K_COMPROB_TRASPASO, K_ORIGEN_REAJUSTE, vl_cod_usuario,SYSDATE);

      SELECT  comprobante_sq.CURRVAL
      INTO    vl_cod_comprobante_cp
      FROM    DUAL;

      vl_nom_comprobante  := 'Interes del Periodo LP ' || a.cod_tipo_deuda;
      spu_comprobante('INSERT', NULL, SYSDATE, vl_cod_usuario, vl_nom_comprobante, a.cod_familia_deuda, K_EST_COMPROBANTE_PDTE, K_COMPROB_TRASPASO, K_ORIGEN_REAJUSTE, vl_cod_usuario,SYSDATE);

      SELECT  comprobante_sq.CURRVAL
      INTO    vl_cod_comprobante_lp
      FROM    DUAL;

      FOR b IN c_deuda (a.cod_tipo_deuda) LOOP
        /***********************************************************************/
        /**********************************SVP**********************************/
        /***********************************************************************/
        vl_fecha_svp := To_Date('31/12/'||To_Char(To_Number(To_Char(SYSDATE,'yyyy')) - 1),'dd/mm/yyyy');
        IF (b.cod_estado_deuda = K_NO_EXIGIBLE) THEN
          vl_interes_svp            := f_deuda_saldo_componente (b.cod_deuda, K_COMPONENTE_NO_EXIGIBLE, vl_fecha_svp) * a.tasa_interes_credito/100;
          vl_interes_svp_peso       := vl_interes_svp * f_mon_get_valor_moneda(a.cod_moneda, To_Date('01/01/'||To_Char(SYSDATE,'YYYY'),'dd/mm/yyyy'));

          spu_analitico ('INSERT', NULL, vl_cod_comprobante_svp, b.cod_deuda, vl_cta_svp, vl_interes_svp_peso, 0);
          spu_analitico ('INSERT', NULL, vl_cod_comprobante_svp, b.cod_deuda, vl_cta_interes_normal, 0, vl_interes_svp_peso);

          spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante_svp, vl_cta_svp, vl_interes_svp_peso, 0);
          spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante_svp, vl_cta_interes_normal, 0, vl_interes_svp_peso);

          -- como aumento la deuda, para el caso CS puede ser que haya pasado de 12 a 15 años
          sp_deuda_cant_cuota(b.cod_deuda);
        ELSE
          IF (b.cod_tipo_deuda = K_DEUDA_CS) THEN
            -- validar que exista cuota 9999
            SELECT Count(*)
            INTO vl_count
            FROM plan_pago
            WHERE cod_deuda = b.cod_deuda
              AND ano_cobro = 9999;
            IF (vl_count=0) THEN
              spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, 'Deuda: ' || To_Char(b.cod_deuda) || ' Deuda CS sin cuota 9999.');
              GOTO end_loop;
            END IF;

            sp_deuda_plan_pago_h(b.cod_deuda, 'spp_cgi_comp_genera_int_ano', ve_cod_ejecucion_proceso);


            --************* ESTE UPDATE SOLO SE DEBE HACER SI FECHA_INICIO_COBRO ES MENOR A ESTE AÑO
           IF(b.ano_inicio_cobro < to_number(To_Char(SYSDATE, 'yyyy'))) THEN
           /*Este if va para que solo calcule el 2% de interes a las deudas que tienen inicio de cobro
           anterior al año en curso, ya que antes se generaba el plan pago y asignaba 2% int y al correr
           este proc asignaba 2% mas de interes. en total era 4% de interes lo que estaria malo.*/

              -- se actualiza la cuota FUTURA
            /*
            para todas menos la USACH

            UPDATE  plan_pago
	          SET     monto_capital = monto_capital + monto_interes
                  ,monto_interes = (monto_capital + monto_interes) * a.tasa_interes_credito/100
            WHERE   cod_deuda = b.cod_deuda
                		  AND ano_cobro = 9999;
            */

            -----------------------------
            -- para USACH
            UPDATE  plan_pago
	          SET     monto_capital = monto_capital + monto_interes
            WHERE   cod_deuda = b.cod_deuda
                		  AND ano_cobro = 9999;

            SELECT Nvl(Sum(f_pp_saldo_capital(cod_plan_pago) + f_pp_saldo_interes_normal(cod_plan_pago)), 0)
            INTO vl_K_por_pagar
            FROM plan_pago
            WHERE cod_deuda = b.cod_deuda;

            UPDATE  plan_pago
	          SET     monto_interes = vl_K_por_pagar * a.tasa_interes_credito/100
            WHERE   cod_deuda = b.cod_deuda
                		  AND ano_cobro = 9999;
            -----------------------------

           END IF;

            -- LP
            SELECT  f_deuda_cat(b.cod_deuda, To_Number(To_Char(SYSDATE, 'yyyy'))) -  f_deuda_cat(b.cod_deuda, To_Number(To_Char(SYSDATE, 'yyyy')) - 1)
            INTO    vl_valor_cat_LP
            FROM    dual;

            SELECT  Count(*)
            INTO    vl_cant_cuota_pp
            FROM deuda d, plan_pago p
            WHERE   cod_tipo_deuda = a.cod_tipo_deuda
            AND     p.cod_Deuda = d.cod_deuda
            AND     ano_cobro NOT IN (to_number(To_Char(SYSDATE, 'yyyy')), 9999)
            AND     d.cod_deuda = b.cod_deuda;

            SELECT  cant_cuota
            INTO    vl_cant_cuota
            FROM    deuda
            WHERE   cod_deuda = b.cod_deuda;

            vl_dif_cant_cuota := vl_cant_cuota - vl_cant_cuota_pp;

            vl_monto_int_peso_LP := ((vl_valor_cat_LP * vl_dif_cant_cuota) * f_mon_get_valor_moneda(a.cod_moneda, To_Date('01/01/'||To_Char(SYSDATE,'YYYY'),'dd/mm/yyyy')));

            spu_analitico ('INSERT', NULL, vl_cod_comprobante_lp, b.cod_deuda, vl_cta_lp, vl_monto_int_peso_LP, 0);
            spu_analitico ('INSERT', NULL, vl_cod_comprobante_lp, b.cod_deuda, vl_cta_interes_normal, 0, vl_monto_int_peso_LP);

            spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante_lp, vl_cta_lp, vl_monto_int_peso_LP, 0);
            spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante_lp, vl_cta_interes_normal, 0, vl_monto_int_peso_LP);

            --- CP
            SELECT  f_deuda_cat(b.cod_deuda, To_Number(To_Char(SYSDATE, 'yyyy'))) -  f_deuda_cat(b.cod_deuda, To_Number(To_Char(SYSDATE, 'yyyy')) - 1)
            INTO    vl_valor_cat_CP
            FROM    dual;

            vl_monto_int_peso_CP := vl_valor_cat_CP * f_mon_get_valor_moneda(a.cod_moneda, To_Date('01/01/'||To_Char(SYSDATE,'YYYY'),'dd/mm/yyyy'));

            spu_analitico ('INSERT', NULL, vl_cod_comprobante_cp, b.cod_deuda, vl_cta_cp, vl_monto_int_peso_CP, 0);
            spu_analitico ('INSERT', NULL, vl_cod_comprobante_cp, b.cod_deuda, vl_cta_interes_normal, 0, vl_monto_int_peso_CP);

            spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante_cp, vl_cta_cp, vl_monto_int_peso_CP, 0);
            spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante_cp, vl_cta_interes_normal, 0, vl_monto_int_peso_CP);

          ELSE

            --CP
            SELECT Count(*)
            INTO vl_count_int_actual
            FROM plan_pago
            WHERE cod_deuda = b.cod_deuda
              AND ano_cobro = To_Number(To_Char(SYSDATE,'YYYY'));

            SELECT Count(*)
            INTO vl_count_int_ant
            FROM plan_pago
            WHERE cod_deuda = b.cod_deuda
              AND ano_cobro = To_Number(To_Char(SYSDATE,'YYYY') -1);

            IF (vl_count_int_actual > 0 and vl_count_int_ant > 0)THEN
              SELECT monto_interes
              INTO vl_monto_int_actual
              FROM plan_pago
              WHERE cod_deuda = b.cod_deuda
                AND ano_cobro = To_Number(To_Char(SYSDATE,'YYYY'));

              SELECT monto_interes
              INTO vl_monto_int_ant
              FROM plan_pago
              WHERE cod_deuda = b.cod_deuda
                AND ano_cobro = To_Number(To_Char(SYSDATE,'YYYY') -1);

              vl_monto_int_cp := ((vl_monto_int_ant - vl_monto_int_actual) * f_mon_get_valor_moneda(a.cod_moneda, To_Date('01/01/'||To_Char(SYSDATE,'YYYY'),'dd/mm/yyyy')));

              spu_analitico ('INSERT', NULL, vl_cod_comprobante_cp, b.cod_deuda, vl_cta_cp, vl_monto_int_cp, 0);
              spu_analitico ('INSERT', NULL, vl_cod_comprobante_cp, b.cod_deuda, vl_cta_interes_normal, 0, vl_monto_int_cp);

              spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante_cp, vl_cta_cp, vl_monto_int_cp, 0);
              spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante_cp, vl_cta_interes_normal, 0, vl_monto_int_cp);

              --LP
              SELECT Count(*)
              INTO vl_cuota_restante
              FROM plan_pago
              WHERE cod_deuda = b.cod_deuda
                AND ano_cobro > To_Number(To_Char(SYSDATE,'YYYY'));

              vl_monto_int_lp := vl_monto_int_cp * (vl_cuota_restante * f_mon_get_valor_moneda(a.cod_moneda, To_Date('01/01/'||To_Char(SYSDATE,'YYYY'),'dd/mm/yyyy')));

              spu_analitico ('INSERT', NULL, vl_cod_comprobante_lp, b.cod_deuda, vl_cta_lp, vl_monto_int_lp, 0);
              spu_analitico ('INSERT', NULL, vl_cod_comprobante_lp, b.cod_deuda, vl_cta_interes_normal, 0, vl_monto_int_lp);

              spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante_lp, vl_cta_lp, vl_monto_int_lp, 0);
              spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante_lp, vl_cta_interes_normal, 0, vl_monto_int_lp);
            END IF;
          END IF;
		    END IF;
        <<end_loop>>  -- 7not allowed unless an executable statement follows
        NULL; -- add NULL statement to avoid error

        spu_ejecucion_proceso('SET_ACTUAL', ve_cod_ejecucion_proceso, i);
        i := i + 1;
      END LOOP;

      sp_comp_simplifica(vl_cod_comprobante_svp);
      sp_comp_simplifica(vl_cod_comprobante_cp);
      sp_comp_simplifica(vl_cod_comprobante_lp);
    END LOOP;
    spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, i-1);
    EXCEPTION
      WHEN OTHERS THEN
        -- Si ocurre un error de la BD al grabar un registro lo registra en el LOG. del sistema
        spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', SQLCODE, SubStr(SQLERRM,1,100), 'spp_cgi_comp_genera_int_ano:...');
        COMMIT;
      END;
END spp_cgi_comp_genera_int_ano;
/

