CREATE OR REPLACE PROCEDURE spu_parametro (ve_cod_parametro         NUMBER
                            				,ve_valor               VARCHAR2)
AS
/******************************************************************************
   NAME:       sp_parametro
   Realiza update a parametro, recibe como parametro: cod_parametro y valor.
   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        19/12/2009   Iván Sánchez

   NOTAS:

******************************************************************************/
BEGIN
  UPDATE parametro
    SET valor = ve_valor
  WHERE cod_parametro = ve_cod_parametro;
END;
/