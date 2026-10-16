create global temporary table TEMP_DEUDAS_CANCELADAS
  ( RUT							      VARCHAR2(20)
  ,COD_DEUDA              NUMBER
  ,MONTO_ABONO            NUMBER
  ,MONTO_DESCTO            NUMBER
  ,MONTO_TOTAL            NUMBER
)
on commit delete ROWS;
