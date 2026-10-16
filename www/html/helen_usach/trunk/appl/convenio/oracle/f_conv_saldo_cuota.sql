PROMPT CREATE OR REPLACE FUNCTION f_conv_saldo_cuota
CREATE OR REPLACE FUNCTION f_conv_saldo_cuota(ve_cod_cuota_convenio number)
RETURN NUMBER
AS
BEGIN
  DECLARE vl_monto_cuota  NUMBER;
		      vl_sum_pago			NUMBER;
		      vc_cod_ingreso	NUMBER;
		      vl_abono				NUMBER;

  CURSOR C_INGRESO IS
	select i.cod_ingreso
	from pago_cuota_convenio p, ingreso i
	where p.cod_cuota_convenio = ve_cod_cuota_convenio
	  and i.cod_ingreso = p.cod_ingreso
	  and i.cod_estado_ingreso in (1, 3);	-- emitido, cerrado


		
	BEGIN
    select monto_cuota_moneda
    INTO vl_monto_cuota
	  from cuota_convenio
	  where cod_cuota_convenio = ve_cod_cuota_convenio;


	  vl_sum_pago := 0;
    FOR i IN C_INGRESO LOOP
      select sum(monto_moneda)
      INTO vl_abono
      from abono_ingreso ai, abono a
      where ai.cod_ingreso = i.cod_ingreso
	      and a.cod_abono = ai.cod_abono
	      and a.cod_estado_pago = 1;	-- vigente
		
      vl_sum_pago := vl_sum_pago + vl_abono;
                                  
		END LOOP;
                                  
	  return vl_monto_cuota -  vl_sum_pago;
  END;
END;