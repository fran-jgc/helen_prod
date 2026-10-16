PROMPT CREATE OR REPLACE FUNCTION f_ingreso_CR_codigo
CREATE OR REPLACE FUNCTION f_ingreso_CR_codigo(ve_cod_ingreso number)
RETURN number
AS
BEGIN                                
  DECLARE	vl_codigo_PIC NUMBER;
			  vl_codigo_PCC NUMBER;
			  vl_codigo_PIR NUMBER;
			  vl_codigo	NUMBER;

	BEGIN

    select Nvl(COD_CONVENIO, 0)
    INTO vl_codigo_PIC
    from PAGO_INICIAL_CONVENIO
    where COD_INGRESO = ve_cod_ingreso;

    select Nvl(COD_CONVENIO, 0)
    INTO vl_codigo_PCC
    from PAGO_CUOTA_CONVENIO PCC, CUOTA_CONVENIO CC
    where PCC.COD_INGRESO = ve_cod_ingreso
	    AND PCC.COD_CUOTA_CONVENIO = CC.COD_CUOTA_CONVENIO;
	
    select Nvl(COD_RENEGOCIACION, 0)
    INTO vl_codigo_PIR
    from PAGO_INICIAL_RENEGOCIACION
    where COD_INGRESO = ve_cod_ingreso;
                                      
    IF (vl_codigo_PIC > 0) THEN
	    vl_codigo := vl_codigo_PIC;
    ELSIF(vl_codigo_PCC > 0) THEN
	    vl_codigo := vl_codigo_PCC;
    ELSIF (vl_codigo_PIR > 0)THEN
	    vl_codigo := vl_codigo_PIR;
    END IF;

    return vl_codigo;

  END;
END;
     