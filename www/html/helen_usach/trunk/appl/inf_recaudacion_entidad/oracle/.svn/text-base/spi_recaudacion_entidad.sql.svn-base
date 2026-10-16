CREATE OR REPLACE PROCEDURE spi_recaudacion_entidad(ve_fecha_inicio      DATE
                                                  ,ve_fecha_termino     DATE
                                                  ,ve_cod_origen_pago   NUMBER
                                                  ,ve_cod_tipo_deuda    VARCHAR2)
AS
BEGIN
  DECLARE
    K_ABONO_VIGENTE                 NUMBER:=1;

  BEGIN
    INSERT INTO TEMP_RECAUDACION_ENTIDAD
      (RUT
      ,FECHA_ABONO
      ,MONTO_ABONO)
    SELECT REPLACE(To_Char(de.rut, '99,999,999'), ',', '.') || '-' || de.dig_verif rut
          ,Trunc(a.fecha_abono)
          ,Sum(a.monto_moneda)
    FROM abono a, deuda d, deudor de
    WHERE a.fecha_abono BETWEEN ve_fecha_inicio AND ve_fecha_termino
      AND a.cod_origen_pago = ve_cod_origen_pago
      AND a.cod_estado_pago = K_ABONO_VIGENTE
      AND d.cod_deuda = a.cod_deuda
      AND d.cod_tipo_deuda = ve_cod_tipo_deuda
      AND de.cod_deudor = d.cod_deudor
    GROUP BY de.rut, de.dig_verif, Trunc(a.fecha_abono);

  END;
END spi_recaudacion_entidad;
/