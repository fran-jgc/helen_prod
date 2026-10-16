create global temporary table TEMP_DEUDORES_COBRANZA
  ( RUT							      VARCHAR2(20)
  ,ANO                    NUMBER
  ,COD_DEUDA              NUMBER
  ,NOM_ESTADO_PLAN_PAGO   VARCHAR2(100)
  ,MONTO_CAPITAL          NUMBER
  ,MONTO_INTERES          NUMBER
  ,MONTO_MULTA            NUMBER
  ,MONTO_ABONO_CAPITAL    NUMBER
  ,MONTO_ABONO_INTERES    NUMBER
  ,SALDO_PLAN_PAGO        NUMBER
  ,SALDO_DEUDA            NUMBER
  ,CANT_DEUDOR            NUMBER
)
on commit delete ROWS;
