CREATE OR REPLACE PROCEDURE spu_tipo_bitacora (
   ve_operacion             IN       VARCHAR
 , ve_cod_tipo_bitacora     IN       NUMBER
 , ve_nom_tipo_bitacora     IN       VARCHAR := NULL
 , ve_permite_manual		    IN		   VARCHAR:=NULL
)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO tipo_bitacora
                  (cod_tipo_bitacora
                  ,nom_tipo_bitacora
                  ,permite_manual
                  )
           VALUES (tipo_bitacora_sq.NEXTVAL
                  ,ve_nom_tipo_bitacora
                  ,ve_permite_manual
                  );
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE tipo_bitacora
         SET nom_tipo_bitacora = ve_nom_tipo_bitacora
            ,permite_manual = ve_permite_manual
       WHERE cod_tipo_bitacora = ve_cod_tipo_bitacora;
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM tipo_bitacora
            WHERE cod_tipo_bitacora = ve_cod_tipo_bitacora;
   END IF;
END;
/