PROMPT CREATE OR REPLACE PROCEDURE spu_plan_pago_convenio
CREATE OR REPLACE PROCEDURE spu_plan_pago_convenio (ve_operacion	        VARCHAR2
											                  ,ve_cod_convenio	      NUMBER := NULL
											                  ,ve_cod_plan_pago		    NUMBER := NULL
											                  ,ve_saldo_cuota		      NUMBER := NULL
											                  ,ve_saldo_multa		      NUMBER := NULL
											                  ,ve_total_saldo_moneda	NUMBER := NULL
											                  ,ve_total_saldo_pesos	  NUMBER := NULL
                                        )
AS
BEGIN
	IF(ve_operacion = 'INSERT') THEN
		INSERT INTO PLAN_PAGO_CONVENIO
		    (COD_PLAN_PAGO_CONVENIO
		      ,COD_CONVENIO
			  ,COD_PLAN_PAGO
			  ,SALDO_CUOTA
			  ,SALDO_MULTA
			  ,TOTAL_SALDO_MONEDA
			  ,TOTAL_SALDO_PESOS
			  )
		VALUES
			(plan_pago_convenio_sq.NEXTVAL
			,ve_cod_convenio
			,ve_cod_plan_pago
			,ve_saldo_cuota
			,ve_saldo_multa
			,ve_total_saldo_moneda
			,ve_total_saldo_pesos
			);
  END IF;
END;
/

