CREATE OR REPLACE PROCEDURE spu_cuenta_contable (
   ve_operacion                 IN       VARCHAR
 , ve_cod_cuenta_contable       IN       NUMERIC
 , ve_cod_tipo_cuenta_contable  IN       NUMERIC := NULL
 , ve_nom_cuenta_contable       IN       VARCHAR := NULL
)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO cuenta_contable
                  (cod_cuenta_contable
                  ,cod_tipo_cuenta_contable
                  ,nom_cuenta_contable
                  )
           VALUES (ve_cod_cuenta_contable
                  ,ve_cod_tipo_cuenta_contable
                  ,ve_nom_cuenta_contable
                  );
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE cuenta_contable
         SET cod_tipo_cuenta_contable = ve_cod_tipo_cuenta_contable
            ,nom_cuenta_contable = ve_nom_cuenta_contable
       WHERE cod_cuenta_contable = ve_cod_cuenta_contable;
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM cuenta_contable
            WHERE cod_cuenta_contable = ve_cod_cuenta_contable;
   END IF;
END;
/