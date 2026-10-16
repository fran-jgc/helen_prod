CREATE OR REPLACE PROCEDURE spu_comuna (
   ve_operacion	   	IN		VARCHAR2
 , ve_cod_comuna    IN		NUMBER
 , ve_cod_ciudad    IN		NUMBER   := NULL
 , ve_nom_comuna    IN		VARCHAR2 := NULL
 )
AS
BEGIN
	IF (ve_operacion = 'INSERT')
	THEN
		INSERT INTO comuna
					(cod_comuna
          ,cod_ciudad
          ,nom_comuna
					)
			VALUES (ve_cod_comuna
             ,ve_cod_ciudad
					   ,ve_nom_comuna
					);
	END IF;

	IF (ve_operacion = 'UPDATE')
		THEN
			UPDATE comuna
		   	SET cod_comuna = ve_cod_comuna,
            cod_ciudad = ve_cod_ciudad,
            nom_comuna = ve_nom_comuna
             WHERE cod_comuna = ve_cod_comuna;
  ELSIF (ve_operacion = 'DELETE')
    THEN
      DELETE FROM comuna
            WHERE cod_comuna = ve_cod_comuna;
    END IF;
END;
/