PROMPT CREATE OR REPLACE PROCEDURE spi_libro_diario
CREATE OR REPLACE PROCEDURE spi_libro_diario(ve_fecha_inicio            date
                                             ,ve_fecha_termino          date
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


    INSERT INTO TEMP_LIBRO_DIARIO
        (NRO_COMPROBANTE
        ,FECHA_COMPROBANTE
        ,NOM_TIPO_COMPROBANTE
        ,COD_CUENTA_CONTABLE
        ,NOM_CUENTA_CONTABLE
        ,MONTO_DEBE
        ,MONTO_HABER
        ,NOM_ESTADO_COMPROBANTE
        ,NOM_COMPROBANTE)
    SELECT C.NRO_COMPROBANTE
          ,C.FECHA_COMPROBANTE
          ,T.NOM_TIPO_COMPROBANTE
          ,IT.COD_CUENTA_CONTABLE
          ,CTA.NOM_CUENTA_CONTABLE
          ,IT.MONTO_DEBE
          ,IT.MONTO_HABER
          ,E.NOM_ESTADO_COMPROBANTE
          ,C.NOM_COMPROBANTE
    FROM COMPROBANTE C, TIPO_COMPROBANTE T, ITEM_COMPROBANTE IT, CUENTA_CONTABLE CTA, ESTADO_COMPROBANTE E
    WHERE C.COD_FAMILIA_DEUDA = ve_cod_familia
      AND C.COD_ESTADO_COMPROBANTE BETWEEN vl_cod_estado_ini AND vl_cod_estado_fin
      AND C.FECHA_COMPROBANTE BETWEEN ve_fecha_inicio AND ve_fecha_termino + 1 - 1/(24*3600)
      AND T.COD_TIPO_COMPROBANTE = C.COD_TIPO_COMPROBANTE
      AND IT.COD_COMPROBANTE = C.COD_COMPROBANTE
      AND CTA.COD_CUENTA_CONTABLE = IT.COD_CUENTA_CONTABLE
      AND E.COD_ESTADO_COMPROBANTE = C.COD_ESTADO_COMPROBANTE;
  END;
END;
/
