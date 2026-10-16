CREATE OR REPLACE PROCEDURE spu_ciudad (
   ve_operacion	   	IN		VARCHAR
 , ve_cod_ciudad    IN		NUMERIC
 , ve_cod_region    IN		NUMERIC := NULL
 , ve_cod_pais      IN		NUMERIC := NULL
 , ve_nom_ciudad    IN        VARCHAR := NULL
 )
AS
BEGIN
    IF (ve_operacion = 'INSERT')
    THEN
        INSERT INTO ciudad
                    (cod_ciudad
          ,cod_region
          ,cod_pais
                    ,nom_ciudad
                    )
            VALUES (ve_cod_ciudad
             ,ve_cod_region
             ,ve_cod_pais
                       ,ve_nom_ciudad
                    );
    END IF;

    IF (ve_operacion = 'UPDATE')
        THEN
            UPDATE ciudad
               SET cod_ciudad = ve_cod_ciudad,
            cod_region = ve_cod_region,
            cod_pais   = ve_cod_pais,
            nom_ciudad = ve_nom_ciudad
             WHERE cod_ciudad = ve_cod_ciudad;
  ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM ciudad
            WHERE cod_ciudad = ve_cod_ciudad;
    END IF;
END;
/