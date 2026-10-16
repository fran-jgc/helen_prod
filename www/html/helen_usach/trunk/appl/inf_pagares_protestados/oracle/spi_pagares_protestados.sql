CREATE OR REPLACE PROCEDURE spi_pagares_protestados (ve_fecha_inicio      DATE
                                                    ,ve_fecha_termino     DATE)
AS
BEGIN
  DECLARE   K_COD_FAMILIA_DEUDA			NUMBER:=1;  -- FONDO SOLIDARIO	COD_CUENTA: 31115
			K_PAGARE_PROTESTADO			NUMBER:=f_get_constante('K_EST_PAGARE_PROTESTADO');

         CURSOR C_PAGARES_PROTESTADOS
         IS
            SELECT	 REPLACE(TO_CHAR(DO.RUT, '99,999,999'), ',', '.') || '-' || DO.DIG_VERIF RUT
				    ,DO.APELLIDO_PATERNO
				    ,DO.APELLIDO_MATERNO
				    ,DO.NOMBRE
				    ,DI.DIRECCION
				    ,CO.NOM_COMUNA
				    ,CI.NOM_CIUDAD
				    ,DE.COD_DEUDA
				    ,P.FOLIO_PAGARE
				    ,M.SIMBOLO
				    ,P.MONTO_MONEDA
				    ,P.MONTO_PESOS
		FROM	PAGARE P, DEUDOR DO	left outer join DIRECCION DI on DO.COD_DEUDOR = DI.COD_DEUDOR
								              left outer join COMUNA CO on DI.COD_COMUNA = CO.COD_COMUNA
								              left outer join CIUDAD CI on DI.COD_CIUDAD = CI.COD_CIUDAD
			    ,DEUDA DE, TIPO_DEUDA TD, MONEDA M
		WHERE P.COD_ESTADO_PAGARE = K_PAGARE_PROTESTADO
		AND	P.FECHA_PROTESTO BETWEEN ve_fecha_inicio AND ve_fecha_termino
		AND	P.COD_DEUDA = DE.COD_DEUDA
		AND	DE.COD_DEUDOR = DO.COD_DEUDOR
		AND DE.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
		AND TD.COD_FAMILIA_DEUDA = K_COD_FAMILIA_DEUDA
		AND	TD.COD_MONEDA = M.COD_MONEDA;
                                
      BEGIN
         FOR A IN C_PAGARES_PROTESTADOS
         LOOP
            INSERT INTO TEMP_PAGARES_PROTESTADOS
                        (RUT
	                    ,APELLIDO_PATERNO
	                    ,APELLIDO_MATERNO
	                    ,NOMBRE
	                    ,DIRECCION
	                    ,NOM_COMUNA
	                    ,NOM_CIUDAD
	                    ,NRO_DEUDA
	                    ,FOLIO_PAGARE
	                    ,MONEDA
	                    ,MONTO
	                    ,MONTO_PESOS)
                 VALUES (A.RUT
				        ,A.APELLIDO_PATERNO
				        ,A.APELLIDO_MATERNO
				        ,A.NOMBRE
				        ,A.DIRECCION
				        ,A.NOM_COMUNA
				        ,A.NOM_CIUDAD
				        ,A.COD_DEUDA
				        ,A.FOLIO_PAGARE
				        ,A.SIMBOLO
				        ,A.MONTO_MONEDA
				        ,A.MONTO_PESOS);
         END LOOP;
      END;
END spi_pagares_protestados;