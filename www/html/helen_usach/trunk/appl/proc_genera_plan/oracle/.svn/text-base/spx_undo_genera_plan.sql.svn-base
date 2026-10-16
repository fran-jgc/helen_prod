PROMPT CREATE OR REPLACE PROCEDURE spx_undo_genera_plan
CREATE OR REPLACE PROCEDURE spx_undo_genera_plan
AS
BEGIN
/*
para corregir fecha_inicio_cobro
UPDATE deuda
SET FECHA_INICIO_COBRO = (SELECT To_Date('01/01/' || To_Char(To_Number(To_Char(Max(fecha_pagare), 'yyyy')) + 3),'dd/mm/yyyy') FROM pagare p WHERE p.cod_deuda = deuda.cod_deuda)
WHERE  FECHA_INICIO_COBRO = To_Date('01/01/3000','dd/mm/yyyy')
AND  COD_ESTADO_DEUDA = 1



antes de ejecutar genera plan
          SELECT Sum(f_deuda_saldo_total(d.cod_deuda, To_Date('31/12/' || To_Char(To_Number(To_Char(sysdate, 'yyyy')) - 1), 'dd/mm/yyyy') + 1 - 1/86400)) AS saldo_deuda
    FROM deuda d, tipo_deuda t
    WHERE d.cod_estado_deuda = 1
      AND d.fecha_inicio_cobro <= SYSDATE
      AND d.cod_cartera = f_get_parametro (2)
      AND        t.cod_tipo_deuda = d.cod_tipo_deuda

       454399.1999

SELECT * FROM deuda
WHERE fecha_inicio_cobro = To_Date('01/01/2010','dd/mm/yyyy')
AND cod_estado_deuda = 2
AND cod_cartera = f_get_parametro (2)
AND cod_tipo_deuda = 'CS'
=0 registros
*/
  DELETE plan_pago
  WHERE cod_deuda IN (SELECT cod_deuda FROM deuda
                      WHERE   cod_estado_deuda = 2
                          AND fecha_inicio_cobro =  To_Date('01/01/2010','dd/mm/yyyy')
                          AND cod_cartera = f_get_parametro (2)
                          AND cod_tipo_deuda = 'CS');

  /*por inconsistencia de datos se eliminan los abonos del plan_pago = 670469, realizado por IS
  delete FROM abono WHERE cod_plan_pago = 670469;
  */

  UPDATE deuda
  SET cod_estado_deuda = 1
  WHERE   cod_estado_deuda = 2
      AND fecha_inicio_cobro =  To_Date('01/01/2010','dd/mm/yyyy')
      AND cod_cartera = f_get_parametro (2)
      AND cod_tipo_deuda = 'CS';

COMMIT;
      /*
      OJO se esta perdiendo fecha_inicio_cobro (todas las deudas estan quedando con 01/01/2010
      */

END spx_undo_genera_plan;
/
