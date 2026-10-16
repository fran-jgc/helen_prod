CREATE OR REPLACE FUNCTION f_conv_monto_actualizado(ve_monto_convenio		NUMBER
										,ve_fecha_convenio		date
										,ve_fecha_inicio		date)
RETURN NUMBER
AS
BEGIN
	DECLARE
		
    K_PARAM_INTERES_CONVENIO	VARCHAR2(10);
		vl_monto_actualizado		NUMBER;
		vl_tasa					NUMBER;
		vl_cod_tipo_deuda_reneg	varchar2(100);
		vl_fecha_act				DATE;


  BEGIN



	K_PARAM_INTERES_CONVENIO := '30';
  vl_tasa := To_Number(f_get_parametro(K_PARAM_INTERES_CONVENIO)) / 100;

	vl_fecha_act := ADD_MONTHS(ve_fecha_inicio,-1);
	vl_monto_actualizado := round(ve_monto_convenio * power (1+vl_tasa, (to_date(vl_fecha_act,'DD-MM-YYYY') - to_date(ve_fecha_convenio,'DD-MM-YYYY'))  / 30.0000), 4);

	return vl_monto_actualizado;

  END;

END;
/

