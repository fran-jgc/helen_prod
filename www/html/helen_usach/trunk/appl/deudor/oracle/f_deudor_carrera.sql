CREATE OR REPLACE FUNCTION f_deudor_carrera(v_cod_deudor IN NUMBER)
RETURN NUMBER
AS
/******************************************************************************
   NAME:
   PURPOSE:

   REVISIONS:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------


   NOTES:
******************************************************************************/
   vs_cod_carrera NUMBER;
   vl_cod_matricula NUMBER;
   vl_cod_universidad_defecto NUMBER;
BEGIN
   BEGIN
    SELECT  VALOR
    INTO    vl_cod_universidad_defecto
    FROM    PARAMETRO
    WHERE   COD_PARAMETRO = 3;

    SELECT  Max(cod_matricula)
    INTO    vl_cod_matricula
    FROM    matricula
    WHERE   cod_deudor = v_cod_deudor
    AND     cod_universidad = vl_cod_universidad_defecto;

    IF vl_cod_matricula IS NULL THEN
      RETURN null;
     END IF;

    SELECT  cod_carrera
    INTO    vs_cod_carrera
    FROM    matricula
    WHERE   cod_matricula = vl_cod_matricula;

    RETURN  vs_cod_carrera;

   END;
END;
/