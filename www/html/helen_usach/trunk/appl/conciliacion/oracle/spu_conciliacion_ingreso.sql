CREATE OR REPLACE PROCEDURE spu_conciliacion_ingreso(
                                            ve_operacion                  IN   VARCHAR
                                            ,ve_cod_conciliacion_ingreso  IN   conciliacion_ingreso.cod_conciliacion_ingreso%TYPE	:= NULL
                                            ,ve_cod_conciliacion          IN   conciliacion_ingreso.cod_conciliacion %TYPE			:= NULL
                                            ,ve_nom_conciliacion_ingreso  IN   conciliacion_ingreso.nom_conciliacion_ingreso%TYPE	:= NULL
                                            ,ve_monto_ingreso             IN   conciliacion_ingreso.monto_ingreso%TYPE				:= NULL
)
AS
/****************************************************************************************************
   NAME:       spu_conciliacion_ingreso

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        26/03/2010  Sergio Castro    Creación

   NOTAS:


*****************************************************************************************************/
BEGIN
  BEGIN
    IF (ve_operacion = 'INSERT') THEN
      INSERT INTO CONCILIACION_INGRESO  (COD_CONCILIACION_INGRESO
                                        ,COD_CONCILIACION
                                        ,NOM_CONCILIACION_INGRESO
                                        ,MONTO_INGRESO)
      VALUES                            (conciliacion_ingreso_sq.NEXTVAL
                                        ,ve_cod_conciliacion
                                        ,ve_nom_conciliacion_ingreso
                                        ,ve_monto_ingreso);
      ELSIF (ve_operacion = 'UPDATE') THEN
        UPDATE  CONCILIACION_INGRESO
        SET     COD_CONCILIACION          = ve_cod_conciliacion
                ,NOM_CONCILIACION_INGRESO = ve_nom_conciliacion_ingreso
                ,MONTO_INGRESO            = ve_monto_ingreso
        WHERE   COD_CONCILIACION_INGRESO  = ve_cod_conciliacion_ingreso;
      ELSIF (ve_operacion = 'DELETE') THEN
        DELETE FROM CONCILIACION_INGRESO
        WHERE       COD_CONCILIACION_INGRESO  = ve_cod_conciliacion_ingreso;
      END IF;
   END;
END;
/