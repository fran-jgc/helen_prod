CREATE OR REPLACE PROCEDURE spu_tipo_descuento(
   ve_operacion               IN       VARCHAR
 , ve_cod_tipo_descuento      IN       NUMERIC
 , ve_nom_tipo_descuento      IN       VARCHAR := NULL
 , ve_porc_descuento          IN       NUMBER  := NULL
)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO tipo_descuento
                  (cod_tipo_descuento
                  ,nom_tipo_descuento
                  ,porc_descuento
                  )
           VALUES (ve_cod_tipo_descuento
                  ,ve_nom_tipo_descuento
                  ,ve_porc_descuento
                  );
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE tipo_descuento
         SET cod_tipo_descuento = ve_cod_tipo_descuento,
             nom_tipo_descuento = ve_nom_tipo_descuento,
             porc_descuento     = ve_porc_descuento
       WHERE cod_tipo_descuento = ve_cod_tipo_descuento;
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM tipo_descuento
            WHERE cod_tipo_descuento = ve_cod_tipo_descuento;
   END IF;
END;
/



