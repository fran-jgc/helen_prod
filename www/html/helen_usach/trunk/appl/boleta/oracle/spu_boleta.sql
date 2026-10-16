CREATE OR REPLACE PROCEDURE spu_boleta	 (ve_operacion				VARCHAR2
										 ,ve_cod_boleta           NUMBER
										 ,ve_cod_usuario          NUMBER
										 ,ve_cod_deuda            NUMBER
										 ,ve_cod_plan_pago        NUMBER
										 ,ve_monto_capital        NUMBER
										 ,ve_monto_interes        NUMBER
										 ,ve_monto_multa          NUMBER
										 ,ve_monto_moneda         NUMBER
										 ,ve_monto_pesos          NUMBER
										 ,ve_cod_tipo_descuento	NUMBER
										 ,ve_dscto_capital        NUMBER
										 ,ve_dscto_interes        NUMBER
										 ,ve_dscto_multa          NUMBER
										 ,ve_dscto_pesos          NUMBER
										 ,ve_fecha_primera_cuota  DATE
										 ,ve_cant                 NUMBER
										 ,ve_cartera_conocida		VARCHAR2)
AS
/******************************************************************************
   NAME:       spu_boleta

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        23/10/2009   Paula Cea       modifica boletas
   2.0        06/01/2010   Sergio Castro   Modificaciones Procedimiento



******************************************************************************/
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO boleta
                  (cod_boleta
                 , fecha_boleta
				 , cod_usuario
				 , cod_deuda
				 , cod_plan_pago
				 , monto_capital
				 , monto_interes
				 , monto_multa
				 , monto_moneda
				 , monto_pesos
				 , cod_tipo_descuento
				 , dscto_capital
				 , dscto_interes
				 , dscto_multa
				 , dscto_pesos
				 , fecha_primera_cuota
				 , cant
				 , cartera_conocida)
           VALUES (boleta_sq.NEXTVAL
                 , SYSDATE
                 , ve_cod_usuario
				 , ve_cod_deuda
				 , ve_cod_plan_pago
				 , ve_monto_capital
				 , ve_monto_interes
				 , ve_monto_multa
				 , ve_monto_moneda
				 , ve_monto_pesos
				 , ve_cod_tipo_descuento
				 , ve_dscto_capital
				 , ve_dscto_interes
				 , ve_dscto_multa
				 , ve_dscto_pesos
                 , ve_fecha_primera_cuota
                 , ve_cant
				 , ve_cartera_conocida);                  
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE boleta
      SET	 cod_plan_pago	= ve_cod_plan_pago
			,monto_capital	= ve_monto_capital
			,monto_interes	= ve_monto_interes
			,monto_multa	= ve_monto_multa
			,monto_moneda	= ve_monto_moneda
			,monto_pesos	= ve_monto_pesos
			,cod_tipo_descuento = ve_cod_tipo_descuento
			,dscto_capital	= ve_dscto_capital
			,dscto_interes	= ve_dscto_interes
			,dscto_multa	= ve_dscto_multa
			,dscto_pesos	= ve_dscto_pesos
			,fecha_primera_cuota = ve_fecha_primera_cuota
			,cant			= ve_cant
			,cartera_conocida = ve_cartera_conocida
      WHERE  cod_boleta = ve_cod_boleta;
   END IF;
END;
/