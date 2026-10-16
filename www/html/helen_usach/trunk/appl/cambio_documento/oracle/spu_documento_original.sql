CREATE OR REPLACE PROCEDURE spu_documento_original (ve_operacion					VARCHAR2
												 , ve_cod_documento_original   NUMBER
												 , ve_cod_cambio_documento		NUMBER := null
												 , ve_cod_documento_pago       NUMBER := null
												 )
AS
BEGIN
	    IF (ve_operacion = 'INSERT') THEN
		    INSERT INTO documento_original
                      (cod_documento_original
                    , cod_cambio_documento
                    , cod_documento_pago
					    )
              VALUES (documento_original_sq.NEXTVAL
           		    , ve_cod_cambio_documento
                    , ve_cod_documento_pago
				    );
	    
      ELSIF(ve_operacion = 'UPDATE') THEN
		      UPDATE	documento_original
            SET		cod_documento_pago = ve_cod_documento_pago
          WHERE	cod_documento_original = ve_cod_documento_original;
	    END IF;
END;
/