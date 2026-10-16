CREATE OR REPLACE PROCEDURE HELEN.spu_devolucion_pagare (
    VE_OPERACION                    IN VARCHAR2,
    VE_COD_DEVOLUCION_PAGARE        IN NUMBER := NULL,
    VE_COD_DEUDA                    IN NUMBER := NULL,
    VE_ORIGEN                       IN VARCHAR2 := NULL,
    VE_COD_ESTADO_DEVOLUCION_PAG    IN NUMBER := NULL,
    VE_RETIRADO_POR                 IN VARCHAR2 := NULL,
    VE_RUT_RETIRA                   IN VARCHAR2 := NULL,
    VE_NOM_RETIRA                   IN VARCHAR2 := NULL,
    VE_COD_ESTADO_ENVIO_DOMICILIO   IN NUMBER := NULL,
    VE_DIRECCION_ENVIO_DOMICILIO    IN VARCHAR2 := NULL,
    VE_NRO_ORDEN_TRABAJO            IN VARCHAR2 := NULL,
    VE_FECHA_RECEPCION              IN DATE := NULL,
    VE_MOTIVO                       IN VARCHAR2 := NULL,
    VE_COD_USUARIO                  IN NUMBER := NULL,
    VE_EMAIL                        IN VARCHAR2 := NULL,
    VE_COD_DEUDOR                   IN NUMBER := NULL,
    VE_COD_ESTADO_RETIRO_PRE        IN NUMBER := NULL
)
AS
    KE_PAGERE_ENVIO_POR_EMAIL       NUMBER := 3;
    KE_PAGERE_ENVIO_DOMICILIO       NUMBER := 2;
    KE_PAGERE_RETIRO_PRESENCIAL     NUMBER := 1;

    KE_DOMICILIO_EN_PREPARACION     NUMBER := 1;
    KE_DOMICILIO_ENVIADO_EXITOSO    NUMBER := 2;
    KE_DOMICILIO_ENVIADO_FALLIDO    NUMBER := 3;

    KE_ESTADO_RETIRO_PROCESO        NUMBER := 1;
    KE_ESTADO_RETIRO_PRE_DEVUELTO   NUMBER := 2;

    -- Estados de pagaré
    KE_PROCESO_DEVOLUCION           NUMBER := 6;  -- En proceso de devolución
    KE_ESTADO_PAGARE_DEVUELTO       NUMBER := 3;  -- Devuelto (estado final exitoso)

    KL_DEV_PAGARE                   NUMBER := 32;
    KL_FOLIO                        NUMBER := NULL;
    KL_NRO_FOLIO                    NUMBER;
    KL_FECHA_EMISION_ORDINARIO      DATE := NULL;
    KL_FECHA_ENVIO_MAIL             DATE := NULL;
    KL_FECHA_ESTADO_DEV_PAGARE      DATE := NULL;
    KL_COD_UNIVERSIDAD              NUMBER := 6;
BEGIN
    IF VE_OPERACION = 'INSERT' THEN

        IF VE_COD_ESTADO_DEVOLUCION_PAG IS NOT NULL THEN
            KL_FOLIO := nro_folio_pagare_sq.NEXTVAL;
            KL_FECHA_EMISION_ORDINARIO := SYSDATE;
            KL_FECHA_ENVIO_MAIL := SYSDATE;
            KL_FECHA_ESTADO_DEV_PAGARE := SYSDATE;
        END IF;

        INSERT INTO DEVOLUCION_PAGARE (
            COD_DEVOLUCION_PAGARE,
            COD_DEUDA,
            ORIGEN,
            COD_ESTADO_DEVOLUCION_PAGARE,
            NRO_FOLIO,
            RETIRADO_POR,
            RUT_RETIRA,
            NOM_RETIRA,
            COD_ESTADO_ENVIO_DOMICILIO,
            DIRECCION_ENVIO_DOMICILIO,
            FECHA_EMISION_ORDINARIO,
            NRO_ORDEN_TRABAJO,
            FECHA_RECEPCION,
            FECHA_ENVIO_MAIL,
            FECHA_ESTADO_DEV_PAGARE,
            MOTIVO,
            COD_USUARIO,
            EMAIL,
            COD_ESTADO_RETIRO_PRESENCIAL
        )
        VALUES (
            devolucion_pagare_sq.NEXTVAL,
            VE_COD_DEUDA,
            VE_ORIGEN,
            VE_COD_ESTADO_DEVOLUCION_PAG,
            KL_FOLIO,
            VE_RETIRADO_POR,
            VE_RUT_RETIRA,
            VE_NOM_RETIRA,
            VE_COD_ESTADO_ENVIO_DOMICILIO,
            VE_DIRECCION_ENVIO_DOMICILIO,
            KL_FECHA_EMISION_ORDINARIO,
            VE_NRO_ORDEN_TRABAJO,
            VE_FECHA_RECEPCION,
            KL_FECHA_ENVIO_MAIL,
            KL_FECHA_ESTADO_DEV_PAGARE,
            VE_MOTIVO,
            VE_COD_USUARIO,
            VE_EMAIL,
            VE_COD_ESTADO_RETIRO_PRE
        );

        spu_devolucion_pagare(
            'UPDATE',
            devolucion_pagare_sq.CURRVAL,
            VE_COD_DEUDA,
            VE_ORIGEN,
            VE_COD_ESTADO_DEVOLUCION_PAG,
            VE_RETIRADO_POR,
            VE_RUT_RETIRA,
            VE_NOM_RETIRA,
            VE_COD_ESTADO_ENVIO_DOMICILIO,
            VE_DIRECCION_ENVIO_DOMICILIO,
            VE_NRO_ORDEN_TRABAJO,
            VE_FECHA_RECEPCION,
            VE_MOTIVO,
            VE_COD_USUARIO,
            VE_EMAIL,
            VE_COD_DEUDOR,
            VE_COD_ESTADO_RETIRO_PRE
        );

    ELSIF VE_OPERACION = 'UPDATE' THEN

        IF VE_COD_ESTADO_DEVOLUCION_PAG IS NOT NULL THEN
            SELECT NRO_FOLIO
            INTO KL_NRO_FOLIO
            FROM DEVOLUCION_PAGARE
            WHERE COD_DEVOLUCION_PAGARE = VE_COD_DEVOLUCION_PAGARE;

            IF KL_NRO_FOLIO IS NULL THEN
                UPDATE DEVOLUCION_PAGARE
                SET NRO_FOLIO = nro_folio_pagare_sq.NEXTVAL
                WHERE COD_DEVOLUCION_PAGARE = VE_COD_DEVOLUCION_PAGARE;
            END IF;
        END IF;

        -- =====================================================================
        -- TIPO 1: RETIRO PRESENCIAL
        -- =====================================================================
        IF VE_COD_ESTADO_DEVOLUCION_PAG = KE_PAGERE_RETIRO_PRESENCIAL THEN

            -- Sub-estado 1: En proceso (esperando retiro) ¿ pagaré queda en 6
            IF VE_COD_ESTADO_RETIRO_PRE = KE_ESTADO_RETIRO_PROCESO THEN
                UPDATE DEVOLUCION_PAGARE
                SET RETIRADO_POR                = VE_RETIRADO_POR,
                    RUT_RETIRA                  = VE_RUT_RETIRA,
                    NOM_RETIRA                  = VE_NOM_RETIRA,
                    FECHA_ESTADO_DEV_PAGARE     = SYSDATE,
                    COD_USUARIO                 = VE_COD_USUARIO,
                    COD_ESTADO_DEVOLUCION_PAGARE = KE_PAGERE_RETIRO_PRESENCIAL,
                    COD_ESTADO_RETIRO_PRESENCIAL = VE_COD_ESTADO_RETIRO_PRE
                WHERE COD_DEVOLUCION_PAGARE = VE_COD_DEVOLUCION_PAGARE;

                UPDATE PAGARE
                SET COD_ESTADO_PAGARE = KE_PROCESO_DEVOLUCION  -- 6: sigue en proceso
                WHERE COD_DEUDA = VE_COD_DEUDA
                  AND COD_UNIVERSIDAD = KL_COD_UNIVERSIDAD;

                spu_bitacora_deudor(
                    'PAGARES DEUDA ' || TO_CHAR(VE_COD_DEUDA) || ' EN PREPARACION PARA RETIRO EN FORMA PRESENCIAL',
                    KL_DEV_PAGARE,
                    VE_COD_DEUDOR,
                    VE_COD_USUARIO
                );
            END IF;

            -- Sub-estado 2: Retirado exitosamente ¿ pagaré pasa a DEVUELTO (3)
            IF VE_COD_ESTADO_RETIRO_PRE = KE_ESTADO_RETIRO_PRE_DEVUELTO THEN
                UPDATE DEVOLUCION_PAGARE
                SET RETIRADO_POR                = VE_RETIRADO_POR,
                    RUT_RETIRA                  = VE_RUT_RETIRA,
                    NOM_RETIRA                  = VE_NOM_RETIRA,
                    FECHA_ESTADO_DEV_PAGARE     = SYSDATE,
                    COD_USUARIO                 = VE_COD_USUARIO,
                    COD_ESTADO_DEVOLUCION_PAGARE = KE_PAGERE_RETIRO_PRESENCIAL,
                    COD_ESTADO_RETIRO_PRESENCIAL = VE_COD_ESTADO_RETIRO_PRE
                WHERE COD_DEVOLUCION_PAGARE = VE_COD_DEVOLUCION_PAGARE;

                -- FIX: retiro confirmado ¿ pagaré queda DEVUELTO (3), no en proceso (6)
                UPDATE PAGARE
                SET COD_ESTADO_PAGARE = KE_ESTADO_PAGARE_DEVUELTO  -- 3: devuelto
                WHERE COD_DEUDA = VE_COD_DEUDA
                  AND COD_UNIVERSIDAD = KL_COD_UNIVERSIDAD;

                spu_bitacora_deudor(
                    'PAGARES DEUDA ' || TO_CHAR(VE_COD_DEUDA) || ' RETIRADOS EN FORMA PRESENCIAL',
                    KL_DEV_PAGARE,
                    VE_COD_DEUDOR,
                    VE_COD_USUARIO
                );
            END IF;

        -- =====================================================================
        -- TIPO 2: ENVÍO A DOMICILIO
        -- =====================================================================
        ELSIF VE_COD_ESTADO_DEVOLUCION_PAG = KE_PAGERE_ENVIO_DOMICILIO THEN

            -- Sub-estado 1: En preparación ¿ pagaré queda en proceso (6)
            IF VE_COD_ESTADO_ENVIO_DOMICILIO = KE_DOMICILIO_EN_PREPARACION THEN
                UPDATE DEVOLUCION_PAGARE
                SET NRO_ORDEN_TRABAJO           = VE_NRO_ORDEN_TRABAJO,
                    FECHA_EMISION_ORDINARIO     = SYSDATE,
                    FECHA_ESTADO_DEV_PAGARE     = SYSDATE,
                    COD_USUARIO                 = VE_COD_USUARIO,
                    DIRECCION_ENVIO_DOMICILIO   = VE_DIRECCION_ENVIO_DOMICILIO,
                    COD_ESTADO_DEVOLUCION_PAGARE = KE_PAGERE_ENVIO_DOMICILIO,
                    COD_ESTADO_ENVIO_DOMICILIO  = KE_DOMICILIO_EN_PREPARACION
                WHERE COD_DEVOLUCION_PAGARE = VE_COD_DEVOLUCION_PAGARE;

                UPDATE PAGARE
                SET COD_ESTADO_PAGARE = KE_PROCESO_DEVOLUCION  -- 6: sigue en proceso
                WHERE COD_DEUDA = VE_COD_DEUDA
                  AND COD_UNIVERSIDAD = KL_COD_UNIVERSIDAD;

                spu_bitacora_deudor(
                    'PAGARES DEUDA ' || TO_CHAR(VE_COD_DEUDA) || ' EN PREPARACION ENVIO CORREO POSTAL',
                    KL_DEV_PAGARE,
                    VE_COD_DEUDOR,
                    VE_COD_USUARIO
                );
            END IF;

            -- Sub-estado 2: Entrega exitosa ¿ pagaré pasa a DEVUELTO (3)
            IF VE_COD_ESTADO_ENVIO_DOMICILIO = KE_DOMICILIO_ENVIADO_EXITOSO THEN
                UPDATE DEVOLUCION_PAGARE
                SET NRO_ORDEN_TRABAJO           = VE_NRO_ORDEN_TRABAJO,
                    FECHA_RECEPCION             = VE_FECHA_RECEPCION,
                    FECHA_ESTADO_DEV_PAGARE     = SYSDATE,
                    COD_USUARIO                 = VE_COD_USUARIO,
                    COD_ESTADO_DEVOLUCION_PAGARE = KE_PAGERE_ENVIO_DOMICILIO,
                    COD_ESTADO_ENVIO_DOMICILIO  = KE_DOMICILIO_ENVIADO_EXITOSO
                WHERE COD_DEVOLUCION_PAGARE = VE_COD_DEVOLUCION_PAGARE;

                -- FIX: entrega exitosa ¿ pagaré queda DEVUELTO (3), no en proceso (6)
                UPDATE PAGARE
                SET COD_ESTADO_PAGARE = KE_ESTADO_PAGARE_DEVUELTO  -- 3: devuelto
                WHERE COD_DEUDA = VE_COD_DEUDA
                  AND COD_UNIVERSIDAD = KL_COD_UNIVERSIDAD;

                spu_bitacora_deudor(
                    'PAGARES DEUDA ' || TO_CHAR(VE_COD_DEUDA) || ' RECEPCIONADOS EN DOMICILIO',
                    KL_DEV_PAGARE,
                    VE_COD_DEUDOR,
                    VE_COD_USUARIO
                );
            END IF;

            IF VE_COD_ESTADO_ENVIO_DOMICILIO = KE_DOMICILIO_ENVIADO_FALLIDO THEN
                UPDATE DEVOLUCION_PAGARE
                SET NRO_ORDEN_TRABAJO           = VE_NRO_ORDEN_TRABAJO,
                    MOTIVO                      = VE_MOTIVO,
                    FECHA_ESTADO_DEV_PAGARE     = SYSDATE,
                    COD_USUARIO                 = VE_COD_USUARIO,
                    COD_ESTADO_DEVOLUCION_PAGARE = KE_PAGERE_ENVIO_DOMICILIO,
                    COD_ESTADO_ENVIO_DOMICILIO  = KE_DOMICILIO_ENVIADO_FALLIDO
                WHERE COD_DEVOLUCION_PAGARE = VE_COD_DEVOLUCION_PAGARE;

                UPDATE PAGARE
                SET COD_ESTADO_PAGARE = KE_PROCESO_DEVOLUCION
                WHERE COD_DEUDA = VE_COD_DEUDA
                  AND COD_UNIVERSIDAD = KL_COD_UNIVERSIDAD;

                spu_bitacora_deudor(
                    'PAGARES DEUDA ' || TO_CHAR(VE_COD_DEUDA) || ' NO ENTREGADOS EN DOMICILIO - RETORNAN AL FS. MOTIVO: ' || VE_MOTIVO,
                    KL_DEV_PAGARE,
                    VE_COD_DEUDOR,
                    VE_COD_USUARIO
                );
            END IF;

        -- =====================================================================
        -- TIPO 3: ENVÍO POR EMAIL
        -- =====================================================================
        ELSIF VE_COD_ESTADO_DEVOLUCION_PAG = KE_PAGERE_ENVIO_POR_EMAIL THEN
            UPDATE DEVOLUCION_PAGARE
            SET EMAIL                       = VE_EMAIL,
                FECHA_ENVIO_MAIL            = SYSDATE,
                FECHA_ESTADO_DEV_PAGARE     = SYSDATE,
                COD_USUARIO                 = VE_COD_USUARIO,
                COD_ESTADO_DEVOLUCION_PAGARE = KE_PAGERE_ENVIO_POR_EMAIL
            WHERE COD_DEVOLUCION_PAGARE = VE_COD_DEVOLUCION_PAGARE;

            UPDATE PAGARE
            SET COD_ESTADO_PAGARE = KE_ESTADO_PAGARE_DEVUELTO
            WHERE COD_DEUDA = VE_COD_DEUDA
              AND COD_UNIVERSIDAD = KL_COD_UNIVERSIDAD;

            spu_bitacora_deudor(
                'PAGARES DEUDA ' || TO_CHAR(VE_COD_DEUDA) || ' ESCANEADOS Y ENVIADOS POR EMAIL',
                KL_DEV_PAGARE,
                VE_COD_DEUDOR,
                VE_COD_USUARIO
            );
        END IF;

    END IF;
END;
