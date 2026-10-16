create global temporary table TEMP_LIBRO_MAYOR
  ( COD_TEMP_LIBRO_MAYOR number
		,NRO_COMPROBANTE            number
		,FECHA_COMPROBANTE        	date
		,NOM_COMPROBANTE   			VARCHAR2(100)
		,COD_CUENTA_CONTABLE      	number
		,NOM_CUENTA_CONTABLE      	VARCHAR2(100)
		,MONTO_DEBE               	number
		,MONTO_HABER              	number
		,NOM_ESTADO_COMPROBANTE   	VARCHAR2(100)
		,DEBITO               	  	number
		,CREDITO               		number
		,SALDO               		number
		,INICIAL_DEBITO             number
		,INICIAL_CREDITO            number
		,INICIAL_SALDO              number
)
on commit delete ROWS;
