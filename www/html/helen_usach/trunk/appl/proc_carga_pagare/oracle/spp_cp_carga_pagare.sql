PROMPT CREATE OR REPLACE PROCEDURE spp_cp_carga_pagare
CREATE OR REPLACE PROCEDURE spp_cp_carga_pagare (ve_cod_ejecucion_proceso   NUMBER
                                                ,ve_fecha_carga date)
AS
/******************************************************************************
   NAME:       spp_cp_carga_pagare

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        20/10/2009   SC              Realiza la Carga del pagaré

   NOTES:

******************************************************************************/
BEGIN
  DECLARE
    vl_cod_usuario            NUMBER;
    vl_count                  NUMBER := 0;
    vl_cod_deudor             NUMBER;
    vl_cod_deuda              deuda.cod_deuda%TYPE;
    vl_cod_estado_deuda       estado_deuda.cod_estado_deuda%TYPE;
    vl_cod_cartera            cartera.cod_cartera%TYPE;
    vl_perido_gracia          tipo_deuda.periodo_gracia%TYPE;
    vl_fecha_inicio_cobra     DATE;
    vl_direccion              VARCHAR (100) := ' ';
    vl_nom_comprobante        VARCHAR2 (100);
    vl_cod_comuna             NUMBER        := NULL;
    vl_cod_familia_deuda      NUMBER;
    vl_cod_ciudad             NUMBER        := NULL;
    vl_cod_pais               NUMBER        := NULL;
    vl_cantidad_cuota         NUMBER := 0;
    vl_email                  VARCHAR (100) := NULL;
    vl_telefono               VARCHAR (100) := NULL;
    vl_celular                VARCHAR (100) := NULL;
    K_CARTERA_DEFECTO         NUMBER := 2;
    K_UNIVERSIDAD_DEFECTO     NUMBER := 3;
    K_CANCELADA               NUMBER := 5;
    K_NO_EXIGIBLE             NUMBER := 1;
    K_EXIGIBLE                NUMBER := 2;
    K_EST_COMPROBANTE_PDTE    NUMBER := 1;
    K_COMPROB_TRASPASO        NUMBER := 3;
    K_ORIGEN_CARGA_PAGARE     NUMBER := 12;
    K_MODIFICA_PAGARE         NUMBER := 5;
    K_INST_EDUCACION_SUPERIOR NUMBER := 11;
    K_SIN_VENCIMIENTO_PACTADO NUMBER := 1;
    K_CTA_INTERES_NORMAL      NUMBER:= 6;
    vl_cuenta_svp             NUMBER;
    vl_cuenta_univ            NUMBER;
    vl_cuenta_interes         NUMBER;
    vl_contador_comprobante   NUMBER;
    vl_cod_comprobante_int    NUMBER;
    vl_cod_comprobante        NUMBER;
    vl_count_registros        NUMBER;
    i                         NUMBER := 0;
    vl_count_direccion        NUMBER;
    vl_rut_error              NUMBER;
    vl_tasa_interes_credito   NUMBER;
    vl_diferencia             NUMBER;
    vl_monto_interes          NUMBER;
    vl_fecha_inicio_cobra_old DATE;

    CURSOR C_CARGA_PAGARE IS
      SELECT  COD_CARGA_PAGARE
              ,RUT
              ,DIG_VERIF
              ,NOMBRE
              ,APELLIDO_PATERNO
              ,APELLIDO_MATERNO
              ,SEXO
              ,COD_ESTADO_CIVIL
              ,FECHA_NACIM
              ,DIRECCION
              ,COD_COMUNA
              ,COD_CIUDAD
              ,COD_PAIS
              ,EMAIL
              ,TELEFONO
              ,CELULAR
              ,COD_TIPO_DEUDA
              ,FOLIO_PAGARE
              ,FECHA_PAGARE
              ,MONTO_MONEDA
              ,MONTO_PESOS
      FROM    CARGA_PAGARE
      WHERE   CARGADO = 'N'
      AND   Trunc(FECHA_CARGA) = Trunc(ve_fecha_carga);

    CURSOR C_CARGA_MATRICULA (ve_rut NUMBER) IS
      SELECT  DISTINCT ano_matricula
                 ,semestre
          FROM carga_matricula
          WHERE rut = ve_rut;


      BEGIN
      vl_contador_comprobante              := 0;

      -- usuario que ejecuta el proceso
      SELECT cod_usuario
      INTO vl_cod_usuario
      FROM ejecucion_proceso
      WHERE cod_ejecucion_proceso = ve_cod_ejecucion_proceso;

      -- IMPORTANTE: El where de este select debe ser igual al del cursor !!
      -- calcula el total de registros
      SELECT  Count(*)
      INTO    vl_count_registros
      FROM    CARGA_PAGARE
      WHERE   CARGADO = 'N';

      spu_ejecucion_proceso('SET_TOTAL', ve_cod_ejecucion_proceso, vl_count_registros);
      i := 1;

      FOR A IN C_CARGA_PAGARE LOOP
        vl_rut_error := a.rut;

        -- valida que exista cod_estado_civil
        SELECT Count(*)
        INTO vl_count
        FROM estado_civil
        WHERE cod_estado_civil = a.cod_estado_civil;

        IF (vl_count = 0) THEN
          spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, 'RUT: ' || To_Char(a.rut) || ' Estado civil inexistente.');
          GOTO end_loop;
        END IF;


        -- valida que exista cod_comuna
        IF(a.cod_comuna IS NOT null) THEN
          SELECT Count(*)
          INTO vl_count
          FROM comuna
          WHERE cod_comuna = a.cod_comuna;
          IF (vl_count = 0) THEN
            spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, 'RUT: ' || To_Char(a.rut) || ' Comuna inexistente.');
            GOTO end_loop;
          END IF;
        END IF;


        -- valida que exista cod_ciudad
        IF(a.cod_ciudad IS NOT null) THEN
          SELECT Count(*)
          INTO vl_count
          FROM ciudad
          WHERE cod_ciudad = a.cod_ciudad;
          IF (vl_count = 0) THEN
            spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, 'RUT: ' || To_Char(a.rut) || ' Ciudad inexistente.');
            GOTO end_loop;
          END IF;
        END IF;

        -- valida que exista cod_pais
        SELECT Count(*)
        INTO vl_count
        FROM pais
        WHERE cod_pais = a.cod_pais;
        IF (vl_count = 0) THEN
          spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, 'RUT: ' || To_Char(a.rut) || ' Pais inexistente.');
          GOTO end_loop;
        END IF;

        -- valida que exista cod_tipo_deuda
        SELECT Count(*)
        INTO vl_count
        FROM tipo_deuda
        WHERE cod_tipo_deuda = a.cod_tipo_deuda;
        IF (vl_count = 0) THEN
          spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, 'RUT: ' || To_Char(a.rut) || ' Tipo deuda inexistente.');
          GOTO end_loop;
        END IF;

        -- valida que el RUT tenga matricula para el año del pagare, 1ro busca en MATRICULA
        SELECT Count(*)
        INTO vl_count
        FROM matricula m, deudor d
        WHERE d.rut = a.rut
          AND m.cod_deudor = d.cod_deudor
          AND m.ano_matricula = To_Number(To_Char(a.fecha_pagare, 'yyyy'));
        IF (vl_count = 0) THEN
          -- busca en carga_matricula
          SELECT Count(*)
          INTO vl_count
          FROM carga_matricula cm, carrera c
          WHERE cm.rut = a.rut
            AND cm.ano_matricula = To_Number(To_Char(a.fecha_pagare, 'yyyy'))
            AND c.cod_carrera = cm.cod_carrera; -- cod_carrera debe ser valido
          IF (vl_count = 0) THEN
            spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, 'RUT: ' || To_Char(a.rut) || ' No registra matricula.');
            GOTO end_loop;
          END IF;
        END IF;

        -- verifica si el proceso ha sido cancelado
        IF (f_ep_cancela_proceso(ve_cod_ejecucion_proceso) = 'S') THEN
          sp_comp_simplifica(vl_cod_comprobante);
          COMMIT;
          RETURN;
        END IF;

        SELECT  Count(*)
        INTO    vl_count
        FROM    DEUDOR
        WHERE   RUT = a.rut;

        IF (vl_count = 0) THEN
          spu_deudor( 'INSERT'
                      ,null
                      ,a.rut
                      ,a.dig_verif
                      ,a.nombre
                      ,a.apellido_paterno
                      ,a.apellido_materno
                      ,a.sexo
                      ,a.cod_estado_civil
                      ,a.fecha_nacim);

          -- Se carga la matricula desde CARGA_MATRICULA
          FOR b IN C_CARGA_MATRICULA(a.rut) LOOP
            INSERT INTO MATRICULA(  COD_MATRICULA
                                    ,COD_DEUDOR
                                    ,COD_UNIVERSIDAD
                                    ,NOM_CARRERA
                                    ,ANO_MATRICULA
                                    ,SEMESTRE
                                    ,COD_CARRERA)
            SELECT matricula_sq.NEXTVAL
                  ,deudor_sq.currval
                  ,f_get_parametro (K_UNIVERSIDAD_DEFECTO)
                  ,nom_carrera
                  ,ano_matricula
                  ,semestre
                  ,cod_carrera
            FROM carga_matricula
            WHERE rut = a.rut
              AND ano_matricula = b.ano_matricula
              AND semestre = b.semestre
              AND ROWNUM = 1;
          END LOOP;
        END IF;

        SELECT  COD_DEUDOR
        INTO    vl_cod_deudor
        FROM    DEUDOR
        WHERE   RUT = a.rut;


        SELECT  Count(*)
        INTO    vl_count_direccion
        FROM    DIRECCION
        WHERE   COD_DEUDOR = vl_cod_deudor
                AND TIPO_DIRECCION = 'P'
                AND ES_VALIDA = 'S';

        IF (vl_count_direccion <> 0) THEN
          SELECT  DIRECCION
                  ,COD_COMUNA
                  ,COD_CIUDAD
                  ,COD_PAIS
                  ,EMAIL
                  ,TELEFONO
                  ,CELULAR
          INTO    vl_direccion
                  ,vl_cod_comuna
                  ,vl_cod_ciudad
                  ,vl_cod_pais
                  ,vl_email
                  ,vl_telefono
                  ,vl_celular
          FROM    DIRECCION
          WHERE   COD_DEUDOR = vl_cod_deudor
                  AND TIPO_DIRECCION = 'P'
                  AND ES_VALIDA = 'S'
                  AND ROWNUM = 1;

        END IF;
        IF  (Upper(vl_direccion) <> Upper(a.direccion)
            OR Upper(vl_cod_comuna) <> Upper(a.cod_comuna)
            OR Upper(vl_cod_ciudad) <> Upper(a.cod_ciudad)
            OR Upper(vl_cod_pais) <> Upper(a.cod_pais)
            OR Upper(vl_email) <> Upper(a.email)
            OR Upper(vl_telefono) <> Upper(a.telefono)
            OR Upper(vl_celular) <> Upper(a.celular)) THEN

          spu_direccion ( 'INSERT'
                          ,NULL
                          ,vl_cod_deudor
                          ,'P'
                          ,a.direccion
                          ,a.cod_comuna
                          ,a.cod_ciudad
                          ,a.cod_pais
                          ,a.email
                          ,a.telefono
                          ,a.celular
                          ,'S'
                          ,vl_cod_usuario
                          ,null
                          ,null
                          ,null);
        END IF;

        SELECT  Count(*)
        INTO    vl_count
        FROM    deuda
        WHERE   cod_deudor = vl_cod_deudor
                AND cod_tipo_deuda = a.cod_tipo_deuda
                AND cod_estado_deuda = K_NO_EXIGIBLE;

        IF (vl_count > 1) THEN
          -------**ERROR encontro mas de 1 deuda no exigible para 1 tipo deuda **-------
          spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, 'RUT: ' || To_Char(a.rut) || ' Tiene más de una deuda No Exigible del mismo tipo.');
          GOTO end_loop;
        ELSE
          SELECT  Count(*)
          INTO    vl_count
          FROM    deuda
          WHERE   cod_deudor = vl_cod_deudor
                  AND deuda.cod_tipo_deuda    = a.cod_tipo_deuda
                  AND deuda.cod_estado_deuda  = K_NO_EXIGIBLE;

          IF (vl_count = 0) THEN
            SPU_DEUDA(  'INSERT'
                        ,NULL
                        ,f_get_parametro (K_CARTERA_DEFECTO)
                        ,f_get_parametro (K_UNIVERSIDAD_DEFECTO)
                        ,vl_cod_deudor
                        ,a.cod_tipo_deuda);
            SELECT  deuda_sq.CURRVAL
            INTO    vl_cod_deuda
            FROM    DUAL;
          ELSE
            SELECT  cod_deuda
            INTO    vl_cod_deuda
            FROM    deuda
            WHERE   cod_deudor = vl_cod_deudor
                    AND deuda.cod_tipo_deuda    = a.cod_tipo_deuda
                    AND deuda.cod_estado_deuda  = K_NO_EXIGIBLE;
          END IF;

          SELECT   periodo_gracia, tasa_interes_credito
          INTO vl_perido_gracia, vl_tasa_interes_credito
          FROM tipo_deuda
          WHERE cod_tipo_deuda = a.cod_tipo_deuda;

          SELECT fecha_inicio_cobro
          INTO vl_fecha_inicio_cobra_old
          FROM deuda
          WHERE   COD_DEUDA = vl_cod_deuda;
                                        
          vl_fecha_inicio_cobra := Add_Months(To_Date('01/01/' || To_Char(a.fecha_pagare, 'yyyy'), 'dd/mm/yyyy'), (vl_perido_gracia + 12));

          IF (vl_fecha_inicio_cobra > vl_fecha_inicio_cobra_old) THEN
            UPDATE  DEUDA
            SET     FECHA_INICIO_COBRO = vl_fecha_inicio_cobra
            WHERE   COD_DEUDA = vl_cod_deuda;
          
          END IF;

          -- Inserta el pagare en la tabla PAGARE
          SELECT  Count(*)
          INTO    vl_count
          FROM    PAGARE
          WHERE   FOLIO_PAGARE = a.folio_pagare
                  AND FECHA_PAGARE = a.fecha_pagare
                  AND MONTO_MONEDA = a.monto_moneda;


          IF (vl_count = 0) THEN
            INSERT INTO PAGARE( COD_PAGARE
                                ,FOLIO_PAGARE
                                ,FECHA_PAGARE
                                ,COD_DEUDA
                                ,MONTO_MONEDA
                                ,MONTO_PESOS
                                ,COD_ESTADO_PAGARE)
            VALUES            ( pagare_sq.NEXTVAL-- DA UN NRO QUE YA EXISTE
                                ,a.folio_pagare
                                ,a.fecha_pagare
                                ,vl_cod_deuda
                                ,a.monto_moneda
                                ,a.monto_pesos
                                ,f_get_constante('K_EST_PAGARE_VIG'));
          ELSE
            -------**ERROR**-------
            --error se cargo 2 veces el pagare => llamar a sp que registrara error
            spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, 'RUT: ' || To_Char(a.rut) || ' Se cargó dos veces el pagaré con folio: '||a.folio_pagare);
            GOTO end_loop;
          END IF;
          --agregar a bitacora del deudor => llamar a sp que inserte en bitacora
          spu_bitacora_deudor(  'Carga Pagaré'
                                ,K_MODIFICA_PAGARE
                                ,vl_cod_deudor
                                ,vl_cod_usuario);

          sp_deuda_cant_cuota(  vl_cod_deuda);


          -- crear comprobante contable
          /*
          comprobante
                cuenta                debe                       haber
          ____________________________________________________________
          cta_svp            vl_total_suscrito
          cta_univ                                              vl_total_suscrito
          */
          SELECT  COD_FAMILIA_DEUDA
          INTO    vl_cod_familia_deuda
          FROM    TIPO_DEUDA
          WHERE   COD_TIPO_DEUDA = a.cod_tipo_deuda;

          IF (vl_contador_comprobante = 0) THEN
            vl_nom_comprobante  := 'Carga Pagare deudas ' || a.cod_tipo_deuda;
            spu_comprobante(  'INSERT'
                              ,NULL
                              ,SYSDATE
                              ,vl_cod_usuario
                              ,vl_nom_comprobante
                              ,vl_cod_familia_deuda
                              ,K_EST_COMPROBANTE_PDTE
                              ,K_COMPROB_TRASPASO
                              ,K_ORIGEN_CARGA_PAGARE
                              ,vl_cod_usuario
                              ,SYSDATE);

            SELECT  comprobante_sq.CURRVAL
            INTO    vl_cod_comprobante
            FROM    DUAL;

            vl_contador_comprobante := vl_contador_comprobante + 1;

            --Interes parcial
            vl_nom_comprobante  := 'Interes parcial Carga Pagare deudas ' || a.cod_tipo_deuda;
            spu_comprobante(  'INSERT'
                              ,NULL
                              ,SYSDATE
                              ,vl_cod_usuario
                              ,vl_nom_comprobante
                              ,vl_cod_familia_deuda
                              ,K_EST_COMPROBANTE_PDTE
                              ,K_COMPROB_TRASPASO
                              ,K_ORIGEN_CARGA_PAGARE
                              ,vl_cod_usuario
                              ,SYSDATE);


            SELECT  comprobante_sq.CURRVAL
            INTO    vl_cod_comprobante_int
            FROM    DUAL;

          END IF;

          vl_cuenta_svp   := f_comp_get_cuenta_tipo_deuda (A.COD_TIPO_DEUDA, K_SIN_VENCIMIENTO_PACTADO);
          vl_cuenta_univ  := f_comp_get_cuenta_tipo_deuda (A.COD_TIPO_DEUDA, K_INST_EDUCACION_SUPERIOR);

          spu_item_comprobante('INSERT',NULL,vl_cod_comprobante,vl_cuenta_svp,a.monto_pesos,0);
          spu_analitico('INSERT',NULL,vl_cod_comprobante,vl_cod_deuda,vl_cuenta_svp,a.monto_pesos,0);
          spu_item_comprobante('INSERT', NULL, vl_cod_comprobante, vl_cuenta_univ, 0, a.monto_pesos);
          spu_analitico('INSERT',NULL,vl_cod_comprobante,vl_cod_deuda,vl_cuenta_univ,0,a.monto_pesos);


          --- comprobante interes
          vl_cuenta_interes := f_comp_get_cuenta_tipo_deuda (A.COD_TIPO_DEUDA, K_CTA_INTERES_NORMAL);

          vl_diferencia := (To_Date('31/12/'||To_Char(To_Number(To_Char(A.FECHA_PAGARE,'yyyy'))),'dd/mm/yyyy') - A.FECHA_PAGARE) / 365;
          vl_monto_interes := Round(A.MONTO_PESOS * power(1 + vl_tasa_interes_credito / 100, vl_diferencia), 0) - A.MONTO_PESOS;

          spu_item_comprobante('INSERT',NULL,vl_cod_comprobante_int,vl_cuenta_svp,vl_monto_interes,0);
          spu_analitico('INSERT',NULL,vl_cod_comprobante_int,vl_cod_deuda,vl_cuenta_svp,vl_monto_interes,0);
          spu_item_comprobante('INSERT', NULL, vl_cod_comprobante_int, vl_cuenta_interes, 0, vl_monto_interes);
          spu_analitico('INSERT',NULL,vl_cod_comprobante_int,vl_cod_deuda,vl_cuenta_interes,0,vl_monto_interes);


          UPDATE  carga_pagare
          SET     cargado = 'S'
          WHERE   cod_carga_pagare = a.cod_carga_pagare;
        END IF;

        -- actualiza avance
        spu_ejecucion_proceso('SET_ACTUAL', ve_cod_ejecucion_proceso, i);
        COMMIT;

        <<end_loop>>  -- 7not allowed unless an executable statement follows
        NULL; -- add NULL statement to avoid error

        i := i + 1;
      END LOOP;
      sp_comp_simplifica(vl_cod_comprobante);
      sp_comp_simplifica(vl_cod_comprobante_int);
      spu_ejecucion_proceso('END', ve_cod_ejecucion_proceso, i-1);
      COMMIT;

      EXCEPTION
        WHEN OTHERS THEN
        -- Si ocurre un error de la BD al grabar un registro lo registra en el LOG. del sistema
        spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', SQLCODE, SUBSTR(SQLERRM,1,100), 'Rut: ' || To_Char(vl_rut_error));
        COMMIT;

  END;
END spp_cp_carga_pagare;
/

