CREATE OR REPLACE PROCEDURE spp_cm_carga_matricula( ve_cod_ejecucion_proceso   NUMBER
                                                    ,ve_fecha_carga date)
AS
/******************************************************************************
   NAME:       spp_cm_carga_matricula

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        21/10/2009   SC              Realiza la Carga de las Matriculas.

   NOTES:

******************************************************************************/
BEGIN
  DECLARE
    vl_cod_deudor           DEUDOR.COD_DEUDOR%TYPE;
    vl_perido_gracia        TIPO_DEUDA.PERIODO_GRACIA%TYPE;
    vl_count                NUMBER := 0;
    K_UNIVERSIDAD_DEFECTO   NUMBER := 3;
    K_DEUDA_NO_EXIGIBLE     NUMBER := 1;
    K_DEUDA_EXIGIBLE        NUMBER := 2;
    vl_cod_usuario          NUMBER;
    vl_count_registros      NUMBER;
    i                       NUMBER;
    vl_rut_error            NUMBER;
    vl_ultimo_ano_matricula NUMBER;
    vl_fecha_inicio_cobra	  DATE;
    vl_fecha_inicio_cobra_old DATE;

    CURSOR C_CARGA_MATRICULA IS
      SELECT  COD_CARGA_MATRICULA
              ,COD_CARRERA
              ,RUT
              ,NOM_CARRERA
              ,ANO_MATRICULA
              ,SEMESTRE
      FROM    CARGA_MATRICULA
      WHERE   CARGADO = 'N'
        AND   trunc(FECHA_CARGA) = trunc(ve_fecha_carga);

    CURSOR C_DEUDAS (vl_cod_deudor   NUMBER) IS
      SELECT  COD_DEUDA
      FROM    DEUDA
      WHERE   COD_DEUDOR = vl_cod_deudor
              AND COD_ESTADO_DEUDA = K_DEUDA_NO_EXIGIBLE;

  BEGIN
    -- usuario que ejecuta el proceso
    SELECT cod_usuario
    INTO vl_cod_usuario
    FROM ejecucion_proceso
    WHERE cod_ejecucion_proceso = ve_cod_ejecucion_proceso;

    -- IMPORTANTE: El where de este select debe ser igual al del cursor !!
    -- calcula el total de registros
    SELECT  Count(*)
    INTO    vl_count_registros
    FROM    CARGA_MATRICULA
    WHERE   CARGADO = 'N';

    spu_ejecucion_proceso('SET_TOTAL', ve_cod_ejecucion_proceso, vl_count_registros);
    i := 1;

    FOR a IN C_CARGA_MATRICULA LOOP
      -- verifica si el proceso ha sido cancelado
      IF (f_ep_cancela_proceso(ve_cod_ejecucion_proceso) = 'S') THEN
        COMMIT;
        RETURN;
      END IF;

      SELECT  Count(*)
      INTO    vl_count
      FROM    carrera
      WHERE   cod_carrera = a.cod_carrera;

      IF (vl_count = 0) THEN
        spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, 'RUT: ' || To_Char(a.rut) || ' Codigo Carrera: ' || To_Char(a.cod_carrera) || ' No EXISTE');
      ELSE
        SELECT  Count(*)
        INTO    vl_count
        FROM    DEUDOR
        WHERE   RUT = a.rut;

        vl_rut_error := a.rut ;

        IF (vl_count > 0) THEN
          SELECT  COD_DEUDOR
          INTO    vl_cod_deudor
          FROM    DEUDOR
          WHERE   RUT = a.rut;


          SELECT Count(*)
          INTO vl_count
          FROM MATRICULA
          WHERE cod_deudor =  vl_cod_deudor
            AND ANO_MATRICULA = a.ano_matricula
            AND SEMESTRE = a.semestre;

          IF (vl_count = 0) THEN
            INSERT INTO MATRICULA(  COD_MATRICULA
                                    ,COD_DEUDOR
                                    ,COD_UNIVERSIDAD
                                    ,NOM_CARRERA
                                    ,ANO_MATRICULA
                                    ,SEMESTRE
                                    ,COD_CARRERA)
            VALUES                ( matricula_sq.NEXTVAL
                                    ,vl_cod_deudor
                                    ,f_get_parametro (K_UNIVERSIDAD_DEFECTO)
                                    ,a.nom_carrera
                                    ,a.ano_matricula
                                    ,a.semestre
                                    ,a.cod_carrera);
          ELSE
            UPDATE MATRICULA
            SET COD_UNIVERSIDAD = f_get_parametro (K_UNIVERSIDAD_DEFECTO)
               ,NOM_CARRERA = a.nom_carrera
               ,COD_CARRERA = a.cod_carrera
            WHERE COD_DEUDOR = vl_cod_deudor
              AND ANO_MATRICULA = a.ano_matricula
              AND SEMESTRE = a.semestre;
          END IF;

          SELECT  Count(*)
          INTO    vl_count
          FROM    DEUDA
          WHERE   COD_ESTADO_DEUDA = K_DEUDA_EXIGIBLE
                  AND COD_DEUDOR = vl_cod_deudor;

          IF (vl_count > 0) THEN
            -------**ERROR tiene deuda en estado 01 **-------
            spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'W', NULL, NULL, 'RUT: ' || To_Char(a.rut) || ' posee deuda en estado EXIGIBLE.');
          ELSE
            SELECT Max(ANO_MATRICULA)
            INTO vl_ultimo_ano_matricula
            FROM matricula
            WHERE cod_deudor = vl_cod_deudor;

            FOR B IN C_DEUDAS (vl_cod_deudor) LOOP
              SELECT  TD.PERIODO_GRACIA
              INTO    vl_perido_gracia
              FROM    TIPO_DEUDA  TD
                      ,DEUDA      D
              WHERE   TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
                      AND D.COD_DEUDA = B.COD_DEUDA;

				      SELECT fecha_inicio_cobro
          		INTO vl_fecha_inicio_cobra_old
          		FROM deuda
          		WHERE   COD_DEUDA = B.COD_DEUDA;
                                        
          		vl_fecha_inicio_cobra := Add_Months(To_Date('01/01/' || To_Char(vl_ultimo_ano_matricula), 'dd/mm/yyyy'), (vl_perido_gracia + 12));

          		IF (vl_fecha_inicio_cobra > vl_fecha_inicio_cobra_old) THEN
            		UPDATE  DEUDA
            		SET     FECHA_INICIO_COBRO = vl_fecha_inicio_cobra
            	  WHERE   COD_DEUDA = B.COD_DEUDA;
          
          		END IF;
                      
            END LOOP;

            UPDATE  carga_matricula
            SET     cargado = 'S'
            WHERE   cod_carga_matricula = a.cod_carga_matricula;
          END IF;
        ELSE
          -------**ERROR no existe rut deudor **-------
          spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', NULL, NULL, 'Rut: ' || To_Char(a.rut) || ' no existe.');
        END IF;
      END IF;
      -- actualiza avance
      spu_ejecucion_proceso('SET_ACTUAL', ve_cod_ejecucion_proceso, i);
      i := i + 1;
      COMMIT;

    END LOOP;

    spu_ejecucion_proceso('END', ve_cod_ejecucion_proceso, i - 1);
    COMMIT;

    EXCEPTION
      WHEN OTHERS THEN
      -- Si ocurre un error de la BD al grabar un registro lo registra en el LOG. del sistema
      spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', SQLCODE, SUBSTR(SQLERRM,1,100), 'Matricula de rut : ' || To_Char(vl_rut_error));
      COMMIT;
  END;
END spp_cm_carga_matricula;
/
