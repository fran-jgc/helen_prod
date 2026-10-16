PROMPT CREATE OR REPLACE PROCEDURE spu_familia_deuda
CREATE OR REPLACE PROCEDURE spu_familia_deuda (
   ve_operacion               IN       VARCHAR
 , ve_cod_familia_deuda       IN       NUMERIC := NULL
 , ve_nom_familia_deuda       IN       VARCHAR := NULL
 , ve_cod_cuenta_ch_fecha     IN       NUMERIC := NULL
 , ve_cod_cuenta_protesto     IN       NUMERIC := NULL
 , ve_cod_cuenta_cobranza     IN       NUMERIC := NULL
)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO familia_deuda
                  (cod_familia_deuda
                 , nom_familia_deuda
                 , cod_cuenta_ch_fecha
                 , cod_cuenta_protesto
                 , cod_cuenta_cobranza)
           VALUES (familia_deuda_sq.NEXTVAL
                 , ve_nom_familia_deuda
                 , ve_cod_cuenta_ch_fecha
                 , ve_cod_cuenta_protesto
                 , ve_cod_cuenta_cobranza);
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE familia_deuda
         SET nom_familia_deuda = ve_nom_familia_deuda
             ,cod_cuenta_protesto = ve_cod_cuenta_protesto
             ,cod_cuenta_ch_fecha = ve_cod_cuenta_ch_fecha
             ,cod_cuenta_cobranza = ve_cod_cuenta_cobranza
       WHERE cod_familia_deuda = ve_cod_familia_deuda;
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM familia_deuda
            WHERE cod_familia_deuda = ve_cod_familia_deuda;
   END IF;
END;
/



