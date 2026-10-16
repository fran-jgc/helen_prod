CREATE OR REPLACE PROCEDURE spu_concepto_cobro (
   ve_operacion                IN       VARCHAR
 , ve_cod_concepto_cobro       IN       NUMBER
 , ve_nom_concepto_cobro       IN       VARCHAR := NULL
)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO concepto_cobro
                  (cod_concepto_cobro
                  ,nom_concepto_cobro
                  )
           VALUES (concepto_cobro_sq.NEXTVAL
                  ,ve_nom_concepto_cobro
                  );
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE concepto_cobro
         SET nom_concepto_cobro = ve_nom_concepto_cobro
       WHERE cod_concepto_cobro = ve_cod_concepto_cobro;
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM concepto_cobro
            WHERE cod_concepto_cobro = ve_cod_concepto_cobro;
   END IF;
END;
/