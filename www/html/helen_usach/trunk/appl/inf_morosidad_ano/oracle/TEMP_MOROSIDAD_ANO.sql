create global temporary table TEMP_MOROSIDAD_ANO
  ( ANO                       NUMBER
    ,CANT_DEUDOR              NUMBER
    ,MONTO_MORA               NUMBER
    ,MONTO_MORA_PESOS         NUMBER
    ,MONTO_MULTA              NUMBER
    ,MONTO_MULTA_PESOS        NUMBER
    ,MONTO_TOTAL              NUMBER
    ,MONTO_TOTAL_PESOS        NUMBER
)
on commit delete ROWS;