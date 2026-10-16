PROMPT CREATE OR REPLACE PROCEDURE spu_moneda
CREATE OR REPLACE PROCEDURE spu_moneda (
  ve_operacion        IN VARCHAR2,
  ve_cod_moneda       IN NUMBER,
  ve_nom_moneda       IN VARCHAR2 := NULL,
  ve_simbolo          IN VARCHAR2 := NULL,
  ve_valor_mensual    IN VARCHAR2 := NULL,
  ve_porc_proyectada  IN NUMBER
)
AS
BEGIN
  IF (ve_operacion = 'INSERT') THEN
    INSERT INTO moneda
                (nom_moneda
                ,simbolo
                ,valor_mensual
                ,porc_proyectada)
		VALUES      (ve_nom_moneda
                ,ve_simbolo
                ,ve_valor_mensual
                ,ve_porc_proyectada);
  END IF;

  IF (ve_operacion = 'UPDATE') THEN
    UPDATE  moneda
		SET     nom_moneda = ve_nom_moneda
            ,simbolo = ve_simbolo
            ,valor_mensual = ve_valor_mensual
            ,porc_proyectada = ve_porc_proyectada
	  WHERE   cod_moneda = ve_cod_moneda;
  END IF;
  IF (ve_operacion = 'DELETE') THEN
    DELETE  moneda
   	WHERE   cod_moneda = ve_cod_moneda;
  END IF;
END;
/

