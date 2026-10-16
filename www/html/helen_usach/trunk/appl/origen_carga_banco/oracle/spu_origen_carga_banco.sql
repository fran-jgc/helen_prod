CREATE OR REPLACE PROCEDURE spu_origen_carga_banco(
   ve_operacion       		        IN       VARCHAR
 , ve_cod_origen_carga_banco     IN       NUMERIC
 , ve_nom_origen_carga_banco     IN       VARCHAR := NULL
 , ve_cod_cuenta_corriente			    IN		   NUMERIC := NULL
)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO origen_carga_banco
                  (cod_origen_carga_banco
                  ,nom_origen_carga_banco
                  ,cod_cuenta_corriente
                  )
           VALUES (origen_carga_banco_sq.NEXTVAL
                  ,ve_nom_origen_carga_banco
                  ,ve_cod_cuenta_corriente
                  );
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE origen_carga_banco
      SET    nom_origen_carga_banco = ve_nom_origen_carga_banco
             , cod_cuenta_corriente = ve_cod_cuenta_corriente
      WHERE  cod_origen_carga_banco = ve_cod_origen_carga_banco;
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM origen_carga_banco
            WHERE cod_origen_carga_banco = ve_cod_origen_carga_banco;
   END IF;
END;
/