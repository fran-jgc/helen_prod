CREATE OR REPLACE PROCEDURE spu_sub_boleta (
   ve_operacion             IN   VARCHAR
 , ve_cod_sub_boleta        IN   NUMBER
 , ve_cod_boleta            IN   NUMBER
 , ve_nro_sub_boleta        IN   NUMBER
 , ve_fecha_vencto          IN   DATE
 , ve_monto_capital         IN   NUMBER
 , ve_monto_interes         IN   NUMBER
 , ve_monto_multa           IN   NUMBER
 , ve_fecha_pago            IN   DATE
 , ve_cod_componente_deuda  IN   NUMBER
)
AS
/******************************************************************************
   NAME:       spu_sub_boleta

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        06/01/2010   Sergio Castro   Creacion del Procedimiento




******************************************************************************/
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO sub_boleta
                  (cod_sub_boleta
                 , cod_boleta
                 , nro_sub_boleta
                 , fecha_vencto
                 , monto_capital
                 , monto_interes
                 , monto_multa
                 , fecha_pago
                 , cod_componente_deuda
                  )
           VALUES (sub_boleta_sq.NEXTVAL
                 , ve_cod_boleta
                 , ve_nro_sub_boleta
                 , ve_fecha_vencto
                 , ve_monto_capital
                 , ve_monto_interes
                 , ve_monto_multa
                 , ve_fecha_pago
                 , ve_cod_componente_deuda
                  );
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE sub_boleta
         SET nro_sub_boleta = ve_nro_sub_boleta
           , fecha_vencto = ve_fecha_vencto
           , monto_capital = ve_monto_capital
           , monto_interes = ve_monto_interes
           , monto_multa = ve_monto_multa
           , fecha_pago = ve_fecha_pago
           , cod_componente_deuda = ve_cod_componente_deuda
       WHERE cod_sub_boleta = ve_cod_sub_boleta;
   END IF;
END;
/