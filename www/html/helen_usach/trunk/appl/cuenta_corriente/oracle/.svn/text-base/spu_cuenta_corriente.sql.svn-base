CREATE OR REPLACE PROCEDURE spu_cuenta_corriente (
   ve_operacion                  IN       VARCHAR2
 , ve_cod_cuenta_corriente       IN       NUMBER
 , ve_nom_cuenta_corriente       IN       VARCHAR2 := NULL
 , ve_cod_familia_deuda          IN       NUMBER   := NULL
 , ve_cod_cuenta_contable        IN       NUMBER   := NULL
 , ve_cod_banco                  IN       NUMBER   := NULL
 , ve_nro_cuenta_corriente       IN       VARCHAR2 := NULL
)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO cuenta_corriente
                  (cod_cuenta_corriente
                  ,nom_cuenta_corriente
                  ,cod_familia_deuda
                  ,cod_cuenta_contable
                  ,cod_banco
                  ,nro_cuenta_corriente
                  )
           VALUES (cuenta_corriente_sq.NEXTVAL
                  ,ve_nom_cuenta_corriente
                  ,ve_cod_familia_deuda
                  ,ve_cod_cuenta_contable
                  ,ve_cod_banco
                  ,ve_nro_cuenta_corriente
                  );
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE cuenta_corriente
         SET nom_cuenta_corriente = ve_nom_cuenta_corriente
            ,cod_familia_deuda = ve_cod_familia_deuda
            ,cod_cuenta_contable = ve_cod_cuenta_contable
            ,cod_banco = ve_cod_banco
            ,nro_cuenta_corriente = ve_nro_cuenta_corriente
       WHERE cod_cuenta_corriente = ve_cod_cuenta_corriente;
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM cuenta_corriente
            WHERE cod_cuenta_corriente = ve_cod_cuenta_corriente;
   END IF;
END;
/