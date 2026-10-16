PROMPT CREATE OR REPLACE PROCEDURE spu_carga_cuota_otra_cartera
CREATE OR REPLACE PROCEDURE spu_carga_cuota_otra_cartera(ve_operacion	IN	VARCHAR2
														,ve_rut			IN	NUMBER	:= NULL
					                                    ,ve_dig_verif	IN	VARCHAR2 := NULL
					                                    ,ve_monto_capital	IN	NUMBER := NULL
					                                    ,ve_monto_interes	IN	NUMBER := NULL
					                                    ,ve_fecha_vencto	IN	DATE := NULL
					                                    ,ve_cod_concepto_cobro	IN NUMBER := NULL
					                                    ,ve_ano_cobro		IN	NUMBER := NULL
					                                    ,ve_fecha_carga		IN	DATE := NULL)
AS
BEGIN
	IF (ve_operacion = 'BORRA_CARGA_CUOTA_OTRA_CARTERA') THEN
		DELETE CARGA_CUOTA_OTRA_CARTERA;
		
	ELSIF (ve_operacion = 'INSERT') THEN
		INSERT INTO CARGA_CUOTA_OTRA_CARTERA
					(COD_CARGA_CUOTA_OTRA_CARTERA
					,RUT
					,DIG_VERIF
					,MONTO_CAPITAL
					,MONTO_INTERES
					,FECHA_VENCTO
					,COD_CONCEPTO_COBRO
					,ANO_COBRO
					,FECHA_CARGA)
			VALUES	(usach_cuota_otra_cartera_sq.NEXTVAL
					,ve_rut
					,ve_dig_verif
					,ve_monto_capital
					,ve_monto_interes
					,ve_fecha_vencto
					,ve_cod_concepto_cobro
					,ve_ano_cobro
					,ve_fecha_carga);
	END IF;
	COMMIT;
END spu_carga_cuota_otra_cartera;
/