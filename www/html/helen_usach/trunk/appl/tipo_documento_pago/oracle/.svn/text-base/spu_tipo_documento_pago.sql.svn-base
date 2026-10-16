CREATE OR REPLACE PROCEDURE spu_tipo_documento_pago(
   ve_operacion               	   IN       VARCHAR
 , ve_cod_tipo_documento_pago      IN       NUMERIC
 , ve_nom_tipo_documento_pago      IN       VARCHAR := NULL

)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO tipo_documento_pago
                  (cod_tipo_documento_pago
                  ,nom_tipo_documento_pago
                  )
           VALUES (ve_cod_tipo_documento_pago
                  ,ve_nom_tipo_documento_pago
                  );
   
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM tipo_documento_pago
            WHERE cod_tipo_documento_pago = ve_cod_tipo_documento_pago;
   END IF;
END;
/
