CREATE OR REPLACE PROCEDURE spu_plaza (
   ve_operacion	 	IN		VARCHAR2
 , ve_cod_plaza   IN		NUMBER
 , ve_nom_plaza   IN		VARCHAR2 := NULL
 )
AS
BEGIN
	IF (ve_operacion = 'INSERT')
	THEN
		INSERT INTO plaza
					(cod_plaza
					,nom_plaza
					)
			VALUES (ve_cod_plaza
					   ,ve_nom_plaza
					);
	ELSIF (ve_operacion = 'UPDATE')
		THEN
			UPDATE plaza
		   	SET cod_plaza = ve_cod_plaza,
            nom_plaza = ve_nom_plaza
		 	WHERE cod_plaza = ve_cod_plaza;
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM plaza
            WHERE cod_plaza = ve_cod_plaza;
	END IF;
END;
/