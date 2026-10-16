PROMPT CREATE OR REPLACE PROCEDURE sp_comp_ing
CREATE OR REPLACE PROCEDURE sp_comp_ing(ve_cod_comprobante        IN NUMBER
                                        ,ve_cod_abono             IN NUMBER
                                        ,ve_cod_descuento         IN NUMBER:=null
                                        ,ve_cod_ingreso			  IN NUMBER:=null)
AS
/******************************************************************************
   NAME:       sp_comp_ing

   Crea los items de comprobante contable, solo el haber,de los abonos

******************************************************************************/
BEGIN
	DECLARE
    -- componentes
    K_NO_EXIGIBLE               NUMBER:=1;
    K_VENCIDA                   NUMBER:=2;
    K_VIGENTE                   NUMBER:=3;
    K_FUTURA                    NUMBER:=4;
    K_SIN_COMPONENTE            NUMBER:=5;
    --cuentas
    K_CTA_SVP                   NUMBER:=1;
    K_CTA_CP                    NUMBER:=2;
    K_CTA_LP                    NUMBER:=3;
    K_CTA_CASTIGO_VENCIDA       NUMBER:=4;
    K_CTA_UTILIDAD_VENCIDA      NUMBER:=5;
    K_CTA_INTERES_PENAL         NUMBER:=8;
    -- estado cuota
    K_PLAN_MOROSO               NUMBER:=4;

    vl_monto_capital            NUMBER;
    vl_monto_interes            NUMBER;
    vl_monto_multa              NUMBER;
    vl_monto_moneda             NUMBER;
    vl_monto_pesos              NUMBER;
    vl_cod_componente_deuda     NUMBER;
    vl_cod_tipo_deuda           VARCHAR2(20);
    vl_cod_cuenta_svp           NUMBER;
    vl_cod_cuenta_cp            NUMBER;
    vl_cod_cuenta_lp            NUMBER;
    vl_cod_cuenta_castigo       NUMBER;
    vl_cod_cuenta_mora          NUMBER;
    vl_cod_cuenta_multa         NUMBER;
    vl_cod_deuda                NUMBER;
    vl_fecha_abono              DATE;
    vl_fecha_vencto_pp          DATE;
    vl_cod_plan_pago            NUMBER;
    vl_cod_estado_pp            NUMBER;
    vl_cod_moneda               NUMBER;
    vl_monto_K_IN_pesos         NUMBER;
    vl_monto_IP_pesos           NUMBER;
    vl_cod_analitico			NUMBER;

  BEGIN
    IF (ve_cod_descuento IS NOT NULL) THEN
      -- obtiene los datos del abono
      SELECT d.monto_capital
            ,d.monto_interes
            ,d.monto_multa
            ,d.monto_moneda
            ,d.monto_pesos
            ,d.cod_componente_deuda
            ,da.cod_tipo_deuda
            ,d.cod_deuda
            ,d.fecha_descuento
            ,d.cod_plan_pago
            ,t.cod_moneda
      INTO  vl_monto_capital
            ,vl_monto_interes
            ,vl_monto_multa
            ,vl_monto_moneda
            ,vl_monto_pesos
            ,vl_cod_componente_deuda
            ,vl_cod_tipo_deuda
            ,vl_cod_deuda
            ,vl_fecha_abono
            ,vl_cod_plan_pago
            ,vl_cod_moneda
      FROM descuento d, deuda da, tipo_deuda t
      WHERE d.cod_descuento = ve_cod_descuento
        AND da.cod_deuda = d.cod_deuda
        AND t.cod_tipo_deuda = da.cod_tipo_deuda;
    ELSE
      -- obtiene los datos del abono
      SELECT a.monto_capital
            ,a.monto_interes
            ,a.monto_multa
            ,a.monto_moneda
            ,a.monto_pesos
            ,a.cod_componente_deuda
            ,d.cod_tipo_deuda
            ,a.cod_deuda
            ,a.fecha_abono
            ,a.cod_plan_pago
            ,t.cod_moneda
      INTO  vl_monto_capital
            ,vl_monto_interes
            ,vl_monto_multa
            ,vl_monto_moneda
            ,vl_monto_pesos
            ,vl_cod_componente_deuda
            ,vl_cod_tipo_deuda
            ,vl_cod_deuda
            ,vl_fecha_abono
            ,vl_cod_plan_pago
            ,vl_cod_moneda
      FROM abono a, deuda d, tipo_deuda t
      WHERE a.cod_abono = ve_cod_abono
        AND d.cod_deuda = a.cod_deuda
        AND t.cod_tipo_deuda = d.cod_tipo_deuda;
    END IF;

    IF (vl_cod_componente_deuda = K_NO_EXIGIBLE) THEN
      vl_cod_cuenta_svp := f_comp_get_cuenta_tipo_deuda (vl_cod_tipo_deuda, K_CTA_SVP);
      spu_item_comprobante('INSERT', NULL, ve_cod_comprobante, vl_cod_cuenta_svp, 0, vl_monto_pesos);
      spu_analitico ('INSERT', NULL, ve_cod_comprobante, vl_cod_deuda, vl_cod_cuenta_svp, 0, vl_monto_pesos);
      SELECT  analitico_comprobante_sq.CURRVAL
	  INTO vl_cod_analitico
	  FROM dual;
				
	  UPDATE ANALITICO_COMPROBANTE
	  SET COD_INGRESO = ve_cod_ingreso
	  WHERE COD_ANALITICO_COMPROBANTE = vl_cod_analitico;

    ELSIF (vl_cod_componente_deuda = K_VENCIDA) THEN
      vl_cod_cuenta_castigo := f_comp_get_cuenta_tipo_deuda (vl_cod_tipo_deuda, K_CTA_CASTIGO_VENCIDA);
      vl_cod_cuenta_mora := f_comp_get_cuenta_tipo_deuda (vl_cod_tipo_deuda, K_CTA_UTILIDAD_VENCIDA);
      vl_cod_cuenta_multa := f_comp_get_cuenta_tipo_deuda (vl_cod_tipo_deuda, K_CTA_INTERES_PENAL);
      vl_cod_cuenta_cp := f_comp_get_cuenta_tipo_deuda (vl_cod_tipo_deuda, K_CTA_CP);

      SELECT fecha_vencto
            ,f_pp_get_estado(cod_plan_pago, vl_fecha_abono)
      INTO  vl_fecha_vencto_pp
            ,vl_cod_estado_pp
      FROM plan_pago
      WHERE cod_plan_pago = vl_cod_plan_pago;

      vl_monto_K_IN_pesos := Round((vl_monto_capital + vl_monto_interes) * f_mon_get_valor_moneda(vl_cod_moneda, vl_fecha_abono), 0);
      IF (vl_fecha_abono <= Add_Months(vl_fecha_vencto_pp, 3)) THEN
        -- lo contabiliza como CP
        spu_item_comprobante('INSERT', NULL, ve_cod_comprobante, vl_cod_cuenta_cp, 0, vl_monto_K_IN_pesos);
        spu_analitico ('INSERT', NULL, ve_cod_comprobante, vl_cod_deuda, vl_cod_cuenta_cp, 0, vl_monto_K_IN_pesos);
        SELECT  analitico_comprobante_sq.CURRVAL
	  	INTO vl_cod_analitico
	  	FROM dual;
				
	    UPDATE ANALITICO_COMPROBANTE
	    SET COD_INGRESO = ve_cod_ingreso
	    WHERE COD_ANALITICO_COMPROBANTE = vl_cod_analitico;
      ELSE -- MORA normal
        spu_item_comprobante('INSERT', NULL, ve_cod_comprobante, vl_cod_cuenta_mora, 0, vl_monto_K_IN_pesos);
        spu_analitico ('INSERT', NULL, ve_cod_comprobante, vl_cod_deuda, vl_cod_cuenta_mora, 0, vl_monto_K_IN_pesos);
        SELECT  analitico_comprobante_sq.CURRVAL
	  	INTO vl_cod_analitico
	  	FROM dual;
				
	    UPDATE ANALITICO_COMPROBANTE
	    SET COD_INGRESO = ve_cod_ingreso
	    WHERE COD_ANALITICO_COMPROBANTE = vl_cod_analitico;        
      END IF;

      -- toda la diferencia debe ser multa, no deberia dar < 0, si esta bien conformado el abono; pero se agrega la validación para evitar erroes
      vl_monto_IP_pesos := vl_monto_pesos - vl_monto_K_IN_pesos;
      IF (vl_monto_IP_pesos < 0) THEN
        vl_monto_IP_pesos := 0; -- si pasa esto es un error que viene desde la creacion del abono
      END IF;
      spu_item_comprobante('INSERT', NULL, ve_cod_comprobante, vl_cod_cuenta_multa, 0, vl_monto_IP_pesos);
      spu_analitico ('INSERT', NULL, ve_cod_comprobante, vl_cod_deuda, vl_cod_cuenta_multa, 0, vl_monto_IP_pesos);
      SELECT  analitico_comprobante_sq.CURRVAL
	  INTO vl_cod_analitico
	  FROM dual;
				
	  UPDATE ANALITICO_COMPROBANTE
	  SET COD_INGRESO = ve_cod_ingreso
	  WHERE COD_ANALITICO_COMPROBANTE = vl_cod_analitico;      

    ELSIF (vl_cod_componente_deuda = K_VIGENTE) THEN
      vl_cod_cuenta_cp := f_comp_get_cuenta_tipo_deuda (vl_cod_tipo_deuda, K_CTA_CP);
      spu_item_comprobante('INSERT', NULL, ve_cod_comprobante, vl_cod_cuenta_cp, 0, vl_monto_pesos);
      spu_analitico ('INSERT', NULL, ve_cod_comprobante, vl_cod_deuda, vl_cod_cuenta_cp, 0, vl_monto_pesos);
      
        SELECT  analitico_comprobante_sq.CURRVAL
	  	INTO vl_cod_analitico
	  	FROM dual;
				
	    UPDATE ANALITICO_COMPROBANTE
	    SET COD_INGRESO = ve_cod_ingreso
	    WHERE COD_ANALITICO_COMPROBANTE = vl_cod_analitico;      

    ELSIF (vl_cod_componente_deuda = K_FUTURA) THEN
      vl_cod_cuenta_lp := f_comp_get_cuenta_tipo_deuda (vl_cod_tipo_deuda, K_CTA_LP);
      spu_item_comprobante('INSERT', NULL, ve_cod_comprobante, vl_cod_cuenta_lp, 0, vl_monto_pesos);
      spu_analitico ('INSERT', NULL, ve_cod_comprobante, vl_cod_deuda, vl_cod_cuenta_lp, 0, vl_monto_pesos);

        SELECT  analitico_comprobante_sq.CURRVAL
	  	INTO vl_cod_analitico
	  	FROM dual;
				
	    UPDATE ANALITICO_COMPROBANTE
	    SET COD_INGRESO = ve_cod_ingreso
	    WHERE COD_ANALITICO_COMPROBANTE = vl_cod_analitico;      

    -- sin componente (solo casos de traspaso) se asume VIGENTE
    ELSIF (vl_cod_componente_deuda = K_SIN_COMPONENTE) THEN
      vl_cod_cuenta_cp := f_comp_get_cuenta_tipo_deuda (vl_cod_tipo_deuda, K_CTA_CP);
      spu_item_comprobante('INSERT', NULL, ve_cod_comprobante, vl_cod_cuenta_cp, 0, vl_monto_pesos);
      spu_analitico ('INSERT', NULL, ve_cod_comprobante, vl_cod_deuda, vl_cod_cuenta_cp, 0, vl_monto_pesos);
      
        SELECT  analitico_comprobante_sq.CURRVAL
	  	INTO vl_cod_analitico
	  	FROM dual;
				
	    UPDATE ANALITICO_COMPROBANTE
	    SET COD_INGRESO = ve_cod_ingreso
	    WHERE COD_ANALITICO_COMPROBANTE = vl_cod_analitico;      
    END IF;
  END;
END;
/