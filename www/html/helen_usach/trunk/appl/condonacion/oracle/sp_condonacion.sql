PROMPT CREATE OR REPLACE PROCEDURE sp_condonacion
CREATE OR REPLACE PROCEDURE sp_condonacion (
   ve_cod_deuda_condonacion         NUMBER
 , ve_cod_usuario                   NUMBER
 , vs_cod_comprobante         OUT   NUMBER
)
AS
BEGIN
   DECLARE
      kl_deuda_no_exigible                    NUMBER := 1;
      kl_deuda_exigible                       NUMBER := 2;
      kl_deuda_condonada                      NUMBER := 3;
      kl_pp_condonada                         NUMBER := 5;
      kl_est_comprobante_pdte                 NUMBER := 1;
      kl_comprob_traspaso                     NUMBER := 3;
      kl_origen_comprob_condonacion           NUMBER := 4;
      k_cta_condonacion                       NUMBER := 7;
      k_cta_svp                               NUMBER := 1;
      k_cta_vencida                           NUMBER := 5;
      kl_cta_cp                               NUMBER := 2;
      kl_cta_lp                               NUMBER := 3;
      kl_componente_svp                       NUMBER := 1;
      k_condonacion                           NUMBER := 9;
      vl_nom_comprobante                      VARCHAR2 (100);
      vl_cod_deuda                            NUMBER;
      vl_cod_familia_deuda                    NUMBER;
      vl_cod_tipo_deuda                       VARCHAR2 (100);
      vl_cta_condonacion                      NUMBER;
      vl_cta_svp                              NUMBER;
      vl_cta_cp                               NUMBER;
      vl_cta_lp                               NUMBER;
      vl_cod_deudor                           NUMBER;
      vl_cod_estado_deuda                     NUMBER;
      vl_svp                                  NUMBER;
      vl_k_cp                                 NUMBER;
      vl_k_lp                                 NUMBER;
      vl_fecha_condonacion                    DATE;
      vl_cuota_vigente                        NUMBER;
      vl_vencto_mas_3                         DATE;
      vl_valor_moneda                         NUMBER;
      vl_cod_moneda                           NUMBER;

      CURSOR c_pp (
         ve_cod_deuda   NUMBER
      )
      IS
         SELECT cod_plan_pago
              , fecha_vencto
           FROM plan_pago
          WHERE cod_deuda = ve_cod_deuda
            AND f_pp_saldo_plan_pago (cod_plan_pago) > 0;
   BEGIN
      -- obtiene datos iniciales
      -- fecha_condonacion = fecha_confirma
      SELECT dc.cod_deuda
           , t.cod_familia_deuda
           , d.cod_tipo_deuda
           , d.cod_estado_deuda
           , c.fecha_confirma
           , t.cod_moneda
        INTO vl_cod_deuda
           , vl_cod_familia_deuda
           , vl_cod_tipo_deuda
           , vl_cod_estado_deuda
           , vl_fecha_condonacion
           , vl_cod_moneda
        FROM deuda_condonacion dc
           , deuda d
           , tipo_deuda t
           , condonacion c
       WHERE dc.cod_deuda_condonacion = ve_cod_deuda_condonacion
         AND d.cod_deuda = dc.cod_deuda
         AND t.cod_tipo_deuda = d.cod_tipo_deuda
         AND c.cod_condonacion = dc.cod_condonacion;

      -- Obtiene los montos
      vl_svp                               := 0;
      vl_k_cp                              := 0;
      vl_k_lp                              := 0;

      IF (vl_cod_estado_deuda = kl_deuda_no_exigible)
      THEN
         vl_svp := f_deuda_saldo_componente (vl_cod_deuda, kl_componente_svp, vl_fecha_condonacion);
      ELSE
         vl_cuota_vigente := f_deuda_cuota_vigente (vl_cod_deuda, vl_fecha_condonacion);

         FOR a IN c_pp (vl_cod_deuda) LOOP
            IF (a.cod_plan_pago = vl_cuota_vigente) THEN-- CP
               vl_k_cp := vl_k_cp + f_pp_saldo_capital (a.cod_plan_pago, vl_fecha_condonacion) + f_pp_saldo_interes_normal(a.cod_plan_pago, vl_fecha_condonacion);
            ELSE -- LP
               vl_k_lp := vl_k_lp + f_pp_saldo_capital (a.cod_plan_pago, vl_fecha_condonacion) + f_pp_saldo_interes_normal (a.cod_plan_pago, vl_fecha_condonacion);
            END IF;
         END LOOP;
      END IF;

      -- calcula el reajuste
      vl_valor_moneda                      := f_mon_get_valor_moneda (vl_cod_moneda, vl_fecha_condonacion);
      -- llevar los valores a $
      vl_svp                               := ROUND (vl_svp * vl_valor_moneda, 0);
      vl_k_cp                              := ROUND (vl_k_cp * vl_valor_moneda, 0);
      vl_k_lp                              := ROUND (vl_k_lp * vl_valor_moneda, 0);
      -- Comprobante
      vl_nom_comprobante                   := 'Condonación deuda: ' || TO_CHAR (vl_cod_deuda);
      spu_comprobante ('INSERT'
                     , NULL
                     , SYSDATE
                     , ve_cod_usuario
                     , vl_nom_comprobante
                     , vl_cod_familia_deuda
                     , kl_est_comprobante_pdte
                     , kl_comprob_traspaso
                     , kl_origen_comprob_condonacion
                     , ve_cod_usuario
                     , SYSDATE
                      );

      SELECT comprobante_sq.CURRVAL
        INTO vs_cod_comprobante
        FROM DUAL;

      SELECT c.cod_deudor
           , cod_tipo_deuda
        INTO vl_cod_deudor
           , vl_cod_tipo_deuda
        FROM condonacion c
           , deuda d
           , deuda_condonacion dc
       WHERE dc.cod_deuda = vl_cod_deuda
         AND dc.cod_deuda_condonacion = ve_cod_deuda_condonacion
         AND dc.cod_deuda = d.cod_deuda
         AND c.cod_condonacion = dc.cod_condonacion;

      spu_bitacora_deudor ('Condonación tipo deuda: ' || TO_CHAR (vl_cod_tipo_deuda) || ' Deudor: ' || TO_CHAR (vl_cod_deudor)
                         , k_condonacion
                         , vl_cod_deudor
                         , ve_cod_usuario
                          );
      -- obtiene las cuentas contables
      vl_cta_condonacion := f_comp_get_cuenta_tipo_deuda (vl_cod_tipo_deuda, k_cta_condonacion);
      vl_cta_svp         := f_comp_get_cuenta_tipo_deuda (vl_cod_tipo_deuda, k_cta_svp);
      vl_cta_cp          := f_comp_get_cuenta_tipo_deuda (vl_cod_tipo_deuda, kl_cta_cp);
      vl_cta_lp          := f_comp_get_cuenta_tipo_deuda (vl_cod_tipo_deuda, kl_cta_lp);
      -- items comprobante
      spu_item_comprobante ('INSERT', NULL, vs_cod_comprobante, vl_cta_condonacion, vl_svp + vl_k_cp + vl_k_lp, 0);
      spu_item_comprobante ('INSERT', NULL, vs_cod_comprobante, vl_cta_svp, 0, vl_svp);
      spu_item_comprobante ('INSERT', NULL, vs_cod_comprobante, vl_cta_cp, 0, vl_k_cp);
      spu_item_comprobante ('INSERT', NULL, vs_cod_comprobante, vl_cta_lp, 0, vl_k_lp);
      -- analitico
      spu_analitico ('INSERT', NULL, vs_cod_comprobante, vl_cod_deuda, vl_cta_condonacion, vl_svp + vl_k_cp + vl_k_lp, 0);
      spu_analitico ('INSERT', NULL, vs_cod_comprobante, vl_cod_deuda, vl_cta_svp, 0, vl_svp);
      spu_analitico ('INSERT', NULL, vs_cod_comprobante, vl_cod_deuda, vl_cta_cp, 0, vl_k_cp);
      spu_analitico ('INSERT', NULL, vs_cod_comprobante, vl_cod_deuda, vl_cta_lp, 0, vl_k_lp);

      -- actualiza estado de la deuda y su plan pago
      sp_deuda_update_estado (vl_cod_deuda, kl_deuda_condonada);

      FOR a IN c_pp (vl_cod_deuda) LOOP
         sp_pp_update_estado (a.cod_plan_pago, kl_pp_condonada);
      END LOOP;

	  --no se llama al sp_comp_simplifica, porque a veces no creaba item_comprobante y se caia
      sp_comp_cruzado(vs_cod_comprobante);

   END;
END;
/

