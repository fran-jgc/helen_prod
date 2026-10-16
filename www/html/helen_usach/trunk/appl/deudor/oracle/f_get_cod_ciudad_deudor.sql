CREATE OR REPLACE FUNCTION f_get_cod_ciudad_deudor (
   vl_cod_deudor              IN       NUMBER
 , vl_tipo_direccion          IN       VARCHAR2
)
   RETURN NUMBER
IS
/******************************************************************************
   NAME:
   PURPOSE:

   REVISIONS:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------


   NOTES:
******************************************************************************/
   vl_cod_ciudad                           NUMBER;
BEGIN
   vl_cod_ciudad                        := 0;

   SELECT cod_ciudad
     INTO vl_cod_ciudad
     FROM direccion
    WHERE cod_direccion = f_get_direccion_deudor (vl_cod_deudor, vl_tipo_direccion);

   RETURN vl_cod_ciudad;
EXCEPTION
   WHEN NO_DATA_FOUND
   THEN
      NULL;
   WHEN OTHERS
   THEN
      -- Consider logging the error and then re-raise
      RAISE;
END f_get_cod_ciudad_deudor;
/