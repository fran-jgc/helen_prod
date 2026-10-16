PROMPT CREATE OR REPLACE PROCEDURE sp_susp_suspende_cuota
CREATE OR REPLACE PROCEDURE sp_susp_suspende_cuota (
   ve_cod_deuda        IN   NUMBER
 , ve_cod_tipo_deuda   IN   VARCHAR
 , ve_ano_suspension   IN   NUMBER
)
AS
/***************************************************************************************************************
   NAME:       sp_susp_suspende_cuota

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        13/10/2009   Paula Cea       SP que suspende la cuota del plan pago de un año y deuda determinado.
   2.0        19/11/2009   Sergio Castro   Revisión y Cambios
   NOTAS:


****************************************************************************************************************/
BEGIN
   DECLARE
      vl_num_cuota            NUMBER;
      vl_prox_ano             NUMBER;
      vl_ult_num_cuota        NUMBER;
      vl_monto_capital        NUMBER;
      vl_monto_interes        NUMBER;
      vl_cod_plan_pago        NUMBER;
      vl_fecha1               VARCHAR (10);
      vl_fecha_nueva          VARCHAR (10);
      vl_ano1                 VARCHAR (4);
      vl_cod_plan_9999        plan_pago.cod_plan_pago%TYPE;
      K_COMP_DEUDA_FUTURA     NUMBER:= 4;
      K_ESTADO_PP_SUSPENDIDA  NUMBER:= 6;
      K_ESTADO_PP_EMITIDA     NUMBER:=1;
      vl_tasa_interes_credito NUMBER(5,2);
   BEGIN
      -- OBTENGO NRO DE LA CUOTA QUE SE ELIMINARÁ
      SELECT  nro_cuota
              ,cod_plan_pago
      INTO    vl_num_cuota
              ,vl_cod_plan_pago
      FROM    plan_pago
      WHERE   cod_deuda = ve_cod_deuda
              AND ano_cobro = ve_ano_suspension;

      IF (ve_cod_tipo_deuda = 'CSR' OR ve_cod_tipo_deuda = 'CSN') THEN
        --AÑO QUE CONTINUARIA EN EL PLAN PAGO
        --NRO_CUOTA MAXIMA EN EL PLAN PAGO, SE USARÁ EN EL FOR
        SELECT  Max(ano_cobro) + 1
                ,Max(nro_cuota) + 1
        INTO    vl_prox_ano
                ,vl_ult_num_cuota
        FROM    plan_pago
        WHERE   cod_deuda = ve_cod_deuda
                AND cod_estado_plan_pago <> K_ESTADO_PP_SUSPENDIDA;

        --FECHAS A REEMPLAZAR
        SELECT To_Char(Trunc(fecha_vencto), 'dd/mm/yyyy')
        INTO   vl_fecha1
        FROM   plan_pago
        WHERE  cod_deuda = ve_cod_deuda
               AND ano_cobro = ve_ano_suspension
               AND cod_estado_plan_pago <> K_ESTADO_PP_SUSPENDIDA;

        SELECT SubStr(To_Char(Trunc(fecha_vencto), 'dd/mm/yyyy'), 7)
        INTO   vl_ano1
        FROM   plan_pago
        WHERE  cod_deuda = ve_cod_deuda
               AND ano_cobro = ve_ano_suspension
               AND cod_estado_plan_pago <> K_ESTADO_PP_SUSPENDIDA;

         --nueva fecha
        SELECT REPLACE(vl_fecha1,vl_ano1,vl_prox_ano)
        INTO   vl_fecha_nueva
        FROM   DUAL;

        
        SELECT  monto_capital
                ,monto_interes
        INTO    vl_monto_capital
                ,vl_monto_interes
        FROM    plan_pago
        WHERE   cod_deuda = ve_cod_deuda
        AND     ano_cobro = ve_ano_suspension;


        -- Se Crea un nuevo plan de pago en estado suspendido
        INSERT INTO plan_pago
                    (COD_PLAN_PAGO
                    ,COD_DEUDA
                    ,NRO_CUOTA
                    ,ANO_COBRO
                    ,MONTO_CAPITAL
                    ,MONTO_INTERES
                    ,FECHA_VENCTO
                    ,COD_CONCEPTO_COBRO
                    ,COD_ESTADO_PLAN_PAGO
                    ,COD_EMPRESA_COBRANZA
                    ,PUBLICA_DICOM)
                    (SELECT  PLAN_PAGO_SQ.NEXTVAL
                            ,ve_cod_deuda
                            ,vl_num_cuota
                            ,ve_ano_suspension
                            ,0
                            ,0
                            ,To_Date(vl_fecha1,'dd/mm/yyyy')
                            ,null
                            ,K_ESTADO_PP_SUSPENDIDA
                            ,NULL
                            ,'N'
                    FROM    dual);

        SELECT  tasa_interes_credito
        INTO    vl_tasa_interes_credito
        FROM    tipo_deuda
        WHERE   cod_tipo_deuda = ve_cod_tipo_deuda;
   
        vl_monto_interes := vl_monto_interes + (vl_monto_capital * power(1+vl_tasa_interes_credito/100, (vl_prox_ano - ve_ano_suspension)));

        UPDATE plan_pago
        SET    nro_cuota = vl_ult_num_cuota
               ,ano_cobro = vl_prox_ano
               ,fecha_vencto = To_Date(vl_fecha_nueva, 'dd/mm/yyyy')
               ,cod_estado_plan_pago = K_ESTADO_PP_EMITIDA
               ,monto_capital =  vl_monto_capital
               ,monto_interes =  vl_monto_interes
        WHERE  cod_deuda = ve_cod_deuda
               AND ano_cobro = ve_ano_suspension
               AND cod_estado_plan_pago <> K_ESTADO_PP_SUSPENDIDA;

      ELSIF (ve_cod_tipo_deuda = 'CS') THEN
        SELECT  monto_capital
                ,monto_interes
        INTO    vl_monto_capital
                ,vl_monto_interes
        FROM    plan_pago
        WHERE   cod_plan_pago = vl_cod_plan_pago;

        UPDATE  plan_pago
        SET     monto_capital = monto_capital + vl_monto_capital
                ,monto_interes = monto_interes + vl_monto_interes
        WHERE   cod_deuda = ve_cod_deuda
                AND ano_cobro = 9999;

         --update de tablas relacionadas
         UPDATE plan_pago_renegociacion
         SET    cod_plan_pago = NULL
         WHERE  cod_plan_pago = vl_cod_plan_pago;

         UPDATE boleta
         SET    cod_plan_pago = NULL
         WHERE  cod_plan_pago = vl_cod_plan_pago;

         SELECT cod_plan_pago
         INTO   vl_cod_plan_9999
         FROM   plan_pago
         WHERE  cod_deuda = ve_cod_deuda
                AND ano_cobro = 9999;

         UPDATE abono
         SET    cod_plan_pago = vl_cod_plan_9999
                ,cod_componente_deuda = K_COMP_DEUDA_FUTURA
         WHERE  cod_plan_pago = vl_cod_plan_pago;

         UPDATE descuento
         SET    cod_plan_pago = vl_cod_plan_9999
                ,cod_componente_deuda = K_COMP_DEUDA_FUTURA
         WHERE  cod_plan_pago = vl_cod_plan_pago;

         sp_pp_actualiza_estado (vl_cod_plan_9999);

         --Actualiza el plan de pago suspendido
         UPDATE PLAN_PAGO
         SET    MONTO_CAPITAL         = 0
                ,MONTO_INTERES        = 0
                ,COD_ESTADO_PLAN_PAGO = K_ESTADO_PP_SUSPENDIDA
         WHERE  COD_PLAN_PAGO = vl_cod_plan_pago;

      END IF;
   END;
END;
/
