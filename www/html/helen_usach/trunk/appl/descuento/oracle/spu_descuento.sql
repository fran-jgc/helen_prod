CREATE OR REPLACE PROCEDURE spu_descuento(ve_operacion              IN  VARCHAR2
                                          ,ve_cod_descuento         IN  NUMBER
                                          ,ve_fecha_descuento       IN  DATE
                                          ,ve_cod_usuario           IN  NUMBER
                                          ,ve_cod_componente_deuda  IN  NUMBER
                                          ,ve_monto_capital         IN  NUMBER
                                          ,ve_monto_interes         IN  NUMBER
                                          ,ve_monto_multa           IN  NUMBER
                                          ,ve_monto_moneda          IN  NUMBER
                                          ,ve_monto_pesos           IN  NUMBER
                                          ,ve_cod_plan_pago         IN  NUMBER
                                          ,ve_cod_deuda             IN  NUMBER
                                          ,ve_cod_comprobante       IN  NUMBER
                                          ,ve_cod_estado_pago       IN  NUMBER
                                          ,ve_cod_origen_pago       IN  NUMBER
                                          ,ve_cod_sub_boleta        IN  NUMBER DEFAULT NULL)
AS
/******************************************************************************
   NAME:       spu_descuento
   Crea los descuentos

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        16/02/2010   SC

   NOTAS:

******************************************************************************/
BEGIN
	DECLARE	vl_monto_capital		NUMBER;
			vl_monto_interes		NUMBER;
			vl_monto_multa			NUMBER;
			vl_excedente_moneda		NUMBER;
			vl_monto_pesos			NUMBER;
			
	BEGIN
		IF (ve_operacion = 'INSERT') THEN
			vl_monto_capital := ve_monto_capital;
			vl_monto_interes := ve_monto_interes;
			vl_monto_multa := ve_monto_multa;
			
			sp_ingreso_distribuye(ve_cod_plan_pago
								 ,ve_fecha_descuento
								 ,ve_cod_componente_deuda
								 ,vl_monto_capital
								 ,vl_monto_interes
								 ,vl_monto_multa
								 ,vl_excedente_moneda);
								 
			vl_monto_pesos := ve_monto_pesos; 
			IF (vl_excedente_moneda > 0) THEN
				--** FALTA manejo e excedente
				vl_excedente_moneda := vl_excedente_moneda;
				-- se deben corregir los @vl_monto_pesos
			END IF;
			
			INSERT INTO descuento (COD_DESCUENTO
		                          ,FECHA_REGISTRO
		                          ,FECHA_DESCUENTO
		                          ,COD_USUARIO
		                          ,COD_COMPONENTE_DEUDA
		                          ,MONTO_CAPITAL
		                          ,MONTO_INTERES
		                          ,MONTO_MULTA
		                          ,MONTO_MONEDA
		                          ,MONTO_PESOS
		                          ,COD_PLAN_PAGO
		                          ,COD_DEUDA
		                          ,COD_COMPROBANTE
		                          ,COD_ESTADO_PAGO
		                          ,COD_ORIGEN_PAGO
		                          ,COD_SUB_BOLETA)
						VALUES	(descuento_sq.NEXTVAL
		                          ,SYSDATE
		                          ,ve_fecha_descuento
		                          ,ve_cod_usuario
		                          ,ve_cod_componente_deuda
		                          ,vl_monto_capital
		                          ,vl_monto_interes
		                          ,vl_monto_multa
		                          ,vl_monto_capital + vl_monto_interes + vl_monto_multa
		                          ,vl_monto_pesos
		                          ,ve_cod_plan_pago
		                          ,ve_cod_deuda
		                          ,ve_cod_comprobante
		                          ,ve_cod_estado_pago
		                          ,ve_cod_origen_pago
		                          ,ve_cod_sub_boleta);

			sp_pp_actualiza_estado(ve_cod_plan_pago);
			sp_deuda_actualiza_estado(ve_cod_deuda);
		ELSIF (ve_operacion = 'UPDATE') THEN
			UPDATE  descuento
		    SET     fecha_descuento       = ve_fecha_descuento
		            ,cod_componente_deuda = ve_cod_componente_deuda
		            ,monto_capital        = ve_monto_capital
		            ,monto_interes        = ve_monto_interes
		            ,monto_multa          = ve_monto_multa
		            ,monto_moneda         = ve_monto_moneda
		            ,monto_pesos          = ve_monto_pesos
		            ,cod_plan_pago        = ve_cod_plan_pago
		            ,cod_deuda            = ve_cod_deuda
		            ,cod_comprobante      = ve_cod_comprobante
		            ,cod_estado_pago      = ve_cod_estado_pago
		            ,cod_origen_pago      = ve_cod_origen_pago
		            ,cod_sub_boleta       = ve_cod_sub_boleta
		    WHERE   cod_descuento         = ve_cod_descuento;
		    
		ELSIF (ve_operacion = 'DELETE') THEN
			DELETE FROM descuento
			WHERE  cod_descuento = ve_cod_descuento;
		END IF;
	END;
END;
/