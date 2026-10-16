CREATE OR REPLACE PROCEDURE sp_comp_anula_comprobante
(
   ve_cod_comprobante        IN       number,
   ve_cod_usuario            IN       NUMBER,
   vs_cod_comprobante_new    OUT      NUMBER
)
AS
/******************************************************************************
   NAME:       sp_comp_anula_comprobante

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        23/10/2009   Victor Melo    anula un comprobante contable y sus items

   NOTAS:

******************************************************************************/

BEGIN
      DECLARE

        KL_PENDIENTE              NUMBER:=1;
        KL_TRASPASO               NUMBER:=3;

        BEGIN
          -- crea el comprobante
          INSERT INTO COMPROBANTE
            (COD_COMPROBANTE,
             FECHA_COMPROBANTE,
             COD_USUARIO,
             NOM_COMPROBANTE,
             COD_FAMILIA_DEUDA,
             COD_ESTADO_COMPROBANTE,
             COD_TIPO_COMPROBANTE,
             COD_ORIGEN_COMPROBANTE,
             COD_USUARIO_ESTADO,
             FECHA_ESTADO)
          SELECT comprobante_sq.NEXTVAL,
             sysdate,
             ve_cod_usuario,
             'Comprobante de Anulación del Comprobante '||to_char(ve_cod_comprobante),
             COD_FAMILIA_DEUDA,
             KL_PENDIENTE,
             KL_TRASPASO,
             COD_ORIGEN_COMPROBANTE,
             ve_cod_usuario,
             sysdate
          FROM COMPROBANTE
          WHERE COD_COMPROBANTE = ve_cod_comprobante;

          SELECT comprobante_sq.CURRVAL
          INTO vs_cod_comprobante_new
          FROM dual;

          -- copia los items invirtiendo el debe y haber
          INSERT INTO ITEM_COMPROBANTE
            (COD_ITEM_COMPROBANTE,
             COD_COMPROBANTE,
             COD_CUENTA_CONTABLE,
             MONTO_DEBE,
             MONTO_HABER)
          SELECT  ITEM_COMPROBANTE_SQ.NEXTVAL,
             vs_cod_comprobante_new,
             COD_CUENTA_CONTABLE,
             MONTO_HABER,
             MONTO_DEBE
          FROM ITEM_COMPROBANTE
          WHERE COD_COMPROBANTE = ve_cod_comprobante;

          -- copia el analitico
          INSERT INTO ANALITICO_COMPROBANTE
            (COD_ANALITICO_COMPROBANTE,
             COD_COMPROBANTE,
             COD_DEUDA,
             COD_CUENTA_CONTABLE,
             MONTO_DEBE,
             MONTO_HABER)
          SELECT analitico_comprobante_sq.NEXTVAL,
             vs_cod_comprobante_new,
             COD_DEUDA,
             COD_CUENTA_CONTABLE,
             MONTO_DEBE,
             MONTO_HABER
          FROM ANALITICO_COMPROBANTE
          WHERE COD_COMPROBANTE = ve_cod_comprobante;

      END;

END;
/