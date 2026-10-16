CREATE OR REPLACE PROCEDURE spu_cuota_anual(ve_operacion	IN VARCHAR2
											,ve_cod_cuota_anual	IN NUMBER := NULL
											,ve_cod_pagare_reprogramacion IN	NUMBER:= NULL
											,ve_nro_cuota		IN	NUMBER:= NULL
											,ve_fecha_vencimiento	IN DATE := NULL
											,ve_monto		NUMBER := NULL
											,ve_deuda_capital		NUMBER:= NULL
											,ve_capital	NUMBER := NULL
											,ve_interes	NUMBER := NULL
											,ve_interes_penal_capitalizado	NUMBER := NULL
											,ve_interes_penal		NUMBER := NULL
											,ve_interes_normal		NUMBER := NULL
											,ve_valor_cuota_cuponera		NUMBER := NULL
											,ve_dscto_int_penal_adeudado	NUMBER := NULL
											,ve_saldo_int_penal_adeudado	NUMBER := NULL
											,ve_acum_dscto_sig_periodo		NUMBER := NULL)
AS
BEGIN
	IF (ve_operacion = 'INSERT') THEN
		INSERT INTO CUOTA_ANUAL(COD_CUOTA_ANUAL
								,COD_PAGARE_REPROGRAMACION
								,NRO
								,FECHA_VCTO
								,MONTO
								,DEUDA_CAPITAL
								,CAPITAL
								,INTERES
								,INTERES_PENAL_CAPITALIZADO
								,INTERES_PENAL
								,INTERES_NORMAL
								,VALOR_CUOTA_CUPONERA
								,DSCTO_INT_PENAL_ADEUDADO
								,SALDO_INT_PENAL_ADEUDADO
								,ACUM_DSCTO_SIG_PERIODO)
 						VALUES	(cuota_pago_anual_sq.NEXTVAL
 								,ve_cod_pagare_reprogramacion
								,ve_nro_cuota
								,ve_fecha_vencimiento
								,ve_monto
								,ve_deuda_capital
								,ve_capital
								,ve_interes
								,ve_interes_penal_capitalizado
								,ve_interes_penal
								,ve_interes_normal
								,ve_valor_cuota_cuponera
								,ve_dscto_int_penal_adeudado
								,ve_saldo_int_penal_adeudado
								,ve_acum_dscto_sig_periodo);
	ELSIF (ve_operacion = 'UPDATE')THEN
			UPDATE CUOTA_ANUAL
			SET 	COD_PAGARE_REPROGRAMACION	= ve_cod_pagare_reprogramacion
					,NRO			= ve_nro_cuota
					,FECHA_VCTO		= ve_fecha_vencimiento
					,MONTO			= ve_monto
					,DEUDA_CAPITAL	= ve_deuda_capital
					,CAPITAL		= ve_capital
					,INTERES		= ve_interes
					,INTERES_PENAL_CAPITALIZADO	= ve_interes_penal_capitalizado
					,INTERES_PENAL		= ve_interes_penal
					,INTERES_NORMAL		= ve_interes_normal
					,VALOR_CUOTA_CUPONERA		= ve_valor_cuota_cuponera
					,DSCTO_INT_PENAL_ADEUDADO	= ve_dscto_int_penal_adeudado
					,SALDO_INT_PENAL_ADEUDADO	= ve_saldo_int_penal_adeudado
					,ACUM_DSCTO_SIG_PERIODO		= ve_acum_dscto_sig_periodo
			WHERE COD_CUOTA_ANUAL = ve_cod_cuota_anual;
	ELSIF (ve_operacion = 'DELETE')THEN
		DELETE FROM CUOTA_ANUAL
		WHERE COD_CUOTA_ANUAL = ve_cod_cuota_anual;
	END IF;
END;