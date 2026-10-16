create global temporary table TEMP_LIBRO_MAYOR2
  (COD_TEMP_LIBRO_MAYOR number
  		,FECHA_COMPROBANTE        	VARCHAR2(20)
		,NRO_COMPROBANTE            number
		,COD_CUENTA_CONTABLE      	number
		,NOM_CUENTA_CONTABLE      	VARCHAR2(100)
		,RUT						VARCHAR2(10)
		,NOM_CAJERO					VARCHAR2(100)
		,MONTO_DEBE               	number
		,MONTO_HABER              	number
		,DEBITO               	  	number
		,CREDITO               		number
		,SALDO               		number
		,INICIAL_DEBITO             number
		,INICIAL_CREDITO            number
		,INICIAL_SALDO              number
)
on commit delete ROWS;
