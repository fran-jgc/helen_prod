PROMPT CREATE OR REPLACE PROCEDURE sp_comp_simplifica
CREATE OR REPLACE PROCEDURE sp_comp_simplifica (
   ve_cod_comprobante   NUMBER
)
AS
/******************************************************************************
   NOMBRE:       sp_comp_simplifica
   PROPOSITO: Simplifica un comprobante contable si este tiene duplicados cuentas contables
              juntando las n lineas que tienen la misma cuenta en 1 sola linea

   REVISION:
   Ver        Date         Author            Description
   ---------  ----------   ----------------  ------------------------------------
   1.0        26/10/2009   Victor Melo
   2.0        13/11/2010   IS                 elimina el comprobante si no tiene item_comprobante
   NOTAS:

******************************************************************************/
BEGIN
   DECLARE
      vl_monto_debe                           NUMBER;
      vl_monto_haber                          NUMBER;
      k_borrar                                NUMBER := -1234567890;
      vl_count_item                           NUMBER;
      K_FONDO_INSTITUCIONAL                   NUMBER:=2;
      vl_cod_familia_deuda                    NUMBER;

      CURSOR c_item_comprobante
      IS
         SELECT cod_item_comprobante
              , cod_cuenta_contable
              , monto_debe
              , monto_haber
           FROM item_comprobante
          WHERE cod_comprobante = ve_cod_comprobante;

   BEGIN

      IF (ve_cod_comprobante IS null)THEN
        RETURN;
      END IF;

    ----------------------------------------------------
		--recalcula el comprobante si este es cruzado_cfcu
		sp_cruzado_cfcu (ve_cod_comprobante);
		-----------------------------------------------------


      SELECT Count(*)
      INTO vl_count_item
      FROM item_comprobante
      WHERE cod_comprobante = ve_cod_comprobante;

      IF(vl_count_item = 0) THEN
        DELETE analitico_comprobante
        WHERE cod_comprobante = ve_cod_comprobante;

        DELETE comprobante
        WHERE cod_comprobante = ve_cod_comprobante;

        RETURN;
      END IF;

      /*si la familia deuda del comprobante es = 2, elimina todo lo relacionado con
      comprobante, item y analitico*/
      SELECT  cod_familia_deuda
      INTO    vl_cod_familia_deuda
      FROM    comprobante
      WHERE   cod_comprobante = ve_cod_comprobante;


      IF (vl_cod_familia_deuda = K_FONDO_INSTITUCIONAL) THEN
        DELETE analitico_comprobante
        WHERE cod_comprobante = ve_cod_comprobante;

        DELETE item_comprobante
        WHERE cod_comprobante = ve_cod_comprobante;

        DELETE comprobante
        WHERE cod_comprobante = ve_cod_comprobante;
      END IF;


      FOR a IN c_item_comprobante
      LOOP
         vl_monto_debe                        := 1;

         SELECT monto_debe
           INTO vl_monto_debe
           FROM item_comprobante
          WHERE cod_item_comprobante = a.cod_item_comprobante;

         IF (vl_monto_debe <> k_borrar)
         THEN
            -- suma los debe y haber de la misma cuenta en otras lineas
              SELECT NVL (SUM (monto_debe), 0)
                 , NVL (SUM (monto_haber), 0)
              INTO vl_monto_debe
                 , vl_monto_haber
              FROM item_comprobante
             WHERE cod_comprobante = ve_cod_comprobante
               AND cod_cuenta_contable = a.cod_cuenta_contable
               AND cod_item_comprobante <> a.cod_item_comprobante
               AND monto_debe <> k_borrar;

            IF (   vl_monto_debe <> 0
                OR vl_monto_haber <> 0)
            THEN
               -- actualiza el debe o haber
               IF (a.monto_debe + vl_monto_debe > a.monto_haber + vl_monto_haber)
               THEN
                  UPDATE item_comprobante
                     SET monto_debe = monto_debe + vl_monto_debe - (a.monto_haber + vl_monto_haber)
                       , monto_haber = 0
                   WHERE cod_item_comprobante = a.cod_item_comprobante;
               ELSE
                  UPDATE item_comprobante
                     SET monto_debe = 0
                       , monto_haber = monto_haber + vl_monto_haber - (monto_debe + vl_monto_debe)
                   WHERE cod_item_comprobante = a.cod_item_comprobante;
               END IF;

               -- marca para eliminar los items simplificados
               UPDATE item_comprobante
                  SET monto_debe = k_borrar
                WHERE cod_comprobante = ve_cod_comprobante
                  AND cod_cuenta_contable = a.cod_cuenta_contable
                  AND cod_item_comprobante <> a.cod_item_comprobante
                  AND monto_debe <> k_borrar;
            END IF;
         END IF;
      END LOOP;

      -- borrar los comprobantes eliminados
      DELETE      item_comprobante
      WHERE       cod_comprobante = ve_cod_comprobante
              AND monto_debe = k_borrar;


      -- borra las lineas en cero
      DELETE      item_comprobante
      WHERE       cod_comprobante = ve_cod_comprobante
              AND monto_debe = 0
              AND monto_haber = 0;

     SELECT Count(*)
      INTO vl_count_item
      FROM item_comprobante
      WHERE cod_comprobante = ve_cod_comprobante;

      IF(vl_count_item = 0) THEN
        DELETE analitico_comprobante
        WHERE cod_comprobante = ve_cod_comprobante;

        DELETE comprobante
        WHERE cod_comprobante = ve_cod_comprobante;

        RETURN;
      END IF;

      ----------------------------------------------------
		-- recalcula el comprobante si este es cruzado dejando el porcentaje que correspinde a la U
		--sp_comp_cruzado  (ve_cod_comprobante); --se deja para hito 2
		-----------------------------------------------------

   END;
END;
/
