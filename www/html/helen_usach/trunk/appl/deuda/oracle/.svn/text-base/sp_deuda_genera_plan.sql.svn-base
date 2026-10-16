PROMPT CREATE OR REPLACE PROCEDURE sp_deuda_genera_plan
CREATE OR REPLACE PROCEDURE sp_deuda_genera_plan( ve_cod_deuda    NUMBER
                                                  ,ve_cod_usuario NUMBER)
AS
/******************************************************************************
   NAME:       sp_deuda_genera_plan
   Crea el plan de pagos de una deuda.
   se calcula el plan de pagos en n cuotas iguales usando el calculo comuna de interes compuesto (similar a funcion PAGO de excel)
   como capital se usa MONTO_INICIO_COBRO

   Situaciones especiales:
      CS solo se debe crear la cuota 9999 con el 2% incluido
        Cuando inicia cobranza MONTO_INICIO_COBRO = pagares actualizados al 31-12-año_anterior
        la cuota 9999 se crea con los intereses del año, es decir MONTO_INICIO_COBRO *1.02
        K = MONTO_INICIO_COBRO
        IN = MONTO_INICIO_COBRO * 0.02

        en proceso "genera interes del año", este se corre a principios de año
        se debe actualizar la cuota 9999 con los nuevos intereses, primero se capitalizan los anteriores y luego se actualiza
        K = K + IN
        IN = K * 0.02

   Supuesto: Las cuotas son anuales y vencen el 31/12/cada año


   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        02/12/2009   Victor Melo

   NOTAS:

******************************************************************************/
BEGIN
   DECLARE
    K_DEUDA_NO_EXIGIBLE       NUMBER:=1;
    K_INICIO_EXIGIBLE         NUMBER:=18;
    K_DEUDA_EXIGIBLE          NUMBER:=2;
    K_PP_EMITIDA              NUMBER:=1;
    vl_fecha_inicio_cobro     DATE;
    vl_fecha                  DATE;
    vl_saldo_deuda            NUMBER;
    vl_cod_tipo_deuda         TIPO_DEUDA.COD_TIPO_DEUDA%TYPE;
    vl_cant_cuota             NUMBER;
    vl_capital                NUMBER;
    vl_interes                NUMBER;
    vl_tasa                   NUMBER;
    vl_fecha_vencto           DATE;
    vl_valor_cuota            NUMBER;
    vl_ano_cuota              NUMBER;
    vl_cod_estado_deuda_ant   NUMBER;
    vl_cod_deudor             NUMBER;

   BEGIN
    sp_deuda_cant_cuota(ve_cod_deuda);

   -- obtiene datos de la deuda
    SELECT d.cod_tipo_deuda
          ,t.tasa_interes_credito / 100
          ,d.cant_cuota
          ,d.fecha_inicio_cobro
    INTO vl_cod_tipo_deuda
        ,vl_tasa
        ,vl_cant_cuota
        ,vl_fecha_inicio_cobro
    FROM deuda d, tipo_deuda t
    WHERE d.cod_deuda = ve_cod_deuda
      AND t.cod_tipo_deuda = d.cod_tipo_deuda ;

    IF(vl_cant_cuota = 0)THEN /*si cant cuota = 0, se debe leer cant_cuota del tipo_deuda*/
      SELECT  cant_cuota
      INTO    vl_cant_cuota
      FROM    tipo_deuda
      WHERE   cod_tipo_deuda = vl_cod_tipo_deuda;  
    END IF;

    -- actualiza la deuda
    -- vl_fecha = 31/12/año anterior deuda.fecha_inicio_cobro
    vl_fecha := To_Date('01/01/' || To_Char(To_Number(To_Char(vl_fecha_inicio_cobro, 'yyyy')) - 1),'dd/mm/yyyy');
    vl_saldo_deuda := f_deuda_saldo_total(ve_cod_deuda, vl_fecha);

    UPDATE deuda
    SET monto_inicio_cobro = vl_saldo_deuda
    where cod_deuda = ve_cod_deuda;

    SELECT  cod_estado_deuda
            ,cod_deudor
    INTO    vl_cod_estado_deuda_ant
            ,vl_cod_deudor
    FROM    deuda
    WHERE   cod_deuda = ve_cod_deuda;

	  sp_deuda_update_estado(ve_cod_deuda, K_DEUDA_EXIGIBLE);

    IF (vl_cod_estado_deuda_ant = K_DEUDA_NO_EXIGIBLE) THEN
      spu_bitacora_deudor ('Inicio Exigible, deuda : ' || To_Char(ve_cod_deuda), K_INICIO_EXIGIBLE, vl_cod_deudor, ve_cod_usuario);
    END IF;

    IF (vl_cod_tipo_deuda='CS') THEN
      -- IN = MONTO_INICIO_COBRO * 0.02
      vl_interes := Round(vl_saldo_deuda * vl_tasa, 4);
      -- crear cuota 9999
      INSERT INTO plan_pago
        (cod_plan_pago,             cod_deuda,            nro_cuota,
         ano_cobro,                 monto_capital,        monto_interes,
         fecha_vencto,              cod_concepto_cobro,   cod_estado_plan_pago,
         cod_empresa_cobranza,      publica_dicom)
      VALUES
        (plan_pago_sq.NEXTVAL,      ve_cod_deuda,         99,
         9999,                      vl_saldo_deuda,       vl_interes,
         To_Date('31/12/9999','dd/mm/yyyy'), NULL,        K_PP_EMITIDA,
         NULL,                      'N');

    ELSE
      --  vl_fecha_vencto := 31/12/año actual;
      vl_fecha_vencto := To_Date('31/12/' || To_Char(vl_fecha_inicio_cobro,'yyyy'),'dd/mm/yyyy');
      
      vl_valor_cuota := Round(vl_saldo_deuda * ((Power(1+vl_tasa, vl_cant_cuota)) * vl_tasa)/((Power(1+vl_tasa, vl_cant_cuota))-1), 4);
      for i in 1..vl_cant_cuota loop
        if vl_tasa = 0 then
          vl_interes := 0;
          if i = vl_cant_cuota then
            vl_capital := vl_saldo_deuda;
          else
            vl_capital := vl_valor_cuota;
          end if;
        else
          if i = vl_cant_cuota then
              vl_capital := vl_saldo_deuda;
	          if (vl_capital = 0) then
	                vl_interes := 0;
	          else
	              vl_interes := Round(vl_valor_cuota - vl_capital, 4);
	              IF (vl_interes < 0) THEN
	                vl_interes := 0;
	              END IF;
	          end if;
          else
            vl_interes := Round(vl_saldo_deuda * vl_tasa, 4);
            vl_capital := Round(vl_valor_cuota - vl_interes, 4);
            IF (vl_capital  >  vl_saldo_deuda) then
              vl_capital := vl_saldo_deuda;
            END IF;
          end if;
        end if;

        vl_saldo_deuda := vl_saldo_deuda - vl_capital;
        vl_ano_cuota := To_Number(To_Char(vl_fecha_vencto, 'YYYY'));

        -- crea el plan_pago
        INSERT INTO plan_pago
          (cod_plan_pago,             cod_deuda,            nro_cuota,
          ano_cobro,                 monto_capital,         monto_interes,
          fecha_vencto,              cod_concepto_cobro,    cod_estado_plan_pago,
          cod_empresa_cobranza,        publica_dicom)
        VALUES
          (plan_pago_sq.NEXTVAL,      ve_cod_deuda,         i,
          vl_ano_cuota,               vl_capital,           vl_interes,
          vl_fecha_vencto,            NULL,                 K_PP_EMITIDA,
          NULL,                       'N');

        vl_fecha_vencto := Add_Months(vl_fecha_vencto, 12);
      end loop;
    END IF;
  END;
END sp_deuda_genera_plan;
/

