CREATE OR REPLACE PROCEDURE spu_documento_ingreso_ch_fecha (
ve_cod_ingreso                  IN NUMBER := NULL
, ve_nro_documento_pago         IN NUMBER := NULL
, ve_cod_banco                  IN NUMBER := NULL
, ve_cod_plaza                  IN NUMBER := NULL
, ve_fecha_documento_pago       IN DATE := NULL
, ve_monto                      IN NUMBER := NULL
, ve_cod_deudor                 IN NUMBER := NULL
, ve_cod_familia_deuda          IN NUMBER := NULL
, ve_rut_girador				IN NUMBER := NULL
, ve_dig_verif_girador			IN VARCHAR2 := NULL
)
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

      K_ESTADO_POR_DEPOSITAR		NUMBER:= 1;
      K_TIPO_DOC_ING_CHEQUE_FECHA	NUMBER:= 5;
      vl_cod_documento_pago			NUMBER;

      BEGIN

      spu_documento_pago ('INSERT'
                          , NULL					--ve_cod_documento_pago
                          , K_ESTADO_POR_DEPOSITAR	--ve_cod_estado_documento_pago
                          , K_TIPO_DOC_ING_CHEQUE_FECHA
                          , ve_nro_documento_pago
                          , ve_cod_banco
                          , ve_cod_plaza
                          , ve_fecha_documento_pago
                          , ve_monto
                          , ve_cod_deudor
                          , ve_cod_familia_deuda
                          , ve_rut_girador
						  , ve_dig_verif_girador);

        SELECT  documento_pago_sq.CURRVAL
        INTO vl_cod_documento_pago
        FROM dual;

        INSERT INTO documento_ingreso_ch_fecha
                (cod_documento_ingreso_ch_fecha
                ,cod_ingreso
                ,cod_documento_pago
                )
        VALUES (documento_ingreso_ch_fecha_sq.NEXTVAL
              , ve_cod_ingreso
              , vl_cod_documento_pago);

  END;
END;
/