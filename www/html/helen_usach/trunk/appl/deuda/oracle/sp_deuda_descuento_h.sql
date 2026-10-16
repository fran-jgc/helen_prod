CREATE OR REPLACE PROCEDURE sp_deuda_descuento_h(ve_cod_deuda     NUMBER
                                                ,ve_origen        varchar2
                                                ,ve_cod_origen    NUMBER)
AS
/******************************************************************************
   NAME:       sp_deuda_decuento_h
   Copia a DESCUENTO_H todos los descuentos que tiene la deuda
   Entradas
    ve_cod_deuda : codigo de la deuda
    ve_origen    : string que identifica desde fue gatillado esta copia, puede ser 'PRORROGA', etc
    ve_cod_origen: corresponde al identificador del documento desde donde se gatillo el llamado
                   puede ser cod_prorroga, etc

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        02/12/2009   Victor Melo

   NOTAS:

******************************************************************************/
BEGIN
  INSERT INTO descuento_h
    (cod_descuento_h
    ,origen
    ,cod_origen
    ,cod_descuento
    ,fecha_registro
    ,fecha_descuento
    ,cod_usuario
    ,cod_componente_deuda
    ,monto_capital
    ,monto_interes
    ,monto_multa
    ,monto_moneda
    ,monto_pesos
    ,cod_plan_pago
    ,cod_deuda
    ,cod_comprobante
    ,cod_estado_pago
    ,cod_origen_pago)
  SELECT descuento_h_sq.NEXTVAL
        ,ve_origen
        ,ve_cod_origen
        ,cod_descuento
        ,fecha_registro
        ,fecha_descuento
        ,cod_usuario
        ,cod_componente_deuda
        ,monto_capital
        ,monto_interes
        ,monto_multa
        ,monto_moneda
        ,monto_pesos
        ,cod_plan_pago
        ,cod_deuda
        ,cod_comprobante
        ,cod_estado_pago
        ,cod_origen_pago
   FROM descuento
   WHERE cod_deuda = ve_cod_deuda;
END;
/