PROMPT CREATE OR REPLACE PROCEDURE spu_region
CREATE OR REPLACE PROCEDURE spu_region (
   ve_operacion	   	IN		VARCHAR
 , ve_cod_region    IN		NUMBER
 , ve_cod_pais      IN		NUMBER  := NULL
 , ve_nom_region    IN		VARCHAR := NULL
 )
AS
BEGIN
	IF (ve_operacion = 'INSERT')
	THEN
		INSERT INTO region
					(cod_region
          ,cod_pais
					,nom_region
					)
			VALUES (ve_cod_region
             ,ve_cod_pais
					   ,ve_nom_region
					);
	END IF;

	IF (ve_operacion = 'UPDATE')
		THEN
			UPDATE region
		   	SET cod_region = ve_cod_region,
            nom_region = ve_nom_region,
            cod_pais   = ve_cod_pais
             WHERE cod_region = ve_cod_region;
  ELSIF (ve_operacion = 'DELETE')
    THEN
      DELETE FROM region
            WHERE cod_region = ve_cod_region;

    END IF;
END;
/

