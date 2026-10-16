CREATE OR REPLACE PROCEDURE spi_pronto_pago(ve_fecha_inicio     DATE
                                           ,ve_fecha_termino    DATE
                                           ,ve_cod_tipo_deuda   varchar2)
AS
BEGIN
  DECLARE
    K_ABONO_VIGENTE                 NUMBER:=1;
    K_COMPONENTE_NO_EXIGIBLE        NUMBER:=1;
    K_COMPONENTE_FUTURA             NUMBER:=4;

    vl_count                        NUMBER;
    vl_monto_mora						        NUMBER;
	  vl_monto_multa					        NUMBER;
	  vl_monto_capital_vigente			  NUMBER;
	  vl_monto_interes_vigente			  NUMBER;
	  vl_monto_capital_futuro			    NUMBER;
	  vl_monto_interes_futuro			    NUMBER;
	  vl_monto_capital_no_exigible		NUMBER;
	  vl_monto_interes_no_exgigible		NUMBER;
	  vl_valor_moneda		NUMBER;
	  vl_valor_pesos		NUMBER;

    CURSOR c_abono(ve_cod_componente_deuda NUMBER) IS
    SELECT REPLACE(To_Char(de.rut, '99,999,999'), ',', '.') || '-' || de.dig_verif rut
          ,Trunc(a.fecha_abono) fecha_pago
          ,d.cod_tipo_deuda
          ,a.monto_capital + a.monto_interes monto_abono
          ,a.fecha_abono
    FROM abono a, deuda d, deudor de
    WHERE a.fecha_abono BETWEEN ve_fecha_inicio AND ve_fecha_termino
      AND a.cod_estado_pago = K_ABONO_VIGENTE
      AND a.cod_componente_deuda = ve_cod_componente_deuda
      AND d.cod_deuda = a.cod_deuda
      AND d.cod_tipo_deuda = ve_cod_tipo_deuda
      AND de.cod_deudor = d.cod_deudor;

    CURSOR c_dscto(ve_cod_componente_deuda NUMBER)  IS
    SELECT REPLACE(To_Char(de.rut, '99,999,999'), ',', '.') || '-' || de.dig_verif rut
          ,Trunc(a.fecha_descuento) fecha_pago
          ,d.cod_tipo_deuda
          ,a.monto_capital + a.monto_interes monto_descuento
          ,a.fecha_descuento
    FROM descuento a, deuda d, deudor de
    WHERE a.fecha_descuento BETWEEN ve_fecha_inicio AND ve_fecha_termino
      AND a.cod_estado_pago = K_ABONO_VIGENTE
      AND a.cod_componente_deuda = ve_cod_componente_deuda
      AND d.cod_deuda = a.cod_deuda
      AND d.cod_tipo_deuda = ve_cod_tipo_deuda
      AND de.cod_deudor = d.cod_deudor;

  BEGIN
    --------------------- ABONO -----------------
    -- abono no exigible
    FOR a IN c_abono(K_COMPONENTE_NO_EXIGIBLE) LOOP
      SELECT Count(*)
      INTO vl_count
      FROM TEMP_PRONTO_PAGO
      WHERE RUT = a.rut
        AND FECHA_PAGO = a.fecha_pago;
	  --vl_cod_moneda = 3 UTM => pesos
      vl_valor_moneda := f_mon_get_valor_moneda(3, a.fecha_abono);
      vl_valor_pesos := a.monto_abono * vl_valor_moneda;
      
      IF (vl_count=0) THEN
        INSERT INTO TEMP_PRONTO_PAGO
          (RUT                       ,FECHA_PAGO              	,COD_TIPO_DEUDA
          ,MONTO_ABONO_NO_EXIGIBLE   ,PESOS_ABONO_NO_EXIGIBLE	,MONTO_ABONO_FUTURO		,PESOS_ABONO_FUTURO
          ,MONTO_DSCTO_NO_EXIGIBLE   ,PESOS_DSCTO_NO_EXIGIBLE	,MONTO_DSCTO_FUTURO		,PESOS_DSCTO_FUTURO
          ,MONTO_TOTAL				 ,MONTO_TOTAL_PESOS)
        VALUES
          (a.rut                    ,a.fecha_pago           	,ve_cod_tipo_deuda
          ,a.monto_abono			,vl_valor_pesos				,0						,0
          ,0                        ,0							, 0						,0
          ,0						,0);
      ELSE
        UPDATE TEMP_PRONTO_PAGO
        SET MONTO_ABONO_NO_EXIGIBLE = MONTO_ABONO_NO_EXIGIBLE + a.monto_abono
        	,PESOS_ABONO_NO_EXIGIBLE = PESOS_ABONO_NO_EXIGIBLE + vl_valor_pesos
        WHERE RUT = a.rut AND FECHA_PAGO = a.fecha_pago;
      END IF;
    END LOOP;

    -- abono futura
    FOR a IN c_abono(K_COMPONENTE_FUTURA) LOOP
      SELECT Count(*)
      INTO vl_count
      FROM TEMP_PRONTO_PAGO
      WHERE RUT = a.rut
        AND FECHA_PAGO = a.fecha_pago;

      --vl_cod_moneda = 3 UTM => pesos
      vl_valor_moneda := f_mon_get_valor_moneda(3, a.fecha_abono);
      vl_valor_pesos := a.monto_abono * vl_valor_moneda;
      
      IF (vl_count=0) THEN
        INSERT INTO TEMP_PRONTO_PAGO
          (RUT                       ,FECHA_PAGO              	,COD_TIPO_DEUDA
          ,MONTO_ABONO_NO_EXIGIBLE   ,PESOS_ABONO_NO_EXIGIBLE	,MONTO_ABONO_FUTURO		,PESOS_ABONO_FUTURO
          ,MONTO_DSCTO_NO_EXIGIBLE   ,PESOS_DSCTO_NO_EXIGIBLE	,MONTO_DSCTO_FUTURO		,PESOS_DSCTO_FUTURO
          ,MONTO_TOTAL				 ,MONTO_TOTAL_PESOS)
        VALUES
          (a.rut                    ,a.fecha_pago           	,ve_cod_tipo_deuda
          ,0						,0							,a.monto_abono			,vl_valor_pesos
          ,0                        ,0							, 0						,0
          ,0						,0);
      ELSE
        UPDATE TEMP_PRONTO_PAGO
        SET MONTO_ABONO_FUTURO = MONTO_ABONO_FUTURO + a.monto_abono
        	,PESOS_ABONO_FUTURO = PESOS_ABONO_FUTURO + vl_valor_pesos
        WHERE RUT = a.rut AND FECHA_PAGO = a.fecha_pago;
      END IF;
    END LOOP;

    --------------------- DESCUENTO -----------------
    -- descuento no exigible
    FOR a IN c_dscto(K_COMPONENTE_NO_EXIGIBLE) LOOP
      SELECT Count(*)
      INTO vl_count
      FROM TEMP_PRONTO_PAGO
      WHERE RUT = a.rut
        AND FECHA_PAGO = a.fecha_pago;

      vl_valor_moneda := f_mon_get_valor_moneda(3, a.fecha_descuento);
      vl_valor_pesos := a.monto_descuento * vl_valor_moneda;
      
      IF (vl_count=0) THEN
        INSERT INTO TEMP_PRONTO_PAGO
          (RUT                       ,FECHA_PAGO              	,COD_TIPO_DEUDA
          ,MONTO_ABONO_NO_EXIGIBLE   ,PESOS_ABONO_NO_EXIGIBLE	,MONTO_ABONO_FUTURO		,PESOS_ABONO_FUTURO
          ,MONTO_DSCTO_NO_EXIGIBLE   ,PESOS_DSCTO_NO_EXIGIBLE	,MONTO_DSCTO_FUTURO		,PESOS_DSCTO_FUTURO
          ,MONTO_TOTAL				 ,MONTO_TOTAL_PESOS)
        VALUES
          (a.rut                     ,a.fecha_pago       		,ve_cod_tipo_deuda
          ,0						 ,0							,0						,0
          ,a.monto_descuento         ,vl_valor_pesos			,0						,0
          ,0						 ,0);
      ELSE
        UPDATE TEMP_PRONTO_PAGO
        SET MONTO_DSCTO_NO_EXIGIBLE = MONTO_DSCTO_NO_EXIGIBLE + a.monto_descuento
        	,PESOS_DSCTO_NO_EXIGIBLE = PESOS_DSCTO_NO_EXIGIBLE + vl_valor_pesos
        WHERE RUT = a.rut AND FECHA_PAGO = a.fecha_pago;
      END IF;
    END LOOP;

    -- abono futura
    FOR a IN c_dscto(K_COMPONENTE_FUTURA) LOOP
      SELECT Count(*)
      INTO vl_count
      FROM TEMP_PRONTO_PAGO
      WHERE RUT = a.rut
        AND FECHA_PAGO = a.fecha_pago;

      vl_valor_moneda := f_mon_get_valor_moneda(3, a.fecha_descuento);
      vl_valor_pesos := a.monto_descuento * vl_valor_moneda;
      
      IF (vl_count=0) THEN
        INSERT INTO TEMP_PRONTO_PAGO
          (RUT                       ,FECHA_PAGO              	,COD_TIPO_DEUDA
          ,MONTO_ABONO_NO_EXIGIBLE   ,PESOS_ABONO_NO_EXIGIBLE	,MONTO_ABONO_FUTURO		,PESOS_ABONO_FUTURO
          ,MONTO_DSCTO_NO_EXIGIBLE   ,PESOS_DSCTO_NO_EXIGIBLE	,MONTO_DSCTO_FUTURO		,PESOS_DSCTO_FUTURO
          ,MONTO_TOTAL				 ,MONTO_TOTAL_PESOS)
        VALUES
          (a.rut                     ,a.fecha_pago       		,ve_cod_tipo_deuda
          ,0                         ,0							,0						,0
          ,0                         ,0							,a.monto_descuento	 	,vl_valor_pesos
          ,0						 ,0);
      ELSE
        UPDATE TEMP_PRONTO_PAGO
        SET MONTO_DSCTO_FUTURO = MONTO_DSCTO_FUTURO + a.monto_descuento
        	,PESOS_DSCTO_FUTURO = PESOS_DSCTO_FUTURO + vl_valor_pesos
        WHERE RUT = a.rut AND FECHA_PAGO = a.fecha_pago;
      END IF;
    END LOOP;
  END;
  UPDATE TEMP_PRONTO_PAGO
  SET MONTO_TOTAL = MONTO_ABONO_NO_EXIGIBLE + MONTO_ABONO_FUTURO + MONTO_DSCTO_NO_EXIGIBLE + MONTO_DSCTO_FUTURO
  		,MONTO_TOTAL_PESOS = PESOS_ABONO_NO_EXIGIBLE + PESOS_ABONO_FUTURO + PESOS_DSCTO_NO_EXIGIBLE + PESOS_DSCTO_FUTURO;

END spi_pronto_pago;
/