create or replace PROCEDURE sp_conta_cierre(ve_ano_cierre            number
                                ,ve_cod_familia        number
                                ,ve_cod_usuario        number
                                ,ve_cod_ejecucion_proceso number)
-- Crea el comprobante de apertura para el a?o = @ve_ano_cierre + 1
AS
BEGIN
    declare
        K_ESTADO_COMP_PENDIENTE    NUMBER  := 1;
        K_TIPO_COMP_TRASPASO    NUMBER  := 3;
        K_ORIGEN_CIERRE            NUMBER := 24;
        vl_nom_comprobante        varchar2(100);
        vl_fecha_comprobante    DATE;
        vl_diferencia            NUMBER;
           vl_fecha_comp              varchar2(20);
          vl_cod_comprobante         NUMBER;
          vl_cod_item_comprobante NUMBER;
          vl_monto_debe            NUMBER;
        vl_monto_haber            NUMBER;
           vl_cod_cuenta_contable     NUMBER;
        vl_count_registros NUMBER;
        i NUMBER;

    CURSOR C_PAGARE IS
    SELECT COD_CUENTA_CONTABLE
            ,case COD_TIPO_CUENTA_CONTABLE
            when 1 then case sign(MONTO_DEBE - MONTO_HABER)
                                when 1 then MONTO_DEBE - MONTO_HABER
                                else 0
                            end
            when 2 then case sign(MONTO_DEBE - MONTO_HABER)
                                when 1 then MONTO_DEBE - MONTO_HABER
                                else 0
                            end
            else 0
            end ACTIVO
            ,case COD_TIPO_CUENTA_CONTABLE
            when 2 then case sign(MONTO_HABER - MONTO_DEBE)
                                when 1 then MONTO_HABER - MONTO_DEBE
                                else 0
                            end
            when 1 then case sign(MONTO_HABER - MONTO_DEBE)
                                when 1 then MONTO_HABER - MONTO_DEBE
                                else 0
                            end
            else 0
            end PASIVO
           FROM    (SELECT    CTA.COD_CUENTA_CONTABLE
                    ,SUM(IT.MONTO_DEBE) MONTO_DEBE
                    ,SUM(IT.MONTO_HABER) MONTO_HABER
                    ,CTA.COD_TIPO_CUENTA_CONTABLE
        FROM    COMPROBANTE C, ITEM_COMPROBANTE IT, CUENTA_CONTABLE CTA
        WHERE    C.COD_FAMILIA_DEUDA = ve_cod_familia
        AND        C.COD_ESTADO_COMPROBANTE IN (1,3)    --**** OJO solo los cotabilizados
        AND c.fecha_comprobante BETWEEN To_Date('01/01/'||ve_ano_cierre,'dd/mm/yyyy') AND To_Date('31/12/'||ve_ano_cierre||' 23:59:59','dd/mm/yyyy HH24:MI:SS')
        AND        IT.COD_COMPROBANTE = C.COD_COMPROBANTE
        AND        CTA.COD_CUENTA_CONTABLE = IT.COD_CUENTA_CONTABLE
        GROUP BY CTA.COD_CUENTA_CONTABLE
                    ,CTA.COD_TIPO_CUENTA_CONTABLE) A
        order by     COD_CUENTA_CONTABLE;

    BEGIN

     -- IMPORTANTE: El where de este select debe ser igual al del cursor !!
     -- calcula el total de registros
     SELECT Count(*)
     INTO vl_count_registros
      FROM    (SELECT    CTA.COD_CUENTA_CONTABLE
                    ,SUM(IT.MONTO_DEBE) MONTO_DEBE
                    ,SUM(IT.MONTO_HABER) MONTO_HABER
                    ,CTA.COD_TIPO_CUENTA_CONTABLE
        FROM    COMPROBANTE C, ITEM_COMPROBANTE IT, CUENTA_CONTABLE CTA
        WHERE    C.COD_FAMILIA_DEUDA = ve_cod_familia
        AND        C.COD_ESTADO_COMPROBANTE IN (1,3)    --**** OJO solo los cotabilizados
        AND c.fecha_comprobante BETWEEN To_Date('01/01/'||ve_ano_cierre,'dd/mm/yyyy') AND To_Date('31/12/'||ve_ano_cierre||' 23:59:59','dd/mm/yyyy HH24:MI:SS')
        AND        IT.COD_COMPROBANTE = C.COD_COMPROBANTE
        AND        CTA.COD_CUENTA_CONTABLE = IT.COD_CUENTA_CONTABLE
        GROUP BY CTA.COD_CUENTA_CONTABLE
                    ,CTA.COD_TIPO_CUENTA_CONTABLE) A
        order by     COD_CUENTA_CONTABLE;

    spu_ejecucion_proceso ('SET_TOTAL', ve_cod_ejecucion_proceso, vl_count_registros);
    i                   := 1;

    if(vl_count_registros > 0)then
        vl_nom_comprobante := 'Comprobante apertura a?o '|| to_char(ve_ano_cierre + 1);
        vl_fecha_comp := '01/01' || To_Char(ve_ano_cierre + 1);
        vl_fecha_comprobante := To_Date(vl_fecha_comp, 'dd/mm/yyyy');
        spu_comprobante('INSERT', NULL, vl_fecha_comprobante, ve_cod_usuario, vl_nom_comprobante, ve_cod_familia, K_ESTADO_COMP_PENDIENTE, K_TIPO_COMP_TRASPASO, K_ORIGEN_CIERRE, ve_cod_usuario, vl_fecha_comprobante);
    
        SELECT  comprobante_sq.CURRVAL
        INTO    vl_cod_comprobante
        FROM    DUAL;
    
        FOR a IN C_PAGARE LOOP
    
        vl_cod_cuenta_contable  := a.COD_CUENTA_CONTABLE;
        vl_monto_debe            := a.ACTIVO;
        vl_monto_haber            := a.PASIVO;
    
        INSERT INTO ITEM_COMPROBANTE
            (COD_ITEM_COMPROBANTE
               ,COD_COMPROBANTE
            ,COD_CUENTA_CONTABLE
            ,MONTO_DEBE
            ,MONTO_HABER
            )
        VALUES
            (item_comprobante_sq.NEXTVAL
            ,vl_cod_comprobante
            ,vl_cod_cuenta_contable
            ,vl_monto_debe
            ,vl_monto_haber);
    
        -- actualiza avance
            spu_ejecucion_proceso ('SET_ACTUAL', ve_cod_ejecucion_proceso, i);
            i := i + 1;
    
        END LOOP;
    
        -- borra las lineas vacias
        DELETE ITEM_COMPROBANTE
        where COD_COMPROBANTE = vl_cod_comprobante
        AND MONTO_DEBE = 0
        AND MONTO_HABER = 0;
    
        -- contabiliza la perdida o ganacia
        select SUM(monto_debe) - SUM(monto_haber)
          INTO vl_diferencia
        from ITEM_COMPROBANTE
        where COD_COMPROBANTE = vl_cod_comprobante;
    
        IF (vl_diferencia > 0) THEN
             vl_monto_debe := 0;
             vl_monto_haber := vl_diferencia;
    
        ELSE
             vl_monto_debe    := - vl_diferencia;
             vl_monto_haber    := 0;
        END IF;
    
        INSERT INTO ITEM_COMPROBANTE
            (COD_ITEM_COMPROBANTE
               ,COD_COMPROBANTE
            ,COD_CUENTA_CONTABLE
            ,MONTO_DEBE
            ,MONTO_HABER
            )
        VALUES
            (item_comprobante_sq.NEXTVAL
            ,vl_cod_comprobante
            ,22400
            ,vl_monto_debe
            ,vl_monto_haber);
    
        -- crea los analiticos
        insert into ANALITICO_COMPROBANTE
            (COD_ANALITICO_COMPROBANTE
            ,COD_COMPROBANTE
            ,COD_DEUDA
            ,COD_CUENTA_CONTABLE
            ,COD_DEUDOR
            ,MONTO_DEBE
            ,MONTO_HABER
            ,MONTO_DEBE_ORIGINAL
            ,MONTO_HABER_ORIGINAL
            )
        select analitico_comprobante_sq.NEXTVAL
            ,COD_COMPROBANTE
            ,null            -- COD_DEUDA
            ,COD_CUENTA_CONTABLE
            ,null            --COD_DEUDOR
            ,MONTO_DEBE
            ,MONTO_HABER
            ,null            --MONTO_DEBE_ORIGINAL
            ,null            --MONTO_HABER_ORIGINAL
        from    ITEM_COMPROBANTE
        where    COD_COMPROBANTE = vl_cod_comprobante;
    
        spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, i-1);
    end if;
         

  END;
END sp_conta_cierre;