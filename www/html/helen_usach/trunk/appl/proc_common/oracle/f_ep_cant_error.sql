CREATE OR REPLACE FUNCTION f_ep_cant_error(ve_cod_ejecucion_proceso NUMBER, ve_tipo_error varchar2)
RETURN NUMBER
AS
/******************************************************************************
   NAME:       f_ep_cant_error
   PURPOSE:    Retorna la cantidad de errores de f_ep_cant_error

   REVISIONS:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        11/02/2010  Victor Melo
   NOTAS:

******************************************************************************/
BEGIN
  DECLARE vl_cant    NUMBER;
  BEGIN
    select count(*)
    INTO vl_cant
		from ERROR_PROCESO
		where COD_EJECUCION_PROCESO = ve_cod_ejecucion_proceso
		  and TIPO_ERROR = ve_tipo_error;

    RETURN vl_cant;
  END;
END;
/