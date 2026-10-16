CREATE OR REPLACE PROCEDURE spu_pagare_reprogramacion(ve_operacion	VARCHAR2
													,ve_cod_pagare_reprogramacion	NUMBER := NULL
													,ve_cod_reprogramacion		NUMBER := NULL
													,ve_id_pagare	VARCHAR2	:= NULL
													,ve_monto_pago_inicial		NUMBER := NULL
													,ve_nro_cuotas_pago_inicial	NUMBER := NULL
													,ve_estado_pago_inicial	NUMBER := NULL
													,ve_cod_universidad		NUMBER := NULL
													,ve_monto_pagare			NUMBER := NULL
													,ve_saldo_capital			NUMBER := NULL
													,ve_intereses	NUMBER := NULL
													,ve_fecha_generacion_pagare	DATE := NULL
													,ve_fecha_recepcion_pagare	DATE := NULL
													,ve_porc_condonacion_int_penal	NUMBER := NULL
													,ve_estado_pagare			NUMBER := NULL
													,ve_monto_reprogramado		NUMBER := NULL
													,ve_nro_cuotas_reprogramado		NUMBER := NULL
													,ve_recibido			VARCHAR2 := NULL)
AS
BEGIN
	IF (ve_operacion = 'INSERT')THEN
	INSERT INTO PAGARE_REPROGRAMACION(COD_PAGARE_REPROGRAMACION
									 ,COD_REPROGRAMACION
									 ,ID_PAGARE
									 ,MONTO_PAGO_INICIAL
									 ,NRO_CUOTAS_PAGO_INICIAL
									 ,FECHA_PAGO_INICIAL
									 ,ESTADO_PAGO_INICIAL
									 ,COD_UNIVERSIDAD
									 ,MONTO_PAGARE
									 ,SALDO_CAPITAL
									 ,INTERESES
									 ,FECHA_GENERACION_PAGARE
									 ,FECHA_RECEPCION_PAGARE
									 ,PORC_CONDONACION_INTERES_PENAL
									 ,ESTADO_PAGARE
									 ,MONTO_REPROGRAMADO
									 ,NRO_CUOTAS_REPROGRAMADO
									 ,RECIBIDO)
							VALUES  (pagare_reprogramacion_sq.NEXTVAL
									,ve_cod_reprogramacion
									,ve_id_pagare
									,ve_monto_pago_inicial
									,ve_nro_cuotas_pago_inicial
									,null
									,ve_estado_pago_inicial
									,ve_cod_universidad
									,ve_monto_pagare
									,ve_saldo_capital
									,ve_intereses
									,ve_fecha_generacion_pagare
									,ve_fecha_recepcion_pagare
									,ve_porc_condonacion_int_penal
									,ve_estado_pagare
									,ve_monto_reprogramado
									,ve_nro_cuotas_reprogramado
									,ve_recibido);
	ELSIF (ve_operacion = 'UPDATE')THEN
		UPDATE PAGARE_REPROGRAMACION
		SET		 RECIBIDO			= ve_recibido
		WHERE COD_PAGARE_REPROGRAMACION = ve_cod_pagare_reprogramacion;	
	
	ELSIF (ve_operacion = 'DELETE')THEN
		DELETE FROM PAGARE_REPROGRAMACION
        WHERE COD_PAGARE_REPROGRAMACION = ve_cod_pagare_reprogramacion;
    END IF;
END;