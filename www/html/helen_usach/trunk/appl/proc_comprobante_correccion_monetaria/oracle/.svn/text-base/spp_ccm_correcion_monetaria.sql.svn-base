CREATE OR REPLACE PROCEDURE spp_ccm_correcion_monetaria(
  ve_cod_ejecucion_proceso   NUMBER)
AS
/******************************************************************************
   NAME:       spp_ccm_correcion_monetaria

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        02/05/2010   SC

   NOTES:
   En este proceso no se utiliza f_comp_get_cuenta_tipo_deuda ya que las cuentas
   las lee de un cursor
******************************************************************************/
BEGIN
  DECLARE
    K_EST_COMPROBANTE_PDTE        NUMBER:= 1;
    K_EST_COMPROBANTE_APROB       NUMBER:= 2;
    K_UTM                         NUMBER:= 3;
    K_COMPROB_TRASPASO            NUMBER:= 3;
    K_ORIGEN_CORRECCION_MONET     NUMBER:= 18;
    K_CTA_UTILIDAD_VENCIDA        NUMBER:= 5;
    K_CTA_INTERES_PENAL           NUMBER:= 8;
    K_CTA_RESERVAS_CREDITO        NUMBER:= 21;
    K_CTA_REAJUSTE_CREDITO        NUMBER:= 20;
    K_CTA_APORTES_DONACIONES      NUMBER:= 22;

    --Desde parametro se obtiene la siguiente cuenta
    K_CTA_CORRECCION_MONETARIA    NUMBER:= 21;

    K_CTA_CASTIGOS_VENCIDA        NUMBER:= 4;
    K_CTA_DESCUENTOS_PRONTO_PAGO  NUMBER:= 9;
    K_CTA_COMISION_PAGADA_CREDITO NUMBER:= 24;
    K_CTA_OTRO_GASTO_OPERACIONAL  NUMBER:= 25;
    vl_cod_usuario                NUMBER;
    vl_count_registros            NUMBER := 0;
    vl_count_familia_deuda        NUMBER;
    vl_count_concepto_cuenta      NUMBER;
    i                             NUMBER;
    vl_cod_comprobante            NUMBER;
    vl_nom_comprobante            VARCHAR(100);
    vl_ano_comprobante            NUMBER;
    vl_monto_debe                 NUMBER;
    vl_monto_haber                NUMBER;
    vl_monto_debe_pesos           NUMBER;
    vl_monto_haber_pesos          NUMBER;
    vl_cuenta_contable            NUMBER;
    vl_correccion_monteratia      NUMBER;

    CURSOR c_familia_deuda IS
      SELECT  cod_familia_deuda
      FROM    familia_deuda;

    CURSOR c_concepto_cuenta IS
      SELECT  cod_concepto_cuenta
      FROM    concepto_cuenta
      WHERE   cod_concepto_cuenta IN ( K_CTA_UTILIDAD_VENCIDA
                                      ,K_CTA_INTERES_PENAL
                                      ,K_CTA_REAJUSTE_CREDITO
                                      ,K_CTA_RESERVAS_CREDITO
                                      ,K_CTA_APORTES_DONACIONES --
                                      ,K_CTA_CASTIGOS_VENCIDA
                                      ,K_CTA_DESCUENTOS_PRONTO_PAGO
                                      ,K_CTA_COMISION_PAGADA_CREDITO
                                      ,K_CTA_OTRO_GASTO_OPERACIONAL);

    CURSOR c_cuenta (vec_cod_concepto_cuenta NUMBER
                    ,vec_cod_familia_deuda NUMBER)IS
      SELECT  DISTINCT ctd.cod_cuenta_contable
              ,td.cod_tipo_deuda
              ,td.cod_moneda
      FROM    cuenta_tipo_deuda ctd
              ,tipo_deuda td
      WHERE   ctd.cod_concepto_cuenta = vec_cod_concepto_cuenta
              AND td.cod_tipo_deuda = ctd.cod_tipo_deuda
              AND td.cod_familia_deuda = vec_cod_familia_deuda
      ORDER BY ctd.cod_cuenta_contable;
  BEGIN
    -- usuario que ejecuta el proceso
    SELECT  cod_usuario
    INTO    vl_cod_usuario
    FROM    ejecucion_proceso
    WHERE   cod_ejecucion_proceso = ve_cod_ejecucion_proceso;

    -- calcula el total de registros
    SELECT  Count(*)
    INTO    vl_count_familia_deuda
    FROM    familia_deuda;

    SELECT  Count(*)
    INTO    vl_count_concepto_cuenta
    FROM    concepto_cuenta
    WHERE   cod_concepto_cuenta IN ( K_CTA_UTILIDAD_VENCIDA
                                    ,K_CTA_INTERES_PENAL
                                    ,K_CTA_REAJUSTE_CREDITO
                                    ,K_CTA_RESERVAS_CREDITO
                                    ,K_CTA_APORTES_DONACIONES
                                    ,K_CTA_CASTIGOS_VENCIDA
                                    ,K_CTA_DESCUENTOS_PRONTO_PAGO
                                    ,K_CTA_COMISION_PAGADA_CREDITO
                                    ,K_CTA_OTRO_GASTO_OPERACIONAL);

    vl_count_registros := vl_count_familia_deuda * vl_count_concepto_cuenta;
    spu_ejecucion_proceso('SET_TOTAL', ve_cod_ejecucion_proceso, vl_count_registros);
    i                     := 1;
    vl_cod_comprobante  := NULL;
    ------------------------------------------------------------------------------------------
    -- MAIN
    ------------------------------------------------------------------------------------------
    FOR a IN c_familia_deuda LOOP
      --CREAR EL COMPROBANTE (LA CABECERA)
      vl_nom_comprobante  := 'Comprobante Corrección Monetaria';
      vl_ano_comprobante  := To_Number(To_Char(SYSDATE,'YYYY'));
      spu_comprobante('INSERT', NULL, SYSDATE, vl_cod_usuario, vl_nom_comprobante, a.cod_familia_deuda, K_EST_COMPROBANTE_PDTE, K_COMPROB_TRASPASO, K_ORIGEN_CORRECCION_MONET, vl_cod_usuario,SYSDATE);
      SELECT  comprobante_sq.CURRVAL
      INTO    vl_cod_comprobante
      FROM    DUAL;

      FOR b IN c_concepto_cuenta LOOP
        vl_cuenta_contable := 0;
        vl_correccion_monteratia := 0;
        FOR c IN c_cuenta(b.cod_concepto_cuenta,a.cod_familia_deuda) LOOP
          IF (c.cod_cuenta_contable != vl_cuenta_contable) THEN
             vl_cuenta_contable := c.cod_cuenta_contable;
            SELECT  Nvl(Round(Sum(
                          (MONTO_HABER / f_mon_get_valor_moneda(K_UTM,To_Date('31/12/'||to_char(To_Number(To_Char(SYSDATE,'YYYY'))-1),'dd/mm/yyyy')))
                          -
                          (MONTO_HABER/ f_mon_get_valor_moneda(K_UTM,FECHA_COMPROBANTE)))
                        ,4),0)
            INTO    vl_monto_haber
            FROM    item_comprobante ic
                    ,comprobante com
            WHERE   ic.cod_cuenta_contable = c.cod_cuenta_contable
                    AND ic.cod_comprobante = com.cod_comprobante
                    AND To_Number(To_Char(com.fecha_comprobante,'YYYY')) = To_Number(To_Char(SYSDATE,'YYYY')) - 1
                    AND cod_estado_comprobante = K_EST_COMPROBANTE_APROB;
            vl_monto_haber_pesos  := vl_monto_haber * f_mon_get_valor_moneda(K_UTM,To_Date('31/12/'||To_Char(To_Number(To_Char(SYSDATE,'YYYY')-1)),'dd/mm/yyyy'));
            vl_correccion_monteratia := vl_correccion_monteratia + vl_monto_haber_pesos;

            spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, c.cod_cuenta_contable, 0, vl_monto_haber_pesos);
            spu_ejecucion_proceso('SET_ACTUAL', ve_cod_ejecucion_proceso, i);
            i := i + 1;
          END IF;
        END LOOP;
        spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, f_get_cta_tipo_deuda_parametro(K_CTA_CORRECCION_MONETARIA), vl_correccion_monteratia, 0);
        sp_comp_simplifica(vl_cod_comprobante);
      END LOOP;
    END LOOP;
    spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, i-1);

    EXCEPTION
      WHEN OTHERS THEN
        -- Si ocurre un error de la BD al grabar un registro lo registra en el LOG. del sistema
        spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', SQLCODE, SUBSTR(SQLERRM,1,100), 'spp_ccm_correcion_monetaria');
        COMMIT;
      END;
END spp_ccm_correcion_monetaria;