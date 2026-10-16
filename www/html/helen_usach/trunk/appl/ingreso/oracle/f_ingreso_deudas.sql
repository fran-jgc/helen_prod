CREATE OR REPLACE function f_ingreso_deudas(ve_cod_ingreso   NUMBER)
RETURN VARCHAR2
AS
BEGIN
	DECLARE
		vs_deudas			VARCHAR2(100):= '';
	    
	CURSOR c_item IS
			select	i.cod_deuda
					,d.cod_tipo_deuda
			from item_ingreso i, deuda d
			where i.cod_ingreso = ve_cod_ingreso
			and d.cod_deuda = i.cod_deuda;
			
	BEGIN
		FOR c IN c_item LOOP
			vs_deudas := vs_deudas || c.cod_tipo_deuda || ' ' || TO_CHAR(c.cod_deuda) || ' - ';
		END LOOP;
		
		if (vs_deudas <> '') then
			vs_deudas := SubStr(vs_deudas, 1, Length(vs_deudas) - 3);
		end if;
		
		RETURN vs_deudas;
	END;
END;
/