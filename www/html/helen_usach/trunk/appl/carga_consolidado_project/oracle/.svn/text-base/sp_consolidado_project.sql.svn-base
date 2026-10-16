CREATE OR REPLACE PROCEDURE sp_consolidado_project (
  ve_cod_usuario   NUMBER
)
AS
BEGIN
  DECLARE
    K_CARTERA_POR_DEFECTO       NUMBER      :=2;
    K_UNIVERSIDAD_POR_DEFECTO   NUMBER      :=3;
    K_DEUDA_NO_EXIGIBLE         NUMBER      :=1;
    K_CARGA_CONSOLIDADO_PROJECT NUMBER      :=16;

    vl_cod_universidad_defecto  NUMBER;
    vl_cod_cartera_defecto      NUMBER;
    vl_fecha                    DATE;
    vl_cod_universidad_cobra    NUMBER;
    vl_count_deudor             NUMBER;
    vl_cod_deudor               NUMBER;
    vl_count_deuda              NUMBER;
    vl_cod_deuda                NUMBER;
    vl_fecha_pagare             DATE;
    vl_cod_cartera_deuda        NUMBER;
    vl_cod_universidad_deuda    NUMBER;
    vl_cod_estado_deuda_deuda   NUMBER;
    vl_fecha_inicio_cobro_deuda DATE;
    vl_cod_pagare               NUMBER;
    vl_cod_ejecucion_proceso    NUMBER;

    vl_cod_log_cambio           NUMBER;
    vl_rut                      NUMBER;
    vl_ano_consolidacion        NUMBER;
    vl_cod_universidad_cobra1   NUMBER;
    vl_cod_universidad_cobra2   NUMBER;
    vl_ano_inicio_cobro         NUMBER;

    vl_cod_universidad_deuda1   NUMBER;
    vl_monto_deuda_consolidada1 NUMBER;
    vl_porc_recauda1            NUMBER;
    vl_cod_error1               VARCHAR2(1);

    vl_cod_universidad_deuda2   NUMBER;
    vl_monto_deuda_consolidada2 NUMBER;
    vl_porc_recauda2            NUMBER;
    vl_cod_error2               VARCHAR2(1);

    vl_cod_universidad_deuda3   NUMBER;
    vl_monto_deuda_consolidada3 NUMBER;
    vl_porc_recauda3            NUMBER;
    vl_cod_error3               VARCHAR2(1);

    vl_cod_universidad_deuda4   NUMBER;
    vl_monto_deuda_consolidada4 NUMBER;
    vl_porc_recauda4            NUMBER;
    vl_cod_error4               VARCHAR2(1);

    vl_cod_universidad_deuda5   NUMBER;
    vl_monto_deuda_consolidada5 NUMBER;
    vl_porc_recauda5            NUMBER;
    vl_cod_error5               VARCHAR2(1);

    CURSOR C_CONSOLIDADO_PROJECT IS
      SELECT  cod_consolidado_project, ano_consolidado_project, spool_consolidado_project
      FROM    consolidado_project;
  BEGIN
    vl_cod_universidad_defecto  := f_get_parametro(K_UNIVERSIDAD_POR_DEFECTO);
    vl_cod_cartera_defecto      := f_get_parametro(K_CARTERA_POR_DEFECTO);
    
    INSERT INTO ejecucion_proceso VALUES (ejecucion_proceso_sq.NEXTVAL, K_CARGA_CONSOLIDADO_PROJECT,ve_cod_usuario,SYSDATE,NULL,0,0,'N',NULL, NULL);
    COMMIT;
    
    SELECT  ejecucion_proceso_sq.CURRVAL
    INTO    vl_cod_ejecucion_proceso
    FROM dual;

    FOR a IN C_CONSOLIDADO_PROJECT LOOP
      vl_rut        := to_number(substr(a.spool_consolidado_project, 1, 9)); -- 1 RUT del Deudor Numérico 9
      /*
        2 Dígito Verificador del RUT Caracter 1
        3 Apellido Paterno Caracter 20
        4 Apellido Materno Caracter 20
        5 Nombres Caracter 20
        6 Dirección Caracter 40
        7 Comuna Caracter 20
        8 Ciudad Caracter 20
        9 Teléfono Caracter 15
        10 Código U. que aportó los datos Numérico 2
        11 Tipo de Deudor Numérico 1
        12 Deuda Total Consolidada Numérico 7 (3Ent/4Dec)
      */
      vl_ano_consolidacion        := To_Number(SubStr(a.spool_consolidado_project, 176 , 4));   -- 13 Año de la Consolidación Numérico 4 (aaaa)
      /*
        14 Situación Determinada Numérico 1
      */
      vl_cod_universidad_cobra1   := To_Number(SubStr(a.spool_consolidado_project, 181 , 2));   -- 15 Código U.1 que cobra Numérico 2
      vl_cod_universidad_cobra2   := To_Number(SubStr(a.spool_consolidado_project, 183 , 2));   -- 16 Código U.2 que cobra Numérico 2
      vl_ano_inicio_cobro         := To_Number(SubStr(a.spool_consolidado_project, 183 , 2));   -- 17 Año inicio de cobro Numérico 4 (aaaa)
      /*
        18 Año último crédito Numérico 4 (aaaa)
        19 Año última matrícula Numérico 4 (aaaa)
      */
      IF (f_isnumeric(SubStr(a.spool_consolidado_project, 197 , 2))='S') THEN 
        vl_cod_universidad_deuda1   := To_Number(SubStr(a.spool_consolidado_project, 197 , 2));   --20 1-Código Universidad con Deuda Numérico 2
        vl_monto_deuda_consolidada1 := To_Number(SubStr(a.spool_consolidado_project, 199 , 3)||'.'||SubStr(a.spool_consolidado_project, 202 , 4));  --21 1-Monto de la Deuda Consolidada Numérico 7 (3Ent/4Dec)
        vl_porc_recauda1            := To_Number(SubStr(a.spool_consolidado_project, 206 , 3)||'.'||SubStr(a.spool_consolidado_project, 209 , 3));  --22 1-Porcentaje para recaudación Numérico 6 (3Ent/3Dec)
        vl_cod_error1               := SubStr(a.spool_consolidado_project, 212 , 1);               --23 1-Código de error Caracter 1
      END IF;
    
      IF (f_isnumeric(SubStr(a.spool_consolidado_project, 213 , 2))='S') THEN
        vl_cod_universidad_deuda2   := To_Number(SubStr(a.spool_consolidado_project, 213 , 2));   --24 2-Código Universidad con Deuda Numérico 2
        vl_monto_deuda_consolidada2 := To_Number(SubStr(a.spool_consolidado_project, 215 , 3)||'.'||SubStr(a.spool_consolidado_project, 218 , 4));  --25 2-Monto de la Deuda Consolidada Numérico 7 (3Ent/4Dec)
        vl_porc_recauda2            := To_Number(SubStr(a.spool_consolidado_project, 222 , 3)||'.'||SubStr(a.spool_consolidado_project, 225 , 3));  --26 2-Porcentaje para recaudación Numérico 6 (3Ent/3Dec)
        vl_cod_error2               := SubStr(a.spool_consolidado_project, 228 , 1);              --27 2-Código de error Caracter 1
      END IF;

      IF (f_isnumeric(SubStr(a.spool_consolidado_project, 229 , 2))='S') THEN
        vl_cod_universidad_deuda3   := To_Number(SubStr(a.spool_consolidado_project, 229 , 2));   --28 3-Código Universidad con Deuda Numérico 2
        vl_monto_deuda_consolidada3 := To_Number(SubStr(a.spool_consolidado_project, 231 , 3)||'.'||SubStr(a.spool_consolidado_project, 234 , 4));  --29 3-Monto de la Deuda Consolidada Numérico 7 (3Ent/4Dec)
        vl_porc_recauda3            := To_Number(SubStr(a.spool_consolidado_project, 238 , 3)||'.'||SubStr(a.spool_consolidado_project, 241 , 3));  --30 3-Porcentaje para recaudación Numérico 6 (3Ent/3Dec)
        vl_cod_error3               := SubStr(a.spool_consolidado_project, 244 , 1);              --31 3-Código de error Caracter 1
      END IF;

      IF (f_isnumeric(SubStr(a.spool_consolidado_project, 245 , 2))='S') then
        vl_cod_universidad_deuda4   := To_Number(SubStr(a.spool_consolidado_project, 245 , 2));   --32 4-Código Universidad con Deuda Numérico 2
        vl_monto_deuda_consolidada4 := To_Number(SubStr(a.spool_consolidado_project, 247 , 3)||'.'||SubStr(a.spool_consolidado_project, 250 , 4));  --33 4-Monto de la Deuda Consolidada Numérico 7 (3Ent/4Dec)
        vl_porc_recauda4            := To_Number(SubStr(a.spool_consolidado_project, 254 , 3)||'.'||SubStr(a.spool_consolidado_project, 257 , 3));  --34 4-Porcentaje para recaudación Numérico 6 (3Ent/3Dec)
        vl_cod_error4               := SubStr(a.spool_consolidado_project, 260 , 1);              --35 4-Código de error Caracter 1
      END IF;

      IF (f_isnumeric(SubStr(a.spool_consolidado_project, 261 , 2))='S') then
        vl_cod_universidad_deuda5   := To_Number(SubStr(a.spool_consolidado_project, 261 , 2));   --36 5-Código Universidad con Deuda Numérico 2
        vl_monto_deuda_consolidada5 := To_Number(SubStr(a.spool_consolidado_project, 263 , 3)||'.'||SubStr(a.spool_consolidado_project, 266 , 4));  --37 5-Monto de la Deuda Consolidada Numérico 7 (3Ent/4Dec)
        vl_porc_recauda5            := To_Number(SubStr(a.spool_consolidado_project, 270 , 3)||'.'||SubStr(a.spool_consolidado_project, 273 , 3));  --38 5-Porcentaje para recaudación Numérico 6 (3Ent/3Dec)
        vl_cod_error5               := SubStr(a.spool_consolidado_project, 276 , 1);              --39 5-Código de error Caracter 1
      END IF;

      vl_fecha := To_Date('31/12/'||To_Char(vl_ano_consolidacion),'dd/mm/yyyy');

      IF (vl_cod_universidad_cobra2 <> 0) THEN
        vl_cod_universidad_cobra :=  vl_cod_universidad_cobra2;
      ELSE
        vl_cod_universidad_cobra :=  vl_cod_universidad_cobra1;
      END IF;

      -- si la UNIV no es acreedor de de la deuda se la salta 
      IF ((vl_cod_universidad_deuda1 <> vl_cod_universidad_defecto)
      AND (vl_cod_universidad_deuda2 <> vl_cod_universidad_defecto)
      AND (vl_cod_universidad_deuda3 <> vl_cod_universidad_defecto)
      AND (vl_cod_universidad_deuda4 <> vl_cod_universidad_defecto)
      AND (vl_cod_universidad_deuda5 <> vl_cod_universidad_defecto)) THEN
          GOTO CONTINUAR;
      END IF;

      SELECT  Count(*)
      INTO    vl_count_deudor
      FROM    deudor
      WHERE   rut = vl_rut;

      IF (vl_count_deudor = 0) THEN
        spu_error_proceso('INSERT', NULL, vl_cod_ejecucion_proceso, 'E',NULL, NULL, 'RUT: ' || To_Char(vl_rut) || ' Deudor Inexistente.');
      ELSE
        SELECT  cod_deudor
        INTO    vl_cod_deudor
        FROM    deudor
        WHERE   rut = vl_rut;

        SELECT  Count(*)
        INTO    vl_count_deuda
        FROM    deuda
        WHERE   cod_deudor = vl_cod_deudor
                AND cod_tipo_deuda = 'CS';

        IF (vl_count_deuda = 0) THEN
          spu_error_proceso('INSERT', NULL, vl_cod_ejecucion_proceso,'E', NULL, NULL, 'RUT: ' || To_Char(vl_rut) || ' deudor sin deuda CS.');
        ELSE
          IF (vl_count_deuda > 1) THEN
            spu_error_proceso('INSERT', NULL ,vl_cod_ejecucion_proceso,'W', NULL, NULL, 'RUT: ' || To_Char(vl_rut) ||' deudor con mas de una deuda CS (se actualizara la ultima).');
            SELECT  Max(cod_pagare)
            INTO    vl_cod_pagare
            FROM    deuda d
                    ,pagare p
            WHERE   cod_deudor = vl_cod_deudor
                    AND cod_tipo_deuda = 'CS'
                    AND d.cod_deuda = p.cod_deuda;

            SELECT  d.cod_deuda
            INTO    vl_cod_deuda
            FROM    deuda d
                    ,pagare p
            WHERE   cod_deudor = vl_cod_deudor
                    AND cod_tipo_deuda = 'CS'
                    AND d.cod_deuda = p.cod_deuda
                    AND cod_pagare = vl_cod_pagare;
          ELSE
            SELECT  cod_deuda
            INTO    vl_cod_deuda
            FROM    deuda
            WHERE   cod_deudor = vl_cod_deudor
                    AND cod_tipo_deuda = 'CS';
          END IF;

          SELECT  cod_cartera
                  ,cod_universidad
                  ,cod_estado_deuda
                  ,fecha_inicio_cobro
          INTO    vl_cod_cartera_deuda
                  ,vl_cod_universidad_deuda
                  ,vl_cod_estado_deuda_deuda
                  ,vl_fecha_inicio_cobro_deuda
          FROM    deuda
          WHERE   cod_deuda = vl_cod_deuda;

          IF (vl_cod_universidad_cobra = vl_cod_universidad_defecto)
            AND (vl_cod_universidad_deuda <> vl_cod_universidad_defecto) THEN
            UPDATE deuda
            SET    cod_universidad = vl_cod_universidad_cobra
                   ,cod_cartera =   vl_cod_cartera_defecto
            WHERE  cod_deuda = vl_cod_deuda;

            sp_log_cambio ('DEUDA_PROJECT', Trim(To_Char(vl_cod_deuda)), ve_cod_usuario, 'U');
            SELECT Max(cod_log_cambio)
            INTO vl_cod_log_cambio
            FROM log_cambio;

            sp_detalle_cambio (vl_cod_log_cambio, 'COD_UNIVERSIDAD', Trim(To_Char(vl_cod_universidad_deuda)),  Trim(To_Char(vl_cod_universidad_cobra)));
            sp_detalle_cambio (vl_cod_log_cambio, 'COD_CARTERA', Trim(To_Char(vl_cod_cartera_deuda)),  Trim(To_Char(vl_cod_cartera_defecto)));
          END IF;
          IF ((vl_cod_estado_deuda_deuda = K_DEUDA_NO_EXIGIBLE)
            AND (vl_ano_inicio_cobro < To_Number(To_Char(SYSDATE,'YYYY')))) THEN

            spu_error_proceso('INSERT', NULL,vl_cod_ejecucion_proceso,'W', NULL, NULL, 'Deuda: ' || To_Char(vl_cod_deuda) ||' deberia ser exigible.');
          END IF;
          IF (vl_cod_estado_deuda_deuda = K_DEUDA_NO_EXIGIBLE ) THEN
            DELETE  CRUZADO
            WHERE   cod_deuda = vl_cod_deuda;

            IF (vl_cod_universidad_deuda1 <> 0) THEN
              INSERT INTO CRUZADO
                ( COD_CRUZADO
                  ,COD_DEUDA
                  ,COD_UNIVERSIDAD
                  ,MONTO_ACTUALIZADO
                  ,PORC
                  ,FECHA_CONSOLIDACION)
              VALUES
                (cruzado_sq.NEXTVAL
                ,vl_cod_deuda
                ,vl_cod_universidad_deuda1
                ,vl_monto_deuda_consolidada1
                ,vl_porc_recauda1
                ,vl_fecha);
            END IF;
            IF (vl_cod_universidad_deuda2 <> 0) THEN
              INSERT INTO CRUZADO
                ( COD_CRUZADO
                  ,COD_DEUDA
                  ,COD_UNIVERSIDAD
                  ,MONTO_ACTUALIZADO
                  ,PORC
                  ,FECHA_CONSOLIDACION)
              VALUES
                (cruzado_sq.NEXTVAL
                ,vl_cod_deuda
                ,vl_cod_universidad_deuda2
                ,vl_monto_deuda_consolidada2
                ,vl_porc_recauda2
                ,vl_fecha);
            END IF;
            IF (vl_cod_universidad_deuda3 <> 0) THEN
              INSERT INTO CRUZADO
                ( COD_CRUZADO
                  ,COD_DEUDA
                  ,COD_UNIVERSIDAD
                  ,MONTO_ACTUALIZADO
                  ,PORC
                  ,FECHA_CONSOLIDACION)
              VALUES
                (cruzado_sq.NEXTVAL
                ,vl_cod_deuda
                ,vl_cod_universidad_deuda3
                ,vl_monto_deuda_consolidada3
                ,vl_porc_recauda3
                ,vl_fecha);
            END IF;
            IF (vl_cod_universidad_deuda4 <> 0) THEN
              INSERT INTO CRUZADO
                ( COD_CRUZADO
                  ,COD_DEUDA
                  ,COD_UNIVERSIDAD
                  ,MONTO_ACTUALIZADO
                  ,PORC
                  ,FECHA_CONSOLIDACION)
              VALUES
                (cruzado_sq.NEXTVAL
                ,vl_cod_deuda
                ,vl_cod_universidad_deuda4
                ,vl_monto_deuda_consolidada4
                ,vl_porc_recauda4
                ,vl_fecha);
            END IF;
            IF (vl_cod_universidad_deuda5 <> 0) THEN
              INSERT INTO CRUZADO
                ( COD_CRUZADO
                  ,COD_DEUDA
                  ,COD_UNIVERSIDAD
                  ,MONTO_ACTUALIZADO
                  ,PORC
                  ,FECHA_CONSOLIDACION)
              VALUES
                (cruzado_sq.NEXTVAL
                ,vl_cod_deuda
                ,vl_cod_universidad_deuda5
                ,vl_monto_deuda_consolidada5
                ,vl_porc_recauda5
                ,vl_fecha);
            END IF;
            COMMIT;
          END IF;
        END IF;
      END IF;
      COMMIT;
      <<CONTINUAR>> NULL;
    END LOOP;
    UPDATE ejecucion_proceso
    SET   fecha_termino = SYSDATE
    WHERE cod_ejecucion_proceso = vl_cod_ejecucion_proceso;
    
    COMMIT;
  END;
END sp_consolidado_project;