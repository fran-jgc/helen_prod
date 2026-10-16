PROMPT CREATE OR REPLACE FUNCTION f_get_region_deudor
CREATE OR REPLACE FUNCTION f_get_region_deudor (
   ve_cod_deudor       NUMBER
 , ve_tipo_direccion   VARCHAR2
)
   RETURN NUMBER
/******************************************************************************
   NOMBRE:       f_get_region_deudor

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        01/10/2009   Cristian Sánchez   Retorna la region del Deudor de
                                              acuerdo al tipo de direccion

   NOTAS:

******************************************************************************/
IS
   vs_cod_region                        NUMBER;
BEGIN
   DECLARE
      vs_cod_region        NUMBER;
      vl_count             NUMBER;
      vl_cod_direccion     NUMBER;
   BEGIN
      SELECT  Count(*)
      INTO    vl_count
      FROM    direccion d, ciudad c
      WHERE   c.cod_ciudad = d.cod_ciudad(+)
      AND     cod_deudor = ve_cod_deudor
      AND     tipo_direccion = ve_tipo_direccion
      AND     es_valida = 'S';

      IF (vl_count = 0) THEN
        RETURN NULL;
      ELSE
        SELECT  Max(cod_direccion)
        INTO    vl_cod_direccion
        FROM    direccion d, ciudad c
        WHERE   c.cod_ciudad = d.cod_ciudad(+)
        AND     cod_deudor = ve_cod_deudor
        AND     tipo_direccion = ve_tipo_direccion
        AND     es_valida = 'S';

        SELECT  cod_region 
        INTO    vs_cod_region 
        FROM    direccion d, ciudad c
        WHERE   d.cod_ciudad = c.cod_ciudad
        AND     cod_direccion = vl_cod_direccion;

        RETURN (vs_cod_region);
      END IF;
   END;
END f_get_region_deudor;
/



