PROMPT CREATE OR REPLACE PROCEDURE spu_prorroga_docpago
CREATE OR REPLACE PROCEDURE spu_prorroga_docpago (
   ve_operacion                     IN   VARCHAR
 , ve_cod_prorroga_documento_pago   IN   NUMERIC := NULL
 , ve_cod_usuario                   IN   NUMERIC := NULL
 , ve_cod_documento_pago            IN   NUMERIC := NULL
 , ve_num_prorroga_documento_pago   IN   NUMERIC := NULL
 , ve_fecha_ant                     IN   VARCHAR := NULL
 , ve_fecha_nueva                   IN   VARCHAR := NULL
 , ve_cod_deudor                    IN   VARCHAR := NULL
)
AS
BEGIN
  DECLARE 	K_PRORROGA_DOCUMENTO  NUMBER :=13;
            vl_cod_deudor         NUMBER;

  BEGIN
    IF (ve_operacion = 'INSERT') THEN
      INSERT INTO prorroga_documento_pago
                  (cod_prorroga_documento_pago
                 , fecha_registro
                 , cod_usuario
                 , cod_documento_pago
                 , nro_prorroga_documento_pago
                 , fecha_anterior
                 , fecha_nueva
                  )
           VALUES (prorroga_documento_pago_sq.NEXTVAL
                 , TRUNC (SYSDATE)
                 , ve_cod_usuario
                 , ve_cod_documento_pago
                 , ve_num_prorroga_documento_pago
                 , TO_DATE (ve_fecha_ant, 'dd/mm/yyyy')
                 , TO_DATE (ve_fecha_nueva, 'dd/mm/yyyy')
                  );
      UPDATE documento_pago
      SET    fecha_documento_pago = TO_DATE (ve_fecha_nueva, 'dd/mm/yyyy')
      WHERE  cod_documento_pago = ve_cod_documento_pago;

    
      spu_bitacora_deudor ('Prorroga de Documento Nº: ' || To_Char(ve_cod_documento_pago), K_PRORROGA_DOCUMENTO, ve_cod_deudor, ve_cod_usuario);
    END IF;
  END;
END;
/

