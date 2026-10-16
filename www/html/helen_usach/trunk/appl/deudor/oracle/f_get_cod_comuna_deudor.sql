CREATE OR REPLACE FUNCTION f_get_cod_comuna_deudor (
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
   vl_cod_comuna                           NUMBER;
BEGIN
   vl_cod_comuna                        := 0;

   SELECT cod_comuna
     INTO vl_cod_comuna
     FROM direccion
    WHERE cod_direccion = f_get_direccion_deudor (vl_cod_deudor, vl_tipo_direccion);

   RETURN vl_cod_comuna;
EXCEPTION
   WHEN NO_DATA_FOUND
   THEN
      NULL;
   WHEN OTHERS
   THEN
      -- Consider logging the error and then re-raise
      RAISE;
END f_get_cod_comuna_deudor;
/