PROMPT CREATE OR REPLACE FUNCTION f_carta_get_cant_sub_boleta
CREATE OR REPLACE FUNCTION f_carta_get_cant_sub_boleta(ve_cod_plan_pago number)
RETURN number
AS
/******************************************************************************
   NAME:
   PURPOSE:

   REVISIONS:
   Ver        Date        Author           Description
	1.0		10/12/10		IS				retorna la cantidad de sub_boletas de un plan_pago
   ---------  ----------  ---------------  ------------------------------------


   NOTES:
******************************************************************************/
BEGIN
	DECLARE vl_fecha_vencto_pp  DATE;
		      vl_dif_meses        NUMBER;
		      vl_cant             NUMBER;
		      vl_cant_sub_boleta	NUMBER;

	BEGIN

	SELECT  FECHA_VENCTO
  INTO    vl_fecha_vencto_pp
  FROM    PLAN_PAGO
  WHERE   COD_PLAN_PAGO = ve_cod_plan_pago;

  vl_dif_meses := ceil(Months_Between(vl_fecha_vencto_pp,SYSDATE));


	IF (vl_dif_meses = 0)THEN
		vl_dif_meses := 1;
  END IF;

  select To_Number(f_get_parametro(18))
	into vl_cant_sub_boleta
	from dual;
	 

	IF (vl_dif_meses > vl_cant_sub_boleta) and (vl_cant_sub_boleta <> 0) THEN
		vl_dif_meses := vl_cant_sub_boleta;
	END IF;
  	
	return vl_dif_meses;
	
  END;
END;
/