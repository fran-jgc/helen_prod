CREATE OR REPLACE PROCEDURE sp_deuda_abono_h(ve_cod_deuda     NUMBER
                                             ,ve_origen        varchar2
                                             ,ve_cod_origen    NUMBER)
AS
/******************************************************************************
   NAME:       sp_deuda_abono_h
   Copia a ABONO_H todos los abonos que tiene la deuda
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
  INSERT INTO abono_h
    (cod_abono_h
    ,origen
    ,cod_origen
    ,cod_abono
    ,fecha_registro
    ,fecha_abono
    ,cod_usuario
    ,cod_componente_deuda
    ,monto_capital
    ,monto_interes
    ,monto_multa
    ,monto_moneda
    ,monto_pesos
    ,diferencia_pesos
    ,cod_plan_pago
    ,cod_deuda
    ,cod_comprobante
    ,cod_abono_desde
    ,cod_estado_pago
    ,cod_origen_pago
    ,cod_sub_boleta)
  SELECT abono_h_sq.NEXTVAL
        ,ve_origen
        ,ve_cod_origen
        ,cod_abono
        ,fecha_registro
        ,fecha_abono
        ,cod_usuario
        ,cod_componente_deuda
        ,monto_capital
        ,monto_interes
        ,monto_multa
        ,monto_moneda
        ,monto_pesos
        ,diferencia_pesos
        ,cod_plan_pago
        ,cod_deuda
        ,cod_comprobante
        ,cod_abono_desde
        ,cod_estado_pago
        ,cod_origen_pago
        ,cod_sub_boleta
   FROM abono
   WHERE cod_deuda = ve_cod_deuda;
END;
/