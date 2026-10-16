create global temporary table TEMP_RECAUDACION_ENTIDAD
  (RUT                    VARCHAR2(20)
  ,FECHA_ABONO            DATE
  ,MONTO_ABONO            NUMBER
)
on commit delete ROWS;
