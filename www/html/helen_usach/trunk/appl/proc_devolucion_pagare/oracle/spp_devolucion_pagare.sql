create or replace PROCEDURE       spp_devolucion_pagare (
   ve_cod_ejecucion_proceso        NUMBER
)
AS
BEGIN
    DECLARE k_cod_cartera                   NUMBER := 6;
            k_estado_deuda                  NUMBER := 4;
            k_pagare_vigente                NUMBER := 1;
            k_pagare_proc_dev               NUMBER := 6;
            k_univesidad                    NUMBER := 6;
            vl_count_deudas                 NUMBER;
            vl_ano_anterior                 NUMBER;
            vl_ano_actual                   NUMBER;
            vl_total_deudas                 NUMBER;
            vl_i                            NUMBER;
            vl_cod_deuda_err                NUMBER;
            vl_total_pagares                NUMBER;
            vl_tiene_pagares                NUMBER;
            
        CURSOR C_DEUDA(vl_ano NUMBER) IS   SELECT DE.COD_DEUDA
                                            FROM  DEUDA DE,CAMBIO_ESTADO_DEUDA CE
                                            WHERE CE.COD_DEUDA = DE.COD_DEUDA
                                            AND DE.cod_estado_deuda = CE.cod_estado_deuda_nuevo
                                            AND CE.COD_ESTADO_DEUDA_NUEVO = k_estado_deuda
                                            AND COD_CARTERA = k_cod_cartera
                                            AND EXTRACT(YEAR FROM CE.FECHA_CAMBIO_ESTADO_DEUDA)  = vl_ano
                                            AND DE.COD_DEUDA NOT IN(SELECT COD_DEUDA FROM DEVOLUCION_PAGARE)
                                            AND 0<(SELECT  COUNT(*) FROM PAGARE P 
                                                    WHERE  P.COD_ESTADO_PAGARE  IN(k_pagare_vigente,k_pagare_proc_dev) 
                                                     AND P.COD_DEUDA = DE.COD_DEUDA)
                                            GROUP BY DE.COD_DEUDA
                                            ORDER BY DE.COD_DEUDA DESC;
                                
          CURSOR C_PAGARE(vl_cod_deuda NUMBER) IS   SELECT  P.COD_PAGARE ,P.COD_ESTADO_PAGARE 
                                                                    FROM PAGARE P 
                                                                    WHERE  P.COD_DEUDA = vl_cod_deuda
                                                                    AND P.COD_UNIVERSIDAD = k_univesidad
                                                                    GROUP BY P.COD_PAGARE,P.COD_ESTADO_PAGARE;
				
                

    BEGIN
    --SETEO DE AÑO ACTUAL
        SELECT TO_CHAR(SYSDATE,'YYYY')
        INTO vl_ano_actual
        FROM   DUAL;
        
        vl_ano_anterior:=vl_ano_actual-1;
    --TOTAL DEUDAS
        SELECT COUNT(*) INTO VL_TOTAL_DEUDAS 
        FROM (SELECT DE.COD_DEUDA
              FROM  DEUDA DE,CAMBIO_ESTADO_DEUDA CE
              WHERE CE.COD_DEUDA = DE.COD_DEUDA
              AND DE.cod_estado_deuda = CE.cod_estado_deuda_nuevo
              AND CE.COD_ESTADO_DEUDA_NUEVO = k_estado_deuda
              AND COD_CARTERA = k_cod_cartera
              AND EXTRACT(YEAR FROM CE.FECHA_CAMBIO_ESTADO_DEUDA)  = vl_ano_anterior
              AND DE.COD_DEUDA NOT IN(SELECT COD_DEUDA FROM DEVOLUCION_PAGARE)
              AND 0<(SELECT  COUNT(*) FROM PAGARE P 
                      WHERE  P.COD_ESTADO_PAGARE  IN(k_pagare_vigente,k_pagare_proc_dev) 
                       AND P.COD_DEUDA = DE.COD_DEUDA)
              GROUP BY DE.COD_DEUDA);
    
        spu_ejecucion_proceso ('SET_TOTAL', ve_cod_ejecucion_proceso, vl_total_deudas);
        vl_i := 1;
        
        FOR A IN C_DEUDA(vl_ano_anterior) LOOP
            vl_cod_deuda_err:=a.cod_deuda;
            
            vl_total_pagares := 0;
            vl_tiene_pagares := 0;
            FOR P IN C_PAGARE(a.cod_deuda) LOOP
                vl_total_pagares := vl_total_pagares + 1;
            
                IF (p.cod_estado_pagare = K_PAGARE_VIGENTE OR p.cod_estado_pagare = K_PAGARE_PROC_DEV) THEN
                    vl_tiene_pagares := vl_tiene_pagares + 1;
                END IF;
            END LOOP;
            
            IF(vl_total_pagares = vl_tiene_pagares)THEN
            
                spu_devolucion_pagare ('INSERT',NULL,A.cod_deuda,'Automático');
           
                spu_ejecucion_proceso ('SET_ACTUAL', ve_cod_ejecucion_proceso, vl_i);

                vl_i := vl_i + 1;
            END IF;
        END LOOP;
        
        vl_i := vl_i - 1;
        spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, vl_i);

  EXCEPTION
        WHEN OTHERS THEN
        -- Si ocurre un error de la BD al grabar un registro lo registra en el LOG. del sistema
        spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', SQLCODE, SUBSTR(SQLERRM,1,100), 'Deuda: ' || To_Char(vl_cod_deuda_err));
        spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, vl_i);
   END;
END;