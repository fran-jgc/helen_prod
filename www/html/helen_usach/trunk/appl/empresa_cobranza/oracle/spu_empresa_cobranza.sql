CREATE OR REPLACE PROCEDURE spu_empresa_cobranza (
   ve_operacion               IN       VARCHAR
 , ve_cod_empresa_cobranza    IN       NUMERIC
 , ve_nom_empresa_cobranza    IN       VARCHAR := NULL
)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO empresa_cobranza
                  (cod_empresa_cobranza
                  ,nom_empresa_cobranza
                  )
           VALUES (ve_cod_empresa_cobranza
                  ,ve_nom_empresa_cobranza
                  );
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE empresa_cobranza
         SET cod_empresa_cobranza = ve_cod_empresa_cobranza,
             nom_empresa_cobranza = ve_nom_empresa_cobranza
       WHERE cod_empresa_cobranza = ve_cod_empresa_cobranza;
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM empresa_cobranza
            WHERE cod_empresa_cobranza = ve_cod_empresa_cobranza;
   END IF;
END;
/
