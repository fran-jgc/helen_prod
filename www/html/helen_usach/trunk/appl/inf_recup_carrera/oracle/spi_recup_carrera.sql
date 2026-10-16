CREATE OR REPLACE PROCEDURE spi_recup_carrera (
  ve_cod_familia   NUMBER
 ,ve_ano           NUMBER
 ,ve_cod_usuario   NUMBER
)  AS
/*
exec spi_recup_carrera(1, 2016, 1);

select sum(CANT_DEUDOR) from temp_recup_carrera;
--42033

select count(*) from TEMP_RECUP_CARRERA_DETALLE
-- 43224

select sum(monto_deuda_vigente) from TEMP_RECUP_CARRERA_DETALLE
220257,903
231347,7416
*/
BEGIN
  DECLARE
    K_COD_COMPONENTE_NO_EXIGIBLE  NUMBER := 1;
    K_COD_COMPONENTE_VENCIDA      NUMBER := 2;
    K_COD_COMPONENTE_VIGENTE      NUMBER := 3;
    K_COD_COMPONENTE_FUTURA       NUMBER := 4;
    K_COD_ESTADO_PAGO_VIGENTE     NUMBER := 1;
    K_DEUDA_NO_EXIGIBLE           NUMBER := 1;
    K_DEUDA_EXIGIBLE              NUMBER := 2;
    K_UNIVERSIDAD_POR_DEFECTO     NUMBER := 3;
    vl_cant_deudor                NUMBER := 0;
    vl_monto_deuda_vigente        NUMBER := 0;
    vl_monto_abono_vigente        NUMBER := 0;
    vl_monto_abono_vencida        NUMBER := 0;
    vl_monto_abono_anticipado     NUMBER := 0;
    vl_monto_abono_futura         NUMBER := 0;
    vl_monto_abono_no_exigible    NUMBER := 0;
    vl_monto_abono_otro           NUMBER := 0;
    vl_monto_total                NUMBER := 0;
    vl_monto_deuda_vigente_temp   NUMBER := 0;
    vl_monto_abono_temp           NUMBER := 0;
    vl_monto_abono_pesos_temp     NUMBER := 0;
    vl_cod_componente_deuda       NUMBER := 0;
    vl_cod_universidad_por_defecto NUMBER;
    vl_cod_estado_deuda             NUMBER;
    vl_fecha_fin_ano                DATE;
    vl_monto_abono                  NUMBER;
    vl_count                        NUMBER;
    vl_rut				                  NUMBER;
    vl_dig_verif                    VARCHAR2(1);
    vl_nom_deudor					        VARCHAR2(100);
    vl_monto_abono_vigente_utm		NUMBER;
	  vl_monto_abono_vencida_utm		 NUMBER;
	  vl_abono_anticipado_utm			  NUMBER;
	  vl_monto_abono_otro_utm			  NUMBER;
    vl_monto_abono_futura_utm     NUMBER;
    vl_monto_abono_no_exigible_utm  NUMBER;
	  vl_monto_total_utm	          NUMBER;
		vl_monto_total_pesos          NUMBER;
    vl_monto_abono_vigente_pesos  NUMBER;
    vl_monto_abono_vencida_pesos  NUMBER;
    vl_abono_anticipado_pesos     NUMBER;
    vl_monto_abono_no_exig_pesos    NUMBER;
    vl_monto_abono_futura_pesos     NUMBER;
    vl_monto_abono_otro_pesos       NUMBER;
    vl_cod_tipo_deuda               VARCHAR2(100);

    vl_cant_deudor_exig             NUMBER;
    vl_cant_deudor_svp              NUMBER;
    vl_monto_deuda_vigente_exig     NUMBER;
    vl_monto_deuda_vigente_svp      NUMBER;

    CURSOR C_CARRERA_TEMP IS
      SELECT  DISTINCT cod_carrera
            ,nom_carrera
      FROM    temp_recup_carrera
      WHERE  cod_usuario = ve_cod_usuario
      ORDER BY cod_carrera;

    CURSOR C_DEUDORES(vl_cod_carrera VARCHAR) IS
      SELECT  cod_deudor
              ,cod_deuda
      FROM    TEMP_RECUP_CARRERA_DEUDOR
      WHERE   cod_carrera = vl_cod_carrera
        AND   cod_usuario = ve_cod_usuario
     -- AND cod_deudor = 80033947
      ;

  BEGIN
    DELETE temp_recup_carrera WHERE cod_usuario = ve_cod_usuario;
    DELETE TEMP_RECUP_CARRERA_DEUDOR WHERE cod_usuario = ve_cod_usuario;
    DELETE TEMP_RECUP_CARRERA_DETALLE WHERE cod_usuario = ve_cod_usuario;

    COMMIT;

    vl_cod_universidad_por_defecto := f_get_parametro(K_UNIVERSIDAD_POR_DEFECTO);
    INSERT INTO temp_recup_carrera
                (cod_carrera, nom_carrera, cod_usuario, FECHA_REGISTRO)
    (SELECT  DISTINCT m.cod_carrera, c.nom_carrera, ve_cod_usuario, sysdate
     FROM  matricula m, carrera c
     WHERE m.cod_universidad = vl_cod_universidad_por_defecto
       AND c.cod_carrera = m.cod_carrera);

    vl_fecha_fin_ano := to_Date('31/12/'||To_Char(ve_ano) || ' 23:59:59','dd/mm/yyyy hh24:mi:ss');

    --deuda NO exigible y exigible
    INSERT INTO TEMP_RECUP_CARRERA_DEUDOR (cod_deudor, cod_carrera, cod_deuda, cod_usuario)
    (SELECT d.cod_deudor
            ,f_get_matricula(d.cod_deudor, ve_ano)
            ,da.cod_deuda
            ,ve_cod_usuario
     FROM deudor d, deuda da, tipo_deuda t
     WHERE da.cod_deudor = d.cod_deudor
       --AND da.cod_universidad = vl_cod_universidad_por_defecto
       AND da.COD_DEUDA NOT IN(1)
       AND da.cod_estado_deuda IN (1,2)  --no exigible, exigible
       AND t.cod_tipo_deuda = da.cod_tipo_deuda
       AND t.cod_familia_deuda = ve_cod_familia);

    --deuda cancelada
    INSERT INTO TEMP_RECUP_CARRERA_DEUDOR (cod_deudor, cod_carrera, cod_deuda, cod_usuario)
    (SELECT d.cod_deudor
            ,f_get_matricula(d.cod_deudor, ve_ano)
            ,da.cod_deuda
            ,ve_cod_usuario
     FROM deudor d, deuda da, tipo_deuda t
     WHERE da.cod_deudor = d.cod_deudor
       --AND da.cod_universidad = vl_cod_universidad_por_defecto
       AND da.COD_DEUDA NOT IN(1)
       and	da.cod_estado_deuda IN (4, 21, 32)  --cancelada, reprogramada,. DEUDA ELIMINADA DE LA COBRANZA
       AND t.cod_tipo_deuda = da.cod_tipo_deuda
       AND t.cod_familia_deuda = ve_cod_familia
       AND (da.COD_DEUDA IN (SELECT COD_DEUDA FROM ABONO a WHERE a.COD_ESTADO_PAGO = 1 AND A.COD_DEUDA = da.COD_DEUDA AND To_number(To_Char(A.FECHA_ABONO,'yyyy')) >= ve_ano)
	       or da.COD_DEUDA IN (SELECT COD_DEUDA FROM DESCUENTO D WHERE D.COD_ESTADO_PAGO = 1 AND D.COD_DEUDA = da.COD_DEUDA AND To_number(To_Char(D.FECHA_DESCUENTO,'yyyy')) >= ve_ano))
      );

    --deuda reprogramada CRI
    INSERT INTO TEMP_RECUP_CARRERA_DEUDOR (cod_deudor, cod_carrera, cod_deuda, cod_usuario)
    (SELECT d.cod_deudor
            ,f_get_matricula(d.cod_deudor, ve_ano)
            ,da.cod_deuda
            ,ve_cod_usuario
     FROM deudor d, deuda da, tipo_deuda t
     WHERE da.cod_deudor = d.cod_deudor
       --AND da.cod_universidad = vl_cod_universidad_por_defecto
       AND da.COD_DEUDA NOT IN(1)
       and	da.cod_estado_deuda IN (22)  --CRI
       AND t.cod_tipo_deuda = da.cod_tipo_deuda
       AND t.cod_familia_deuda = ve_cod_familia
       AND da.cod_deuda IN (SELECT p.cod_deuda FROM plan_pago p WHERE p.cod_deuda = da.cod_deuda AND p.ano_cobro >= ve_ano)  --que tenga alguna cuota
       AND da.COD_DEUDOR IN (SELECT r.COD_DEUDOR FROM repro_rcs r WHERE r.COD_ESTADO_repro_rcs = 2 AND r.COD_DEUDOR= d.COD_DEUDOR AND To_number(To_Char(r.FECHA_REPRO_RCS,'yyyy')) >= ve_ano)
      );

    --deuda condonada
    INSERT INTO TEMP_RECUP_CARRERA_DEUDOR (cod_deudor, cod_carrera, cod_deuda, cod_usuario)
    (SELECT d.cod_deudor
            ,f_get_matricula(d.cod_deudor, ve_ano)
            ,da.cod_deuda
            ,ve_cod_usuario
     FROM deudor d, deuda da, tipo_deuda t
     WHERE da.cod_deudor = d.cod_deudor
       --AND da.cod_universidad = vl_cod_universidad_por_defecto
       AND da.COD_DEUDA NOT IN(1)
       and	da.cod_estado_deuda IN (3)  --condonada
       AND t.cod_tipo_deuda = da.cod_tipo_deuda
       AND t.cod_familia_deuda = ve_cod_familia
       AND da.COD_DEUDA IN (SELECT COD_DEUDA FROM deuda_condonacion dc, condonacion c WHERE c.COD_ESTADO_CONDONACION = 2 AND dc.COD_DEUDA = da.COD_DEUDA AND To_number(To_Char(c.FECHA_CONFIRMA,'yyyy')) >= ve_ano AND dc.cod_condonacion = c.cod_condonacion)
      );

    --deuda prorrogada
    --estas estan no exigible, por tanto ya estan consideradas

    COMMIT;

    FOR a IN C_CARRERA_TEMP LOOP
      FOR b IN C_DEUDORES (a.cod_carrera) LOOP

				vl_monto_abono_vigente_utm := 0;
				vl_monto_abono_vigente_pesos := 0;
				vl_monto_abono_vencida_utm := 0;
				vl_monto_abono_vencida_pesos := 0;
				vl_abono_anticipado_utm := 0;
				vl_abono_anticipado_pesos := 0;
				vl_monto_abono_otro_utm := 0;
				vl_monto_abono_otro_pesos := 0;
				vl_monto_total_utm := 0;
				vl_monto_total_pesos := 0;

        --para aquellas deudas que registran prorroga en el ve_a?o+1 o pesterior
        SELECT da.cod_tipo_deuda
        INTO vl_cod_tipo_deuda
        FROM deuda da
        WHERE da.cod_deuda = b.cod_deuda;

        SELECT Count(*)
        INTO vl_count
        FROM prorroga p
        WHERE p.COD_ESTADO_PRORROGA = 2
          AND p.COD_DEUDA = b.cod_deuda
          AND To_number(To_Char(p.FECHA_SOLICITUD_TRAMITE,'yyyy')) > ve_ano;

        IF (vl_count > 0) THEN
          vl_monto_deuda_vigente_temp := f_pp_saldo_plan_pago_flujo(b.cod_deuda, ve_ano, vl_fecha_fin_ano, vl_cod_tipo_deuda, ve_ano);
        ELSE
          vl_cod_estado_deuda := f_deuda_get_estado(b.cod_deuda, vl_fecha_fin_ano);
          IF (vl_cod_estado_deuda = 2) THEN  -- exigible
            vl_monto_deuda_vigente_temp := f_pp_saldo_plan_pago_flujo(b.cod_deuda, ve_ano, vl_fecha_fin_ano,vl_cod_tipo_deuda, ve_ano);
          ELSE
            vl_monto_deuda_vigente_temp := 0;
          END IF;
        END IF;
--Dbms_Output.put_line('b.cod_deuda=' || b.cod_deuda || ' vl_monto_deuda_vigente_temp=' || vl_monto_deuda_vigente_temp);


        vl_monto_deuda_vigente  := vl_monto_deuda_vigente + vl_monto_deuda_vigente_temp;


        -- vigente
        SELECT Nvl(Sum(monto_pesos), 0)
	        		,Nvl(Sum(monto_moneda), 0)
        INTO vl_monto_abono_vigente_pesos
            ,vl_monto_abono_vigente_utm
        FROM abono
        WHERE cod_estado_pago = 1
          AND cod_deuda = b.cod_deuda
          AND cod_componente_deuda = K_COD_COMPONENTE_VIGENTE
          AND To_Number(to_char(FECHA_ABONO, 'yyyy')) = ve_ano;
        vl_monto_abono_vigente := vl_monto_abono_vigente + vl_monto_abono_vigente_pesos;

        -- VENCIDA
        SELECT Nvl(Sum(monto_pesos), 0)
	        		,Nvl(Sum(monto_moneda), 0)
        INTO vl_monto_abono_vencida_pesos
            ,vl_monto_abono_vencida_utm
        FROM abono
        WHERE cod_estado_pago = 1
          AND cod_deuda = b.cod_deuda
          AND cod_componente_deuda = K_COD_COMPONENTE_VENCIDA
          AND To_Number(to_char(FECHA_ABONO, 'yyyy')) = ve_ano;
        vl_monto_abono_vencida := vl_monto_abono_vencida + vl_monto_abono_vencida_pesos;

        --futura
        SELECT Nvl(Sum(monto_pesos), 0)
	        		,Nvl(Sum(monto_moneda), 0)
        INTO vl_monto_abono_futura_pesos
            ,vl_monto_abono_futura_utm
        FROM abono
        WHERE cod_estado_pago = 1
          AND cod_deuda = b.cod_deuda
          AND cod_componente_deuda = K_COD_COMPONENTE_FUTURA
          AND To_Number(to_char(FECHA_ABONO, 'yyyy')) = ve_ano;
        vl_monto_abono_futura := vl_monto_abono_futura + vl_monto_abono_futura_pesos;

        --no exigible
        SELECT Nvl(Sum(monto_pesos), 0)
	        		,Nvl(Sum(monto_moneda), 0)
        INTO vl_monto_abono_no_exig_pesos
            ,vl_monto_abono_no_exigible_utm
        FROM abono
        WHERE cod_estado_pago = 1
          AND cod_deuda = b.cod_deuda
          AND cod_componente_deuda = K_COD_COMPONENTE_NO_EXIGIBLE
          AND To_Number(to_char(FECHA_ABONO, 'yyyy')) = ve_ano;
        vl_monto_abono_no_exigible := vl_monto_abono_no_exigible + vl_monto_abono_no_exig_pesos;

        vl_abono_anticipado_pesos := vl_monto_abono_futura_pesos + vl_monto_abono_no_exig_pesos;
        vl_monto_abono_anticipado  := vl_monto_abono_anticipado + vl_abono_anticipado_pesos;
        vl_abono_anticipado_utm := vl_monto_abono_futura_utm + vl_monto_abono_no_exigible_utm;

        --OTRO COMPONENTE
        SELECT Nvl(Sum(monto_pesos), 0)
	        		,Nvl(Sum(monto_moneda), 0)
        INTO vl_monto_abono_otro_pesos
            ,vl_monto_abono_otro_utm
        FROM abono
        WHERE cod_estado_pago = 1
          AND cod_deuda = b.cod_deuda
          AND cod_componente_deuda NOT IN (K_COD_COMPONENTE_VIGENTE, K_COD_COMPONENTE_VENCIDA, K_COD_COMPONENTE_FUTURA, K_COD_COMPONENTE_NO_EXIGIBLE)
          AND To_Number(to_char(FECHA_ABONO, 'yyyy')) = ve_ano;
        vl_monto_abono_otro := vl_monto_abono_otro + vl_monto_abono_otro_pesos;


        ----------------------
        --detalle
       	select nombre || ' ' || apellido_paterno || ' ' || apellido_materno
       			,rut
            ,dig_verif
       	into vl_nom_deudor
       		 ,vl_rut
           ,vl_dig_verif
       	from deudor
       	where cod_deudor = b.cod_deudor;

			  vl_monto_total_utm	 := vl_monto_abono_vigente_utm + vl_monto_abono_vencida_utm + vl_abono_anticipado_utm + vl_monto_abono_otro_utm;
			  vl_monto_total_pesos := vl_monto_abono_vigente_pesos + vl_monto_abono_vencida_pesos + vl_abono_anticipado_pesos + vl_monto_abono_otro_pesos;

        SELECT Count(*)
        INTO vl_count
        FROM TEMP_RECUP_CARRERA_DETALLE
        WHERE RUT_DEUDOR = vl_rut
          AND cod_usuario = ve_cod_usuario;

        IF (vl_count = 0) THEN
          vl_cant_deudor := vl_cant_deudor + 1;

     	    INSERT INTO TEMP_RECUP_CARRERA_DETALLE
					    (COD_DEUDOR
				  	    ,NOM_DEUDOR
				  	    ,RUT_DEUDOR
                ,DIG_VERIF
				  	    ,COD_CARRERA
				        ,NOM_CARRERA
				        ,MONTO_DEUDA_VIGENTE
				        ,MONTO_ABONO_VIGENTE_UTM
				        ,MONTO_ABONO_VIGENTE_PESOS
				        ,MONTO_ABONO_VENCIDA_UTM
				        ,MONTO_ABONO_VENCIDA_PESOS
				        ,MONTO_ABONO_ANTICIPADO_UTM
				        ,MONTO_ABONO_ANTICIPADO_PESOS
				        ,MONTO_ABONO_OTRO_UTM
				        ,MONTO_ABONO_OTRO_PESOS
				        ,MONTO_ABONO_TOTAL_UTM
				        ,MONTO_ABONO_TOTAL_PESOS
                ,COD_USUARIO)
			    VALUES  (b.cod_deudor
					    ,vl_nom_deudor
					    ,vl_rut
              ,vl_dig_verif
					    ,a.cod_carrera
					    ,a.nom_carrera
					    ,vl_monto_deuda_vigente_temp
					    ,vl_monto_abono_vigente_utm
					    ,vl_monto_abono_vigente_pesos
					    ,vl_monto_abono_vencida_utm
					    ,vl_monto_abono_vencida_pesos
					    ,vl_abono_anticipado_utm
					    ,vl_abono_anticipado_pesos
					    ,vl_monto_abono_otro_utm
					    ,vl_monto_abono_otro_pesos
					    ,vl_monto_total_utm
					    ,vl_monto_total_pesos
              ,ve_cod_usuario
					    );
        ELSE
     	    UPDATE TEMP_RECUP_CARRERA_DETALLE
				  SET MONTO_ABONO_VIGENTE_UTM       = MONTO_ABONO_VIGENTE_UTM + vl_monto_abono_vigente_utm
				        ,MONTO_ABONO_VIGENTE_PESOS  = MONTO_ABONO_VIGENTE_PESOS + vl_monto_abono_vigente_pesos
				        ,MONTO_ABONO_VENCIDA_UTM    = MONTO_ABONO_VENCIDA_UTM + vl_monto_abono_vencida_utm
				        ,MONTO_ABONO_VENCIDA_PESOS  = MONTO_ABONO_VENCIDA_PESOS + vl_monto_abono_vencida_pesos
				        ,MONTO_ABONO_ANTICIPADO_UTM = MONTO_ABONO_ANTICIPADO_UTM + vl_abono_anticipado_utm
				        ,MONTO_ABONO_ANTICIPADO_PESOS = MONTO_ABONO_ANTICIPADO_PESOS + vl_abono_anticipado_pesos
				        ,MONTO_ABONO_OTRO_UTM       = MONTO_ABONO_OTRO_UTM + vl_monto_abono_otro_utm
				        ,MONTO_ABONO_OTRO_PESOS     = MONTO_ABONO_OTRO_PESOS + vl_monto_abono_otro_pesos
				        ,MONTO_ABONO_TOTAL_UTM      = MONTO_ABONO_TOTAL_UTM + vl_monto_total_utm
				        ,MONTO_ABONO_TOTAL_PESOS    = MONTO_ABONO_TOTAL_PESOS + vl_monto_total_pesos
				        ,MONTO_DEUDA_VIGENTE		= MONTO_DEUDA_VIGENTE + vl_monto_deuda_vigente_temp
          WHERE RUT_DEUDOR = vl_rut
            AND COD_USUARIO = ve_cod_usuario;

        END IF;
        ----------------------
        COMMIT;

      END LOOP;

      vl_monto_total := vl_monto_abono_vigente + vl_monto_abono_vencida + vl_monto_abono_anticipado + vl_monto_abono_otro;


      ----------------
      -- La cantidad de deudores se obtiene de los flujos !! solicitado por elias verdugo jara 08-08-2017 por mail
      --antes se usaba vl_cant_deudor y  vl_monto_deuda_vigente

      SELECT Count(*)
            ,Nvl(Sum(monto_ano1 + monto_ano2 + monto_ano3 + monto_ano4 + monto_ano5 + monto_ano6 + monto_ano7 + monto_ano8 + monto_ano9 + monto_ano10 + monto_ano11 + monto_ano12 + monto_ano13 + monto_ano14 + monto_ano15 + monto_ano16 + monto_ano17 + monto_9999), 0)
      INTO vl_cant_deudor_exig
            ,vl_monto_deuda_vigente_exig
      FROM TEMP_FLUJO_EXIGIBLE
      WHERE cod_usuario = ve_cod_usuario
        AND cod_carrera = a.cod_carrera;

      SELECT Count(*)
            ,Nvl(Sum(monto_ano1 + monto_ano2 + monto_ano3 + monto_ano4 + monto_ano5 + monto_ano6 + monto_ano7 + monto_ano8 + monto_ano9 + monto_ano10 + monto_ano11 + monto_final), 0)
      INTO vl_cant_deudor_svp
            ,vl_monto_deuda_vigente_svp
      FROM TEMP_FLUJO_SVP
      WHERE cod_usuario = ve_cod_usuario
        AND cod_carrera = a.cod_carrera;

      ----------------

      UPDATE temp_recup_carrera
      SET    cant_deudor = vl_cant_deudor_exig + vl_cant_deudor_svp
            ,monto_deuda_vigente = vl_monto_deuda_vigente_exig + vl_monto_deuda_vigente_svp
            ,monto_abono_vigente = vl_monto_abono_vigente
            ,monto_abono_vencida = vl_monto_abono_vencida
            ,monto_abono_anticipado = vl_monto_abono_anticipado
            ,monto_abono_otro = vl_monto_abono_otro
            ,monto_total = vl_monto_total
      WHERE cod_carrera = a.cod_carrera
        AND COD_USUARIO = ve_cod_usuario;

      vl_cant_deudor                       := 0;
      vl_monto_deuda_vigente               := 0;
      vl_monto_abono_vigente               := 0;
      vl_monto_abono_vencida               := 0;
      vl_monto_abono_anticipado            := 0;
      vl_monto_abono_futura                := 0;
      vl_monto_abono_no_exigible           := 0;
      vl_monto_abono_otro                  := 0;
      vl_monto_total                       := 0;
      vl_monto_deuda_vigente               := 0;
      vl_monto_deuda_vigente_temp          := 0;

    END LOOP;
    COMMIT;
  END;
END;
/

