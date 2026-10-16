PROMPT CREATE OR REPLACE FUNCTION f_doc_fecha_deposito
CREATE OR REPLACE FUNCTION f_doc_fecha_deposito (ve_cod_documento_pago  NUMERIC)
   RETURN DATE
AS
BEGIN
    DECLARE
          K_DEPOSITO	NUMBER:=3;
			    K_TRANSBANK	NUMBER:=6;
			    K_AJUSTE_SALDO	NUMBER:=99;
          KL_DEP_CONFIRMADO	NUMBER:=2;
			    vl_cod_tipo_documento_pago NUMBER;
			    vl_fecha_deposito	DATE;
      BEGIN

        select cod_tipo_documento_pago
        INTO vl_cod_tipo_documento_pago 
		    from documento_pago
		    where cod_documento_pago = ve_cod_documento_pago;
		
		    if (vl_cod_tipo_documento_pago = K_DEPOSITO or vl_cod_tipo_documento_pago = K_TRANSBANK or vl_cod_tipo_documento_pago = K_AJUSTE_SALDO) THEN
			    select fecha_documento_pago
          INTO vl_fecha_deposito 
			    from DOCUMENTO_PAGO
			    where cod_documento_pago = ve_cod_documento_pago;
  		  else
			    select fecha_deposito
          INTO vl_fecha_deposito 
			    from deposito d, deposito_documento dp
			    where d.cod_deposito = dp.cod_deposito
				    and dp.cod_documento_pago = ve_cod_documento_pago
				    and d.cod_estado_deposito = KL_DEP_CONFIRMADO;
		    END IF;

		    return vl_fecha_deposito;

      END;
END;