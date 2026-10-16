CREATE OR REPLACE PROCEDURE spi_deudas_canceladas (
   VE_COD_TIPO_DEUDA   VARCHAR
 , VE_FECHA_INICIO     DATE
 , VE_FECHA_TERMINO    DATE
)
AS
BEGIN
   DECLARE
      KL_COD_DEUDA_CANCELADA                  NUMBER := 4;
      KL_COD_ESTADO_PAGO_VIGENTE              NUMBER := 1;
      VL_COD_DEUDA                            NUMBER;
      VL_RUT                                  VARCHAR (20);
      VL_FECHA_PAGO                           DATE;
      VL_MONTO_ABONO                          NUMBER;
      VL_MONTO_DESCUENTO                      NUMBER;
   BEGIN
      DECLARE
         CURSOR C_DEUDAS
         IS
            SELECT COD_DEUDA
                 , REPLACE (TO_CHAR (DE.RUT, '99,999,999')
                          , ','
                          , '.'
                           ) || '-' || DE.DIG_VERIF RUT
                 , NVL (F_DEUDA_LAST_FECHA_ABONO (COD_DEUDA), TO_DATE ('01/01/1900', 'dd/mm/yyyy')) FECHA_ABONO
                 , NVL (F_DEUDA_LAST_FECHA_DESCUENTO (COD_DEUDA), TO_DATE ('01/01/1900', 'dd/mm/yyyy')) FECHA_DESCUENTO
              FROM DEUDA D
                 , DEUDOR DE
             WHERE COD_ESTADO_DEUDA = KL_COD_DEUDA_CANCELADA
               AND DE.COD_DEUDOR = D.COD_DEUDOR
               AND D.COD_TIPO_DEUDA LIKE VE_COD_TIPO_DEUDA;
      BEGIN
         FOR A IN C_DEUDAS
         LOOP
            VL_FECHA_PAGO                        := A.FECHA_ABONO;

            IF (VL_FECHA_PAGO < A.FECHA_DESCUENTO)
            THEN
               VL_FECHA_PAGO                        := A.FECHA_DESCUENTO;
            END IF;

            IF (    (VE_FECHA_INICIO < VL_FECHA_PAGO)
                AND (VL_FECHA_PAGO < VE_FECHA_TERMINO))
            THEN
               SELECT NVL (SUM (MONTO_MONEDA), 0)
                 INTO VL_MONTO_ABONO
                 FROM ABONO
                WHERE COD_DEUDA = A.COD_DEUDA
                  AND COD_ESTADO_PAGO = KL_COD_ESTADO_PAGO_VIGENTE;

               SELECT NVL (SUM (MONTO_MONEDA), 0)
                 INTO VL_MONTO_DESCUENTO
                 FROM DESCUENTO
                WHERE COD_DEUDA = A.COD_DEUDA
                  AND COD_ESTADO_PAGO = KL_COD_ESTADO_PAGO_VIGENTE;

               INSERT INTO TEMP_DEUDAS_CANCELADAS
                           (RUT
                          , COD_DEUDA
                          , MONTO_ABONO
                          , MONTO_DESCTO
                          , MONTO_TOTAL
                           )
                    VALUES (A.RUT
                          , A.COD_DEUDA
                          , VL_MONTO_ABONO
                          , VL_MONTO_DESCUENTO
                          , VL_MONTO_ABONO + VL_MONTO_DESCUENTO
                           );
            END IF;
         END LOOP;
      END;
   END;
END SPI_DEUDAS_CANCELADAS;
/