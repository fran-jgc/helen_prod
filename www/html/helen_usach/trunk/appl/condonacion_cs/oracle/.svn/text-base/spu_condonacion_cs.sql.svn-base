CREATE OR REPLACE PROCEDURE spu_condonacion_cs (
   ve_operacion                   IN  VARCHAR2
 , ve_cod_condonacion_cs          IN  NUMBER   := NULL
 , ve_fecha_condonacion_cs        IN  DATE     := NULL
 , ve_cod_estado_condonacion_cs   IN  NUMBER   := NULL
 , ve_cod_deuda                   IN  NUMBER   := NULL
 , ve_obs                         IN  VARCHAR2 := NULL
 , ve_cod_usuario_confirma        IN  NUMBER   := NULL
)
AS
  vl_cod_estado_antes       NUMBER;
  kl_cod_estado_emitida     NUMBER := 1;
  kl_cod_estado_confirmada  NUMBER := 2;
  K_COD_ESTADO_RECHAZA      NUMBER:=3;
  K_TIPO_CONDONACION_CS     NUMBER:=1;
  K_CONDONACION_SOLICITADA  NUMBER:=1;
  K_CONDONACION_CONFIRMADA  NUMBER:=2;
  K_BITACORA_CONDONA_CS     NUMBER:=22;

  vl_cod_deudor             NUMBER;
  vl_cod_deuda              NUMBER;
  vl_monto_lp               NUMBER;
  vl_cod_moneda             NUMBER;
  vl_cod_condonacion        NUMBER;
  vl_valor_moneda           NUMBER;
  vl_cod_deuda_condonacion  NUMBER;
  vl_cod_comprobante1_new   NUMBER := NULL;
  vl_cod_comprobante2_new	NUMBER := NULL;

BEGIN
   SELECT   cod_estado_condonacion_cs
   INTO     vl_cod_estado_antes
   FROM     condonacion_cs
   WHERE    cod_condonacion_cs = ve_cod_condonacion_cs;

   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO condonacion_cs
                  (cod_condonacion_cs
                  ,fecha_condonacion_cs
                  ,cod_estado_condonacion_cs
                  ,cod_deuda
                  ,obs)
        VALUES    (condonacion_cs_sq.NEXTVAL
                  ,ve_fecha_condonacion_cs
                  ,ve_cod_estado_condonacion_cs
                  ,ve_cod_deuda
                  ,ve_obs);
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE condonacion_cs
         SET cod_estado_condonacion_cs = ve_cod_estado_condonacion_cs
             ,obs = ve_obs
       WHERE cod_condonacion_cs = ve_cod_condonacion_cs;

       IF(vl_cod_estado_antes = kl_cod_estado_emitida AND ve_cod_estado_condonacion_cs = kl_cod_estado_confirmada) THEN
          spu_condonacion_cs('CONFIRMA', ve_cod_condonacion_cs, NULL, NULL, NULL, NULL, ve_cod_usuario_confirma);   
       ELSIF(vl_cod_estado_antes = kl_cod_estado_emitida AND ve_cod_estado_condonacion_cs = K_COD_ESTADO_RECHAZA) THEN
          spu_condonacion_cs('RECHAZA', ve_cod_condonacion_cs, NULL, NULL, NULL, NULL, ve_cod_usuario_confirma);   
       END IF;
   ELSIF (ve_operacion = 'CONFIRMA') THEN 
    SELECT d.cod_deudor
            ,c.cod_deuda
            ,t.cod_moneda
      INTO vl_cod_deudor
          ,vl_cod_deuda
          ,vl_cod_moneda
      FROM condonacion_cs c, deuda d, tipo_deuda t
      WHERE c.cod_condonacion_cs = ve_cod_condonacion_cs
        AND d.cod_deuda = c.cod_deuda
        AND t.cod_tipo_deuda = d.cod_tipo_deuda;

      SELECT f_pp_saldo_plan_pago(cod_plan_pago)
      INTO vl_monto_lp
      FROM plan_pago
      WHERE cod_deuda = vl_cod_deuda
        AND ano_cobro = 9999;

      spu_condonacion('INSERT', NULL, ve_cod_usuario_confirma, K_TIPO_CONDONACION_CS, vl_cod_deudor, NULL, K_CONDONACION_SOLICITADA, ve_cod_usuario_confirma, NULL, NULL);
      SELECT condonacion_sq.CURRVAL INTO vl_cod_condonacion FROM dual;
      spu_condonacion('UPDATE', vl_cod_condonacion, ve_cod_usuario_confirma, K_TIPO_CONDONACION_CS, vl_cod_deudor, NULL, K_CONDONACION_CONFIRMADA, ve_cod_usuario_confirma, NULL, NULL);

      vl_valor_moneda := f_mon_get_valor_moneda (vl_cod_moneda, sysdate);
      vl_monto_lp := ROUND (vl_monto_lp * vl_valor_moneda, 0);
      sp_condonacion_deuda('INSERT', NULL, vl_cod_condonacion, vl_cod_deuda, 0, 0, 0, vl_monto_lp, 'N', ve_cod_usuario_confirma);
      SELECT deuda_condonacion_sq.CURRVAL INTO vl_cod_deuda_condonacion FROM dual;
      sp_condonacion_deuda('UPDATE', vl_cod_deuda_condonacion, vl_cod_condonacion, vl_cod_deuda, 0, 0, 0, vl_monto_lp, 'S', ve_cod_usuario_confirma);


      spu_bitacora_deudor ('Condonación CS, deuda : ' || To_Char(vl_cod_deuda), K_BITACORA_CONDONA_CS, vl_cod_deudor, ve_cod_usuario_confirma);
   ELSIF (ve_operacion = 'RECHAZA') THEN 
      SELECT d.cod_deudor
            ,c.cod_deuda
      INTO vl_cod_deudor
          ,vl_cod_deuda
      FROM condonacion_cs c, deuda d
      WHERE c.cod_condonacion_cs = ve_cod_condonacion_cs
        AND d.cod_deuda = c.cod_deuda;

      sp_deuda_cuota_cs (vl_cod_deuda, To_Number(To_Char(SYSDATE,'yyyy')), 'N', null, vl_cod_comprobante1_new, vl_cod_comprobante2_new, ve_cod_usuario_confirma);
   END IF;
END;
/