PROMPT CREATE OR REPLACE PROCEDURE spu_detalle_abono
CREATE OR REPLACE PROCEDURE spu_detalle_abono (
   VE_OPERACION               IN   VARCHAR2
 , VE_COD_RENDICION_ABONO     IN   NUMBER
 , VE_COD_RENDICION           IN   NUMBER := NULL
 , VE_COD_ABONO               IN   NUMBER := NULL
 , VE_COD_DESCUENTO           IN   NUMBER := NULL
 , VE_MONTO_MONEDA            IN   NUMBER := NULL
 , VE_MONTO_PESOS             IN   NUMBER := NULL
 , VE_PORC                    IN   NUMBER := NULL
 , VE_COD_UNIVERSIDAD         IN   NUMBER := NULL
 , VE_MONTO_RENDIDO_MONEDA    IN   NUMBER := NULL
 , VE_MONTO_RENDIDO_PESOS     IN   NUMBER := NULL
)
AS
/******************************************************************************
   NOMBRE:       SPU_DETALLE_ABONO

   REVISION:
   VER        DATE         AUTHOR            DESCRIPTION
   ---------  ----------   ----------------  ------------------------------------
   1.0        08/03/2010   FERNANDO MERCADO   INSERTA O ACTUALIZA LA LISTA DE ABONOS
                                              Y DESCUENTOS CORRESPONDIENTES AL RANGO INGRESADO
                                              POR EL USUARIO EN LA RENDICION

   NOTAS:

******************************************************************************/
BEGIN
  BEGIN
    IF (VE_OPERACION = 'INSERT')
      THEN
        INSERT INTO RENDICION_ABONO
                  ( COD_RENDICION_ABONO
                  , COD_RENDICION
                  , COD_ABONO
                  , COD_DESCUENTO
                  , MONTO_MONEDA
                  , MONTO_PESOS
                  , PORC
                  , COD_UNIVERSIDAD
                  , MONTO_RENDIDO_MONEDA
                  , MONTO_RENDIDO_PESOS)
          VALUES  (rendicion_abono_sq.NEXTVAL
                  , VE_COD_RENDICION
                  , VE_COD_ABONO
                  , VE_COD_DESCUENTO
                  , VE_MONTO_MONEDA
                  , VE_MONTO_PESOS
                  , VE_PORC
                  , VE_COD_UNIVERSIDAD
                  , VE_MONTO_RENDIDO_MONEDA
                  , VE_MONTO_RENDIDO_PESOS);
    ELSIF(VE_OPERACION = 'DELETE')  THEN
    	DELETE RENDICION_ABONO
    	WHERE COD_RENDICION_ABONO = VE_COD_RENDICION_ABONO;
    END IF;
  END;
END;
/

