CREATE OR REPLACE PROCEDURE spi_deudores_novados(ve_fecha_inicio         DATE
                                                ,ve_fecha_termino       DATE)
AS
BEGIN
  DECLARE
    K_NOVACION_APROBADA     NUMBER:=2;
  BEGIN
    INSERT INTO TEMP_DEUDORES_NOVADOS
      (COD_NOVACION
      ,FECHA_NOVACION
      ,RUT
      ,NOMBRE
      ,APELLIDO_PATERNO
      ,APELLIDO_MATERNO
      ,COD_DEUDA_ANTIGUA
      ,COD_DEUDA_NUEVA
      ,FOLIO_PAGARE)
    SELECT n.cod_novacion
          ,n.fecha_novacion
          ,REPLACE(To_Char(de.rut, '99,999,999'), ',', '.') || '-' || de.dig_verif rut
          ,de.nombre
          ,de.apellido_paterno
          ,de.apellido_materno
          ,n.cod_deuda_antigua
          ,n.cod_deuda_nueva
          ,p.folio_pagare
    FROM novacion n, deuda d, deudor de, pagare p
    WHERE n.fecha_novacion BETWEEN ve_fecha_inicio AND ve_fecha_termino
      AND n.cod_estado_novacion = K_NOVACION_APROBADA
      AND d.cod_deuda = n.cod_deuda_antigua
      AND de.cod_deudor = d.cod_deudor
      AND p.cod_deuda = n.cod_deuda_nueva
    ORDER BY n.cod_novacion;
  END;
END spi_deudores_novados;
/