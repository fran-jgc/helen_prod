CREATE OR REPLACE PROCEDURE spi_abono(ve_session_ID       VARCHAR2
                                      ,ve_cod_tipo_deuda VARCHAR2
                                      , ve_fecha_desde  DATE
                                      , ve_fecha_hasta  DATE
                                      , ve_cod_familia_deuda VARCHAR2) as
Begin
/*
exec  spi_abono('PRUEBA', 'CS', to_date('21/01/2019', 'dd/mm/yyyy'), to_date('31/12/2020', 'dd/mm/yyyy'));

select * from  INF_ABONO;


notas 14-09-2020
19.359.514 deuda cancelada y en porcentaje el informe entrega 100, y en xls ejemplo viene vacio
17306074 misma situacion, ademas como el abono es a no exiible no viene dato monto_cuota, en vencimiento da vacio y xls viene fecha_inicio_cobro ?



*/

Declare
  vl_fecha_registro     DATE;

  CURSOR c_excedente IS
  SELECT e.cod_abono
        ,ee.nom_estado_excedente
  FROM excedente e, INF_ABONO i, estado_excedente ee
  WHERE e.cod_abono = i.folio
  AND ee.cod_estado_excedente = e.cod_estado_excedente;

  CURSOR c_descuento IS
  SELECT d.cod_abono
        ,d.monto_moneda
        ,d.monto_pesos
  FROM descuento d, INF_ABONO i
  WHERE d.cod_abono = i.folio;

BEGIN

vl_fecha_registro := SYSDATE;

--borra todos los registros de mas de 1 dia
DELETE INF_ABONO
WHERE  FECHA_REGISTRO < SYSDATE - 1;

--borra los registros de la misma sesion
DELETE INF_ABONO
WHERE  SESSION_ID = ve_session_ID;

COMMIT;


--Todos los Abonos independiente del cod_estado_pago
INSERT INTO INF_ABONO
  (COD_INF_ABONO
  ,SESSION_ID
  ,FECHA_REGISTRO
  ,RUT
  ,DV
  ,TIPO_DEUDA
  ,COD_DEUDA
  ,ESTADO_DEUDA
  ,FOLIO
  ,FOLIO_ORSAN
  ,ANO_PAGA
  ,COMPONENTE_DEUDA
  ,FECHA_PAGO
  ,COMPROBANTE
  ,ENTIDAD
  ,TIPO_DOCUMENTO_PAGO
  ,TIPO_ABONO
  ,ESTADO_EXCEDENTE
  ,FECHA_DEPOSITO
  ,PORCENTAJE
  ,P_MONTO_CUOTA
  ,CAP_UTM
  ,INT_MULTA_UTM
  ,TOTAL_UTM
  ,CAP_PESOS
  ,INT_MULTA_PESOS
  ,TOTAL_PESOS
  ,FECHA_INGRESO
  ,DESCUENTO_UTM
  ,DESCUENTO_PESOS
  ,INICIO_COBRO
  ,PORC_USACH
  ,PORC_FONDO
  ,PORC_CF
  ,PORC_CU
  ,VENCIMIENTO
  ,COD_ESTADO_PAGO
)
SELECT  inf_abono_sq.NEXTVAL      --COD_INF_ABONO
        ,ve_session_ID            --SESSION_ID
        ,sysdate --vl_fecha_registro        --FECHA_REGISTRO
        ,de.RUT                   --RUT
        ,de.DIG_VERIF             --DV
        ,da.COD_TIPO_DEUDA        --TIPO_DEUDA
        ,da.COD_DEUDA             --COD_DEUDA
        ,e.NOM_ESTADO_DEUDA       --ESTADO_DEUDA
        ,a.COD_ABONO              --FOLIO
        ,a.FOLIO_CARGA            --FOLIO_ORSAN
        ,p.ANO_COBRO              --ANO_PAGA
        ,cd.NOM_COMPONENTE_DEUDA  --COMPONENTE_DEUDA
        ,a.FECHA_ABONO            --FECHA_PAGO
        ,a.COD_COMPROBANTE        --COMPROBANTE
        ,o.NOM_ORIGEN_PAGO        --ENTIDAD
        ,tdp.NOM_TIPO_DOCUMENTO_PAGO  --TIPO_DOCUMENTO_PAGO
        ,'ABONO CUOTA'            --TIPO_ABONO  siemre queda 'ABONO CUOTA' y se cambia a EXCEDENTE' cuando corresponda mas abajo en el codigo
        ,NULL                     --ESTADO_EXCEDENTE   se llena en loop mas abajo
        ,a.FECHA_DEPOSITO         --FECHA_DEPOSITO
        ,Nvl(c.PORC, 100)         --PORCENTAJE
        ,(p.MONTO_CAPITAL + p.MONTO_INTERES)  --P_MONTO_CUOTA
        ,(a.MONTO_CAPITAL + a.MONTO_INTERES)  --CAP_UTM
        ,a.MONTO_MULTA            --INT_MULTA_UTM
        ,a.MONTO_MONEDA           --TOTAL_UTM
        ,a.MONTO_PESOS - (Round(a.MONTO_MULTA * f_mon_get_valor_moneda(3, a.FECHA_ABONO), 0)) --CAP_PESOS
        ,Round(a.MONTO_MULTA * f_mon_get_valor_moneda(3, a.FECHA_ABONO), 0) --CAP_PESOS
        ,a.MONTO_PESOS            --TOTAL_PESOS
        ,a.FECHA_REGISTRO         --FECHA_INGRESO
        ,NULL                     --DESCUENTO_UTM se llena en loop mas abajo
        ,NULL                     --DESCUENTO_PESOS se llena en loop mas abajo
        ,da.FECHA_INICIO_COBRO    --INICIO_COBRO
        ,ci.PORC                   --PORC_USACH
        ,Nvl(c.PORC, 100)         --PORC_FONDO
        ,cf.PORC_CF               --PORC_CF
        ,cf.PORC_CU               --PORC_CU
        ,p.FECHA_VENCTO           --VENCIMIENTO
        ,a.COD_ESTADO_PAGO        --COD_ESTADO_PAGO
FROM abono a left OUTER JOIN plan_pago p ON p.cod_plan_pago = a.cod_plan_pago
             left OUTER JOIN documento_pago dp ON dp.cod_documento_pago = a.cod_documento_pago
             left OUTER JOIN tipo_documento_pago tdp ON tdp.cod_tipo_documento_pago = dp.cod_tipo_documento_pago
             left OUTER JOIN deposito_documento dd ON dd.cod_documento_pago = dp.cod_documento_pago
    , deuda da left OUTER JOIN cruzado c ON c.cod_deuda = da.cod_deuda AND c.cod_universidad = 6  --usach
               left OUTER JOIN cruzado_interno ci ON ci.cod_deuda = da.cod_deuda AND ci.cod_acreedor_interno = 2  --FONDO INSTITUCIONAL
               left OUTER JOIN cruzado_cfcu cf ON cf.cod_deuda = da.cod_deuda
    , deudor de, componente_deuda cd, origen_pago o, estado_deuda e ,TIPO_DEUDA TD 
WHERE a.fecha_abono BETWEEN ve_fecha_desde AND ve_fecha_hasta
AND da.cod_deuda = a.cod_deuda
AND ('TODAS' = ve_cod_tipo_deuda or da.cod_tipo_deuda = ve_cod_tipo_deuda)
AND de.cod_deudor = da.cod_deudor
AND cd.cod_componente_deuda = a.cod_componente_deuda
AND o.cod_origen_pago = a.cod_origen_pago
AND e.cod_estado_deuda = da.cod_estado_deuda
AND ('TODAS' = ve_cod_tipo_deuda or da.COD_TIPO_DEUDA=td.COD_TIPO_DEUDA)
AND td.COD_FAMILIA_DEUDA=ve_cod_familia_deuda;
--AND  ROWNUM <= 10; 

DELETE INF_ABONO
WHERE cod_estado_pago = 2
and FOLIO NOT IN (SELECT e.cod_abono FROM excedente e WHERE e.cod_estado_excedente IN (1,2) AND e.cod_abono = INF_ABONO.FOLIO);

--excedentes
FOR a IN c_excedente LOOP
  UPDATE INF_ABONO
  SET ESTADO_EXCEDENTE = a.nom_estado_excedente
      ,TIPO_ABONO = 'EXCEDENTE'
  WHERE folio = a.cod_abono;
END LOOP;

--descuentos
FOR a IN c_descuento LOOP
  UPDATE INF_ABONO
  SET DESCUENTO_UTM = a.monto_moneda
      ,DESCUENTO_PESOS = a.monto_pesos
  WHERE folio = a.cod_abono;
END LOOP;

COMMIT;

END;
END spi_abono;