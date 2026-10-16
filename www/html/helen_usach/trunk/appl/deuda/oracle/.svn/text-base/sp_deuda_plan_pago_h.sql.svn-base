CREATE OR REPLACE PROCEDURE sp_deuda_plan_pago_h(ve_cod_deuda     NUMBER
                                                ,ve_origen        varchar2
                                                ,ve_cod_origen    NUMBER)
AS
/******************************************************************************
   NAME:       sp_deuda_plan_pago_h
   Copia a PLAN_PAGO_H el actual plan de pagos de la deuda
   Entradas
    ve_cod_deuda : codigo de la deuda
    ve_origen    : string que identifica desde fue gatillado esta copia, puede ser 'SUSPENSION', 'PRORROGA', etc
    ve_cod_origen: corresponde al identificador del documento desde donde se gatillo el llamado
                   puede ser cod_suspension, cod_prorroga, etc

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        02/12/2009   Victor Melo

   NOTAS:

******************************************************************************/
BEGIN
  INSERT INTO plan_pago_h
    (cod_plan_pago_h
    ,origen
    ,cod_origen
    ,cod_plan_pago
    ,cod_deuda
    ,nro_cuota
    ,ano_cobro
    ,monto_capital
    ,monto_interes
    ,fecha_vencto
    ,cod_concepto_cobro
    ,cod_estado_plan_pago)
  SELECT plan_pago_h_sq.NEXTVAL
          , ve_origen
          , ve_cod_origen
          , cod_plan_pago
          , cod_deuda
          , nro_cuota
          , ano_cobro
          , monto_capital
          , monto_interes
          , fecha_vencto
          , cod_concepto_cobro
          , cod_estado_plan_pago
        FROM plan_pago
      WHERE cod_deuda = ve_cod_deuda;
END;
/