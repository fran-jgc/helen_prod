CREATE OR REPLACE PROCEDURE spx_actualiza_estado_pp(ve_cod_carga_orsan_usach		IN  NUMBER  := NULL)	

AS
BEGIN
  DECLARE
    vl_cod_plan_pago			NUMBER;
     
	CURSOR 	C_PP IS
	 
		select cod_plan_pago
			  ,cod_deuda
		from plan_pago 
		where cod_deuda in (select cod_deuda
					  		from item_carga_orsan_usach 
					  		where cod_carga_orsan_usach	= ve_cod_carga_orsan_usach);

	BEGIN
		
		FOR a IN C_PP LOOP  
			
		
			sp_pp_actualiza_estado(a.cod_plan_pago);
			sp_deuda_actualiza_estado(a.cod_deuda);
		
			
		END LOOP; 
		
	END;
END;
/