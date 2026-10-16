create global temporary table TEMP_LIBRO_DIARIO
  ( NRO_COMPROBANTE            number
		,FECHA_COMPROBANTE        date
		,NOM_TIPO_COMPROBANTE     VARCHAR2(100)
		,COD_CUENTA_CONTABLE      number
		,NOM_CUENTA_CONTABLE      VARCHAR2(100)
		,MONTO_DEBE               number
		,MONTO_HABER              number
		,NOM_ESTADO_COMPROBANTE   VARCHAR2(100)
		,NOM_COMPROBANTE          VARCHAR2(100)
)
on commit delete ROWS;