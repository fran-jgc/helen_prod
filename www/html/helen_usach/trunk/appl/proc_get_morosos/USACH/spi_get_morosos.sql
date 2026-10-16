CREATE OR REPLACE PROCEDURE spi_get_morosos(ve_fecha_corte IN DATE)
AS
/* 
 
 */
/*se crea proceso segun correo enviado por Wilson Jimenez:
-------- Mensaje original --------
Asunto:     Re: informe moroso publicacion
Fecha:     Wed, 7 Jan 2015 10:20:58 -0300
De:     Wilson Jimenez Medina <wilson.jimenez@usach.cl>
Para:     Leandro Fuenzalida <lfuenzalida@integrasystem.cl>


Estimado Leandro:

1. Se deben incorporar todos los tipos de credito, excepto los: CAM, CAME, CD y CS cruzados que el acreedor sea otra universidad (ellos se encargan de enviarlos).

2. El monto minimo es de 0,1 UTM.

Slds.
*/

BEGIN
    DELETE FROM TEMP_MOROSO;

    DECLARE vc_cod_deudor           NUMBER(10,0);
            vc_rut                  NUMBER(10,0);
            vc_dig_verif            VARCHAR(1);
            vc_paterno              VARCHAR(100);
            vc_materno              VARCHAR(100);
            vc_nombre               VARCHAR(100);
            vl_monto_moroso_utm     NUMERIC(10,4);
            vc_cod_estado_pp        NUMBER(10,0);
            vc_cod_deuda            NUMBER(10,0);
            vc_cod_plan_pago        NUMBER(10,0);
            vl_fecha_vencto         DATE;
            ve_cod_deudor           NUMBER(10,0);
            KL_PP_EMITIDA           NUMERIC(10,4):=1;
            KL_PP_ABONADA           NUMERIC(10,4):=2;
            KL_PP_MOROSA            NUMERIC(10,4):=4;
    --cursor moroso
    CURSOR   C_MOROSOS IS
    SELECT   DISTINCT DE.COD_DEUDOR
            ,DE.RUT
            ,DE.DIG_VERIF
            ,DE.APELLIDO_PATERNO
            ,DE.APELLIDO_MATERNO
            ,DE.NOMBRE
    FROM    DEUDOR DE,DEUDA DEU    ,TIPO_DEUDA TD
    WHERE   DE.COD_DEUDOR = DEU.COD_DEUDOR
    AND     TD.COD_TIPO_DEUDA = DEU.COD_TIPO_DEUDA
    AND     TD.COD_FAMILIA_DEUDA = 1
    AND   TD.COD_TIPO_DEUDA not in('CAM', 'CAME', 'CD')
    --AND     TD.COD_TIPO_DEUDA in('CR12', 'CRPI', 'CRI')
    AND     DEU.COD_ESTADO_DEUDA = 2
    AND     DEU.COD_CARTERA = 6
    AND NOT EXISTS (SELECT * FROM CRUZADO WHERE CRUZADO.COD_DEUDA = DEU.COD_DEUDA)
    ;


  CURSOR C_DEUDAS (ve_cod_deudor NUMERIC) IS
  SELECT   COD_DEUDA
  FROM     DEUDA D, TIPO_DEUDA TD
  WHERE    TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
           AND     TD.COD_FAMILIA_DEUDA = 1
           AND     TD.COD_TIPO_DEUDA not in('CAM', 'CAME', 'CD')
           --AND     TD.COD_TIPO_DEUDA in('CR12', 'CRPI', 'CRI')
           AND     D.COD_ESTADO_DEUDA = 2
           AND     D.COD_CARTERA = 6
           AND     COD_DEUDOR = ve_cod_deudor
           AND NOT EXISTS (SELECT * FROM CRUZADO WHERE CRUZADO.COD_DEUDA = D.COD_DEUDA)
           ;

  CURSOR     C_PLAN_PAGO(ve_cod_deuda numeric) IS
    SELECT     COD_PLAN_PAGO
                        ,COD_ESTADO_PLAN_PAGO
                FROM     PLAN_PAGO
                WHERE     COD_DEUDA= ve_cod_deuda;
  
    BEGIN

        FOR a IN C_MOROSOS LOOP

                vl_monto_moroso_utm := 0;

                FOR d IN C_DEUDAS(a.cod_deudor) LOOP
                     
                                FOR c IN C_PLAN_PAGO(d.cod_deuda)LOOP
                                
                                        IF (c.cod_estado_plan_pago = KL_PP_EMITIDA OR c.cod_estado_plan_pago  = KL_PP_ABONADA OR c.cod_estado_plan_pago  = KL_PP_MOROSA) THEN
                                                              
                                              select     FECHA_VENCTO into vl_fecha_vencto
                                            FROM     plan_pago
                                            WHERE     COD_PLAN_PAGO=c.cod_plan_pago;

                                            IF (vl_fecha_vencto < ve_fecha_corte) THEN
                                               vl_monto_moroso_utm := ROUND (vl_monto_moroso_utm + f_pp_saldo_mora(c.cod_plan_pago, SYSDATE) + f_pp_saldo_multa(c.cod_plan_pago, SYSDATE),4);
                                            END IF;

                                        END IF;

                                END LOOP; 
                END LOOP;
--        dbms_output.put_line(vl_monto_moroso_utm);
                IF (vl_monto_moroso_utm > 0.1000) THEN
                    INSERT INTO TEMP_MOROSO
                    (rut
                    ,dig_verif
                    ,paterno
                    ,materno
                    ,nombre
                    ,monto_moroso_utm)
                    VALUES
                    (a.rut
                    ,a.dig_verif
                    ,a.APELLIDO_PATERNO
                    ,a.APELLIDO_MATERNO
                    ,a.nombre
                    ,vl_monto_moroso_utm);
                END IF;

        END LOOP;
    END;
END;