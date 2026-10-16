CREATE OR REPLACE PROCEDURE spu_deposito_documento (
   ve_operacion	   	        IN		VARCHAR2
 , ve_cod_documento_pago    IN		NUMBER
 , ve_cod_deposito          IN		NUMBER
 , ve_cod_deposito_documento IN   NUMBER
 )
AS

/******************************************************************************
   NAME:       spu_deposito_documento
   PURPOSE:    permite crear, actualizar depositos, ademas dependiendo del estado
                se podra crear un comprobante, item_comprobante, analitico, como tambien
                se puede anular un deposito.

   REVISIONS:
   Ver        Date        Author              Description
   ---------  ----------  ---------------     ------------------------------------
   1.0        24/10/2009   Cristian Sánchez

   NOTES:
******************************************************************************/
/*
 * 
 */  
	
BEGIN
   IF(ve_cod_deposito_documento IS null)then   
    IF (ve_operacion = 'INSERT')
	    THEN

        --INSERTAR EN deposito_doucumento
		  INSERT INTO deposito_documento
					    (cod_deposito_documento
              ,cod_documento_pago
              ,cod_deposito
              )
		  VALUES (deposito_documento_sq.NEXTVAL
              ,ve_cod_documento_pago
              ,ve_cod_deposito
            );
	  END IF;
  END IF;
  IF(ve_operacion = 'DESELECCIONAR') THEN

    DELETE DEPOSITO_DOCUMENTO
    WHERE COD_DEPOSITO = ve_cod_deposito
    AND   COD_DOCUMENTO_PAGO = ve_cod_documento_pago;		
    
  END IF;
END;