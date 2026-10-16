PROMPT CREATE OR REPLACE PROCEDURE spi_balance_ocho
CREATE OR REPLACE PROCEDURE spi_balance_ocho(ve_fecha                   date
                                             ,ve_cod_familia            NUMBER
                                             ,ve_aprobado               VARCHAR2
                                             ,ve_pendiente              VARCHAR2)
AS
BEGIN
   DECLARE
    vl_cod_estado_ini             NUMBER;
    vl_cod_estado_fin             NUMBER;

   BEGIN

    IF (ve_pendiente='S') THEN
      vl_cod_estado_ini := 1;
    ELSE
      vl_cod_estado_ini := 2;
    END IF;
    IF (ve_aprobado='S') THEN
      vl_cod_estado_fin := 2;
    ELSE
      vl_cod_estado_fin := 1;
    END IF;


    INSERT INTO TEMP_BALANCE_OCHO
        (COD_CUENTA_CONTABLE
        ,NOM_CUENTA_CONTABLE
        ,MONTO_DEBE
        ,MONTO_HABER
        ,SALDO_DEBITO
        ,SALDO_CREDITO
        ,ACTIVO
        ,PASIVO
        ,PERDIDA
        ,UTILIDAD)
    SELECT COD_CUENTA_CONTABLE,
          NOM_CUENTA_CONTABLE,
          MONTO_DEBE,
          MONTO_HABER,
          Decode(sign(MONTO_DEBE - MONTO_HABER), 1, MONTO_DEBE - MONTO_HABER, 0) SALDO_DEBITO,
          Decode(sign(MONTO_DEBE - MONTO_HABER),-1, MONTO_HABER - MONTO_DEBE, 0) SALDO_CREDITO,
          Decode(COD_TIPO_CUENTA_CONTABLE,1, decode(sign(MONTO_DEBE - MONTO_HABER), 1, MONTO_DEBE - MONTO_HABER, 0),
                                          2, decode(sign(MONTO_DEBE - MONTO_HABER), 1, MONTO_DEBE - MONTO_HABER, 0),
                                          0) ACTIVO,
          Decode(COD_TIPO_CUENTA_CONTABLE,2, decode(sign(MONTO_HABER - MONTO_DEBE), 1, MONTO_HABER - MONTO_DEBE, 0),
                                          1, decode(sign(MONTO_HABER - MONTO_DEBE), 1, MONTO_HABER - MONTO_DEBE, 0),
                                          0) PASIVO,
          Decode(COD_TIPO_CUENTA_CONTABLE,3, decode(sign(MONTO_DEBE - MONTO_HABER), 1, MONTO_DEBE - MONTO_HABER, 0),
                                          4, decode(sign(MONTO_DEBE - MONTO_HABER), 1, MONTO_DEBE - MONTO_HABER, 0),
                                          0) PERDIDA,
          Decode(COD_TIPO_CUENTA_CONTABLE,4, decode(sign(MONTO_HABER - MONTO_DEBE), 1, MONTO_HABER - MONTO_DEBE, 0),
                                          3, decode(sign(MONTO_HABER - MONTO_DEBE), 1, MONTO_HABER - MONTO_DEBE, 0),
                                          0) UTILIDAD
    FROM (SELECT CTA.COD_CUENTA_CONTABLE
                  ,CTA.NOM_CUENTA_CONTABLE
                  ,SUM(IT.MONTO_DEBE) MONTO_DEBE
                  ,SUM(IT.MONTO_HABER) MONTO_HABER
                  ,CTA.COD_TIPO_CUENTA_CONTABLE
          FROM COMPROBANTE C, ITEM_COMPROBANTE IT, CUENTA_CONTABLE CTA
          WHERE C.COD_FAMILIA_DEUDA = ve_cod_familia
            AND C.COD_ESTADO_COMPROBANTE BETWEEN vl_cod_estado_ini AND vl_cod_estado_fin
            AND C.FECHA_COMPROBANTE BETWEEN To_Date('01/01/' || To_Char(ve_fecha,'YYYY'),'dd/mm/yyyy') AND ve_fecha
            AND IT.COD_COMPROBANTE = C.COD_COMPROBANTE
            AND CTA.COD_CUENTA_CONTABLE = IT.COD_CUENTA_CONTABLE
          GROUP BY CTA.COD_CUENTA_CONTABLE
                  ,CTA.NOM_CUENTA_CONTABLE
                  ,CTA.COD_TIPO_CUENTA_CONTABLE
          );
  END;
END;
/
