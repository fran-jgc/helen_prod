CREATE OR REPLACE PROCEDURE spu_banco (
   ve_operacion       IN       VARCHAR
 , ve_cod_banco       IN       NUMERIC
 , ve_nom_banco       IN       VARCHAR := NULL
)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO banco
                  (cod_banco
                  ,nom_banco
                  )
           VALUES (ve_cod_banco
                  ,ve_nom_banco
                  );
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE banco
         SET cod_banco = ve_cod_banco,
             nom_banco = ve_nom_banco
       WHERE cod_banco = ve_cod_banco;
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM banco
            WHERE cod_banco = ve_cod_banco;
   END IF;
END;
/