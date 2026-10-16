CREATE OR REPLACE PROCEDURE spu_fecha_carta_aviso(
  ve_cod_carta_aviso  IN NUMBER
  ,ve_cod_usuario     IN NUMBER
)
AS
/******************************************************************************
   NAME:       spu_fecha_carta_aviso

   Update a los campos fecha carta de la renegociacion

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        06/07/2010  Sergio Castro

******************************************************************************/
BEGIN
  DECLARE
    K_ESTADO_RENEG_CONFIR   NUMBER :=2;
    K_BITACORA_CARTA_INCUMP NUMBER :=24;
    vl_cod_deudor           NUMBER;

    CURSOR carta_1 IS
      SELECT  DISTINCT b.cod_deuda
			FROM    renegociacion r
						  ,plan_pago_renegociacion ppr
						  ,boleta b
						  ,sub_boleta sb
			WHERE   cod_estado_renegociacion = K_ESTADO_RENEG_CONFIR
						  AND ppr.cod_renegociacion = r.cod_renegociacion
						  AND b.cod_boleta = ppr.cod_boleta
						  AND b.cod_boleta = sb.cod_boleta
						  AND fecha_carta1 IS NULL
						  AND fecha_carta2 IS NULL
						  AND fecha_carta3 IS NULL
						  AND FECHA_VENCTO < SYSDATE
						    AND FECHA_PAGO IS NULL;

    CURSOR carta_2 IS
      SELECT  DISTINCT b.cod_deuda
			FROM    renegociacion r
						  ,plan_pago_renegociacion ppr
						  ,boleta b
						  ,sub_boleta sb
			WHERE   cod_estado_renegociacion = K_ESTADO_RENEG_CONFIR
						  AND ppr.cod_renegociacion = r.cod_renegociacion
						  AND b.cod_boleta = ppr.cod_boleta
						  AND b.cod_boleta = sb.cod_boleta
						  AND fecha_carta1 IS NOT NULL
						  AND fecha_carta2 IS NULL
						  AND fecha_carta3 IS NULL
						  AND FECHA_VENCTO < SYSDATE
						    AND FECHA_PAGO IS NULL;

    CURSOR carta_3 IS
      SELECT  DISTINCT b.cod_deuda
		  FROM    renegociacion r
						  ,plan_pago_renegociacion ppr
						  ,boleta b
						  ,sub_boleta sb
		  WHERE   cod_estado_renegociacion = K_ESTADO_RENEG_CONFIR
						  AND ppr.cod_renegociacion = r.cod_renegociacion
						  AND b.cod_boleta = ppr.cod_boleta
						  AND b.cod_boleta = sb.cod_boleta
						  AND fecha_carta1 IS NOT NULL
						  AND fecha_carta2 IS NOT NULL
						  AND fecha_carta3 IS NULL
						  AND FECHA_VENCTO < SYSDATE
						  AND FECHA_PAGO IS NULL;

  BEGIN
    IF (ve_cod_carta_aviso = 1) THEN
      FOR a IN carta_1 LOOP
        SELECT  cod_deudor
        INTO    vl_cod_deudor
        FROM    deuda
        WHERE   cod_deuda = a.cod_deuda;

        spu_bitacora_deudor ('Primera Carta Aviso: ' || To_Char(a.cod_deuda), K_BITACORA_CARTA_INCUMP, vl_cod_deudor, ve_cod_usuario);

      END LOOP;

      UPDATE renegociacion
      SET fecha_carta1 = SYSDATE
      WHERE cod_renegociacion IN (SELECT  DISTINCT r.cod_renegociacion
				                          FROM    renegociacion r
						                              ,plan_pago_renegociacion ppr
						                              ,boleta b
						                              ,sub_boleta sb
				                          WHERE   cod_estado_renegociacion = K_ESTADO_RENEG_CONFIR
						                              AND ppr.cod_renegociacion = r.cod_renegociacion
						                              AND b.cod_boleta = ppr.cod_boleta
						                              AND b.cod_boleta = sb.cod_boleta
						                              AND fecha_carta1 IS NULL
						                              AND fecha_carta2 IS NULL
						                              AND fecha_carta3 IS NULL
						                              AND FECHA_VENCTO < SYSDATE
						                              AND FECHA_PAGO IS NULL);
      
    ELSIF (ve_cod_carta_aviso = 2) THEN
      FOR a IN carta_2 LOOP
        SELECT  cod_deudor
        INTO    vl_cod_deudor
        FROM    deuda
        WHERE   cod_deuda = a.cod_deuda;

        spu_bitacora_deudor ('Segundo Carta Aviso: ' || To_Char(a.cod_deuda), K_BITACORA_CARTA_INCUMP, vl_cod_deudor, ve_cod_usuario);

      END LOOP;

      UPDATE renegociacion
      SET fecha_carta2 = SYSDATE
      WHERE cod_renegociacion IN (SELECT  DISTINCT r.cod_renegociacion
				                          FROM    renegociacion r
						                              ,plan_pago_renegociacion ppr
						                              ,boleta b
						                              ,sub_boleta sb
				                          WHERE   cod_estado_renegociacion = K_ESTADO_RENEG_CONFIR
						                              AND ppr.cod_renegociacion = r.cod_renegociacion
						                              AND b.cod_boleta = ppr.cod_boleta
						                              AND b.cod_boleta = sb.cod_boleta
						                              AND fecha_carta1 IS NOT NULL
						                              AND fecha_carta2 IS NULL
						                              AND fecha_carta3 IS NULL
						                              AND FECHA_VENCTO < SYSDATE
						                              AND FECHA_PAGO IS NULL);
    ELSIF (ve_cod_carta_aviso = 3) THEN
      FOR a IN carta_3 LOOP
        SELECT  cod_deudor
        INTO    vl_cod_deudor
        FROM    deuda
        WHERE   cod_deuda = a.cod_deuda;

        spu_bitacora_deudor ('Tercera Carta Aviso: ' || To_Char(a.cod_deuda), K_BITACORA_CARTA_INCUMP, vl_cod_deudor, ve_cod_usuario);

      END LOOP;

      UPDATE renegociacion
      SET fecha_carta3 = SYSDATE
      WHERE cod_renegociacion IN (SELECT  DISTINCT r.cod_renegociacion
				                          FROM    renegociacion r
						                              ,plan_pago_renegociacion ppr
						                              ,boleta b
						                              ,sub_boleta sb
				                          WHERE   cod_estado_renegociacion = K_ESTADO_RENEG_CONFIR
						                              AND ppr.cod_renegociacion = r.cod_renegociacion
						                              AND b.cod_boleta = ppr.cod_boleta
						                              AND b.cod_boleta = sb.cod_boleta
						                              AND fecha_carta1 IS NOT NULL
						                              AND fecha_carta2 IS NOT NULL
						                              AND fecha_carta3 IS NULL
						                              AND FECHA_VENCTO < SYSDATE
						                              AND FECHA_PAGO IS NULL);
    END IF;
    COMMIT;
  END;
END spu_fecha_carta_aviso;