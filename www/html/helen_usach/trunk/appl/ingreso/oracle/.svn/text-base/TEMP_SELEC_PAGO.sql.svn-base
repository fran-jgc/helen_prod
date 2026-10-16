create global temporary table TEMP_SELEC_PAGO
  ( SELECCION                       VARCHAR2(1)
    ,TIPO                           VARCHAR2(100)	-- PAGO_NORMAL, CONVENIO_PAGO_INICIAL, CONVENIO_PAGO_CUOTA, RENEG_PAGO_INICIAL
    ,CODIGO                         NUMBER -- puede ser cod_convenio cod_cuota_convenio o o cod_renegociacion
    ,FECHA_CUOTA                    VARCHAR2(10)
    ,COD_CUOTA_CONVENIO             NUMBER
    ,NRO_CUOTA                      NUMBER
    ,MONTO_MONEDA                   NUMBER
    ,MONTO_PESOS                    NUMBER
)
on commit delete ROWS;
