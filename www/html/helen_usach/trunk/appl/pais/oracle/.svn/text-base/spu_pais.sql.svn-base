CREATE OR REPLACE PROCEDURE spu_pais (
   ve_operacion	 	IN		VARCHAR
 , ve_cod_pais    IN		NUMERIC
 , ve_nom_pais    IN		VARCHAR := NULL
 )
AS
BEGIN
	IF (ve_operacion = 'INSERT')
	THEN
		INSERT INTO pais
					(cod_pais
					,nom_pais
					)
			VALUES (ve_cod_pais
					   ,ve_nom_pais
					);
	ELSIF (ve_operacion = 'UPDATE')
		THEN
			UPDATE pais
		   	SET cod_pais = ve_cod_pais,
            nom_pais = ve_nom_pais
		 	WHERE cod_pais = ve_cod_pais;
  ELSIF (ve_operacion = 'DELETE')
    THEN
      DELETE FROM pais
            WHERE cod_pais = ve_cod_pais;
	END IF;
END;
/