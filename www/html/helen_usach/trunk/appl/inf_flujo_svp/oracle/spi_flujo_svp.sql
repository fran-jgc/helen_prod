CREATE OR REPLACE PROCEDURE spi_flujo_svp(ve_ano            NUMBER
                                         ,ve_cod_usuario    NUMBER)
AS
/*
exec  spi_flujo_svp (2016, 1);

select * from TEMP_FLUJO_SVP
where cod_usuario = 1
and rut = 17848178;

*/

BEGIN
  DECLARE
    vl_fecha_fin_ano              DATE;
    vl_cuota                      NUMBER;
    vl_monto1                     NUMBER;
    vl_monto2                     NUMBER;
    vl_monto3                     NUMBER;
    vl_monto4                     NUMBER;
    vl_monto5                     NUMBER;
    vl_monto6                     NUMBER;
    vl_monto7                     NUMBER;
    vl_monto8                     NUMBER;
    vl_monto9                     NUMBER;
    vl_monto10                    NUMBER;
    vl_monto11                    NUMBER;
    vl_monto_final                NUMBER;
    vl_porc_usach                 NUMBER;
    vl_count                      NUMBER;
    vl_es_cruzado                 VARCHAR2(2);
    vl_count_prorroga             NUMBER;
    vl_con_prorroga               VARCHAR2(2);
      vl_cod_carrera              NUMBER;
      vl_nom_carrera              VARCHAR(100);

    CURSOR c_deudas IS
      SELECT cod_deuda
            ,MONTO_DEUDA
            ,CANT_CUOTA
            ,To_Number(To_Char(FECHA_INICIO_COBRO, 'yyyy')) ANO
      FROM  TEMP_FLUJO_SVP
      where cod_usuario = ve_cod_usuario;

  BEGIN

    delete TEMP_FLUJO_SVP where cod_usuario = ve_cod_usuario;

    vl_fecha_fin_ano := To_Date('31/12/' || ve_ano || ' 23:59:59', 'dd/mm/yyyy HH24:MI:SS');

    INSERT INTO TEMP_FLUJO_SVP
      (RUT
	    ,DIG_VERIF
	    ,TIPO_DEUDA
	    ,COD_DEUDA
	    ,FECHA_INICIO_COBRO
	    ,CANT_CUOTA
	    ,ESTADO_DEUDA
	    ,UNIVERSIDAD
	    ,CARTERA
	    ,MONTO_DEUDA
	    ,ANO_CONTABLE
	    ,COD_USUARIO
	    ,FECHA_REGISTRO
      )
      SELECT de.RUT                 --RUT
            ,de.DIG_VERIF           --DIG_VERIF
	          ,da.cod_tipo_deuda      --TIPO_DEUDA
	          ,da.COD_DEUDA           --COD_DEUDA
	          ,da.FECHA_INICIO_COBRO  --FECHA_INICIO_COBRO
	          ,da.CANT_CUOTA					--CANT_CUOTA
            ,ed.nom_estado_deuda    --ESTADO_DEUDA
            ,u.nom_universidad      --UNIVERSIDAD
            ,c.nom_cartera          --CARTERA
            ,f_deuda_saldo_total(da.cod_deuda, vl_fecha_fin_ano)  --MONTO_DEUDA
      	    ,ve_ano                 --ANO_CONTABLE
	          ,ve_cod_usuario         --COD_USUARIO
	          ,SYSDATE                --FECHA_REGISTRO
      FROM  deuda da,deudor de,estado_deuda ed, universidad u, cartera c, tipo_deuda t
      WHERE f_deuda_get_estado(da.cod_deuda, vl_fecha_fin_ano) = 1
      AND da.cod_tipo_deuda <> 'CRI'
      AND de.cod_deudor = da.cod_deudor
      AND ed.cod_estado_deuda = 1
      AND u.cod_universidad = da.cod_universidad
      AND c.cod_cartera = da.cod_cartera
      AND t.cod_tipo_deuda = da.cod_tipo_deuda
      AND t.cod_familia_deuda = 1
      AND da.COD_DEUDA NOT IN(1)
    --deuda prorrogada
    AND da.COD_DEUDA NOT IN (SELECT COD_DEUDA FROM prorroga p WHERE p.COD_ESTADO_PRORROGA = 2 AND p.COD_DEUDA = da.COD_DEUDA AND To_number(To_Char(p.FECHA_SOLICITUD_TRAMITE,'yyyy')) > ve_ano)
--AND de.rut IN (13296274,13801286,13904658,13905918,16609995,17313280,17866570)
--AND de.rut IN (16609995,17313280,17866570)     --los saldo deben ser al 31/12/2016 !
      ;


    COMMIT;

    FOR a IN c_deudas LOOP
	    vl_monto1 := 0;
	    vl_monto2 := 0;
	    vl_monto3 := 0;
	    vl_monto4 := 0;
	    vl_monto5 := 0;
	    vl_monto6 := 0;
	    vl_monto7 := 0;
	    vl_monto8 := 0;
	    vl_monto9 := 0;
	    vl_monto10 := 0;
	    vl_monto11 := 0;
	    vl_monto_final := 0;

      vl_cuota := a.MONTO_DEUDA / a.CANT_CUOTA;

      --ano1
      IF (a.ANO <= ve_ano + 1) THEN
        vl_monto1 := vl_cuota;
      ELSE
        vl_monto1 := 0;
      END IF;

      --ano2
      IF (a.ANO <= ve_ano + 2) THEN
        vl_monto2 := vl_cuota;
      ELSE
        vl_monto2 := 0;
      END IF;

      --ano3
      IF (a.ANO <= ve_ano + 3) THEN
        vl_monto3 := vl_cuota;
      ELSE
        vl_monto3 := 0;
      END IF;

      --ano4
      IF (a.ANO <= ve_ano + 4) THEN
        vl_monto4 := vl_cuota;
      ELSE
        vl_monto4 := 0;
      END IF;

      --ano5
      IF (a.ANO <= ve_ano + 5) THEN
        vl_monto5 := vl_cuota;
      ELSE
        vl_monto5 := 0;
      END IF;

      --ano6
      IF (a.ANO <= ve_ano + 6) THEN
        vl_monto6 := vl_cuota;
      ELSE
        vl_monto6 := 0;
      END IF;

      --ano7
      IF (a.ANO <= ve_ano + 7) THEN
        vl_monto7 := vl_cuota;
      ELSE
        vl_monto7 := 0;
      END IF;

      --ano8
      IF (a.ANO <= ve_ano + 8) THEN
        vl_monto8 := vl_cuota;
      ELSE
        vl_monto8 := 0;
      END IF;

      --ano9
      IF (a.ANO <= ve_ano + 9) THEN
        vl_monto9 := vl_cuota;
      ELSE
        vl_monto9 := 0;
      END IF;

      --ano10
      IF (a.ANO <= ve_ano + 10) THEN
        vl_monto10 := vl_cuota;
      ELSE
        vl_monto10 := 0;
      END IF;

      --ano11
      IF (a.ANO <= ve_ano + 11) THEN
        vl_monto11 := vl_cuota;
      ELSE
        vl_monto11 := 0;
      END IF;

      --ano12
      vl_monto_final := a.MONTO_DEUDA - (vl_monto1 + vl_monto2 + vl_monto3 + vl_monto4 + vl_monto5 + vl_monto6 + vl_monto7 + vl_monto8 + vl_monto9 + vl_monto10 + vl_monto11);

      -- obtiene el % que corresponde a ala Universidad
      SELECT Count(*)
      INTO vl_count
      FROM cruzado
      WHERE cod_deuda = a.cod_deuda
        AND cod_universidad = 6;

      IF (vl_count = 0) THEN
          vl_es_cruzado := 'NO';
          vl_porc_usach := 100;
      ELSE
        vl_es_cruzado := 'SI';
        SELECT porc
        INTO vl_porc_usach
        FROM cruzado
        WHERE cod_deuda = a.cod_deuda
          AND cod_universidad = 6;
      END IF;

      --prorroga
      SELECT count(*)
      into vl_count_prorroga
      FROM    prorroga
      WHERE    cod_deuda = a.cod_deuda;

      if(vl_count_prorroga > 0) then
        vl_con_prorroga := 'SI';
      else
        vl_con_prorroga := 'NO';
      end if;

      --CARRERA
		  SELECT f_get_matricula(cod_deudor, ve_ano )
		  into vl_cod_carrera
		  FROM	deuda da
		  WHERE	da.cod_deuda = a.cod_deuda;

      SELECT nom_carrera
      INTO    vl_nom_carrera
      FROM carrera
      WHERE cod_carrera = vl_cod_carrera;



      UPDATE  TEMP_FLUJO_SVP
	    SET MONTO_ANO1  = vl_monto1
	        ,MONTO_ANO2 = vl_monto2
	        ,MONTO_ANO3 = vl_monto3
	        ,MONTO_ANO4 = vl_monto4
	        ,MONTO_ANO5 = vl_monto5
	        ,MONTO_ANO6 = vl_monto6
	        ,MONTO_ANO7 = vl_monto7
	        ,MONTO_ANO8 = vl_monto8
	        ,MONTO_ANO9 = vl_monto9
	        ,MONTO_ANO10 = vl_monto10
	        ,MONTO_ANO11 = vl_monto11
	        ,MONTO_FINAL = vl_monto_final
          ,ES_CRUZADO = vl_es_cruzado
          ,PORC_USACH = vl_porc_usach
          ,CON_PRORROGA = vl_con_prorroga
        	,COD_CARRERA  = vl_cod_carrera 
  	      ,NOM_CARRERA  = vl_nom_carrera
      WHERE cod_deuda = a.cod_deuda;

    END LOOP;

    COMMIT;
  END;
END;
/

