----------------------- f_get_nro_convenio_boleta ---------------------
CREATE OR REPLACE  FUNCTION f_get_nro_convenio_boleta(ve_cod_boleta NUMBER)
	RETURN NUMBER
AS
-- retorna  cod_convenio de la tabla CONVENIO_BDD para imprimier en boleta
BEGIN
	DECLARE
		vl_cod_tipo_deuda		VARCHAR2(20);
		vl_count_boleta_futura	NUMBER;
		vl_con_declaracion		VARCHAR2(2);
		vl_convenio				NUMBER;
		vl_cod_deuda			NUMBER;
		vl_cod_deudor			NUMBER;
		vl_cod_plan_pago		NUMBER;
		vl_ano_plan_pago		NUMBER;
		vl_count_convenio		NUMBER;

BEGIN
	--saber si la deuda es futura o estado no exigible
	SELECT	 de.cod_tipo_deuda
			,de.cod_deuda
			,de.cod_deudor
			,Nvl(b.cod_plan_pago, 0)
	INTO	 vl_cod_tipo_deuda
			,vl_cod_deuda
			,vl_cod_deudor
			,vl_cod_plan_pago
	FROM	boleta b, deuda de
	WHERE	b.cod_boleta = ve_cod_boleta 
	  AND	b.cod_deuda = de.cod_deuda; 
  
	--boleta no tiene plan_pago es "no_exigible"
	/*SELECT Nvl(cod_plan_pago, 0)
	INTO	vl_cod_plan_pago
	FROM boleta 
	WHERE cod_boleta = ve_cod_boleta;*/ 

	IF (vl_cod_plan_pago = 0) THEN 
		vl_convenio := f_get_td_convenio(vl_cod_tipo_deuda, 9998); --CONVENIO_NO_EXIGIBLE
		RETURN NVL(vl_convenio, 0);
	END IF;
	
	--cod_convenio = 9999 = futuro
	SELECT	count(*) 
	INTO	vl_count_boleta_futura
	FROM	plan_pago
	WHERE	cod_plan_pago = vl_cod_plan_pago
	  AND	ano_cobro = 9999; --futuro

	IF (vl_count_boleta_futura > 0) THEN 
		vl_convenio := f_get_td_convenio(vl_cod_tipo_deuda, 9999); --CONVENIO_FUTURA
		RETURN NVL(vl_convenio, 0);
	END IF;
	
	--obtiene datos para buscar en CONVENIO BANCO DEL DESARROLLO
	--Archivo->Parámetros->Tipo Deuda->'CS'
	SELECT	ano_cobro
	INTO	vl_ano_plan_pago
	FROM	plan_pago 
	WHERE	cod_plan_pago = vl_cod_plan_pago;
	
	SELECT	case count(*)
			  when 0 then 'N'
			  else 'S'
			end CON_DECLARACION
	INTO	vl_con_declaracion
	FROM	declaracion
	WHERE	cod_deudor = vl_cod_deudor
	  AND	ano = vl_ano_plan_pago - 1;

	SELECT	count(*)
	INTO	vl_count_convenio
	FROM	convenio_bdd
	WHERE	cod_tipo_deuda =  vl_cod_tipo_deuda
	  AND	ano = vl_ano_plan_pago
	  AND	con_declaracion = vl_con_declaracion;

	IF (vl_count_convenio = 0) THEN 
		--no existe datos para convenio
		RETURN 0;
	END IF;
	
	SELECT	convenio
	INTO	vl_convenio
	FROM	convenio_bdd
	WHERE	cod_tipo_deuda =  vl_cod_tipo_deuda
	  AND	ano = vl_ano_plan_pago
	  AND	con_declaracion = vl_con_declaracion;

	RETURN NVL(vl_convenio, 0);
	END;
END;