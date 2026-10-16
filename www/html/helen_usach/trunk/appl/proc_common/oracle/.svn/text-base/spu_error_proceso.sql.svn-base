CREATE OR REPLACE PROCEDURE spu_error_proceso(ve_operacion	 	          IN  VARCHAR2
                                              ,ve_cod_error_proceso     IN  NUMBER
                                              ,ve_cod_ejecucion_proceso IN  NUMBER
                                              ,ve_tipo_error            IN  VARCHAR2
                                              ,ve_error_code_bd         IN  NUMBER
                                              ,ve_error_message_bd      IN  VARCHAR2
                                              ,ve_error_message         IN  VARCHAR2)
AS
BEGIN
	IF (ve_operacion = 'INSERT') THEN
		INSERT INTO error_proceso
      (cod_error_proceso
      ,cod_ejecucion_proceso
      ,tipo_error
      ,error_code_bd
      ,error_message_bd
      ,error_message)
    VALUES
      (error_proceso_sq.NEXTVAL
      ,ve_cod_ejecucion_proceso
      ,ve_tipo_error
      ,ve_error_code_bd
      ,ve_error_message_bd
      ,ve_error_message);
	END IF;
END;
/