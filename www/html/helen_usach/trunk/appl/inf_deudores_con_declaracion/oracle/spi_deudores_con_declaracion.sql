CREATE OR REPLACE PROCEDURE spi_deudores_con_declaracion (
   VE_ANO                      NUMBER
 , VE_COD_ESTADO_DECLARACION   NUMBER
 , VE_COD_VIA_DECLARACION		NUMBER
)
AS
BEGIN
   DECLARE
      KL_COD_DEUDA_CANCELADA                  NUMBER := 4;
      KL_COD_ESTADO_PAGO_VIGENTE              NUMBER := 1;
      KL_COD_VIA_DECLARACION_WEB              NUMBER := 2;
      VL_COD_DEUDA                            NUMBER;
      VL_RUT                                  VARCHAR (20);
      VL_TIPO_DIREC_PARTI                     VARCHAR (1) := 'P';
      VL_FECHA_PAGO                           DATE;
      VL_MONTO_ABONO                          NUMBER;
      VL_MONTO_DESCUENTO                      NUMBER;
   BEGIN
      DECLARE
         CURSOR C_DEUDORES
         IS
            SELECT REPLACE (TO_CHAR (DR.RUT, '99,999,999')
                          , ','
                          , '.'
                           ) || '-' || DR.DIG_VERIF RUT
                 , NOMBRE
                 , APELLIDO_PATERNO
                 , APELLIDO_MATERNO
                 , TELEFONO
                 , DIRECCION
                 , NOM_COMUNA
                 , NOM_CIUDAD
                 , EMAIL
                 , FECHA_DECLARACION
              FROM DECLARACION DEC
                 ,DEUDOR DR LEFT OUTER JOIN DIRECCION DI ON DR.COD_DEUDOR = DI.COD_DEUDOR
						              LEFT OUTER JOIN COMUNA CO ON DI.COD_COMUNA = CO.COD_COMUNA
						              LEFT OUTER JOIN CIUDAD CI ON DI.COD_CIUDAD = CI.COD_CIUDAD
                              		  LEFT OUTER JOIN PAIS PA ON DI.COD_PAIS = PA.COD_PAIS
             WHERE ANO = VE_ANO
              	AND (VE_COD_ESTADO_DECLARACION = 100 OR COD_ESTADO_DECLARACION = VE_COD_ESTADO_DECLARACION)
               	AND (VE_COD_VIA_DECLARACION = 100 OR COD_VIA_DECLARACION = VE_COD_VIA_DECLARACION)
           		  AND DEC.COD_DEUDOR = DR.COD_DEUDOR
                AND F_GET_DIRECCION_DEUDOR (DEC.COD_DEUDOR, VL_TIPO_DIREC_PARTI) = DI.COD_DIRECCION;

      BEGIN
         FOR A IN C_DEUDORES
         LOOP
            INSERT INTO TEMP_DEUDORES
                        (RUT
                       , NOMBRE
                       , APELLIDO_PATERNO
                       , APELLIDO_MATERNO
                       , DIRECCION
                       , NOM_COMUNA
                       , NOM_CIUDAD
                       , TELEFONO
                       , EMAIL
                       , FECHA_INGRESO
                        )
                 VALUES (A.RUT
                       , A.NOMBRE
                       , A.APELLIDO_PATERNO
                       , A.APELLIDO_MATERNO
                       , A.DIRECCION
                       , A.NOM_COMUNA
                       , A.NOM_CIUDAD
                       , A.TELEFONO
                       , A.EMAIL
                       , A.FECHA_DECLARACION
                        );
         END LOOP;
      END;
   END;
END spi_deudores_con_declaracion;
/