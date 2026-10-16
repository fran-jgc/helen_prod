CREATE OR REPLACE PROCEDURE sp_condonacion_deuda (
   ve_operacion               IN       VARCHAR2
 , ve_cod_deuda_condonacion   IN       NUMBER
 , ve_cod_condonacion         IN       NUMBER:=NULL
 , ve_cod_deuda               IN       NUMBER:=NULL
 , ve_monto_svp               IN       NUMBER:=NULL
 , ve_monto_mora              IN       NUMBER:=NULL
 , ve_monto_cp                IN       NUMBER:=NULL
 , ve_monto_lp                IN       NUMBER:=NULL
 , ve_confirma                IN       VARCHAR2 :=NULL
 , ve_cod_usuario_confirma    IN       NUMBER:=NULL
)
AS
BEGIN
   DECLARE
      vl_cod_comprobante      NUMBER;
   BEGIN
      IF (ve_operacion = 'INSERT') THEN
         INSERT INTO deuda_condonacion
                     (cod_deuda_condonacion
                    , cod_condonacion
                    , cod_deuda
                    , monto_svp
                    , monto_mora
                    , monto_cp
                    , monto_lp
                     )
              VALUES (deuda_condonacion_sq.NEXTVAL
                    , ve_cod_condonacion
                    , ve_cod_deuda
                    , ve_monto_svp
                    , ve_monto_mora
                    , ve_monto_cp
                    , ve_monto_lp
                     );
      ELSIF (ve_operacion = 'UPDATE') THEN
        UPDATE deuda_condonacion
        SET cod_condonacion = ve_cod_condonacion
            ,cod_deuda = ve_cod_deuda
            ,monto_svp = ve_monto_svp
            ,monto_mora = ve_monto_mora
            ,monto_cp = ve_monto_cp
            ,monto_lp = ve_monto_lp
        WHERE cod_deuda_condonacion = ve_cod_deuda_condonacion;

        IF (ve_confirma = 'S') THEN
          sp_condonacion(ve_cod_deuda_condonacion, ve_cod_usuario_confirma, vl_cod_comprobante);

          UPDATE deuda_condonacion
		   	  SET cod_comprobante = vl_cod_comprobante
          WHERE cod_deuda_condonacion = ve_cod_deuda_condonacion;
        END IF;

      ELSIF (ve_operacion = 'DELETE') THEN
        DELETE deuda_condonacion
        WHERE cod_deuda_condonacion = ve_cod_deuda_condonacion;
      END IF;
   END;
END;
/