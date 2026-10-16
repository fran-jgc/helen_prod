PROMPT CREATE OR REPLACE FUNCTION f_ingreso_pp_en_CR
CREATE OR REPLACE FUNCTION f_ingreso_pp_en_CR(ve_cod_plan_pago number)
RETURN VARCHAR2
AS
BEGIN                                
  DECLARE  vl_count_c NUMBER;
			   vl_count_r NUMBER;

  BEGIN
	  select count(*)
    INTO vl_count_c
	  from plan_pago_convenio ppc, convenio c
	  where cod_plan_pago = ve_cod_plan_pago
		  and ppc.cod_convenio = c.cod_convenio
		  and c.cod_estado_convenio in (1,2);  			

	  select count(*)
    INTO vl_count_r
	  from plan_pago_renegociacion ppr, renegociacion r
	  where cod_plan_pago = ve_cod_plan_pago
		  and ppr.cod_renegociacion = r.cod_renegociacion
		  and r.cod_estado_renegociacion in (1,2);  			
		
	  if (vl_count_c+vl_count_r > 0)THEN
		  return 'S';
    END IF;
		
	  return 'N';
               
  END;
END;