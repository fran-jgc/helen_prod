PROMPT CREATE OR REPLACE FUNCTION f_ingreso_CR_cuotas
CREATE OR REPLACE FUNCTION f_ingreso_CR_cuotas(ve_cod_ingreso number)
RETURN VARCHAR2
AS
BEGIN                                
  DECLARE	vl_count_PIC NUMBER;
			  vl_count_PCC NUMBER;
			  vl_count_PIR NUMBER;
			  vl_cuotas	VARCHAR2(100);
			  vc_COD_CUOTA_CONVENIO NUMBER;
			  vl_NRO_CUOTA VARCHAR2(10);

  	CURSOR C_CONVENIO IS
		SELECT  COD_CUOTA_CONVENIO
		from PAGO_CUOTA_CONVENIO
		where COD_INGRESO = ve_cod_ingreso;
                                       

  BEGIN
  			
	select COUNT(*)
  INTO vl_count_PIC
	from PAGO_INICIAL_CONVENIO
	where COD_INGRESO = ve_cod_ingreso;

	select COUNT(*)
  INTO vl_count_PCC
	from PAGO_CUOTA_CONVENIO
	where COD_INGRESO = ve_cod_ingreso;
	
	select COUNT(*)
  INTO vl_count_PIR
	from PAGO_INICIAL_RENEGOCIACION
	where COD_INGRESO = ve_cod_ingreso;

	vl_cuotas := '';

	if (vl_count_PIC > 0) THEN
		vl_cuotas := '0-';
  END IF;
	
	if (vl_count_PCC > 0) THEN

    FOR a IN C_CONVENIO LOOP
      SELECT To_Char(NRO_CUOTA)
      INTO vl_NRO_CUOTA
			FROM  CUOTA_CONVENIO
			WHERE COD_CUOTA_CONVENIO = a.COD_CUOTA_CONVENIO;
			
			vl_cuotas := vl_cuotas || vl_NRO_CUOTA||'-';

		END LOOP;
	END IF;
	
	if (vl_cuotas <> '') THEN
		vl_cuotas := SubStr(vl_cuotas, 1, Length(vl_cuotas) - 1);
  END IF;
  	
	if (vl_count_PIR > 0) THEN
		vl_cuotas := '0';
	END IF;
  					
	return vl_cuotas;		

  END;
END;
