PROMPT CREATE OR REPLACE PROCEDURE sp_susp_comprobante
CREATE OR REPLACE PROCEDURE sp_susp_comprobante(  ve_cod_comprobante          IN  NUMBER  := NULL
                                                  ,ve_fecha_comprobante       IN  DATE    := NULL
                                                  ,ve_cod_usuario             IN  NUMBER  := NULL
                                                  ,ve_cod_familia_deuda       IN  NUMBER  := NULL
                                                  ,ve_cod_origen_comprobante  IN  NUMBER  := NULL
                                                  ,ve_cod_usuario_estado      IN  NUMBER  := NULL
                                                  ,ve_fecha_estado            IN  DATE    := NULL
                                                  ,ve_cod_tipo_deuda          IN  VARCHAR := NULL
                                                  ,ve_cod_plan_pago           IN  NUMBER  := NULL
                                                  ,ve_cod_deuda               IN  NUMBER  := NULL
                                                  ,ve_cod_suspension          IN  NUMBER  := NULL
)
AS
/******************************************************************************
   NAME:       sp_susp_comprobante

   REVISION:
    Creacion del comprobante contable de una Suspensión
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        14/10/2009   Paula Cea       crea un comprobante contable y sus items
   2.0        21/11/2009   Sergio Castro   Revisión y Cambios

   NOTAS:

******************************************************************************/
BEGIN
  DECLARE
    K_TIPO_CUENTA_CP        NUMBER := 2;
    K_TIPO_CUENTA_LP        NUMBER := 3;
    K_TIPO_CASTIGO          NUMBER := 4;
    K_TIPO_UTILIDAD_NORMAL  NUMBER := 6;
    K_ESTADO_COMP_PENDIENTE NUMBER := 1;
    K_TIPO_COMP_TRASPASO    NUMBER := 3;
    K_MONEDA_UTM            NUMBER := 3;
    vl_cod_cuenta_contable  NUMBER;
    vl_cuenta_cp_cs         NUMBER;
    vl_cuenta_lp_cs         NUMBER;
    vl_cuenta_castigo       NUMBER;
    vl_cuenta_util_normal   NUMBER;
    vl_nom_comprobante      VARCHAR(60);
    vl_cod_comprobante      NUMBER;
    vl_monto_K              NUMBER;
    vl_monto_IN             NUMBER;
    vl_cod_cuota_vigente    NUMBER;
    vl_monto                NUMBER;
    vl_count                NUMBER;
    vl_fecha_vencto_pp		DATE;

  BEGIN
    vl_cuenta_cp_cs := f_comp_get_cuenta_tipo_deuda (ve_cod_tipo_deuda, K_TIPO_CUENTA_CP);
    vl_cuenta_lp_cs := f_comp_get_cuenta_tipo_deuda (ve_cod_tipo_deuda, K_TIPO_CUENTA_LP);
    vl_cuenta_castigo := f_comp_get_cuenta_tipo_deuda (ve_cod_tipo_deuda, K_TIPO_CASTIGO);
    vl_cuenta_util_normal := f_comp_get_cuenta_tipo_deuda (ve_cod_tipo_deuda, K_TIPO_UTILIDAD_NORMAL);

    SELECT  'Comprobante de Suspensión ' || CAST (ve_cod_suspension AS VARCHAR2 (10)) || ', Deuda ' || CAST (ve_cod_deuda AS VARCHAR2 (10))
    INTO    vl_nom_comprobante
    FROM    DUAL;

    --1. INGRESAR EN COMPROBANTE_CONTABLE  c/ SP ya echo
    spu_comprobante(  'INSERT'
                      ,NULL
                      ,ve_fecha_comprobante
                      ,ve_cod_usuario
                      ,vl_nom_comprobante
                      ,ve_cod_familia_deuda
                      ,K_ESTADO_COMP_PENDIENTE
                      ,K_TIPO_COMP_TRASPASO
                      ,ve_cod_origen_comprobante
                      ,NULL
                      ,NULL);
    SELECT  comprobante_sq.CURRVAL
    INTO    vl_cod_comprobante
    FROM    dual;

    /*
    UPDATE suspension
    SET cod_comprobante = vl_cod_comprobante
    WHERE  cod_suspension = ve_cod_suspension;
    */
    -- saldos de la cuota
    if(ve_cod_tipo_deuda = 'CR12')then
    	
    	select nvl(Round(monto_capital *  f_mon_get_valor_moneda(K_MONEDA_UTM), 0),0)
		into vl_monto_K
		from plan_pago 
		where cod_plan_pago = ve_cod_plan_pago;
		
		select nvl(Round(monto_interes *  f_mon_get_valor_moneda(K_MONEDA_UTM), 0),0)
		into vl_monto_IN
		from plan_pago 
		where cod_plan_pago = ve_cod_plan_pago;
   		
	else
		vl_monto_K  := Round(f_pp_saldo_capital(ve_cod_plan_pago) *  f_mon_get_valor_moneda(K_MONEDA_UTM), 0);
    	vl_monto_IN := Round(f_pp_saldo_interes_normal(ve_cod_plan_pago) *  f_mon_get_valor_moneda(K_MONEDA_UTM), 0);
	end if;
    
    
    -- verifica si la suspension es del año actual o no para decidir la forma del 
    vl_cod_cuota_vigente := f_deuda_cuota_vigente(ve_cod_deuda);
    IF (ve_cod_plan_pago = vl_cod_cuota_vigente) THEN
      -- año actual
      spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cuenta_cp_cs, 0, vl_monto_K + vl_monto_IN);
      spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cuenta_lp_cs, vl_monto_K + vl_monto_IN, 0);

      spu_analitico ('INSERT', NULL, vl_cod_comprobante, ve_cod_deuda, vl_cuenta_cp_cs, 0, vl_monto_K + vl_monto_IN);
      spu_analitico ('INSERT', NULL, vl_cod_comprobante, ve_cod_deuda, vl_cuenta_lp_cs, vl_monto_K + vl_monto_IN, 0);
    ELSE
      -- año anterior
      /* ANTES
      spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cuenta_castigo, 0, vl_monto_K);
      spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cuenta_util_normal, 0, vl_monto_IN);
      spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cuenta_lp_cs, vl_monto_K + vl_monto_IN, 0);

      spu_analitico ('INSERT', NULL, vl_cod_comprobante, ve_cod_deuda, vl_cuenta_castigo, 0, vl_monto_K);
      spu_analitico ('INSERT', NULL, vl_cod_comprobante, ve_cod_deuda, vl_cuenta_util_normal, 0, vl_monto_IN);
      spu_analitico ('INSERT', NULL, vl_cod_comprobante, ve_cod_deuda, vl_cuenta_lp_cs, vl_monto_K + vl_monto_IN, 0);
      */
      /*
      hay algo hecho pero se debe revisar
      - revisar que permita suspender cuotas antiguas
      - comprobante para cuando se suspende cuota antigua

                          debe   haber
      cta_castigo                  x
      lp                   x

      donde x es el saldo en mora (usar f_saldo_mora)
      recuerda que el comprobante es en $
      */
    	
    /*  Según correo enviado por Fabiola a Iván, fecha: 22-01-2015
     *  Iván
		Referente a este punto, si bien la operación la efectua corrrectamente, lo que agregamos como monto en utm se encuentra incorrecto ya que muestra la cuota sin el descuento y lo que suspende es el total de la cuota , con el descuento y por ende la contabilidad se encuentra incorrecta ya que el asiento queda por el monto de la cuota rebajado el descuento.
		Y ayer se me olvidó recordar, pero lo que siempre les recuerdo, despúes del vencimiento de la cuota, se considera la cuenta castigo, pasado 90 días de vencida la cuota, antes de esta fecha se considera la cuenta de corto plazo.
		atenta a sus comentarios, Fabiola
     */
    	
   		
   	  select fecha_vencto + 90
   	  into vl_fecha_vencto_pp
   	  from plan_pago 
   	  where cod_plan_pago = ve_cod_plan_pago;
   	  
   	  if(ve_fecha_estado > vl_fecha_vencto_pp + 90)then 
   	  
   	  	if(ve_cod_tipo_deuda = 'CR12')then
    		select nvl(Round((monto_capital + monto_interes) *  f_mon_get_valor_moneda(K_MONEDA_UTM), 0),0)
			into vl_monto
			from plan_pago 
			where cod_plan_pago = ve_cod_plan_pago;
		else
   	  		vl_monto := f_pp_saldo_mora (ve_cod_plan_pago,ve_fecha_comprobante) * f_mon_get_valor_moneda(K_MONEDA_UTM,ve_fecha_comprobante);
	    end if;  
    	  
    	  
    	  spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cuenta_castigo, 0, vl_monto);
	      spu_analitico ('INSERT', NULL, vl_cod_comprobante, ve_cod_deuda, vl_cuenta_castigo, 0, vl_monto);
	      spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cuenta_lp_cs, vl_monto, 0);
	      spu_analitico ('INSERT', NULL, vl_cod_comprobante, ve_cod_deuda, vl_cuenta_lp_cs, vl_monto, 0);
	  else
	  
	  	  if(ve_cod_tipo_deuda = 'CR12')then
    		select nvl(Round((monto_capital + monto_interes) *  f_mon_get_valor_moneda(K_MONEDA_UTM), 0),0)
			into vl_monto
			from plan_pago 
			where cod_plan_pago = ve_cod_plan_pago;
		  else
	  	  	vl_monto := f_pp_saldo_mora (ve_cod_plan_pago,ve_fecha_comprobante) * f_mon_get_valor_moneda(K_MONEDA_UTM,ve_fecha_comprobante);
	      end if;
	      
	  	  spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cuenta_cp_cs, 0, vl_monto);
	      spu_analitico ('INSERT', NULL, vl_cod_comprobante, ve_cod_deuda, vl_cuenta_cp_cs, 0, vl_monto);
	      spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cuenta_lp_cs, vl_monto, 0);
	      spu_analitico ('INSERT', NULL, vl_cod_comprobante, ve_cod_deuda, vl_cuenta_lp_cs, vl_monto, 0);
	 end if;

    END IF;
    
   
    /* Este codigo se usa para los tipo_deuda = 'CS', 
    ya que el comprobante puede ser 0*/
    SELECT Count(*)
    INTO vl_count
    FROM comprobante
    WHERE cod_comprobante = vl_cod_comprobante;

    IF (vl_count <> 0)THEN 
      UPDATE suspension
      SET cod_comprobante = vl_cod_comprobante
      WHERE  cod_suspension = ve_cod_suspension; 
    END IF;

  END;
END sp_susp_comprobante;
/

