CREATE OR REPLACE PROCEDURE spu_bitacora_deudor (
   ve_nom                     IN       VARCHAR
 , ve_cod_tipo                IN       NUMERIC
 , ve_cod_deudor              IN       NUMERIC
 , ve_cod_usuario             IN       NUMERIC
)
AS
BEGIN
   INSERT INTO bitacora_deudor
               (cod_bitacora_deudor
              , nom_bitacora_deudor
              , fecha_bitacora_deudor
              , cod_tipo_bitacora
              , cod_deudor
              , cod_usuario
               )
        VALUES (bitacora_deudor_sq.NEXTVAL
              , ve_nom
              , sysdate
              , ve_cod_tipo
              , ve_cod_deudor
              , ve_cod_usuario
               );
END;
/