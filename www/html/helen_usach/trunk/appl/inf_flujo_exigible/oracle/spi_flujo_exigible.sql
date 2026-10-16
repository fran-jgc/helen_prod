CREATE OR REPLACE PROCEDURE spi_flujo_exigible(ve_ano       		NUMBER
                                              ,ve_cod_cartera       NUMBER
                                              ,ve_cod_usuario 		NUMBER)
AS
/*
exec spi_flujo_exigible (2016, 0, 1);

select * from TEMP_FLUJO_EXIGIBLE where cod_usuario = 1
and rut = 16743337 ;

select * from TEMP_FLUJO_EXIGIBLE where cod_usuario = 1
and cod_deuda = 10099452

select * from TEMP_FLUJO_EXIGIBLE where cod_usuario = 1
and fecha_inicio_cobro >= to_date('01/01/2017','dd/mm/yyyy');

select sum(monto_ano1) from TEMP_FLUJO_EXIGIBLE where cod_usuario = 1

*/
BEGIN
	DECLARE
      K_DEUDA_EXIGIBLE            NUMBER:=2;
			vl_cod_universidad_defecto  NUMBER:=6;
			vl_tasa                     NUMBER;
			vl_porc                     NUMBER;
			vl_porc2					          NUMBER;

			vl_monto1                   NUMBER;
			vl_monto2                   NUMBER;
			vl_monto3                   NUMBER;
			vl_monto4                   NUMBER;
			vl_monto5                   NUMBER;
			vl_monto6                   NUMBER;
			vl_monto7                   NUMBER;
			vl_monto8                   NUMBER;
			vl_monto9                   NUMBER;
			vl_monto10                  NUMBER;
			vl_monto11                  NUMBER;
			vl_monto12                  NUMBER;
			vl_monto13                  NUMBER;
      vl_monto14                  NUMBER;
	    vl_monto15                  NUMBER;
	    vl_monto16                  NUMBER;
	    vl_monto17                  NUMBER;
	    vl_monto9999                NUMBER;

			vl_count                    NUMBER;
			vl_fin_ano                  DATE:= to_date(To_Char(31)||'/'||12||'/'||ve_ano||' 23:59:59', 'dd/mm/yyyy hh24:mi:ss');
			vc_cod_deuda				        NUMBER;
      vc_cod_tipo_deuda           VARCHAR(100);
			vl_cuota_fija			          VARCHAR2(2);
			vl_con_prorroga				      VARCHAR2(2);
      vl_es_cruzado        		    VARCHAR2(2);
      vl_es_cruzado_interno       VARCHAR2(2);
			vl_porc_usach				        NUMBER;
      vl_porc_fondo               number;
			vl_count_SDJ				        NUMBER;
      vl_count_prorroga  	 		    NUMBER;
      vl_CTY                      NUMBER;
      vl_CAT                      NUMBER;
		  vl_count_suspension         NUMBER;
      vl_con_suspension           VARCHAR(100);
		  vl_count_condonacion        NUMBER;
      vl_fecha_condonacion        VARCHAR(100);
      vl_cod_carrera              NUMBER;
      vl_nom_carrera              VARCHAR(100);
      vl_cuota_ano                NUMBER;
      vl_cod_estado_deuda         NUMBER;
      vl_dscto_CR12               NUMBER;
      vl_cod_prorroga             NUMBER;
      vl_min_ano_pp               NUMBER;
      vl_nom_estado_deuda         VARCHAR(100);


  CURSOR c_deudas_flujo(ve_cod_usuario NUMBER) IS
  SELECT cod_deuda
        ,tipo_deuda
	  from TEMP_FLUJO_EXIGIBLE
    where cod_usuario = ve_cod_usuario
--AND rut IN (13066515,10109951,13200130,15930035,15777176)
--and rut = 16716914
--AND cod_deuda IN (21000132)
    ;

	BEGIN

	  DELETE TEMP_FLUJO_EXIGIBLE where cod_usuario = ve_cod_usuario;
    COMMIT;


    --deuda exigible
    INSERT INTO TEMP_FLUJO_EXIGIBLE
	        (cod_deuda
			      ,TIPO_DEUDA
            ,ANOS_PACTADOS
            ,rut
            ,dig_verif
            ,fecha_inicio_cobro
            ,ESTADO_DEUDA
            ,NOMBRE_UNIVERSIDAD
            ,NOMBRE_CARTERA
            ,cod_usuario
            )
	  SELECT da.cod_deuda
            ,da.cod_tipo_deuda
			      ,da.cant_cuota
            ,de.rut
            ,de.dig_verif
            ,da.fecha_inicio_cobro
            ,ed.nom_estado_deuda
            ,u.nom_universidad
            ,c.nom_cartera
            ,ve_cod_usuario
    from deuda da, deudor de, estado_deuda ed, tipo_deuda td, universidad u, cartera c
    where de.cod_deudor = da.cod_deudor
    and da.cod_estado_deuda = ed.cod_estado_deuda
    --and	da.cod_universidad = vl_cod_universidad_defecto   --30-03-2017 en el ejemplo envbiado x usach vienen datos de la umce rut   13200130, por tanto se asume que debe entregar todo independiente de la u
    and	da.cod_estado_deuda  = 2  --exigible
    AND da.COD_DEUDA NOT IN(1)
    and	(ve_cod_cartera=0 or da.cod_cartera = ve_cod_cartera)
    AND td.cod_tipo_deuda = da.cod_tipo_deuda
    AND td.cod_familia_deuda = 1
    AND u.cod_universidad = da.cod_universidad
    AND c.cod_cartera = da.cod_cartera
    AND da.cod_deuda IN (SELECT p.cod_deuda FROM plan_pago p WHERE p.cod_deuda = da.cod_deuda AND p.ano_cobro >= ve_ano);  --que tenga alguna cuota

    COMMIT;

    --deuda cancelada
    INSERT INTO TEMP_FLUJO_EXIGIBLE
	        (cod_deuda
			      ,TIPO_DEUDA
            ,ANOS_PACTADOS
            ,rut
            ,dig_verif
            ,fecha_inicio_cobro
            ,ESTADO_DEUDA
            ,NOMBRE_UNIVERSIDAD
            ,NOMBRE_CARTERA
            ,cod_usuario
            )
	  SELECT da.cod_deuda
            ,da.cod_tipo_deuda
			      ,da.cant_cuota
            ,de.rut
            ,de.dig_verif
            ,da.fecha_inicio_cobro
            ,ed.nom_estado_deuda
            ,u.nom_universidad
            ,c.nom_cartera
            ,ve_cod_usuario
    from deuda da, deudor de, estado_deuda ed, tipo_deuda td, universidad u, cartera c
    where de.cod_deudor = da.cod_deudor
    and da.cod_estado_deuda = ed.cod_estado_deuda
    --and	da.cod_universidad = vl_cod_universidad_defecto   --30-03-2017 en el ejemplo envbiado x usach vienen datos de la umce rut   13200130, por tanto se asume que debe entregar todo independiente de la u
    and	da.cod_estado_deuda  IN ( 4, 21)  --cancelada, reprogramada
    AND da.COD_DEUDA NOT IN(1)
    and	(ve_cod_cartera=0 or da.cod_cartera = ve_cod_cartera)
    AND td.cod_tipo_deuda = da.cod_tipo_deuda
    AND td.cod_familia_deuda = 1
    AND u.cod_universidad = da.cod_universidad
    AND c.cod_cartera = da.cod_cartera
    AND da.cod_deuda IN (SELECT p.cod_deuda FROM plan_pago p WHERE p.cod_deuda = da.cod_deuda AND p.ano_cobro >= ve_ano)  --que tenga alguna cuota
    AND (da.COD_DEUDA IN (SELECT COD_DEUDA FROM ABONO a WHERE a.COD_ESTADO_PAGO = 1 AND A.COD_DEUDA = da.COD_DEUDA AND To_number(To_Char(A.FECHA_ABONO,'yyyy')) >= ve_ano)
	       or da.COD_DEUDA IN (SELECT COD_DEUDA FROM DESCUENTO D WHERE D.COD_ESTADO_PAGO = 1 AND D.COD_DEUDA = da.COD_DEUDA AND To_number(To_Char(D.FECHA_DESCUENTO,'yyyy')) >= ve_ano));

    COMMIT;

    --deuda reprogramada CRI
    INSERT INTO TEMP_FLUJO_EXIGIBLE
	        (cod_deuda
			      ,TIPO_DEUDA
            ,ANOS_PACTADOS
            ,rut
            ,dig_verif
            ,fecha_inicio_cobro
            ,ESTADO_DEUDA
            ,NOMBRE_UNIVERSIDAD
            ,NOMBRE_CARTERA
            ,cod_usuario
            )
	  SELECT da.cod_deuda
            ,da.cod_tipo_deuda
			      ,da.cant_cuota
            ,de.rut
            ,de.dig_verif
            ,da.fecha_inicio_cobro
            ,ed.nom_estado_deuda
            ,u.nom_universidad
            ,c.nom_cartera
            ,ve_cod_usuario
    from deuda da, deudor de, estado_deuda ed, tipo_deuda td, universidad u, cartera c
    where de.cod_deudor = da.cod_deudor
    and da.cod_estado_deuda = ed.cod_estado_deuda
    --and	da.cod_universidad = vl_cod_universidad_defecto   --30-03-2017 en el ejemplo envbiado x usach vienen datos de la umce rut   13200130, por tanto se asume que debe entregar todo independiente de la u
    and	da.cod_estado_deuda  IN (22)  --CRI
    AND da.COD_DEUDA NOT IN(1)
    and	(ve_cod_cartera=0 or da.cod_cartera = ve_cod_cartera)
    AND td.cod_tipo_deuda = da.cod_tipo_deuda
    AND td.cod_familia_deuda = 1
    AND u.cod_universidad = da.cod_universidad
    AND c.cod_cartera = da.cod_cartera
    AND da.cod_deuda IN (SELECT p.cod_deuda FROM plan_pago p WHERE p.cod_deuda = da.cod_deuda AND p.ano_cobro >= ve_ano)  --que tenga alguna cuota
    AND da.COD_DEUDOR IN (SELECT r.COD_DEUDOR FROM repro_rcs r WHERE r.COD_ESTADO_repro_rcs = 2 AND r.COD_DEUDOR= de.COD_DEUDOR AND To_number(To_Char(r.FECHA_REPRO_RCS,'yyyy')) >= ve_ano);

    COMMIT;

    --deuda condonada
    INSERT INTO TEMP_FLUJO_EXIGIBLE
	        (cod_deuda
			      ,TIPO_DEUDA
            ,ANOS_PACTADOS
            ,rut
            ,dig_verif
            ,fecha_inicio_cobro
            ,ESTADO_DEUDA
            ,NOMBRE_UNIVERSIDAD
            ,NOMBRE_CARTERA
            ,cod_usuario
            )
	  SELECT da.cod_deuda
            ,da.cod_tipo_deuda
			      ,da.cant_cuota
            ,de.rut
            ,de.dig_verif
            ,da.fecha_inicio_cobro
            ,ed.nom_estado_deuda
            ,u.nom_universidad
            ,c.nom_cartera
            ,ve_cod_usuario
    from deuda da, deudor de, estado_deuda ed, tipo_deuda td, universidad u, cartera c
    where de.cod_deudor = da.cod_deudor
    and da.cod_estado_deuda = ed.cod_estado_deuda
    --and	da.cod_universidad = vl_cod_universidad_defecto   --30-03-2017 en el ejemplo envbiado x usach vienen datos de la umce rut   13200130, por tanto se asume que debe entregar todo independiente de la u
    and	da.cod_estado_deuda  = 3  --condonada
    AND da.COD_DEUDA NOT IN(1)
    and	(ve_cod_cartera=0 or da.cod_cartera = ve_cod_cartera)
    AND td.cod_tipo_deuda = da.cod_tipo_deuda
    AND td.cod_familia_deuda = 1
    AND u.cod_universidad = da.cod_universidad
    AND c.cod_cartera = da.cod_cartera
    AND da.cod_deuda IN (SELECT p.cod_deuda FROM plan_pago p WHERE p.cod_deuda = da.cod_deuda AND p.ano_cobro >= ve_ano)  --que tenga alguna cuota
    AND da.COD_DEUDA IN (SELECT COD_DEUDA FROM deuda_condonacion dc, condonacion c WHERE c.COD_ESTADO_CONDONACION = 2 AND dc.COD_DEUDA = da.COD_DEUDA AND c.FECHA_CONFIRMA >= To_Date('01/01/' || To_Char(ve_ano),'dd/mm/yyyy') AND dc.cod_condonacion = c.cod_condonacion);

    COMMIT;

    --deuda prorrogada
    INSERT INTO TEMP_FLUJO_EXIGIBLE
	        (cod_deuda
			      ,TIPO_DEUDA
            ,ANOS_PACTADOS
            ,rut
            ,dig_verif
            ,fecha_inicio_cobro
            ,ESTADO_DEUDA
            ,NOMBRE_UNIVERSIDAD
            ,NOMBRE_CARTERA
            ,cod_usuario
            )
	  SELECT da.cod_deuda
            ,da.cod_tipo_deuda
			      ,da.cant_cuota
            ,de.rut
            ,de.dig_verif
            ,da.fecha_inicio_cobro  
            ,ed.nom_estado_deuda
            ,u.nom_universidad
            ,c.nom_cartera
            ,ve_cod_usuario
    from deuda da, deudor de, estado_deuda ed, tipo_deuda td, universidad u, cartera c
    where de.cod_deudor = da.cod_deudor
    and da.cod_estado_deuda = ed.cod_estado_deuda
    --and	da.cod_universidad = vl_cod_universidad_defecto   --30-03-2017 en el ejemplo envbiado x usach vienen datos de la umce rut   13200130, por tanto se asume que debe entregar todo independiente de la u
    and	da.cod_estado_deuda  = 1  --no exigible
    AND da.COD_DEUDA NOT IN(1)
    and	(ve_cod_cartera=0 or da.cod_cartera = ve_cod_cartera)
    AND td.cod_tipo_deuda = da.cod_tipo_deuda
    AND td.cod_familia_deuda = 1
    AND u.cod_universidad = da.cod_universidad
    AND c.cod_cartera = da.cod_cartera
    AND da.COD_DEUDA IN (SELECT COD_DEUDA FROM prorroga p WHERE p.COD_ESTADO_PRORROGA = 2 AND p.COD_DEUDA = da.COD_DEUDA AND To_number(To_Char(p.FECHA_SOLICITUD_TRAMITE,'yyyy')) > ve_ano)
    AND da.cod_deuda IN (SELECT p.cod_deuda FROM prorroga pp, plan_pago_h p  WHERE pp.COD_ESTADO_PRORROGA = 2 AND pp.COD_DEUDA = da.COD_DEUDA AND To_number(To_Char(pp.FECHA_SOLICITUD_TRAMITE,'yyyy')) > ve_ano 
                                                                               AND p.origen = 'PRORROGA' AND  p.cod_origen = pp.cod_prorroga);  --que tenga alguna cuota

    COMMIT;

  --las deudas que inician cobro el 2017 no deben estar en el flujo 2016
  delete TEMP_FLUJO_EXIGIBLE
  WHERE cod_usuario = ve_cod_usuario
    AND To_Number(To_Char(fecha_inicio_cobro, 'yyyy')) > ve_ano
    AND COD_DEUDA NOT IN (SELECT COD_DEUDA FROM prorroga p WHERE p.COD_ESTADO_PRORROGA = 2 AND p.COD_DEUDA = TEMP_FLUJO_EXIGIBLE.COD_DEUDA AND To_number(To_Char(p.FECHA_SOLICITUD_TRAMITE,'yyyy')) > ve_ano);

    COMMIT;

  --las deudas CRPI no son parte del flujo
  delete TEMP_FLUJO_EXIGIBLE
  WHERE cod_usuario = ve_cod_usuario
    AND TIPO_DEUDA = 'CRPI';

	COMMIT;

	FOR a IN c_deudas_flujo(ve_cod_usuario) LOOP

		vc_cod_deuda := a.cod_deuda;
    vc_cod_tipo_deuda := a.tipo_deuda;

    -- obteiene los datos del tipo de deuda
    SELECT 1 + (tasa_interes_credito / 100)
	  into vl_tasa
    FROM tipo_deuda
    WHERE cod_tipo_deuda = vc_cod_tipo_deuda;


	  --cruzado externo
      -- obtiene el % que corresponde a ala Universidad
		SELECT count(*)
		into vl_count
		FROM	cruzado
		WHERE	cod_deuda = vc_cod_deuda
        AND		cod_universidad = vl_cod_universidad_defecto;

    if (vl_count = 0) then
       	vl_es_cruzado := 'NO';
     	  vl_porc_usach := 100;
			  vl_porc := 1;   -- la deuda es 100% de la universidad
		else
			vl_es_cruzado := 'SI';
			SELECT nvl(porc, 100.0000) / 100.0000
				  ,porc
			into vl_porc
				,vl_porc_usach
			FROM	cruzado
			WHERE	cod_deuda = vc_cod_deuda
			AND		cod_universidad = vl_cod_universidad_defecto;

		end if;

		--cruzado interno
		select count(*)
		into vl_count
		from cruzado_interno
		where COD_DEUDA = vc_cod_deuda
			and COD_ACREEDOR_INTERNO = 1;

		if (vl_count=0)then
			vl_es_cruzado_interno := 'NO';
			vl_porc2 := 1;
      vl_porc_fondo := 100;
		else
			vl_es_cruzado_interno := 'SI';
			select nvl(porc, 100.0000) / 100.0000
          ,porc
			into vl_porc2
          ,vl_porc_fondo
			from cruzado_interno
			where COD_DEUDA = vc_cod_deuda
			and COD_ACREEDOR_INTERNO = 1;
		end if;

		IF (vc_cod_tipo_deuda = 'CS') THEN
			select count(*)
			into vl_count_SDJ
			from plan_pago
			where cod_deuda = vc_cod_deuda
			and ano_cobro > ve_ano
			and cod_concepto_cobro = 11;	-- SDJ

			if (vl_count_SDJ >0) then
				vl_cuota_fija := 'SI';
			ELSE
        --existen deudas sin cuotas SDJ, pero que sin embargo son cuota fija!! ej cod_deuda 30009270
			  select count(*)
			  into vl_count
			  from plan_pago
			  where cod_deuda = vc_cod_deuda
			  and ano_cobro = 9999;

        IF (vl_count = 0) THEN
  				vl_cuota_fija := 'SI';
        ELSE
			    vl_cuota_fija := 'NO';
        END IF;
			end if;
    ELSE
			vl_cuota_fija := 'SI';
    END IF;

    ---------------------------
    --para aquellas deudas que registran prorroga en el ve_año+1 o pesterior 
    SELECT Count(*)
    INTO vl_count
    FROM prorroga p
    WHERE p.COD_ESTADO_PRORROGA = 2 
      AND p.COD_DEUDA = vc_cod_deuda
      AND To_number(To_Char(p.FECHA_SOLICITUD_TRAMITE,'yyyy')) > ve_ano;
      
    IF (vl_count > 0) THEN
        vl_cuota_ano := f_pp_saldo_plan_pago_flujo(vc_cod_deuda, ve_ano, vl_fin_ano,vc_cod_tipo_deuda, ve_ano);

        --tiene cuota del año
		    select Count(*)
		    into vl_count
		    from PLAN_PAGO
		    where COD_DEUDA = vc_cod_deuda
		    and ANO_COBRO = ve_ano;

        IF (vl_count > 0) THEN
		      select NVL(SUM(monto_capital + monto_interes), 0)
		      into vl_CTY
		      from PLAN_PAGO
		      where COD_DEUDA = vc_cod_deuda
		      and ANO_COBRO = ve_ano;

          --se mantienen la fecha inicio cobro
        ELSE
          --busca la cuota y fecha inicio en plan_pago_h
          SELECT Count(*)
          INTO vl_count
          FROM prorroga p
          WHERE p.COD_ESTADO_PRORROGA = 2
            AND p.COD_DEUDA = vc_cod_deuda
            AND To_number(To_Char(p.FECHA_SOLICITUD_TRAMITE,'yyyy')) > ve_ano
            AND p.cod_prorroga IN (SELECT cod_origen FROM plan_pago_h WHERE origen = 'PRORROGA')
            AND ROWNUM = 1;

          IF ( vl_count = 0) THEN
            vl_CTY := 0;
          ELSE 
            SELECT cod_prorroga
            INTO vl_cod_prorroga
            FROM prorroga p
            WHERE p.COD_ESTADO_PRORROGA = 2
              AND p.COD_DEUDA = vc_cod_deuda
              AND To_number(To_Char(p.FECHA_SOLICITUD_TRAMITE,'yyyy')) > ve_ano
              AND p.cod_prorroga IN (SELECT cod_origen FROM plan_pago_h WHERE origen = 'PRORROGA')
              AND ROWNUM = 1;

            SELECT Nvl(Sum(monto_capital + monto_interes), 0)
            INTO vl_CTY
            FROM plan_pago_h
            WHERE cod_deuda = vc_cod_deuda
              AND ano_cobro = ve_ano
              AND cod_origen = vl_cod_prorroga
              AND origen = 'PRORROGA';

            SELECT min(p.ano_cobro)
            INTO vl_min_ano_pp
            FROM plan_pago_h p 
            WHERE p.origen = 'PRORROGA' 
              AND p.cod_origen = vl_cod_prorroga;

            IF (vl_min_ano_pp = 9999) THEN  --aun no se calculaba la cuota
              vl_min_ano_pp := ve_ano + 1;      --con esto quedan deudas que inician posterior al año del flujo, estas se eliminan al final (*)
            END IF;

            SELECT nom_estado_deuda
            INTO vl_nom_estado_deuda
            FROM estado_deuda
            WHERE cod_estado_deuda = 2; --en cobranza normal
             
		        UPDATE TEMP_FLUJO_EXIGIBLE
            SET FECHA_INICIO_COBRO = To_Date('01/01/' || To_Char(vl_min_ano_pp), 'dd/mm/yyyy')
                ,ESTADO_DEUDA = vl_nom_estado_deuda
            WHERE COD_DEUDA =  vc_cod_deuda;
          END IF;
        END IF;
    ELSE 
      --cuota año
      vl_cod_estado_deuda := f_deuda_get_estado(vc_cod_deuda, vl_fin_ano);
      IF (vl_cod_estado_deuda = 2) THEN  -- exigible
        vl_cuota_ano := f_pp_saldo_plan_pago_flujo(vc_cod_deuda, ve_ano, vl_fin_ano,vc_cod_tipo_deuda, ve_ano);
      ELSIF (vl_cod_estado_deuda = 4) THEN  -- cancelada
        vl_cuota_ano := f_pp_saldo_plan_pago_flujo(vc_cod_deuda, ve_ano, vl_fin_ano,vc_cod_tipo_deuda, ve_ano);
      ELSE
        vl_cuota_ano := 0;
      END IF;

      --CTY
		  select NVL(SUM(monto_capital + monto_interes), 0)
		  into vl_CTY
		  from PLAN_PAGO
		  where COD_DEUDA = vc_cod_deuda
		  and ANO_COBRO = ve_ano;
    END IF;


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
    vl_monto12 := 0;
    vl_monto13 := 0;
    vl_monto14 := 0;
    vl_monto15 := 0;
    vl_monto16 := 0;
    vl_monto17 := 0;
		if (vl_cuota_fija = 'SI') THEN
--Dbms_Output.put_line('16' || vl_cant_cuota_restante );
      vl_monto1 := vl_cuota_ano;

      vl_monto2 := f_pp_saldo_plan_pago_flujo(vc_cod_deuda, ve_ano + 1, vl_fin_ano,vc_cod_tipo_deuda, ve_ano) / Power(vl_tasa, 1);
      vl_monto3 := f_pp_saldo_plan_pago_flujo(vc_cod_deuda, ve_ano + 2, vl_fin_ano,vc_cod_tipo_deuda, ve_ano) / Power(vl_tasa, 2);
      vl_monto4 := f_pp_saldo_plan_pago_flujo(vc_cod_deuda, ve_ano + 3, vl_fin_ano,vc_cod_tipo_deuda, ve_ano) / Power(vl_tasa, 3);
      vl_monto5 := f_pp_saldo_plan_pago_flujo(vc_cod_deuda, ve_ano + 4, vl_fin_ano,vc_cod_tipo_deuda, ve_ano) / Power(vl_tasa, 4);
      vl_monto6 := f_pp_saldo_plan_pago_flujo(vc_cod_deuda, ve_ano + 5, vl_fin_ano,vc_cod_tipo_deuda, ve_ano) / Power(vl_tasa, 5);
      vl_monto7 := f_pp_saldo_plan_pago_flujo(vc_cod_deuda, ve_ano + 6, vl_fin_ano,vc_cod_tipo_deuda, ve_ano) / Power(vl_tasa, 6);
      vl_monto8 := f_pp_saldo_plan_pago_flujo(vc_cod_deuda, ve_ano + 7, vl_fin_ano,vc_cod_tipo_deuda, ve_ano) / Power(vl_tasa, 7);
      vl_monto9 := f_pp_saldo_plan_pago_flujo(vc_cod_deuda, ve_ano + 8, vl_fin_ano,vc_cod_tipo_deuda, ve_ano) / Power(vl_tasa, 8);
      vl_monto10 := f_pp_saldo_plan_pago_flujo(vc_cod_deuda, ve_ano + 9, vl_fin_ano,vc_cod_tipo_deuda, ve_ano) / Power(vl_tasa, 9);
      vl_monto11 := f_pp_saldo_plan_pago_flujo(vc_cod_deuda, ve_ano + 10, vl_fin_ano,vc_cod_tipo_deuda, ve_ano) / Power(vl_tasa, 10);
      vl_monto12 := f_pp_saldo_plan_pago_flujo(vc_cod_deuda, ve_ano + 11, vl_fin_ano,vc_cod_tipo_deuda, ve_ano) / Power(vl_tasa, 11);
      vl_monto13 := f_pp_saldo_plan_pago_flujo(vc_cod_deuda, ve_ano + 12, vl_fin_ano,vc_cod_tipo_deuda, ve_ano) / Power(vl_tasa, 12);
      vl_monto14 := f_pp_saldo_plan_pago_flujo(vc_cod_deuda, ve_ano + 13, vl_fin_ano,vc_cod_tipo_deuda, ve_ano) / Power(vl_tasa, 13);
      vl_monto15 := f_pp_saldo_plan_pago_flujo(vc_cod_deuda, ve_ano + 14, vl_fin_ano,vc_cod_tipo_deuda, ve_ano) / Power(vl_tasa, 14);
      vl_monto16 := f_pp_saldo_plan_pago_flujo(vc_cod_deuda, ve_ano + 15, vl_fin_ano,vc_cod_tipo_deuda, ve_ano) / Power(vl_tasa, 15);
      vl_monto17 := f_pp_saldo_plan_pago_flujo(vc_cod_deuda, ve_ano + 16, vl_fin_ano,vc_cod_tipo_deuda, ve_ano) / Power(vl_tasa, 16);

    --------------------------------
		else
      vl_monto1 := vl_cuota_ano;
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
      vl_monto12 := 0;
      vl_monto13 := 0;
      vl_monto14 := 0;
      vl_monto15 := 0;
      vl_monto16 := 0;
      vl_monto17 := 0;
		end if;

    vl_CAT := f_deuda_cat(vc_cod_deuda, ve_ano);

    vl_monto9999 := 0;
		select NVL(SUM(f_pp_saldo_capital(COD_PLAN_PAGO, vl_fin_ano)), 0)
		into vl_monto9999
		from PLAN_PAGO
		where COD_DEUDA = vc_cod_deuda
		and ANO_COBRO = 9999;

    --PRORROGA
		SELECT count(*)
		into vl_count_prorroga
		FROM	prorroga
		WHERE	cod_deuda = vc_cod_deuda
      AND cod_estado_prorroga = 2
      AND To_Number(to_char(FECHA_SOLICITUD_TRAMITE,'yyyy')) = ve_ano;

		if(vl_count_prorroga > 0) then
			vl_con_prorroga := 'SI';
		else
			vl_con_prorroga := 'NO';
		end if;

    --SUSPENSION
		SELECT count(*)
		into vl_count_suspension
		FROM	suspension
		WHERE	cod_deuda = vc_cod_deuda
      AND cod_estado_suspension = 2
      AND To_Number(to_char(FECHA_SUSPENSION,'yyyy')) = ve_ano;

		if(vl_count_suspension > 0) then
			vl_con_suspension := 'SI';
		else
			vl_con_suspension := 'NO';
		end if;

    --CONDONACION
		SELECT count(*)
		into vl_count_condonacion
		FROM	deuda da, condonacion c
		WHERE	da.cod_deuda = vc_cod_deuda
      AND c.cod_deudor = da.cod_deudor
      AND c.cod_estado_condonacion IN (1, 2)
      AND To_Number(to_char(c.fecha_confirma,'yyyy')) = ve_ano;
		
		if(vl_count_condonacion > 0) THEN
		  SELECT count(*)
		  into vl_count_condonacion
		  FROM	deuda da, condonacion c
		  WHERE	da.cod_deuda = vc_cod_deuda
        AND c.cod_deudor = da.cod_deudor
        AND c.cod_estado_condonacion IN (2)
        AND To_Number(to_char(c.fecha_confirma,'yyyy')) = ve_ano;

		  if(vl_count_condonacion > 0) THEN --tiene condonacion confirmada
		    SELECT To_Char(Max(c.fecha_confirma), 'dd/mm/yyyy')
		    into vl_fecha_condonacion
		    FROM	deuda da, condonacion c
		    WHERE	da.cod_deuda = vc_cod_deuda
          AND c.cod_deudor = da.cod_deudor
          AND c.cod_estado_condonacion = 2
          AND To_Number(to_char(c.fecha_confirma,'yyyy')) = ve_ano;
      ELSE
        vl_fecha_condonacion := 'En Proceso de Condonacion';
      END IF;
		else
			vl_fecha_condonacion := 'SIN CONDONAR';
		end if;

    --CARRERA
		SELECT f_get_matricula(cod_deudor, ve_ano )
		into vl_cod_carrera
		FROM	deuda da
		WHERE	da.cod_deuda = vc_cod_deuda;

    SELECT nom_carrera
    INTO    vl_nom_carrera
    FROM carrera
    WHERE cod_carrera = vl_cod_carrera;


		UPDATE TEMP_FLUJO_EXIGIBLE
    SET PORC_USACH  = vl_porc_usach          --PORC_USACH
	    ,POR_FONDO    = vl_porc_fondo          --POR_FONDO
	    ,MONTO_ANO1   = vl_monto1              --MONTO_ANO1
	    ,MONTO_ANO2   = vl_monto2              --MONTO_ANO2
	    ,MONTO_ANO3   = vl_monto3              --MONTO_ANO3
	    ,MONTO_ANO4   = vl_monto4              --MONTO_ANO4
	    ,MONTO_ANO5   = vl_monto5              --MONTO_ANO5
	    ,MONTO_ANO6   = vl_monto6              --MONTO_ANO6
	    ,MONTO_ANO7   = vl_monto7              --MONTO_ANO7
	    ,MONTO_ANO8   = vl_monto8              --MONTO_ANO8
	    ,MONTO_ANO9   = vl_monto9              --MONTO_ANO9
	    ,MONTO_ANO10  = vl_monto10             --MONTO_ANO10
	    ,MONTO_ANO11  = vl_monto11             --MONTO_ANO11
	    ,MONTO_ANO12  = vl_monto12             --MONTO_ANO12
	    ,MONTO_ANO13  = vl_monto13             --MONTO_ANO13
      ,MONTO_ANO14  = vl_monto14             --MONTO_ANO14
	    ,MONTO_ANO15  = vl_monto15             --MONTO_ANO15
	    ,MONTO_ANO16  = vl_monto16             --MONTO_ANO16
	    ,MONTO_ANO17  = vl_monto17             --MONTO_ANO17
	    ,MONTO_9999   = vl_monto9999           --MONTO_9999
      ,CTY          = vl_CTY                 --CTY
      ,CAT          = vl_CAT                 --CAT
      ,CRUZADO      = vl_es_cruzado          --CRUZADO
      ,CRUZADO_INTERNO = vl_es_cruzado_interno  --CRUZADO_INTERNO
      ,PRORROGA     = vl_con_prorroga        --PRORROGA
      ,SUSPENSION   = vl_con_suspension      --SUSPENSION
      ,FECHA_CONDONACION  = vl_fecha_condonacion   --FECHA_CONDONACION
      ,CUOTA_FIJA   = vl_cuota_fija       --CUOTA_FIJA
      ,COD_CARRERA  = vl_cod_carrera         --COD_CARRERA
      ,NOM_CARRERA  = vl_nom_carrera         --NOM_CARRERA
      ,FECHA_REGISTRO= SYSDATE                --FECHA_REGISTRO
    WHERE COD_DEUDA =  vc_cod_deuda;
	  COMMIT;
	END LOOP;

  --(*)se eliminan deudas que inician posterior al año del flujo
  DELETE TEMP_FLUJO_EXIGIBLE
  WHERE cod_usuario = ve_cod_usuario
    AND FECHA_INICIO_COBRO >= To_Date('01/01/' || To_Char(ve_ano+1), 'dd/mm/yyyy');

	COMMIT;

END;
END;
/

