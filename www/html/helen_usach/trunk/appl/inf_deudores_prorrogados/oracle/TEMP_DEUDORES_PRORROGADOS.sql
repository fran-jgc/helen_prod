create global temporary table TEMP_DEUDORES_PRORROGADOS
  (COD_PRORROGA           NUMBER 
  ,FECHA_PRORROGA         DATE
  ,RUT							      VARCHAR2(20)
  ,NOMBRE                 VARCHAR2(100)
  ,APELLIDO_PATERNO       VARCHAR2(100)
  ,APELLIDO_MATERNO       VARCHAR2(100)
  ,COD_DEUDA              NUMBER 
  ,COD_TIPO_DEUDA         VARCHAR2(20)
)
on commit delete ROWS;