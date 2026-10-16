CREATE OR REPLACE PROCEDURE spi_deudores_prorrogados(ve_fecha_inicio         DATE
                                                     ,ve_fecha_termino       DATE)
AS
BEGIN
  DECLARE
    K_PRORROGA_APROBADA     NUMBER:=2;
  BEGIN
    INSERT INTO TEMP_DEUDORES_PRORROGADOS
      (COD_PRORROGA
      ,FECHA_PRORROGA
      ,RUT
      ,NOMBRE
      ,APELLIDO_PATERNO
      ,APELLIDO_MATERNO
      ,COD_DEUDA
      ,COD_TIPO_DEUDA)
    SELECT p.cod_prorroga
          ,p.fecha_prorroga
          ,REPLACE(To_Char(de.rut, '99,999,999'), ',', '.') || '-' || de.dig_verif rut
          ,de.nombre
          ,de.apellido_paterno
          ,de.apellido_materno
          ,p.cod_deuda
          ,d.cod_tipo_deuda
    FROM prorroga p, deuda d, deudor de
    WHERE p.fecha_prorroga BETWEEN ve_fecha_inicio AND ve_fecha_termino
      AND p.cod_estado_prorroga = K_PRORROGA_APROBADA
      AND d.cod_deuda = p.cod_deuda
      AND de.cod_deudor = d.cod_deudor
    ORDER BY p.cod_prorroga;
  END;
END spi_deudores_prorrogados;
/