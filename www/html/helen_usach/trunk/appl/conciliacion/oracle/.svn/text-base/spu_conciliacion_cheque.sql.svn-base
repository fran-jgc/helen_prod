CREATE OR REPLACE PROCEDURE spu_conciliacion_cheque(
                                            ve_operacion                IN   VARCHAR
                                            ,ve_cod_conciliacion_cheque IN   conciliacion_cheque.cod_conciliacion_cheque%TYPE	:= NULL
                                            ,ve_cod_conciliacion        IN   conciliacion_cheque.cod_conciliacion %TYPE			:= NULL
                                            ,ve_nro_cheque              IN   conciliacion_cheque.nro_cheque %TYPE			:= NULL
                                            ,ve_nom_conciliacion_cheque IN   conciliacion_cheque.nom_conciliacion_cheque%TYPE	:= NULL
                                            ,ve_monto_cheque            IN   conciliacion_cheque.monto_cheque%TYPE				:= NULL
)
AS
/****************************************************************************************************
   NAME:       spu_conciliacion_cheque

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        26/03/2010  Sergio Castro    Creación

   NOTAS:


*****************************************************************************************************/
BEGIN
  BEGIN
    IF (ve_operacion = 'INSERT') THEN
      INSERT INTO CONCILIACION_CHEQUE  (COD_CONCILIACION_CHEQUE
                                        ,COD_CONCILIACION
                                        ,NRO_CHEQUE
                                        ,NOM_CONCILIACION_CHEQUE
                                        ,MONTO_cheque)
      VALUES                            (conciliacion_cheque_sq.NEXTVAL
                                        ,ve_cod_conciliacion
                                        ,ve_nro_cheque
                                        ,ve_nom_conciliacion_cheque
                                        ,ve_monto_cheque);
      ELSIF (ve_operacion = 'UPDATE') THEN
        UPDATE  CONCILIACION_CHEQUE
        SET     COD_CONCILIACION         = ve_cod_conciliacion
                ,NOM_CONCILIACION_CHEQUE = ve_nom_conciliacion_cheque
                ,MONTO_CHEQUE            = ve_monto_cheque
        WHERE   COD_CONCILIACION_CHEQUE  = ve_cod_conciliacion_cheque;
      ELSIF (ve_operacion = 'DELETE') THEN
        DELETE FROM CONCILIACION_cheque
        WHERE       COD_CONCILIACION_cheque  = ve_cod_conciliacion_cheque;
      END IF;
   END;
END;
/