PROMPT CREATE OR REPLACE PROCEDURE spu_suspension
CREATE OR REPLACE PROCEDURE spu_suspension( ve_operacion              IN  VARCHAR
                                            ,ve_cod_suspension        IN  NUMBER  := NULL
                                            ,ve_fecha_suspension      IN  VARCHAR := NULL
                                            ,ve_cod_usuario           IN  NUMBER  := NULL
                                            ,ve_cod_deuda             IN  NUMBER  := NULL
                                            ,ve_ano_suspension        IN  NUMBER  := NULL
                                            ,ve_cod_tipo_suspension   IN  NUMBER  := NULL
                                            ,ve_obs                   IN  VARCHAR := NULL
                                            ,ve_cod_comprobante       IN  NUMBER  := NULL
                                            ,ve_cod_estado_suspension IN  NUMBER  := NULL
                                            ,ve_cod_usuario_confirma  IN  NUMBER  := NULL
                                            ,ve_cod_tipo_deuda        IN  VARCHAR := NULL
                                            ,ve_fecha_solicitud_tramite IN  VARCHAR := NULL
                                            ,ve_monto_utm       	  IN  NUMBER  := NULL
                                            ,ve_monto_pesos       	  IN  NUMBER  := NULL
                                          )
AS
/******************************************************************************
   NAME:       spu_suspension

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        13/10/2009   Paula Cea       crea y modifica una suspensión
   2.0        19/11/2009   Sergio Castro   Revision y Modificaciones

   NOTAS:
   CONDICIONES DEL UPDATE: CUANDO CAMBIAN LOS ESTADOS DE LA SUSPENSION

         solicitada (1) ->  aprobada (2)
                debe suspender la cuota del año y crear un comprobante contable
         solicitada (1) -> anulada (4)
                nada
         aprobada -> anulada
                volver la cuota a vigente y crear comprobante contable


******************************************************************************/
BEGIN
  DECLARE
    K_ESTADO_SOLICITADA           NUMBER:= 1;
    K_ESTADO_APROBADA             NUMBER:= 2;
    K_COD_TIPO_COMP_TRASP         NUMBER:= 3;
    k_comprobante_suspension      NUMBER:= 3;
    K_SUSPENSION_CUOTA            NUMBER:= 8;
    K_EST_PP_SUSPENDIDA           NUMBER:= 6;

    vl_cod_deudor                 NUMBER;
    vl_fecha_comprobante          VARCHAR (10);
    vl_cod_familia_deuda          NUMBER;
    vl_cod_plan_pago              NUMBER;
    vl_cod_comprobante_new        NUMBER;
    vl_cod_comprobante_suspension NUMBER;
    vl_cod_estado_susp_antiguo    suspension.cod_estado_suspension%TYPE;
    vl_count                      NUMBER;
    vl_fecha_confirma             DATE;
    vl_cod_usuario_confirma       NUMBER;
    vl_nro_cuota                  NUMBER;
    vl_fecha_vencto               DATE;

  BEGIN
    IF (ve_cod_estado_suspension = K_ESTADO_APROBADA) THEN
      vl_cod_usuario_confirma := ve_cod_usuario_confirma;
      vl_fecha_confirma       := SYSDATE;
    ELSE
      vl_cod_usuario_confirma := NULL;
      vl_fecha_confirma       := NULL;
    END IF;

    SELECT  TO_CHAR (TRUNC (SYSDATE), 'dd/mm/yyyy')
    INTO    vl_fecha_comprobante
    FROM    DUAL;

    SELECT  td.cod_familia_deuda
            ,d.cod_deudor
    INTO    vl_cod_familia_deuda
            ,vl_cod_deudor
    FROM    deuda d
            ,tipo_deuda td
            ,familia_deuda fd
    WHERE   d.cod_tipo_deuda = td.cod_tipo_deuda
            AND td.cod_familia_deuda = fd.cod_familia_deuda
            AND d.cod_deuda = ve_cod_deuda;

    IF (ve_operacion = 'INSERT') THEN
      INSERT INTO suspension
                  (cod_suspension
                  ,fecha_suspension
                  ,cod_usuario
                  ,cod_deuda
                  ,ano_suspension
                  ,cod_tipo_suspension
                  ,obs
                  ,cod_comprobante
                  ,cod_estado_suspension
                  ,cod_usuario_confirma
                  ,fecha_confirma
                  ,fecha_solicitud_tramite
                  ,monto_utm
                  ,monto_pesos)
      VALUES      (suspension_sq.NEXTVAL
                  ,TO_DATE (ve_fecha_suspension, 'dd/mm/yyyy')
                  ,ve_cod_usuario
                  ,ve_cod_deuda
                  ,ve_ano_suspension
                  ,ve_cod_tipo_suspension
                  ,ve_obs
                  ,ve_cod_comprobante
                  ,ve_cod_estado_suspension
                  ,vl_cod_usuario_confirma
                  ,vl_fecha_confirma
                  ,TO_DATE(ve_fecha_solicitud_tramite, 'dd/mm/yyyy')
                  ,ve_monto_utm
                  ,ve_monto_pesos);
    ELSIF (ve_operacion = 'UPDATE') THEN
      SELECT  cod_estado_suspension
      INTO    vl_cod_estado_susp_antiguo
      FROM    suspension
      WHERE   cod_suspension = ve_cod_suspension;

      UPDATE  suspension
      SET     ano_suspension = ve_ano_suspension
              ,cod_tipo_suspension = ve_cod_tipo_suspension
              ,obs = ve_obs
              ,cod_comprobante = ve_cod_comprobante
              ,cod_estado_suspension = ve_cod_estado_suspension
              ,cod_usuario_confirma = vl_cod_usuario_confirma
              ,fecha_confirma = vl_fecha_confirma
              ,fecha_solicitud_tramite = TO_DATE(ve_fecha_solicitud_tramite, 'dd/mm/yyyy')
      WHERE   cod_suspension = ve_cod_suspension;

      IF ((vl_cod_estado_susp_antiguo = K_ESTADO_SOLICITADA)
          AND (ve_cod_estado_suspension = K_ESTADO_APROBADA)) THEN

        --verifica si existe PP asociado
        SELECT  Count(*)
        INTO    vl_count
        FROM    plan_pago
        WHERE   cod_deuda = ve_cod_deuda
                AND ano_cobro = ve_ano_suspension;

        /*
        si no tiene PP, debe crearlo con valores en cero, pero solo si el tipo de deuda es CS.
        */
        IF(vl_count = 0 AND ve_cod_tipo_deuda = 'CS')THEN

          SELECT  nvl(Max(nro_cuota),0)
          INTO    vl_nro_cuota
          FROM    plan_pago
          WHERE   cod_deuda = ve_cod_deuda
          AND     nro_cuota <> 99
          ORDER BY nro_cuota DESC ;

          vl_nro_cuota:= vl_nro_cuota + 1;

          vl_fecha_vencto := To_date('31/12/'||To_Char(ve_ano_suspension), 'dd/mm/yyyy');

          -- llamar a spu_plan_pago()
          spu_plan_pago('INSERT'
                        ,NULL
                        ,ve_cod_deuda
                        ,vl_nro_cuota
                        ,ve_ano_suspension
                        ,0
                        ,0
                        ,vl_fecha_vencto
                        ,NULL
                        ,K_EST_PP_SUSPENDIDA
                        ,NULL
                        ,NULL);

        END IF;

        SELECT  cod_plan_pago
        INTO    vl_cod_plan_pago
        FROM    plan_pago
        WHERE   cod_deuda = ve_cod_deuda
                AND ano_cobro = ve_ano_suspension;


        -- traspaso a PLAN_PAGO_H todas las cuotas de la deuda
        sp_deuda_plan_pago_h( ve_cod_deuda
                              ,'SUSPENSION'
                              ,ve_cod_suspension);

        /*Creacion del Comprobante para la Suspensión Aprobada*/

        /*
        si no tiene PP, debe crearlo con valores en cero, pero solo si el tipo de deuda es CS.
        */
        IF(vl_count = 0 AND ve_cod_tipo_deuda = 'CS')THEN
          sp_susp_comprobante_cat(  NULL
                              ,SYSDATE
                              ,ve_cod_usuario
                              ,vl_cod_familia_deuda
                              ,K_COMPROBANTE_SUSPENSION
                              ,ve_cod_tipo_deuda
                              ,vl_cod_plan_pago
                              ,ve_cod_deuda
                              ,ve_cod_suspension);
        ELSE
          sp_susp_comprobante(  NULL
                    ,SYSDATE
                    ,ve_cod_usuario
                    ,vl_cod_familia_deuda
                    ,K_COMPROBANTE_SUSPENSION
                    ,ve_cod_usuario
                    ,SYSDATE
                    ,ve_cod_tipo_deuda
                    ,vl_cod_plan_pago
                    ,ve_cod_deuda
                    ,ve_cod_suspension);
        END IF;


        --Suspende el plan de pago relacionado a la fecha de suspensión
        sp_susp_suspende_cuota( ve_cod_deuda
                                ,ve_cod_tipo_deuda
                                ,ve_ano_suspension);

        spu_bitacora_deudor ('Suspensión año: ' || To_Char(ve_ano_suspension), K_SUSPENSION_CUOTA, vl_cod_deudor, vl_cod_usuario_confirma);
      END IF;
    END IF;
  END;
END;
/

