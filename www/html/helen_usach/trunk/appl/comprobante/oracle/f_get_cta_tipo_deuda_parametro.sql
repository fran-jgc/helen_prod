CREATE OR REPLACE FUNCTION f_get_cta_tipo_deuda_parametro(
   ve_cod_parametro                    NUMBER
)
  RETURN VARCHAR2
IS
BEGIN
  DECLARE
    valor VARCHAR2(100);
  BEGIN
    SELECT  valor
    INTO    valor
    FROM    parametro
    WHERE   cod_parametro = ve_cod_parametro;

    IF (valor = '') THEN
      valor := NULL;
    END IF;

    RETURN valor;
  END;
END f_get_cta_tipo_deuda_parametro;