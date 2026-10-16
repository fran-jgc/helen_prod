CREATE OR REPLACE PROCEDURE spu_componente_deuda (
   ve_operacion	   	        IN		VARCHAR
 , ve_cod_componente_deuda  IN		NUMERIC
 , ve_nom_componente_deuda  IN		VARCHAR := NULL
 )
AS
BEGIN
	IF (ve_operacion = 'INSERT')
	THEN
		INSERT INTO componente_deuda
					(cod_componente_deuda
          ,nom_componente_deuda
					)
			VALUES (componente_deuda_sq.NEXTVAL
             ,ve_nom_componente_deuda
					);
	END IF;

	IF (ve_operacion = 'UPDATE')
		THEN
			UPDATE componente_deuda
		   	SET cod_componente_deuda = ve_cod_componente_deuda,
            nom_componente_deuda = ve_nom_componente_deuda
		 	WHERE cod_componente_deuda = ve_cod_componente_deuda;
	END IF;
END;
/