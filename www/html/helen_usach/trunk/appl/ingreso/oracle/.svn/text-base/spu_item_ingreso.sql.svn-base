CREATE OR REPLACE PROCEDURE spu_item_ingreso(
   ve_operacion                 IN   VARCHAR2
 , ve_cod_item_ingreso          IN   NUMBER	  :=NULL
 , ve_seleccion                 IN   VARCHAR2 :=NULL
 , ve_cod_ingreso               IN   NUMBER   :=NULL
 , ve_cod_deuda                 IN   NUMBER   :=NULL
 , ve_cod_plan_pago             IN   NUMBER   :=NULL
 , ve_saldo_cuota               IN   NUMBER   :=NULL
 , ve_saldo_multa               IN   NUMBER   :=NULL
 , ve_cod_tipo_descuento        IN   NUMBER   :=NULL
 , ve_monto_descuento           IN   NUMBER   :=NULL
 , ve_saldo_total               IN   NUMBER   :=NULL
 , ve_saldo_total_pesos         IN   NUMBER   :=NULL
 , ve_monto_pago                IN   NUMBER   :=NULL
 , ve_monto_pago_pesos          IN   NUMBER   :=NULL
 , ve_cod_sub_boleta			IN   NUMBER   :=NULL
 , ve_porc_descuento			IN   NUMBER   :=NULL
)
AS
BEGIN
   BEGIN
      IF (ve_operacion = 'INSERT') THEN
        IF (ve_seleccion = 'S') THEN
          INSERT INTO item_ingreso
                    (cod_item_ingreso
                    , cod_ingreso
                    , cod_deuda
                    , cod_plan_pago
                    , saldo_cuota
                    , saldo_multa
                    , cod_tipo_descuento
                    , monto_descuento
                    , saldo_total
                    , saldo_total_pesos
                    , monto_pago
                    , monto_pago_pesos
                    , cod_sub_boleta
                    , porc_descuento)
                VALUES (item_ingreso_sq.NEXTVAL
                    , ve_cod_ingreso
                    , ve_cod_deuda
                    , ve_cod_plan_pago
                    , ve_saldo_cuota
                    , ve_saldo_multa
                    , ve_cod_tipo_descuento
                    , ve_monto_descuento
                    , ve_saldo_total
                    , ve_saldo_total_pesos
                    , ve_monto_pago
                    , ve_monto_pago_pesos
                    , ve_cod_sub_boleta
                    , ve_porc_descuento);
        END IF;
      END IF;
   END;
END spu_item_ingreso;
/