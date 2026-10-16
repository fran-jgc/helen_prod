CREATE OR REPLACE FUNCTION f_get_monto_comprobante
(
   ve_mes                   NUMBER
   ,ve_ano                  NUMBER
   ,ve_cod_tipo_comprobante NUMBER
   ,ve_cod_cuenta_corriente NUMBER
)
RETURN NUMERIC
AS ve_monto     NUMERIC;




/*****************************************************************************
   NAME:       f_get_monto_comprobante

   Obtiene la suma del monto debe de los comprobantes entre fechas y tipo de
   comprobante.

   REVISIONS:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        29/03/2010  Sergio Castro    Creacion
   NOTAS:

******************************************************************************/
BEGIN
  DECLARE
    K_COMP_APROBADO     NUMBER := 2;
    K_TIPO_COMP_INGRESO NUMBER := 1;
    K_TIPO_COMP_EGRESO  NUMBER := 2;
  BEGIN
    IF (ve_cod_tipo_comprobante = K_TIPO_COMP_INGRESO) THEN
      SELECT  Nvl(SUM(MONTO_DEBE),0)
      INTO    ve_monto
      FROM    ITEM_COMPROBANTE    IC
              ,COMPROBANTE        C
              ,CUENTA_CORRIENTE   CC
      WHERE   C.COD_COMPROBANTE = IC.COD_COMPROBANTE
              AND To_Number(To_Char(FECHA_COMPROBANTE,'mm')) = ve_mes
              AND To_Number(To_Char(FECHA_COMPROBANTE,'yyyy')) = ve_ano
              AND COD_TIPO_COMPROBANTE = ve_cod_tipo_comprobante
              AND COD_ESTADO_COMPROBANTE = K_COMP_APROBADO
              AND COD_CUENTA_CORRIENTE    = ve_cod_cuenta_corriente
              AND CC.COD_CUENTA_CONTABLE = IC.COD_CUENTA_CONTABLE;
    ELSIF (ve_cod_tipo_comprobante = K_TIPO_COMP_EGRESO) THEN
      SELECT  Nvl(SUM(MONTO_HABER),0)
      INTO    ve_monto
      FROM    ITEM_COMPROBANTE    IC
              ,COMPROBANTE        C
              ,CUENTA_CORRIENTE   CC
      WHERE   C.COD_COMPROBANTE = IC.COD_COMPROBANTE
              AND To_Number(To_Char(FECHA_COMPROBANTE,'mm')) = ve_mes
              AND To_Number(To_Char(FECHA_COMPROBANTE,'yyyy')) = ve_ano
              AND COD_TIPO_COMPROBANTE    = ve_cod_tipo_comprobante
              AND COD_ESTADO_COMPROBANTE  = K_COMP_APROBADO
              AND COD_CUENTA_CORRIENTE    = ve_cod_cuenta_corriente
              AND CC.COD_CUENTA_CONTABLE  = IC.COD_CUENTA_CONTABLE;
    END IF;

    RETURN Nvl(ve_monto,0);
  END;
END;