CREATE OR REPLACE PROCEDURE spi_credito_asig(ve_cod_tipo_deuda   VARCHAR2
                                              ,ve_ano_inicio      NUMBER
                                              ,ve_ano_termino     NUMBER)
AS
BEGIN
  DECLARE
    K_PLAN_PAGO_MOROSO      NUMBER:=4;

  BEGIN
    INSERT INTO TEMP_CREDITO_ASIG
      (FOLIO
      ,ANO
      ,RUT_DEUDOR
      ,MONTO_ASIGNADO)
	SELECT	 p.folio_pagare
			,To_Number(To_Char(p.fecha_pagare, 'yyyy')) ano
			,To_Char(DO.rut) || '-' || DO.dig_verif RUT
			,monto_moneda
	FROM	pagare p, deuda d, deudor do
	WHERE	To_Number(To_Char(p.fecha_pagare, 'yyyy')) BETWEEN ve_ano_inicio AND ve_ano_termino
	  AND	d.cod_deuda = p.cod_deuda
	  AND	d.cod_deudor = do.cod_deudor 
	  AND	d.cod_tipo_deuda = ve_cod_tipo_deuda
	ORDER BY To_Number(To_Char(p.fecha_pagare, 'yyyy'));
  END;
END spi_credito_asig;
/