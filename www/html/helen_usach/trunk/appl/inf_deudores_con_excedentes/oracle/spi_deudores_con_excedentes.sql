CREATE OR REPLACE PROCEDURE spi_deudores_con_excedentes(ve_fecha_inicio         DATE
                                                        ,ve_fecha_termino         DATE
                                                        ,ve_cod_estado_excedente  NUMBER)
AS
BEGIN
  BEGIN
    INSERT INTO TEMP_DEUDORES_CON_EXCEDENTES
      (COD_EXCEDENTE
      ,FECHA_EXCEDENTE
      ,RUT
      ,NOMBRE
      ,APELLIDO_PATERNO
      ,APELLIDO_MATERNO
      ,DIRECCION
      ,NOM_COMUNA
      ,NOM_CIUDAD
      ,TELEFONO
      ,EMAIL
      ,MONTO_EXCEDENTE)
    SELECT e.cod_excedente
          ,e.fecha_excedente
          ,REPLACE(To_Char(de.rut, '99,999,999'), ',', '.') || '-' || de.dig_verif rut
          ,de.nombre
          ,de.apellido_paterno
          ,de.apellido_materno
          ,DIRECCION
          ,NOM_COMUNA
          ,NOM_CIUDAD
          ,TELEFONO
          ,EMAIL
          ,monto_excedente
    FROM excedente e
    	,DEUDOR DE LEFT OUTER JOIN DIRECCION DI ON DE.COD_DEUDOR = DI.COD_DEUDOR
						              LEFT OUTER JOIN COMUNA CO ON DI.COD_COMUNA = CO.COD_COMUNA
						              LEFT OUTER JOIN CIUDAD CI ON DI.COD_CIUDAD = CI.COD_CIUDAD
                              		  LEFT OUTER JOIN PAIS PA ON DI.COD_PAIS = PA.COD_PAIS
    WHERE e.fecha_excedente BETWEEN ve_fecha_inicio AND ve_fecha_termino
      AND e.cod_estado_excedente = ve_cod_estado_excedente
      AND de.cod_deudor = e.cod_deudor
      AND F_GET_DIRECCION_DEUDOR(DE.COD_DEUDOR, 'P') = DI.COD_DIRECCION
    ORDER BY e.fecha_excedente;
  END;
END spi_deudores_con_excedentes;
/