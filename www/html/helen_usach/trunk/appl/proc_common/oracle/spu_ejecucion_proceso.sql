CREATE OR REPLACE PROCEDURE spu_ejecucion_proceso (ve_operacion               VARCHAR2
                                                  ,ve_cod_ejecucion_proceso   NUMBER
                                                  ,ve_cod_proceso             NUMBER := NULL
                                                  ,ve_cod_usuario             NUMBER := NULL
                                                  ,ve_valor                   NUMBER := 0
                                                  ,ve_archivo_resultado       VARCHAR2 := NULL)
AS
/******************************************************************************
   NAME:       spu_ejecucion_proceso

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        09/02/2010   VM             Registro de control de la ejecucipn de un proceso

   NOTES:

   ve_operacion :    NEW crea un nuevo registro de ejecucion_proceso
               SET_TOTAL actualiza el valor del total de registros
               SET_ACTUAL actualiza el valor de actual_registro
               CANCEL marca como cancelado la ejecucion del proceso, el sp del proceso debe preguntar permanetemente por este campo para saber si se cancelo
               END marca como terminada la ejecucion del proceso
 ve_cod_ejecucion_proceso : PK, es un parametro de entrada para identificar registro, excepto en NEW
 ve_cod_proceso : Variable de entrada usada en 2 formas distintas
                  en NEW = Codigo del proceso
                  en SET_ACTUAL y SET_TOTAL = valor a setear
                  en cualquier otro caso no es usada
 ve_cod_usuario : Usuario que ejecuta el proceso

******************************************************************************/
BEGIN
   IF (ve_operacion = 'NEW') THEN
      INSERT INTO ejecucion_proceso
        (cod_ejecucion_proceso
        ,cod_proceso
        ,cod_usuario
        ,fecha_ejecucion_proceso
        ,fecha_termino
        ,tot_registro
        ,actual_registro
        ,cancela_proceso
        ,cod_usuario_cancela
        ,archivo_resultado)
      VALUES
        (ejecucion_proceso_sq.NEXTVAL
        ,ve_cod_proceso
        ,ve_cod_usuario
        ,SYSDATE
        ,NULL
        ,1  -- Se asume que al menos existe 1 registro a procesar, esto para evitar que el avance parta en 100%
        ,0
        ,'N'
        ,NULL
        ,null);
   ELSIF (ve_operacion = 'SET_ACTUAL') THEN
      UPDATE ejecucion_proceso
         SET actual_registro = ve_cod_proceso -- viene el valor
       WHERE cod_ejecucion_proceso = ve_cod_ejecucion_proceso;
   ELSIF (ve_operacion = 'SET_TOTAL') THEN
      UPDATE ejecucion_proceso
         SET tot_registro = ve_cod_proceso -- viene el valor
            ,archivo_resultado = ve_archivo_resultado
       WHERE cod_ejecucion_proceso = ve_cod_ejecucion_proceso;
   ELSIF (ve_operacion = 'CANCEL') THEN
      UPDATE ejecucion_proceso
         SET cancela_proceso = 'S'
            ,fecha_termino = sysdate
            ,COD_USUARIO_CANCELA = ve_cod_usuario
       WHERE cod_ejecucion_proceso = ve_cod_ejecucion_proceso;
   ELSIF (ve_operacion = 'END') THEN
      UPDATE ejecucion_proceso
         SET fecha_termino = SYSDATE
            ,actual_registro = ve_cod_proceso -- viene el valor
       WHERE cod_ejecucion_proceso = ve_cod_ejecucion_proceso;
   END IF;

   /* A diferencia de todos los SPU en este caso si lleva COMMIT
      pues spu_ejecucion_proceso es llamado dfurante la ejecucion del proceso y va actualizandop el avance el cual debe ser
      leido por la appl en php
   */
   COMMIT;

END;
/