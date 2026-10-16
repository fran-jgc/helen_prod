CREATE OR REPLACE PROCEDURE spu_analitico (
   ve_operacion                   IN   VARCHAR2,
   ve_cod_analitico_comprobante   IN   NUMBER,
   ve_cod_comprobante             IN   NUMBER := NULL,
   ve_cod_deuda                   IN   NUMBER := NULL,
   ve_cod_cuenta_contable         IN   NUMBER := NULL,
   ve_monto_debe                  IN   NUMBER := NULL,
   ve_monto_haber                 IN   NUMBER := NULL
)
AS
/******************************************************************************
   NAME:       spu_analitico
   PURPOSE:    crea en la tabla analitico comprobante los registros necesarios,
                viene ligado a comprobante
******************************************************************************/
BEGIN
  DECLARE
    vl_monto_debe   NUMBER;
    vl_monto_haber  NUMBER;
    vl_cod_deudor	NUMBER;

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
      
    IF (ve_cod_deuda is not null)THEN
		select cod_deudor 
		into vl_cod_deudor
		from deuda 
		where cod_deuda = ve_cod_deuda;
	ELSE
		vl_cod_deudor := null;
	END IF;
	
      IF (ve_operacion = 'INSERT') THEN
        IF (vl_monto_debe + vl_monto_haber = 0) THEN
          RETURN;
        END IF;

         INSERT INTO analitico_comprobante
                     (cod_analitico_comprobante, cod_comprobante,
                      cod_deuda, cod_cuenta_contable, monto_debe,
                      monto_haber, cod_deudor)
              VALUES (analitico_comprobante_sq.NEXTVAL, ve_cod_comprobante,
                      ve_cod_deuda, ve_cod_cuenta_contable, vl_monto_debe,
                      vl_monto_haber, vl_cod_deudor);
      END IF;
   END;
END;
/
