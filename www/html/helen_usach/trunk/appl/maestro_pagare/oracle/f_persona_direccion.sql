CREATE OR REPLACE FUNCTION f_persona_direccion
(
  ve_cod_deudor number,
  ve_tipo_direccion	VARCHAR2
)
RETURN number
AS
  vs_cod_direccion NUMBER;
  vl_count NUMBER;
BEGIN
  BEGIN
    select  Count(*)
    INTO    vl_count
    from    direccion
    where   direccion.cod_deudor = ve_cod_deudor and
            direccion.es_valida = 'S' and
	          direccion.tipo_direccion = ve_tipo_direccion AND
             ROWNUM < 2
   order by direccion.fecha_direccion desc;

   IF (vl_count > 0) THEN
    select  direccion.cod_direccion
    INTO    vs_cod_direccion
    from    direccion
    where   direccion.cod_deudor = ve_cod_deudor and
            direccion.es_valida = 'S' and
	          direccion.tipo_direccion = ve_tipo_direccion AND
             ROWNUM < 2
    order by direccion.fecha_direccion desc;
   ELSE
      vs_cod_direccion := 0;
   END IF;

   RETURN vs_cod_direccion;

  END;
END;
/