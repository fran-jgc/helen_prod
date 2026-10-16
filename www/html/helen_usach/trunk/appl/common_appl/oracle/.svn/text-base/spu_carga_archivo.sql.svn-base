CREATE OR REPLACE PROCEDURE spu_carga_archivo(ve_cod_proceso        IN  NUMERIC
                                              ,ve_nom_archivo_carga IN  VARCHAR)
AS
BEGIN
  INSERT INTO ARCHIVO_CARGA
              (COD_ARCHIVO_CARGA
              ,COD_PROCESO
              ,NOM_ARCHIVO_CARGA
              ,FECHA_ARCHIVO_CARGA)
        VALUES(archivo_carga_sq.NEXTVAL
              ,ve_cod_proceso
              ,ve_nom_archivo_carga
              ,sysdate
              );
END;
/