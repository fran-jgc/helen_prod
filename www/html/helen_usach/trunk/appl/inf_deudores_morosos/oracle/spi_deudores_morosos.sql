CREATE OR REPLACE PROCEDURE spi_deudores_morosos (
   VE_COD_TIPO_DEUDA   VARCHAR
 , VE_FECHA            DATE
)
AS
BEGIN
   DECLARE
      KL_COD_ESTADO_PP_MOROSO                 NUMBER := 4;
      K_EST_DEUDA_COBRANZA				number:=2;
   BEGIN
      DECLARE
         CURSOR C_DEUDORES_MOROSOS
         IS
            SELECT REPLACE (TO_CHAR (DE.RUT, '99,999,999')
                          , ','
                          , '.'
                           ) || '-' || DE.DIG_VERIF RUT
                 , NOMBRE
                 , APELLIDO_PATERNO
                 , APELLIDO_MATERNO
                 , Sum(F_PP_SALDO_MORA (COD_PLAN_PAGO, VE_FECHA)) MORA
                 , Sum(F_PP_SALDO_MULTA (COD_PLAN_PAGO, VE_FECHA)) MULTA
                 , C.NOM_CARTERA
              FROM DEUDA D
                 , DEUDOR DE
                 , PLAN_PAGO PP
                 , CARTERA C
             WHERE PP.FECHA_VENCTO < VE_FECHA
			   and f_pp_saldo_mora(COD_PLAN_PAGO, VE_FECHA) > 0
               AND DE.COD_DEUDOR = D.COD_DEUDOR
               AND D.COD_DEUDA = PP.COD_DEUDA
               AND D.COD_TIPO_DEUDA LIKE VE_COD_TIPO_DEUDA
				       AND f_deuda_get_estado(D.COD_DEUDA, VE_FECHA) = K_EST_DEUDA_COBRANZA
               AND D.COD_CARTERA = C.COD_CARTERA
               GROUP BY DE.RUT, DE.DIG_VERIF, NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO, C.NOM_CARTERA;
      BEGIN
         FOR A IN C_DEUDORES_MOROSOS
         LOOP
            INSERT INTO TEMP_DEUDORES_MOROSOS
                        (RUT
                       , NOMBRE
                       , APELLIDO_PATERNO
                       , APELLIDO_MATERNO
                       , MONTO_MORA
                       , MONTO_MULTA
                       , MONTO_TOTAL
                       , NOM_CARTERA
                        )
                 VALUES (A.RUT
                       , A.NOMBRE
                       , A.APELLIDO_PATERNO
                       , A.APELLIDO_MATERNO
                       , A.MORA
                       , A.MULTA
                       , A.MORA + A.MULTA
                       , A.NOM_CARTERA
                        );
         END LOOP;
      END;
   END;
END SPI_DEUDORES_MOROSOS;
/