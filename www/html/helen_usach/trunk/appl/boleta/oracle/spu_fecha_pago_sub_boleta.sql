CREATE OR REPLACE PROCEDURE spu_fecha_pago_sub_boleta (
  ve_cod_sub_boleta       IN   NUMBER
)
AS
/******************************************************************************
   NAME:       spu_fecha_pago_sub_boleta

   Actualiza la fecha de pago de una subboleta

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        06/01/2010   Sergio Castro   Creacion del Procedimiento

******************************************************************************/
BEGIN
  UPDATE sub_boleta
  SET  fecha_pago = TO_DATE (sysdate, 'dd/mm/yyyy')
  WHERE cod_sub_boleta = ve_cod_sub_boleta;
END;
/