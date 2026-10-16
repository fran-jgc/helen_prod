PROMPT CREATE OR REPLACE FUNCTION f_ingreso_CR_tipo
CREATE OR REPLACE FUNCTION f_ingreso_CR_tipo(ve_cod_ingreso number)
RETURN VARCHAR2
AS
BEGIN                                
  DECLARE vl_tipo_PIC NUMBER;
			  vl_tipo_PCC NUMBER;
			  vl_tipo_PIR NUMBER;
			  vl_tipo	VARCHAR2(20);

  BEGIN

			
	select COUNT(*)
  INTO vl_tipo_PIC
	from PAGO_INICIAL_CONVENIO
	where COD_INGRESO = ve_cod_ingreso;

	select COUNT(*)
  INTO vl_tipo_PCC 
	from PAGO_CUOTA_CONVENIO
	where COD_INGRESO = ve_cod_ingreso;
	
	select COUNT(*)
  INTO vl_tipo_PIR
	from PAGO_INICIAL_RENEGOCIACION
	where COD_INGRESO = ve_cod_ingreso; 

	
	IF (vl_tipo_PIC > 0 OR vl_tipo_PCC > 0) THEN
		vl_tipo := 'CONVENIO';
	ELSIF(vl_tipo_PIR > 0) THEN
		vl_tipo := 'RENEGOCIACION';
	ELSE
		vl_tipo := '';
  END IF;
	
	return vl_tipo;		
		
  END;
END;
