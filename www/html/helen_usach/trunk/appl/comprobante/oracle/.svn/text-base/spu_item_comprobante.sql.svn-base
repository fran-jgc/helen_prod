CREATE OR REPLACE PROCEDURE spu_item_comprobante (
   ve_operacion               IN       VARCHAR2
 , ve_cod_item_comprobante    IN       NUMBER
 , ve_cod_comprobante         IN       NUMBER   := NULL
 , ve_cod_cuenta_contable     IN       NUMBER   := NULL
 , ve_monto_debe              IN       NUMBER   := NULL
 , ve_monto_haber             IN       NUMBER   := NULL
)
AS
BEGIN
  DECLARE
    vl_monto_debe   NUMBER;
    vl_monto_haber  NUMBER;

  BEGIN
   vl_monto_debe := ve_monto_debe;
   vl_monto_haber := ve_monto_haber;
   IF (vl_monto_debe < 0) THEN
    vl_monto_haber := vl_monto_haber - vl_monto_debe;
    vl_monto_debe := 0;
   END IF;
   IF (vl_monto_haber < 0) THEN
    vl_monto_debe := vl_monto_debe - vl_monto_haber;
    vl_monto_haber := 0;
   END IF;

   IF (ve_operacion = 'INSERT') THEN

      IF (vl_monto_debe + vl_monto_haber = 0) THEN
        RETURN;
      END IF;

      INSERT INTO item_comprobante
            (cod_item_comprobante
            , cod_comprobante
            , cod_cuenta_contable
            , monto_debe
            , monto_haber
            )
      VALUES (item_comprobante_sq.NEXTVAL
            , ve_cod_comprobante
            , ve_cod_cuenta_contable
            , vl_monto_debe
            , vl_monto_haber
            );
   ELSIF (ve_operacion = 'UPDATE') THEN
      UPDATE item_comprobante
         SET cod_cuenta_contable = ve_cod_cuenta_contable
           , monto_debe = vl_monto_debe
           , monto_haber = vl_monto_haber
       WHERE cod_item_comprobante = ve_cod_item_comprobante;
   ELSIF (ve_operacion = 'DELETE') THEN
      DELETE      item_comprobante
            WHERE cod_item_comprobante = ve_cod_item_comprobante;
   END IF;
   END;
END;
/