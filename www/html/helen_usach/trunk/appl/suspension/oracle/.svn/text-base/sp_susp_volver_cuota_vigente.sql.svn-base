PROMPT CREATE OR REPLACE PROCEDURE sp_susp_volver_cuota_vigente
CREATE OR REPLACE PROCEDURE sp_susp_volver_cuota_vigente (
   ve_cod_deuda        IN   NUMERIC
 , ve_cod_tipo_deuda   IN   VARCHAR
 , ve_ano_suspension   IN   NUMERIC
 , ve_cod_suspension   IN   NUMERIC
)
AS
/****************************************************************************************************
   NAME:       sp_susp_volver_cuota_vigente

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        13/10/2009   Paula Cea       Vuelve la cuota del plan de pago suspendida a ser vigente.
   2.0        19/11/2009   Sergio Castro   Revisión y Cambios

   NOTAS:


*****************************************************************************************************/
BEGIN
   DECLARE
      vl_num_cuota                            NUMERIC;
      vl_ult_num_cuota                        NUMERIC;
      vl_monto_capital                        NUMERIC;
      vl_monto_interes                        NUMERIC;
      vl_ano_cobro                            NUMERIC;
      vl_nro_cuota                            NUMERIC;
      vl_cod_plan_pago                        NUMERIC;
      vl_max_nro_cuota                        NUMERIC;
      vl_fecha_nueva                          VARCHAR (10);
   BEGIN
      IF (ve_cod_tipo_deuda = 'CSR')
      THEN
         --1. OBTENGO COD_PLAN_PAGO DEL ROW ELIMINADO EN PLAN_PAGO_H
         SELECT cod_plan_pago
           INTO vl_cod_plan_pago
           FROM plan_pago_h
          WHERE origen = 'SUSPENSION'
            AND cod_origen = ve_cod_suspension
            AND ano_cobro = ve_ano_suspension;

         --2.- OBTENGO NRO DE LA CUOTA, FECHA.. QUE SE ELIMINÓ EN PLAN_PAGO_H PARA DESPUÈS UPDETEARLOS
         SELECT cod_plan_pago
              , nro_cuota
              , TO_CHAR (TRUNC (fecha_vencto), 'dd/mm/yyyy') ve_fecha_nueva
              , ano_cobro
           INTO vl_cod_plan_pago
              , vl_num_cuota
              , vl_fecha_nueva
              , vl_ano_cobro
           FROM plan_pago_h
          WHERE origen = 'SUSPENSION'
            AND cod_origen = ve_cod_suspension
            AND cod_plan_pago = vl_cod_plan_pago;

         --3. UPDETEAR NROS DE CUOTA
             --OBTENGO MAX NRO_CUOTA EXISTENTE
         SELECT MAX (nro_cuota)
           INTO vl_max_nro_cuota
           FROM plan_pago
          WHERE cod_deuda = ve_cod_deuda;

         UPDATE plan_pago
         SET nro_cuota = nro_cuota + 1
         WHERE  cod_deuda = ve_cod_deuda
            AND (    nro_cuota >= vl_num_cuota
                 AND nro_cuota < vl_max_nro_cuota);

         --4. updetear suspensión suspendida-->volverla a ser vigente
         UPDATE plan_pago
         SET nro_cuota = vl_num_cuota
           , ano_cobro = vl_ano_cobro
           , fecha_vencto = TO_DATE (vl_fecha_nueva, 'dd/mm/yyyy')
         WHERE  cod_deuda = ve_cod_deuda
            AND cod_plan_pago = vl_cod_plan_pago;
      ELSIF (ve_cod_tipo_deuda = 'CS')
      THEN
         --1. UPDETEO VALORES DE CUOTA 9999
         --2. UPDETEO NRO_CUOTA
         --3. VEO EN PLAN_PAGO_H CUÁL ES LA SUSPENSION QUE FALTA Y LA INGRESO

         /*1*/
         SELECT monto_capital
              , monto_interes
              , nro_cuota
           INTO vl_monto_capital
              , vl_monto_interes
              , vl_nro_cuota
           FROM plan_pago_h
          WHERE cod_deuda = ve_cod_deuda
            AND origen = 'SUSPENSION'
            AND cod_origen = ve_cod_suspension
            AND ano_cobro = ve_ano_suspension;

         UPDATE plan_pago
         SET monto_capital = monto_capital - vl_monto_capital
           , monto_interes = monto_interes - vl_monto_interes
         WHERE  cod_deuda = ve_cod_deuda
            AND nro_cuota = 9999;

         UPDATE plan_pago
         SET nro_cuota = nro_cuota + 1
         WHERE  cod_deuda = ve_cod_deuda
            AND (    nro_cuota >= vl_nro_cuota
                 AND nro_cuota < 9999);

         /*3 */
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
            ,PUBLICA_DICOM
            )
            SELECT plan_pago_h_sq.NEXTVAL
                 , cod_deuda
                 , nro_cuota
                 , ano_cobro
                 , monto_capital
                 , monto_interes
                 , fecha_vencto
                 , cod_concepto_cobro
                 , cod_estado_plan_pago
                 , NULL   -- cod_empresa_cobranza
                 , 'N'    -- publica_dicom
              FROM plan_pago_h
             WHERE cod_deuda = ve_cod_deuda
               AND origen = 'SUSPENSION'
               AND cod_origen = ve_cod_suspension
               AND ano_cobro = ve_ano_suspension;
      END IF;
   END;
END;
/
