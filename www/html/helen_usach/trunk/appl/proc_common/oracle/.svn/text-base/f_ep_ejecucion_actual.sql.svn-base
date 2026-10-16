CREATE OR REPLACE FUNCTION f_ep_ejecucion_actual(ve_cod_proceso  NUMBER)
RETURN NUMBER
AS
/******************************************************************************
   NAME:       f_ep_ejecucion_actual
   PURPOSE:    Retorna el cod_ejecucion proceso para ve_cod_proceso que esta running
               Si no existen ejecuciones anteriores retorna 0
                   ve_cod_proceso : Proceso para el que se busca

   REVISIONS:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        12/02/2010  Victor Melo
   NOTAS:

******************************************************************************/
BEGIN

  DECLARE vl_cod_ejecucion_proceso    NUMBER;
          vl_fecha_termino            DATE;
  BEGIN
    select NVL(MAX(COD_EJECUCION_PROCESO), 0)
    INTO vl_cod_ejecucion_proceso
		from EJECUCION_PROCESO
		where COD_PROCESO = ve_cod_proceso;

    IF (vl_cod_ejecucion_proceso <> 0) then
      SELECT fecha_termino
      INTO vl_fecha_termino
      FROM EJECUCION_PROCESO
      WHERE cod_ejecucion_proceso = vl_cod_ejecucion_proceso;

      IF (vl_fecha_termino IS NULL) THEN -- esta running
        RETURN vl_cod_ejecucion_proceso;
      ELSE
        RETURN 0;
      END IF;
    END IF;

    RETURN vl_cod_ejecucion_proceso;
  END;
END;
/