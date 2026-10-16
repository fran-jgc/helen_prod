CREATE OR REPLACE PROCEDURE spi_libro_mayor(ve_fecha_inicio             date
                                             ,ve_fecha_termino          date
                                             ,ve_cod_familia            NUMBER
                                             ,ve_cod_cuenta_inicio      NUMBER
                                             ,ve_cod_cuenta_termino     NUMBER
                                             ,ve_aprobado               VARCHAR2
                                             ,ve_pendiente              VARCHAR2
                                             ,ve_analitico          	  VARCHAR2)
AS
BEGIN
   DECLARE
    vl_cod_estado_ini             NUMBER;
    vl_cod_estado_fin             NUMBER;
    vl_inicial_debito             NUMBER;
    vl_inicial_credito            NUMBER;
    vl_cod_cuenta_ant             NUMBER;
    vl_saldo                      NUMBER;
    vl_new_saldo                  NUMBER;
    vl_debito                     NUMBER;
    vl_credito                    NUMBER;

    CURSOR c_temp IS
        SELECT COD_TEMP_LIBRO_MAYOR
              ,COD_CUENTA_CONTABLE
              ,MONTO_DEBE
              ,MONTO_HABER
        FROM TEMP_LIBRO_MAYOR2
        ORDER BY COD_CUENTA_CONTABLE, NRO_COMPROBANTE, COD_TEMP_LIBRO_MAYOR;

     CURSOR c_temp2 IS
        SELECT COD_TEMP_LIBRO_MAYOR
              ,COD_CUENTA_CONTABLE
              ,MONTO_DEBE
              ,MONTO_HABER
        FROM TEMP_LIBRO_MAYOR2
        ORDER BY COD_CUENTA_CONTABLE, NRO_COMPROBANTE, COD_TEMP_LIBRO_MAYOR;

   BEGIN

    IF (ve_pendiente='S') THEN
      vl_cod_estado_ini := 1;
    ELSE
      vl_cod_estado_ini := 2;
    END IF;
    IF (ve_aprobado='S') THEN
      vl_cod_estado_fin := 2;
    ELSE
      vl_cod_estado_fin := 1;
    END IF;
	
    IF(ve_analitico = 'N') THEN
        INSERT INTO TEMP_LIBRO_MAYOR
            (COD_TEMP_LIBRO_MAYOR
            ,NRO_COMPROBANTE
            ,FECHA_COMPROBANTE
            ,NOM_COMPROBANTE
            ,COD_CUENTA_CONTABLE
            ,NOM_CUENTA_CONTABLE
            ,MONTO_DEBE
            ,MONTO_HABER
		        ,DEBITO
		        ,CREDITO
		        ,SALDO
		        ,INICIAL_DEBITO
		        ,INICIAL_CREDITO
		        ,INICIAL_SALDO)
        SELECT ROWNUM
              ,C.NRO_COMPROBANTE
              ,C.FECHA_COMPROBANTE
              ,C.NOM_COMPROBANTE
              ,IT.COD_CUENTA_CONTABLE
              ,CTA.NOM_CUENTA_CONTABLE
              ,IT.MONTO_DEBE
              ,IT.MONTO_HABER
              ,0 DEBITO
              ,0 CREDITO
              ,0 SALDO
              ,0 INICIAL_DEBITO
              ,0 INICIAL_CREDITO
              ,0 INICIAL_SALDO
        FROM COMPROBANTE C, ITEM_COMPROBANTE IT, CUENTA_CONTABLE CTA
        WHERE C.COD_FAMILIA_DEUDA = ve_cod_familia
              AND C.COD_ESTADO_COMPROBANTE BETWEEN vl_cod_estado_ini AND vl_cod_estado_fin
              AND C.FECHA_COMPROBANTE BETWEEN ve_fecha_inicio AND ve_fecha_termino + 1 - 1/(24*3600)
              AND IT.COD_COMPROBANTE = C.COD_COMPROBANTE
              AND IT.COD_CUENTA_CONTABLE  BETWEEN   ve_cod_cuenta_inicio AND ve_cod_cuenta_termino
              AND CTA.COD_CUENTA_CONTABLE = IT.COD_CUENTA_CONTABLE
	    ORDER BY CTA.COD_CUENTA_CONTABLE, C.NRO_COMPROBANTE ASC;

		-- si no existen registros vacios para las cuentas que no tienen movimiento
        INSERT INTO TEMP_LIBRO_MAYOR
            (COD_TEMP_LIBRO_MAYOR
            ,NRO_COMPROBANTE
            ,FECHA_COMPROBANTE
            ,NOM_COMPROBANTE
            ,COD_CUENTA_CONTABLE
            ,NOM_CUENTA_CONTABLE
            ,MONTO_DEBE
            ,MONTO_HABER
		        ,DEBITO
		        ,CREDITO
		        ,SALDO
		        ,INICIAL_DEBITO
		        ,INICIAL_CREDITO
		        ,INICIAL_SALDO)
        SELECT null
              ,null
              ,null
              ,null
              ,COD_CUENTA_CONTABLE
              ,NOM_CUENTA_CONTABLE
              ,0
              ,0
              ,0 DEBITO
              ,0 CREDITO
              ,0 SALDO
              ,0 INICIAL_DEBITO
              ,0 INICIAL_CREDITO
              ,0 INICIAL_SALDO
        FROM CUENTA_CONTABLE
        WHERE COD_CUENTA_CONTABLE  BETWEEN   ve_cod_cuenta_inicio AND ve_cod_cuenta_termino
	    ORDER BY COD_CUENTA_CONTABLE ASC;

		-- calula los saldos iniciales
        vl_inicial_debito := 0;
        vl_inicial_credito := 0;
        vl_cod_cuenta_ant := 0;
        vl_saldo := 0;
        FOR a IN c_temp LOOP
          IF (a.COD_CUENTA_CONTABLE <> vl_cod_cuenta_ant) THEN
            vl_cod_cuenta_ant := a.COD_CUENTA_CONTABLE;

            SELECT Nvl(Sum(IT.MONTO_DEBE),0)
                  ,Nvl(Sum(IT.MONTO_HABER),0)
            INTO   vl_inicial_debito
                  ,vl_inicial_credito
            FROM COMPROBANTE C, ITEM_COMPROBANTE IT
            WHERE C.COD_FAMILIA_DEUDA = ve_cod_familia
                  AND C.COD_ESTADO_COMPROBANTE BETWEEN vl_cod_estado_ini AND vl_cod_estado_fin
                  AND C.FECHA_COMPROBANTE < ve_fecha_inicio
                  AND IT.COD_COMPROBANTE = C.COD_COMPROBANTE
                  AND IT.COD_CUENTA_CONTABLE = vl_cod_cuenta_ant;

            vl_debito := vl_inicial_debito;
            vl_credito := vl_inicial_credito;
            vl_saldo := vl_inicial_debito - vl_inicial_credito;
          END IF;

            vl_new_saldo := a.MONTO_DEBE - a.MONTO_HABER;
            vl_saldo := vl_saldo + vl_new_saldo;
          IF (vl_new_saldo > 0) THEN
            vl_debito := vl_debito + vl_new_saldo;
            ELSE
            vl_credito := vl_credito - vl_new_saldo;
            END IF;

          UPDATE TEMP_LIBRO_MAYOR
          SET INICIAL_DEBITO = vl_inicial_debito
              ,INICIAL_CREDITO = vl_inicial_credito
              ,INICIAL_SALDO = vl_inicial_debito - vl_inicial_credito
              ,DEBITO = vl_debito
              ,CREDITO = vl_credito
              ,SALDO = vl_saldo
          WHERE COD_TEMP_LIBRO_MAYOR = a.COD_TEMP_LIBRO_MAYOR;
        END LOOP;

		    update TEMP_LIBRO_MAYOR
		    set MONTO_DEBE = Decode(Sign(INICIAL_SALDO), 1, INICIAL_SALDO, 0)
			    ,MONTO_HABER = Decode(Sign(INICIAL_SALDO), -1,  - INICIAL_SALDO, 0)
		    where NRO_COMPROBANTE is NULL;

        DELETE TEMP_LIBRO_MAYOR 
        where (MONTO_DEBE = 0 and MONTO_HABER = 0 and SALDO = 0 and INICIAL_SALDO = 0);

    ELSIF (ve_analitico = 'S')THEN
		  INSERT INTO TEMP_LIBRO_MAYOR2
          (COD_TEMP_LIBRO_MAYOR
				  ,FECHA_COMPROBANTE
				  ,NRO_COMPROBANTE
				  ,COD_CUENTA_CONTABLE
				  ,NOM_CUENTA_CONTABLE
				  ,RUT
				  ,NOM_CAJERO
				  ,MONTO_DEBE
				  ,MONTO_HABER
				  ,DEBITO
				  ,CREDITO
				  ,SALDO
				  ,INICIAL_DEBITO
				  ,INICIAL_CREDITO
				  ,INICIAL_SALDO)
		  SELECT ROWNUM
          ,C.FECHA_COMPROBANTE
				  ,C.NRO_COMPROBANTE
				  ,AC.COD_CUENTA_CONTABLE
				  ,CTA.NOM_CUENTA_CONTABLE
				  ,TO_CHAR(D.RUT) || '-' || D.DIG_VERIF
				  ,NOM_USUARIO
				  ,AC.MONTO_DEBE
				  ,AC.MONTO_HABER
				  ,0 DEBITO
				  ,0 CREDITO
				  ,0 SALDO
				  ,0 INICIAL_DEBITO
				  ,0 INICIAL_CREDITO
				  ,0 INICIAL_SALDO
		  FROM ANALITICO_COMPROBANTE AC LEFT OUTER JOIN DEUDOR D ON D.COD_DEUDOR = AC.COD_DEUDOR,
			  COMPROBANTE C, CUENTA_CONTABLE CTA, USUARIO U
		  WHERE C.COD_COMPROBANTE = AC.COD_COMPROBANTE
			  AND C.COD_FAMILIA_DEUDA = ve_cod_familia
			  AND C.COD_ESTADO_COMPROBANTE BETWEEN vl_cod_estado_ini AND vl_cod_estado_fin
			  AND C.FECHA_COMPROBANTE BETWEEN ve_fecha_inicio AND ve_fecha_termino + 1 - 1/(24*3600)
			  AND AC.COD_CUENTA_CONTABLE  BETWEEN ve_cod_cuenta_inicio AND ve_cod_cuenta_termino
			  AND CTA.COD_CUENTA_CONTABLE = AC.COD_CUENTA_CONTABLE
			  AND U.COD_USUARIO = C.COD_USUARIO
		  ORDER BY CTA.COD_CUENTA_CONTABLE, C.NRO_COMPROBANTE ASC;

		-- si no existen registros vacios para las cuentas que no tienen movimiento
		  INSERT INTO TEMP_LIBRO_MAYOR2
          (COD_TEMP_LIBRO_MAYOR
				  ,FECHA_COMPROBANTE
				  ,NRO_COMPROBANTE
				  ,COD_CUENTA_CONTABLE
				  ,NOM_CUENTA_CONTABLE
				  ,RUT
				  ,NOM_CAJERO
				  ,MONTO_DEBE
				  ,MONTO_HABER
				  ,DEBITO
				  ,CREDITO
				  ,SALDO
				  ,INICIAL_DEBITO
				  ,INICIAL_CREDITO
				  ,INICIAL_SALDO)
		  SELECT null
          ,null
				  ,null
				  ,COD_CUENTA_CONTABLE
				  ,NOM_CUENTA_CONTABLE
				  ,null
				  ,null
				  ,0
				  ,0
				  ,0 DEBITO
				  ,0 CREDITO
				  ,0 SALDO
				  ,0 INICIAL_DEBITO
				  ,0 INICIAL_CREDITO
				  ,0 INICIAL_SALDO
		  FROM CUENTA_CONTABLE 
		  WHERE COD_CUENTA_CONTABLE  BETWEEN ve_cod_cuenta_inicio AND ve_cod_cuenta_termino
		  ORDER BY COD_CUENTA_CONTABLE ASC;

		-- calula los saldos iniciales
		  vl_inicial_debito	:= 0;
		  vl_inicial_credito := 0;
		  vl_cod_cuenta_ant	:= 0;
		  vl_saldo			:= 0;

      FOR b IN c_temp2 LOOP
        IF (b.COD_CUENTA_CONTABLE <> vl_cod_cuenta_ant) THEN
          vl_cod_cuenta_ant := b.COD_CUENTA_CONTABLE;

          SELECT Nvl(Sum(IT.MONTO_DEBE),0)
                ,Nvl(Sum(IT.MONTO_HABER),0)
          INTO   vl_inicial_debito
                ,vl_inicial_credito
          FROM COMPROBANTE C, ITEM_COMPROBANTE IT
          WHERE C.COD_FAMILIA_DEUDA = ve_cod_familia
                AND C.COD_ESTADO_COMPROBANTE BETWEEN vl_cod_estado_ini AND vl_cod_estado_fin
                AND C.FECHA_COMPROBANTE < ve_fecha_inicio
                AND IT.COD_COMPROBANTE = C.COD_COMPROBANTE
                AND IT.COD_CUENTA_CONTABLE = vl_cod_cuenta_ant;

          vl_debito := vl_inicial_debito;
          vl_credito := vl_inicial_credito;
          vl_saldo := vl_inicial_debito - vl_inicial_credito;
        END IF;

          vl_new_saldo := b.MONTO_DEBE - b.MONTO_HABER;
          vl_saldo := vl_saldo + vl_new_saldo;
        IF (vl_new_saldo > 0) THEN
          vl_debito := vl_debito + vl_new_saldo;
          ELSE
          vl_credito := vl_credito - vl_new_saldo;
          END IF;

        UPDATE TEMP_LIBRO_MAYOR
        SET INICIAL_DEBITO = vl_inicial_debito
            ,INICIAL_CREDITO = vl_inicial_credito
            ,INICIAL_SALDO = vl_inicial_debito - vl_inicial_credito
            ,DEBITO = vl_debito
            ,CREDITO = vl_credito
            ,SALDO = vl_saldo
        WHERE COD_TEMP_LIBRO_MAYOR = b.COD_TEMP_LIBRO_MAYOR;
        END LOOP;

		    update TEMP_LIBRO_MAYOR
		    set MONTO_DEBE = Decode(Sign(INICIAL_SALDO), 1, INICIAL_SALDO, 0)
			    ,MONTO_HABER = Decode(Sign(INICIAL_SALDO), -1, - INICIAL_SALDO, 0)
		    where NRO_COMPROBANTE is NULL;

        DELETE TEMP_LIBRO_MAYOR 
        where (MONTO_DEBE = 0 and MONTO_HABER = 0 and SALDO = 0 and INICIAL_SALDO = 0);
    END IF;
  END;
END;
/
