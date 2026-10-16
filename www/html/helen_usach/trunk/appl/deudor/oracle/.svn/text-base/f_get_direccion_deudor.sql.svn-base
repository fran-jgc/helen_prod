PROMPT CREATE OR REPLACE FUNCTION f_get_direccion_deudor
CREATE OR REPLACE FUNCTION f_get_direccion_deudor (
   ve_cod_deudor       NUMBER
 , ve_tipo_direccion   VARCHAR2
)
   RETURN NUMBER
/******************************************************************************
   NOMBRE:       f_get_direccion_deudor

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        01/10/2009   Sergio Castro   Retorna la direccion del Deudor de
                                           acuerdo al tipo de direccion

   NOTAS:

******************************************************************************/
IS
   vs_cod_direccion                        NUMBER;
BEGIN
   DECLARE
      vs_cod_direccion        NUMBER;
      vl_count                NUMBER;
   BEGIN
      SELECT Count(*)
      INTO vl_count
      FROM direccion
      WHERE cod_deudor = ve_cod_deudor
      AND tipo_direccion = ve_tipo_direccion
      AND es_valida = 'S';

      IF (vl_count = 0) THEN
        RETURN NULL;
      ELSE                          
        select cod_direccion
        INTO vs_cod_direccion
        from  
          (select cod_direccion 
              from direccion
              WHERE	cod_deudor = ve_cod_deudor
                AND		tipo_direccion = ve_tipo_direccion
                AND		es_valida = 'S'
            order by fecha_direccion DESC) 
        where ROWNUM = 1;


        RETURN (vs_cod_direccion);
      END IF;
   END;
END f_get_direccion_deudor;
/
