CREATE OR REPLACE FUNCTION f_ep_cancela_proceso(ve_cod_ejecucion_proceso  NUMBER)
RETURN VARCHAR2
AS
/******************************************************************************
   NAME:       f_ep_cancela_proceso
   PURPOSE:    Retorna S o N segun  si el proceso fue cancelado por el ususaio

   REVISIONS:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        12/02/2010  Victor Melo
   NOTAS:

******************************************************************************/
BEGIN

  DECLARE vl_cancela_proceso    VARCHAR2(1);
  BEGIN
    select CANCELA_PROCESO
    INTO vl_cancela_proceso
		from EJECUCION_PROCESO
		where COD_EJECUCION_PROCESO = ve_cod_ejecucion_proceso;

    RETURN vl_cancela_proceso;
  END;
END;
/