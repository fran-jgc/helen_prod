CREATE OR REPLACE FUNCTION f_ingreso_get_cuenta(ve_cod_documento_pago	NUMBER)
RETURN NUMBER 
AS
BEGIN
	DECLARE	
        vs_cod_cuenta_ingreso			    NUMBER;
		    vl_cod_familia_deuda			NUMBER;
		    vl_cod_tipo_documento_pago	    NUMBER;
		    vl_usar_cuenta_ctacte			VARCHAR2(1);
		    vl_cod_banco					NUMBER;

  BEGIN
    SELECT  cod_familia_deuda
			,cod_tipo_documento_pago
			,cod_banco
    INTO    vl_cod_familia_deuda
            ,vl_cod_tipo_documento_pago
            ,vl_cod_banco
	FROM  	documento_pago
	WHERE 	cod_documento_pago = ve_cod_documento_pago;

	-- obtiene la cuenta de ingreso
	SELECT  cod_cuenta_contable
			,usar_cuenta_ctacte
    INTO    vs_cod_cuenta_ingreso
            ,vl_usar_cuenta_ctacte
	FROM    cuenta_tipo_documento_pago
  	WHERE   cod_familia_deuda = vl_cod_familia_deuda
    AND   	cod_tipo_documento_pago = vl_cod_tipo_documento_pago;

	IF(vl_usar_cuenta_ctacte = 'S')THEN  -- es deposito i similar
		SELECT  cod_cuenta_contable
      	INTO    vs_cod_cuenta_ingreso
		FROM    cuenta_corriente
		WHERE   cod_familia_deuda = vl_cod_familia_deuda
		AND   	cod_banco = vl_cod_banco;
    END IF;

	RETURN vs_cod_cuenta_ingreso;
  END;
END;
/