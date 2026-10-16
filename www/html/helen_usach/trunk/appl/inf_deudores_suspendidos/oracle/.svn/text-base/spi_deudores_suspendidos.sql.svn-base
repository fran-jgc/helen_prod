CREATE OR REPLACE PROCEDURE spi_deudores_suspendidos(ve_fecha_inicio         DATE
                                                    ,ve_fecha_termino       DATE)
AS
BEGIN
  DECLARE
    K_SUSPENSION_APROBADA     NUMBER:=2;
  BEGIN
    INSERT INTO TEMP_DEUDORES_SUSPENDIDOS
      (COD_SUSPENSION
      ,FECHA_SUSPENSION
      ,RUT
      ,NOMBRE
      ,APELLIDO_PATERNO
      ,APELLIDO_MATERNO
      ,NOM_TIPO_SUSPENSION
      ,COD_DEUDA
      ,COD_TIPO_DEUDA
      ,ANO_SUSPENSION)
    SELECT s.cod_suspension
          ,s.fecha_suspension
          ,REPLACE(To_Char(de.rut, '99,999,999'), ',', '.') || '-' || de.dig_verif rut
          ,de.nombre
          ,de.apellido_paterno
          ,de.apellido_materno
          ,t.nom_tipo_suspension
          ,s.cod_deuda
          ,d.cod_tipo_deuda
          ,s.ano_suspension
    FROM suspension s, deuda d, deudor de, tipo_suspension t
    WHERE s.fecha_suspension BETWEEN ve_fecha_inicio AND ve_fecha_termino
      AND s.cod_estado_suspension = K_SUSPENSION_APROBADA
      AND d.cod_deuda = s.cod_deuda
      AND de.cod_deudor = d.cod_deudor
      AND t.cod_tipo_suspension = s.cod_tipo_suspension
    ORDER BY s.cod_suspension;
  END;
END spi_deudores_suspendidos;
/