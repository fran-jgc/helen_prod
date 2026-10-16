PROMPT CREATE OR REPLACE PROCEDURE spu_documento_ingreso
CREATE OR REPLACE PROCEDURE spu_documento_ingreso (ve_cod_ingreso                  IN NUMBER := NULL
													, ve_cod_tipo_documento_pago    IN NUMBER := NULL
													, ve_nro_documento_pago         IN NUMBER := NULL
													, ve_cod_banco                  IN NUMBER := NULL
													, ve_cod_plaza                  IN NUMBER := NULL
													, ve_fecha_documento_pago       IN DATE := NULL
													, ve_monto                      IN NUMBER := NULL
													, ve_cod_deudor                 IN NUMBER := NULL
													, ve_cod_familia_deuda          IN NUMBER := NULL)
AS
/******************************************************************************
   NOMBRE:       spu_documento_ingreso

   REVISION:
   Ver        Date         Author            Description
   ---------  ----------   ----------------  ------------------------------------
   1.0        20/12/2010 Iván Sánchez

   NOTAS:

******************************************************************************/
BEGIN
   DECLARE
			K_ESTADO_POR_DEPOSITAR	NUMBER:= 1;
			K_ESTADO_DEPOSITADO		NUMBER:= 2;
			K_DEPOSITO				NUMBER:= 3;
			K_TRANSBANK				NUMBER:= 6;
			K_AJUSTE_SALDO			NUMBER:= 99;
			vl_cod_documento_pago	NUMBER;
			vl_cod_estado_documento_pago	NUMBER;

	BEGIN

	IF(ve_cod_tipo_documento_pago = K_DEPOSITO or ve_cod_tipo_documento_pago = K_TRANSBANK or ve_cod_tipo_documento_pago = K_AJUSTE_SALDO) THEN
		vl_cod_estado_documento_pago := K_ESTADO_DEPOSITADO;
	ELSE
		vl_cod_estado_documento_pago := K_ESTADO_POR_DEPOSITAR;
	END IF;

		spu_documento_pago ('INSERT'
							,NULL			--ve_cod_documento_pago
							,vl_cod_estado_documento_pago
							,ve_cod_tipo_documento_pago
							,ve_nro_documento_pago
							,ve_cod_banco
							,ve_cod_plaza
							,ve_fecha_documento_pago
							,ve_monto
							,ve_cod_deudor
							,ve_cod_familia_deuda);

        SELECT  documento_pago_sq.CURRVAL
        INTO vl_cod_documento_pago
        FROM dual;

        INSERT INTO documento_ingreso
                (cod_documento_ingreso
                ,cod_ingreso
                ,cod_documento_pago)
        VALUES (documento_ingreso_sq.NEXTVAL
              , ve_cod_ingreso
              , vl_cod_documento_pago);

	END;
END;
/

