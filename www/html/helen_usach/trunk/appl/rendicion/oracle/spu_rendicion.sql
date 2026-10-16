CREATE OR REPLACE PROCEDURE spu_rendicion(
   ve_operacion             IN       VARCHAR2
 , ve_cod_rendicion         IN       NUMBER
 , ve_cod_usuario           IN       NUMBER   := NULL
 , ve_fecha_inicio          IN       DATE     := NULL
 , ve_fecha_termino         IN       DATE     := NULL
 , ve_cod_estado_rendicion  IN       NUMBER   := NULL
 , ve_cod_tipo_deuda        IN       VARCHAR2 := NULL
 , ve_cod_cuenta_corriente  IN       NUMBER   := NULL
)
AS
BEGIN
  DECLARE
    K_RENDICION_SOLICITADA        NUMBER := 1;
    K_RENDICION_APROBADA          NUMBER := 2;
    K_CTA_POR_RENDIR              NUMBER := 26;
    K_EST_COMPROBANTE_PENDIENTE   NUMBER := 1;
    K_TIPO_COMPROBANTE_TRASPASO   NUMBER := 3;
    K_ORIG_COMP_RENDICION         NUMBER := 25;

    vl_estado_rendicion_anterior  NUMBER;
    vl_cta_banco                  NUMBER;
    vl_cta_rendicion              NUMBER;
    vl_cod_universidad_temp       NUMBER;
    vl_cod_comprobante            NUMBER;
    vl_fecha_abono                DATE;
    vl_cod_usuario                NUMBER;
    vl_cod_plan_pago              NUMBER;
    vl_monto_capital_rendido	  NUMBER;
    vl_monto_moneda_rendido       NUMBER;
    vl_monto_interes_rendido      NUMBER;
    vl_monto_multa_rendido        NUMBER;
    vl_monto_pesos_rendido        NUMBER;
    vl_monto_capital			  NUMBER;
    vl_monto_moneda               NUMBER;
    vl_monto_interes              NUMBER;
    vl_monto_multa                NUMBER;
    vl_monto_pesos                NUMBER;
    vl_monto_capital_fict         NUMBER;
    vl_monto_moneda_fict          NUMBER;
    vl_monto_interes_fict         NUMBER;
    vl_monto_multa_fict           NUMBER;
    vl_monto_pesos_fict           NUMBER;
    


    CURSOR C_RENDICION_ABONO (vec_cod_rendicion NUMBER)IS
      SELECT    ra.cod_universidad
                ,nom_universidad
                ,cod_familia_deuda
                ,ra.monto_rendido_pesos
                ,ra.monto_rendido_moneda
                ,ra.monto_pesos
                ,ra.monto_moneda
                ,ra.cod_abono
                ,cod_deuda
      FROM      rendicion_abono ra
                ,universidad u
                ,rendicion r
                ,tipo_deuda td
                ,abono a
      WHERE     ra.cod_rendicion = vec_cod_rendicion
                AND ra.cod_universidad = u.cod_universidad
                AND r.cod_rendicion = ra.cod_rendicion
                AND r.cod_tipo_deuda = td.cod_tipo_deuda
                AND ra.cod_abono = a.cod_abono
      ORDER BY  ra.cod_universidad; 
  BEGIN
    IF (ve_operacion = 'INSERT') THEN
      INSERT INTO rendicion
                  ( cod_rendicion
                  , fecha_rendicion
                  , cod_usuario
                  , fecha_inicio
                  , fecha_termino
                  , cod_estado_rendicion
                  , cod_tipo_deuda
                  , cod_cuenta_corriente
                  )
           VALUES ( rendicion_sq.NEXTVAL
                  , SYSDATE
                  , ve_cod_usuario
                  , ve_fecha_inicio
                  , ve_fecha_termino
                  , ve_cod_estado_rendicion
                  , ve_cod_tipo_deuda
                  , ve_cod_cuenta_corriente
                  );
    ELSIF (ve_operacion = 'UPDATE') THEN
      SELECT  cod_estado_rendicion
      INTO    vl_estado_rendicion_anterior
      FROM    rendicion
      WHERE   cod_rendicion = ve_cod_rendicion;

      UPDATE  rendicion
      SET     cod_usuario          = ve_cod_usuario,
              fecha_inicio         = ve_fecha_inicio,
              fecha_termino        = ve_fecha_termino,
              cod_estado_rendicion = ve_cod_estado_rendicion,
              cod_tipo_deuda       = ve_cod_tipo_deuda,
              cod_cuenta_corriente = ve_cod_cuenta_corriente
      WHERE   cod_rendicion        = ve_cod_rendicion;

      IF (vl_estado_rendicion_anterior = K_RENDICION_SOLICITADA
          AND ve_cod_estado_rendicion = K_RENDICION_APROBADA) THEN

        SELECT  cod_cuenta_contable
        INTO    vl_cta_banco
        FROM    rendicion r
                ,cuenta_corriente cc
        WHERE   cc.cod_cuenta_corriente = r.cod_cuenta_corriente
                AND cod_rendicion = ve_cod_rendicion;

        vl_cta_rendicion := f_comp_get_cuenta_tipo_deuda(ve_cod_tipo_deuda, K_CTA_POR_RENDIR);
        vl_cod_comprobante := NULL;
        vl_cod_universidad_temp := 0;
        FOR A IN C_RENDICION_ABONO (ve_cod_rendicion) LOOP
          IF (vl_cod_universidad_temp <> a.cod_universidad) THEN
            spu_comprobante('INSERT'
                        , NULL
                        , SYSDATE
                        , ve_cod_usuario
                        , 'Rendicion Universidad '|| a.nom_universidad
                        , a.cod_familia_deuda
                        , K_EST_COMPROBANTE_PENDIENTE
                        , K_TIPO_COMPROBANTE_TRASPASO
                        , K_ORIG_COMP_RENDICION
                        , NULL
                        , SYSDATE);

            SELECT  comprobante_sq.CURRVAL
            INTO    vl_cod_comprobante
            FROM    dual;

            vl_cod_universidad_temp := a.cod_universidad;
          END IF;
          -- crear item comprobante
          spu_item_comprobante('INSERT', NULL, vl_cod_comprobante, vl_cta_banco, 0, a.monto_rendido_pesos);
          spu_analitico('INSERT',NULL, vl_cod_comprobante , a.cod_deuda, vl_cta_banco, 0, a.monto_rendido_pesos);

          spu_item_comprobante('INSERT', NULL, vl_cod_comprobante, vl_cta_rendicion, a.monto_rendido_pesos, 0);
          spu_analitico('INSERT',null, vl_cod_comprobante , a.cod_deuda, vl_cta_rendicion, a.monto_rendido_pesos,0); 
          
          SELECT FECHA_ABONO
       	  		,COD_USUARIO
       	  		,COD_PLAN_PAGO
       	  		,AB.MONTO_CAPITAL
       	  		,AB.MONTO_MONEDA
       	  		,AB.MONTO_INTERES
       	  		,AB.MONTO_MULTA
       	  		,AB.MONTO_PESOS
       	  		,Round(AB.MONTO_CAPITAL * C.PORC/100, 4)
       	  		,Round(AB.MONTO_MONEDA * C.PORC/100, 4) 
       	  		,Round(AB.MONTO_INTERES * C.PORC/100, 4) 
       	  		,Round(AB.MONTO_MULTA * C.PORC/100, 4)
       	  		,Round(AB.MONTO_PESOS * C.PORC/100, 0)
       	  INTO vl_fecha_abono
       	  		,vl_cod_usuario
       	  		,vl_cod_plan_pago
       	  		,vl_monto_capital
       	  		,vl_monto_moneda
       	  		,vl_monto_interes
       	  		,vl_monto_multa
       	  		,vl_monto_pesos
       	  		,vl_monto_capital_rendido
       	  		,vl_monto_moneda_rendido
       	  		,vl_monto_interes_rendido
       	  		,vl_monto_multa_rendido
       	  		,vl_monto_pesos_rendido
       	  FROM ABONO AB, CRUZADO C
       	  WHERE COD_ABONO = a.cod_abono
       	  AND C.COD_DEUDA = AB.COD_DEUDA
       	  AND C.COD_UNIVERSIDAD = a.cod_universidad;
          
       	  --se divide el abono, los montos son de acuerdo al porcentaje que le corresponde a usach
       	  UPDATE abono
          SET monto_capital = vl_monto_capital - vl_monto_capital_rendido
          	,monto_moneda = (vl_monto_capital + vl_monto_interes + vl_monto_multa) - (vl_monto_capital_rendido + vl_monto_interes_rendido + vl_monto_multa_rendido)
          	,monto_interes = vl_monto_interes - vl_monto_interes_rendido
          	,monto_multa = vl_monto_multa - vl_monto_multa_rendido
          	,monto_pesos = vl_monto_pesos - vl_monto_pesos_rendido
          where cod_abono = a.cod_abono;         
       	  
          --se calcula los montos a rendir a otra univ, y se crea como abono ficticio
       	  vl_monto_capital_fict := vl_monto_capital_rendido;
       	  vl_monto_moneda_fict := vl_monto_capital_rendido + vl_monto_interes_rendido + vl_monto_multa_rendido;
       	  vl_monto_interes_fict := vl_monto_interes_rendido;
       	  vl_monto_multa_fict := vl_monto_multa_rendido;
       	  vl_monto_pesos_fict := vl_monto_pesos_rendido;
          
          INSERT INTO ABONO_FICTICIO
          VALUES(abono_ficticio_sq.NEXTVAL  -- cod_abono_ficticio
               , SYSDATE                    -- fecha_registro
               , vl_fecha_abono               -- fecha_abono
               , vl_cod_usuario             -- cod_usuario
               , vl_monto_capital_fict      -- monto_capital
               , vl_monto_interes_fict		-- monto_interes
               , vl_monto_multa_fict		-- monto_multa
               , vl_monto_moneda_fict   	-- monto_moneda
               , vl_monto_pesos_fict   		-- monto_pesos
               , a.cod_deuda                -- cod_deuda
               , vl_cod_plan_pago           -- cod_plan_pago
               , a.cod_abono               	-- cod_abono
               ,NULL         				-- fecha_deposito
               ,NULL       					-- folio_carga
               ,NULL
               ,NULL
               ,NULL
               ,NULL
               ,NULL
               ,NULL
               ,NULL
               ,NULL
               ,NULL);
          
        END LOOP;
      END IF;
    ELSIF (ve_operacion = 'DELETE') THEN
      DELETE rendicion_abono
      WHERE cod_rendicion = ve_cod_rendicion;

      DELETE rendicion
      WHERE cod_rendicion = ve_cod_rendicion;
    END IF;
  END;
END spu_rendicion;