CREATE OR REPLACE PROCEDURE spu_carga_matricula(  ve_operacion       IN  VARCHAR
                                                  ,ve_cod_carrera    IN  NUMBER   := NULL
                                                  ,ve_rut           IN  NUMBER   := NULL
                                                  ,ve_nom_carrera   IN  VARCHAR2 :=NULL
                                                  ,ve_ano_matricula IN  NUMBER   := NULL
                                                  ,ve_semestre      IN  NUMBER:=NULL
                                                  ,ve_cargado       IN  VARCHAR2:=NULL
                                                  ,ve_fecha_carga   IN DATE:=NULL)
AS
/******************************************************************************
   NAME:       spu_carga_matricula
               Inserta los registros leidos en del archivo carga matricula

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        07/04/2010   SC              Creación

   NOTES:

******************************************************************************/
BEGIN
  IF (ve_operacion = 'BORRA_CARGA_MATRICULA') THEN
    DELETE CARGA_MATRICULA;
  ELSIF (ve_operacion = 'INSERT') THEN
    INSERT INTO CARGA_MATRICULA
                (COD_CARGA_MATRICULA
                ,COD_CARRERA
                ,RUT
                ,NOM_CARRERA
                ,ANO_MATRICULA
                ,SEMESTRE
                ,CARGADO
                ,FECHA_CARGA)
    VALUES      (carga_matricula_sq.NEXTVAL
                ,ve_cod_carrera
                ,ve_rut
                ,ve_nom_carrera
                ,ve_ano_matricula
                ,ve_semestre
                ,ve_cargado
                ,ve_fecha_carga);
  END IF;
END;
/

