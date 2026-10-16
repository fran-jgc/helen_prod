CREATE OR REPLACE PROCEDURE sp_novacion(ve_cod_novacion         NUMBER
                                        ,ve_cod_usuario         NUMBER
                                        ,vs_cod_deuda_new   OUT NUMBER
                                        ,vs_cod_comprobante OUT NUMBER)
AS
/******************************************************************************
   NAME:       sp_novacion
   Realiza la novacion de una deuda CS a CSN
   Se marca como novada la deuda inicial, se crea una nueva deuda con su respectivo pagare y se crea el comprobante contable
   Solo se pueden novar deudas en estado No exigible

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        02/12/2009   Victor Melo

   NOTAS:

******************************************************************************/
BEGIN
  DECLARE
    K_DEUDA_NOVADA            NUMBER:=7;
    K_DEUDA_EXIGIBLE          NUMBER:=2;
    K_CARTERA_POR_DEFECTO     NUMBER:=2;
	  K_UNIVERSIDAD_POR_DEFECTO NUMBER:=3;
    K_EST_COBRANZA_NORMAL     NUMBER:=1;
    K_EST_PAGARE_VIG          NUMBER;
    K_EST_COMPROBANTE_PDTE    NUMBER:=1;
    K_COMPROB_TRASPASO        NUMBER:=3;
    K_ORIGEN_COMPROB_NOVACION NUMBER:=6;
    K_CTA_SIN_VENCTO          NUMBER:=1;
    K_CTA_LARGO_PLAZO         NUMBER:=3;
    K_CTA_INTERES_NORMAL      NUMBER:=6;

    vl_cod_deuda              NUMBER;
    vl_cod_deuda_antigua      NUMBER;
    vl_cod_moneda             NUMBER;
    vl_saldo_moneda           NUMBER;
    vl_saldo_pesos            NUMBER;
    vl_saldo_nueva_deuda      NUMBER;
    vl_diferencia             NUMBER;
    vl_fecha_novacion         DATE;
    vl_cod_deudor             NUMBER;
    vl_cant_cuota             NUMBER;
    vl_nom_comprobante        VARCHAR2(100);
    vl_cod_familia_deuda      NUMBER;
    vl_valor_moneda           NUMBER;
    vl_fecha_inicio_cobro     DATE;
    vl_fecha_pagare_novacion  DATE;

    vl_cta_svp_CS             NUMBER;
    vl_cta_lp_CSN             NUMBER;
    vl_cta_in_CSN             NUMBER;
    vl_count                  NUMBER;
    vl_cod_pagare             NUMBER;
    vl_cod_universidad        NUMBER;

    CURSOR C_CRUZADO (vec_cod_deuda NUMBER)IS
      SELECT  COD_CRUZADO
              ,PORC
      FROM    CRUZADO
      WHERE   COD_DEUDA = vec_cod_deuda;

  BEGIN

    SELECT  f_get_constante('K_EST_PAGARE_VIG')
    INTO    K_EST_PAGARE_VIG
    FROM    dual;


    SELECT  cod_deuda_antigua
            ,fecha_novacion
            ,td.cod_moneda
            ,d.cod_deudor
            ,n.nro_anos     -- CAMBIAR A CANT_CUOTA
            ,td.cod_familia_deuda
            ,d.fecha_inicio_cobro
    INTO    vl_cod_deuda_antigua
            ,vl_fecha_novacion
            ,vl_cod_moneda
            ,vl_cod_deudor
            ,vl_cant_cuota
            ,vl_cod_familia_deuda
            ,vl_fecha_inicio_cobro
    FROM    novacion n
            ,deuda d
            ,tipo_deuda td
    WHERE   n.cod_novacion = ve_cod_novacion
            AND d.cod_deuda = n.cod_deuda_antigua
            AND td.cod_tipo_deuda = d.cod_tipo_deuda;

    --calcular el saldo de la deuda
    --vl_saldo_moneda := f_deuda_saldo_total(vl_cod_deuda_antigua, vl_fecha_novacion);
    --vl_saldo_pesos  := Round(vl_saldo_moneda * f_mon_get_valor_moneda(vl_cod_moneda, vl_fecha_novacion), 0);
    vl_saldo_moneda := f_deuda_saldo_total(vl_cod_deuda_antigua, To_Date('31/12/' || To_Char(vl_fecha_inicio_cobro - 1,'yyyy'), 'dd/mm/yyyy'));
    --vl_saldo_pesos  := Round(vl_saldo_moneda * f_mon_get_valor_moneda(vl_cod_moneda, To_Date('31/12/' || To_Char(vl_fecha_inicio_cobro - 1,'yyyy'), 'dd/mm/yyyy')), 0);
	--se debe dejar el valor de vl_saldo_pesos en cero por que no se puede obtener el valor de la UTM futura.
    vl_saldo_pesos  := 0;
    
    --cambiar estado de la deuda a Novada = 7
    sp_deuda_update_estado (vl_cod_deuda_antigua, K_DEUDA_NOVADA);

    spu_deuda ('INSERT', NULL, To_Number(f_get_parametro(K_CARTERA_POR_DEFECTO)), To_Number(f_get_parametro(K_UNIVERSIDAD_POR_DEFECTO)), vl_cod_deudor, 'CSN', vl_fecha_inicio_cobro);

    SELECT  deuda_sq.CURRVAL
    INTO    vs_cod_deuda_new
    FROM    dual;

    UPDATE  deuda
    SET     cant_cuota = vl_cant_cuota
    WHERE   cod_deuda = vs_cod_deuda_new;

    -- crear pagare
    SELECT  Count(*)
    INTO    vl_count
    FROM    CRUZADO
    WHERE   COD_DEUDA = vl_cod_deuda_antigua;
    
    vl_fecha_pagare_novacion := To_Date('31/12/' || To_Char(vl_fecha_inicio_cobro - 1,'yyyy'), 'dd/mm/yyyy');

    IF (vl_count > 0) THEN
      FOR a IN C_CRUZADO (vl_cod_deuda_antigua) LOOP
        spu_pagare ('INSERT', NULL, vl_fecha_pagare_novacion, To_Char(ve_cod_novacion), vl_saldo_moneda * a.porc / 100, vl_saldo_pesos * a.porc / 100, K_EST_PAGARE_VIG, vs_cod_deuda_new);

        SELECT  pagare_sq.CURRVAL
				INTO vl_cod_pagare
				FROM dual;

        SELECT cod_universidad
        INTO vl_cod_universidad 
        FROM deuda 
        WHERE cod_deuda = vl_cod_deuda_antigua;

        --actualiza el cod_universidad en pagare
        UPDATE pagare
        SET cod_universidad = vl_cod_universidad
        WHERE cod_pagare = vl_cod_pagare;        
        
        UPDATE  cruzado
        SET     cod_deuda   = vs_cod_deuda_new
        WHERE   cod_cruzado = a.cod_cruzado;
      END LOOP;
    ELSE
      spu_pagare ('INSERT', NULL, vl_fecha_pagare_novacion, To_Char(ve_cod_novacion), vl_saldo_moneda, vl_saldo_pesos, K_EST_PAGARE_VIG, vs_cod_deuda_new);

       SELECT  pagare_sq.CURRVAL
				INTO vl_cod_pagare
				FROM dual;

        SELECT cod_universidad
        INTO vl_cod_universidad 
        FROM deuda 
        WHERE cod_deuda = vl_cod_deuda_antigua;

        --actualiza el cod_universidad en pagare
        UPDATE pagare
        SET cod_universidad = vl_cod_universidad
        WHERE cod_pagare = vl_cod_pagare;        
        
    END IF;

    --5.- crear plan pago
    sp_deuda_genera_plan(vs_cod_deuda_new, ve_cod_usuario);

    --6.- crear comprobante contable
    --vl_saldo_pesos estaba en cero, porque el pagare se crea en cero. Para efectos contables se calcula a la UTM de hoy
	  vl_saldo_pesos  := Round(vl_saldo_moneda * f_mon_get_valor_moneda(vl_cod_moneda, SYSDATE), 0);
    
    vl_nom_comprobante := 'Novacion deuda CS:'||To_Char(vl_cod_deuda_antigua)||' a deuda CSN: '||To_Char(vs_cod_deuda_new);

    spu_comprobante ('INSERT', NULL, To_Date(vl_fecha_novacion, 'dd/mm/yyyy'), ve_cod_usuario, vl_nom_comprobante, vl_cod_familia_deuda, K_EST_COMPROBANTE_PDTE, K_COMPROB_TRASPASO, K_ORIGEN_COMPROB_NOVACION, ve_cod_usuario, sysdate);

    SELECT  comprobante_sq.CURRVAL
    INTO    vs_cod_comprobante
    FROM    dual;

    vl_cta_svp_CS := f_comp_get_cuenta_tipo_deuda ('CS', K_CTA_SIN_VENCTO);
    vl_cta_in_CSN := f_comp_get_cuenta_tipo_deuda ('CSN', K_CTA_INTERES_NORMAL);
    vl_cta_lp_CSN := f_comp_get_cuenta_tipo_deuda ('CSN', K_CTA_LARGO_PLAZO);

    vl_saldo_moneda := f_deuda_saldo_total(vs_cod_deuda_new, sysdate);
    vl_saldo_nueva_deuda  := Round(vl_saldo_moneda * f_mon_get_valor_moneda(vl_cod_moneda, vl_fecha_novacion), 0);
    vl_diferencia :=  vl_saldo_nueva_deuda - vl_saldo_pesos;

    spu_item_comprobante ('INSERT', NULL, vs_cod_comprobante, vl_cta_svp_CS, 0, vl_saldo_pesos);
    spu_item_comprobante ('INSERT', NULL, vs_cod_comprobante, vl_cta_in_CSN, 0, vl_diferencia);
    spu_item_comprobante ('INSERT', NULL, vs_cod_comprobante, vl_cta_lp_CSN, vl_saldo_nueva_deuda, 0);

    -- crear el analitico del comprobante asociado a la deuda original (que son 3 lineas)
    spu_analitico ('INSERT', NULL, vs_cod_comprobante, vl_cod_deuda_antigua, vl_cta_svp_CS, 0, vl_saldo_pesos);
    spu_analitico ('INSERT', NULL, vs_cod_comprobante, vs_cod_deuda_new, vl_cta_in_CSN, 0, vl_diferencia);
    spu_analitico ('INSERT', NULL, vs_cod_comprobante, vs_cod_deuda_new, vl_cta_lp_CSN, vl_saldo_nueva_deuda, 0);
  END;
END;
/
