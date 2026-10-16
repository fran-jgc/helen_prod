CREATE OR REPLACE FUNCTION f_get_cod_pais_deudor (
   vl_cod_deudor              IN       NUMBER
 , vl_tipo_direccion          IN       VARCHAR2
)
   RETURN NUMBER
IS
   vl_cod_pais                             NUMBER;
BEGIN
   vl_cod_pais                          := 0;

   SELECT cod_pais
     INTO vl_cod_pais
     FROM direccion
    WHERE cod_direccion = f_get_direccion_deudor (vl_cod_deudor, vl_tipo_direccion);

   RETURN vl_cod_pais;
EXCEPTION
   WHEN NO_DATA_FOUND
   THEN
      NULL;
   WHEN OTHERS
   THEN
      -- Consider logging the error and then re-raise
      RAISE;
END f_get_cod_pais_deudor;
/