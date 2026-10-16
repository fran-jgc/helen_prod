CREATE OR REPLACE PROCEDURE sp_conciliacion(
                                            ve_operacion                IN   VARCHAR
                                            ,ve_cod_conciliacion        IN   conciliacion.cod_conciliacion%TYPE
                                            ,ve_fecha_conciliacion      IN   conciliacion.fecha_conciliacion%TYPE
                                            ,ve_cod_usuario             IN   conciliacion.cod_usuario%TYPE
                                            ,ve_cod_estado_conciliacion IN   conciliacion.cod_estado_conciliacion%TYPE
                                            ,ve_mes                     IN   conciliacion.mes%TYPE
                                            ,ve_ano                     IN   conciliacion.ano%TYPE
                                            ,ve_cod_cuenta_corriente    IN   conciliacion.cod_cuenta_corriente%TYPE
                                            ,ve_saldo_anterior          IN   conciliacion.saldo_anterior%TYPE
                                            ,ve_ingreso_mes             IN   conciliacion.ingreso_mes%TYPE
                                            ,ve_egreso_mes              IN   conciliacion.egreso_mes%TYPE
                                            ,ve_cheques_mes             IN   conciliacion.cheques_mes%TYPE
)
AS
/****************************************************************************************************
   NAME:       sp_conciliacion

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        25/03/2010  Sergio Castro    Creación

   NOTAS:


*****************************************************************************************************/
BEGIN
  BEGIN
    IF (ve_operacion = 'INSERT') THEN
      INSERT INTO conciliacion( cod_conciliacion
                                ,fecha_conciliacion
                                ,cod_usuario
                                ,cod_estado_conciliacion
                                ,mes
                                ,ano
                                ,cod_cuenta_corriente
                                ,saldo_anterior
                                ,ingreso_mes
                                ,egreso_mes
                                ,cheques_mes)
                        VALUES( conciliacion_sq.NEXTVAL
                                ,ve_fecha_conciliacion
                                ,ve_cod_usuario
                                ,ve_cod_estado_conciliacion
                                ,ve_mes
                                ,ve_ano
                                ,ve_cod_cuenta_corriente
                                ,ve_saldo_anterior
                                ,ve_ingreso_mes
                                ,ve_egreso_mes
                                ,ve_cheques_mes
                            );
      ELSIF (ve_operacion = 'UPDATE') THEN
        UPDATE  conciliacion
        SET     cod_estado_conciliacion  = ve_cod_estado_conciliacion
                ,mes                      = ve_mes
                ,ano                      = ve_ano
                ,cod_cuenta_corriente     = ve_cod_cuenta_corriente
                ,saldo_anterior           = ve_saldo_anterior
                ,ingreso_mes              = ve_ingreso_mes
                ,egreso_mes               = ve_egreso_mes
                ,cheques_mes              = ve_cheques_mes
        WHERE   cod_conciliacion          = ve_cod_conciliacion;

      END IF;
   END;
END;