CREATE OR REPLACE FUNCTION f_get_matricula(ve_cod_deudor  NUMBER, ve_ano NUMBER)
/*VMC 23-03-2017 para la usach
  retorna el cod_carrera, para que la agrupacion en el informe de "recupeacion por carrera" 
*/
  RETURN VARCHAR
IS
  BEGIN
  DECLARE
    K_UNIVERSIDAD_POR_DEFECTO NUMBER :=3;
    vl_count                  NUMBER;
    vl_cod_carrera            number;
    vl_ano_matricula          NUMBER;
    vl_cod_universidad_por_defecto  NUMBER;
    vl_ano                          NUMBER;

  BEGIN
    vl_cod_universidad_por_defecto := f_get_parametro(K_UNIVERSIDAD_POR_DEFECTO);

    SELECT Count(*)
    INTO  vl_count
    FROM matricula
    WHERE cod_deudor = ve_cod_deudor
      AND cod_universidad = vl_cod_universidad_por_defecto
      AND ano_matricula <= ve_ano;

    IF (vl_count > 0) THEN
      SELECT Max(ano_matricula)
      INTO vl_ano
      FROM matricula
      WHERE cod_deudor = ve_cod_deudor
        AND cod_universidad = vl_cod_universidad_por_defecto;

      SELECT cod_carrera
      INTO vl_cod_carrera
      FROM matricula
      WHERE cod_deudor = ve_cod_deudor
        AND cod_universidad = vl_cod_universidad_por_defecto
        AND ano_matricula = vl_ano
        AND ROWNUM = 1;
    ELSE
      vl_cod_carrera := 0;
    END IF;

    RETURN vl_cod_carrera;
  END;
END f_get_matricula;
/

