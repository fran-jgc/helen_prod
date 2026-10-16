PROMPT CREATE OR REPLACE PROCEDURE spu_cuenta_tipo_documento_pago
CREATE OR REPLACE PROCEDURE spu_cuenta_tipo_documento_pago (ve_operacion  IN  VARCHAR2
												,ve_cod_cta_tipo_documento_pago    IN NUMBER
												,ve_cod_familia_deuda                 IN NUMBER:=null
												,ve_cod_cuenta_contable		            IN NUMBER:=null
												,ve_cod_tipo_documento_pago	          IN NUMBER:=null
												,ve_usar_cuenta_ctacte			          IN VARCHAR2:=null)

AS
BEGIN
	if (ve_operacion='INSERT')then
			insert into cuenta_tipo_documento_pago
           (cod_cuenta_tipo_documento_pago
           ,cod_familia_deuda
           ,cod_cuenta_contable
           ,cod_tipo_documento_pago
           ,usar_cuenta_ctacte)
			values
           (cuenta_tipo_documento_pago_sq.nextval
           ,ve_cod_familia_deuda
           ,ve_cod_cuenta_contable
           ,ve_cod_tipo_documento_pago
           ,ve_usar_cuenta_ctacte);
	elsif(ve_operacion='UPDATE')then
		update cuenta_tipo_documento_pago
		set cod_cuenta_contable			= ve_cod_cuenta_contable
			,cod_tipo_documento_pago	= ve_cod_tipo_documento_pago
			,usar_cuenta_ctacte		= ve_usar_cuenta_ctacte
		where cod_cuenta_tipo_documento_pago = ve_cod_cta_tipo_documento_pago;
	elsif (ve_operacion='DELETE')then
		delete cuenta_tipo_documento_pago
    	where cod_cuenta_tipo_documento_pago = ve_cod_cta_tipo_documento_pago;

	END IF;
END;
/
