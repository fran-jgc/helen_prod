CREATE OR REPLACE PROCEDURE spu_conciliacion_egreso(
                                            ve_operacion                IN   VARCHAR2
                                            ,ve_cod_conciliacion_egreso IN   conciliacion_egreso.cod_conciliacion_egreso%TYPE
                                            ,ve_cod_conciliacion        IN   conciliacion_egreso.cod_conciliacion %TYPE       := NULL
                                            ,ve_nro_cheque              IN   VARCHAR2
                                            ,ve_nom_conciliacion_egreso IN   conciliacion_egreso.nom_conciliacion_egreso%TYPE := NULL
                                            ,ve_monto_egreso            IN   conciliacion_egreso.monto_egreso%TYPE            := NULL
)
AS
/****************************************************************************************************
   NAME:       spu_conciliacion_egreso

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        26/03/2010  Sergio Castro    Creación

   NOTAS:


*****************************************************************************************************/
BEGIN
  BEGIN
    IF (ve_operacion = 'INSERT') THEN
      INSERT INTO CONCILIACION_EGRESO  (COD_CONCILIACION_EGRESO
                                        ,COD_CONCILIACION
                                        ,NRO_CHEQUE
                                        ,NOM_CONCILIACION_EGRESO
                                        ,MONTO_EGRESO)
      VALUES                            (conciliacion_egreso_sq.NEXTVAL
                                        ,ve_cod_conciliacion
                                        ,ve_nro_cheque
                                        ,ve_nom_conciliacion_egreso
                                        ,ve_monto_egreso);
      ELSIF (ve_operacion = 'UPDATE') THEN
        UPDATE  CONCILIACION_EGRESO
        SET     COD_CONCILIACION         = ve_cod_conciliacion
                ,NOM_CONCILIACION_EGRESO = ve_nom_conciliacion_egreso
                ,MONTO_EGRESO            = ve_monto_egreso
        WHERE   COD_CONCILIACION_EGRESO  = ve_cod_conciliacion_egreso;
      ELSIF (ve_operacion = 'DELETE') THEN
        DELETE FROM CONCILIACION_EGRESO
        WHERE       COD_CONCILIACION_EGRESO  = ve_cod_conciliacion_egreso;
      END IF;
   END;
END;
/