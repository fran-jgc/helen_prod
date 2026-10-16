CREATE OR REPLACE function f_valida_moneda
return VARCHAR2
is
BEGIN
	DECLARE
    		vl_current_date DATE := to_char(sysdate, 'dd/mm/yyyy');
    		ve_cod_moneda NUMBER;
    		vl_string  VARCHAR2(1000);
    		vl_count NUMBER;
    		ve_simbolo VARCHAR2(10);
    		vl_tiene_moneda NUMBER := 0;
	

	BEGIN
  	select	count(*) INTO vl_count 
	from	VALOR_MONEDA where cod_moneda <> 1
	AND		To_Char(FECHA_VALOR_MONEDA,'dd/mm/yyyy') = vl_current_date;
	
		IF(vl_count > 0) then
			vl_string := '0';
		
		ELSE
		  	vl_string := 'Señor Usuario: El valor moneda del presente mes, no esta ingresado.\nIngrese valor moneda por favor para: ';
		END IF;	
	
	DECLARE CURSOR C_MONEDA  is
	select cod_moneda, simbolo from moneda where cod_moneda <> 1;

	BEGIN
	open C_MONEDA; 
    LOOP

	FETCH C_MONEDA INTO ve_cod_moneda,ve_simbolo;
 	EXIT WHEN C_MONEDA%NOTFOUND;
	
	SELECT	count(*) INTO vl_count
	from	VALOR_MONEDA 
	where	COD_MONEDA = ve_cod_moneda 
	AND		To_Char(FECHA_VALOR_MONEDA,'dd/mm/yyyy')  = vl_current_date;
	
	END LOOP;
	
	CLOSE C_MONEDA;
	END;
	
	    if(vl_count = 0)THEN
        vl_tiene_moneda := 1;
		    vl_string := vl_string || ve_simbolo ||', ';
	    END IF;	

  
 
		if(vl_tiene_moneda = 1)THEN
			vl_string := vl_string || SUBSTR(', ',0,0);
		END IF;

	return 	vl_string;
	
  END;
END;
