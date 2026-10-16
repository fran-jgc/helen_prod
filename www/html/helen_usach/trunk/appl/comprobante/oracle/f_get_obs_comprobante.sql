CREATE OR REPLACE FUNCTION f_get_obs_comprobante(ve_cod_comprobante number, ve_cod_origen_comprobante number)
  RETURN VARCHAR2
AS		
  BEGIN
  DECLARE K_ORIGEN_C_SUSPENSION	  NUMBER:= 3;
          K_ORIGEN_C_CONDONACION  NUMBER:= 4;
          K_ORIGEN_C_PRORROGA	    NUMBER:= 5;
          K_ORIGEN_C_NOVACION	    NUMBER:= 6;
          vl_cod_deuda            NUMBER;
          vl_observacion			    VARCHAR2(1000);
          vl_cod_deudor			      NUMBER;
	BEGIN 
	IF(K_ORIGEN_C_SUSPENSION = ve_cod_origen_comprobante)THEN 
		select	obs 
    INTO    vl_observacion
		from	  suspension
		where	  cod_comprobante = ve_cod_comprobante;

	ELSIF(K_ORIGEN_C_CONDONACION = ve_cod_origen_comprobante)THEN 
		select	cod_deuda
    INTO    vl_cod_deuda
		from	  analitico_comprobante 
		where	  cod_comprobante = ve_cod_comprobante
    AND     ROWNUM = 1;
		
		select	cod_deudor
    INTO    vl_cod_deudor
		from	  deuda
		where	  cod_deuda = vl_cod_deuda;
		
		select	obs 
    INTO    vl_observacion
		from	  condonacion
		where   cod_deudor = vl_cod_deudor;

	ELSIF(K_ORIGEN_C_PRORROGA = ve_cod_origen_comprobante)THEN 
		select	obs 
    INTO    vl_observacion
		from	  prorroga 
		where	  cod_comprobante  = ve_cod_comprobante;

	ELSIF(K_ORIGEN_C_NOVACION = ve_cod_origen_comprobante)THEN 
		select	obs 
    INTO    vl_observacion
		from	  novacion
		where	  cod_comprobante = ve_cod_comprobante;
	END IF;

	RETURN vl_observacion;
  END;
END;
/