CREATE OR REPLACE FUNCTION f_conv_calculo_cuota(ve_monto_convenio			NUMBER
										                ,ve_cant_cuota			NUMBER
										                ,ve_fecha_convenio		date
										                ,ve_fecha_inicio		date)
RETURN NUMBER
AS
BEGIN
	DECLARE
		K_PARAM_INTERES_CONVENIO	VARCHAR2(10);
		vl_monto_actualizado		  NUMBER;
		vl_tasa					        NUMBER;
		vl_cod_tipo_deuda_reneg	VARCHAR2(100);
		vl_cuota					        NUMBER;

  BEGIN
	    K_PARAM_INTERES_CONVENIO := '30';
	    vl_tasa := To_Number(f_get_parametro(K_PARAM_INTERES_CONVENIO)) / 100.00;

	    vl_monto_actualizado := f_conv_monto_actualizado(ve_monto_convenio, ve_fecha_convenio, ve_fecha_inicio);
	    vl_cuota := Round(vl_monto_actualizado * ((power((1+vl_tasa),ve_cant_cuota)) * vl_tasa)/((power((1+vl_tasa),ve_cant_cuota))-1), 4);

      return vl_cuota;
	END;


END;
/
