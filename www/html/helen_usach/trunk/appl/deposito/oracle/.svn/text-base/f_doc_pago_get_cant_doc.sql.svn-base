PROMPT CREATE OR REPLACE FUNCTION f_doc_pago_get_cant_doc
CREATE OR REPLACE FUNCTION f_doc_pago_get_cant_doc
(
   ve_cod_deposito   NUMBER
)
RETURN NUMERIC

AS        ve_cantidad     NUMERIC;
/******************************************************************************
   NAME:       f_doc_pago_get_cant_doc
   PURPOSE:  contar la cantidad de documentos que estes asociados a un deposito en especifico,
            se mostrara en el output de deposito

   REVISIONS:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        23/10/2009  Cristian Sánchez  cuenta los documentos que esten asociados a un deposito.
   NOTAS:

******************************************************************************/
BEGIN
       BEGIN
          SELECT  count(COD_DEPOSITO) INTO ve_cantidad FROM DEPOSITO_DOCUMENTO  WHERE COD_DEPOSITO=ve_cod_deposito;

          RETURN ve_cantidad;
       END;
END;
/

